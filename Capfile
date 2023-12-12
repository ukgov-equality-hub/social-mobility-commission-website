set :deploy_config_path, 'deploy/deploy.rb'
set :stage_config_path,  'deploy/stages'

# Load DSL and Setup Up Stages
require 'capistrano/setup'
require 'capistrano/deploy'
require 'capistrano/composer'
require 'capistrano/wpcli'
require 'capistrano/ssh_doctor'
require 'capistrano/scm/git'
install_plugin Capistrano::SCM::Git

Dir.glob('lib/capistrano/tasks/*.cap').each { |r| import r }
