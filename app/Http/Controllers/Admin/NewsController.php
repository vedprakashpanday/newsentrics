<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use App\Models\News;
use Illuminate\Support\Str;
use App\Models\Category; 
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    public function trending(Request $request)
{
    $country = $request->input('country', 'IN');
    $url = "https://trends.google.com/trends/trendingsearches/daily/rss?geo=" . $country;

    try {
        // withoutVerifying() localhost par SSL errors ko bypass karta hai
        $response = Http::withoutVerifying()->timeout(15)->get($url);

        if ($response->successful()) {
            $xmlString = $response->body();
            // XML parse karne se pehle check karein
            $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA);
            
            $trends = [];
            if ($xml && isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    $trends[] = [
                        'title' => (string) $item->title,
                        'link' => (string) $item->link,
                    ];
                }
            }
        } else {
            // Agar Google 403 ya kuch aur error de raha ho
            logger("Google Trends Error: " . $response->status());
            $trends = [];
        }
    } catch (\Exception $e) {
        // Asli error kya hai, wo Laravel Log mein dikhega
        logger("Connection Error: " . $e->getMessage());
        $trends = [];
    }

    return view('admin.trending', compact('trends', 'country'));
}

public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'country' => 'required|string',
            'category_id' => 'required|exists:categories,id', // Validation add kiya
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'keywords' => 'nullable|string'
        ]);

        // ... Image upload logic wahi rahega ...

        $slug = \Illuminate\Support\Str::slug($request->title) . '-' . time();

        News::create([
            'category_id' => $request->category_id, // Nayi line add ki
            'title' => $request->title,
            'slug' => $slug,
            'country' => $request->country,
            'image' => $imageName ?? null,
            'content' => $request->content,
            'keywords' => $request->keywords,
        ]);

        return redirect()->back()->with('success', 'News published successfully!');
    }

// Yeh function missing tha, ise ab add karein
  // Form dikhane wala function
public function create() 
{
    $categories = Category::all(); // Saari categories database se nikal li
    return view('admin.create_news', compact('categories')); // Form wale page par bhej di
}

 public function generateAIContent(Request $request) 
{
    try {
        $keywords = $request->keywords;
        $apiKey = trim(env('GEMINI_API_KEY')); 

        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'API Key is empty!']);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    ['parts' => [
                        // YAHAN MAINE PROMPT BADAL DIYA HAI -> "Strictly in professional English"
                        ['text' => "Write a professional and highly engaging news article strictly in English. Heading: [Catchy Title] Content: [Detailed News Body in pure English]. Use these keywords: " . $keywords . ". Return EXACTLY like this -> Heading: Your Title \n\n Content: Your Article"]
                    ]]
                ]
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            return response()->json(['success' => true, 'text' => $aiText]);
        }
        
        $errorData = $response->json();
        $googleError = $errorData['error']['message'] ?? $response->body();

        return response()->json([
            'success' => false, 
            'message' => 'Google Error: ' . $googleError
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
    }
}
}