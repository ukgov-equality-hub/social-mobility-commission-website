
resource "aws_cloudfront_cache_policy" "cloudfront_cache_policy__main_website_public" {
  name = "${var.service_name_hyphens}--${var.environment_hyphens}-Cache-Policy--Public"
  min_ttl = 0
  default_ttl = 60
  max_ttl = 600

  parameters_in_cache_key_and_forwarded_to_origin {
    cookies_config {
      cookie_behavior = "none"
    }
    headers_config {
      header_behavior = "whitelist"
      headers {
        items = [
          "Host"
        ]
      }
    }
    query_strings_config {
      query_string_behavior = "all"
    }

    enable_accept_encoding_gzip = true
    enable_accept_encoding_brotli = true
  }
}

resource "aws_cloudfront_cache_policy" "cloudfront_cache_policy__wordpress" {
  name = "${var.service_name_hyphens}--${var.environment_hyphens}-Cache-Policy--WordPress"
  min_ttl = 0
  default_ttl = 0
  max_ttl = 600

  parameters_in_cache_key_and_forwarded_to_origin {
    cookies_config {
      cookie_behavior = "all"
    }
    headers_config {
      header_behavior = "whitelist"
      headers {
        items = [
          "Authorization",
          "Host",
          "X-CSRFToken",
          "X-WP-Nonce",

          # WordPress uses User-Agent to decide whether to show the rich-text/block editor
          # If we don't allow User-Agent, it shows the rich-text/block editor as a code editor (damn you, WordPress!)
          # https://stackoverflow.com/questions/42144410/tinymce-doesnt-load-after-putting-a-cdn-on-website
          "User-Agent"
        ]
      }
    }
    query_strings_config {
      query_string_behavior = "all"
    }

    enable_accept_encoding_gzip = true
    enable_accept_encoding_brotli = true
  }
}

resource "aws_cloudfront_cache_policy" "cloudfront_cache_policy__uploads" {
  name = "${var.service_name_hyphens}--${var.environment_hyphens}-Cache-Policy--Uploads"
  min_ttl = 0
  default_ttl = 60
  max_ttl = 600

  parameters_in_cache_key_and_forwarded_to_origin {
    cookies_config {
      cookie_behavior = "none"
    }
    headers_config {
      header_behavior = "none"
    }
    query_strings_config {
      query_string_behavior = "none"
    }

    enable_accept_encoding_gzip = true
    enable_accept_encoding_brotli = true
  }
}

locals {
  distribution_for_main_website__main_origin_id = "${var.service_name_hyphens}--${var.environment_hyphens}--Main-Website-origin"
  distribution_for_main_website__uploads_origin_id = "${var.service_name_hyphens}--${var.environment_hyphens}--Uploads-origin"
}

