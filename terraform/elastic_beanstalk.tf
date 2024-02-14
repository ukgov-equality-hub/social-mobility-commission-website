
locals {
  main_app_elastic_beanstalk_solution_stack_name = "64bit Amazon Linux 2023 v4.0.3 running PHP 8.1"
  main_app_elastic_beanstalk_ec2_instance_type = "t4g.small"

  main_app_elastic_beanstalk_min_instances = 1
  main_app_elastic_beanstalk_max_instances = 2

  main_app_elastic_beanstalk_health_check_path = "/"  // It would be nice if this was a dedicated "/health-check" endpoint
  main_app_elastic_beanstalk_health_check_matcher_http_code = "200,301,302"
}


// An S3 bucket to store the code that is deployed by Elastic Beanstalk
resource "aws_s3_bucket" "main_app_elastic_beanstalk_code_s3_bucket" {
  bucket_prefix = lower("${var.service_name_hyphens}--${var.environment_hyphens}--S3-Beanstalk-")
}

resource "aws_s3_bucket_public_access_block" "main_app_elastic_beanstalk_code_s3_bucket_public_access_block" {
  bucket = aws_s3_bucket.main_app_elastic_beanstalk_code_s3_bucket.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}


resource "aws_elastic_beanstalk_application" "main_app_elastic_beanstalk_application" {
  name        = "${var.service_name}__${var.environment}__Elastic_Beanstalk_Application"
}


