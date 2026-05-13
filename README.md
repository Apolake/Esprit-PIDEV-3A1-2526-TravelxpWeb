<p align="center">
  <img src="icon.png" alt="TravelXP Logo" width="120"/>
</p>

<h1 align="center">TravelXP Web — Travel Like a Pro</h1>

<p align="center">
  A full-stack Symfony travel platform for discovering properties, planning trips, booking stays, managing budgets and wallet payments, and engaging with an AI-assisted travel community — powered by maps, dynamic pricing, schedulers, and gamification.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.2+"/>
  <img src="https://img.shields.io/badge/Symfony-7.4-black?logo=symfony&logoColor=white" alt="Symfony 7.4"/>
  <img src="https://img.shields.io/badge/Twig-Templates-339933?logo=symfony&logoColor=white" alt="Twig"/>
  <img src="https://img.shields.io/badge/Doctrine-ORM%203.6-FC6A31?logo=doctrine&logoColor=white" alt="Doctrine ORM"/>
  <img src="https://img.shields.io/badge/MySQL%2FMariaDB-Database-4479A1?logo=mysql&logoColor=white" alt="MySQL/MariaDB"/>
  <img src="https://img.shields.io/badge/Stripe-Wallet%20Payments-635BFF?logo=stripe&logoColor=white" alt="Stripe"/>
  <img src="https://img.shields.io/badge/Gemini_AI-Assistant-4285F4?logo=googlegemini&logoColor=white" alt="Gemini AI"/>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Controllers-25-blueviolet" alt="25 Controllers"/>
  <img src="https://img.shields.io/badge/Services-35-ff69b4" alt="35 Services"/>
  <img src="https://img.shields.io/badge/Entities-18-yellow" alt="18 Entities"/>
  <img src="https://img.shields.io/badge/Repositories-18-cyan" alt="18 Repositories"/>
  <img src="https://img.shields.io/badge/Forms-15-lightgrey" alt="15 Forms"/>
  <img src="https://img.shields.io/badge/Views_(Twig)-93-ff8c00" alt="93 Twig Views"/>
  <img src="https://img.shields.io/badge/Migrations-16-success" alt="16 Migrations"/>
  <img src="https://img.shields.io/badge/Tests-112_passed-brightgreen" alt="112 Tests Passed"/>
</p>

---

## Table of Contents

