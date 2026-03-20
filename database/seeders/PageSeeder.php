<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Page;
use Illuminate\Support\Str;


class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $pages = ['About Us', 'Privacy Policy', 'Terms and Conditions'];

    foreach ($pages as $pageTitle) {
        Page::create([
            'title' => $pageTitle,
            'slug' => Str::slug($pageTitle),
            'content' => 'Coming soon... Edit this content from Admin Panel.',
            'meta_description' => 'Default meta description for ' . $pageTitle
        ]);
    }
    }
}
