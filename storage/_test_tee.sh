#!/usr/bin/env bash
# Проверка механики логирования, которую использует Envoy.blade.php:
#   exec > >(tee -a "$LOG" | tee "$RUN_LOG") 2>&1
set -e

LOG=/tmp/envoy_t1.log
RUN=/tmp/envoy_t2.log
rm -f "$LOG" "$RUN"

exec 3>&1                                   # сохранили исходный stdout в fd 3
exec > >(tee -a "$LOG" | tee "$RUN") 2>&1   # весь вывод — в консоль, в LOG и в RUN

echo "line-1 (stdout)"
echo "line-2 (stderr)" >&2
set -x
echo "line-3 (xtrace)"
set +x
echo "line-4 (last)"
sleep 1

exec 1>&3 2>&3                              # вернули вывод в консоль (чтобы не читать лог «в себя»)
echo "=== строк в LOG: $(wc -l < "$LOG") | строк в RUN: $(wc -l < "$RUN") ==="
if diff -q "$LOG" "$RUN" >/dev/null 2>&1; then echo "OK: журналы идентичны"; else echo "FAIL: журналы различаются"; fi
echo "--- содержимое LOG ---"
cat "$LOG"
echo "--- содержимое RUN ---"
cat "$RUN"

echo "=== проверка кода возврата (subshell + временный файл) ==="
TMP="$(mktemp)"
(
    echo "внутри subshell"
    exit 3
) >"$TMP" 2>&1
RC=$?
cat "$TMP"
echo "RC=$RC (ожидается 3)"
rm -f "$TMP"
