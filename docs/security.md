# Security

## Authentication & Authorisation

This application uses **Laravel Sanctum** for API token authentication. Every
protected route requires a valid bearer token in the `Authorization` header.

Authorisation is enforced via **Laravel Policies**. Every action that operates
on a user-owned resource must go through a policy check before performing the
operation. This prevents users from reading, modifying, or deleting data that
belongs to others.

Key principles:

- Never trust user-supplied IDs. Always resolve ownership through the
  authenticated user's relationships.
- Policies are the single source of truth for access rules — do not duplicate
  checks in controllers or actions.
- Return `403 Forbidden` for authorisation failures, `401 Unauthorized` for
  missing or invalid tokens.

---

## Telegram Webhook Security

The Telegram webhook endpoint is publicly accessible. It must be protected
against:

### Stale message replay

Messages older than `TELEGRAM_STALE_MESSAGE_THRESHOLD` seconds are silently
dropped. This prevents replayed or delayed messages from triggering actions.

### Bot token secrecy

The bot token must never be exposed in logs, error messages, or API responses.
Keep it in the `.env` file and access it only via `config()`.

### Webhook URL secrecy

Using a non-guessable webhook path (e.g. containing the bot token or a random
secret) adds a layer of obscurity. Telegram itself recommends this approach.

---

## Configuration Security

- Environment-specific secrets must always be stored in `.env` and never
  committed to version control. `.env` is in `.gitignore`.
- Access config values via `config()`, never via `env()` outside of config
  files.
- In production, set `APP_DEBUG=false` and `APP_ENV=production`. Debug mode
  exposes stack traces and internal application details.
- Rotate `APP_KEY` and any API tokens immediately if they are ever exposed.

---

## Database Security

- Use Eloquent and the query builder exclusively — never concatenate raw user
  input into SQL strings.
- Scope all queries to the authenticated user. Do not fetch records and then
  check ownership after the fact.
- Use transactions for operations that span multiple writes to keep the database
  consistent.

---

## API Response Security

- Use **API Resources** to control exactly which fields are returned. Never
  return raw model data directly.
- Do not expose internal IDs, foreign keys, or implementation details in
  responses unless required by the client.
- Return meaningful but non-revealing error messages. Avoid leaking information
  about system internals in error bodies.

---

## Queue & Background Jobs

- Validate all job payloads when jobs are constructed, not only when they are
  processed — the queue is not a trusted boundary.
- Use `ShouldBeUnique` when duplicate jobs could cause double-processing (e.g.
  sending duplicate notifications).
- Failed jobs should be logged and reviewed. Do not silently discard failures.

---

## Rate Limiting

Apply rate limiting to authentication endpoints (`/register`, `/login`) and the
Telegram webhook to prevent brute force and abuse. Laravel's built-in `throttle`
middleware is sufficient for most cases.

---

## Dependency Management

- Keep dependencies up to date. Run `composer outdated` and `npm outdated`
  regularly.
- Review changelogs before upgrading. Pay attention to security advisories.
- Do not add new packages without evaluating their maintenance status and
  security history.

---

## Logging & Monitoring

- Do not log sensitive data (tokens, passwords, personal information).
- Use structured logging to make log analysis easier.
- Monitor for repeated authentication failures, unexpected spikes in webhook
  traffic, and job queue failures.
