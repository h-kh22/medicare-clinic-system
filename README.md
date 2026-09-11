# MediCare Clinic Management System

## 1. Project Overview

MediCare is a full-stack clinic management system. It connects a Laravel REST API and MySQL database to a React clinic portal.

The system has three roles:

- **Admin** manages doctors, specialties, patients, appointments, and clinical data.
- **Doctor** manages their appointments, relevant patients, medical records, and prescriptions.
- **Patient** manages their profile, books appointments, and views their own records and prescriptions.

## 2. Main Features

- Sanctum registration, login, current-user, and logout flow.
- Role-based authorization enforced by Laravel and React route guards.
- Public doctor directory and specialty listing.
- Admin doctor creation, search, editing, and soft deactivation.
- Admin specialty creation, editing, and protected deletion.
- Patient profile search and own-profile updates.
- Appointment booking, status changes, cancellation, pagination, filters, and conflict prevention.
- Medical records restricted to authorized doctors, patients, and admins.
- Prescriptions with multiple validated medicine items.
- Responsive React dashboards and hospital-style tables/forms.
- Postman collection for the complete API flow.

## 3. Technology Stack

**Backend:** Laravel 12, PHP 8.2+, MySQL, Laravel Sanctum, Eloquent ORM.

**Frontend:** React 19, Vite, Axios, React Router, Tailwind CSS, Lucide React.

**Testing:** Laravel PHPUnit feature tests, Vite production build, and Postman API workflows.

## 4. Project Structure

```text
medicare/
├── backend/
│   ├── app/                 Application code, models, controllers, requests, policies
│   ├── routes/              Laravel API routes
│   ├── database/            Migrations, factories, and seeders
│   └── tests/               Feature and unit tests
├── frontend/
│   ├── src/components/      Reusable UI components
│   ├── src/layouts/         Portal and public page layouts
│   ├── src/pages/           Public, admin, doctor, and patient screens
│   ├── src/services/        Centralized Axios API services
│   └── src/routes/          React Router configuration
├── postman/                 API collection and local environment template
└── README.md
```

In Laravel, `app/` contains application logic, `routes/` defines URLs, `database/` defines storage and seed data, and `tests/` verifies behavior. In React, `src/` contains the interface; components are reusable pieces, pages are screens, layouts provide shared shells, services call the API, and routes connect URLs to pages.

## 5. Requirements

Install these tools before setup:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL, such as the XAMPP MySQL service
- Git

The backend requires PHP `^8.2`; the exact frontend runtime version is determined by your installed Node.js version and the Vite package in `frontend/package.json`.

## 6. Database Setup

Start MySQL, then create the development database:

```sql
CREATE DATABASE medicare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Create `backend/.env` from `backend/.env.example` and set the database values. A safe local example is:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medicare
DB_USERNAME=root
DB_PASSWORD=
```

Use your own local MySQL username and password. Do not commit real credentials.

## 7. Backend Installation

From the project root:

```powershell
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

`composer install` installs PHP dependencies. `key:generate` creates the Laravel application key. `migrate --seed` creates tables and demo data. `php artisan serve` starts the API at `http://127.0.0.1:8000`.

For a disposable development database, `php artisan migrate:fresh --seed` rebuilds all tables and seed data. Do not use it against production data.

## 8. Frontend Installation

Open a second terminal:

```powershell
cd frontend
npm install
copy .env.example .env
npm run dev
```

Set `frontend/.env` to:

```dotenv
VITE_API_URL=http://127.0.0.1:8000/api
```

Open the Vite URL printed in the terminal, normally `http://localhost:5173`.

## 9. Running Both Servers

Terminal 1:

```powershell
cd backend
php artisan serve
```

Terminal 2:

```powershell
cd frontend
npm run dev
```

The frontend uses Axios to call the Laravel API. Both servers must be running for the portal to load live data.

## 10. Demo Accounts

Seed data provides these development accounts. The password is `password123` for each account.

| Role | Email |
| :--- | :--- |
| Admin | `admin@medicare.test` |
| Doctor | `doctor@medicare.test` |
| Patient | `patient@medicare.test` |

## 11. Important Frontend Routes

- Public: `/`, `/login`, `/register`
- Admin: `/admin/dashboard`, `/admin/doctors`, `/admin/patients`, `/admin/appointments`, `/admin/specialties`, `/admin/medical-records`, `/admin/prescriptions`
- Doctor: `/doctor/dashboard`, `/doctor/patients`, `/doctor/appointments`, `/doctor/records`, `/doctor/prescriptions`
- Patient: `/patient/dashboard`, `/patient/profile`, `/patient/appointments`, `/patient/book-appointment`, `/patient/records`, `/patient/prescriptions`