resource "aws_cloudfront_distribution" "distribution__main_website" {
  // CloudFront distributions have to be created in the us-east-1 region (for some reason!)
  provider = aws.us-east-1

  comment = "${var.service_name_hyphens}--${var.environment_hyphens}--main-website"

  origin {
    domain_name = aws_elastic_beanstalk_environment.main_app_elastic_beanstalk_environment.cname
    origin_id = local.distribution_for_main_website__main_origin_id

    custom_origin_config {
      http_port = 80
      https_port = 443
      origin_protocol_policy = "http-only"
      origin_ssl_protocols = ["TLSv1.2"]
    }
  }

  origin {
    domain_name = aws_s3_bucket.s3_bucket__uploads.bucket_regional_domain_name
    origin_id = local.distribution_for_main_website__uploads_origin_id
    origin_access_control_id = aws_cloudfront_origin_access_control.oac_for_s3_uploads.id
  }

  price_class = "PriceClass_100"

  aliases = ["${var.dns_record_subdomain_including_dot__main_website}${data.aws_route53_zone.route_53_zone_for_our_domain.name}"]

  viewer_certificate {
    acm_certificate_arn = aws_acm_certificate_validation.certificate_validation_waiter__main_website.certificate_arn
    cloudfront_default_certificate = false
    minimum_protocol_version = "TLSv1"
    ssl_support_method = "sni-only"
  }

  enabled = true
  is_ipv6_enabled = true

  # Cache behavior with precedence 0
  ordered_cache_behavior {
    path_pattern = "/app/uploads/*"
    cache_policy_id = aws_cloudfront_cache_policy.cloudfront_cache_policy__uploads.id
    allowed_methods = ["GET", "HEAD", "OPTIONS"]
    cached_methods = ["GET", "HEAD", "OPTIONS"]
    target_origin_id = local.distribution_for_main_website__uploads_origin_id
    viewer_protocol_policy = "redirect-to-https"
    compress = true

    dynamic "function_association" {
      for_each = var.environment != "Prod" ? [1] : []  // Only create this Function Association in non-production environments (i.e. if "var.environment" is not "Prod")

      content {
        event_type = "viewer-request"
        function_arn = aws_cloudfront_function.http_basic_auth_function[0].arn
      }
    }
  }

  # Cache behavior with precedence 1
  ordered_cache_behavior {
    path_pattern = "/wp*"
    cache_policy_id = aws_cloudfront_cache_policy.cloudfront_cache_policy__wordpress.id  // For WordPress (editor) requests, don't cache anything
    allowed_methods = ["DELETE", "GET", "HEAD", "OPTIONS", "PATCH", "POST", "PUT"]
    cached_methods = ["GET", "HEAD", "OPTIONS"]
    target_origin_id = local.distribution_for_main_website__main_origin_id
    viewer_protocol_policy = "redirect-to-https"
    compress = true

    dynamic "function_association" {
      for_each = var.environment != "Prod" ? [1] : []  // Only create this Function Association in non-production environments (i.e. if "var.environment" is not "Prod")

      content {
        event_type = "viewer-request"
        function_arn = aws_cloudfront_function.http_basic_auth_function[0].arn
      }
    }
  }

  default_cache_behavior {
    cache_policy_id = aws_cloudfront_cache_policy.cloudfront_cache_policy__main_website_public.id  // For Public (non-editor) requests, use more caching
    allowed_methods = ["DELETE", "GET", "HEAD", "OPTIONS", "PATCH", "POST", "PUT"]
    cached_methods = ["GET", "HEAD", "OPTIONS"]
    target_origin_id = local.distribution_for_main_website__main_origin_id
    viewer_protocol_policy = "redirect-to-https"
    compress = true

    dynamic "function_association" {
      for_each = var.environment != "Prod" ? [1] : []  // Only create this Function Association in non-production environments (i.e. if "var.environment" is not "Prod")

      content {
        event_type = "viewer-request"
        function_arn = aws_cloudfront_function.http_basic_auth_function[0].arn
      }
    }
  }

  restrictions {
    geo_restriction {
      restriction_type = "none"
      locations = []
    }
  }
}

resource "aws_cloudfront_origin_access_control" "oac_for_s3_uploads" {
  name                              = "${var.service_name_hyphens}--${var.environment_hyphens}--oac_for_s3_uploads"
  origin_access_control_origin_type = "s3"
  signing_behavior                  = "always"
  signing_protocol                  = "sigv4"
}

resource "aws_cloudfront_function" "http_basic_auth_function" {
  count = (var.environment != "Prod") ? 1 : 0  // Only create this CloudFront Function in non-production environments (i.e. if "var.environment" is not "Prod")

  name    = "${var.service_name_hyphens}--${var.environment_hyphens}--http-basic-auth-function"
  runtime = "cloudfront-js-1.0"
  publish = true
  code    = <<EOT
function handler(event) {
  var authHeaders = event.request.headers.authorization;

  // Configure authentication
  var authUser = '${var.BASIC_AUTH_USERNAME}';
  var authPass = '${var.BASIC_AUTH_PASSWORD}';

  function b2a(a) {
    var c, d, e, f, g, h, i, j, o, b = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", k = 0, l = 0, m = "", n = [];
    if (!a) return a;
    do c = a.charCodeAt(k++), d = a.charCodeAt(k++), e = a.charCodeAt(k++), j = c << 16 | d << 8 | e,
    f = 63 & j >> 18, g = 63 & j >> 12, h = 63 & j >> 6, i = 63 & j, n[l++] = b.charAt(f) + b.charAt(g) + b.charAt(h) + b.charAt(i); while (k < a.length);
    return m = n.join(""), o = a.length % 3, (o ? m.slice(0, o - 3) :m) + "===".slice(o || 3);
  }

  // Construct the Basic Auth string
  var expected = 'Basic ' + b2a(authUser + ':' + authPass);

  // If an Authorization header is supplied and it's an exact match, pass the
  // request on through to CF/the origin without any modification.
  if (authHeaders && authHeaders.value === expected) {
    return event.request;
  }

  // If the request is for the WordPress admin portal or the WP JSON API, let the request through because it uses its own authentication.
  if (event.request.uri.startsWith('/wp/') || event.request.uri.startsWith('/wp-json/')) {
    return event.request;
  }

  // But if we get here, we must either be missing the auth header or the
  // credentials failed to match what we expected.
  // Request the browser present the Basic Auth dialog.
  var response = {
    statusCode: 401,
    statusDescription: "Unauthorized",
    headers: {
      "www-authenticate": {
        value: 'Basic realm="Inclusion Confident Scheme design history"',
      },
    },
  };

  return response;
}
EOT
}
