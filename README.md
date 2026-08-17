# Catholic Association of Youth (CAM) Diocesan Festival 2026 Management System
### Catholic Diocese of Livingstone

A comprehensive, real-time festival management and digital adjudication platform built with **Laravel 12**, **Filament 3**, **Livewire 3**, and **Tailwind CSS**.

---

## 🌟 Key System Modules & Features

### 1. 🔐 Unified Official Authentication
- **Central Sign-In Panel**: `/admin/login`
- **Role-Based Redirection**:
  - **Judges** are routed directly into the in-dashboard **Judge Workstation**.
  - **Admins** land on the main **Admin Dashboard** with festival oversight stats.
- **Strict Sidebar Isolation**: Judges only see their Dashboard and Judge Workstation — all backend tables and settings are restricted to Admins.

### 2. ⚖️ 3-Judge Digital Adjudication Workstation
- Accessible under **Filament &rarr; Judging & Results &rarr; ⚖️ Judge Workstation**.
- **Official Judge Seats**: `Judge 1` (Technical), `Judge 2` (Artistic & Harmony), and `Judge 3` (Presentation & Diction).
- **Dedicated In-Page Scorecards**: Real-time rubric point computation, max score validation, performance metadata (conductor, song title, language), remarks, and score locking.
- **Automatic Average Consolidation**: Instantly computes normalized averages across 3 judges upon submission.

### 3. 👥 Adjudicator & Judge Assignment Manager (Admin Only)
- Accessible under **Filament &rarr; Festival Operations &rarr; Adjudicators & Assignments**.
- Create, assign, or reassign judges to official seats (`Judge 1`, `Judge 2`, `Judge 3`) and monitor live evaluation counts.

### 4. 📅 Editable Timetable & Live Stage Control
- Accessible under **Filament &rarr; Festival Operations &rarr; Timetable & Program**.
- **Inline Table Cell Editing**: Instant 1-click stage status updates (`⏳ Scheduled`, `● LIVE`, `✓ Completed`), order number changes, title edits, and time penalty recordings.
- **Dynamic Current-Day Filtering**: Full support for all festival days including **Monday** opening day.

### 5. ⛪ Diocesan Parishes & Check-In System
- Categorized dropdown covering all **16 Parishes** across the 3 canonical Deaneries:
  - **Livingstone Deanery**: St. Theresa's Cathedral, Christ the King, Kazungula, Maria Regina, Our Lady of Angels, St. Francis, St. Peter, St. Thomas the Apostle.
  - **Sesheke Deanery**: St. Kizito, St. Fidelis, St. Mary's Njoko, St. Arnold Janssen, Nawinda.
  - **Sioma Deanery**: Lusu, Sioma, Shangombo.
- **Public Parishes Page** (`/registration`): Displays only parishes that have physically checked in to the festival camp.

### 6. 🏆 Real-Time Leaderboard & Big Screen Display
- **Live Leaderboard** (`/leaderboard`): Overall Diocesan Championship Cup standings and category winners.
- **Big Screen Stage Projector** (`/leaderboard/big-screen`): High-contrast live projector view with rotating standings.

---

## 🔑 Default Login Credentials

| Role | Email | Password | Assigned Seat / Scope |
| :--- | :--- | :--- | :--- |
| **Festival Administrator** | `admin@camfestival.org` | `password` | Full System & Judge Assignment |
| **Adjudicator 1** | `judge1@camfestival.org` | `password` | `Judge 1` Live Workstation |
| **Adjudicator 2** | `judge2@camfestival.org` | `password` | `Judge 2` Live Workstation |
| **Adjudicator 3** | `judge3@camfestival.org` | `password` | `Judge 3` Live Workstation |

---

## 🚀 Quick Start & Development Guide

### 1. Prerequisites
- PHP 8.2+ with SQLite, cURL, MBString, and OpenSSL extensions
- Composer 2+
- Node.js 18+ & npm

### 2. Installation
```bash
# Clone the repository
git clone https://github.com/Mutale-Kabamba/cam_app.git
cd cam_app

# Install PHP dependencies
composer install

# Install frontend dependencies & build assets
npm install
npm run build

# Configure environment
cp .env.example .env
php artisan key:generate

# Run database migrations and seed diocesan festival dataset
php artisan migrate:fresh --seed
```

### 3. Running the Application
```bash
# Start local PHP server (runs at http://127.0.0.1:8000)
php artisan serve

# (Optional) Run Vite dev server for hot asset reloading
npm run dev
```

### 4. Running Automated Tests
```bash
php artisan test
```

---

## 📍 Primary Application URLs

- **Public Timetable**: [http://127.0.0.1:8000/program](http://127.0.0.1:8000/program)
- **Checked-In Parishes**: [http://127.0.0.1:8000/registration](http://127.0.0.1:8000/registration)
- **Diocesan Leaderboard**: [http://127.0.0.1:8000/leaderboard](http://127.0.0.1:8000/leaderboard)
- **Big Screen Stage Display**: [http://127.0.0.1:8000/leaderboard/big-screen](http://127.0.0.1:8000/leaderboard/big-screen)
- **Official Login**: [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login)
- **Filament Admin / Judge Portal**: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
