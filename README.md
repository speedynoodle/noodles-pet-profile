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
- 🦮 **Sitter Information** – public page with walk schedule, per-pet feeding schedules, emergency contacts and vet info
- 🔒 **Admin panel** – password-protected login to manage health notes and sitter information per pet
- 🩺 **Health notes** – injection, physio, fleaing, vet visit, medication records (admin-only)
- 📱 **Responsive design** – works on mobile and desktop

## Local Development

### Prerequisites

- **PHP 8.0+** with the `pdo_mysql` extension enabled
  - macOS: [Laravel Herd](https://herd.laravel.com/) (recommended), Homebrew PHP, or MAMP
  - Windows: [Laragon](https://laragon.org/) or XAMPP
  - Linux: `sudo apt install php php-mysql` (or distro equivalent)
- **MySQL 8.0** (usually bundled with the tools above, or install via Homebrew / `apt`)

### 1 – Clone the repository

```bash
git clone https://github.com/speedynoodle/noodles-pet-profile.git
cd noodles-pet-profile
```

### 2 – Create the local database

Log in to MySQL and run the init script to create the schema and seed Jack-Jack & Nagi's data:

```bash
mysql -u root -p < sql/init.sql
```

Then run the migrations to add the admin/health-notes tables and the sitter-information tables:

```bash
mysql -u root -p < sql/migrations/add_admin_and_health_notes.sql
mysql -u root -p < sql/migrations/add_sitter_info.sql
```

Or open both files in phpMyAdmin / MySQL Workbench and execute them in order.

### 3 – Configure the database connection

Copy the example config and adjust credentials for your local MySQL:

```bash
cp src/config/database.example.php src/config/database.php
```

Edit `src/config/database.php` if your local MySQL uses a different username or password (the defaults in the example file work for most out-of-the-box installations).

### 4 – Create the admin user

```bash
php scripts/create_admin.php
```

This prompts for a username and password and stores a bcrypt hash in the database.

### 5 – Start the built-in PHP web server

```bash
php -S localhost:8080 -t src router.php
```

`router.php` (project root) mimics the Apache `.htaccess` rewrite rules so that all URLs resolve correctly without needing Apache.

### 6 – Open the app

Navigate to **<http://localhost:8080>** in your browser.  
Admin panel: **<http://localhost:8080/admin/login.php>**

---

## Deployment – IONOS Web Hosting Plus

### 1 – Set PHP version to 8.0 or 8.1

This app uses PHP 8.0 features (union return types etc.).

1. Log in to your [IONOS Control Panel](https://my.ionos.com/).
2. Navigate to **Hosting** → **your domain** → **PHP version**.
3. Select **PHP 8.0** or **PHP 8.1** and save.

### 2 – Create a MySQL database

1. In the IONOS Control Panel, navigate to **Hosting** → **Databases** → **Create Database**.
2. Note down the **database name** (e.g. `dbs12345678`), **username**, **password**, and **host** (check the control panel – usually `localhost` on current accounts).

### 3 – Import the database schema & seed data

> ⚠️ **IONOS phpMyAdmin import note**: The `sql/init.sql` file contains `CREATE DATABASE` and `USE` lines that are needed for local dev but will **fail** on shared hosting. Before importing, either:
> - Delete or comment out those two lines, **or**
> - Use the steps below which skip those lines automatically.

**Option A – phpMyAdmin (easiest)**

1. In the IONOS Control Panel, open **phpMyAdmin** for your database.
2. **Select your database** in the left panel (click its name).
3. Click the **SQL** tab and run the schema without the `CREATE DATABASE`/`USE` lines:
   - Open `sql/init.sql`, copy everything **after** the `USE` line, paste into the SQL box, and execute.
4. Repeat for `sql/migrations/add_admin_and_health_notes.sql` – this file has no `USE` line, so you can import it directly via the **Import** tab.
5. Repeat for `sql/migrations/add_sitter_info.sql` – same process.

**Option B – MySQL CLI via SSH**

```bash
# Replace dbs12345678, dbuser, dbpass with your IONOS values
mysql -h localhost -u dbuser -pdbpass dbs12345678 < sql/init_tables_only.sql
mysql -h localhost -u dbuser -pdbpass dbs12345678 < sql/migrations/add_admin_and_health_notes.sql
mysql -h localhost -u dbuser -pdbpass dbs12345678 < sql/migrations/add_sitter_info.sql
```

> The `CREATE DATABASE` and `USE` lines in `init.sql` are harmless with CLI when you pass the database name as an argument – MySQL ignores the `USE` and the `CREATE DATABASE IF NOT EXISTS` will simply fail silently on permission-denied, which is fine since the database already exists.

### 4 – Configure the database connection

Edit `src/config/database.php` (copy from `database.example.php`) and replace with your IONOS database credentials:

```php
define('DB_HOST',     'localhost');           // Check IONOS control panel
define('DB_PORT',     '3306');
define('DB_NAME',     'dbs12345678');         // Your IONOS-assigned database name
define('DB_USER',     'your_database_user');  // IONOS DB username
define('DB_PASSWORD', 'your_db_password');    // IONOS DB password
```

> ⚠️ **Keep `src/config/database.php` private** – never commit it to a public repository once it contains real credentials.

### 5 – Upload files via FTP / File Manager

Upload the **contents of the `src/` folder** to the **document root** of your IONOS hosting (typically `httpdocs/` or `public_html/`).

Your file structure on the server should look like:

```
httpdocs/
├── index.php
├── .htaccess
├── config/
│   ├── database.php
│   └── session.php
├── includes/
│   ├── auth_middleware.php
│   ├── pet_model.php
│   ├── header.php
│   └── footer.php
├── pages/
│   └── pet.php
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── health_notes.php
│   ├── health_note_save.php
│   └── health_note_delete.php
└── assets/
    ├── css/style.css
    └── js/app.js
```

> The `sql/` folder and `scripts/` do **not** need to be uploaded to `httpdocs/`.

### 6 – Create the admin user

**Option A – phpMyAdmin (easiest)**

1. Generate a bcrypt hash. Run this one-liner anywhere you have PHP:
   ```bash
   php -r "echo password_hash('your_chosen_password', PASSWORD_BCRYPT) . PHP_EOL;"
   ```
2. In phpMyAdmin, open the **SQL** tab for your database and run:
   ```sql
   INSERT INTO admin_users (username, password_hash)
   VALUES ('admin', '$2y$10$pasteYourHashHere');
   ```

**Option B – SSH (IONOS Web Hosting Plus includes SSH)**

1. Upload `scripts/create_admin.php` to your home directory (outside `httpdocs/`) via FTP/SFTP.
2. Connect via SSH and run:
   ```bash
   php ~/create_admin.php --db-config=~/httpdocs/config/database.php
   ```

### 7 – Open the app

Navigate to your domain (e.g. `https://yourdomain.com`) – the pet profiles will be live.  
Admin panel: `https://yourdomain.com/admin/login.php`

---

## phpMyAdmin

IONOS provides a built-in phpMyAdmin interface accessible via the **IONOS Control Panel** → **Databases** → **Manage** → **phpMyAdmin**.

Use it to:
- View and edit pet records directly
- Import SQL files to set up or reset the schema
- Run custom queries

---

## Project Structure

```
.
├── router.php                # Local dev only – PHP built-in server URL router
├── scripts/
│   └── create_admin.php      # CLI: create the first admin user (local dev or IONOS SSH)
├── sql/
│   ├── init.sql              # Base schema + seed data (Jack-Jack & Nagi)
│   └── migrations/
│       └── add_admin_and_health_notes.sql  # Admin users + health notes tables
└── src/                      # Upload the contents of this folder to your IONOS document root
    ├── index.php             # Homepage – pet card gallery
    ├── .htaccess             # Apache rewrite rules (used on IONOS; router.php handles this locally)
    ├── config/
    │   ├── database.example.php  # ✅ Committed template – copy to database.php and fill in credentials
    │   ├── database.php          # ⚠️ Your credentials – gitignored, never commit
    │   └── session.php           # Session start + auth helpers (isAdminLoggedIn, CSRF)
    ├── includes/
    │   ├── auth_middleware.php   # Redirect guard for admin pages
    │   ├── pet_model.php         # Data-access functions (pets, vaccinations, health notes, sitter info)
    │   ├── header.php            # Shared HTML header (shows admin bar when logged in)
    │   └── footer.php            # Shared HTML footer
    ├── pages/
    │   ├── pet.php               # Individual pet profile page
    │   └── sitter.php            # Public sitter information page
    ├── admin/
    │   ├── index.php             # Admin dashboard – lists all pets
    │   ├── login.php             # Admin login form
    │   ├── logout.php            # Destroys session, redirects to login
    │   ├── health_notes.php      # List + add/edit health notes for a pet
    │   ├── health_note_save.php  # POST: create or update a health note
    │   ├── health_note_delete.php      # POST: delete a health note
    │   ├── sitter_info.php             # Manage household info, walk & feeding schedules
    │   ├── sitter_info_save.php        # POST: save household sitter info
    │   ├── walk_schedule_save.php      # POST: create or update a walk schedule entry
    │   ├── walk_schedule_delete.php    # POST: delete a walk schedule entry
    │   ├── feeding_schedule_save.php   # POST: create or update a feeding schedule entry
    │   └── feeding_schedule_delete.php # POST: delete a feeding schedule entry
    └── assets/
        ├── css/style.css         # Main stylesheet (includes admin and sitter styles)
        └── js/app.js             # Minimal JavaScript
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

admin_users                        ← added by add_admin_and_health_notes migration
 └── id, username, password_hash, created_at

health_notes                       ← added by add_admin_and_health_notes migration
 └── pet_id → pets.id
     note_date, weight_kg (nullable)
     type: injection | physio | fleaing | vet_visit | medication | other
     notes, created_at, updated_at

sitter_household_info              ← added by add_sitter_info migration
 └── id (always 1), emergency_contact_name, emergency_contact_phone
     vet_name, vet_phone, vet_address, general_notes

walk_schedules                     ← added by add_sitter_info migration
 └── id, label, walk_time, duration_minutes, notes, sort_order

feeding_schedules                  ← added by add_sitter_info migration
 └── pet_id → pets.id
     meal_label, feed_time, food_description, notes, sort_order
```