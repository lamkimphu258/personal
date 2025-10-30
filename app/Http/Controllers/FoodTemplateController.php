<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodTemplateRequest;
use App\Models\FoodTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FoodTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $templates = FoodTemplate::query()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderByDesc('created_at')
            ->get();

        return view('food-templates.index', [
            'templates' => $templates,
            'searchTerm' => $search,
        ]);
    }

    public function store(FoodTemplateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        FoodTemplate::create($data);

        return redirect()
            ->route('food-templates.index')
            ->with('status', 'template-created');
    }

    public function show(FoodTemplate $foodTemplate): View
    {
        return view('food-templates.show', [
            'template' => $foodTemplate,
        ]);
    }
}
