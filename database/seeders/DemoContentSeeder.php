<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Page;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\Lead;
use App\Models\ActivityLog;
use Carbon\Carbon;

/**
 * Comprehensive Demo Data Seeder
 * Theme: Career / Training / Consulting Website
 * Language: English
 */
class DemoContentSeeder extends Seeder
{
    private $users = [];
    private $categories = [];
    private $tags = [];
    private $media = [];
    private $posts = [];
    private $pages = [];

    public function run(): void
    {
        $this->command->info('🌱 Starting Demo Content Seeder...');

        $this->seedUsers();
        $this->seedMediaFolders();
        $this->seedMedia();
        $this->seedCategories();
        $this->seedTags();
        $this->seedPages();
        $this->seedPosts();
        $this->seedLeads();
        $this->seedPostViewStats();
        $this->seedActivityLogs();

        $this->command->info('✅ Demo Content Seeder completed!');
    }

    /**
     * 1️⃣ Users: 1 Admin, 2 Active Editors, 1 Inactive Editor
     */
    private function seedUsers(): void
    {
        $this->command->info('  → Seeding Users...');

        $this->users['admin'] = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'admin@local.test',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->users['editor1'] = User::create([
            'name' => 'Michael Chen',
            'email' => 'michael@local.test',
            'password' => Hash::make('123456'),
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->users['editor2'] = User::create([
            'name' => 'Emily Parker',
            'email' => 'emily@local.test',
            'password' => Hash::make('123456'),
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->users['editor_inactive'] = User::create([
            'name' => 'David Wilson',
            'email' => 'david@local.test',
            'password' => Hash::make('123456'),
            'role' => 'editor',
            'is_active' => false,
        ]);
    }

    /**
     * 2️⃣ Media Folders
     */
    private function seedMediaFolders(): void
    {
        $this->command->info('  → Seeding Media Folders...');

        $folders = ['Brand', 'Blog', 'Team', 'Landing'];
        foreach ($folders as $name) {
            MediaFolder::create(['name' => $name]);
        }
    }

    /**
     * 3️⃣ Media Library (placeholder records)
     */
    private function seedMedia(): void
    {
        $this->command->info('  → Seeding Media...');

        $folders = MediaFolder::all()->keyBy('name');
        $admin = $this->users['admin'];

        $mediaItems = [
            // Brand folder
            ['folder' => 'Brand', 'name' => 'logo.png', 'alt' => 'Company Logo', 'w' => 200, 'h' => 60],
            ['folder' => 'Brand', 'name' => 'favicon.ico', 'alt' => 'Favicon', 'w' => 32, 'h' => 32],
            ['folder' => 'Brand', 'name' => 'hero-banner.jpg', 'alt' => 'Hero Banner Image', 'w' => 1920, 'h' => 600],
            
            // Blog folder
            ['folder' => 'Blog', 'name' => 'career-growth.jpg', 'alt' => 'Career Growth Illustration', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'interview-tips.jpg', 'alt' => 'Job Interview Setting', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'resume-writing.jpg', 'alt' => 'Resume and Laptop', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'soft-skills.jpg', 'alt' => 'Team Collaboration', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'remote-work.jpg', 'alt' => 'Working From Home', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'leadership.jpg', 'alt' => 'Leadership Meeting', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'networking.jpg', 'alt' => 'Professional Networking Event', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'skill-development.jpg', 'alt' => 'Learning New Skills', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'japan-career.jpg', 'alt' => 'Tokyo Office Building', 'w' => 1200, 'h' => 630],
            ['folder' => 'Blog', 'name' => 'it-career.jpg', 'alt' => 'Developer at Work', 'w' => 1200, 'h' => 630],
            
            // Team folder
            ['folder' => 'Team', 'name' => 'team-photo.jpg', 'alt' => 'Our Team', 'w' => 1200, 'h' => 800],
            ['folder' => 'Team', 'name' => 'sarah-johnson.jpg', 'alt' => 'Sarah Johnson - CEO', 'w' => 400, 'h' => 400],
            ['folder' => 'Team', 'name' => 'michael-chen.jpg', 'alt' => 'Michael Chen - Career Consultant', 'w' => 400, 'h' => 400],
            ['folder' => 'Team', 'name' => 'emily-parker.jpg', 'alt' => 'Emily Parker - Training Specialist', 'w' => 400, 'h' => 400],
            
            // Landing folder
            ['folder' => 'Landing', 'name' => 'consultation-hero.jpg', 'alt' => 'Career Consultation Service', 'w' => 1920, 'h' => 800],
            ['folder' => 'Landing', 'name' => 'testimonial-bg.jpg', 'alt' => 'Testimonial Background', 'w' => 1920, 'h' => 600],
            ['folder' => 'Landing', 'name' => 'cta-background.jpg', 'alt' => 'Call to Action Background', 'w' => 1920, 'h' => 400],
        ];

        foreach ($mediaItems as $item) {
            $media = Media::create([
                'disk' => 'public',
                'path' => 'uploads/demo/' . $item['name'],
                'original_name' => $item['name'],
                'mime' => Str::endsWith($item['name'], '.png') ? 'image/png' : 'image/jpeg',
                'size' => rand(50000, 500000),
                'uploaded_by' => $admin->id,
                'alt_text' => $item['alt'],
                'caption' => $item['alt'],
                'width' => $item['w'],
                'height' => $item['h'],
                'folder_id' => $folders[$item['folder']]->id ?? null,
            ]);
            $this->media[$item['name']] = $media;
        }
    }

    /**
     * 4️⃣ Categories
     */
    private function seedCategories(): void
    {
        $this->command->info('  → Seeding Categories...');

        $cats = [
            'Career Advice' => 'Tips and strategies for career advancement',
            'Skills Development' => 'Learning new skills for professional growth',
            'Case Studies' => 'Success stories from our clients',
            'Industry News' => 'Latest updates from the job market',
            'Interview Tips' => 'Prepare for your next job interview',
            'Announcements' => 'Company news and updates',
        ];

        foreach ($cats as $name => $desc) {
            $this->categories[$name] = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }

    /**
     * 5️⃣ Tags
     */
    private function seedTags(): void
    {
        $this->command->info('  → Seeding Tags...');

        $tagNames = [
            'Resume', 'CV', 'Interview', 'Salary Negotiation', 'Remote Work',
            'Soft Skills', 'Leadership', 'Japan Career', 'IT Jobs', 'Career Change',
            'Networking', 'LinkedIn', 'Personal Branding', 'Communication', 'Time Management'
        ];

        foreach ($tagNames as $name) {
            $this->tags[$name] = Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }

    /**
     * 6️⃣ Pages (5-7)
     */
    private function seedPages(): void
    {
        $this->command->info('  → Seeding Pages...');

        $admin = $this->users['admin'];

        $pagesData = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'excerpt' => 'Welcome to CareerPath - Your Partner in Professional Growth',
                'content' => '<h2>Welcome to CareerPath</h2>
<p>We are a leading career consulting firm dedicated to helping professionals achieve their career goals. Whether you\'re looking for a new job, planning a career change, or seeking to develop new skills, we\'re here to guide you every step of the way.</p>
<h3>Our Services</h3>
<ul>
<li><strong>Career Consulting</strong> - One-on-one sessions with experienced career advisors</li>
<li><strong>Resume Review</strong> - Professional feedback to make your CV stand out</li>
<li><strong>Interview Coaching</strong> - Practice and preparation for your dream job</li>
<li><strong>Skills Training</strong> - Workshops and courses for professional development</li>
</ul>
<p>Join thousands of professionals who have transformed their careers with CareerPath.</p>',
                'meta_title' => 'CareerPath - Professional Career Consulting & Training',
                'meta_description' => 'CareerPath helps professionals achieve their career goals through expert consulting, resume review, interview coaching, and skills training.',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about',
                'excerpt' => 'Learn about our mission, team, and commitment to your success',
                'content' => '<h2>Our Mission</h2>
<p>At CareerPath, we believe everyone deserves a fulfilling career. Our mission is to empower professionals with the knowledge, skills, and confidence they need to succeed in today\'s competitive job market.</p>
<h3>Our Story</h3>
<p>Founded in 2020, CareerPath started as a small consulting practice with a big vision. Today, we\'ve helped over 5,000 professionals land their dream jobs and advance their careers.</p>
<h3>Our Team</h3>
<p>Our team consists of experienced HR professionals, former recruiters, and career coaches who bring decades of combined experience to every consultation.</p>
<h3>Our Values</h3>
<ul>
<li><strong>Integrity</strong> - We provide honest, actionable advice</li>
<li><strong>Excellence</strong> - We strive for the best outcomes for our clients</li>
<li><strong>Empowerment</strong> - We equip you with tools for long-term success</li>
</ul>',
                'meta_title' => 'About CareerPath - Our Mission and Team',
                'meta_description' => 'Learn about CareerPath\'s mission to empower professionals, our experienced team, and our commitment to your career success.',
            ],
            [
                'title' => 'Services',
                'slug' => 'services',
                'excerpt' => 'Comprehensive career services tailored to your needs',
                'content' => '<h2>Our Services</h2>
<h3>Career Consulting</h3>
<p>Personalized one-on-one sessions with our expert career advisors. We\'ll help you identify your strengths, explore opportunities, and create a strategic plan for your career.</p>
<h3>Resume & CV Review</h3>
<p>Our professional writers will review and enhance your resume to ensure it stands out to recruiters and passes ATS screening.</p>
<h3>Interview Coaching</h3>
<p>Practice makes perfect. Our interview coaches will prepare you with mock interviews, feedback, and strategies for common questions.</p>
<h3>Skills Training</h3>
<p>From soft skills like communication and leadership to technical skills like data analysis, our training programs help you stay competitive.</p>
<h3>Japan Career Program</h3>
<p>Specialized consulting for professionals seeking opportunities in Japan, including visa guidance, cultural training, and job placement support.</p>',
                'meta_title' => 'Career Services - Consulting, Resume Review, Interview Coaching',
                'meta_description' => 'Explore CareerPath\'s comprehensive services including career consulting, resume review, interview coaching, and skills training.',
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'excerpt' => 'Get in touch with our team',
                'content' => '<h2>Contact Us</h2>
<p>We\'d love to hear from you! Whether you have questions about our services or want to schedule a consultation, our team is here to help.</p>
<h3>Office Hours</h3>
<p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 2:00 PM<br>Sunday: Closed</p>
<h3>Location</h3>
<p>123 Career Street, Suite 456<br>Business District<br>New York, NY 10001</p>
<h3>Email</h3>
<p>General Inquiries: info@careerpath.example<br>Support: support@careerpath.example</p>',
                'meta_title' => 'Contact CareerPath - Get in Touch',
                'meta_description' => 'Contact CareerPath for career consulting inquiries. Visit our office or send us a message to schedule your consultation.',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'excerpt' => 'How we collect, use, and protect your information',
                'content' => '<h2>Privacy Policy</h2>
<p>Last updated: January 2026</p>
<h3>Information We Collect</h3>
<p>We collect information you provide directly to us, such as when you fill out a contact form, schedule a consultation, or subscribe to our newsletter.</p>
<h3>How We Use Your Information</h3>
<p>We use your information to provide and improve our services, communicate with you, and send relevant updates about career opportunities.</p>
<h3>Data Security</h3>
<p>We implement industry-standard security measures to protect your personal information from unauthorized access or disclosure.</p>
<h3>Your Rights</h3>
<p>You have the right to access, correct, or delete your personal data at any time. Contact us at privacy@careerpath.example for any data-related requests.</p>',
                'meta_title' => 'Privacy Policy - CareerPath',
                'meta_description' => 'Read CareerPath\'s privacy policy to understand how we collect, use, and protect your personal information.',
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'excerpt' => 'Terms and conditions for using our services',
                'content' => '<h2>Terms of Service</h2>
<p>Last updated: January 2026</p>
<h3>Acceptance of Terms</h3>
<p>By accessing or using CareerPath services, you agree to be bound by these Terms of Service.</p>
<h3>Services</h3>
<p>CareerPath provides career consulting, resume review, interview coaching, and training services. Results may vary based on individual circumstances and effort.</p>
<h3>Payment Terms</h3>
<p>Payment is due at the time of booking unless otherwise agreed. Refund policies vary by service type.</p>
<h3>Limitation of Liability</h3>
<p>CareerPath provides guidance and advice but does not guarantee job placement or specific outcomes. Success depends on multiple factors beyond our control.</p>',
                'meta_title' => 'Terms of Service - CareerPath',
                'meta_description' => 'Review CareerPath\'s terms of service, including payment terms, service descriptions, and limitation of liability.',
            ],
        ];

        foreach ($pagesData as $data) {
            $this->pages[$data['slug']] = Page::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'status' => 'published',
                'published_at' => now()->subDays(rand(30, 90)),
                'author_id' => $admin->id,
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
            ]);
        }
    }

    /**
     * 7️⃣ Posts (15 posts with various statuses)
     */
    private function seedPosts(): void
    {
        $this->command->info('  → Seeding Posts...');

        $postsData = [
            // PUBLISHED POSTS (9 posts - 60%)
            [
                'title' => '10 Resume Mistakes That Are Costing You Job Interviews',
                'excerpt' => 'Avoid these common resume errors that recruiters see every day. Learn how to make your CV stand out from the competition.',
                'status' => 'published',
                'days_ago' => 2,
                'category' => 'Career Advice',
                'tags' => ['Resume', 'CV', 'Interview'],
                'media' => 'resume-writing.jpg',
                'author' => 'admin',
            ],
            [
                'title' => 'How to Ace Your Technical Interview: A Complete Guide',
                'excerpt' => 'From coding challenges to system design questions, here\'s everything you need to know to succeed in technical interviews.',
                'status' => 'published',
                'days_ago' => 5,
                'category' => 'Interview Tips',
                'tags' => ['Interview', 'IT Jobs', 'Communication'],
                'media' => 'interview-tips.jpg',
                'author' => 'editor1',
            ],
            [
                'title' => 'The Ultimate Guide to Salary Negotiation',
                'excerpt' => 'Don\'t leave money on the table. Learn proven strategies to negotiate your salary with confidence.',
                'status' => 'published',
                'days_ago' => 7,
                'category' => 'Career Advice',
                'tags' => ['Salary Negotiation', 'Career Change'],
                'media' => 'career-growth.jpg',
                'author' => 'admin',
            ],
            [
                'title' => 'Remote Work Success: Building a Productive Home Office',
                'excerpt' => 'Tips and strategies for staying productive while working from home in the post-pandemic era.',
                'status' => 'published',
                'days_ago' => 10,
                'category' => 'Skills Development',
                'tags' => ['Remote Work', 'Time Management'],
                'media' => 'remote-work.jpg',
                'author' => 'editor2',
            ],
            [
                'title' => 'Case Study: From Developer to Engineering Manager in 3 Years',
                'excerpt' => 'How one of our clients successfully transitioned from a senior developer role to leading a team of 15 engineers.',
                'status' => 'published',
                'days_ago' => 12,
                'category' => 'Case Studies',
                'tags' => ['Leadership', 'IT Jobs', 'Career Change'],
                'media' => 'leadership.jpg',
                'author' => 'admin',
            ],
            [
                'title' => '5 Soft Skills Every Professional Needs in 2026',
                'excerpt' => 'Technical skills get you in the door, but soft skills help you climb the ladder. Here are the must-have skills for this year.',
                'status' => 'published',
                'days_ago' => 15,
                'category' => 'Skills Development',
                'tags' => ['Soft Skills', 'Communication', 'Leadership'],
                'media' => 'soft-skills.jpg',
                'author' => 'editor1',
            ],
            [
                'title' => 'Working in Japan: A Complete Guide for Foreign Professionals',
                'excerpt' => 'Everything you need to know about finding a job, getting a visa, and thriving in the Japanese workplace.',
                'status' => 'published',
                'days_ago' => 18,
                'category' => 'Career Advice',
                'tags' => ['Japan Career', 'Career Change'],
                'media' => 'japan-career.jpg',
                'author' => 'admin',
            ],
            [
                'title' => 'LinkedIn Profile Optimization: Get Noticed by Recruiters',
                'excerpt' => 'Transform your LinkedIn profile from invisible to irresistible. Tips from recruiters who use the platform daily.',
                'status' => 'published',
                'days_ago' => 21,
                'category' => 'Career Advice',
                'tags' => ['LinkedIn', 'Personal Branding', 'Networking'],
                'media' => 'networking.jpg',
                'author' => 'editor2',
            ],
            [
                'title' => 'The IT Job Market in 2026: Trends and Opportunities',
                'excerpt' => 'Explore the hottest tech roles, emerging skills in demand, and salary trends for IT professionals this year.',
                'status' => 'published',
                'days_ago' => 25,
                'category' => 'Industry News',
                'tags' => ['IT Jobs', 'Career Change'],
                'media' => 'it-career.jpg',
                'author' => 'admin',
            ],

            // DRAFT POSTS (4 posts - 27%)
            [
                'title' => 'How to Build Your Personal Brand on Social Media',
                'excerpt' => 'A comprehensive guide to establishing yourself as a thought leader in your industry.',
                'status' => 'draft',
                'days_ago' => null,
                'category' => 'Skills Development',
                'tags' => ['Personal Branding', 'LinkedIn', 'Networking'],
                'media' => null,
                'author' => 'editor1',
            ],
            [
                'title' => 'Networking Events: How to Make Meaningful Connections',
                'excerpt' => 'Stop collecting business cards and start building relationships that advance your career.',
                'status' => 'draft',
                'days_ago' => null,
                'category' => 'Career Advice',
                'tags' => ['Networking', 'Communication'],
                'media' => null,
                'author' => 'editor2',
            ],
            [
                'title' => 'Career Change at 40: It\'s Never Too Late',
                'excerpt' => 'Inspiring stories and practical advice for professionals considering a mid-career pivot.',
                'status' => 'draft',
                'days_ago' => null,
                'category' => 'Case Studies',
                'tags' => ['Career Change', 'Personal Branding'],
                'media' => null,
                'author' => 'admin',
            ],
            [
                'title' => 'The Future of Work: AI and Human Collaboration',
                'excerpt' => 'How artificial intelligence is changing the workplace and what skills you need to stay relevant.',
                'status' => 'draft',
                'days_ago' => null,
                'category' => 'Industry News',
                'tags' => ['IT Jobs', 'Soft Skills'],
                'media' => null,
                'author' => 'editor1',
            ],

            // REVIEW POSTS (2 posts - 13%)
            [
                'title' => 'Mastering the Art of Public Speaking',
                'excerpt' => 'From boardroom presentations to conference talks, learn to speak with confidence and impact.',
                'status' => 'review',
                'days_ago' => null,
                'category' => 'Skills Development',
                'tags' => ['Communication', 'Soft Skills', 'Leadership'],
                'media' => 'skill-development.jpg',
                'author' => 'editor2',
            ],
            [
                'title' => 'New Partnership Announcement: CareerPath x TechCorp',
                'excerpt' => 'Exciting news! We\'re partnering with TechCorp to bring exclusive job opportunities to our clients.',
                'status' => 'review',
                'days_ago' => null,
                'category' => 'Announcements',
                'tags' => ['IT Jobs'],
                'media' => null,
                'author' => 'admin',
            ],
        ];

        foreach ($postsData as $data) {
            $author = $this->users[$data['author']];
            $category = $this->categories[$data['category']];
            $tagIds = collect($data['tags'])->map(fn($t) => $this->tags[$t]->id)->toArray();

            $post = Post::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'excerpt' => $data['excerpt'],
                'content' => $this->generatePostContent($data['title'], $data['excerpt']),
                'status' => $data['status'],
                'published_at' => $data['days_ago'] ? now()->subDays($data['days_ago']) : null,
                'author_id' => $author->id,
                'featured_image_id' => $data['media'] ? ($this->media[$data['media']]->id ?? null) : null,
                'meta_title' => $data['title'] . ' | CareerPath Blog',
                'meta_description' => $data['excerpt'],
            ]);

            $post->categories()->sync([$category->id]);
            $post->tags()->sync($tagIds);

            $this->posts[] = $post;
        }

        // Create 1 trashed post
        $trashedPost = Post::create([
            'title' => 'Outdated: Job Market Predictions for 2024',
            'slug' => 'job-market-predictions-2024',
            'excerpt' => 'This article is no longer relevant and has been archived.',
            'content' => '<p>This content is outdated.</p>',
            'status' => 'published',
            'published_at' => now()->subYear(),
            'author_id' => $this->users['admin']->id,
            'deleted_at' => now()->subDays(5),
            'meta_title' => 'Outdated Article',
            'meta_description' => 'Archived content.',
        ]);
    }

