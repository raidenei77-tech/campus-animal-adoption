# Campus Animal Adoption Center — Final Project

A complete PHP + MySQL/MariaDB campus animal rescue and adoption management system. It keeps the strawberry-cat visual theme and implements the entities from the supplied schema in a normalized, working database.

## Main features

- Account registration and login with secure password hashing.
- General User and Volunteer roles.
- Animal registration, photo upload, search/filter, profiles and status history.
- Rescue reporting, volunteer case assignment, case closing and case counters.
- Adoption requests, volunteer approval/rejection and automatic adopted status.
- Duplicate/adopted-animal adoption protection.
- Feeding schedules with consumption, behavior and observation notes.
- Veterinarian directory and treatment history.
- Money/item donations and donation status management.
- Volunteer-only expense recording with optional donation-to-expense linkage.
- Editable user profile.
- CSRF protection for forms, prepared SQL statements, session regeneration, output escaping, and safer image upload validation.

## Mac XAMPP setup

1. Rename this folder to `campus-animal-adoption`.
2. Put it at `/Applications/XAMPP/xamppfiles/htdocs/campus-animal-adoption/`.
3. In XAMPP Manager, start **Apache Web Server** and **MySQL Database**.
4. Open `http://localhost/phpmyadmin`.
5. If you do not need your old development data, remove the old `campus_animal_adoption` database first. The supplied SQL intentionally rebuilds it cleanly.
6. Import `database.sql` from this folder.
7. Open `http://localhost/campus-animal-adoption/`.
8. Create a General User account and a Volunteer account to test both permission levels.

## Important

Use the `database.sql` included with this project. The original SQL dump had inconsistent table/column names, incorrect data types, missing keys, and several multi-column status designs that did not match the PHP code.

## Suggested demonstration flow

1. Register a General User and a Volunteer.
2. Register an animal.
3. Submit a rescue report for that animal.
4. Log in as the Volunteer, take the case, and update the animal to `rescued` / `available`.
5. Add a vet and treatment if needed.
6. Add a feeding record.
7. Log in as the General User and request adoption.
8. Log in as the Volunteer and approve the request.
9. Record a donation and an expense linked to the donation.

## Folder structure

- `index.php`, `signup.php`, `login.php`, `register.php`, `logout.php` — authentication
- `dashboard.php` — overview
- `animals.php`, `animal.php` — animal records
- `reports.php` — rescue cases
- `adoptions.php` — adoption workflow
- `feeding.php` — feeding records
- `treatments.php` — vets and medical records
- `donations.php` — donation records
- `expenses.php` — expense/fund usage records
- `profile.php` — user profile
- `auth.php`, `db.php`, `layout.php` — shared helpers
- `assets/` — CSS and images
- `uploads/` — uploaded animal images
- `database.sql` — corrected database schema
