# Development Guide

This guide covers the normal local workflow for LL13.

## Runtime

Recommended versions:

- PHP 8.4
- Composer 2
- Node.js 22
- npm
- SQLite

The Composer constraint allows PHP 8.3 or newer, but CI and local development are intended to run on PHP 8.4.

## First Run

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
composer run dev
```

## Useful Commands

Run tests:

```bash
php artisan test --compact
```

Run PHP formatting:

```bash
php vendor/bin/pint
```

Check frontend formatting:

```bash
npm run format:check
```

Apply frontend formatting:

```bash
npm run format
```

Run frontend linting:

```bash
npm run lint
```

Build assets:

```bash
npm run build
```

## Application Shape

Important areas:

| Path | Purpose |
| --- | --- |
| `app/LearningQuestFactory.php` | Curated quest catalog, MCQs, tasks, and study plans |
| `app/Http/Controllers` | Dashboard, learning path, checkpoint, and log actions |
| `app/Models` | Learning path, checkpoint, and log models |
| `resources/js/pages/dashboard.tsx` | Main learning dashboard UI |
| `tests/Feature/LearningPathTest.php` | Feature coverage for catalog and learning workflows |

## Data Storage

SQLite is the default local database. The generated database file belongs at:

```text
database/database.sqlite
```

Do not commit local database files.

## Public Repo Checklist

Before opening a pull request or pushing public-facing updates:

1. Run `php artisan test --compact`.
2. Run `php vendor/bin/pint`.
3. Run `npm run format:check`.
4. Run `npm run lint`.
5. Run `npm run build`.
6. Confirm no local secrets, databases, build assets, or tooling metadata are staged.
