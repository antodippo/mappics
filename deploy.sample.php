<?php
namespace Deployer;

require 'recipe/symfony4.php';

// This is the ip (or DNS) of the host where you want to deploy
host('<host_ip_address>')
    // This is the directory in the host
    ->set('deploy_path', '/var/www/mappics')
    // This is the user that is connecting to the host
    ->user('<user>')
    // You can also use you ssh key to connect to the host
    ->identityFile('~/.ssh/id_rsa');


set('application', 'mappics');
set('repository', 'git@github.com:antodippo/mappics.git');
set('git_tty', true);

add('shared_dirs', ['public/galleries', 'var/galleries', 'var/log', 'var/sessions']);
set('shared_files', ['.env', 'var/mappics_prod.db']);
set('bin_dir', 'bin');
set('var_dir', 'var');
set('assets', ['public/css', 'public/images', 'public/js']);

set('keep_releases', 3);

task('build', function () {
    run('cd {{release_path}} && build');
});

task('deploy:assets:install', function () {
    run('{{bin/php}} {{bin/console}} assets:install {{console_options}} {{release_path}}/public');
})->desc('Install bundle assets');

after('deploy:failed', 'deploy:unlock');

before('deploy:symlink', 'database:migrate');

