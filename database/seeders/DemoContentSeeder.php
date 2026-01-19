<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Page;
use App\Models\Media;
use App\Models\Lead;
use Carbon\Carbon;

class DemoContentSeeder extends Seeder
{
    public function run()
    {
        // 1. Setup Admin Author
        $admin = User::first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@local.test',
                'password' => bcrypt('123456'),
            ]);
        }

        // 2. Clear existing demo content (optional, keep users safer)
        // Category::truncate(); Tag::truncate(); Post::truncate(); Lead::truncate();
        // For safety, we'll just append.

        // 3. Create Categories
        $categories = [
            'Strategy' => 'High-level insights for business growth.',
            'Design' => 'Minimalist principles and UI/UX guides.',
            'Engineering' => 'Technical deep dives and architectural patterns.',
            'Marketing' => 'Growth hacking and content strategy.',
            'Culture' => 'Building remote teams and company values.',
        ];

        $catModels = [];
        foreach ($categories as $name => $desc) {
            $catModels[] = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // 4. Create Tags
        $tagNames = ['SaaS', 'Growth', 'Tutorial', 'Minimalism', 'Laravel', 'Frontend', 'Case Study', 'Productivity', 'Remote Work', 'UX'];
        $tagModels = [];
        foreach ($tagNames as $name) {
            $tagModels[] = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // 5. Handle Images (Offline Mode)
        // Expects images in public/demo/posts/*.jpg
        $sourceDir = public_path('demo/posts');
        $destDir = 'uploads/demo';
        
        // Ensure destination exists in storage
        Storage::disk('public')->makeDirectory($destDir);
        
        // Get files
        $demoFiles = [];
        if (File::exists($sourceDir)) {
            $demoFiles = File::files($sourceDir);
        }

        $mediaIds = [];
        
        // Helper to create media
        $createMedia = function($file) use ($destDir) {
            $filename = $file->getFilename();
            $targetPath = $destDir . '/' . $filename;
            
            // Copy file to storage
            Storage::disk('public')->put($targetPath, File::get($file));
            
            return Media::create([
                'path' => $targetPath,
                'original_name' => $filename,
                'mime_type' => 'image/jpeg', // Assumption for demo
                'size' => $file->getSize(),
            ])->id;
        };

        if (count($demoFiles) > 0) {
            foreach ($demoFiles as $file) {
                // Only process jpg/png
                if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                   $mediaIds[] = $createMedia($file);
                }
            }
        }

        // 6. Create Pages
        $pages = [
            'About' => [
                'content' => '<h2>Our Mission</h2><p>We are a small team dedicated to building <strong>minimalist software</strong> that respects your attention. Simple tools for complex problems.</p><h3>The Team</h3><p>Distributed across 5 timezones, we value asynchronous communication and deep work.</p>',
                'status' => 'published'
            ],
            'How it works' => [
                'content' => '<h2>Simple Steps</h2><ul><li><strong>Step 1:</strong> Sign up for an account.</li><li><strong>Step 2:</strong> Configure your workspace.</li><li><strong>Step 3:</strong> Invite your team.</li></ul><p>It is really that simple.</p>',
                'status' => 'published'
            ],
            'Privacy' => [
                'content' => '<p>We do not sell your data. We do not track you across the web. Minimal data collection for maximum privacy.</p>',
                'status' => 'published'
            ]
        ];

        foreach ($pages as $title => $data) {
            Page::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'excerpt' => 'Learn more about ' . $title,
                    'content' => $data['content'],
                    'status' => $data['status'],
                    'published_at' => now(),
                    'author_id' => $admin->id,
                    'meta_title' => $title . ' - PointOne',
                    'meta_description' => strip_tags(Str::limit($data['content'], 150))
                ]
            );
        }

        // 7. Create Posts (Realistic Content)
        $titles = [
            "The Art of Digital Minimalism" => "Explore how reducing digital clutter can skyrocket these productivity metrics.",
            "Why We Switched to Tailwind" => "A deep dive into utility-first CSS and why it scales better for SaaS products.",
            "10 Principles of Calm UI" => "Designing interfaces that respect user attention and reduce anxiety.",
            "Asynchronous Workflows" => "How to manage a remote team without constant meetings.",
            "Laravel Performance Tuning" => "Optimizing Eloquent queries for high-scale applications.",
            "The Future of SaaS Pricing" => "Why usage-based pricing is taking over the subscription economy.",
            "Building Accessible Forms" => "Ensure your application is usable by everyone, regardless of ability.",
            "Typography Matters" => "Choosing the right font stack for long-form reading experiences.",
            "Zero-Downtime Deployments" => "Strategies for deploying code without interrupting user sessions.",
            "Database Indexing 101" => "Understand how B-Trees work and speed up your SQL queries.",
            "Content Strategy for Devs" => "How to write technical articles that actually get read.",
            "Mobile-First Design Patterns" => "Adapting complex tables and dashboards for small screens.",
            "The Monolith is Fine" => "Why you probably don't need microservices just yet.",
            "Clean Code Practices" => "Refactoring legacy codebases with confidence.",
            "SEO for Single Page Apps" => "Solving the indexing challenge with server-side rendering.",
            "State Management Wars" => "Redux, MobX, Context, or just plain old drilling?",
            "API Design Guidelines" => "REST vs GraphQL: Making the right choice for your API.",
            "Handling Webhooks Scale" => "Processing millions of events without losing data.",
            "Dark Mode Implementation" => "Using CSS variables to support system color schemes.",
            "The Psychology of Color" => "How color theory impacts conversion rates in software."
        ];

        $htmlContent = '
            <h2>Introduction</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <h3>Key Takeaways</h3>
            <ul>
                <li>Focus on the essential.</li>
                <li>Remove the unnecessary.</li>
                <li>Iterate quickly and often.</li>
            </ul>
            <blockquote>"Perfection is achieved, not when there is nothing more to add, but when there is nothing left to take away." - Antoine de Saint-Exupéry</blockquote>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
        ';

        $i = 0;
        foreach ($titles as $title => $excerpt) {
            $isPublished = $i < 12; // 12 published
            $status = $isPublished ? 'published' : ($i < 16 ? 'review' : 'draft');
            $date = $isPublished ? Carbon::now()->subDays(rand(1, 30)) : null;
            
            $post = Post::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'content' => $htmlContent,
                    'status' => $status,
                    'published_at' => $date,
                    'author_id' => $admin->id,
                    'featured_image_id' => count($mediaIds) > 0 ? $mediaIds[$i % count($mediaIds)] : null,
                    'meta_title' => $title,
                    'meta_description' => $excerpt
                ]
            );

            // Sync random categories/tags
            $post->categories()->sync($catModels[array_rand($catModels)]->id);
            $post->tags()->sync([
                $tagModels[array_rand($tagModels)]->id, 
                $tagModels[array_rand($tagModels)]->id
            ]);
            
            $i++;
        }

        // 8. Create Leads
        $leadStatuses = ['new', 'handled', 'spam'];
        for ($j = 0; $j < 10; $j++) {
            Lead::create([
                'name' => 'Lead User ' . ($j + 1),
                'email' => "lead{$j}@example.com",
                'phone' => '555-010' . $j,
                'message' => 'I would like to inquire about your enterprise services. Please contact me.',
                'source' => 'contact_form',
                'status' => $leadStatuses[array_rand($leadStatuses)],
            ]);
        }
    }
}
