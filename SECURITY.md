# Security Policy

LL13 is a learning project, but security reports are still welcome.

## Supported Versions

The public `main` branch is the supported development line.

## Reporting A Vulnerability

Please do not open a public issue for a suspected security vulnerability.

Instead, report it privately through GitHub security advisories for this repository. Include:

- A short description of the issue.
- Steps to reproduce.
- Affected routes or files when known.
- Expected impact.
- Any safe proof of concept details.

## Scope

Useful reports include:

- Authentication or authorization bypasses.
- Exposure of answer keys through the dashboard payload.
- Cross-site scripting.
- Unsafe file or environment handling.
- Dependency vulnerabilities with a practical impact.

Out of scope:

- Denial-of-service claims without a practical reproduction.
- Reports that require access to local `.env` files or developer machines.
- Social engineering.

## Security Expectations

LL13 should not expose local secrets, SQLite databases, generated assets, or assistant/tooling metadata in commits. The app should keep MCQ correct answers on the server and only expose explanations in learner-facing contexts.
