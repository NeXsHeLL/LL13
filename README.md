# LL13: Learn Laravel 13

LL13 is a Laravel 13 learning app for building real framework fluency through guided learning paths, multiple-choice checkpoints, hands-on tasks, study logs, and progress tracking.

The project is designed to be useful as both a learning tool and a readable Laravel codebase. It uses Laravel, Inertia, React, TypeScript, Tailwind CSS, SQLite by default, and PHPUnit feature tests.

## Features

- Guided quest catalog from beginner PHP to advanced Laravel topics.
- Multiple-choice checkpoints with server-side grading.
- Hands-on tasks that encourage building and explaining real Laravel features.
- Custom learning paths with target dates, confidence tracking, and weekly study minutes.
- Learning logs for recording study sessions and reflections.
- Progress cards for active paths, logged hours, completed checkpoints, weekly plan, and confidence.
- Authentication, authorization policies, migrations, factories, seed data, and feature tests.

## Learning Tracks

The built-in quest catalog includes:

- PHP Foundations
- Laravel Foundations
- Laravel 13 Quest: Basics to Advanced
- Modern Laravel Deep Dive

The track structure is inspired by public topic and category organization from Laracasts Discover, but the activities and quiz content in this repository are original.

## Tech Stack

- Laravel 13
- PHP 8.4 recommended
- Inertia.js 2
- React 19
- TypeScript
- Tailwind CSS 4
- SQLite for local development
- PHPUnit 12

## Requirements

- PHP 8.4 or newer
- Composer
- Node.js and npm
- SQLite

## Installation

Clone the repository and install dependencies:

```bash
composer install
npm install
```

Create the environment file and application key:

```bash
cp .env.example .env
php artisan key:generate
```

Create the SQLite database and run migrations:

```bash
touch database/database.sqlite
php artisan migrate
```

Start the local development stack:

```bash
composer run dev
```

The app will be available at the Laravel development server URL printed in the terminal, typically `http://127.0.0.1:8000`.

## Seed Data

To create a demo user and sample learning quest:

```bash
php artisan migrate:fresh --seed
```

Default seeded user:

```text
Email: test@example.com
Password: password
```

## Quality Checks

Run the backend test suite:

```bash
php artisan test
```

Format PHP:

```bash
php vendor/bin/pint
```

Format and lint frontend assets:

```bash
npm run format
npm run lint
```

Build production assets:

```bash
npm run build
```

## Repository Hygiene

This repository intentionally excludes local environment files, installed dependencies, generated build assets, IDE folders, and local assistant/tooling metadata. Do not commit `.env`, `vendor`, `node_modules`, `public/build`, or agent-specific configuration files.

## License

LL13 is open-sourced software licensed under the MIT license.
