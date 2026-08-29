# Central Library Management System

A Laravel 12 and Oracle library application for CSE 3110 Database Systems Lab. The interface is inspired by the spacious editorial style, crimson accent, serif headings, and clear service navigation of Harvard Library without copying its branding.

## Features

- Admin, Librarian, and Member role-based access
- Secure registration, login, logout, and database sessions
- Book, author/category detail, copy, and branch management
- Catalog search by title, author, category, or ISBN
- Book issue/return with automatic 14-day due dates
- Reservation queue and member notifications
- Automatic overdue fines at Tk 5 per day
- Inventory, overdue-loan, and outstanding-fine reports
- Responsive and accessible public/member/staff interfaces

## Local setup

Requirements: PHP 8.2+, Composer, Oracle Instant Client, the PHP `oci8` extension, SQL*Plus, and Oracle XE 21c.

```bash
cp .env.example .env
composer install
php artisan key:generate
sqlplus library_user/library_password@localhost:1521/XEPDB1 @database/oracle/schema.sql
php artisan serve
```

Open `http://127.0.0.1:8000`.

The project has only one database connection: Oracle. Schema creation and sample data are handled exclusively by [`database/oracle/schema.sql`](database/oracle/schema.sql); Laravel migrations, SQLite, MySQL, PostgreSQL, SQL Server, and MariaDB configurations are intentionally absent.

The Oracle script includes DDL, DML, primary/foreign/check constraints, sequences, joins, transactions, PL/SQL conditionals, a cursor loop, procedures, a function, triggers, sample data, and report views. These constructs are limited to topics demonstrated in the supplied CSE 3110 lab ZIP.

To replace an older project schema, run the Oracle-only reset script instead:

```bash
sqlplus library_user/library_password@localhost:1521/XEPDB1 @database/oracle/reset.sql
```

`reset.sql` permanently removes the existing Central Library tables and data before rebuilding them.

Sample accounts (all use the password `password`):

| Role | Email |
| --- | --- |
| Admin | `admin@library.test` |
| Librarian | `librarian@library.test` |
| Member | `member@library.test` |

## Tests

Tests are Oracle integration tests. They use the Oracle Server configured in `.env` and verify the Oracle connection, required tables, and PL/SQL object validity:

```bash
php artisan test
```
