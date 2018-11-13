<?php
namespace Deployer;

use Deployer\Exception\Exception;
use Symfony\Component\Console\Input\ArgvInput;

require 'recipe/symfony.php';

// Project name
set('application', 'camdram');

// Project repository
set('repository', 'git@github.com:camdram/camdram.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', ['app/data']);

// Writable dirs by web server 
add('writable_dirs', ['app/data']);
set('allow_anonymous_stats', false);

// Hosts

host('production')
    ->hostname('antigone.camdram.net')
    ->user('deploy')
    ->stage('production')
    ->set('deploy_path', '/var/www/camdram/production')
    ->set('keep_releases', 2);

host('development')
    ->hostname('antigone.camdram.net')
    ->user('deploy')
    ->stage('development')
    ->set('deploy_path', '/var/www/camdram/dev/master')
    ->set('keep_releases', 1);
    
// Yarn tasks
set('bin/yarn', function () {
    return run('which yarn');
});

task('yarn:install', function () {
    if (has('previous_release')) {
        if (test('[ -d {{previous_release}}/node_modules ]')) {
            run('cp -R {{previous_release}}/node_modules {{release_path}}');
        }
    }
    run("cd {{release_path}} && {{bin/yarn}}");
})->desc('Install Yarn packages');

task('yarn:build', function () {
    run("cd {{release_path}} && {{bin/yarn}} build");
})->desc('Build assets');

// Database Tasks

task('database:update', function() {
    if (get('stage') == 'production')
    {
        run('{{bin/php}} {{bin/console}} doctrine:migrations:migrate {{console_options}}');
    }
    elseif(get('stage') == 'development')
    {
        run('{{bin/php}} {{bin/console}} camdram:database:refresh');
    }
})->desc('Refresh development database');

// Deployment Tasks

task('deploy:validate', function() {
    if (get('stage') == 'production' && (!input()->hasOption('tag') || empty(input()->getOption('tag')))) {
        throw new Exception('Only release tags can be deployed to production (e.g. --tag 20001010)');
    }
    if (get('stage') == 'development') {
        set('deploy_path', '/var/www/camdram/dev/'.input()->getOption('branch'));
    }
})->desc('Validate git target');

// If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

task('deploy', [
    'deploy:info',
    'deploy:validate',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:clear_paths',
    'deploy:create_cache_dir',
    'deploy:shared',
    'deploy:assets',
    'deploy:vendors',
    'yarn:install',
    'yarn:build',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:writable',
    'database:update',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy Camdram');
