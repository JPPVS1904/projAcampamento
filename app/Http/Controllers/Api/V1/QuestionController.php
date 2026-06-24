<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Question::with('categories')->orderBy('order', 'asc');

        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        $question = Question::create([
            'text' => $validated['text'],
            'order' => $validated['order'] ?? 0,
            'section_id' => $validated['section_id'] ?? 1, // Fallback to 1 if no section
        ]);

        if (isset($validated['category_ids'])) {
            $question->categories()->sync($validated['category_ids']);
        }

        return response()->json(['data' => $question->load('categories')], 201);
    }

    public function show(Question $question)
    {
        return response()->json(['data' => $question->load('categories')]);
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'text' => 'sometimes|string|max:255',
            'order' => 'nullable|integer',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        $question->update($validated);

        if (isset($validated['category_ids'])) {
            $question->categories()->sync($validated['category_ids']);
        }

        return response()->json(['data' => $question->load('categories')]);
    }

    public function destroy(Question $question)
    {
        $question->categories()->detach();
        $question->delete();

        return response()->noContent();
    }
}
