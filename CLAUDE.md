# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

An Attendance & Payroll system for "ACE Medical Center", written in procedural PHP on the
classic AdminLTE 2 / Bootstrap 3 admin template. It runs under XAMPP (Apache + MariaDB) with
no framework, no build step, and no automated tests. PHP files are served directly by Apache
from `c:\xampp\htdocs\apsyste`.

- **Public app** (repo root): `index.php` is a simple **landing page** with two choices —
  Employee Portal (`employee/`) and HR/Admin (`admin/`). The old kiosk time clock (`attendance.php`
  + the in/out form) was **removed** — attendance comes from the biometric CSV import
  (`admin/attendance_import.php`), not a manual punch screen.
- **Admin app** (`admin/`): the HR back office — employees, departments, positions, schedules,
  attendance, deductions, holidays, overtime, cash advance, payroll PDF generation, and 13th
  month pay. This is where ~all real work happens.
- **Employee portal** (`employee/`): a self-service area where each employee logs in to see ONLY
  their own data — dashboard, payslip PDF, attendance, 13th month, and an account page to change
  their password. Auth is separate from admin: it uses `$_SESSION['employee']` (the int
  `employees.id`), gated by `employee/includes/session.php`. Login is **Employee ID =
  `employees.employee_id` (the badge, unique) + password = `employees.password`** (bcrypt; default
  `123456`). (The `employees.username` column still exists but is now vestigial — login no longer
  uses it.) It reuses the shared compute (`payslip_compute.php`, `payslip_render.php`,
  `thirteenth_month_compute.php`) via `../admin/includes/...`, so the employee payslip is byte-for-byte
  the same form as the admin one but scoped to the logged-in employee. Chrome lives in
  `employee/includes/` (its own header/navbar/menubar/scripts) and loads root assets with `../`.

## Running & data

- Serve via XAMPP and open `http://localhost/apsyste/` (landing page), `http://localhost/apsyste/admin/`
  (HR login), or `http://localhost/apsyste/employee/` (employee portal). There is no CLI entry point,
  build, lint, or test command — edit a `.php` file and refresh the browser.
- Database is MySQL/MariaDB schema **`payroll_advnce`** (note: the DB name differs from the repo
  folder name `apsyste`). Credentials are hardcoded as `root` / empty password in the two
  `conn.php` files (`conn.php` at root and `admin/includes/conn.php`).
- Import/reset schema from `db/apsystem.sql` via phpMyAdmin or
  `mysql -u root payroll_advnce < db/apsystem.sql`. **Caveat:** this dump is still stale on the
  *data* side (rows lag the live DB), so treat the live DB as source of truth. The
  `thirteenth_month_release` table (previously missing) has now been added to the dump, so a fresh
  import no longer fatals on the 13th-month pages.
- Default admin login is username `admin` (see the seeded `admin` row; password is a bcrypt hash).
  Passwords are checked with `password_verify` against `admin.password`.

## Architecture & conventions

The codebase follows one rigid CRUD pattern per entity. Learn it once from
[admin/department.php](admin/department.php) + [admin/includes/department_modal.php](admin/includes/department_modal.php)
and every other entity (employee, position, schedule, holiday, deduction, overtime, cashadvance,
deduction_type, employee_deduction) is the same shape with different fields:

- **`<entity>.php`** — full page: includes session/header/navbar/menubar, renders a Bootstrap
  `box` with a DataTable (`<table id="example1">`), and inlines the jQuery that wires `.edit` /
  `.delete` buttons to a `getRow(id)` AJAX call.
- **`includes/<entity>_modal.php`** — the Add / Edit / Delete Bootstrap modals; forms POST
  directly to the handler scripts.
- **`<entity>_add.php` / `_edit.php` / `_delete.php`** — handler scripts. They `include
  'includes/session.php'`, mutate the DB, set `$_SESSION['success']` or `$_SESSION['error']`,
  then `header('location: <entity>.php')`. The list page reads and unsets those flash messages
  at the top to show an alert.
- **`<entity>_row.php`** — POST `{id}`, returns one row as JSON via `echo json_encode($row)`;
  consumed by `getRow()` to populate the edit/delete modals.

### Shared includes (`admin/includes/`)

Every admin page composes these by `include` (no autoloading, no templating engine):

- **`session.php`** — the gatekeeper. Calls `session_start()`, includes `../includes/conn.php`,
  redirects to `../index.php` if `$_SESSION['admin']` is unset, then loads the current admin into
  `$user`. **Every protected page and handler must include this first.**
- **`header.php`** / **`scripts.php`** — open the HTML doc / load all the JS libs (jQuery,
  Bootstrap, DataTables, Moment, daterangepicker, TCPDF is loaded separately). Asset paths are
  relative with `../` because admin pages live one level down.
