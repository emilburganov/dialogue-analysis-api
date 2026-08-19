# Сервис для анализа диалогов — API

API для внутреннего сервиса анализа диалогов менеджеров с клиентами.

## Требования

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

## Запуск

```bash
docker compose up -d --build
```

При первом запуске автоматически:
- создаётся `.env` из `.env.example`, если не было файла `.env`
- устанавливаются зависимости Composer
- выполняются миграции и сидер

Тестовый пользователь:
- Email: `admin@example.com`
- Password: `password`

## API авторизации

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/api/login` | Получить Bearer token |
| GET | `/api/me` | Текущий пользователь |
| POST | `/api/logout` | Отозвать token |

Заголовок для защищённых маршрутов: `Authorization: Bearer {token}`

## Адреса

| Сервис | URL |
|--------|-----|
| API | http://localhost:8000 |
| pgAdmin | http://localhost:5050 |

**pgAdmin:** `admin@local.dev` / `admin`

Сервер PostgreSQL уже добавлен в pgAdmin.
