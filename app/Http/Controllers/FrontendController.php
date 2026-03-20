<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FrontendController extends Controller
{
    public function index(Request $request) 
    {
        $country = $request->cookie('user_country') ?? $request->country ?? 'India';
        $country = $request->country ?? 'India';
        $query = News::where('country', $country);

        $heroNews = (clone $query)->latest()->first();
        $latestNews = (clone $query)->latest()->skip(1)->take(4)->get();
        $newsList = (clone $query)->latest()->skip(5)->paginate(9);

        // Response ke saath Cookie set kar do (60 mins * 24 * 30 = 30 din ke liye)
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

        $paragraphs = explode("\n\n", $news->content);
        $middleIndex = max(1, floor(count($paragraphs) / 2));
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
            'message' => 'Your comment has been posted successfully!',
            'data' => [
                'name' => $comment->name,
                'comment' => $comment->comment,
                'date' => $comment->created_at->diffForHumans(),
                'initial' => substr($comment->name, 0, 1)
            ]
        ]);
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $userCountry = $request->country ?? 'India';
        $isWorldCategory = (strtolower($category->name) == 'world' || $slug == 'world');

        if ($request->ajax()) {
            $type = $request->type;
            $skip = $request->skip;
            $query = News::where('category_id', $category->id);

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
            $localNews = collect();
            $worldNews = News::where('country', '!=', $userCountry)->latest()->paginate(12);
            return view('category-world', compact('category', 'worldNews', 'userCountry'));
        }

        $localNews = News::where('category_id', $category->id)->where('country', $userCountry)->latest()->take(8)->get();
        $worldNews = News::where('category_id', $category->id)->where('country', '!=', $userCountry)->latest()->take(4)->get();

        return view('category', compact('category', 'localNews', 'worldNews', 'userCountry'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $country = $request->country ?? 'India';

        $newsList = News::where('country', $country)
                        ->where(function($q) use ($query) {
                            $q->where('title', 'LIKE', "%{$query}%")
                              ->orWhere('keywords', 'LIKE', "%{$query}%")
                              ->orWhere('content', 'LIKE', "%{$query}%");
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(12);

        if ($request->ajax()) {
            $html = view('components.news-list', compact('newsList'))->render();
            return response()->json(['html' => $html, 'hasMore' => $newsList->hasMorePages()]);
        }

        $heroNews = null;
        $latestNews = collect();
        return view('home', compact('heroNews', 'latestNews', 'newsList', 'country', 'query'));
    }

    public function sidebarAiWidget(Request $request)
    {
        $country = $request->country ?? 'India';
        $date = now()->format('F j');
        $apiKey = trim(env('GEMINI_API_KEY'));

        if (empty($apiKey)) {
            return response()->json(['history_title' => "On this day in {$country}", 'history_info' => "History event info.", 'quote' => "A quote."]);
        }

        try {
            $prompt = "Provide a significant historical event for {$date} in {$country} and an inspirational quote. Format: JSON only. Schema: {\"history_title\": \"\", \"history_info\": \"\", \"quote\": \"\"}";
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;
            $response = Http::withoutVerifying()->post($url, ['contents' => [['parts' => [['text' => $prompt]]]]]);

            if ($response->successful()) {
                $aiText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $result = json_decode(trim(str_replace(['```json', '```'], '', $aiText)), true);
                return response()->json($result);
            }
        } catch (\Exception $e) {}

        return response()->json(['history_title' => "Today in {$country}", 'history_info' => "History...", 'quote' => "Quote..."]);
    }

    public function contact() { return view('contact'); }

    public function storeContact(Request $request)
    {
        $request->validate(['name' => 'required', 'email' => 'required|email', 'message' => 'required']);
        Contact::create($request->all());
        return back()->with('success', 'Sandesh mil gaya!');
    }
}