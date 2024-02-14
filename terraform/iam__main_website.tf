
data "aws_iam_policy_document" "assume_role" {
  statement {
    effect = "Allow"

    principals {
      type        = "Service"
      identifiers = ["ec2.amazonaws.com"]
    }

    actions = ["sts:AssumeRole"]
  }
}

resource "aws_iam_role" "iam_role__wordpress" {
  name               = "${var.service_name_hyphens}--${var.environment_hyphens}--EB-Role-WordPress"
  assume_role_policy = data.aws_iam_policy_document.assume_role.json
}

resource "aws_iam_instance_profile" "iam_instance_profile_eb__wordpress" {
  name = "${var.service_name_hyphens}--${var.environment_hyphens}--EB-InstanceProfile-WordPress"
  role = aws_iam_role.iam_role__wordpress.name
}

data "aws_iam_policy_document" "iam_policy_document__allow_wordpress_to_access_uploads_s3_bucket" {
  statement {
    effect    = "Allow"
    actions   = ["s3:*"]
    resources = [
      aws_s3_bucket.s3_bucket__uploads.arn,             // The bucket itself (to enable listing of files)
      "${aws_s3_bucket.s3_bucket__uploads.arn}/*",      // The files within the bucket (to create / update / delete files)
    ]
  }
}

resource "aws_iam_policy" "iam_policy__allow_wordpress_to_access_uploads_s3_bucket" {
  name        = "${var.service_name_hyphens}--${var.environment_hyphens}--Policy--Allow-WordPress-To-Access-Static-Site-S3-Bucket"
  description = "Policy to allow WordPress Elastic Beanstalk instances to access the Uploads S3 bucket"
  policy      = data.aws_iam_policy_document.iam_policy_document__allow_wordpress_to_access_uploads_s3_bucket.json
}

resource "aws_iam_role_policy_attachment" "iam_policy_attachment__allow_publisher_to_access_static_site_s3_bucket" {
  role       = aws_iam_role.iam_role__wordpress.name
  policy_arn = aws_iam_policy.iam_policy__allow_wordpress_to_access_uploads_s3_bucket.arn
}


resource "aws_iam_role_policy_attachment" "attach_AWSElasticBeanstalkWebTier" {
  role       = aws_iam_role.iam_role__wordpress.name
  policy_arn = "arn:aws:iam::aws:policy/AWSElasticBeanstalkWebTier"
}
