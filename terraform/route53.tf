
data "aws_route53_zone" "route_53_zone_for_our_domain" {
  name = "socialmobility.independent-commission.uk."
}

resource "aws_route53_record" "dns_alias_record__main_website" {
  count = var.create_dns_record__main_website ? 1 : 0  // Only create this DNS record if "var.create_dns_record__main_website" is true

  zone_id = data.aws_route53_zone.route_53_zone_for_our_domain.zone_id
  name    = "${var.dns_record_subdomain_including_dot__main_website}${data.aws_route53_zone.route_53_zone_for_our_domain.name}"
  type    = "A"

  alias {
    evaluate_target_health = false
    name = aws_cloudfront_distribution.distribution__main_website.domain_name
    zone_id = aws_cloudfront_distribution.distribution__main_website.hosted_zone_id
  }
}
