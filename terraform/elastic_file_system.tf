
resource "aws_efs_file_system" "efs_file_system__uploads" {
  creation_token = "${var.service_name_hyphens}--${var.environment_hyphens}--efs"
  tags = {
    Name = "${var.service_name_hyphens}--${var.environment_hyphens}--efs"
  }

  encrypted = true
  performance_mode = "generalPurpose"
  throughput_mode = "elastic"
}

resource "aws_efs_backup_policy" "efs_backup_policy__uploads" {
  file_system_id = aws_efs_file_system.efs_file_system__uploads.id

  backup_policy {
    status = "ENABLED"
  }
}


resource "aws_efs_mount_target" "efs_mount_target__uploads__az1" {
  file_system_id = aws_efs_file_system.efs_file_system__uploads.id
  subnet_id = aws_subnet.vpc_main__public_subnet_az1.id
  security_groups = [aws_security_group.security_group__efs.id]
}

resource "aws_efs_mount_target" "efs_mount_target__uploads__az2" {
  file_system_id = aws_efs_file_system.efs_file_system__uploads.id
  subnet_id = aws_subnet.vpc_main__public_subnet_az2.id
  security_groups = [aws_security_group.security_group__efs.id]
}


data "aws_iam_policy_document" "iam_policy_document__efs__uploads" {
  statement {
    effect = "Allow"

    principals {
      type = "AWS"
      identifiers = [aws_iam_role.iam_role__wordpress.arn]
    }

    actions = [
      "elasticfilesystem:ClientMount",
      "elasticfilesystem:ClientWrite",
      "elasticfilesystem:ClientRootAccess",
    ]

    resources = [aws_efs_file_system.efs_file_system__uploads.arn]

    condition {
      test = "Bool"
      variable = "aws:SecureTransport"
      values = ["true"]
    }
    condition {
      test = "Bool"
      variable = "elasticfilesystem:AccessedViaMountTarget"
      values = ["true"]
    }
  }
}

resource "aws_efs_file_system_policy" "efs_file_system_policy__uploads" {
  file_system_id = aws_efs_file_system.efs_file_system__uploads.id
  policy         = data.aws_iam_policy_document.iam_policy_document__efs__uploads.json
}


resource "aws_security_group" "security_group__efs" {
  name = "${var.service_name}__${var.environment}__Security_Group__EFS"
  description = "EFS security group (${var.service_name}/${var.environment})"
  vpc_id = aws_vpc.vpc_main.id

  tags = {
    Name = "${var.service_name}__${var.environment}__Security_Group__EFS"
  }
}

resource "aws_security_group_rule" "security_group__efs__ingress_port2049_ec2_instances" {
  security_group_id = aws_security_group.security_group__efs.id
  type              = "ingress"
  description       = "Allow ingress: Port 2049 from EC2 Instances"
  protocol          = "tcp"
  from_port         = 2049
  to_port           = 2049
  source_security_group_id = aws_security_group.security_group_main_app_instances.id
}

resource "aws_security_group_rule" "security_group__efs__egress_all" {
  security_group_id = aws_security_group.security_group__efs.id
  type              = "egress"
  description       = "Allow egress: all"
  protocol          = "-1"
  from_port         = 0
  to_port           = 0
  cidr_blocks       = ["0.0.0.0/0"]
}
