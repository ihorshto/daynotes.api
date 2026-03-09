# Testing Strategy

## Overview

This project uses [Pest v4](https://pestphp.com/) built on PHPUnit v12. All
tests are written using Pest's expressive syntax. The goal is high confidence
through readable, maintainable tests — not line coverage for its own sake.

---

## Test Types

| Type    | Location         | Purpose                                                 |
| ------- | ---------------- | ------------------------------------------------------- |
| Feature | `tests/Feature/` | Test actions, HTTP endpoints, and database interactions |
| Unit    | `tests/Unit/`    | Test isolated classes: pure logic, models, helpers      |
| Browser | `tests/Browser/` | End-to-end tests in a real browser (Pest v4)            |

The default choice is a **feature test**. Use a unit test only when the logic is
fully self-contained and does not require a database or HTTP context.

---

## Running Tests

```bash
# Full suite
php artisan test

# Single file
php artisan test tests/Feature/Path/To/TestFile.php

# Filter by name
php artisan test --filter=SomeName

# With code coverage
php artisan test --coverage
```

Run only the tests related to your changes first. Once they pass, run the full
suite to check for regressions.

---

## General Conventions

### Test names describe behaviour

Name tests in plain English. The name should explain what the system does, not
what function is called:

```php
it('creates a mood entry for the authenticated user', function () { ... });
it('returns forbidden when user modifies another user\'s data', function () { ... });
it('ignores stale Telegram messages', function () { ... });
```

### Factories over manual setup

Always use model factories to create test data. Check if the factory has
existing states before setting attributes manually.

### Specific assertions

Use the most specific assertion method available rather than a generic status
check. Examples: `assertCreated()`, `assertForbidden()`, `assertNotFound()`,
`assertUnprocessable()`.

### Datasets for validation rules

Use Pest datasets to avoid repeating the same test body for multiple invalid
inputs:

```php
it('rejects invalid input', function (mixed $value) {
    // single test body, many data variations
})->with([
    'too low'  => [0],
    'too high' => [999],
    'null'     => [null],
]);
```

---

## What to Test

Every feature should cover three categories of paths:

| Path    | What to verify                                                       |
| ------- | -------------------------------------------------------------------- |
| Happy   | Successful operation, correct status code, correct response shape    |
| Failure | Validation errors, missing resources, wrong or unexpected state      |
| Auth    | Unauthenticated requests return 401, unauthorised access returns 403 |

Edge cases (boundary values, empty collections, null optional fields) should be
added as they are discovered.

---

## Mocking

Mock only external systems — Telegram API, mail, third-party HTTP calls. Never
mock the database or Eloquent models; use factories and `RefreshDatabase`
instead.

Use the `mock()` helper from Pest or `$this->mock()`, and always import it
explicitly:

```php
use function Pest\Laravel\mock;
```

---

## Static Analysis

PHPStan with Larastan catches type errors before tests run:

```bash
vendor/bin/phpstan analyse
```

This is a required step before finalising any change. Treat PHPStan errors the
same as failing tests.

---

## Browser Tests (Pest v4)

Browser tests exercise the UI in a real browser. They support Laravel test
helpers (`actingAs()`, `Event::fake()`, `RefreshDatabase`) alongside browser
interactions (click, fill, select, scroll, drag-and-drop).

Use browser tests for:

- Full user flows (authentication, form submission, navigation)
- Visual correctness and dark mode
- JavaScript behaviour and console error detection

---

## Code Quality Before Committing

Run these checks before marking any work as done:

```bash
# Fix formatting
vendor/bin/pint --dirty

# Static analysis
vendor/bin/phpstan analyse

# Relevant tests
php artisan test --filter=YourChangedArea
```
