#!/usr/bin/env sh
set -eu

DB_PATH="${DB_DATABASE:-/var/www/html/storage/database.sqlite}"
BACKUP_DIR="${SPECTORA_BACKUP_DIR:-./backups}"
STAMP="$(date -u +%Y%m%d-%H%M%S)"
TARGET="${BACKUP_DIR}/spectora-${STAMP}.sqlite"

mkdir -p "${BACKUP_DIR}"

if [ ! -f "${DB_PATH}" ]; then
  echo "Database not found: ${DB_PATH}" >&2
  exit 1
fi

cp "${DB_PATH}" "${TARGET}"

for suffix in -wal -shm; do
  if [ -f "${DB_PATH}${suffix}" ]; then
    cp "${DB_PATH}${suffix}" "${TARGET}${suffix}"
  fi
done

echo "Backup written to ${TARGET}"
