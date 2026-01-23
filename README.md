# Mini CMS - Laravel Blade Content Management System

A lightweight, minimalist Content Management System built with Laravel and Blade templates. Designed for simplicity and ease of use, featuring a clean admin interface and responsive frontend.

## 🚀 Features

### Core Functionality
- **Posts & Pages Management**: Full CRUD operations with SEO optimization
- **Taxonomies**: Categories and Tags for organizing content
- **Media Library**: Upload, organize in folders, metadata management, and smart resizing
- **User Management**: Role-based access control (Admin/Editor)
- **Lead Management**: Contact form submissions with status tracking

### Analytics & Insights
- **Dashboard**: Comprehensive analytics with Chart.js visualizations
- **View Tracking**: Daily view statistics for posts
- **Activity Logging**: Audit trail for all system actions

### SEO & Performance
- **Dynamic Sitemap**: Auto-generated `/sitemap.xml`
- **Robots.txt**: Configurable robot directives
- **Meta Tags**: Canonical URLs, custom meta titles/descriptions
- **Schema.org**: JSON-LD structured data for better search visibility

### Editor & UI
- **TinyMCE Rich Text Editor**: WordPress-style editing experience
- **Responsive Design**: Mobile-friendly admin and frontend
- **Tailwind CSS**: Clean, minimalist styling via CDN
- **Media Picker Modal**: Reusable component with smart image resizing

### Frontend Features
- **Search Functionality**: Advanced search with keyword highlighting
- **Breadcrumbs**: Navigation aids for better UX
- **Related Posts**: Intelligent content recommendations
- **Contact Form**: Integrated lead generation

## 🛠️ Tech Stack

- **Backend**: Laravel 10+ (PHP 8.1+)
- **Database**: MySQL
- **Frontend**: Blade Templates + Tailwind CSS (CDN)
- **Rich Text Editor**: TinyMCE 6 (GPL License)
- **Charts**: Chart.js
- **Development Environment**: Laragon (Windows)

## 📋 Requirements

- PHP 8.1 or higher
- MySQL 5.7+
- Composer
- Node.js (optional, for development only)
- Laragon or similar local server environment

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/mini-cms.git
   cd mini-cms
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   - Create a MySQL database
   - Update `.env` with database credentials
   - Run migrations and seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Storage link** (for media uploads)
   ```bash
   php artisan storage:link
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 👤 Demo Users

- **Admin**: `admin@local.test` / `123456`
- **Editor**: `editor@local.test` / `123456`

## 📁 Project Structure

```
mini-cms/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── View/Components/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── js/tinymce/
│   └── uploads/
├── resources/
│   ├── views/
│   └── css/
└── routes/
    └── web.php
```

## 🎯 Key Routes

### Frontend
- `/` - Homepage
- `/posts/{slug}` - Individual post view
- `/p/{slug}` - Individual page view
- `/contact` - Contact form
- `/search` - Search results

### Admin Panel (`/admin`)
- `/admin` - Dashboard
- `/admin/posts` - Post management
- `/admin/pages` - Page management
- `/admin/media` - Media library
- `/admin/users` - User management (Admin only)
- `/admin/settings` - Site settings (Admin only)

## 🔧 Configuration

Site-wide settings can be managed through the admin panel at `/admin/settings`. These include:
- Site title and description
- SEO defaults
- Contact information
- Analytics settings

## 📊 Database Schema

Key tables include:
- `users` - User accounts with roles
- `posts` - Blog posts with SEO fields
- `pages` - Static pages
- `media` - File uploads with metadata
- `categories` & `tags` - Taxonomies
- `leads` - Contact form submissions
- `activity_logs` - System audit trail
- `post_view_stats` - Analytics data

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

- Laravel Framework
- TinyMCE Rich Text Editor
- Tailwind CSS
- Chart.js
- All contributors and the Laravel community

---

*Built with ❤️ using Laravel and Blade templates*