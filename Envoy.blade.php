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
    
    # Включаем подробный вывод
    set -x
    
    # Добавляем директорию в безопасные для Git
    git config --global --add safe.directory /home/h/human2xn/journalmh.org
    
    PHP84="/usr/local/php/cgi/8.4/bin/php"
    
    echo "Using PHP 8.4:"
    $PHP84 -v
    
    git fetch origin
    git reset --hard origin/main
    
    # Проверяем наличие .env
    if [ ! -f .env ]; then
        echo "Creating .env file..."
        cp .env.example .env
        $PHP84 artisan key:generate
    fi
    
    $PHP84 artisan down || echo "Artisan down failed"
    
    # Свежий composer без обертки
    rm -f composer.phar
    curl -sS https://getcomposer.org/installer | $PHP84 -- --install-dir=/tmp --filename=composer.phar
    mv /tmp/composer.phar .
    
    # Composer install с подробным выводом
    echo "Running composer install..."
    $PHP84 composer.phar install --no-dev --optimize-autoloader -vvv || {
        echo "Composer install failed with exit code $?"
        exit 1
    }
    
    $PHP84 artisan view:clear || true
    
    echo "Running migrations..."
    $PHP84 artisan migrate --force || {
        echo "Migrations failed"
        exit 1
    }
    
    $PHP84 artisan config:cache || true
    $PHP84 artisan event:cache || true
    $PHP84 artisan route:cache || true
    $PHP84 artisan view:cache || true
    
    $PHP84 artisan up
    echo "Done!"
@endtask

@story('full_deploy')
    push_to_github
    deploy
@endstory
