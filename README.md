# na-cms

October CMS — сайт NA Екатеринбург.

## Разработка

Требования: Docker, Docker Compose.

```bash
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan october:migrate
npm ci && npm run development
```

## Сборка фронтенда

```bash
npm run production
```

Собирает ассеты всех плагинов и модулей через Laravel Mix.

## Секреты

Секреты хранятся в репозитории в зашифрованном виде (SOPS + age).

[Инструкция по работе с секретами](SOPS.md)

## Деплой

Деплой на сервер через GitHub Actions — по кнопке после сборки тегом.

[Инструкция по деплою](DEPLOY.md)
