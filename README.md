# 🎓 Shri V.J. Modha College — Official Website

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Apache](https://img.shields.io/badge/Apache-2.4-D22128?style=flat-square&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-Proprietary-orange?style=flat-square)](#)

The official web portal for **Shri V.J. Modha College**, an educational institution committed to academic excellence, student empowerment, and career-oriented skill development in Porbandar, Gujarat.

---

## 📖 Table of Contents

- [Overview](#-overview)
- [Key Features & Modules](#-key-features--modules)
- [Tech Stack](#-tech-stack)
- [Project Architecture](#-project-architecture)
- [Getting Started](#-getting-started)
  - [Prerequisites](#prerequisites)
  - [Running with Docker (Recommended)](#running-with-docker-recommended)
  - [Running Locally with PHP Built-in Server](#running-locally-with-php-built-in-server)
- [Helpful Docker Commands](#-helpful-docker-commands)
- [Development Workflow](#-development-workflow)
  - [Component & Template System](#component--template-system)
  - [Asset Versioning & Cache Busting](#asset-versioning--cache-busting)
  - [Configuring Course & Lab Data](#configuring-course--lab-data)
- [Production Deployment](#-production-deployment)
- [Documentation & Project Files](#-documentation--project-files)
- [Roadmap & Planned Enhancements](#-roadmap--planned-enhancements)
- [Troubleshooting & FAQs](#-troubleshooting--faqs)
- [Contact & Campus Location](#-contact--campus-location)

---

## 🌟 Overview

The **Shri V.J. Modha College** web application provides prospective and existing students, parents, and faculty with comprehensive information about academic programs, campus facilities, faculty credentials, placement initiatives, scholarships, event galleries, and administrative committee disclosures.

Built with a modular PHP architecture and modern responsive styling, the portal delivers fast load times, automated asset cache-busting, and effortless maintainability without the overhead of heavy third-party CMS frameworks.

---

## 🚀 Key Features & Modules

- **🏛️ Institution & Leadership**: Detailed profile of the college and interactive slider for the Board of Trustees.
- **📚 Academic Programs (`courses.php`)**: Dynamic directory of undergraduate and postgraduate courses with eligibility criteria, duration, and curriculum highlights.
- **👨‍🏫 Faculty Directory (`faculties.php`)**: Categorized listings of experienced professors, lecturers, and departmental heads with their qualifications and designations.
- **🔬 Infrastructure & Labs (`labs.php`)**: Dedicated facility tours highlighting state-of-the-art computer labs, libraries, and science facilities.
- **💼 Training & Placement Cell (`placement.php`)**: Placement records, recruit partners, and career development initiatives.
- **🏆 Scholarships & Financial Aid (`scholarship.php`)**: Information regarding government and institutional scholarship schemes.
- **🖼️ Campus Gallery & Media (`gallery.php`)**: Interactive image galleries showcasing campus events, celebrations, and achievements.
- **📰 E-Magazine & Publications (`e_mag.php`)**: Digital campus magazines and publications.
- **🛡️ Anti-Ragging & Compliance (`anti_ragging.php`)**: UGC-compliant anti-ragging policies, guidelines, and committee contacts.
- **📱 Responsive & Cache-Optimized**: Mobile-first responsive navigation with automatic stylesheet/script versioning via PHP `filemtime`.

---

## 🛠️ Tech Stack

| Layer | Technology | Description |
| :--- | :--- | :--- |
| **Backend** | [PHP 8.2](https://www.php.net/) | Modular view templates, dynamic data rendering, and server-side components |
| **Web Server** | [Apache 2.4](https://httpd.apache.org/) | HTTP server with `mod_rewrite` enabled for clean routing |
| **Frontend** | HTML5 / Vanilla CSS3 / JS (ES6+) | Lightweight, dependency-free UI with custom CSS variables & Google Fonts |
| **Typography** | [Montserrat (Google Fonts)](https://fonts.google.com/specimen/Montserrat) | Clean modern typography |
| **DevOps** | [Docker](https://www.docker.com/) & [Docker Compose](https://docs.docker.com/compose/) | Containerized local environment with live volume binding |

---

## 📂 Project Architecture

```
VJM-Website/
├── .dockerignore
├── .gitignore
├── Dockerfile                  # Apache + PHP 8.2 container definition
├── docker-compose.yml          # Container configuration with live code mount
├── README.md                   # Project documentation
├── TODO                        # Task tracker
├── docs/                       # Project proposals, agreements, and invoices
│   ├── Documentation.pdf
│   ├── Grantt Chart - Phase 1.xlsx
│   └── Project Development Agreement - Phase 1.pdf
├── resources/                  # Uncompressed raw assets and project archives
└── website/                    # Application web root (mapped to /var/www/html)
    ├── assets/                 # Images, logos, photos, and icons
    ├── components/             # Reusable UI partials (testimonials, stats, etc.)
    ├── scripts/                # Client-side JavaScript (nav.js, gallery.js, etc.)
    ├── styles/                 # Modular CSS files (global, nav, per-page styles)
    ├── header.php              # Global head tags, SEO meta, and asset linkers
    ├── nav.html                # Responsive site navigation bar
    ├── footer.html             # Site footer with quick links and contact info
    ├── index.php               # Homepage
    ├── about.php               # About Us & Board of Trustees
    ├── courses.php             # Courses directory
    ├── courses_data.php        # Structured course catalog data
    ├── faculties.php           # Faculty members directory
    ├── labs.php                # Laboratory & campus facilities
    ├── labs_data.php           # Structured laboratory data
    ├── gallery.php             # Photo & event gallery
    ├── placement.php           # Career & placement cell
    ├── scholarship.php         # Scholarships & awards info
    ├── anti_ragging.php        # Anti-ragging cell & compliance
    ├── e_mag.php               # Digital college magazines
    ├── online_courses.php      # Free online certifications
    ├── contact.php             # Contact details, map & inquiries
    └── disclaimer.php          # Legal disclaimer
```

---

## ⚡ Getting Started

### Prerequisites

- **Docker & Docker Compose** (Recommended): [Get Docker](https://docs.docker.com/get-docker/)
- *Alternatively*: Local **PHP 8.0+** and **Apache** installation

---

### Running with Docker (Recommended)

1. **Clone the repository**:
   ```bash
   git clone https://github.com/ezyway/VJM-Website.git
   cd VJM-Website
   ```

2. **Start the development server**:
   ```bash
   docker compose up -d
   ```

3. **Access the application**:
   Open your browser and navigate to:
   ```
   http://localhost:8080
   ```

4. **Stop the container**:
   ```bash
   docker compose down
   ```

> **Live Reload / Volume Mount**: Any changes made inside the `website/` directory are immediately reflected in the running container without rebuilding.

---

### Running Locally with PHP Built-in Server

If you prefer running without Docker:

```bash
cd website
php -S localhost:8080
```
Then visit `http://localhost:8080` in your web browser.

---

## 🐳 Helpful Docker Commands

| Command | Description |
| :--- | :--- |
| `docker compose up -d` | Start web container in detached mode |
| `docker compose down` | Stop and remove running containers |
| `docker compose logs -f web` | View real-time container output and Apache access/error logs |
| `docker compose restart` | Restart container services |
| `docker compose exec web bash` | Open an interactive Bash shell inside the container |
| `docker compose build --no-cache` | Rebuild image from scratch |

---

## 💻 Development Workflow

### Component & Template System
- **Header**: `header.php` handles SEO meta tags, title generation, viewport configurations, and automatic stylesheet linkage.
- **Navigation & Footer**: `nav.html` and `footer.html` are included in each view for universal consistency across all pages.
- **Reusable Partials**: Look under `website/components/` for section blocks such as testimonials, counters, and academic pass rates.

### Asset Versioning & Cache Busting
Stylesheets and JavaScript scripts use automatic PHP `filemtime` query params (e.g., `global.css?v=1738491823`). When you edit CSS/JS files, browsers automatically receive the updated cache key without requiring manual cache clears.

### Configuring Course & Lab Data
- Course listings, descriptions, and eligibility: Edit `website/courses_data.php`.
- Laboratory details, specs, and gallery associations: Edit `website/labs_data.php`.

---

## 🚀 Production Deployment

When deploying to a production Linux server with Apache:

1. **Upload Files**: Copy the contents of `website/` to the web root (e.g. `/var/www/vjm-website/public_html` or `/var/www/html`).
2. **Enable Apache Modules**:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```
3. **Configure Permissions**:
   ```bash
   sudo chown -R www-data:www-data /var/www/html
   sudo find /var/www/html -type d -exec chmod 755 {} \;
   sudo find /var/www/html -type f -exec chmod 644 {} \;
   ```
4. **VirtualHost Configuration (Example)**:
   ```apache
   <VirtualHost *:80>
       ServerName vjmcollege.org
       ServerAlias www.vjmcollege.org
       DocumentRoot /var/www/html

       <Directory /var/www/html>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/vjm_error.log
       CustomLog ${APACHE_LOG_DIR}/vjm_access.log combined
   </VirtualHost>
   ```

---

## 📑 Documentation & Project Files

Project planning and contract documentation can be referenced in the [`docs/`](docs/) folder:
- **Project Agreement**: `docs/Project Development Agreement - Phase 1.pdf`
- **Gantt Schedule**: `docs/Grantt Chart - Phase 1.xlsx`
- **Technical Documentation**: `docs/Documentation.pdf`

---

## ❓ Troubleshooting & FAQs

<details>
<summary><b>Port 8080 is already in use</b></summary>

If port `8080` is occupied on your host machine, update the port mapping in [`docker-compose.yml`](docker-compose.yml):
```yaml
ports:
  - "8081:80"  # Change host port to 8081 or any free port
```
</details>

<details>
<summary><b>File permission issues inside Docker container</b></summary>

The `Dockerfile` configures ownership to `www-data:www-data`. If you encounter write permission issues during local file generation, rebuild the container:
```bash
docker compose build --no-cache && docker compose up -d
```
</details>

---

## 📍 Contact & Campus Location

**Shri V.J. Modha College**  
*“Vidhyadham”, Chhaya-Birla Road, Nr. Pakshi Abhiyaran, Porbandar, Gujarat – 360575*

- 📞 **Phone**: [+91 997 8818 009](tel:+919978818009)
- 💬 **WhatsApp**: [+91 9825 673 093](https://wa.me/919825673093) / [+91 997 8818 009](https://wa.me/919978818009)
- ✉️ **Email**: [shrivjmodha@gmail.com](mailto:shrivjmodha@gmail.com)
- 🗺️ **Google Maps**: [View Location](https://maps.app.goo.gl/1KuyRCNiCoc7pfun9)

---

*© Shri V.J. Modha College. All rights reserved.*
