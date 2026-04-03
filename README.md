# 🐾 Noodle's Pet Profiles

A PHP web application for managing pet profiles, built for Jack-Jack and Nagi – two beautiful Shiba Inus.

## Tech Stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Web server | Apache 2 + PHP 8.x                |
| Framework  | Vanilla PHP (MVC-style structure) |
| Database   | MySQL 8.0                         |
| DB Admin   | phpMyAdmin (via IONOS Control Panel) |
| Hosting    | IONOS Web Hosting Plus            |

## Features

- 🏠 **Homepage** – gallery-style cards for every pet
- 🐕 **Individual pet profiles** – full details including breed, age, weight, colour, favourite toy & food
- 💉 **Vaccination history** – per-pet vaccine records
- 🏥 **Medical records** – visit history with vet notes
- 📱 **Responsive design** – works on mobile and desktop

## Deployment – IONOS Web Hosting Plus

### 1 – Create a MySQL database

1. Log in to your [IONOS Control Panel](https://my.ionos.com/).
2. Navigate to **Hosting** → **Databases** → **Create Database**.
3. Note down the **database name**, **username**, **password**, and **host** (usually `localhost`).

### 2 – Import the database schema & seed data

1. In the IONOS Control Panel, open **phpMyAdmin** for your database.
2. Select your database, click the **Import** tab.
3. Upload and run `sql/init.sql` – this creates all tables and inserts Jack-Jack & Nagi's profiles.

### 3 – Configure the database connection

Edit `src/config/database.php` and replace the placeholder values with your IONOS database credentials:

```php
define('DB_HOST',     'localhost');           // Usually 'localhost' on IONOS
define('DB_PORT',     '3306');
define('DB_NAME',     'your_database_name');  // e.g. dbs12345678
define('DB_USER',     'your_database_user');  // IONOS DB username
define('DB_PASSWORD', 'your_db_password');    // IONOS DB password
```

> ⚠️ **Keep `src/config/database.php` private** – never commit it to a public repository once it contains real credentials.

### 4 – Upload files via FTP / File Manager

Upload the **contents of the `src/` folder** to the **document root** of your IONOS hosting (typically `httpdocs/` or `public_html/`).

Your file structure on the server should look like:

```
httpdocs/
├── index.php
├── .htaccess
├── config/
│   └── database.php
├── includes/
│   ├── pet_model.php
│   ├── header.php
│   └── footer.php
├── pages/
│   └── pet.php
└── assets/
    ├── css/style.css
    └── js/app.js
```

> The `sql/` folder does **not** need to be uploaded to the server.

### 5 – Open the app

Navigate to your domain (e.g. `https://yourdomain.com`) – the pet profiles will be live.

## phpMyAdmin

IONOS provides a built-in phpMyAdmin interface accessible via the **IONOS Control Panel** → **Databases** → **Manage** → **phpMyAdmin**.

Use it to:
- View and edit pet records directly
- Re-import `sql/init.sql` if you need to reset the data
- Run custom queries

## Project Structure

```
.
├── sql/
│   └── init.sql              # Schema + seed data (Jack-Jack & Nagi) – import via phpMyAdmin
└── src/                      # Upload the contents of this folder to your IONOS document root
    ├── index.php             # Homepage – pet card gallery
    ├── .htaccess             # Apache rewrite rules
    ├── config/
    │   └── database.php      # ⚠️ Fill in your IONOS DB credentials here
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