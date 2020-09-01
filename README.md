# Laravel Full Stack CRUD Coding Exercise

## Features

A small laravel application to manage organisation's users:

1. `EMPLOYEE` users see the Home page after logging in. 
1. `EMPLOYEE` users can see a Users data grid but have no editing rights.
1. `ADMIN` users can register a new user and create a new `Organisation`.
1. `ADMIN` users see a Users data grid after logging in, including `ADMIN` users.
1. `ADMIN` users can create, edit, and delete users.
1. Seeders to create organisations and users, including admin users.
1. An Organisations CRUD for `ADMIN` users. 
1. Tests for the users CRUD.
1. Users search form
1. A policy to prevent `ADMIN` users from updating and deleting other `ADMIN` users.
1. A policy to prevent `ADMIN` users from deleting themself.


## Stack

1. Database: MySQL
1. Framework: Laravel
1. Docker


## Setup the project

This project has a `docker-compose.yml` contains the basic stack setup to quickly spin up the local development environment.

To set up the project please follow the steps below:
 
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
docker-compose exec php composer install

npm install
``` 

4. Build the frontend resources

```bash
npm run dev
```

5. Create a key

```bash
docker-compose exec php php artisan key:generate
```

6. Run migrations

```bash
docker-compose exec php php artisan migrate --seed
```

7. View website

View the website at http://localhost:8000/. Log-in as Admin using: "admin@example.com" and "password".