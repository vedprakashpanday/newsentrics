<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use App\Models\Comment;

class FrontendController extends Controller
{
public function index(Request $request) 
    {
        // 1. Country Set Karein
        $country = $request->country ?? 'India';

        // 2. Normal Home Page Logic (Kyunki Search ab apne dedicated method se handle hoga)
        $query = \App\Models\News::where('country', $country); // Country ke hisaab se filter

        $heroNews = clone $query;
        $heroNews = $heroNews->latest()->first();
        
        $latestNews = clone $query;
        $latestNews = $latestNews->latest()->skip(1)->take(4)->get();
        
        $newsList = clone $query;
        $newsList = $newsList->latest()->skip(5)->paginate(9);

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
    $userCountry = $request->country ?? 'India';

    // Agar AJAX request hai (Load More dabaya gaya hai)
    if ($request->ajax()) {
        $type = $request->type; // 'local' ya 'world'
        $skip = $request->skip;

        $query = \App\Models\News::where('category_id', $category->id);

        if ($type == 'local') {
            $query->where('country', $userCountry);
        } else {
            $query->where('country', '!=', $userCountry);
        }

        $extraNews = $query->latest()->skip($skip)->take(4)->get();
        
        // Naye cards ka HTML generate karke bhejna
        $html = '';
        foreach($extraNews as $news) {
            $html .= view('components.news-card', compact('news'))->render();
        }

        return response()->json([
            'html' => $html,
            'count' => $extraNews->count()
        ]);
    }

    // Normal Page Load Logic
    $localNews = \App\Models\News::where('category_id', $category->id)->where('country', $userCountry)->latest()->take(8)->get();
    $worldNews = \App\Models\News::where('category_id', $category->id)->where('country', '!=', $userCountry)->latest()->take(4)->get();

    return view('category', compact('category', 'localNews', 'worldNews', 'userCountry'));
}


   public function search(Request $request)
    {
        $query = $request->input('q');
        $country = $request->country ?? 'India';

        // 1. Search filter logic
        $searchLogic = function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('keywords', 'LIKE', "%{$query}%")
              ->orWhere('content', 'LIKE', "%{$query}%");
        };

        // 2. MAGIC FIX: Search ke time news ko Hero aur Latest mein todna nahi hai!
        // Saari ki saari matched news ko sidha Grid ($newsList) mein daal do.
        $newsList = \App\Models\News::where('country', $country)
                        ->where($searchLogic)
                        ->orderBy('created_at', 'desc')
                        ->paginate(12); // Search results page par 12 ka grid mast lagega

        // AJAX pagination support
        if ($request->ajax()) {
            $html = view('components.news-list', compact('newsList'))->render();
            return response()->json(['html' => $html, 'hasMore' => $newsList->hasMorePages()]);
        }

        // 3. View ko error se bachane ke liye Hero/Latest ko khali bhej do
        $heroNews = null;
        $latestNews = collect();

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