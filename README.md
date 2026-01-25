# Mini CMS

A modern, lightweight Content Management System built with Laravel.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)

---

## ✨ Features

### Content Management
- 📝 **Posts & Pages** - Full CRUD with SEO meta, soft deletes
- 🎨 **TinyMCE Editor** - Rich text editing with media integration
- 🏷️ **Taxonomies** - Categories and Tags
- 📁 **Media Library** - Folders, search, metadata, safe delete

### Page Builder
- 📦 **ZIP Upload** - Upload static HTML/CSS/JS sites
- 🔗 **Contact Wiring** - Auto-inject forms to create leads
- 🚀 **Static Serving** - Optimized delivery at `/b/{slug}`

### Analytics
- 📊 **Dashboard** - Chart.js visualizations
- 📈 **View Tracking** - Unique daily post views
- 📋 **Activity Log** - Full audit trail

### SEO
- 🗺️ **Sitemap** - Dynamic XML generation
- 🤖 **Robots.txt** - Search engine directives
- 📑 **Schema.org** - JSON-LD markup

### Admin
- 👥 **User Roles** - Admin & Editor
- 🌙 **Dark Mode** - Eye-friendly interface
- 📱 **Responsive** - Mobile-ready admin panel

---

## 🚀 Quick Start

### Requirements
- PHP >= 8.1
- MySQL >= 5.7
- Web Server (Apache/Nginx)

### Installation

1. Upload files to your server
2. Create a MySQL database
3. Visit `http://your-domain.com/install`
4. Follow the 4-step wizard
5. Done! Access admin at `/admin`

See [INSTALL.md](INSTALL.md) for detailed instructions.

---

## 📁 Directory Structure

```
mini-cms/
├── app/                    # Application code
│   ├── Http/Controllers/   # Controllers
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic
├── config/                 # Configuration files
├── database/               # Migrations & seeders
├── public/                 # Web root
│   ├── pagebuilder/        # Published static sites
│   └── js/tinymce/         # TinyMCE editor
├── resources/views/        # Blade templates
│   ├── admin/              # Admin panel views
│   ├── installer/          # Install wizard
│   └── site/               # Frontend views
├── routes/web.php          # Route definitions
└── storage/                # Logs, cache, uploads
```

---

## 🔒 Security

- CSRF protection on all forms
- Rate limiting on login/contact
- Safe ZIP extraction (path traversal prevention)
- Honeypot spam protection
- Password hashing with bcrypt

---

## 📄 License

Commercial license. See [LICENSE.md](LICENSE.md) for details.

---

## 📞 Support

For questions and support, contact the developer.

---

Made with ❤️ and Laravel