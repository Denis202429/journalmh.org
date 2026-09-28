@servers(['local' => '127.0.0.1', 'beget' => 'human2xn@human2xn.beget.tech'])

@setup
// Envoy передаёт имя запускаемой задачи в переменной $__task
$currentTask = $__task ?? null;

// Сообщение для коммита требуется только задачам, которые коммитят и деплоят
if (in_array($currentTask, ['push_to_github', 'full_deploy'], true)
    && (!isset($message) || trim((string) $message) === '')) {
    throw new Exception("Необходимо передать сообщение для коммита! Например: envoy run full_deploy --message='Fix bug'");
}

// Каталог проекта на хостинге
$appPath = '/home/h/human2xn/journalmh.org';

// Логи деплоя на хостинге (storage/logs не попадает в git — см. storage/logs/.gitignore)
$logsPath  = $appPath . '/storage/logs';
$deployLog = $logsPath . '/deploy.log';                              // общий журнал (дописывается)
$runLog    = $logsPath . '/deploy-' . date('Y-m-d_H-i-s') . '.log';  // отдельный файл на каждый запуск

// Журнал локальных git-операций (путь относительно каталога запуска envoy)
$localLog  = 'storage/logs/deploy-local.log';
@endsetup

{{--
    Куда попадают результаты деплоя:
      хостинг:  {{ $appPath }}/storage/logs/deploy.log                    — общий журнал всех деплоев
                {{ $appPath }}/storage/logs/deploy-ГГГГ-ММ-ДД_ЧЧ-ММ-СС.log — лог конкретного запуска
      локально: {{ $localLog }}                                            — что делал git при push
    Посмотреть журнал на хостинге: envoy run deploy_log
    Дублировать весь вывод в локальный файл:
      envoy run full_deploy --message="..." 2>&1 | tee -a storage/logs/deploy-local-full.log
--}}

@task('push_to_github', ['on' => 'local'])
LOG="{{ $localLog }}"
mkdir -p "$(dirname "$LOG")"
TMP="$(mktemp)"
export GIT_DISCOVERY_ACROSS_FILESYSTEM=1
(
echo "=================================================="
echo "LOCAL GIT | $(date '+%Y-%m-%d %H:%M:%S') | commit: {{ $message }}"
echo "=================================================="
git add .
ADD_RC=$?
git commit -m "{{ $message }}"
COMMIT_RC=$?
if [ "$COMMIT_RC" -ne 0 ]; then
echo "--- git commit: коммит не создан (код $COMMIT_RC, обычно это «нечего коммитить») ---"
fi
git push origin main
PUSH_RC=$?
echo "--------------------------------------------------"
echo "add=$ADD_RC commit=$COMMIT_RC push=$PUSH_RC | $(date '+%Y-%m-%d %H:%M:%S')"
[ "$PUSH_RC" -eq 0 ] || exit "$PUSH_RC"
) >"$TMP" 2>&1
RC=$?
cat "$TMP"
cat "$TMP" >>"$LOG"
rm -f "$TMP"
[ "$RC" -eq 0 ] || exit "$RC"
@endtask

@task('deploy', ['on' => 'beget'])
cd {{ $appPath }}
set -e

LOG="{{ $deployLog }}"
RUN_LOG="{{ $runLog }}"
mkdir -p {{ $logsPath }}

# Весь дальнейший вывод (включая set -x) идёт в консоль, в общий журнал и в лог этого запуска
exec > >(tee -a "$LOG" | tee "$RUN_LOG") 2>&1

PHP84="/usr/local/php/cgi/8.4/bin/php"

# Что бы ни случилось (ошибка команды, обрыв связи) — вернуть сайт из режима обслуживания
trap '$PHP84 artisan up || true' EXIT

echo "=================================================="
echo "DEPLOY START | $(date '+%Y-%m-%d %H:%M:%S %z')"
echo "Сервер: $(hostname) | каталог: $(pwd)"
echo "Комментарий локального коммита: {{ $message }}"
echo "Журнал этого запуска: $RUN_LOG"
echo "=================================================="

echo "Deploying..."
echo "Using PHP 8.4:"
$PHP84 -v

# Включаем подробный вывод
set -x

# Добавляем директорию в безопасные для Git
git config --global --add safe.directory {{ $appPath }}

git pull origin main --no-rebase
git log -1 --pretty='Задеплоен коммит: %h %ad %s' --date=short

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
set +x
echo "=================================================="
echo "DEPLOY DONE | $(date '+%Y-%m-%d %H:%M:%S %z') | коммит $(git rev-parse --short HEAD)"
echo "Журналы: $RUN_LOG (этот запуск), $LOG (все деплои)"
echo "=================================================="
# Небольшая пауза, чтобы tee успел дописать журнал до закрытия SSH-сессии
sleep 1
echo "Done!"
@endtask

# Показать журнал последних деплоев на хостинге
@task('deploy_log', ['on' => 'beget'])
cd {{ $appPath }}
LOG="{{ $deployLog }}"
if [ ! -f "$LOG" ]; then
echo "Журнала $LOG пока нет — выполните деплой (envoy run full_deploy --message='...')."
exit 1
fi
echo "=== Последние 150 строк $LOG ==="
tail -n 150 "$LOG"
echo
echo "=== Файлы журналов запусков (последние 10) ==="
ls -lt storage/logs/deploy-*.log 2>/dev/null | head -n 10 || true
@endtask

@story('full_deploy')
push_to_github
deploy
@endstory