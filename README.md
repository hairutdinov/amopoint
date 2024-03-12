## Создание файла с переменными окружения
```shell
cp .env.example .env
```

## Сборка проекта
```shell
docker compose build
```

## Поднятие проекта
```shell
docker compose up -d
```

### Проверка, что все 3 контейнера подняты 
```shell
docker compose ps
```

## Установка всех зависимостей
```shell
docker compose exec php composer install
```

## Применение миграций
```shell
docker compose exec php vendor/bin/phinx migrate -e development
```

Проект можно открыть по адресу `http://localhost`