resource "aws_elastic_beanstalk_environment" "main_app_elastic_beanstalk_environment" {
  name                = "${var.service_name_hyphens}--${var.environment_hyphens}--EB-Env"
  application         = aws_elastic_beanstalk_application.main_app_elastic_beanstalk_application.name

  tier                = "WebServer"
  solution_stack_name = local.main_app_elastic_beanstalk_solution_stack_name
  cname_prefix        = "${var.service_name_hyphens}--${var.environment_hyphens}"


  // See this documentation for all the available settings
  // https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/command-options-general.html

  ///////////////
  // VPC
  setting {
    namespace = "aws:ec2:vpc"
    name      = "VPCId"
    value     = aws_vpc.vpc_main.id
  }
  setting {
    namespace = "aws:ec2:vpc"
    name      = "Subnets"
    value     = join(",", [aws_subnet.vpc_main__public_subnet_az1.id, aws_subnet.vpc_main__public_subnet_az2.id])
  }
  setting {
    namespace = "aws:ec2:vpc"
    name      = "ELBSubnets"
    value     = join(",", [aws_subnet.vpc_main__public_subnet_az1.id, aws_subnet.vpc_main__public_subnet_az2.id])
  }
  setting {
    namespace = "aws:ec2:vpc"
    name      = "ELBScheme"
    value     = "public"
  }
  setting {
    namespace = "aws:ec2:vpc"
    name      = "AssociatePublicIpAddress"
    value     = true
  }


  /////////////////////
  // Instances
  setting {
    namespace = "aws:ec2:instances"
    name      = "InstanceTypes"
    value     = local.main_app_elastic_beanstalk_ec2_instance_type
  }

  setting {
    namespace = "aws:autoscaling:launchconfiguration"
    name      = "IamInstanceProfile"
    value     = aws_iam_instance_profile.iam_instance_profile_eb__wordpress.name
  }
  setting {
    namespace = "aws:autoscaling:launchconfiguration"
    name      = "SecurityGroups"
    value     = aws_security_group.security_group_main_app_instances.id
  }


  /////////////////////////
  // Load Balancer
  setting {
    namespace = "aws:elasticbeanstalk:environment"
    name      = "LoadBalancerType"
    value     = "application"
  }
  setting {
    namespace = "aws:elbv2:loadbalancer"
    name      = "SecurityGroups"
    value     = aws_security_group.security_group_main_app_load_balancer.id
  }
  setting {
    namespace = "aws:elbv2:loadbalancer"
    name      = "ManagedSecurityGroup"
    value     = aws_security_group.security_group_main_app_load_balancer.id
  }


  //////////////////////////////////
  // Load Balancer Listener
  setting {
    namespace = "aws:elbv2:listener:default"
    name      = "ListenerEnabled"
    value     = "true"  // was false // disabled. we create our own port 80 listener which redirects to https
  }


  ////////////////////////
  // Auto-scaling
  setting {
    namespace = "aws:autoscaling:asg"
    name      = "MinSize"
    value     = local.main_app_elastic_beanstalk_min_instances
  }
  setting {
    namespace = "aws:autoscaling:asg"
    name      = "MaxSize"
    value     = local.main_app_elastic_beanstalk_max_instances
  }


  /////////////////////////////////
  // Auto-scaling Triggers
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "MeasureName"
    value     = "CPUUtilization"
  }
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "Statistic"
    value     = "Average"
  }
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "Unit"
    value     = "Percent"
  }
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "Period"
    value     = 1  // Time (in minutes) between checks
                   // Note: remember to update the other settings
  }                // BreachDuration = Period * EvaluationPeriods
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "EvaluationPeriods"
    value     = 3  // Number of consecutive checks that must be too high/low to trigger a scaling action
                   // Note: remember to update the other settings
  }                // BreachDuration = Period * EvaluationPeriods
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "BreachDuration"
    value     = 3  // How long (in minutes) must the checks be toon high/low before scaling up/down
                   // Note: remember to update the other settings
  }                // BreachDuration = Period * EvaluationPeriods
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "UpperThreshold"
    value     = 80  // If the CPU % stays above this level, we scale up
  }
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "UpperBreachScaleIncrement"
    value     = 1  // How many instances to add when we scale up
  }
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "LowerThreshold"
    value     = 50  // If the CPU % stays below this level, we scale down
  }
  setting {
    namespace = "aws:autoscaling:trigger"
    name      = "LowerBreachScaleIncrement"
    value     = -1  // How many instances to ADD when we scale down
  }                 // (this needs to be a negative number so we scale down!)


  ///////////////////////
  // Deployments
  setting {
    namespace = "aws:elasticbeanstalk:command"
    name      = "DeploymentPolicy"
    value     = "Rolling"
  }
  setting {
    namespace = "aws:elasticbeanstalk:command"
    name      = "BatchSizeType"
    value     = "Fixed"
  }
  setting {
    namespace = "aws:elasticbeanstalk:command"
    name      = "BatchSize"
    value     = 1
  }


  //////////////////////////////////////////////
  // Rolling Updates (to configuration)
  setting {
    namespace = "aws:autoscaling:updatepolicy:rollingupdate"
    name      = "RollingUpdateEnabled"
    value     = true
  }
  setting {
    namespace = "aws:autoscaling:updatepolicy:rollingupdate"
    name      = "RollingUpdateType"
    value     = "Health"
  }
  setting {
    namespace = "aws:autoscaling:updatepolicy:rollingupdate"
    name      = "MaxBatchSize"
    value     = 1
  }
  setting {
    namespace = "aws:autoscaling:updatepolicy:rollingupdate"
    name      = "MinInstancesInService"
    value     = 1
  }
  setting {
    namespace = "aws:autoscaling:updatepolicy:rollingupdate"
    name      = "PauseTime"  // How long should we pause between finishing updating one batch and starting updating the next batch
    value     = "PT0S"  // PT0S means "0 seconds" https://en.wikipedia.org/wiki/ISO_8601#Durations
  }


  ///////////////////////////
  // Sticky Sessions
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "StickinessEnabled"
    value     = false
  }


  /////////////////////////
  // Health Checks
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "HealthCheckPath"
    value     = local.main_app_elastic_beanstalk_health_check_path
  }
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "HealthCheckInterval"
    value     = 15
  }
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "HealthCheckTimeout"
    value     = 5
  }
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "MatcherHTTPCode"
    value     = local.main_app_elastic_beanstalk_health_check_matcher_http_code
  }
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "DeregistrationDelay"
    value     = 20
  }
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "HealthyThresholdCount"
    value     = 3
  }
  setting {
    namespace = "aws:elasticbeanstalk:environment:process:default"
    name      = "UnhealthyThresholdCount"
    value     = 5
  }


  //////////////////////
  // CloudWatch
  setting {
    namespace = "aws:elasticbeanstalk:cloudwatch:logs"
    name      = "RetentionInDays"
    value     = 7
  }
  setting {
    namespace = "aws:elasticbeanstalk:cloudwatch:logs"
    name      = "StreamLogs"
    value     = true
  }
  setting {
    namespace = "aws:elasticbeanstalk:cloudwatch:logs"
    name      = "DeleteOnTerminate"
    value     = false
  }

  setting {
    namespace = "aws:elasticbeanstalk:cloudwatch:logs:health"
    name      = "RetentionInDays"
    value     = 7
  }
  setting {
    namespace = "aws:elasticbeanstalk:cloudwatch:logs:health"
    name      = "HealthStreamingEnabled"
    value     = true
  }
  setting {
    namespace = "aws:elasticbeanstalk:cloudwatch:logs:health"
    name      = "DeleteOnTerminate"
    value     = false
  }


  /////////////////
  // PHP options
  setting {
    namespace = "aws:elasticbeanstalk:container:php:phpini"
    name      = "document_root"
    value     = "/web"
  }


  /////////////////////////////////
  // Environment variables
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "DB_HOST"
    value     = aws_db_instance.mysql_database.address
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "DB_NAME"
    value     = local.mysql_db_name
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "DB_USER"
    value     = local.mysql_username
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "DB_PASSWORD"
    value     = var.MYSQL_PASSWORD
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "DB_PREFIX"
    value     = "zt_"
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "WP_ENV"
    value     = "production"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "HTTPS"
    value     = "on"
    # WordPress checks that the full URL matches the protocol/domain that it's expecting
    # If not, it replies with a 302 Redirect to the canonical URL
    #
    # We've set the canonical URL to be https://[dev.]socialmobility.independent-commission.uk
    # CloudFront receives the request first
    # We ask CloudFront to forward the Host header ([dev.]socialmobility.independent-commission.uk) ✅
    # But CloudFront forwards the request to Elastic Beanstalk over HTTP, not HTTPS ❌
    #
    # So, WordPress would usually issue a 302 redirect to https://...
    # This results in a redirect loop
    #
    # We use this setting (in conjunction with some code in application.php) to tell WordPress that the user is connecting over HTTPS
    # So, now it thinks the protocol is https:// ✅
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "WP_HOME"
    value     = "https://${var.dns_record_subdomain_including_dot__main_website}${data.aws_route53_zone.route_53_zone_for_our_domain.name}"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "WP_SITEURL"
    value     = "https://${var.dns_record_subdomain_including_dot__main_website}${data.aws_route53_zone.route_53_zone_for_our_domain.name}/wp"
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "SESSION_COOKIE"
    value     = "smc_session"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "SESSION_SECURE_COOKIE"
    value     = 0
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "DEFAULT_TEMPLATE"
    value     = "templates/full.twig"
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "MAIL_RETURN_PATH_AND_REPLY_TO"
    value     = "smc-website@example.com"
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "WP_CACHE_HOME"
    value     = "/var/www/html/web/app/plugins/wp-super-cache/"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "WP_CACHE_STORE"
    value     = "/var/tmp/cache/"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "WP_CACHE_TIME"
    value     = 1  # Cache TTL in seconds
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "ACF_PRO_KEY"
    value     = var.ACF_PRO_KEY
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "AUTH_KEY"
    value     = var.AUTH_KEY
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "SECURE_AUTH_KEY"
    value     = var.SECURE_AUTH_KEY
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "LOGGED_IN_KEY"
    value     = var.LOGGED_IN_KEY
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "NONCE_KEY"
    value     = var.NONCE_KEY
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "AUTH_SALT"
    value     = var.AUTH_SALT
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "SECURE_AUTH_SALT"
    value     = var.SECURE_AUTH_SALT
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "LOGGED_IN_SALT"
    value     = var.LOGGED_IN_SALT
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "NONCE_SALT"
    value     = var.NONCE_SALT
  }

  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "APP_KEY"
    value     = var.APP_KEY
  }

}
