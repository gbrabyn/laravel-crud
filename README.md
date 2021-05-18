# Laravel Full Stack CRUD Coding Exercise

A Laravel application to manage organisation's users.

## Application Behaviour

1. `EMPLOYEE` users see the Home page after logging in.
1. `EMPLOYEE` users can see a Users data grid but have no editing rights.
1. `ADMIN` users see a Users data grid after logging in, including `ADMIN` users.
1. `ADMIN` users can create, edit, and delete users.
1. `ADMIN` users can create `ADMIN` users.
1. `ADMIN` users cannot update or delete other `ADMIN` users.
1. An `ADMIN` user cannot delete themself.
1. An Organisations CRUD for `ADMIN` users.
1. Users search form
1. Seeders to create organisations and users, including admin users.
1. Tests for the users CRUD.

## Stack

1. Database: MySQL
1. Framework: Laravel
1. Docker

## Setup the project

This project uses `Docker` containerisation so you can quickly run it in your local development environment.

To set up the project follow these steps:

1. Set up the `.env` file:

```bash
cp .env.example .env
```

2. Start up the services

```bash
docker-compose up -d
```

3. Install the dependencies

```bash
docker-compose exec app composer install

npm install
```

4. Build the frontend resources

```bash
npm run dev
```

5. Create a key

```bash
docker-compose exec app php artisan key:generate
```

6. Run migrations

```bash
docker-compose exec app php artisan migrate --seed
```

7. View website

View the website at http://localhost:8000/. Log-in as Admin using: "admin@example.com" and "password".
