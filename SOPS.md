# Управление секретами через SOPS + age

## Установка

```bash
brew install age sops
```

---

## Первоначальная настройка (один раз)

### 1. Сгенерировать ключевую пару

```bash
age-keygen -o age.key
```

Вывод будет примерно такой:
```
Public key: age1ql3z7hjy54pw3hyww5ayyfg7zqgvc7w3j2elw8zmrj2kg5sfn9aqmcac8p
```

### 2. Вставить публичный ключ в `.sops.yaml`

Открыть `.sops.yaml` и заменить плейсхолдер:

```yaml
creation_rules:
  - path_regex: \.env
    age: age1ql3z7hjy54pw3hyww5ayyfg7zqgvc7w3j2elw8zmrj2kg5sfn9aqmcac8p
```

### 3. Добавить приватный ключ в GitHub Secrets

В репо → **Settings → Secrets and variables → Actions → New repository secret**

- Name: `SOPS_AGE_KEY`
- Value: содержимое `age.key` (строка вида `AGE-SECRET-KEY-1...`)

> `age.key` уже добавлен в `.gitignore` — не коммитить.

---

## Зашифровать .env

```bash
cp .env .env.ekb                  # скопировать текущий .env как основу
sops -e .env.ekb > .env.ekb.enc   # зашифровать
rm .env.ekb                        # удалить незашифрованный
git add .env.ekb.enc
```

---

## Расшифровать локально (для редактирования)

```bash
export SOPS_AGE_KEY_FILE=age.key
sops -d .env.ekb.enc              # просто посмотреть
sops -d .env.ekb.enc > .env.ekb   # сохранить в файл
```

Или открыть прямо в редакторе (SOPS сам зашифрует при сохранении):

```bash
SOPS_AGE_KEY_FILE=age.key sops .env.ekb.enc
```

---

## Обновить секреты

```bash
SOPS_AGE_KEY_FILE=age.key sops .env.ekb.enc
# редактируем → сохраняем → SOPS автоматически перешифровывает
git add .env.ekb.enc && git commit -m "chore: update secrets"
```

---

## Добавить нового члена команды

У каждого разработчика свой `age.key`. Добавить его публичный ключ через запятую в `.sops.yaml`:

```yaml
creation_rules:
  - path_regex: \.env
    age: >-
      age1ql3z7hjy54pw3hyww5ayyfg7zqgvc7w3j2elw8zmrj2kg5sfn9aqmcac8p,
      age1<PUBLIC_KEY_TEAMMATE>
```

Затем перешифровать файлы под новый список ключей:

```bash
SOPS_AGE_KEY_FILE=age.key sops updatekeys .env.ekb.enc
```

---

## Как это работает в CI/CD

В `pipeline.yml` секрет `SOPS_AGE_KEY` передаётся как переменная окружения,
SOPS читает его и расшифровывает `.env.ekb.enc` перед деплоем.

```yaml
- name: Decrypt .env
  run: sops -d .env.ekb.enc > .env.production
  env:
    SOPS_AGE_KEY: ${{ secrets.SOPS_AGE_KEY }}
```
