
resource "aws_ses_domain_identity" "ses_domain_identity" {
  domain = "mail.${var.dns_record_subdomain_including_dot__main_website}${data.aws_route53_zone.route_53_zone_for_our_domain.name}"
}


//////////
// DKIM

resource "aws_ses_domain_dkim" "ses_domain_dkim" {
  domain = aws_ses_domain_identity.ses_domain_identity.domain
}

resource "aws_route53_record" "route53_amazonses_dkim_record" {
  count   = 3  // Ideally, this should be  `length(aws_ses_domain_dkim.ses_domain_dkim.dkim_tokens)`
               //   but Terraform needs to know how many resources there will before the apply stage starts
               //   In realisty, this will always be 3, so we can hard-code this

  zone_id = data.aws_route53_zone.route_53_zone_for_our_domain.id
  name    = "${aws_ses_domain_dkim.ses_domain_dkim.dkim_tokens[count.index]}._domainkey.mail.${var.dns_record_subdomain_including_dot__main_website}${data.aws_route53_zone.route_53_zone_for_our_domain.name}"
  type    = "CNAME"
  ttl     = "300"
  records = ["${aws_ses_domain_dkim.ses_domain_dkim.dkim_tokens[count.index]}.dkim.amazonses.com"]
}


///////////////
// MAIL FROM

resource "aws_ses_domain_mail_from" "ses_domain_mail_from" {
  domain           = aws_ses_domain_identity.ses_domain_identity.domain
  mail_from_domain = "contact-us.mail.${var.dns_record_subdomain_including_dot__main_website}${data.aws_route53_zone.route_53_zone_for_our_domain.name}"
}

resource "aws_route53_record" "route53_ses_domain_mail_from_mx" {
  zone_id = data.aws_route53_zone.route_53_zone_for_our_domain.id
  name    = aws_ses_domain_mail_from.ses_domain_mail_from.mail_from_domain
  type    = "MX"
  ttl     = "300"
  records = ["10 feedback-smtp.eu-west-2.amazonses.com"]
}

resource "aws_route53_record" "route53_ses_domain_mail_from_txt" {
  zone_id = data.aws_route53_zone.route_53_zone_for_our_domain.id
  name    = aws_ses_domain_mail_from.ses_domain_mail_from.mail_from_domain
  type    = "TXT"
  ttl     = "300"
  records = ["v=spf1 include:amazonses.com ~all"]
}


// Identity verification waiter

resource "aws_ses_domain_identity_verification" "ses_domain_identity_verification" {
  domain = aws_ses_domain_identity.ses_domain_identity.id

  depends_on = [
    aws_route53_record.route53_ses_domain_mail_from_mx,
    aws_route53_record.route53_ses_domain_mail_from_txt,
    aws_route53_record.route53_amazonses_dkim_record
  ]
}


/////////////////////////////////////
// IAM user (for SMTP credentials)

resource "aws_iam_user" "smtp_iam_user" {
  name = "${var.service_name}__${var.environment}__smtp_user"
  path = "/"
}

data "aws_iam_policy_document" "smtp_policy_data" {
  statement {
    effect    = "Allow"
    actions   = ["ses:SendEmail", "ses:SendRawEmail"]
    resources = ["*"]
  }
}

resource "aws_iam_user_policy" "smtp_policy_user" {
  name   = "allow_user_to_send_email_ses_smtp"
  user   = aws_iam_user.smtp_iam_user.name
  policy = data.aws_iam_policy_document.smtp_policy_data.json
}

resource "aws_iam_access_key" "smtp_iam_user_access_key" {
  user = aws_iam_user.smtp_iam_user.name
}
