
variable "service_name" {
  type = string
  description = "The short name of the service."
  default = "smc_web"
}

variable "service_name_hyphens" {
  type = string
  description = "The short name of the service (using hyphen-style)."
  default = "smc-web"
}

variable "environment" {
  type = string
  description = "The environment name."
}

variable "environment_hyphens" {
  type = string
  description = "The environment name (using hyphen-style)."
}

variable "dns_record_subdomain_including_dot__email_domain" {
  type = string
  description = "The subdomain (including dot - e.g. 'dev.' or just '' for production) for the Route53 alias record - for the main website"
}

variable "create_redirect_from_www_domain" {
  type = bool
  description = "Should terraform create a CloudFront distribution to redirect the www domain to the root domain"
}
variable "dns_record_www_domain_including_dot" {
  type = string
  description = "The www domain (including dot - e.g. 'www.') for the www domain redirect"
}

variable "aws_region" {
  type = string
  description = "The AWS region used for the provider and resources."
  default = "eu-west-2"
}
