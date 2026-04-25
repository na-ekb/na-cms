# Деплой на Beget (ekb)

Деплой происходит автоматически при публикации тега `v*.*.*` через GitHub Actions → rsync → SSH.

Структура на сервере:
```
vk.na-ekb/
├── releases/
│   ├── 20250101120000/   ← старый релиз
│   └── 20250102130000/   ← текущий
├── current       →  releases/20250102130000
├── public_html   →  current
├── storage/              ← shared, не трогается при деплое
└── .env                  ← shared, обновляется при деплое
```

---

## Одноразовая настройка

### 1. SSH-ключ для CI

Сгенерировать отдельный ключ для GitHub Actions (не использовать личный):

```bash
ssh-keygen -t ed25519 -C "github-actions-na-cms" -f ~/.ssh/na_cms_deploy -N ""
```

Добавить публичный ключ на Beget:

```bash
cat ~/.ssh/na_cms_deploy.pub
```

Вставить в Beget → **Сайты → SSH-ключи** (или вручную на сервере):

```bash
ssh design24_niko@design24.beget.tech
echo "<публичный ключ>" >> ~/.ssh/authorized_keys
```

### 2. Получить known_hosts

```bash
ssh-keyscan design24.beget.tech
```

Скопировать весь вывод.

### 3. Добавить GitHub Secrets

Репо → **Settings → Secrets and variables → Actions → New repository secret**

| Secret | Значение |
|---|---|
| `SSH_PRIVATE_KEY` | содержимое `~/.ssh/na_cms_deploy` (приватный ключ) |
| `SSH_KNOWN_HOSTS` | вывод `ssh-keyscan design24.beget.tech` |
| `OCTOBER_COMPOSER_AUTH` | содержимое файла `auth.json` |
| `SOPS_AGE_KEY` | приватный age-ключ (строка `AGE-SECRET-KEY-1...` из `age.key`) |

### 4. Создать GitHub Environment

Репо → **Settings → Environments → New environment** → назвать `ekb`.

### 5. Подготовить структуру на сервере (первый деплой)

Если сервер чистый:

```bash
ssh design24_niko@design24.beget.tech
mkdir -p /home/d/design24/vk.na-ekb/releases
mkdir -p /home/d/design24/vk.na-ekb/storage/app/uploads
mkdir -p /home/d/design24/vk.na-ekb/storage/framework/{cache,sessions,views}
mkdir -p /home/d/design24/vk.na-ekb/storage/logs
chmod -R 775 /home/d/design24/vk.na-ekb/storage
```

Если уже был Magallanes — `public_html` это симлинк. Оставить как есть,
CI пересоздаст его сам при первом деплое.

### 6. Убедиться что composer есть на сервере

```bash
ssh design24_niko@design24.beget.tech
/home/d/design24/.local/bin/composer --version
```

Если нет:

```bash
mkdir -p ~/.local/bin
curl -sS https://getcomposer.org/installer | php8.2 -- --install-dir=~/.local/bin --filename=composer
```

---

## Запустить деплой

```bash
git tag v1.0.0
git push origin v1.0.0
```

GitHub Actions: Gitleaks → Build → Deploy. Следить: репо → **Actions**.

---

## Откат на предыдущий релиз

```bash
ssh design24_niko@design24.beget.tech

# посмотреть доступные релизы
ls /home/d/design24/vk.na-ekb/releases/

# переключить на нужный (подставить дату)
ln -sfn /home/d/design24/vk.na-ekb/releases/20250101120000 /home/d/design24/vk.na-ekb/current
```

`public_html` → `current` → нужный релиз. Изменение мгновенное.

---

## Что делает деплой

1. Создаёт `releases/YYYYMMDDHHMMSS/`
2. Заливает `.env` в shared папку
3. rsync файлов в новый релиз
4. Копирует `.env` из shared в релиз
5. Создаёт симлинк `storage → /home/d/design24/vk.na-ekb/storage`
6. `composer install --no-dev` на сервере
7. `artisan optimize:clear && optimize && october:migrate`
8. Переключает `current` → новый релиз
9. Переключает `public_html` → `current`
10. Удаляет старые релизы, оставляет 3 последних
