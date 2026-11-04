# 📋 Rencana Aksi: Aplikasi CMS & Pertanyaan Masyarakat

> Aplikasi Laravel 13 + Filament v5.11 sebagai backend (Admin Panel + REST API) untuk mengelola konten profil instansi dan menerima pertanyaan dari masyarakat. Frontend publik akan dibangun terpisah menggunakan **Flutter**.

---

## 🗂️ Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        string password
        string avatar
        enum role "admin, editor, viewer"
        timestamp email_verified_at
        timestamps created_at
        timestamps updated_at
    }

    categories {
        bigint id PK
        string name
        string slug UK
        text description
        string icon
        int sort_order
        boolean is_active
        timestamps created_at
        timestamps updated_at
    }

    posts {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string title
        string slug UK
        text summary
        longtext content
        string image
        enum status "draft, published, archived"
        timestamp published_at
        int view_count
        boolean is_featured
        timestamps created_at
        timestamps updated_at
    }

    pages {
        bigint id PK
        bigint user_id FK
        string title
        string slug UK
        longtext content
        string image
        enum status "draft, published"
        int sort_order
        boolean show_in_menu
        timestamps created_at
        timestamps updated_at
    }

    media {
        bigint id PK
        bigint user_id FK
        string file_name
        string file_path
        string mime_type
        bigint file_size
        string alt_text
        string disk
        timestamps created_at
        timestamps updated_at
    }

    menus {
        bigint id PK
        string name
        string location "header, footer, sidebar"
        boolean is_active
        timestamps created_at
        timestamps updated_at
    }

    menu_items {
        bigint id PK
        bigint menu_id FK
        bigint parent_id FK "nullable, self-reference"
        string label
        string url
        string type "page, post, category, custom"
        bigint linkable_id "nullable"
        string linkable_type "nullable"
        int sort_order
        boolean is_active
        timestamps created_at
        timestamps updated_at
    }

    slides {
        bigint id PK
        string title
        text description
        string image
        string link_url
        int sort_order
        boolean is_active
        date start_date
        date end_date
        timestamps created_at
        timestamps updated_at
    }

    settings {
        bigint id PK
        string group
        string key UK
        text value
        string type "text, textarea, image, boolean, json"
        timestamps created_at
        timestamps updated_at
    }

    questions {
        bigint id PK
        bigint topic_id FK
        string name
        string email
        string phone
        string subject
        longtext body
        enum status "pending, answered, rejected"
        string ip_address
        timestamps created_at
        timestamps updated_at
    }

    question_answers {
        bigint id PK
        bigint question_id FK
        bigint user_id FK
        longtext body
        enum status "draft, published, rejected"
        boolean is_published
        timestamp answered_at
        timestamps created_at
        timestamps updated_at
    }

    question_topics {
        bigint id PK
        string name
        string slug UK
        text description
        string icon
        int sort_order
        boolean is_active
        timestamps created_at
        timestamps updated_at
    }

    faqs {
        bigint id PK
        bigint topic_id FK
        string question
        longtext answer
        int sort_order
        boolean is_active
        timestamps created_at
        timestamps updated_at
    }

    users ||--o{ posts : "menulis"
    users ||--o{ pages : "menulis"
    users ||--o{ media : "mengunggah"
    users ||--o{ question_answers : "menjawab"

    categories ||--o{ posts : "memiliki"

    menus ||--o{ menu_items : "memiliki"
    menu_items ||--o{ menu_items : "parent-child"

    question_topics ||--o{ questions : "topik"
    question_topics ||--o{ faqs : "topik"

    questions ||--o{ question_answers : "dijawab"
```

---

## 🏛️ Arsitektur Sistem (API-First)

```mermaid
flowchart LR
    subgraph Flutter["Flutter App (Mobile & Web)"]
        FA["📱 Flutter Frontend"]
    end

    subgraph Laravel["Laravel Backend"]
        API["🔌 REST API\n/api/v1/*"]
        FP["🛠️ Filament Admin Panel\n/admin"]
        Models["📦 Models & Business Logic"]
        DB[("🗄️ Database\nMySQL/PostgreSQL")]
    end

    FA -->|"HTTP Requests\n(JSON)"| API
    API --> Models
    FP --> Models
    Models --> DB
    API -->|"JSON Responses"| FA
```

---

## 🏗️ Arsitektur Modul

### Modul 1: CMS (Content Management System)

| Fitur | Deskripsi |
|-------|-----------|
| **Manajemen Post/Artikel** | CRUD artikel dengan kategori, gambar unggulan, status draft/publish/archived, rich text editor |
| **Manajemen Halaman Statis** | Halaman seperti "Tentang Kami", "Visi Misi", "Struktur Organisasi" |
| **Manajemen Kategori** | Pengelompokan artikel berdasarkan kategori |
| **Manajemen Media** | Upload & kelola gambar dan file |
| **Manajemen Menu** | Menu navigasi dinamis untuk header, footer, sidebar |
| **Manajemen Slide** | Slide promosi di halaman utama dengan jadwal tayang |
| **Pengaturan Situs** | Nama instansi, logo, alamat, kontak, media sosial |

### Modul 2: Pertanyaan Masyarakat

| Fitur | Deskripsi |
|-------|-----------|
| **Form Pertanyaan Publik** | REST API endpoint untuk masyarakat mengirim pertanyaan (dikonsumsi Flutter) |
| **Topik Pertanyaan** | Kategori topik pertanyaan (pelayanan, administrasi, dll) |
| **Manajemen Pertanyaan** | Admin review, jawab, atau tolak pertanyaan |
| **FAQ** | Kumpulan pertanyaan yang sering diajukan per topik |
| **Notifikasi Email** | Notifikasi ke penanya ketika pertanyaan dijawab |

---

## 📦 Tahapan Implementasi

### Fase 1: Fondasi & Konfigurasi ⏱️ ~2 hari

- [x] Install Laravel 13 + Filament v5
- [x] Konfigurasi database (`.env`)
- [x] Setup Filament Admin Panel (tema, warna, branding)
- [x] Konfigurasi role & permission pada model User
- [x] Buat migration untuk tabel `settings`
- [x] Buat `SettingResource` di Filament

### Fase 2: Modul CMS ⏱️ ~5 hari

- [x] **Kategori**
  - [x] Buat Model, Migration, Factory, Seeder
  - [x] Buat `CategoryResource` (Filament)
- [x] **Post/Artikel**
  - [x] Buat Model, Migration, Factory, Seeder
  - [x] Buat `PostResource` (Filament) dengan rich text editor
  - [x] Implementasi slug otomatis, featured image, status filter
- [x] **Halaman Statis**
  - [x] Buat Model, Migration
  - [x] Buat `PageResource` (Filament)
- [x] **Media Library**
  - [x] Buat Model, Migration
  - [x] Buat `MediaResource` atau integrasikan Filament Spatie Media Library
- [x] **Menu & Menu Items**
  - [x] Buat Model, Migration
  - [x] Buat `MenuResource` dengan nested menu items (Repeater)
- [x] **Slide**
  - [x] Buat Model, Migration
  - [x] Buat `SlideResource` (Filament)

### Fase 3: Modul Pertanyaan Masyarakat ⏱️ ~3 hari

- [x] **Topik Pertanyaan**
  - [x] Buat Model, Migration
  - [x] Buat `QuestionTopicResource` (Filament)
- [x] **Pertanyaan**
  - [x] Buat Model, Migration
  - [x] Buat `QuestionResource` (Filament) dengan filter status
  - [x] Implementasi validasi & rate limiting pada API
- [x] **Jawaban Pertanyaan**
  - [x] Buat Model, Migration
  - [x] Integrasikan jawaban sebagai Relation Manager di `QuestionResource`
  - [x] Kirim notifikasi email ke penanya
- [x] **FAQ**
  - [x] Buat Model, Migration
  - [x] Buat `FaqResource` (Filament)

### Fase 4: REST API untuk Flutter ⏱️ ~4 hari

- [x] Install & konfigurasi Laravel Sanctum untuk autentikasi API
- [x] **API Publik (tanpa auth)**
  - [x] `GET /api/posts` — Daftar artikel (pagination, filter kategori)
  - [x] `GET /api/posts/{slug}` — Detail artikel
  - [x] `GET /api/pages/{slug}` — Detail halaman statis
  - [x] `GET /api/categories` — Daftar kategori
  - [x] `GET /api/slides` — Daftar slide aktif
  - [x] `GET /api/menus/{location}` — Menu berdasarkan lokasi (header/footer/sidebar)
  - [x] `GET /api/settings` — Pengaturan situs (nama, logo, kontak, dll)
  - [x] `GET /api/faqs` — Daftar FAQ (filter per topik)
  - [x] `GET /api/question-topics` — Daftar topik pertanyaan
  - [x] `GET /api/questions` — Daftar pertanyaan yang sudah dijawab (publik)
  - [x] `POST /api/questions` — Kirim pertanyaan baru (throttle/rate limit)
- [x] Buat API Resource classes (`PostResource`, `SlideResource`, dll) untuk response formatting
- [x] Implementasi API versioning (`/api/v1/...`)
- [ ] Dokumentasi API (menggunakan Scramble atau koleksi Postman)
- [x] CORS configuration untuk akses dari Flutter

### Fase 5: Polish & Deployment ⏱️ ~2 hari

- [x] Dashboard widget (statistik artikel, pertanyaan masuk, dll)
- [x] Optimasi performa API (caching, eager loading, response compression)
- [x] Testing (unit & feature tests untuk API endpoints)
- [ ] Deployment & konfigurasi server

---

## 🗃️ Struktur File yang Akan Dibuat

```
app/
├── Models/
│   ├── User.php (modifikasi: tambah role)
│   ├── Category.php
│   ├── Post.php
│   ├── Page.php
│   ├── Media.php
│   ├── Menu.php
│   ├── MenuItem.php
│   ├── Slide.php
│   ├── Setting.php
│   ├── Question.php
│   ├── QuestionAnswer.php
│   ├── QuestionTopic.php
│   └── Faq.php
├── Filament/
│   ├── Resources/
│   │   ├── CategoryResource.php
│   │   ├── PostResource.php
│   │   ├── PageResource.php
│   │   ├── MediaResource.php
│   │   ├── MenuResource.php
│   │   ├── SlideResource.php
│   │   ├── SettingResource.php
│   │   ├── QuestionResource.php
│   │   ├── QuestionTopicResource.php
│   │   └── FaqResource.php
│   └── Widgets/
│       ├── StatsOverviewWidget.php
│       ├── LatestQuestionsWidget.php
│       └── LatestPostsWidget.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           ├── PostController.php
│   │           ├── PageController.php
│   │           ├── CategoryController.php
│   │           ├── SlideController.php
│   │           ├── MenuController.php
│   │           ├── SettingController.php
│   │           ├── QuestionController.php
│   │           ├── QuestionTopicController.php
│   │           └── FaqController.php
│   └── Resources/
│       └── Api/
│           └── V1/
│               ├── PostResource.php
│               ├── PageResource.php
│               ├── CategoryResource.php
│               ├── SlideResource.php
│               ├── MenuResource.php
│               ├── MenuItemResource.php
│               ├── SettingResource.php
│               ├── QuestionResource.php
│               ├── QuestionTopicResource.php
│               ├── QuestionAnswerResource.php
│               └── FaqResource.php
└── Notifications/
    └── QuestionAnsweredNotification.php

database/
├── migrations/
│   ├── xxxx_create_categories_table.php
│   ├── xxxx_create_posts_table.php
│   ├── xxxx_create_pages_table.php
│   ├── xxxx_create_media_table.php
│   ├── xxxx_create_menus_table.php
│   ├── xxxx_create_menu_items_table.php
│   ├── xxxx_create_slides_table.php
│   ├── xxxx_create_settings_table.php
│   ├── xxxx_create_question_topics_table.php
│   ├── xxxx_create_questions_table.php
│   ├── xxxx_create_question_answers_table.php
│   └── xxxx_create_faqs_table.php
├── factories/
└── seeders/

routes/
└── api.php (REST API routes)
```

---

## ⚙️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend Framework | Laravel 13 |
| Admin Panel | Filament v5.11 |
| PHP | 8.3+ |
| Database | MySQL / PostgreSQL |
| API Authentication | Laravel Sanctum (token-based) |
| Rich Text Editor | Filament TipTap Editor |
| File Storage | Laravel Filesystem (local/S3) |
| Notifikasi | Laravel Notifications (email) |
| API Documentation | Scramble / Postman Collection |
| Frontend (terpisah) | Flutter (mobile & web) |

---

> [!TIP]
> Mulai dari **Fase 1** terlebih dahulu. Setelah fondasi siap, kita bisa langsung mengeksekusi pembuatan model, migration, dan Filament Resources secara bertahap.

> [!IMPORTANT]
> Pastikan database sudah dikonfigurasi di file `.env` sebelum memulai migrasi. Jalankan `php artisan migrate` setelah setiap fase migration selesai.

> [!NOTE]
> Arsitektur ini menggunakan pola **API-first**: Laravel sebagai backend murni (Admin Panel + REST API), sementara Flutter mengkonsumsi data melalui REST API. Tidak ada Blade views atau Livewire di sisi publik.

