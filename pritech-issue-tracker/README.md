# Mini Issue Tracker

A Laravel 13 issue tracking application built as a technical task for PRITECH.

## Stack

- Laravel 13, PHP 8.5
- SQLite
- Blade + Bootstrap 5.3
- AJAX (fetch API)

## Features

- **Projects** — create, view, edit, delete with optional start date and deadline
- **Issues** — full CRUD with status (open / in_progress / closed), priority (low / medium / high), due date, and project assignment
- **Tags** — create tags with a colour; attach/detach to issues inline without page reload
- **Comments** — add comments via AJAX; paginated (5 per page) with a Load More button
- **Filters** — filter issues by status, priority, and tag
- **Search (B3)** — debounced live search (300 ms) on issue title and description
- **User assignment (B1)** — assign/unassign users to issues via AJAX toggle buttons
- **Authorization (B2)** — `ProjectPolicy` restricts edit/delete to the project owner

## Setup

```bash
git clone <repo>
cd pritech-issue-tracker
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Visit `http://127.0.0.1:8000`

## Seeded users

| Name  | Email             | Password |
|-------|-------------------|----------|
| Alice | alice@example.com | password |
| Bob   | bob@example.com   | password |

Alice owns projects 1–4; Bob owns projects 5–8. Log in as each to verify the authorization policy.
