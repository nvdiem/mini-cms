<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email','admin@local.test')->first();
        if(!$user) return;

        $cats = ['Tech','News','Guides','Updates'];
        foreach($cats as $name){
            Category::firstOrCreate(['slug'=>Str::slug($name)], ['name'=>$name]);
        }

        if(Post::count() >= 8) return;

        for($i=1; $i<=8; $i++){
            $title = "Sample post {$i}";
            $post = Post::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => 'Short excerpt for demo.',
                'content' => "Demo content for {$title}.",
                'status' => $i % 3 === 0 ? 'published' : 'draft',
                'author_id' => $user->id,
            ]);

            $post->categories()->sync(Category::inRandomOrder()->limit(rand(1,2))->pluck('id')->all());
        }
    }
}