- **`navbar.php`**, **`menubar.php`** (left sidebar), **`footer.php`** — chrome.
- **`datatable_initializer.php`** — initializes `#example1` / `#example2` DataTables; include it
  *after* `scripts.php` on any list page.
- **`tax.php`** — `compute_tax($salary)` implements the Philippine BIR graduated withholding
  brackets. This is now the **single canonical copy** — `deduction.php`, `payroll.php` and
  `payslip_compute.php` all `include` it (the old duplicate `tax_table.php` was deleted). The one
  remaining stray copy is the inline `compute_tax` inside `payroll_generate.php` — keep it in sync
  with `tax.php` if brackets change (better: replace it with an `include` guarded by
  `function_exists`).
- **`upload_photo.php`** — `save_employee_photo($file)`: the **only** safe way to handle employee/
  admin photo uploads. Validates extension + `getimagesize()`, stores under a server-generated
  random name in `/images`, returns the stored filename or `null`. Used by `employee_add.php`,
  `employee_edit_photo.php`, `profile_update.php`. Never call `move_uploaded_file` with a raw
  `$_FILES['...']['name']` again (that was an RCE).

### Page layout note

`menubar.php` highlights the active link via `$page == '...'`. `$page` is now assigned in
`includes/session.php` as `basename($_SERVER['PHP_SELF'])`, so server-side highlighting works on
every protected page. As a belt-and-suspenders, `scripts.php` also adds `.active`/`.menu-open` by
matching the sidebar link's file name against `window.location` (ignoring any `?query`).

## Payroll domain logic (the important part)

The real complexity lives in the PDF generators, which compute pay rather than just CRUD:

- [admin/payroll_generate.php](admin/payroll_generate.php) — payroll summary cards.
- [admin/payslip_generate.php](admin/payslip_generate.php) — per-employee payslips.
- [admin/thirteenth_month.php](admin/thirteenth_month.php) (list) + `_history.php` (per-employee
  release log) + `_pdf.php` + `_release.php` (record a release) — 13th month. **All four call the
  single shared `compute_thirteenth_month($conn, $empid, $year)` in
  [admin/includes/thirteenth_month_compute.php](admin/includes/thirteenth_month_compute.php)** — do
  the math there only, never re-derive it in a page. Rule (PD 851 / DOLE): 13th month = (basic
  salary actually *earned* that calendar year) / 12, where basic earned per day = `MIN(num_hr, 8) ×
  hourly_rate` summed from `attendance` (the `MIN(...,8)` cap excludes OT, premiums, allowances).
  Entitlement = hired ≥ 1 month into the year; `balance = thirteenth_month − SUM(already released)`.
  Releases are logged in the **`thirteenth_month_release`** table (`employee_id, release_year,
  release_date, amount, release_type`, plus `id`/`remarks`) — now included in `db/apsystem.sql`.

- [admin/payslip_generate.php](admin/payslip_generate.php) — per-employee payslips, rebuilt to
  replicate the **ACE Butuan manual payslip form** exactly (two side-by-side EARNINGS / DEDUCTIONS
  ledgers with every fixed line item). All math lives in the single shared
  `compute_payslip($conn, $empid, $from, $to)` in
  [admin/includes/payslip_compute.php](admin/includes/payslip_compute.php) — do the math there
  only, never re-derive it in the page (same rule as 13th month). This payslip is **top-down**, NOT
  the bottom-up `payroll_generate.php` model: `BASIC SALARY = monthly/2` (fixed semi-monthly), then
  **deduct** `ABSENCES = absent_days × daily_rate` and `LATE = late_minutes × (hourly/60)`.
  - **Absences** are derived (no day-of-week schedule exists): expected workdays = calendar days in
    the period minus **Sundays** (the assumed rest day) and paid holidays; `absences = expected −
    present_workdays`. **Late/undertime** come from `attendance.time_in/out` vs the employee's
    `schedules.time_in/out`, in minutes.
  - `compute_payslip` returns earning/deduction line items keyed by the **exact form labels** (see
    `$PAYSLIP_EARNING_ROWS` / `$PAYSLIP_DEDUCTION_ROWS` in the include). The form lines it does NOT
    yet populate (fixed-table SSS/Pag-IBIG/PHIC amounts, the per-department deductions, ACE Coop,
    donation, night-diff/rest-day premiums, salary/OT adjustments) render as `0`/`-` on purpose —
    they are the agreed **incremental** follow-up; wire each by filling its label in the compute fn,
    and the renderer picks it up automatically. `payroll_generate.php` still uses the older
    bottom-up base-pay model and was intentionally left unchanged.