## 12. API Documentation

All protected requests use `Authorization: Bearer {token}`. Responses use `{ status, message, data }`.

| Method | Endpoint | Role | Description |
| :--- | :--- | :--- | :--- |
| POST | `/api/register` | Public | Register a patient |
| POST | `/api/login` | Public | Login and receive a token |
| GET | `/api/me` | Authenticated | Get the current user |
| POST | `/api/logout` | Authenticated | Revoke the current token |
| GET | `/api/doctors` | Public | List searchable doctors |
| GET | `/api/doctors/{id}` | Public | View doctor details |
| GET | `/api/specialties` | Public | List specialties |
| GET/POST/PUT/DELETE | `/api/admin/doctors...` | Admin | Manage doctors |
| POST/PUT/DELETE | `/api/admin/specialties/{id}` | Admin | Manage specialties |
| GET/PUT | `/api/patients...` | Admin, Doctor, Patient | View authorized profiles and update allowed profiles |
| GET/POST/PUT/DELETE | `/api/appointments...` | Authenticated | Book, view, update, filter, and cancel appointments |
| GET/POST/PUT | `/api/medical-records...` | Authorized roles | Manage protected medical records |
| GET/POST/PUT | `/api/prescriptions...` | Authorized roles | Manage prescriptions and items |

## 13. Postman Testing

Import these files into Postman:

- `postman/MediCare.postman_collection.json`
- `postman/MediCare.local.postman_environment.json`

Recommended flow:

1. Login and save the returned token.
2. List specialties and doctors.
3. Create an appointment as the patient.
4. Confirm and complete it as the doctor.
5. Create a medical record and prescription as the doctor.
6. Login as the patient and verify only that patient's data is visible.
7. Test forbidden cross-patient and cross-doctor requests.

## 14. Testing

Run the complete Laravel suite:

```powershell
cd backend
php artisan test
```

The suite covers authentication, role restrictions, seeded relationships, admin management, patient privacy, appointment conflicts and lifecycle, medical records, and prescriptions.

Build the frontend:

```powershell
cd frontend
npm install
npm run build
```

The browser workflows should be checked with the three demo accounts: patient registration/profile/booking, doctor appointment and clinical workflows, and admin doctor/specialty/patient management.

## 15. Architecture

```text
React Frontend
      |
      | Axios / REST API
      v
Laravel API + Sanctum
      |
      v
MySQL Database
```

React presents the portal and calls centralized services. Laravel validates requests, authenticates users, enforces authorization, and applies appointment and clinical business rules. MySQL stores users, profiles, appointments, records, prescriptions, and specialties.

## 16. Database Relationships

```text
User
├── Doctor
└── Patient

Doctor
├── Specialty
├── Appointments
├── Medical Records
└── Prescriptions

Patient
├── Appointments
├── Medical Records
└── Prescriptions

Prescription
└── Prescription Items
```

A doctor and patient meet through appointments. Completed appointments can produce protected medical records and prescriptions. A prescription contains one or more medicine items.

## 17. Troubleshooting

### Database connection error

Check that MySQL is running, the `medicare` database exists, and the credentials in `backend/.env` are correct.

### Laravel key error

Run:

```powershell
cd backend
php artisan key:generate
```

### Migration problems

For local development only, inspect the error first and use `php artisan migrate:status`. If the database can be recreated safely, use `php artisan migrate:fresh --seed`. Do not use that command where data must be preserved.

### Frontend cannot connect to the API

Confirm Laravel is running, `VITE_API_URL` points to the API including `/api`, and the browser console does not report a CORS or network error.

### npm problems

Run `npm install` from `frontend/` and retry `npm run build`. Keep the existing `package-lock.json` and project files intact.

## 18. Portfolio Value

This project demonstrates Laravel REST API design, React application development, MySQL persistence, Sanctum authentication, authorization policies, CRUD workflows, Eloquent relationships, centralized API integration, appointment conflict prevention, protected medical data, responsive dashboards, automated testing, and Git/GitHub-ready documentation.

## 19. Future Improvements

These features are not implemented yet:

- Medical file uploads
- Email reminders
- SMS reminders
- Online payments
- Doctor availability calendar
- Real-time notifications
- Multi-clinic support
- Cloud deployment
- Advanced reports
