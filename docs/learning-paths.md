# Learning Paths

LL13 is organized around study-first learning paths. A learner should understand the concept before treating checkpoints like an exam.

## Learning Loop

Each guided quest follows this loop:

1. Read the study guide for the current phase.
2. Complete MCQs to check conceptual understanding.
3. Complete hands-on tasks to build the concept in code.
4. Review explanations and write reflections in study logs.
5. Track confidence and progress over time.

## Phase Model

Every built-in quest uses three phases.

| Phase | Purpose |
| --- | --- |
| Basics | Build vocabulary and understand the core files, commands, and request flow |
| Intermediate | Connect concepts across routes, UI, validation, persistence, and feedback |
| Advanced | Prove behavior with tests, authorization, failure handling, and release checks |

## Built-In Quest Catalog

The current first batch includes 104 total activities.

| Quest | Activities | MCQs | Tasks |
| --- | ---: | ---: | ---: |
| PHP Foundations Quest | 26 | 16 | 10 |
| Laravel Foundations Quest | 26 | 16 | 10 |
| Laravel 13 Quest: Basics to Advanced | 26 | 16 | 10 |
| Modern Laravel Deep Dive Quest | 26 | 16 | 10 |

## Content Principles

- Teach before testing.
- Prefer practical Laravel vocabulary over trivia.
- Pair recall questions with implementation tasks.
- Show explanations after a learner completes an MCQ.
- Keep answer keys server-side.
- Ask learners to explain what changed and why.

## Extending The Catalog

The quest catalog lives in `app/LearningQuestFactory.php`.

When adding a new quest:

1. Add a unique quest ID and metadata.
2. Add three study phases: basics, intermediate, advanced.
3. Add MCQs with four options, one correct option, and an explanation.
4. Add hands-on tasks with practical completion criteria.
5. Keep the total mix balanced between conceptual questions and implementation tasks.
6. Add or update feature tests for the catalog shape and dashboard payload.

## Learner Safety

LL13 is a learning app, not a certification authority. Scores and completion states should be treated as personal progress signals, not formal credentials.
