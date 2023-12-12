# General settings
# ===============================================
set :repo_url, 'git@git.simplyzesty.com:/os-dev/social-mobility-council.git'
set :theme_version_path, 'web/app/themes/smc/style.css'

# Links files & Dirs are shared across deployments
# ===============================================
set :linked_files, %w{.env}
set :linked_dirs, %w{web/app/uploads vendor web/app/languages var/cache web/app/plugins/wp-super-cache}

# Lower level settings you typically don't need to change
# ===============================================
set :copy_exclude, [".git/*", ".svn/*", ".DS_Store", ".gitkeep", ".idea/*"]
set :log_level, :info
set :use_sudo, false
set :deploy_via, :remote_cache
set :composer_install_flags, "--no-dev --verbose --prefer-dist --optimize-autoloader"
set :interactive_mode, true

set :ssh_options, {
   keys: %w(~/.ssh/id_ed25519),
   forward_agent: true,
}

set :local_dev_url, (
    (File.read('./.env').match /^LOCAL_DEV_URL=['"]?([^\s'"]+)/
) || [])[1]

# At the end of a deploy, clear the cache and restart services.
before "deploy:finishing", "wpcli:cache:clear_supercache"
# before "deploy:finishing", "wpcli:cache:clear"
before "deploy:finishing", "deploy:clear_cache"
before "deploy:finishing", "deploy:restart"

desc "Check that we can access everything"
task :check_write_permissions do
  on roles(:all) do |host|
    if test("[ -w #{fetch(:deploy_to)} ]")
      info "#{fetch(:deploy_to)} is writable on #{host}"
    else
      error "#{fetch(:deploy_to)} is not writable on #{host}"
    end
  end
end

desc "Check if agent forwarding is working"
task :forwarding do
  on roles(:all) do |h|
    if test("env | grep SSH_AUTH_SOCK")
      info "Agent forwarding is up to #{h}"
    else
      error "Agent forwarding is NOT up to #{h}"
    end
  end
end

namespace :deploy do

  before :updated, :assets_version
  before :updated, :theme_version

  desc 'Assets cachebuster'
  task :assets_version do
    on roles(:web) do
      execute "sed -i 's/TIMESTAMP_HERE/#{fetch(:release_timestamp)}/g' #{fetch(:release_path)}/config/application.php"
    end
  end

  desc 'Theme version'
  task :theme_version do
    on roles(:web) do
      version = "#{fetch(:branch)} #{fetch(:current_revision)}"
      version  = version.gsub('/', '\/')
      execute "sed -i 's/THEME_VERSION/#{version}/g' #{fetch(:release_path)}/#{fetch(:theme_version_path)}"
    end
  end

  desc 'Restart services'
  task :restart do
    on roles(:web) do
      #execute "sudo service php7.0-fpm restart"
      #execute "sudo service nginx restart"
    end
  end

  desc 'Upload Environment cfg'
  task :env do
    on roles(:web) do
      file = File.open( ".env.#{fetch(:stage)}")
      upload! file, "#{shared_path}/.env"
    end
  end

	desc 'Clear the PHP opcache.'
	task :clear_cache do
	 	on roles(:web) do
	 		within fetch(:release_path) do
				execute :curl, "-ksL #{fetch(:wpcli_remote_url)}/opcache_clear.php?clear=doitnow"
			end
		end
	end

end

desc "ssh into :deploy_to"
task :ssh do
  on roles(:web), :primary => true do |host|
    command = "cd #{fetch(:deploy_to)} ; \$SHELL -l"
    exec "ssh -l #{host.user} #{host.hostname} -t '#{command}'"
  end
end

namespace :wpcli do


    desc 'Fix paths in database'
    task :fix_paths do
      on roles(:web) do
        within current_path do

          execute :wp, "search-replace", "/vagrant/nginx.conf", "/home/szdev/#{fetch(:application)}/nginx.conf", fetch(:wpcli_args) || "--skip-columns=guid"
          execute :wp, "search-replace", "/vagrant/nginx.conf", "/home/szdev/#{fetch(:application)}/ithemes-security/logs", fetch(:wpcli_args) || "--skip-columns=guid"

          # because the default task skips GUIDs
          execute :wp, "search-replace", "#{fetch(:wpcli_local_url)}", "#{fetch(:wpcli_remote_url)}"
         end
      end
    end

    desc 'perform Yoast Re-Index  can be slow'
    task :yoast_reindex do
        on roles(:web) do
            within current_path do
               print "[DEBUG] THIS WILL TAKE A WHILE - COME BACK IN about 6 mins\r\n"
               execute :wp, "yoast"," index ","--reindex ","--skip-confirmation"
            end
        end
    end


  namespace :cache do
      desc 'Clear supercache'
      task :clear_supercache do
        on roles(:web) do
          execute "rm -rf #{shared_path}/var/cache/supercache"
        end
      end
  end

  namespace :cache do
      desc 'Clear caches'
      task :clear do
        on roles(:web) do
          # execute "cd #{current_path} && php console app:cache:clear"
          #execute "cd #{current_path}"
          #execute :wp, "cache flush"
           print "[DEBUG] local url: #{fetch(:wpcli_local_url)}"
           print "[DEBUG] remote url: #{fetch(:wpcli_remote_url)}"
           print "[DEBUG] wordpress path: #{fetch(:wpcli_path)}"
        end
      end
  end

  namespace :setp do
    desc 'Clear caches'
    task :coreinstall do
      on roles(:web) do
        execute "cd #{current_path}"
        execute :wp, "core install"
      end
    end
  end

  namespace :auto_update do

    desc 'Attempt to maybe auto update core'
    task :core do
      on roles(:web) do
        within current_path do

          temp_file = "#{current_path}/wp_maybe_auto_update.php"
          execute :touch, "#{temp_file}"
          execute :chmod, "-R 644 #{temp_file}"
          execute :echo, "'<?php require( dirname(__FILE__) . \"/web/wp/wp-load.php\" ); wp_maybe_auto_update();' >	#{temp_file}"
          execute :php, "#{temp_file} && rm -f #{temp_file}"
          execute :wp, "core version"

        end
      end
	end

  end

end
