# Daynotes API

Daynotes is a mood tracking application with Telegram bot integration. Users can
log daily mood entries, view statistics, and receive scheduled notifications via
Telegram or email.

---

## Table of Contents

- [Requirements](#requirements)
- [Technology Stack](#technology-stack)
- [Development Tools](#development-tools)
- [Quick Start](#quick-start)
- [Telegram Bot Setup](#telegram-bot-setup)
- [Documentation](#documentation)

---

## Requirements

| Tool     | Version |
| -------- | ------- |
| PHP      | ^8.3    |
| Composer | ^2.x    |
| Node.js  | ^18.x   |
| MySQL    | ^8.0    |
| Docker   | ^24.x   |

---

## Technology Stack

### Backend

| Package                                  | Version | Purpose                       |
| ---------------------------------------- | ------- | ----------------------------- |
| `laravel/framework`                      | v12     | Core framework                |
| `laravel/sanctum`                        | v4      | API token authentication      |
| `lorisleiva/laravel-actions`             | v2.9    | Action pattern implementation |
| `laravel-notification-channels/telegram` | v6      | Telegram notifications        |

### Frontend

| Package               | Version | Purpose               |
| --------------------- | ------- | --------------------- |
| `tailwindcss`         | v4      | Utility-first CSS     |
| `vite`                | v7      | Frontend bundler      |
| `laravel-vite-plugin` | v2      | Laravel + Vite bridge |
| `axios`               | v1      | HTTP client           |

---

## Development Tools

| Tool                   | Purpose                          |
| ---------------------- | -------------------------------- |
| `laravel/pint`         | PHP code formatter               |
| `pestphp/pest`         | Testing framework (v4)           |
| `phpstan/phpstan`      | Static analysis                  |
| `larastan/larastan`    | Laravel-specific static analysis |
| `rector/rector`        | Automated code refactoring       |
| `prettier`             | JS/CSS formatter                 |
| `commitlint`           | Conventional commit validation   |
| `validate-branch-name` | Branch naming enforcement        |
| `laravel/boost`        | MCP server for AI-assisted dev   |

---

## Quick Start

### 1. Clone & install dependencies

```bash
git clone <repository-url>
cd daynotes.api/web

composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials and other settings. Refer to
`.env.example` for all available variables.

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Build frontend assets

```bash
# Production build
npm run build

# Development with hot reload
npm run dev
```

### 5. Start the application

```bash
# Built-in PHP server
php artisan serve

# All services concurrently (server + queue + vite)
composer run dev
```

### 6. Docker setup (alternative)

A Dockerfile is located in the parent `docker/` directory.

```bash
cd ..
docker-compose up -d
```

---

## Telegram Bot Setup

### Local development with ngrok

1. Install [ngrok](https://ngrok.com/) and authenticate.
2. Run the provided script to start ngrok and register the webhook
   automatically:

    ```bash
    chmod +x scripts/start-ngrok.sh
    ./scripts/start-ngrok.sh
    ```

    The script starts ngrok, updates `NGROK_URL` in `.env`, clears config cache,
    and registers the webhook with Telegram.

3. Or manually set the webhook:
    ```bash
    php artisan telegram:set-webhook
    ```

### Production

Set `APP_URL` to your HTTPS domain and run:

```bash
php artisan telegram:set-webhook
```

### Available bot commands

| Command      | Description             |
| ------------ | ----------------------- |
| `/start`     | Link Telegram account   |
| `/add`       | Log a new mood entry    |
| `/stats`     | View mood statistics    |
| `/analytics` | View detailed analytics |
| `/unlink`    | Unlink Telegram account |

---

## Documentation

| File                                         | Description                      |
| -------------------------------------------- | -------------------------------- |
| [docs/architecture.md](docs/architecture.md) | Laravel Actions architecture     |
| [docs/testing.md](docs/testing.md)           | Testing strategy and conventions |
| [docs/security.md](docs/security.md)         | Security recommendations         |
| [GIT_HOOKS_SETUP.md](GIT_HOOKS_SETUP.md)     | Git hooks setup guide            |

---

## Useful Artisan Commands

```bash
# Testing
php artisan test
php artisan test --filter=SomeName

# Code quality
vendor/bin/pint --dirty
vendor/bin/phpstan analyse

# Scheduled notifications
php artisan notifications:send

# Telegram webhook management
php artisan telegram:set-webhook
php artisan telegram:webhook-info
php artisan telegram:delete-webhook

```
