# Решение проблем с обновлением Composer

## Проблема 1: Таймаут при загрузке
```
curl error 28 while downloading https://packagist.org/providers/ext-gmp.json: 
Operation timed out after 10012 milliseconds
```

## Проблема 2: Несовместимость версий
```
Your requirements could not be resolved to an installable set of packages.
- Root composer.json requires php ^8.2|^8.4 but your php version (8.1.9) does not satisfy that requirement.
- codename/parquet v0.7.2 requires ext-gmp * -> it is missing from your system.
```

## Решения

### ⚠️ ВАЖНО: Сначала обновите PHP!

Перед обновлением зависимостей необходимо:
1. **Обновить PHP до версии 8.4** (см. `PHP_UPDATE_INSTRUCTIONS.md`)
2. **Включить расширение gmp** в php.ini

### Решение 1: Использовать зеркало Packagist (для проблемы с таймаутом)

```bash
# Использовать японское зеркало (быстрее для некоторых регионов)
composer config -g repo.packagist composer https://mirror.packagist.jp

# Или использовать китайское зеркало
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# Или вернуться к оригинальному Packagist
composer config -g repo.packagist composer https://packagist.org
```

### Решение 2: Увеличить таймаут и повторить

```bash
# Таймаут уже увеличен до 600 секунд
composer update --with-all-dependencies --no-interaction
```

### Решение 3: Обновлять пакеты по частям

```bash
# Сначала обновить только Laravel Framework
composer update laravel/framework --with-dependencies

# Затем обновить остальные пакеты
composer update --with-all-dependencies
```

### Решение 4: Очистить кэш Composer

```bash
composer clear-cache
composer update --with-all-dependencies
```

### Решение 5: Использовать --prefer-dist (быстрее)

```bash
composer update --with-all-dependencies --prefer-dist --no-interaction
```

### Решение 6: Обновить с игнорированием платформенных требований (только для разработки!)

```bash
# ⚠️ ВНИМАНИЕ: Используйте только если PHP еще не обновлен, и только для разработки!
composer update --with-all-dependencies --ignore-platform-reqs
```

**НЕ используйте `--ignore-platform-reqs` в продакшене!** Это может привести к несовместимости и ошибкам.

## Проверка текущих настроек

```bash
# Проверить текущее зеркало
composer config -g repo.packagist

# Проверить таймаут
composer config -g process-timeout
```

## Если ничего не помогает

1. Проверьте интернет-соединение
2. Попробуйте использовать VPN
3. Попробуйте обновить в другое время (меньше нагрузка на Packagist)
4. Используйте `composer install` вместо `composer update` (если есть composer.lock)
