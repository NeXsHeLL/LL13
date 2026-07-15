# Contributing

Thanks for considering a contribution to LL13.

This project is both a learning app and a learning codebase. Contributions should keep the app useful, readable, and easy to run locally.

## Good Contributions

- Improve learning content clarity.
- Add practical Laravel MCQs with explanations.
- Add hands-on tasks that teach real framework behavior.
- Improve tests around learning workflows.
- Improve accessibility, responsiveness, and dashboard usability.
- Fix setup, documentation, or CI issues.

## Content Guidelines

- Teach before testing.
- Avoid trivia questions.
- Include an explanation for every MCQ.
- Keep answer keys server-side.
- Prefer small practical tasks over vague prompts.
- Use original wording for lessons, questions, and explanations.

## Development Workflow

1. Fork the repository.
2. Create a focused branch.
3. Make the smallest useful change.
4. Run the quality checks.
5. Open a pull request with a clear summary.

Quality checks:

```bash
php artisan test --compact
php vendor/bin/pint
npm run format:check
npm run lint
npm run build
```

## Pull Request Notes

Please include:

- What changed.
- Why it improves the project.
- What commands you ran.
- Screenshots for UI changes when useful.

## Repository Hygiene

Do not commit:

- `.env` files
- Local SQLite databases
- `vendor`
- `node_modules`
- `public/build`
- IDE folders
- Local assistant or agent configuration files
