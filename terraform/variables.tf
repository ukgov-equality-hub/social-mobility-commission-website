
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

variable "create_dns_record__main_website" {
  type = bool
  description = "Should terraform create a Route53 alias record for the (sub)domain - for the main website"
}
variable "dns_record_subdomain_including_dot__main_website" {
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

// SECRETS
// These variables are set in GitHub Actions environment-specific secrets
// Most of these are passed to the application via Elastic Beanstalk environment variables
variable "MYSQL_PASSWORD" {
  type = string
  sensitive = true
}

variable "BASIC_AUTH_USERNAME" {
  type = string
  default = ""
  sensitive = true
}
variable "BASIC_AUTH_PASSWORD" {
  type = string
  default = ""
  sensitive = true
}

variable "ACF_PRO_KEY" {
  type = string
  sensitive = true
}
variable "AUTH_KEY" {
  type = string
  sensitive = true
}
variable "SECURE_AUTH_KEY" {
  type = string
  sensitive = true
}
variable "LOGGED_IN_KEY" {
  type = string
  sensitive = true
}
variable "NONCE_KEY" {
  type = string
  sensitive = true
}
variable "AUTH_SALT" {
  type = string
  sensitive = true
}
variable "SECURE_AUTH_SALT" {
  type = string
  sensitive = true
}
variable "LOGGED_IN_SALT" {
  type = string
  sensitive = true
}
variable "NONCE_SALT" {
  type = string
  sensitive = true
}
variable "APP_KEY" {
  type = string
  sensitive = true
}
