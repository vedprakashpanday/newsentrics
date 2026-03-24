<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class FrontendController extends Controller
{
    public function index(Request $request) 
    {
        // Cookie se country uthao, warna request se, warna default India
        $country = $request->cookie('user_country') ?? $request->country ?? 'India';
        
        // 🟢 NAYA BADLAV: Sirf wahi news laao jinka status 1 (Published) ho
        $query = News::where('country', $country)->where('status', 1);

        // Cloning the query to avoid filter conflicts
        $heroNews = (clone $query)->latest()->first();
        $latestNews = (clone $query)->latest()->skip(1)->take(4)->get();
        $newsList = (clone $query)->latest()->skip(5)->paginate(9);

        return response(view('home', compact('heroNews', 'latestNews', 'newsList', 'country')))
                ->cookie('user_country', $country, 43200);
    }

    public function show($slug)
    {
        $news = News::with(['comments' => function($query) {
            $query->where('status', 1)->latest(); 
        }])->where('slug', $slug)->firstOrFail();

        $news->increment('view_count');

        $relatedNews = News::where('country', $news->country)
                            ->where('id', '!=', $news->id)
                            ->orderBy('view_count', 'desc')
                            ->take(4)
                            ->get();

        $previous = News::where('id', '<', $news->id)->orderBy('id', 'desc')->first();
        $next = News::where('id', '>', $news->id)->orderBy('id', 'asc')->first();

        // Content split for ads or special formatting
        $paragraphs = explode("\n\n", $news->content);
        $count = count($paragraphs);
        $middleIndex = $count > 1 ? floor($count / 2) : 1;
        
        $contentPart1 = implode("\n\n", array_slice($paragraphs, 0, $middleIndex));
        $contentPart2 = implode("\n\n", array_slice($paragraphs, $middleIndex));

        return view('single', compact('news', 'relatedNews', 'previous', 'next', 'contentPart1', 'contentPart2'));
    }

    public function storeComment(Request $request, $news_id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'comment' => 'required|string|max:1000'
        ]);

        $comment = Comment::create([
            'news_id' => $news_id,
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment posted!',
            'data' => [
                'name' => $comment->name,
                'comment' => $comment->comment,
                'date' => $comment->created_at->diffForHumans(),
                'initial' => strtoupper(substr($comment->name, 0, 1))
            ]
        ]);
    }

// AJAX Sidebar Logic - Optimized for Real-time History & Performance
    public function getSidebarAi(Request $request)
{
    $country = $request->country ?? 'India';
    $wiki_date = now()->format('m/d'); // Format: 03/23
    
    // Default values (Just in case)
    $ai_insights = [
        'history_title' => "Today in History",
        'history_info' => "On this day, significant events shaped our world.",
        'quote' => "The best way to predict the future is to create it."
    ];

    try {
        // 1. Wikipedia API se 'On This Day' data fetch karein
      $wiki_res = Http::withHeaders([
    'User-Agent' => 'Newsentric/1.0 (https://newsentric.com; contact@newsentric.com)'
])->get("https://en.wikipedia.org/api/rest_v1/feed/onthisday/events/{$wiki_date}");
        
        if ($wiki_res->successful() && isset($wiki_res->json()['events'])) {
            $events = $wiki_res->json()['events'];
            
            // Aaj ke din ka koi bhi ek random event uthao
            $event = $events[array_rand($events)]; 
            
            $ai_insights['history_title'] = "Year " . $event['year'] . " - History";
            $ai_insights['history_info'] = \Illuminate\Support\Str::limit($event['text'], 160);
        }
    } catch (\Exception $e) {
        Log::error("Wikipedia API Error: " . $e->getMessage());
    }

    // 2. Ek badhiya Quote Array (Taki quote hamesha real aur sahi dikhe)
    $quotes = [
        "Believe you can and you're halfway there.",
        "The only way to do great work is to love what you do.",
        "Arise, awake, and stop not until the goal is reached.",
        "Your time is limited, so don't waste it living someone else's life.",
        "Everything you've ever wanted is on the other side of fear.",
        "Success is not in what you have, but who you are."
    ];
    $ai_insights['quote'] = $quotes[array_rand($quotes)];

    return view('partials.sidebar_items', compact('ai_insights'))->render();
}

    public function contact() { return view('contact'); }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'message' => 'required|max:2000'
        ]);

        Contact::create($request->all());
        return back()->with('success', 'Thank you! Your message has been received.');
    }

public function category(Request $request, $slug)
{
    // 1. Slug se category dhundo
    $category = \App\Models\Category::where('slug', $slug)->firstOrFail();
    
    // 2. User ki country set karo
    $userCountry = $request->cookie('user_country') ?? $request->country ?? 'India';

    // 3. World category check logic
    $isWorldCategory = (strtolower($category->name) == 'world' || $slug == 'world');

    if ($request->ajax()) {
        $type = $request->type;
        $skip = $request->skip;
        
        // 🟢 NAYA: AJAX request mein sirf published news (status 1)
        $query = News::where('category_id', $category->id)->where('status', 1);

        if ($type == 'local') {
            $query->where('country', $userCountry);
        } else {
            $query->where('country', '!=', $userCountry);
        }

        $extraNews = $query->latest()->skip($skip)->take(4)->get();
        $html = '';
        foreach($extraNews as $news) {
            $html .= view('components.news-card', compact('news'))->render();
        }
        return response()->json(['html' => $html, 'count' => $extraNews->count()]);
    }

    if ($isWorldCategory) {
        // 🟢 NAYA: World category page par sirf published news
        $worldNews = News::where('country', '!=', $userCountry)
                        ->where('status', 1)
                        ->latest()
                        ->paginate(12);
        return view('category-world', compact('category', 'worldNews', 'userCountry'));
    }

    // 🟢 NAYA: Normal category page par Local aur World dono mein sirf published news
    $localNews = News::where('category_id', $category->id)
                    ->where('country', $userCountry)
                    ->where('status', 1)
                    ->latest()
                    ->take(8)
                    ->get();
                    
    $worldNews = News::where('category_id', $category->id)
                    ->where('country', '!=', $userCountry)
                    ->where('status', 1)
                    ->latest()
                    ->take(4)
                    ->get();

    return view('category', compact('category', 'localNews', 'worldNews', 'userCountry'));
}
}