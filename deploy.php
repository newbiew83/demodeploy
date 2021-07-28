<?php   
    namespace Deployer;
    
    require 'recipe/laravel.php';
    require 'vendor/deployer/recipes/recipe/npm.php';
    
    // Project name
    set('application', 'Demo Deploy');
    
    // Project repository
    set('repository', 'git@github.com:newbiew83/demodeploy.git');
    
    
    // [Optional] Allocate tty for git clone. Default value is false.
    set('git_tty', false);
    
    // Shared files/dirs between deploys
    add('shared_files', []);
    add('shared_dirs', [
    'storage'
    ]);
    
    set('keep_releases', 5);
    
    // Writable dirs by web server
    add('writable_dirs', [
    
    ]);
    
    // Hosts
    host('demodeploy.newbiew.com')
    ->user('deployer')
    ->stage('staging') //stage name
    ->set('env', [
    'DB_DATABASE' => 'demodeploy', //hosting server database
    'DB_USERNAME' => 'stagingsiteuser',
    'DB_PASSWORD' => 'Pa$$word'
    ])
    ->identityFile('/Users/newbiew/.ssh/id_rsa')
    ->set('deploy_path', '/var/www/html/demodeploy'); //must be same as define at nginx host
    
    set('ssh_multiplexing', false);
    
    set('composer_options', 'install --verbose');
    
    // Tasks
    task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    // 'artisan:view:cache',
    'artisan:config:cache',
    'deploy:symlink',
    'deploy:failed',
    //'artisan:october',
    'artisan:migrate',
    'reload:php-fpm',
    'cleanup'
    ]);
    
    // [Optional] if deploy fails automatically unlock.
    after('deploy:failed', 'deploy:unlock');
    after('deploy:update_code', 'npm:install');
    
    // Migrate database before symlink new release.
    // task('artisan:october', function () {
    //     run('{{bin/php}} {{release_path}}/artisan october:up');
    // });
    
    task('artisan:migrate', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate');
    });
    
    
    task('reload:php-fpm', function () {
    run('sudo /usr/sbin/service php7.4-fpm reload');
    });

