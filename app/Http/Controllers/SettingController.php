<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Database se pehli setting uthao (Kyunki ek hi row hogi hamesha)
        $setting = Setting::first();
        return view('admin.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        // UpdateOrCreate use kar rahe hain: Agar setting nahi hai toh banayega, hai toh update karega
        $setting = Setting::first() ?? new Setting();

        // Agar naya logo upload hua hai
        if ($request->hasFile('logo')) {
            // Purana logo delete karo (agar hai toh)
            if ($setting->logo && file_exists(public_path('uploads/logo/' . $setting->logo))) {
                unlink(public_path('uploads/logo/' . $setting->logo));
            }
            
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/logo/'), $filename);
            $setting->logo = $filename;
        }

        $setting->site_name = $request->site_name;
        $setting->footer_about = $request->footer_about;
        $setting->facebook = $request->facebook;
        $setting->twitter = $request->twitter;
        $setting->instagram = $request->instagram;
        $setting->save();

        return back()->with('success', 'Site settings updated successfully!');
    }
}