    /**
     * 8️⃣ Leads (25 leads with various sources and statuses)
     */
    private function seedLeads(): void
    {
        $this->command->info('  → Seeding Leads...');

        $leadsData = [
            // contact_form source (10)
            ['name' => 'John Smith', 'email' => 'john.smith@email.com', 'phone' => '+1-555-0101', 'source' => 'contact_form', 'status' => 'new', 'message' => 'I\'m interested in your career consulting services. Can we schedule a call?', 'days_ago' => 0],
            ['name' => 'Lisa Chen', 'email' => 'lisa.chen@company.com', 'phone' => '+1-555-0102', 'source' => 'contact_form', 'status' => 'handled', 'message' => 'Looking for resume review services for my team of 5 managers.', 'days_ago' => 2],
            ['name' => 'Robert Johnson', 'email' => 'rjohnson@gmail.com', 'phone' => '+1-555-0103', 'source' => 'contact_form', 'status' => 'new', 'message' => 'What are your rates for interview coaching? I have an important interview next week.', 'days_ago' => 1],
            ['name' => 'Emma Wilson', 'email' => 'emma.w@outlook.com', 'phone' => null, 'source' => 'contact_form', 'status' => 'handled', 'message' => 'Interested in your Japan Career Program. I\'m a software engineer looking to relocate.', 'days_ago' => 5],
            ['name' => 'David Brown', 'email' => 'dbrown@techco.io', 'phone' => '+1-555-0105', 'source' => 'contact_form', 'status' => 'spam', 'message' => 'AMAZING OFFER! Click here to win $1000!!!', 'days_ago' => 3],
            ['name' => 'Sarah Miller', 'email' => 'sarah.miller@corp.com', 'phone' => '+1-555-0106', 'source' => 'contact_form', 'status' => 'new', 'message' => 'Our company needs corporate training for 20 employees. Do you offer group rates?', 'days_ago' => 0],
            ['name' => 'James Taylor', 'email' => 'jtaylor@startup.io', 'phone' => '+1-555-0107', 'source' => 'contact_form', 'status' => 'handled', 'message' => 'Completed your LinkedIn optimization course. Very helpful! Now interested in 1-on-1 coaching.', 'days_ago' => 7],
            ['name' => 'Jennifer Garcia', 'email' => 'jgarcia@email.com', 'phone' => null, 'source' => 'contact_form', 'status' => 'new', 'message' => 'I\'m considering a career change from marketing to UX design. Can you help?', 'days_ago' => 1],
            ['name' => 'Michael Lee', 'email' => 'mlee@gmail.com', 'phone' => '+1-555-0109', 'source' => 'contact_form', 'status' => 'new', 'message' => 'Looking for help with salary negotiation for a senior position offer I received.', 'days_ago' => 0],
            ['name' => 'Amanda White', 'email' => 'awhite@company.org', 'phone' => '+1-555-0110', 'source' => 'contact_form', 'status' => 'handled', 'message' => 'Thank you for the consultation last week. Ready to proceed with the full program.', 'days_ago' => 4],

            // homepage_cta source (5)
            ['name' => 'Chris Anderson', 'email' => 'chris.a@email.com', 'phone' => '+1-555-0201', 'source' => 'homepage_cta', 'status' => 'new', 'message' => 'Sign me up for a free consultation!', 'days_ago' => 0],
            ['name' => 'Amy Thompson', 'email' => 'athompson@work.com', 'phone' => null, 'source' => 'homepage_cta', 'status' => 'handled', 'message' => 'Clicked the CTA on homepage. Interested in resume services.', 'days_ago' => 3],
            ['name' => 'Kevin Martinez', 'email' => 'k.martinez@tech.io', 'phone' => '+1-555-0203', 'source' => 'homepage_cta', 'status' => 'new', 'message' => 'I need help transitioning from QA to DevOps.', 'days_ago' => 1],
            ['name' => 'Rachel Clark', 'email' => 'rclark@business.com', 'phone' => '+1-555-0204', 'source' => 'homepage_cta', 'status' => 'new', 'message' => 'Free consultation request - executive coaching needed.', 'days_ago' => 0],
            ['name' => 'Test User', 'email' => 'test@fake.com', 'phone' => null, 'source' => 'homepage_cta', 'status' => 'spam', 'message' => 'asdfasdf test 12345', 'days_ago' => 2],

            // blog_post source (5)  
            ['name' => 'Daniel Kim', 'email' => 'dkim@developer.io', 'phone' => '+1-555-0301', 'source' => 'blog_post:10-resume-mistakes', 'status' => 'new', 'message' => 'Great article on resume mistakes! Can you review mine?', 'days_ago' => 1],
            ['name' => 'Nicole Harris', 'email' => 'nharris@pm.me', 'phone' => null, 'source' => 'blog_post:salary-negotiation', 'status' => 'handled', 'message' => 'Read your salary guide. Need personalized help for my situation.', 'days_ago' => 6],
            ['name' => 'Brian Scott', 'email' => 'bscott@company.com', 'phone' => '+1-555-0303', 'source' => 'blog_post:working-in-japan', 'status' => 'new', 'message' => 'Very interested in the Japan program mentioned in your article.', 'days_ago' => 2],
            ['name' => 'Michelle Adams', 'email' => 'madams@corp.org', 'phone' => '+1-555-0304', 'source' => 'blog_post:soft-skills-2026', 'status' => 'new', 'message' => 'Do you offer soft skills training for teams?', 'days_ago' => 0],
            ['name' => 'Steve Robinson', 'email' => 'srobinson@email.com', 'phone' => null, 'source' => 'blog_post:linkedin-optimization', 'status' => 'handled', 'message' => 'Your LinkedIn tips worked! Got 3 recruiter messages already.', 'days_ago' => 4],

            // pagebuilder source (5)
            ['name' => 'Laura King', 'email' => 'lking@business.io', 'phone' => '+1-555-0401', 'source' => 'pagebuilder:career-consultation', 'status' => 'new', 'message' => 'Submitted via landing page. Need urgent career advice.', 'days_ago' => 0],
            ['name' => 'Mark Wright', 'email' => 'mwright@startup.com', 'phone' => '+1-555-0402', 'source' => 'pagebuilder:career-consultation', 'status' => 'new', 'message' => 'Looking for executive coaching services.', 'days_ago' => 1],
            ['name' => 'Sandra Lopez', 'email' => 'slopez@tech.org', 'phone' => null, 'source' => 'pagebuilder:career-consultation', 'status' => 'handled', 'message' => 'Interested in your premium consultation package.', 'days_ago' => 3],
            ['name' => 'Paul Green', 'email' => 'pgreen@company.com', 'phone' => '+1-555-0404', 'source' => 'pagebuilder:career-consultation', 'status' => 'new', 'message' => 'My company wants to book group sessions for 10 people.', 'days_ago' => 0],
            ['name' => 'Spam Bot', 'email' => 'spam@bot.xyz', 'phone' => null, 'source' => 'pagebuilder:career-consultation', 'status' => 'spam', 'message' => 'Buy cheap watches now!!!', 'days_ago' => 1],
        ];

        foreach ($leadsData as $data) {
            Lead::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'source' => $data['source'],
                'status' => $data['status'],
                'message' => $data['message'],
                'created_at' => now()->subDays($data['days_ago'])->subMinutes(rand(0, 1440)),
                'updated_at' => now()->subDays($data['days_ago']),
            ]);
        }
    }

    /**
     * 9️⃣ Post View Stats (last 30 days)
     */
    private function seedPostViewStats(): void
    {
        $this->command->info('  → Seeding Post View Stats...');

        $publishedPosts = Post::where('status', 'published')->whereNull('deleted_at')->get();

        if ($publishedPosts->isEmpty()) return;

        // Define view patterns: high, medium, low performers
        $highPerformers = $publishedPosts->take(2);
        $midPerformers = $publishedPosts->skip(2)->take(4);
        $lowPerformers = $publishedPosts->skip(6);

        for ($daysAgo = 29; $daysAgo >= 0; $daysAgo--) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');

            // High performers: 50-200 views per day
            foreach ($highPerformers as $post) {
                DB::table('post_view_stats')->insert([
                    'post_id' => $post->id,
                    'date' => $date,
                    'views' => rand(50, 200) + ($daysAgo < 7 ? rand(20, 50) : 0), // Recent boost
                ]);
            }

            // Mid performers: 10-50 views per day
            foreach ($midPerformers as $post) {
                DB::table('post_view_stats')->insert([
                    'post_id' => $post->id,
                    'date' => $date,
                    'views' => rand(10, 50),
                ]);
            }

            // Low performers: 0-15 views per day
            foreach ($lowPerformers as $post) {
                if (rand(0, 100) > 30) { // Not every day has views
                    DB::table('post_view_stats')->insert([
                        'post_id' => $post->id,
                        'date' => $date,
                        'views' => rand(0, 15),
                    ]);
                }
            }
        }
    }

    /**
     * 🔟 Activity Logs (storytelling audit trail)
     */
    private function seedActivityLogs(): void
    {
        $this->command->info('  → Seeding Activity Logs...');

        $logs = [];
        $now = now();

        // User Creation Logs
        foreach ($this->users as $key => $user) {
            $logs[] = [
                'user_id' => $this->users['admin']->id,
                'type' => 'user.created',
                'subject_id' => $user->id,
                'subject_type' => User::class,
                'message' => "Created user '{$user->name}'",
                'meta' => json_encode(['role' => $user->role]),
                'created_at' => $now->copy()->subDays(60)->addHours(rand(0, 23)),
            ];
        }

        // Page Creation Logs
        foreach ($this->pages as $slug => $page) {
            $logs[] = [
                'user_id' => $this->users['admin']->id,
                'type' => 'page.created',
                'subject_id' => $page->id,
                'subject_type' => Page::class,
                'message' => "Created page '{$page->title}'",
                'meta' => json_encode(['slug' => $page->slug]),
                'created_at' => $now->copy()->subDays(55)->addHours(rand(0, 23)),
            ];
        }

        // Post Creation & Update Logs
        foreach ($this->posts as $post) {
            $logs[] = [
                'user_id' => $post->author_id,
                'type' => 'post.created',
                'subject_id' => $post->id,
                'subject_type' => Post::class,
                'message' => "Created post '{$post->title}'",
                'meta' => json_encode(['status' => 'draft']),
                'created_at' => $post->created_at->copy()->subHours(rand(1, 12)),
            ];

            if ($post->status === 'published') {
                $logs[] = [
                    'user_id' => $this->users['admin']->id,
                    'type' => 'post.published',
                    'subject_id' => $post->id,
                    'subject_type' => Post::class,
                    'message' => "Published post '{$post->title}'",
                    'meta' => json_encode(['previous_status' => 'review']),
                    'created_at' => $post->published_at ?? $post->created_at,
                ];
            }
        }

        // Media Upload Logs
        $mediaCount = 0;
        foreach ($this->media as $name => $media) {
            if ($mediaCount++ > 10) break; // Limit to 10 for brevity
            $logs[] = [
                'user_id' => $this->users['admin']->id,
                'type' => 'media.uploaded',
                'subject_id' => $media->id,
                'subject_type' => Media::class,
                'message' => "Uploaded media '{$media->original_name}'",
                'meta' => json_encode(['folder' => $media->folder?->name]),
                'created_at' => $now->copy()->subDays(rand(30, 50)),
            ];
        }

        // Lead Creation Logs
        $leads = Lead::take(15)->get();
        foreach ($leads as $lead) {
            $logs[] = [
                'user_id' => null, // Guest
                'type' => 'lead.created',
                'subject_id' => $lead->id,
                'subject_type' => Lead::class,
                'message' => "New lead from {$lead->source}",
                'meta' => json_encode([
                    'ip' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'source' => $lead->source,
                ]),
                'created_at' => $lead->created_at,
            ];
        }

        // Settings Update Log
        $logs[] = [
            'user_id' => $this->users['admin']->id,
            'type' => 'settings.updated',
            'subject_id' => null,
            'subject_type' => null,
            'message' => 'Updated site settings',
            'meta' => json_encode(['fields' => ['site_name', 'site_description']]),
            'created_at' => $now->copy()->subDays(45),
        ];

        // Bulk insert
        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }

    /**
     * Generate realistic post content
     */
    private function generatePostContent(string $title, string $excerpt): string
    {
        return "<h2>Introduction</h2>
<p>{$excerpt}</p>

<h3>Why This Matters</h3>
<p>In today's competitive job market, staying ahead requires continuous learning and adaptation. Whether you're a fresh graduate or an experienced professional, the principles we'll discuss apply to everyone.</p>

<h3>Key Takeaways</h3>
<ul>
<li><strong>Be proactive</strong> - Don't wait for opportunities to come to you</li>
<li><strong>Invest in yourself</strong> - Continuous learning is non-negotiable</li>
<li><strong>Build your network</strong> - Relationships open doors</li>
<li><strong>Stay adaptable</strong> - The market changes, and so should you</li>
</ul>

<h3>Practical Steps</h3>
<p>Here's how you can apply these insights to your career:</p>
<ol>
<li>Assess your current skills and identify gaps</li>
<li>Set specific, measurable career goals</li>
<li>Create a learning plan with deadlines</li>
<li>Seek feedback from mentors and peers</li>
<li>Track your progress and adjust as needed</li>
</ol>

<blockquote>\"The only way to do great work is to love what you do. If you haven't found it yet, keep looking.\" - Steve Jobs</blockquote>

<h3>Conclusion</h3>
<p>Your career is a marathon, not a sprint. By applying the strategies outlined in this article, you'll be well-positioned for long-term success. Remember, every small step counts.</p>

<p>Ready to take the next step? <a href=\"/contact\">Contact us</a> for a personalized career consultation.</p>";
    }
}
