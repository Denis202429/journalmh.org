[human2xn@human2xn.beget.tech]: Welcome to LTD BeGet SSH Server 'chase'
[human2xn@human2xn.beget.tech]: ==================================================
[human2xn@human2xn.beget.tech]: DEPLOY START | 2026-09-28 17:03:01 +0300
[human2xn@human2xn.beget.tech]: Сервер: chase.beget.ru | каталог: /home/h/human2xn/journalmh.org
[human2xn@human2xn.beget.tech]: Комментарий локального коммита: crlf-test
[human2xn@human2xn.beget.tech]: Журнал этого запуска: /home/h/human2xn/journalmh.org/storage/logs/deploy-2026-09-28_17-03-04.log
[human2xn@human2xn.beget.tech]: ==================================================
[human2xn@human2xn.beget.tech]: Deploying...
[human2xn@human2xn.beget.tech]: Using PHP 8.4:
[human2xn@human2xn.beget.tech]: PHP 8.4.24 (cli) (built: Sep 11 2026 12:01:04) (NTS)
[human2xn@human2xn.beget.tech]: Copyright (c) The PHP Group
[human2xn@human2xn.beget.tech]: Zend Engine v4.4.24, Copyright (c) Zend Technologies
[human2xn@human2xn.beget.tech]: + git config --global --add safe.directory /home/h/human2xn/journalmh.org
[human2xn@human2xn.beget.tech]: + git pull origin main --no-rebase
[human2xn@human2xn.beget.tech]: From github.com:Denis202429/journalmh.org
[human2xn@human2xn.beget.tech]: * branch            main       -> FETCH_HEAD
[human2xn@human2xn.beget.tech]: Already up to date.
[human2xn@human2xn.beget.tech]: + git log -1 '--pretty=Задеплоен коммит: %h %ad %s' --date=short
[human2xn@human2xn.beget.tech]: Задеплоен коммит: 9b2b820 2026-09-28 Добавил инфо о регистрации3
[human2xn@human2xn.beget.tech]: + '[' '!' -f .env ']'
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan down
[human2xn@human2xn.beget.tech]: INFO  Application is now in maintenance mode.
[human2xn@human2xn.beget.tech]: + rm -f composer.phar
[human2xn@human2xn.beget.tech]: + curl -sS https://getcomposer.org/installer
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php -- --install-dir=/tmp --filename=composer.phar
[human2xn@human2xn.beget.tech]: All settings correct for using Composer
[human2xn@human2xn.beget.tech]: Downloading...
[human2xn@human2xn.beget.tech]: Composer (version 2.10.3) successfully installed to: /tmp/composer.phar
[human2xn@human2xn.beget.tech]: Use it: php /tmp/composer.phar
[human2xn@human2xn.beget.tech]: + mv /tmp/composer.phar .
[human2xn@human2xn.beget.tech]: mv: preserving permissions for ‘./composer.phar’: Operation not permitted
[human2xn@human2xn.beget.tech]: + echo 'Running composer install...'
[human2xn@human2xn.beget.tech]: Running composer install...
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php composer.phar install --no-dev --optimize-autoloader -vvv
[human2xn@human2xn.beget.tech]: Running 2.10.3 (2026-08-27 13:34:23) with PHP 8.4.24 on Linux / 5.10.258-0-beget-acl
[human2xn@human2xn.beget.tech]: Reading ./composer.json (/home/h/human2xn/journalmh.org/composer.json)
[human2xn@human2xn.beget.tech]: Loading config file ./composer.json (/home/h/human2xn/journalmh.org/composer.json)
[human2xn@human2xn.beget.tech]: Checked CA file /etc/pki/ca-trust/extracted/pem/tls-ca-bundle.pem does not exist or it is not a file.
[human2xn@human2xn.beget.tech]: Checked directory /etc/pki/ca-trust/extracted/pem/tls-ca-bundle.pem does not exist or it is not a directory.
[human2xn@human2xn.beget.tech]: Checked CA file /etc/pki/tls/certs/ca-bundle.crt does not exist or it is not a file.
[human2xn@human2xn.beget.tech]: Checked directory /etc/pki/tls/certs/ca-bundle.crt does not exist or it is not a directory.
[human2xn@human2xn.beget.tech]: Checked CA file /etc/ssl/certs/ca-certificates.crt: valid
[human2xn@human2xn.beget.tech]: Executing command (CWD): 'git' '--version'
[human2xn@human2xn.beget.tech]: Executing command (/home/h/human2xn/journalmh.org): 'git' 'branch' '-a' '--no-color' '--no-abbrev' '-v'
[human2xn@human2xn.beget.tech]: Failed to initialize global composer: Composer could not find the config file: /home/h/human2xn/.config/composer/composer.json
[human2xn@human2xn.beget.tech]: Reading /home/h/human2xn/journalmh.org/vendor/composer/installed.json
[human2xn@human2xn.beget.tech]: Loading plugin Http\Discovery\Composer\Plugin (from php-http/discovery)
[human2xn@human2xn.beget.tech]: Reading ./composer.lock (/home/h/human2xn/journalmh.org/composer.lock)
[human2xn@human2xn.beget.tech]: Installing dependencies from lock file
[human2xn@human2xn.beget.tech]: Verifying lock file contents can be installed on current platform.
[human2xn@human2xn.beget.tech]: Reading ./composer.lock (/home/h/human2xn/journalmh.org/composer.lock)
[human2xn@human2xn.beget.tech]: Built pool.
[human2xn@human2xn.beget.tech]: Running filter list pool filter.
[human2xn@human2xn.beget.tech]: Reading /home/h/human2xn/.cache/composer/repo/https---repo.packagist.org/packages.json from cache
[human2xn@human2xn.beget.tech]: Downloading https://repo.packagist.org/packages.json if modified
[human2xn@human2xn.beget.tech]: [304] https://repo.packagist.org/packages.json
[human2xn@human2xn.beget.tech]: Reading /home/h/human2xn/.cache/composer/repo/https---repo.packagist.org/filter-summary.json from cache
[human2xn@human2xn.beget.tech]: Downloading https://repo.packagist.org/lists/all/summary.json if modified
[human2xn@human2xn.beget.tech]: [304] https://repo.packagist.org/lists/all/summary.json
[human2xn@human2xn.beget.tech]: Generating rules
[human2xn@human2xn.beget.tech]: Resolving dependencies through SAT
[human2xn@human2xn.beget.tech]: Looking at all rules.
[human2xn@human2xn.beget.tech]: Dependency resolution completed in 0.001 seconds
[human2xn@human2xn.beget.tech]: Nothing to install, update or remove
[human2xn@human2xn.beget.tech]: Generating optimized autoload files
[human2xn@human2xn.beget.tech]: > pre-autoload-dump: Http\Discovery\Composer\Plugin->preAutoloadDump
[human2xn@human2xn.beget.tech]: > post-autoload-dump: Illuminate\Foundation\ComposerScripts::postAutoloadDump
[human2xn@human2xn.beget.tech]: > post-autoload-dump: @php artisan package:discover --ansi
[human2xn@human2xn.beget.tech]: Executing command (CWD): '/usr/local/php/cgi/8.4/bin/php' -d allow_url_fopen='1' -d disable_functions='' -d memory_limit='1536M' artisan package:discover --ansi
[human2xn@human2xn.beget.tech]: [37;44m INFO [39;49m Discovering packages.
[human2xn@human2xn.beget.tech]: barryvdh/laravel-debugbar
[human2xn@human2xn.beget.tech]: [90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m
[human2xn@human2xn.beget.tech]: [32;1mDONE[39;22m
[human2xn@human2xn.beget.tech]: laravel/scout
[human2xn@human2xn.beget.tech]: [90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m
[human2xn@human2xn.beget.tech]: [32;1mDONE[39;22m
[human2xn@human2xn.beget.tech]: laravel/tinker
[human2xn@human2xn.beget.tech]: [90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m [32;1mDONE[39;22m
[human2xn@human2xn.beget.tech]: maatwebsite/excel
[human2xn@human2xn.beget.tech]: [90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m
[human2xn@human2xn.beget.tech]: [32;1mDONE[39;22m
[human2xn@human2xn.beget.tech]: nesbot/carbon
[human2xn@human2xn.beget.tech]: [90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m
[human2xn@human2xn.beget.tech]: [32;1mDONE[39;22m
[human2xn@human2xn.beget.tech]: nunomaduro/termwind
[human2xn@human2xn.beget.tech]: [90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m[90m.[39m
[human2xn@human2xn.beget.tech]: [32;1mDONE[39;22m
[human2xn@human2xn.beget.tech]: 73 packages you are using are looking for funding.
[human2xn@human2xn.beget.tech]: Use the `composer fund` command to find out more!
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan view:clear
[human2xn@human2xn.beget.tech]: INFO  Compiled views cleared successfully.
[human2xn@human2xn.beget.tech]: + echo 'Running migrations...'
[human2xn@human2xn.beget.tech]: Running migrations...
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan migrate --force
[human2xn@human2xn.beget.tech]: INFO  Nothing to migrate.
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan config:cache
[human2xn@human2xn.beget.tech]: INFO  Configuration cached successfully.
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan event:cache
[human2xn@human2xn.beget.tech]: INFO  Events cached successfully.
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan route:cache
[human2xn@human2xn.beget.tech]: INFO  Routes cached successfully.
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan view:cache
[human2xn@human2xn.beget.tech]: INFO  Blade templates cached successfully.
[human2xn@human2xn.beget.tech]: + /usr/local/php/cgi/8.4/bin/php artisan up
[human2xn@human2xn.beget.tech]: INFO  Application is now live.
[human2xn@human2xn.beget.tech]: + set +x
[human2xn@human2xn.beget.tech]: ==================================================
[human2xn@human2xn.beget.tech]: DEPLOY DONE | 2026-09-28 17:03:09 +0300 | коммит 9b2b820
[human2xn@human2xn.beget.tech]: Журналы: /home/h/human2xn/journalmh.org/storage/logs/deploy-2026-09-28_17-03-04.log (этот запуск), /home/h/human2xn/journalmh.org/storage/logs/deploy.log (все деплои)
[human2xn@human2xn.beget.tech]: ==================================================
[human2xn@human2xn.beget.tech]: Done!
[human2xn@human2xn.beget.tech]: INFO  Application is already up.
