# 🐾 Noodle's Pet Profiles

A PHP web application for managing pet profiles, built for Jack-Jack and Nagi – two beautiful Shiba Inus.

## Tech Stack

| Layer      | Technology                         |
|------------|------------------------------------|
| Web server | Apache 2 + PHP 8.2                 |
| Framework  | Vanilla PHP (MVC-style structure)  |
| Database   | MySQL 8.0                          |
| DB Admin   | phpMyAdmin                         |
| Dev env    | Docker / Docker Compose            |

## Features

- 🏠 **Homepage** – gallery-style cards for every pet
- 🐕 **Individual pet profiles** – full details including breed, age, weight, colour, favourite toy & food
- 💉 **Vaccination history** – per-pet vaccine records
- 🏥 **Medical records** – visit history with vet notes
- 📱 **Responsive design** – works on mobile and desktop

## Quick Start

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (includes Docker Compose)

### 1 – Clone & configure

```bash
git clone https://github.com/speedynoodle/noodles-pet-profile.git
cd noodles-pet-profile

# Copy the example environment file and edit if needed
cp .env.example .env
```

### 2 – Start all services

```bash
docker compose up -d --build
```

Docker Compose will start three containers:

| Container             | URL                                  | Description              |
|-----------------------|--------------------------------------|--------------------------|
| `pet-profile-app`     | <http://localhost:8080>              | PHP/Apache web app       |
| `pet-profile-db`      | `localhost:3306`                     | MySQL 8.0 database       |
| `pet-profile-phpmyadmin` | <http://localhost:8081>           | phpMyAdmin DB admin UI   |

The database is automatically seeded with Jack-Jack and Nagi's profiles on first run.

### 3 – Open the app

Navigate to **<http://localhost:8080>** in your browser.

### 4 – Stop the services

```bash
docker compose down
```

To also remove the database volume (all data):

```bash
docker compose down -v
```

## phpMyAdmin

Open **<http://localhost:8081>** – the admin UI connects automatically with root credentials.

Default credentials (configurable in `.env`):

| Field    | Default value  |
|----------|----------------|
| Host     | `db`           |
| Username | `root`         |
| Password | `rootpassword` |

## Project Structure

```
.
├── docker-compose.yml        # Service definitions
├── Dockerfile                # PHP/Apache image
├── .env.example              # Example environment variables
├── sql/
│   └── init.sql              # Schema + seed data (Jack-Jack & Nagi)
└── src/                      # PHP application root (served by Apache)
    ├── index.php             # Homepage – pet card gallery
    ├── .htaccess             # Apache rewrite rules
    ├── config/
    │   └── database.php      # PDO connection helper
    ├── includes/
    │   ├── pet_model.php     # Data-access functions
    │   ├── header.php        # Shared HTML header
    │   └── footer.php        # Shared HTML footer
    ├── pages/
    │   └── pet.php           # Individual pet profile page
    └── assets/
        ├── css/style.css     # Main stylesheet
        └── js/app.js         # Minimal JavaScript
```

## Environment Variables

| Variable              | Default        | Description                  |
|-----------------------|----------------|------------------------------|
| `DB_HOST`             | `db`           | MySQL host                   |
| `DB_PORT`             | `3306`         | MySQL port                   |
| `DB_NAME`             | `pet_profiles` | Database name                |
| `DB_USER`             | `pet_user`     | Application DB user          |
| `DB_PASSWORD`         | `pet_password` | Application DB password      |
| `MYSQL_ROOT_PASSWORD` | `rootpassword` | MySQL root password          |

## Database Schema

```
pets
 ├── id, name, species, breed, gender
 ├── birthday, weight_kg, color
 ├── description, personality
 ├── favourite_toy, favourite_food
 └── photo_url, created_at, updated_at

vaccinations
 └── pet_id → pets.id
     vaccine_name, date_given, next_due_date, vet_name, notes

medical_records
 └── pet_id → pets.id
     record_date, record_type, description, vet_name, notes
```