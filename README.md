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

Тестовые пользователи (пароль: `password`):
- `admin@example.com` — администратор, видит все диалоги
- `anna@example.com` — менеджер, видит только свои диалоги
- `igor@example.com` — клиент, видит только свои диалоги

## API

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/api/login` | Авторизация |
| GET | `/api/me` | Текущий пользователь |
| POST | `/api/logout` | Выход |
| GET | `/api/dialogues` | Список диалогов |
| GET | `/api/dialogues/{id}` | Диалог с сообщениями |
| POST | `/api/dialogues/{id}/messages` | Отправить сообщение |

Заголовок: `Authorization: Bearer {token}`

## Адреса

| Сервис | URL |
|--------|-----|
| API | http://localhost:8000 |
| pgAdmin | http://localhost:5050 |

**pgAdmin:** `admin@local.dev` / `admin`

Сервер PostgreSQL уже добавлен в pgAdmin.
