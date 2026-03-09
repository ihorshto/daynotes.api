# Architecture: Laravel Actions

## Overview

This application uses the
[lorisleiva/laravel-actions](https://laravelactions.com/) package to implement
the **Action pattern**. Actions are single-responsibility classes that
encapsulate one piece of business logic. They replace the traditional fat
Controller → Service layer with a flatter, more explicit structure.

---

## Why Actions?

| Problem                                    | Action Pattern Solution                           |
| ------------------------------------------ | ------------------------------------------------- |
| Fat controllers with mixed concerns        | Each action handles exactly one operation         |
| Reusable logic scattered in services       | Actions are plain classes, callable from anywhere |
| Hard to test controller logic in isolation | Actions have no HTTP context — easy to unit test  |
| Unclear entry points to business logic     | Action class names are self-documenting           |

---

## Action Structure

Every action uses the `AsAction` trait and implements a `handle()` method. The
method accepts typed parameters and returns a typed result — no arrays, no
magic.

```php
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMoodEntryAction
{
    use AsAction;

    public function handle(User $user, int $moodScore, ?string $note = null): MoodEntry
    {
        // business logic here
    }
}
```

Actions are called using the static `run()` helper or injected via the service
container. Controllers receive the action as a constructor or method dependency
and call `handle()` directly.

---

## Action Groups

Actions are organised by domain under `app/Actions/`:

- **MoodEntry** — creating, updating, deleting, filtering entries, and
  calculating statistics
- **Telegram** — webhook routing, command dispatching, callback handling,
  messaging
- **UserNotificationSetting** — managing per-user notification time slots

---

## Telegram Integration Architecture

The Telegram integration follows a layered routing approach triggered by a
single public webhook endpoint:

```
Incoming webhook request
    └── WebhookAction
            ├── CommandRouter    → dispatches to Command classes  (/add, /stats, etc.)
            └── CallbackRouter   → dispatches to CallbackHandler classes (inline keyboard responses)
```

Each command and callback handler is a standalone action class. Adding a new bot
command means creating a new class — no router configuration needed.

---

## State Machine (Telegram Conversation Flow)

Multi-step Telegram conversations use a simple state machine. User state is
persisted in the database via a dedicated model.

```
Idle
  │
  └─ /add ──────────────► WaitingForMood
                                │
                  mood selected │
                                ▼
                          WaitingForNote
                                │
                   note sent or │
                   skipped      │
                                ▼
                             Idle
                    (entry saved)
```

State transitions are managed by a dedicated service (`StateManagerService`)
called from within the relevant command and callback handler actions. This keeps
state logic centralised and out of the actions themselves.

---

## Services

Services handle infrastructure-level concerns and shared logic that does not
belong inside a single action: Services are injected into actions via
constructor dependency injection.

---

## HTTP Layer

Controllers are intentionally thin. Their only responsibilities are:

1. Delegate request validation to a **Form Request** class
2. Authorize the operation via a **Policy**
3. Call the relevant action
4. Return an **API Resource** response

No business logic lives in controllers. No validation lives in actions. Each
layer has one job.

---

## Models & Relationships

The domain is simple and relationship-driven:

- A **User** has many **MoodEntries**, many **UserNotificationSettings**, and
  one **UserState**
- All domain entities are scoped to a user — queries always start from the
  authenticated user's relationships

Enums are used for all fixed-value fields (mood score, user state, statistics
period). This makes invalid states impossible to represent in the database.

---

## Key Conventions

- Actions are named in imperative form: `CreateMoodEntryAction`,
  `SendTelegramMessage`, `GenerateLinkCodeAction`
- One action = one responsibility. If an action grows large, extract a
  sub-action or a service method
- Actions do not know about HTTP — they accept typed domain objects and return
  domain objects
- All output is transformed through API Resources before leaving the HTTP layer