Both payroll/payslip take a `$_POST['date_range']` ("MM/DD/YYYY - MM/DD/YYYY" from the
daterangepicker), `explode(' - ')` it into `$from`/`$to`, build an HTML string, and render with
**TCPDF** (`require_once('../tcpdf/tcpdf.php')` → `$pdf->Output(..., 'I')` to stream inline).

Key computation rules in `payroll_generate.php` (mirror these when editing payslips):

- **Rates:** `position.rate` is treated as **monthly** salary; `daily_rate = rate / 26`,
  `hourly_rate = daily_rate / 8`. An employee's rate comes from `position` via `employees.position_id`
  (the `employees.salary` column exists but is `0.00` / unused).
- **Base pay** = worked hours × hourly rate, summed over non-holiday days only.
- **Holidays:** Regular holiday worked → double pay (×2); regular holiday absent → still paid one
  `daily_rate`. Special holiday worked → +30%. Holiday type comes from the `holidays.type` column
  ("Regular"/"Special"), matched case-insensitively.
- **Undertime:** hours < 8 on a present day deducts `(8 - hrs) × hourly_rate`.
- **Government deductions** (`deductions` where `is_government`): each is `fixed` or `percent`
  (% of monthly salary), then **halved** (`/ 2`) because payroll is semi-monthly.
- **Tax** is computed on **half** the monthly salary (`compute_tax(monthly/2)`).
- Overtime, cash advance, and per-employee `employee_deductions` are also subtracted.
- **Net** = base + holiday + overtime − (gov + employee deductions + tax + cash advance + undertime).

## Data model notes / footguns

- **SQL injection:** many list pages and `_row.php` handlers still interpolate `$_POST`/`$_GET`/
  `$_SESSION` directly into queries (e.g. `WHERE id='$id'`) — this is the legacy pattern and is
  being migrated away. Already converted to prepared statements: `department_*`, `attendance_import.php`,
  `login.php`, `employee_add.php`, `employee_edit.php`, `employee_edit_photo.php`, `profile_update.php`,
  and the `employee/` portal. When adding or editing **any** handler, use
  `$conn->prepare()` + `bind_param()` (and `intval()`/`trim()` inputs) — never string interpolation.
  Still TODO: `position_*`, `holiday_*`, `overtime_*`, `cashadvance_*`, the screen queries in
  `payroll.php`, and the remaining `_row.php` endpoints.
- **Three id systems for employees:** `employees.id` (int PK), `employees.employee_id` (the
  human/badge varchar like `LET025174983`), and `employees.biometric_id` (the device "Staff Code").
  Attendance and payroll join on `employees.id`, but some tables (`cashadvance`, `overtime`) store
  `employee_id` as a varchar, and the importer matches on `biometric_id`. Be deliberate about which
  one a query needs. Note `attendance.employee_id` holds the int `employees.id`, not the badge.
- **Employee login:** the `employee/` portal logs in with **`employees.employee_id` (the badge) +
  `employees.password`** (bcrypt hash, default `123456`). New employees get the default password set
  automatically in `employee_add.php`. The `password` column was added to the live DB and to
  `db/apsystem.sql`; rows imported from an old dump need it seeded
  (`password_hash('123456', ...)`). The `username` column also exists (seeded to lastname) but is
  vestigial — nothing reads it anymore.
- **Biometric attendance import:** [admin/attendance_import.php](admin/attendance_import.php) (UI is
  the `#import_biometric` modal in `includes/attendance_modal.php`) parses a CSV exported from the
  biometric device — matching `Staff Code → employees.biometric_id` (zero-padding tolerant), taking
  earliest punch as time-in / latest as time-out, subtracting a 1h break when the span > 4h, and
  upserting one `attendance` row per employee+date with prepared statements. Late = `time_in >`
  schedule `time_in`. Employees with no `biometric_id` set can't be imported.
- **Deductions are split across tables:** `deductions` (global government/standard), `deduction_types`
  (categories like Cafeteria/Uniform), `employee_deductions` (per-employee assignments), plus the
  unused-looking `employee_other_deductions` / `ace_employee_deductions`. Confirm which table a
  feature reads before changing it.
- The stray `admin/includes/employee_modal copy.php` backup and the dead `admin/payroll2.php` have
  been **deleted**; `employee_modal.php` and `payroll.php` are the live versions.

## Vendored libraries (do not edit)

`bower_components/`, `plugins/`, `dist/`, `build/`, and `TCPDF/` (plus `TCPDF-main.zip`) are
third-party assets committed into the repo. Don't modify or "clean up" these. App code lives in
the repo root `.php` files and `admin/`.
