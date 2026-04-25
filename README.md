# na-cms

October CMS — сайт НА Екатеринбург.

## Разработка

Требования: PHP 8.2, Node 20, Composer.

```bash
cp .env.example .env
composer install
npm ci
npm run development
php artisan key:generate
php artisan october:migrate
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
