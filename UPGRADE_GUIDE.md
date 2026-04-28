# Руководство по обновлению проекта до PHP 8.4 и Laravel 11

## ⚠️ ВАЖНО: Прочитайте перед началом

Это большое обновление, которое требует:
1. **Резервную копию проекта и базы данных**
2. **Обновление PHP на сервере до версии 8.4**
3. **Проверку всех зависимостей на совместимость**

## Что уже сделано

✅ Обновлен `composer.json`:
- PHP: `^8.2|^8.4`
- Laravel Framework: `^11.0`
- Все зависимости обновлены до совместимых версий

✅ Обновлен `bootstrap/app.php` для Laravel 11

✅ Создан `routes/web.php` (подключает `routes/main.php`)

## Шаги для завершения обновления

### Шаг 1: Обновите PHP на сервере

**Для Windows (OSPanel):**
1. Скачайте PHP 8.4 Thread Safe (TS) x64 с https://windows.php.net/download/
2. Распакуйте в `C:\OSPanel\modules\php\PHP_8.4\`
3. Скопируйте `php.ini-development` в `php.ini`
4. В `php.ini` найдите и раскомментируйте: `extension=gmp`
5. В OSPanel: Настройки → Модули → Выберите PHP 8.4
6. Перезапустите OSPanel
7. Проверьте: `php -v` (должно показать PHP 8.4.x)

**Для Linux:**
```bash
# Добавьте репозиторий PHP 8.4
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.4 php8.4-cli php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip
```

### Шаг 2: Обновите зависимости

```bash
# Удалите старые зависимости
composer clear-cache

# Обновите все зависимости
composer update --with-all-dependencies

# Если возникнут конфликты, попробуйте:
composer update --with-all-dependencies --ignore-platform-reqs
```

### Шаг 3: Обновите RouteServiceProvider (если нужно)

В Laravel 11 маршруты регистрируются в `bootstrap/app.php`, но `RouteServiceProvider` может остаться для обратной совместимости. Проверьте, что все маршруты работают.

### Шаг 4: Обновите конфигурационные файлы

Некоторые конфигурационные файлы могут быть удалены в Laravel 11. Проверьте:
- `config/app.php` - может потребовать обновления
- `config/auth.php` - должен работать без изменений
- `config/database.php` - должен работать без изменений

### Шаг 5: Очистите кэш

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Шаг 6: Запустите миграции (если нужно)

```bash
php artisan migrate
```

## Изменения в Laravel 11

### 1. Структура bootstrap/app.php

В Laravel 11 `bootstrap/app.php` использует новый функциональный подход:
```php
return Application::configure(...)
    ->withRouting(...)
    ->withMiddleware(...)
    ->withExceptions(...)
    ->create();
```

### 2. Middleware

Middleware теперь регистрируется в `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\Localization::class,
    ]);
    
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

### 3. RouteServiceProvider

В Laravel 11 `RouteServiceProvider` не обязателен, но может остаться для обратной совместимости.

## Потенциальные проблемы и решения

### Проблема 1: Пакеты не совместимы с Laravel 11

**Решение:** Проверьте документацию пакета или обновите до последней версии:
```bash
composer show package-name
composer update package-name
```

### Проблема 2: Ошибки при обновлении зависимостей

**Решение:** 
1. Удалите `composer.lock` и `vendor/`
2. Запустите `composer install` заново

### Проблема 3: Ошибки в коде после обновления

**Решение:** Проверьте breaking changes в Laravel 11:
- https://laravel.com/docs/11.x/upgrade

## Проверка после обновления

✅ Проверьте все маршруты:
```bash
php artisan route:list
```

✅ Проверьте аутентификацию:
- Вход в систему
- Регистрация
- Выход

✅ Проверьте работу контроллеров:
- Все CRUD операции
- Загрузка файлов
- Работа с базой данных

✅ Проверьте работу middleware:
- Аутентификация
- Админ-панель
- Локализация

## Установка Laravel Breeze

После успешного обновления до Laravel 11 и PHP 8.4:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run build
```

## Полезные ссылки

- [Laravel 11 Upgrade Guide](https://laravel.com/docs/11.x/upgrade)
- [Laravel Breeze Documentation](https://laravel.com/docs/11.x/breeze)
- [PHP 8.4 Release Notes](https://www.php.net/releases/8.4/en.php)
