<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run()
    {
        // 1. Check Categories (Agar nahi hai toh khud bana dega)
        $categories = Category::all();
        if ($categories->count() == 0) {
            echo "\n⚠️ Categories nahi mili! Pehle Categories bana raha hoon...\n";
            $cats = ['Politics', 'Technology', 'Sports', 'Entertainment', 'Business'];
            foreach($cats as $cat) {
                Category::create(['name' => $cat, 'slug' => Str::slug($cat)]);
            }
            $categories = Category::all(); // Wapas fetch kar liya
        }

        echo "⏳ 125 Dummy News generate ho rahi hain, 2 second wait karein...\n";

        $faker = Faker::create();
        $countries = ['India', 'USA', 'UK', 'Australia', 'Canada'];
        
        // 2. Main Loop
        foreach ($countries as $country) {
            foreach ($categories as $category) {
                
                // Har combination ke liye 5 news
                for ($i = 0; $i < 5; $i++) {
                    $title = rtrim($faker->sentence(rand(6, 10)), '.');
                    
                    News::create([
                        'category_id' => $category->id,
                        'title'       => $title,
                        'slug'        => Str::slug($title) . '-' . uniqid(),
                        'country'     => $country,
                        'content'     => "This is a detailed breaking news report from " . $country . " regarding " . $category->name . ".\n\n" . $faker->paragraphs(4, true) . "\n\nStay tuned for more live updates.",
                        'keywords'    => implode(', ', $faker->words(4)),
                        'view_count'  => rand(10, 500)
                    ]);
                }
                
            }
        }

        // 3. Guarantee Print Message
        echo "\n🚀 BINGO! Badhai ho! 125 Dummy News successfully seed ho gayi hain!\n";
    }
}