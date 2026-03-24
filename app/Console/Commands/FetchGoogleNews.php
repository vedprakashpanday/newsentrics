<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Category; // Agar aap future me logic badle to kaam ayega
use Illuminate\Support\Str;

class FetchGoogleNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news for multiple countries from Google RSS directly (No AI)';

    public function handle()
    {
        $this->info('Starting Fast Multi-Country News Fetch...');

        // 1. Countries Array
        $countries = [
            'India'     => 'https://news.google.com/rss?hl=en-IN&gl=IN&ceid=IN:en',
            'USA'       => 'https://news.google.com/rss?hl=en-US&gl=US&ceid=US:en',
            'UK'        => 'https://news.google.com/rss?hl=en-GB&gl=GB&ceid=GB:en',
            'Australia' => 'https://news.google.com/rss?hl=en-AU&gl=AU&ceid=AU:en',
            'Canada'    => 'https://news.google.com/rss?hl=en-CA&gl=CA&ceid=CA:en',
        ];

        foreach ($countries as $countryName => $rssUrl) {
            $this->info("--- Fetching News for {$countryName} ---");

            $feed = new \SimplePie();
            $feed->set_feed_url($rssUrl);
            $feed->enable_cache(false);
            $feed->init();

            // Har country se top 10 news (AI nahi hai toh fast fetch hoga)
            $items = $feed->get_items(0, 10); 

            foreach ($items as $item) {
                $fullTitle = $item->get_title(); 
    
                // 2. Title aur Source ko alag karne ka Regex
                if (preg_match('/(.*)\s\[Source:\s*(.*)\]/i', $fullTitle, $matches)) {
                    $cleanTitle = trim($matches[1]);
                    $source = trim($matches[2]);
                } elseif (preg_match('/(.*)\s\[(.*)\]/i', $fullTitle, $matches)) {
                    $cleanTitle = trim($matches[1]);
                    $source = trim($matches[2]);
                } elseif (str_contains($fullTitle, ' - ')) {
                    $parts = explode(' - ', $fullTitle);
                    $source = array_pop($parts);
                    $cleanTitle = implode(' - ', $parts);
                } else {
                    $cleanTitle = $fullTitle;
                    $source = "Global News";
                }

                // 3. Duplicate Check
                if (News::where('title', $cleanTitle)->exists()) {
                    $this->line("   ⏭️ Already have: " . Str::limit($cleanTitle, 40)); 
                    continue;
                }

                $this->info("Processing: " . Str::limit($cleanTitle, 40) . " [Source: $source]");

                // 4. Direct Database Save (Pending Status)
                try {
                    News::create([
                        'title'       => $cleanTitle,
                        'source'      => $source,
                        'slug'        => Str::slug($cleanTitle) . '-' . time(), // Unique slug guarantee
                        'content'     => $item->get_description(), // Google ki basic description
                        'category_id' => 1, // Default Category ID (General)
                        'country'     => $countryName,
                        'status'      => 0, // Pending mode
                        'image'       => "https://loremflickr.com/800/600/" . urlencode(Str::slug(Str::limit($cleanTitle, 20, ''))),
                        'keywords'    => '',
                    ]);
                    $this->info("✅ Fetched & Saved Successfully!");
                } catch (\Exception $e) {
                    $this->error("❌ DB Error: " . $e->getMessage());
                }
            }
        }
        $this->info('🎉 All Countries Processed Successfully!');
    }
}