- [Overview](#overview)
- [Highlights at a Glance](#highlights-at-a-glance)
- [Features](#features)
- [Tech Stack](#tech-stack)
  - [Frontend](#frontend)
  - [Backend](#backend)
- [Architecture](#architecture)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Configuration](#configuration)
  - [Quick Start Cheat Sheet](#quick-start-cheat-sheet)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [API Integrations](#api-integrations)
- [Database](#database)
- [Screenshots](#screenshots)
- [Contributors](#contributors)
- [Academic Context](#academic-context)
- [Acknowledgments](#acknowledgments)

---

## Overview

**TravelXP Web** is the Symfony web implementation of the TravelXP travel management platform. It gives travelers a browser-based experience for exploring accommodations, joining trips and activities, booking properties, paying through a wallet workflow, managing travel budgets, writing blog content, and using AI guidance throughout the journey.

The platform supports two primary roles — **User** and **Admin** — with role-aware navigation, protected dashboards, public catalog browsing, and admin CRUD operations for the core travel resources.

---

## Highlights at a Glance

<table>
<tr>
<td align="center" width="25%">

### Symfony 7.4
MVC web app with Twig, Forms, Security, Messenger

</td>
<td align="center" width="25%">

### 2FA Security
TOTP QR setup + 8 recovery codes

</td>
<td align="center" width="25%">

### AI Assistants
Gemini, OpenAI fallback, optional Ollama

</td>
<td align="center" width="25%">

### Smart Pricing
Season · Timing · Offers · Services

</td>
</tr>
<tr>
<td align="center" width="25%">

### Maps & Routes
Geoapify, Leaflet, OSM, geocoding

</td>
<td align="center" width="25%">

### Stripe Wallet
Checkout top-ups + booking payments

</td>
<td align="center" width="25%">

### Travel Budgets
Expenses, warnings, payment linking

</td>
<td align="center" width="25%">

### Scheduler Jobs
Waiting-list expiry + weather alerts

</td>
</tr>
</table>

---

## Features

### Authentication & Security
- **Email/password authentication** using Symfony Security, CSRF-protected login forms, and automatic password hashing.
- **Role-based access control** with `ROLE_USER` and `ROLE_ADMIN`, separate admin routes, and route-level access rules in `security.yaml`.
- **TOTP two-factor authentication** with OTPHP, 6-digit codes, QR code provisioning via Endroid QR Code, and a dedicated `/2fa/challenge` flow.
- **Recovery codes** — 8 generated recovery codes are hashed with `APP_SECRET` before storage and consumed one time only.
- **Login history** — successful logins can be recorded with IP address, user agent, and optional location metadata from IP-API.
- **Profile management** — users can update profile data, avatar path, password, bio, 2FA settings, and account deletion.

### Trip Planning
- Full **trip CRUD** for admins with name, origin, destination, coordinates, dates, budget, capacity, status, cover image, notes, and XP metadata.
- Public and user trip views: **browse trips**, **my trips**, and detailed trip pages with role-aware actions.
- **Trip participation** — users can join or leave trips; capacity is recalculated from participant counts.
- **Waiting-list support** — full trips move users into waiting-list entries that admins can accept or reject.
- **Trip AI tools** — admin trip description, recommendations, budget plan, and feasibility review; users can ask preset or free-form trip questions.
- **Trip premium tools** — PDF export, email sharing, and QR code generation for trip links.
- **Weather planning** — Open-Meteo forecasts and fallback estimates for mapped destinations, plus scheduled weather warning checks.

### Activity Management
- Full **activity CRUD** with trip linkage, type, description, image URL, date/time, location, transport type, cost, capacity, XP, and status.
- **Activity calendar** powered by FullCalendar, with JSON event endpoints and detail side panels.
- **Activity participation** — users can join/leave activities or enter a waiting list when capacity is full.
- **Temporal status updates** — activities automatically transition to completed when their date/time has passed.
- **Map-aware activities** with Leaflet markers and links to external maps.

### Property & Accommodation
- Browse and administer properties with title, description, type, city, country, address, coordinates, price/night, bedrooms, max guests, images, and active status.
- **Geoapify autocomplete and reverse geocoding** for location picking and coordinate resolution.
- **Leaflet map picker** for precise property placement.
- **Property PDF export** for shareable property sheets.
- **Active offer discovery** through property-linked discount windows.

### Booking Engine
- Complete booking flow from property reservation to payment page with date, duration, services, user ownership, and status tracking.
- **Dynamic pricing engine** with a persisted `pricing_snapshot`:
  - Holiday peak season ×1.24
  - Summer peak season ×1.18
  - Shoulder season ×1.08
  - Low-season advantage ×0.93
  - Weekend demand ×1.06
  - Early-bird discount ×0.95 when booking 60+ days ahead
  - Last-minute premium ×1.07 within 3 days
  - Long-stay discount ×0.93 for 7+ nights
  - Extended-stay discount ×0.88 for 14+ nights
- **Offer discount matching** — the best active property offer is applied when the booking date falls inside the offer window.
- **Service add-ons** — many-to-many services are included in booking totals.
- **Currency conversion** — USD-base live conversion via Open ExchangeRate API with local fallback rates.
- **QR-ready booking details** for payment/confirmation sharing.

### Payments & Wallet
- **Stripe Checkout wallet recharge** using `stripe/stripe-php` and hosted Checkout Sessions.
- **Wallet balance** stored on the user profile and credited after successful Stripe session verification.
- **Booking payment from wallet** with ownership checks, balance validation, payment record creation, and booking confirmation.
- **Payment history** with Stripe session/reference IDs, status, amount, currency, failure reason, and optional budget linking.
- **Budget-aware payments** — payments can be linked to travel budgets for expense tracking.

### Budgets & Expenses
- User-owned travel budgets with title, destination, dates, planned amount, currency, and computed spent/remaining amounts.
- Expense entry CRUD through budget-specific routes.
- Configurable budget warning threshold through `BUDGET_WARNING_THRESHOLD`.
- Supported currencies include USD, EUR, GBP, TND, EGP, NGN, AED, SAR, CAD, JPY, and MAD.

### AI-Powered Features
- **TravelXP Assistant** — authenticated users can ask app-navigation and public-catalog questions from a floating assistant panel.
- **Gemini integration** through Google Generative Language API using `GEMINI_API_KEY` and `GEMINI_MODEL`.
- **Trip AI assistant** supports Gemini, OpenAI Responses API fallback, and optional local Ollama chat.
- **Blog summarization** through `AiSummarizerService`.
- **Privacy guardrails** — the app assistant uses public catalog snapshots and refuses private account data.

### Blog & Community
- Full blog CRUD with title, content, image URL, author, publication timestamps, and user permissions.
- **Comments** with create/edit/delete, ownership checks, and blog linkage.
- **Likes/dislikes** on blogs and comments.
- **Read-time estimation** for blog pages.
- **PDF exports** for blog articles and blog comments.
- **Live search suggestions** for blog discovery.

### Content Moderation & Language Tools
- **Profanity filtering** for blog and comment content before persistence.
- **Grammar checking** via LanguageTool endpoint.
- **Translation** via Google Translate endpoint with fallback providers including MyMemory, Argos OpenTech, and LibreTranslate.
- **Comment sentiment scoring** with positive, neutral, and negative tags.

### Notifications & Schedulers
- User notifications with read/unread state, dropdown panel, full notifications page, and mark-read actions.
- Symfony Scheduler runs:
  - Waiting-list expiration every 15 minutes
  - Weather warning checks every 2 hours
- Scheduler state tracking for admin dashboard visibility.

### Admin Dashboard
- Admin dashboard with metrics for users, trips, activities, joined records, completed trips, and ongoing activities.
- Chart.js dashboards through Symfony UX Chart.js for status distributions, monthly trends, seats, and waiting-list analytics.
- Admin CRUD for users, properties, offers, services, bookings, trips, activities, waiting lists, and gamification.
- Admin gamification tools for quests and user XP/level/streak management.

### UI/UX
- Responsive Twig layouts with a sticky top navigation and role-aware menu items.
- Dark/light theme toggle persisted through local storage and cookies.
- Font Awesome icons, Google Fonts, reusable components, cards, tables, filters, and pagination.
- Stimulus controllers for maps, autocomplete, route rendering, activity calendar, CSRF protection, and AI drawers.

---

## Tech Stack

### Frontend

| Layer | Technology | Version | Purpose |
|-------|-----------|---------|---------|
| **Templates** | Twig | 2.x / 3.x | Server-rendered views and reusable partials |
| **Frontend Runtime** | Symfony Asset Mapper | 7.4 | Importmap-based assets without a Node build step |
| **JS Framework** | Hotwired Stimulus | 3.2.2 | Interactive controllers for maps, forms, AI drawers, calendar |
| **Navigation UX** | Hotwired Turbo | 7.3.0 | Faster page transitions and progressive enhancement |
| **Charts** | Symfony UX Chart.js + Chart.js | 2.24 / 3.9.1 | Admin analytics dashboards |
| **Maps** | Leaflet.js + OpenStreetMap | 1.9.4 | Property, trip, activity, and route maps |
| **Calendar** | FullCalendar | 6.1.15 | Activity calendar display |
| **Icons** | Font Awesome | 6.5.2 | Navigation and action iconography |
| **Styling** | Custom CSS + Google Fonts | — | Dark/light responsive TravelXP UI |

### Backend

| Layer | Technology | Version | Purpose |
|-------|-----------|---------|---------|
| **Language** | PHP | 8.2+ | Application runtime |
| **Framework** | Symfony | 7.4.* | MVC, routing, DI, security, forms, validation |
| **Database ORM** | Doctrine ORM | 3.6 | Entity mapping and repositories |
| **Migrations** | Doctrine Migrations Bundle | 3.4 | Schema evolution |
| **Database** | MySQL / MariaDB | 10.4+ default | Relational persistence through `DATABASE_URL` |
| **Security** | Symfony Security | 7.4 | Login, roles, CSRF, password hashing |
| **2FA** | OTPHP + Endroid QR Code | 11.4 / 6.0 | TOTP verification and QR setup |
| **Payments** | Stripe PHP SDK | 20.x | Wallet top-up Checkout Sessions |
| **PDF** | Dompdf | 3.1 | Trip, property, blog, and comment exports |
| **Messaging** | Symfony Messenger + Scheduler | 7.4 | Scheduled background tasks |
| **HTTP APIs** | Symfony HttpClient | 7.4 | External API integrations |
| **Testing** | PHPUnit | 11.5 | Entity and service tests |
| **Static Analysis** | PHPStan + Symfony/Doctrine extensions | 2.x | Code quality checks |

---

## Architecture

The project follows a **Symfony MVC + service-layer architecture** with Doctrine entities and repositories for persistence, Twig templates for presentation, and Stimulus controllers for progressive frontend behavior.

```
App\
├── Controller/          # HTTP controllers and route actions — 25 classes
├── Entity/              # Doctrine domain entities — 18 classes
├── Repository/          # Doctrine query repositories — 18 classes
├── Form/                # Symfony form types — 15 classes
├── Service/             # Business, API, pricing, AI, payment services — 35 classes
├── EventSubscriber/     # Login history and TOTP enforcement
├── Message/             # Scheduler message DTOs
├── MessageHandler/      # Background job handlers
├── Twig/                # Custom Twig extensions
├── Schedule.php         # Recurring Symfony Scheduler definition
└── Kernel.php           # Symfony kernel
```

**Design Patterns Used:**
- **MVC** — Symfony controllers render Twig views and coordinate request/response flow.
- **Repository Pattern** — Doctrine repositories encapsulate database access.
- **Service Layer** — pricing, payments, AI, maps, notifications, reports, and moderation live outside controllers.
- **Event Subscriber** — login success and request-time TOTP enforcement.
- **Message Handler / Scheduler** — recurring jobs for waiting lists and weather alerts.
- **Provider Fallback Strategy** — AI and translation services try configured providers and degrade gracefully.

### Application Flow

```mermaid
flowchart LR
    A[Open TravelXP Web] --> B[Home / Public Catalog]
    B --> C{Authenticated?}
    C -->|No| D[Register or Login]
    D --> E{TOTP Enabled?}
    E -->|Yes| F[2FA Challenge]
    E -->|No| G[User Portal]
    F --> G
    C -->|Yes| G

    G --> H[Trips & Activities]
    G --> I[Properties & Offers]
    G --> J[Bookings & Wallet]
    G --> K[Budgets]
    G --> L[Blogs & Comments]
    G --> M[TravelXP Assistant]

    I --> N[Reserve Property]
    N --> O[Dynamic Pricing]
    O --> P[Wallet Payment]
    P --> Q[Confirmed Booking]

    H --> R[Join / Waitlist]
    R --> S[Notifications]

    T[Admin] --> U[Admin Dashboard]
    U --> V[CRUD Resources]
    U --> W[Charts & Scheduler Status]
    U --> X[Gamification]
```

### Authentication & 2FA Flow

```mermaid
flowchart TD
    Start([User Opens Login]) --> Login[Email + Password Form]
    Login --> Verify[Symfony Password Verification]
    Verify -->|Invalid| Fail[Access Denied]
    Verify -->|Valid| Check2FA{TOTP Enabled?}
    Check2FA -->|No| Portal[Portal / Target Route]
    Check2FA -->|Yes| Challenge[2FA Challenge]
    Challenge --> Code{6-digit TOTP valid?}
    Code -->|Yes| Portal
    Code -->|No| Recovery{Recovery Code valid?}
    Recovery -->|Yes| Portal
    Recovery -->|No| Retry[Retry Challenge]

    style Portal fill:#22c55e,color:#fff
    style Fail fill:#ef4444,color:#fff
    style Retry fill:#f59e0b,color:#fff
```

### Dynamic Pricing Engine

```mermaid
flowchart TD
    Base["Base Nightly Rate"] --> Season{Season?}
    Season -->|Dec 15-Jan| Holiday["×1.24 Holiday peak"]
    Season -->|Jun-Aug| Summer["×1.18 Summer peak"]
    Season -->|Apr-May, Sep-Oct| Shoulder["×1.08 Shoulder season"]
    Season -->|Other| Low["×0.93 Low season"]

    Holiday & Summer & Shoulder & Low --> Timing{Booking Timing?}
    Timing -->|Weekend| Weekend["×1.06 Weekend demand"]
    Timing -->|60+ days ahead| Early["×0.95 Early-bird"]
    Timing -->|0-3 days ahead| Late["×1.07 Last-minute"]
    Timing -->|Standard| Normal["×1.00 Standard"]

    Weekend & Early & Late & Normal --> Stay{Stay Length?}
    Stay -->|14+ nights| Extended["×0.88 Extended stay"]
    Stay -->|7+ nights| Long["×0.93 Long stay"]
    Stay -->|1-6 nights| NoStay["No stay discount"]

    Extended & Long & NoStay --> Offer{Active Offer?}
    Offer -->|Best property discount| Discount["Subtract offer percent"]
    Offer -->|None| NoDiscount["No offer discount"]
    Discount & NoDiscount --> Services["Add selected services"]
    Services --> Total["Persist total + pricing_snapshot"]

    style Total fill:#22c55e,color:#fff,stroke:#16a34a
    style Base fill:#3b82f6,color:#fff
```

### Entity Relationship Overview

```mermaid
erDiagram
    USER ||--o{ BUDGET : owns
    USER ||--o{ PAYMENT : makes
    USER ||--o{ LOGIN_HISTORY : records
    USER ||--o{ NOTIFICATION : receives
    USER ||--o{ BLOG : writes
    USER }o--o{ TRIP : joins
    USER }o--o{ ACTIVITY : joins
    USER ||--o{ USER_QUEST_PROGRESS : earns

    TRIP ||--o{ ACTIVITY : contains
    TRIP ||--o{ TRIP_WAITING_LIST_ENTRY : queues
    ACTIVITY ||--o{ ACTIVITY_WAITING_LIST_ENTRY : queues

    PROPERTY ||--o{ OFFER : has
    PROPERTY ||--o{ BOOKING : receives
    BOOKING }o--o{ SERVICE : includes
    BOOKING ||--o{ PAYMENT : paid_by

    BUDGET ||--o{ EXPENSE_ENTRY : tracks
    BUDGET ||--o{ PAYMENT : links

    BLOG ||--o{ COMMENT : receives
    USER }o--o{ BLOG : likes
    USER }o--o{ COMMENT : reacts

    QUEST ||--o{ USER_QUEST_PROGRESS : tracks

    USER {
        int id PK
        string username
        string email
        string role
        decimal balance
        boolean totp_enabled
        json recovery_codes
    }
    TRIP {
        int id PK
        string trip_name
        string origin
        string destination
        date start_date
        date end_date
        float budget_amount
        int total_capacity
        string status
    }
    PROPERTY {
        int id PK
        string title
        string city
        string country
        decimal price_per_night
        float latitude
        float longitude
    }
    BOOKING {
        int booking_id PK
        int user_id
        date booking_date
        int duration
        decimal total_price
        json pricing_snapshot
        string booking_status
    }
```

### Gamification Progression

```mermaid
gantt
    title TravelXP Web XP Progression
    dateFormat X
    axisFormat %s XP

    section Rank Bands
    L1 Bronze Traveler      :done, 0, 250
    L2 Bronze Traveler      :done, 250, 500
    L3 Bronze Traveler      :active, 500, 750
    L4 Silver Traveler      :750, 1000
    L5 Silver Traveler      :1000, 1250
    L8 Gold Traveler        :1750, 2000
    L12 Legend Traveler     :2750, 3000
```

---

## Getting Started

### Prerequisites

| Requirement | Version | Notes |
|-------------|---------|-------|
| **PHP** | 8.2+ | Required by `composer.json`; PHP 8.5.5 was used in the recorded PHPUnit run |
| **Composer** | 2.x | Dependency installation; `composer.phar` is also present in the repo |
| **MySQL / MariaDB** | MySQL 8.x or MariaDB 10.4+ | Default `.env` points to a MariaDB-compatible `travelxp` database |
| **Symfony CLI** | Optional | Useful for `symfony server:start`; PHP built-in server also works |
| **Stripe Account** | Optional for payments | Required only for wallet top-up through Stripe Checkout |
| **Gemini / OpenAI / Ollama** | Optional for AI | Gemini powers the main assistant; OpenAI/Ollama are optional trip AI providers |
| **Geoapify API Keys** | Optional for maps | Needed for autocomplete, places, routing, and custom map tiles |

Verify your setup:

```bash
php --version
composer --version
mysql --version
```

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Apolake/Esprit-PIDEV-3A1-2526-TravelxpWeb.git
   cd Esprit-PIDEV-3A1-2526-TravelxpWeb
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Create local configuration**
   ```bash
   cp .env .env.local
   ```
   On Windows PowerShell:
   ```powershell
   Copy-Item .env .env.local
   ```

4. **Configure the database and keys** in `.env.local` (see [Configuration](#configuration)).

5. **Create and migrate the database**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. **Optional: import demo data**
   ```bash
   mysql -u root -p travelxp < mock_examples.sql
   ```

7. **Run the application**
   ```bash
   symfony server:start --port=8000
   ```
   Or without Symfony CLI:
   ```bash
   php -S 127.0.0.1:8000 -t public public/index.php
   ```

8. **Run the scheduler worker** when testing waiting-list expiry or weather jobs:
   ```bash
   php bin/console messenger:consume scheduler_default -vv
   ```

### Configuration

Create `.env.local` and set project-specific values:

```dotenv
# App
APP_ENV=dev
APP_SECRET=change_me_to_a_long_random_secret
APP_SHARE_DIR=var/share
DEFAULT_URI=http://127.0.0.1:8000

# Database
DATABASE_URL="mysql://root:password@127.0.0.1:3306/travelxp?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

# Messenger / Scheduler
MESSENGER_TRANSPORT_DSN=sync://

# Mailer
MAILER_DSN=smtp://user:pass@smtp.example.com:587

# Stripe wallet top-up
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...

# Budget defaults
DEFAULT_BUDGET_CURRENCY=USD
BUDGET_WARNING_THRESHOLD=0.80

# AI providers
GEMINI_API_KEY=your_gemini_key
GEMINI_MODEL=gemini-2.5-flash
OPENAI_API_KEY=
OLLAMA_ENABLED=false
OLLAMA_BASE_URL=http://127.0.0.1:11434
OLLAMA_MODEL=llama3.2

# Blog tools
LANGUAGETOOL_URL=https://api.languagetool.org/v2/check
TRANSLATE_API_URL=https://translate.googleapis.com/translate_a/single?client=gtx&dt=t&sl=auto

# Geoapify
GEOAPIFY_MAP_TILES_API_KEY=
GEOAPIFY_AUTOCOMPLETE_API_KEY=
GEOAPIFY_PLACES_API_KEY=
GEOAPIFY_ROUTING_API_KEY=
GEOAPIFY_MAP_TILES_URL="https://maps.geoapify.com/v1/tile/carto/{z}/{x}/{y}.png"
GEOAPIFY_AUTOCOMPLETE_URL="https://api.geoapify.com/v1/geocode/autocomplete"
GEOAPIFY_PLACES_URL="https://api.geoapify.com/v2/places"
GEOAPIFY_ROUTING_URL="https://api.geoapify.com/v1/routing"
```

> **Security:** Never commit real API keys, Stripe secrets, SMTP passwords, or production `APP_SECRET` values. Keep them in `.env.local` or Symfony secrets.
>
> **Local demo note:** `Admin default.txt` contains local demo admin credentials. Rotate or remove them before any shared or production deployment.

### Quick Start Cheat Sheet

```bash
# 1. Install dependencies
composer install

# 2. Local config
cp .env .env.local

# 3. Database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 4. Start web server
php -S 127.0.0.1:8000 -t public public/index.php

# 5. Optional worker
php bin/console messenger:consume scheduler_default -vv

# 6. Tests and analysis
php bin/phpunit
vendor/bin/phpstan analyse
```

Windows helper scripts are available:

```powershell
.\scripts\dev-up.ps1 -AppPort 8001 -RunChecks
.\scripts\dev-check.ps1 -AppPort 8001
```

---

## Usage

```mermaid
journey
    title User Journey in TravelXP Web
    section Getting Started
      Register an account: 5: User
      Log in securely: 5: User
      Enable TOTP 2FA: 4: User
      Explore the portal: 5: User
    section Planning
      Browse trips: 5: User
      Join trip or waiting list: 4: User
      Review weather and route map: 4: User
      Ask Trip AI: 5: User, AI
    section Booking
      Browse properties: 5: User
      Reserve dates and services: 4: User
      Review dynamic pricing: 4: User
      Recharge wallet with Stripe: 5: Stripe
      Pay booking from wallet: 5: User
    section Engagement
      Create budgets and expenses: 4: User
      Write blog posts: 4: User
      Comment, react, translate: 4: User
      Earn XP and complete quests: 5: System
```

### Step-by-Step

1. **Create an account** or **log in** through `/login`.
2. **Enable 2FA** from Profile to generate a TOTP QR code and recovery codes.
3. **Browse properties, offers, services, trips, activities, and blogs** through the top navigation.
4. **Join trips or activities**; if capacity is full, enter the waiting list.
5. **Reserve a property** from the property detail page and review the dynamic pricing snapshot.
6. **Recharge your wallet** through Stripe Checkout and pay bookings from wallet balance.
7. **Create travel budgets** and record expenses or link payments to budgets.
8. **Ask TravelXP Assistant** for navigation help or public catalog recommendations.
9. **Use admin dashboards** to manage users, resources, charts, waiting lists, quests, and scheduler status.

---

## Project Structure

```
Esprit-PIDEV-3A1-2526-TravelxpWeb/
├── composer.json                    # PHP dependencies and Symfony config
├── composer.lock                    # Locked dependency versions
├── compose.yaml                     # Optional Docker database service
├── importmap.php                    # Asset Mapper import map
├── phpunit.dist.xml                 # PHPUnit configuration
├── phpstan.neon                     # PHPStan static analysis configuration
├── mock_examples.sql                # Optional local demo data
├── Admin default.txt                # Local demo admin credentials
├── public/
│   ├── index.php                    # Front controller
│   ├── icon.png                     # Public favicon/logo
│   └── images/placeholders/         # Placeholder SVG assets
├── assets/
│   ├── app.js                       # Main importmap entrypoint
│   ├── styles/app.css               # Global responsive stylesheet
│   └── controllers/                 # 9 Stimulus controllers
├── config/
│   ├── packages/                    # Symfony bundle configuration
│   ├── routes/                      # Route imports
│   ├── services.yaml                # App services and env binding
│   └── bundles.php                  # Enabled bundles
├── migrations/                      # 16 Doctrine migrations
├── scripts/
│   ├── dev-up.ps1                   # Starts app, Ollama, scheduler
│   └── dev-check.ps1                # Local smoke checks
├── src/
│   ├── Controller/                  # 25 HTTP controllers
│   ├── Entity/                      # 18 Doctrine entities
│   ├── Repository/                  # 18 repositories
│   ├── Form/                        # 15 Symfony form types
│   ├── Service/                     # 35 business/integration services
│   ├── EventSubscriber/             # Login/TOTP event subscribers
│   ├── Message/                     # Scheduler messages
│   ├── MessageHandler/              # Scheduler message handlers
│   ├── Twig/                        # Custom Twig extensions
│   └── Schedule.php                 # Recurring task schedule
├── templates/                       # 93 Twig templates
│   ├── admin/
│   ├── activity/
│   ├── blog/
│   ├── booking/
│   ├── budget/
│   ├── payment/
│   ├── profile/
│   ├── property/
│   ├── security/
│   └── trip/
├── tests/                           # Entity and service tests
└── screenshots/                     # UI screenshots and before/after captures
```

---

## API Integrations

| API / Provider | Purpose | Auth |
|----------------|---------|------|
| [Google Gemini](https://ai.google.dev/) | App assistant and trip AI provider | `GEMINI_API_KEY` |
| [OpenAI Responses API](https://platform.openai.com/docs/api-reference/responses) | Optional trip/blog AI fallback | `OPENAI_API_KEY` |
| [Ollama](https://ollama.com/) | Optional local trip AI provider | Local service |
| [Stripe Checkout](https://stripe.com/docs/checkout) | Wallet top-up sessions and payment verification | Secret key |
| [Geoapify](https://www.geoapify.com/) | Autocomplete, reverse geocoding, places, routing, map tiles | API keys |
| [OpenStreetMap](https://www.openstreetmap.org/) | Leaflet base map data | None |
| [Nominatim](https://nominatim.org/) | Geocoding fallback for trip destinations | None |
| [Open-Meteo](https://open-meteo.com/) | Weather forecast and destination geocoding fallback | None |
| [Open ExchangeRate API](https://open.er-api.com/) | Live currency conversion rates | None |
| [LanguageTool](https://languagetool.org/http-api/) | Grammar and spelling checking | None |
| Google Translate / MyMemory / LibreTranslate | Blog and comment translation fallbacks | None by default |
| [IP-API](https://ip-api.com/) | Login history geolocation metadata | None |

---

## Database

The application uses Doctrine ORM with a default MySQL/MariaDB connection defined by `DATABASE_URL`. The main schema is managed through Doctrine migrations in `migrations/`.

Key tables and join tables include:

| Table | Description |
|-------|-------------|
| `users` | User accounts, roles, wallet balance, profile data, TOTP flags, recovery codes |
| `login_history` | Successful login metadata with IP/user agent/location fields |
| `property` | Accommodation listings with location, price, capacity, and image data |
| `offers` | Time-bound property discounts |
| `booking` | User reservations with dates, status, total price, and pricing snapshots |
| `booking_services` | Many-to-many relationship between bookings and services |
| `services` | Add-on travel services and providers |
| `payments` | Stripe/wallet payment records, statuses, budget/booking links |
| `budgets` | User travel budgets |
| `expense_entries` | Budget expense records |
| `trips` | Trip plans with route, dates, status, seats, budget, and XP |
| `activities` | Trip-linked activities with schedule, location, cost, capacity, and XP |
| `trip_participants` | Users joined to trips |
| `trip_activity_participants` | Users joined to activities |
| `trip_waiting_list_entries` | Trip queue entries for full-capacity trips |
| `activity_waiting_list_entries` | Activity queue entries for full-capacity activities |
| `blogs` | User-authored blog posts |
| `comments` | Blog comments |
| `blog_likes`, `blog_dislikes` | Blog reaction join tables |
| `comment_likes`, `comment_dislikes` | Comment reaction join tables |
| `notifications` | User notifications and read state |
| `quests` | Gamification quests |
| `user_quest_progress` | Per-user quest progress |

### Database Schema Map

```mermaid
flowchart TD
    subgraph "User Domain"
        U[(users)]
        LH[(login_history)]
        N[(notifications)]
    end

    subgraph "Trip Domain"
        T[(trips)]
        A[(activities)]
        TW[(trip_waiting_list_entries)]
        AW[(activity_waiting_list_entries)]
    end

    subgraph "Property & Booking Domain"
        P[(property)]
        O[(offers)]
        B[(booking)]
        S[(services)]
        PM[(payments)]
    end

    subgraph "Budget Domain"
        BG[(budgets)]
        E[(expense_entries)]
    end

    subgraph "Community Domain"
        BL[(blogs)]
        C[(comments)]
    end

    subgraph "Gamification Domain"
        Q[(quests)]
        UQP[(user_quest_progress)]
    end

    U --> LH
    U --> N
    U --> BG
    U --> PM
    U --> BL
    U --> T
    U --> A

    T --> A
    T --> TW
    A --> AW

    P --> O
    P --> B
    B --> S
    B --> PM

    BG --> E
    BG --> PM

    BL --> C
    Q --> UQP
    U --> UQP

    style U fill:#3b82f6,color:#fff
    style T fill:#f59e0b,color:#fff
    style P fill:#10b981,color:#fff
    style B fill:#8b5cf6,color:#fff
    style BL fill:#ef4444,color:#fff
```

---

## Screenshots

### Home & Profile
| Home | Home After | Profile |
|:---:|:---:|:---:|
| ![Home](screenshots/HOME.png) | ![Home After](screenshots/Home%20After.png) | ![Profile](screenshots/profile%20after.png) |

### Core Travel Features
| Trips | Properties | Bookings |
|:---:|:---:|:---:|
| ![Trips](screenshots/Trips%20after.png) | ![Properties](screenshots/properties%20after.png) | ![Bookings](screenshots/bookings%20after.png) |

### Commerce & Planning
| Payments | Budgets | Services |
|:---:|:---:|:---:|
| ![Payments](screenshots/payments%20after.png) | ![Budgets](screenshots/budget%20after.png) | ![Services](screenshots/services%20after.png) |

### Community & Offers
| Blogs | Offers | PHPStan |
|:---:|:---:|:---:|
| ![Blogs](screenshots/blogs%20after.png) | ![Offers](screenshots/offers%20after.png) | ![PHPStan](screenshots/phpstan%20after.png) |

---

## Contributors

<table>
<tr>
<td align="center">
<a href="https://github.com/Apolake">
<img src="https://github.com/Apolake.png" width="80px;" alt="Yassine Raddadi"/><br />
<sub><b>Yassine Raddadi</b></sub>
</a>
</td>
<td align="center">
<a href="https://github.com/Dhia-Raddaoui">
<img src="https://github.com/Dhia-Raddaoui.png" width="80px;" alt="Dhia Raddaoui"/><br />
<sub><b>Dhia Raddaoui</b></sub>
</a>
</td>
<td align="center">
<a href="https://github.com/navTace">
<img src="https://github.com/navTace.png" width="80px;" alt="Anas Nafti"/><br />
<sub><b>Anas Nafti</b></sub>
</a>
</td>
<td align="center">
<a href="https://github.com/ysfltm">
<img src="https://github.com/ysfltm.png" width="80px;" alt="Youssef Litaiem"/><br />
<sub><b>Youssef Litaiem</b></sub>
</a>
</td>
<td align="center">
<a href="https://github.com/omarhlal49">
<img src="https://github.com/omarhlal49.png" width="80px;" alt="Omar Ehlal"/><br />
<sub><b>Omar Ehlal</b></sub>
</a>
</td>
</tr>
</table>

---

## Academic Context

| | |
|---|---|
| **Institution** | [ESPRIT — School of Engineering](https://esprit.tn/) (Tunisia) |
| **Program** | Software Engineering — 3rd Year (3A) |
| **Course** | PIDEV (Projet Intégré de Développement) — 3A1 |
| **Academic Year** | 2025–2026 |
| **Project Type** | Full-stack integrated development project |
| **Team Size** | 5 members |

The **PIDEV** is a capstone-style development project at ESPRIT where students design, implement, and deliver a complete software product covering the full development lifecycle — from requirements gathering and database design through backend logic, UI development, API integration, testing, and deployment.

---

## Acknowledgments

We would like to thank:

- **[ESPRIT](https://esprit.tn/)** — for providing the academic framework and guidance throughout the PIDEV program
- **Our supervising instructors** — for their mentorship, code reviews, and technical direction
- **[Symfony](https://symfony.com/)** — for the PHP web framework powering the application
- **[Doctrine](https://www.doctrine-project.org/)** — for ORM, migrations, and database abstraction
- **[Google Gemini](https://ai.google.dev/)** — for AI assistant capabilities
- **[OpenAI](https://platform.openai.com/)** and **[Ollama](https://ollama.com/)** — for optional AI provider support
- **[Stripe](https://stripe.com/)** — for secure wallet top-up infrastructure
- **[Geoapify](https://www.geoapify.com/)** — for maps, geocoding, places, and routing APIs
- **[OpenStreetMap](https://www.openstreetmap.org/)** and **[Leaflet](https://leafletjs.com/)** — for open map rendering
- **[Open-Meteo](https://open-meteo.com/)** — for weather forecast data
- **[LanguageTool](https://languagetool.org/)** — for grammar and spelling checking
- **[Open ExchangeRate API](https://open.er-api.com/)** — for live currency conversion
- **[Dompdf](https://github.com/dompdf/dompdf)** — for PDF exports
- **[OTPHP](https://github.com/Spomky-Labs/otphp)** and **[Endroid QR Code](https://github.com/endroid/qr-code)** — for TOTP and QR setup
- The **open-source community** — for the libraries and tools that made this project possible

---

<p align="center">
  <img src="https://img.shields.io/badge/Made_with-PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="Made with PHP"/>
  <img src="https://img.shields.io/badge/Powered_by-Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white" alt="Powered by Symfony"/>
  <img src="https://img.shields.io/badge/Built_at-ESPRIT-E4002B?style=for-the-badge" alt="Built at ESPRIT"/>
</p>

<p align="center">
  Built with ❤️ at <strong>ESPRIT</strong> — 2025/2026
</p>
