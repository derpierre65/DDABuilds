<?php

namespace Deployer;

require 'recipe/laravel.php';

set('application', 'builds.dundef.com');
set('repository', 'https://github.com/derpierre65/DDABuilds.git');
set('allow_anonymous_stats', false);
set('git_tty', true);
set('keep_releases', 2);

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

host('127.0.0.1')
	->identityFile('~/.ssh/root_rsa')
	->set('writable_mode', 'chmod')
	->set('deploy_path', '/var/www/{{application}}');

task('build', function () {
	run('cd {{release_path}} && build');
});

after('deploy:failed', 'deploy:unlock');
before('deploy:symlink', 'artisan:migrate');

task('reload:php-fpm', function () {
	run('sudo service php7.4-fpm reload');
});

task('artisan:cache:clear', function () {
	/* skipped */
});
task('artisan:view:cache', function () {
	/* skipped */
});

task('npm:install', function() {
	run('cd {{release_path}} && npm install');
});

task('npm:build', function() {
	run('cd {{release_path}} && npm run legacy:build');
});

after('deploy:shared', 'npm:install');
after('npm:install', 'npm:build');

// Reload php-fpm process.
after('deploy', 'reload:php-fpm');
after('rollback', 'reload:php-fpm');