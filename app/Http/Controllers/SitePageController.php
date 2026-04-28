<?php

namespace App\Http\Controllers;

use App\Models\SitePage;

class SitePageController extends Controller
{
    public function show(string $slug)
    {
        $page = SitePage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }
}

