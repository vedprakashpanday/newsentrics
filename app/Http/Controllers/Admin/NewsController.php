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
            'category_id' => 'required|exists:categories,id',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Mimes define kar diye
            'keywords' => 'nullable|string'
        ]);

        $imageName = null;

        // --- THE WEBP CONVERSION MAGIC ---
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Naya naam .webp extension ke sath
            $imageName = time() . '-' . uniqid() . '.webp'; 
            $destinationPath = public_path('uploads/news');

            // Agar folder nahi hai toh bana do
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Image ko memory mein load karo (kisi bhi format se)
            $img = imagecreatefromstring(file_get_contents($image->getRealPath()));

            // PNG images ki transparency (background) bachane ke liye
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);

            // Image ko WebP format mein save karo (80 quality best hoti hai web ke liye)
            imagewebp($img, $destinationPath . '/' . $imageName, 80);

            // Memory clear karo
            imagedestroy($img);
        }
        // ---------------------------------

        // SEO Friendly Slug Generate
        $slug = \Illuminate\Support\Str::slug($request->title) . '-' . time();

        // Database mein save
        News::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => $slug,
            'country' => $request->country,
            'image' => $imageName, // Yahan ab convert hui WebP image ka naam jayega
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
            // Frontend se aane wala saara data catch karein
            $roughTitle = $request->title ?? '';
            $roughContent = $request->content ?? '';
            $keywords = $request->keywords ?? '';
            $country = $request->country ?? 'Global';
            $category = $request->category ?? 'General News';

            $apiKey = trim(env('GEMINI_API_KEY')); 

            if (empty($apiKey)) {
                return response()->json(['success' => false, 'message' => 'API Key is missing in .env file!']);
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

            // AI ke liye ek strict "System Prompt" banayein
            $prompt = "Act as an expert, professional news editor and journalist. I will provide you with rough news data, keywords, a target country, and a category. Your job is to rewrite this into a highly engaging, SEO-optimized, and professional news article strictly in English.

            Target Audience Country: {$country}
            News Category: {$category}
            Rough Title/Idea: {$roughTitle}
            Rough Content/Snippet: {$roughContent}
            Keywords: {$keywords}

            Output STRICTLY a valid JSON object with EXACTLY these three keys:
            - \"title\": A catchy, professional, SEO-friendly headline (Max 100 characters).
            - \"content\": The detailed, well-structured news article body. Write at least 3-4 professional paragraphs. Use HTML tags like <p>, <br>, or <strong> if necessary for formatting, but keep it clean.
            - \"keywords\": A comma-separated list of the 5-8 best SEO keywords based on the article.";

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    // MAGIC TRICK: AI ko bol rahe hain ki strictly JSON return kare, koi markdown (```json) nahi
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiJsonResponseString = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                
                // Gemini ne jo JSON string di hai, use PHP Array mein convert karo
                $parsedData = json_decode($aiJsonResponseString, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Agar JSON sahi se parse ho gaya, toh success true add karke frontend ko bhej do
                    return response()->json(array_merge(['success' => true], $parsedData));
                } else {
                    return response()->json(['success' => false, 'message' => 'AI returned invalid JSON format.']);
                }
            }
            
            $errorData = $response->json();
            $googleError = $errorData['error']['message'] ?? $response->body();

            return response()->json([
                'success' => false, 
                'message' => 'Google API Error: ' . $googleError
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }

public function index()
{
    $allNews = \App\Models\News::with('category')->latest()->get();
    return view('admin.manage_news', compact('allNews'));
}

public function edit($id)
    {
        // Pehle purani news dhoondho aur categories nikalo
        $news = \App\Models\News::findOrFail($id);
        $categories = \App\Models\Category::all();

        // Edit form wala view return karo
        return view('admin.edit', compact('news', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $news = \App\Models\News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'country' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'keywords' => 'nullable|string'
        ]);

        // Default purani image ka naam rakho
        $imageName = $news->image; 

        // Agar user ne nayi image upload ki hai
        if ($request->hasFile('image')) {
            
            // 🔴 Sabse Pehle: Purani image ko server se HATAO (Taaki storage bache)
            if ($news->image && file_exists(public_path('uploads/news/' . $news->image))) {
                unlink(public_path('uploads/news/' . $news->image));
            }

            // 🟢 Nayi image ko .webp mein convert karke save karo
            $image = $request->file('image');
            $imageName = time() . '-' . uniqid() . '.webp'; 
            $destinationPath = public_path('uploads/news');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $img = imagecreatefromstring(file_get_contents($image->getRealPath()));
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
            imagewebp($img, $destinationPath . '/' . $imageName, 80);
            imagedestroy($img);
        }

        // Database Update karo
        $news->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            // Note: Hum 'slug' update nahi kar rahe taaki SEO aur purane links break na hon
            'country' => $request->country,
            'image' => $imageName, 
            'content' => $request->content,
            'keywords' => $request->keywords,
        ]);

        return redirect()->route('news.manage')->with('success', 'News updated successfully!');
    }

    public function destroy($id)
    {
        $news = \App\Models\News::findOrFail($id);

        // 🔴 Pehle server folder se image delete karo
        if ($news->image && file_exists(public_path('uploads/news/' . $news->image))) {
            unlink(public_path('uploads/news/' . $news->image));
        }

        // 🔴 Fir database se news delete karo
        $news->delete();

        return redirect()->route('news.manage')->with('success', 'News and its image deleted successfully!');
    }
}