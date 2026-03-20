<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavLabel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NavLabelController extends Controller
{
    public function index(): View
    {
        NavLabel::upsertDefaults($this->defaults());

        return view('admin.settings.nav-labels', [
            'labels' => NavLabel::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'labels' => 'required|array',
            'labels.*.key' => 'required|string|max:100',
            'labels.*.label' => 'required|string|max:255',
            'labels.*.is_visible' => 'nullable|boolean',
            'labels.*.sort_order' => 'required|integer|min:0|max:999',
        ]);

        foreach ($validated['labels'] as $row) {
            NavLabel::query()->updateOrCreate(
                ['key' => $row['key']],
                [
                    'label' => $row['label'],
                    'is_visible' => (bool) ($row['is_visible'] ?? false),
                    'sort_order' => (int) $row['sort_order'],
                ]
            );
        }

        Cache::forget(NavLabel::CACHE_KEY);

        return redirect()->route('admin.settings.nav-labels')->with('status', 'Navigation labels saved successfully.');
    }

    protected function defaults(): array
    {
        return NavLabel::defaults();
    }
}
