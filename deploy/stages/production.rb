# Production settings
# ===============================================
set :stage, :production
set :application, "socialmobility.independent-commission.uk"
set :deploy_to, -> { "/home/szdev/sites/socialmobility.independent-commission.uk/web" }
set :tmp_dir, "/home/szdev/sites/socialmobility.independent-commission.uk/tmp"
set :server_name, "212.108.64.4"
set :ssh_user, "szdev"
set :keep_releases, 5

#  deploy to specified branch at runtime
# e.g. cap deploy branch=awesome-new-feature
# ==============================================
set :branch, 'master'

# WP CLI Features
# ===============
set :wpcli_remote_url, "https://socialmobility.independent-commission.uk"
set :wpcli_local_url,  :local_dev_url

# SSHKit.config.command_map[:composer] = "php #{shared_path.join("composer.phar")}"
SSHKit.config.command_map[:composer] = "php8.1 #{shared_path.join("composer.phar")}"

# Simple Role Syntax
# ==================
role :web, "#{fetch(:ssh_user)}@#{fetch(:server_name)}"

 # Extended Server Syntax
# ======================
server fetch(:server_name), user: fetch(:ssh_user), roles: %w{web}, primary: true

fetch(:default_env).merge!(wp_env: :production)
