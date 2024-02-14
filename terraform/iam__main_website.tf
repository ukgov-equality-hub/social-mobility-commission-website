
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


resource "aws_iam_role_policy_attachment" "attach_AWSElasticBeanstalkWebTier" {
  role       = aws_iam_role.iam_role__wordpress.name
  policy_arn = "arn:aws:iam::aws:policy/AWSElasticBeanstalkWebTier"
}
