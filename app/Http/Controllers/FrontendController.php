<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use App\Models\Comment;

class FrontendController extends Controller
{
  public function index(Request $request)
    {
        $country = $request->country ?? 'India';

        // 1. Center Column: Sabse latest (Hero) News
        $heroNews = \App\Models\News::where('country', $country)
                        ->orderBy('created_at', 'desc')
                        ->first();

        // 2. Left Column: Hero news ko chhod kar agli 5 taaza khabrein
        $latestNews = \App\Models\News::where('country', $country)
                        ->where('id', '!=', $heroNews ? $heroNews->id : 0)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();

        // In 6 news (1 hero + 5 latest) ki IDs nikal lo taaki niche list me ye repeat na hon
        $excludeIds = $latestNews->pluck('id')->toArray();
        if ($heroNews) {
            $excludeIds[] = $heroNews->id;
        }

        // 3. Bottom Grid: Baaki saari news chunking/pagination ke sath
        $newsList = \App\Models\News::where('country', $country)
                        ->whereNotIn('id', $excludeIds)
                        ->orderBy('created_at', 'desc')
                        ->paginate(6);

        if ($request->ajax()) {
            $html = view('components.news-list', compact('newsList'))->render();
            return response()->json([
                'html' => $html,
                'hasMore' => $newsList->hasMorePages()
            ]);
        }

        return view('home', compact('heroNews', 'latestNews', 'newsList', 'country'));
    }

   public function show($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        $news->increment('view_count');

        // Sidebar ke liye Related News (4 items)
        $relatedNews = News::where('country', $news->country)
                           ->where('id', '!=', $news->id)
                           ->orderBy('created_at', 'desc')
                           ->take(4)
                           ->get();

        // Next aur Previous Article logic
        $previous = News::where('id', '<', $news->id)->orderBy('id', 'desc')->first();
        $next = News::where('id', '>', $news->id)->orderBy('id', 'asc')->first();

        // Content ko 2 hisson mein todna (Taaki beech mein Ad laga sakein)
        $paragraphs = explode("\n\n", $news->content);
        $middleIndex = max(1, floor(count($paragraphs) / 2));
        $contentPart1 = implode("\n\n", array_slice($paragraphs, 0, $middleIndex));
        $contentPart2 = implode("\n\n", array_slice($paragraphs, $middleIndex));

        return view('single', compact('news', 'relatedNews', 'previous', 'next', 'contentPart1', 'contentPart2'));
    }

    // Naya function comment save karne ke liye
    public function storeComment(Request $request, $news_id)
    {
        // 1. Data Validation (Koi khali form submit na kar de)
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'comment' => 'required|string|max:1000'
        ]);

        // 2. Database mein Save karna
        $comment = Comment::create([
            'news_id' => $news_id,
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment
        ]);

        // 3. JSON Response bhejna (Kyunki hum AJAX use karenge)
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
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();
        $country = $request->country ?? 'India';

        // Us category ki sabse latest (Hero) news
        $heroNews = \App\Models\News::where('category_id', $category->id)
                        ->where('country', $country)
                        ->orderBy('created_at', 'desc')
                        ->first();

        // Us category ki agli 5 news
        $latestNews = \App\Models\News::where('category_id', $category->id)
                        ->where('country', $country)
                        ->where('id', '!=', $heroNews ? $heroNews->id : 0)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();

        $excludeIds = $latestNews->pluck('id')->toArray();
        if ($heroNews) {
            $excludeIds[] = $heroNews->id;
        }

        // Us category ki baaki news (Grid ke liye)
        $newsList = \App\Models\News::where('category_id', $category->id)
                        ->where('country', $country)
                        ->whereNotIn('id', $excludeIds)
                        ->orderBy('created_at', 'desc')
                        ->paginate(6);

        if ($request->ajax()) {
            $html = view('components.news-list', compact('newsList'))->render();
            return response()->json(['html' => $html, 'hasMore' => $newsList->hasMorePages()]);
        }

        return view('home', compact('heroNews', 'latestNews', 'newsList', 'country', 'category'));
    }

   public function search(Request $request)
    {
        $query = $request->input('q');
        $country = $request->country ?? 'India';

        // Search ka logic ek jagah likh liya taaki baar-baar na likhna pade
        $searchLogic = function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('keywords', 'LIKE', "%{$query}%")
              ->orWhere('content', 'LIKE', "%{$query}%");
        };

        // Search ki gayi sabse top news
        $heroNews = \App\Models\News::where('country', $country)
                        ->where($searchLogic)
                        ->orderBy('created_at', 'desc')
                        ->first();

        // Search ki gayi agli 5 news
        $latestNews = \App\Models\News::where('country', $country)
                        ->where($searchLogic)
                        ->where('id', '!=', $heroNews ? $heroNews->id : 0)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();

        $excludeIds = $latestNews->pluck('id')->toArray();
        if ($heroNews) {
            $excludeIds[] = $heroNews->id;
        }

        // Search ki gayi baaki news
        $newsList = \App\Models\News::where('country', $country)
                        ->where($searchLogic)
                        ->whereNotIn('id', $excludeIds)
                        ->orderBy('created_at', 'desc')
                        ->paginate(6);

        if ($request->ajax()) {
            $html = view('components.news-list', compact('newsList'))->render();
            return response()->json(['html' => $html, 'hasMore' => $newsList->hasMorePages()]);
        }

        return view('home', compact('heroNews', 'latestNews', 'newsList', 'country', 'query'));
    }

    public function sidebarAiWidget(Request $request)
    {
        $country = $request->country ?? 'India';
        $date = now()->format('F j'); // Aaj ki date (e.g., "March 18")
        $apiKey = trim(env('GEMINI_API_KEY'));

        // Agar API key nahi hai, toh default message bhej do
        if (empty($apiKey)) {
            return response()->json([
                'history_title' => "On this day in {$country}",
                'history_info' => "Many great historical events took place on {$date} shaping the future of {$country}.",
                'quote' => "\"The best way to predict the future is to create it.\""
            ]);
        }

        try {
            // Gemini API ka Prompt - RAW JSON mangwa rahe hain
            $prompt = "You are an AI assistant. Today is {$date}. Provide a significant historical event that happened on this date related to {$country}. Also provide a short inspirational quote. Return STRICTLY raw JSON format without any markdown backticks. Schema: {\"history_title\": \"Short Title\", \"history_info\": \"2 lines of info\", \"quote\": \"The quote\"}";

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Clean the text in case Gemini still sends markdown
                $cleanJson = str_replace(['```json', '```'], '', $aiText);
                $result = json_decode(trim($cleanJson), true);

                if($result) {
                    return response()->json($result);
                }
            }
        } catch (\Exception $e) {
            // Error aane par page kharab na ho
        }

        return response()->json([
            'history_title' => "Today in {$country}",
            'history_info' => "History is being made every single day.",
            'quote' => "\"Stay positive, work hard, make it happen.\""
        ]);
    }
}