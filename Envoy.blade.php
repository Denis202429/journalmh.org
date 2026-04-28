@servers(['local' => '127.0.0.1', 'beget' => 'human2xn@human2xn.beget.tech'])

@setup
    if (!isset($message)) {
        throw new Exception("Необходимо передать сообщение для коммита! Например: envoy run deploy --message='Fix bug'");
    }
@endsetup

@task('push_to_github', ['on' => 'local'])
    echo "Добавление изменений в Git..."
    export GIT_DISCOVERY_ACROSS_FILESYSTEM=1
    git add .
    git commit -m "{{ $message }}"
    git push origin main
@endtask

@task('deploy', ['on' => 'beget'])
    cd /home/h/human2xn/journalmh.org
    set -e
    echo "Deploying..."
    
    PHP84="/usr/local/php/cgi/8.4/bin/php"
    
    echo "Using PHP 8.4:"
    $PHP84 -v
    
    git fetch origin
    git reset --hard origin/main
    
    $PHP84 artisan down
    
    # Свежий composer без обертки
    rm -f composer.phar
    curl -sS https://getcomposer.org/installer | $PHP84 -- --install-dir=/tmp --filename=composer.phar
    mv /tmp/composer.phar .
    $PHP84 composer.phar install --no-dev --optimize-autoloader
    
    # Только view:clear (component:clear не существует в этой версии Laravel)
    $PHP84 artisan view:clear
    
    $PHP84 artisan migrate --force
    $PHP84 artisan config:cache
    $PHP84 artisan event:cache
    $PHP84 artisan route:cache
    $PHP84 artisan view:cache
    $PHP84 artisan up
    echo "Done!"
@endtask

@story('full_deploy')
    push_to_github
    deploy
@endstory