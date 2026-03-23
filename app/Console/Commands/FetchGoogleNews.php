<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchGoogleNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news for multiple countries and summarize using Gemini';

    public function handle()
    {
        // 1. Countries Array
        $countries = [
            'India' => 'https://news.google.com/rss?hl=en-IN&gl=IN&ceid=IN:en',
            'USA'   => 'https://news.google.com/rss?hl=en-US&gl=US&ceid=US:en',
            'UK'    => 'https://news.google.com/rss?hl=en-GB&gl=GB&ceid=GB:en',
            'Australia' => 'https://news.google.com/rss?hl=en-AU&gl=AU&ceid=AU:en',
            'Canada' => 'https://news.google.com/rss?hl=en-CA&gl=CA&ceid=CA:en',
        ];

        foreach ($countries as $countryName => $rssUrl) {
            $this->info("--- Fetching News for {$countryName} ---");

            $feed = new \SimplePie();
            $feed->set_feed_url($rssUrl);
            $feed->enable_cache(false);
            $feed->init();

            // Har country se sirf top 3 news lete hain (Taaki API limit hit na ho)
            $items = $feed->get_items(0, 3); 

            foreach ($items as $item) {
                $fullTitle = $item->get_title(); // Example: "Apple launches new iPhone - NDTV"
    
    // Title aur Source ko alag karne ka jugad
    // 1. Regex se [Source: ...] ya simple [...] nikaalna
    // Ye pattern dhoondega: space + square brackets ke andar ka maal
    if (preg_match('/(.*)\s\[Source:\s*(.*)\]/i', $fullTitle, $matches)) {
        // Agar [Source: The Guardian] format hai
        $cleanTitle = trim($matches[1]);
        $source = trim($matches[2]);
    } elseif (preg_match('/(.*)\s\[(.*)\]/i', $fullTitle, $matches)) {
        // Agar sirf [The Guardian] format hai
        $cleanTitle = trim($matches[1]);
        $source = trim($matches[2]);
    } elseif (str_contains($fullTitle, ' - ')) {
        // Purana hyphen wala fallback
        $parts = explode(' - ', $fullTitle);
        $source = array_pop($parts);
        $cleanTitle = implode(' - ', $parts);
    } else {
        // Agar kuch na mile toh
        $cleanTitle = $fullTitle;
        $source = "Global News";
    }
   if (News::where('title', $cleanTitle)->exists()) {
        // Line change: Taki pata chale ki skip ho raha hai
        $this->line("   ⏭️ Already have: " . Str::limit($cleanTitle, 40)); 
        continue;
    }

    $this->info("Processing: " . $cleanTitle . " [Source: $source]");

                $aiData = $this->getAiSummary($cleanTitle, $item->get_description());

                if ($aiData && isset($aiData['content'])) {
                    try {
                        // 2. Image Logic: Keywords ke base par random image (Source: Unsplash)
                       $keyword = Str::of($aiData['keywords'])->explode(',')->first() ?? 'news';
                        $imageUrl = "https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=800&q=60&sig=" . rand(1, 999);
                        
                        // Agar aapko keyword base image chahiye toh ye use karein:
                        $imageUrl = "https://loremflickr.com/800/600/" . urlencode($keyword);

                        
                        News::create([
        'title' => $cleanTitle, // Ab sirf headline jayegi
        'source' => $source,    // Alag se source jayega
        'slug' => Str::slug($cleanTitle),
        'content' => $aiData['content'],
        'category_id' => $aiData['category_id'] ?? 1,
        'country' => $countryName,
        'image' => $imageUrl,
        'status' => 1,
        'keywords' => $aiData['keywords']
    ]);
                        $this->info("✅ Saved {$countryName} News!");
                    } catch (\Exception $e) {
                        $this->error("❌ DB ERROR: " . $e->getMessage());
                    }
                }
            }
        }
        $this->info('All Countries Processed!');
    }

    private function getAiSummary($title, $description)
    {
        $apiKey = env('GEMINI_API_KEY');
       $prompt = "You are a senior news editor for 'Newsentric'. 
Rewrite this news: '$title' based on this context: '$description'.

Please follow this structure:
1. Headline: A catchy and professional headline.
2. Introduction: Start with a strong hook (approx 40 words).
3. Main Body: Detailed explanation of the event, its impact, and background (approx 150 words).
4. Conclusion: Future outlook or a closing statement (approx 50 words).

IMPORTANT:
- Total length should be around 450-500 words.
- Use professional journalistic tone.
- Return ONLY a valid JSON object.
- Schema: {\"content\": \"...HTML formatted news content...\", \"category\": \"...\", \"keywords\": \"...\"}";

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent?key=" . $apiKey;
            $response = Http::post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                preg_match('/\{.*\}/s', $rawText, $matches);
                $data = json_decode($matches[0] ?? $rawText, true);

                if ($data) {
                    $category = Category::where('name', 'LIKE', "%" . ($data['category'] ?? 'General') . "%")->first();
                    $data['category_id'] = $category ? $category->id : 1;
                    return $data;
                }
            }
        } catch (\Exception $e) { return null; }
        return null;
    }
}