# Staging settings
# ===============================================
set :stage, :staging
set :application, "social-mobility-commission-staging"
set :deploy_to, -> { "/home/szdev/sites/smc.zestydev.com/web" }
set :tmp_dir, "/home/szdev/sites/smc.zestydev.com/tmp"
set :server_name, "212.108.64.4"
set :ssh_user, "szdev"
set :keep_releases, 2

#  deploy to specified branch at runtime
# e.g. cap deploy branch=awesome-new-feature
# ==============================================
set :branch, ENV['branch'] || 'staging'

# WP CLI Features
# ===============
set :wpcli_remote_url, "https://smc.zestydev.com"
set :wpcli_local_url,  :local_dev_url

# SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"
SSHKit.config.command_map[:composer] = "php8.1 #{shared_path.join("composer.phar")}"

# Simple Role Syntax
# ==================
role :web, "#{fetch(:ssh_user)}@#{fetch(:server_name)}"

 # Extended Server Syntax
# ======================
server fetch(:server_name), user: fetch(:ssh_user), roles: %w{web}, primary: true

fetch(:default_env).merge!(wp_env: :staging)
