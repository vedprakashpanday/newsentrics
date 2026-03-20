<?php
namespace App\Http\Controllers;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller {
    public function index() {
        $ads = Ad::all();
        return view('admin.ads.index', compact('ads'));
    }

    public function update(Request $request, $id) {
        $ad = Ad::findOrFail($id);
        $ad->update([
            'ad_code' => $request->ad_code,
            'is_active' => $request->has('is_active')
        ]);
        return back()->with('success', 'Ad slot updated successfully!');
    }
}