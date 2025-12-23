# Git Hooks Setup

Цей проект використовує git hooks для автоматичної перевірки якості коду перед
комітом та пушем.

## Що встановлено

### Git Hooks (.githooks/)

- **pre-commit** - автоматично форматує код за допомогою Rector, Pint та
  Prettier
- **commit-msg** - перевіряє формат повідомлення коміту за допомогою Commitlint
- **pre-push** - перевіряє назву гілки

### Інструменти

#### PHP

- **Rector** - автоматичне рефакторингування та оновлення коду
- **Laravel Pint** - форматування PHP коду

#### JavaScript

- **Prettier** - форматування JS/TS/CSS файлів
- **Commitlint** - валідація повідомлень комітів
- **validate-branch-name** - валідація назв гілок

## Швидкий старт

### 1. Запустіть Docker контейнери

```bash
cd /Users/ihorshtohryn/Documents/web2025/projects/daynotes/docker
docker compose -f dev-docker-compose.yml up -d --build
```

### 2. Встановіть залежності

```bash
# PHP залежності (включаючи Rector)
docker compose -f dev-docker-compose.yml exec app composer install

# JavaScript залежності (включаючи Prettier, Commitlint)
docker compose -f dev-docker-compose.yml exec app npm install
```

### 3. Git hooks вже налаштовані!

Git hooks вже налаштовані та готові до використання. Вони автоматично
запускатимуться при комітах та пушах.

## Формат назв гілок

Гілки повинні відповідати одному з патернів:

- `feature/*` - для нових функцій
- `bugfix/*` - для виправлення багів
- `hotfix/*` - для термінових виправлень
- `release/*` - для релізів
- `master`, `main`, `develop`, `staging` - основні гілки

**Приклади валідних назв:**

```
feature/user-authentication
bugfix/fix-login-error
hotfix/critical-security-patch
```

## Формат повідомлень комітів

Використовується [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>: <subject>

<body>
```

**Доступні типи:**

- `feat` - нова функція
- `fix` - виправлення бага
- `docs` - зміни в документації
- `style` - форматування коду
- `refactor` - рефакторинг
- `perf` - покращення продуктивності
- `test` - додавання тестів
- `build` - зміни в системі збірки
- `ci` - зміни в CI/CD
- `chore` - інші зміни

**Приклади:**

```bash
git commit -m "feat: add user authentication"
git commit -m "fix: resolve login validation error"
git commit -m "docs: update API documentation"
```

## Запуск інструментів вручну

### Rector

```bash
# Перевірка (без змін)
docker compose -f dev-docker-compose.yml exec app composer rector:check

# Автоматичне виправлення
docker compose -f dev-docker-compose.yml exec app composer rector:fix
```

### Laravel Pint

```bash
# Перевірка (без змін)
docker compose -f dev-docker-compose.yml exec app composer pint:check

# Автоматичне виправлення
docker compose -f dev-docker-compose.yml exec app composer pint:fix
```

### Prettier

```bash
# Автоматичне виправлення
docker compose -f dev-docker-compose.yml exec app npm run prettier:fix
```

### Commitlint (для тестування)

```bash
echo "feat: test commit" | docker compose -f dev-docker-compose.yml exec -T app npx commitlint
```

## Як працюють hooks

### Pre-commit

1. Запускає Rector для автоматичного рефакторингу PHP
2. Запускає Pint для форматування PHP коду
3. Запускає Prettier для форматування JS/TS/CSS
4. Автоматично додає змінені файли до staging area

### Commit-msg

1. Перевіряє формат повідомлення коміту
2. Відхиляє коміт якщо формат невірний

### Pre-push

1. Перевіряє назву поточної гілки
2. Відхиляє push якщо назва гілки невірна

## Відключення hooks (не рекомендується)

Якщо потрібно тимчасово відключити hooks:

```bash
git commit --no-verify -m "message"
git push --no-verify
```

## Troubleshooting

### Hooks не запускаються

Перевірте що git налаштований на використання .githooks:

```bash
git config core.hooksPath
# Повинно повернути: .githooks
```

Якщо ні, встановіть:

```bash
git config core.hooksPath .githooks
```

### Файли hooks не виконуються

Переконайтесь що вони мають права на виконання:

```bash
chmod +x .githooks/commit-msg .githooks/pre-commit .githooks/pre-push
```

### Docker контейнер 'app' не знайдено

Переконайтесь що ви запустили контейнери з оновленого docker-compose:

```bash
docker compose -f dev-docker-compose.yml up -d --build
```
