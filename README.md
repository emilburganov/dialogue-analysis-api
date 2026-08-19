# Сервис для анализа диалогов — API

API для внутреннего сервиса анализа диалогов менеджеров с клиентами.

## Требования

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

## Запуск

```bash
docker compose up -d --build
```

При первом запуске автоматически:
- создаётся `.env` из `.env.example`, если не было файла .env
- устанавливаются зависимости Composer
- выполняются миграции

## Адреса

| Сервис | URL |
|--------|-----|
| API | http://localhost:8000 |
| pgAdmin | http://localhost:5050 |

**pgAdmin:** `admin@local.dev` / `admin`

Сервер PostgreSQL уже добавлен в pgAdmin.
