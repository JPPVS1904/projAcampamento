<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Question::with(['categories', 'options'])->orderBy('order', 'asc');

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
            'type' => 'required|string|max:50',
            'section_id' => 'nullable|integer',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
            'options' => 'nullable|array',
            'options.*.text' => 'required|string|max:255',
        ]);

        $question = Question::create([
            'text' => $validated['text'],
            'order' => $validated['order'] ?? 0,
            'type' => $validated['type'],
            'section_id' => $validated['section_id'] ?? 1, // Fallback to 1 if no section
        ]);

        if (isset($validated['category_ids'])) {
            $question->categories()->sync($validated['category_ids']);
        }

        if (isset($validated['options']) && is_array($validated['options'])) {
            foreach ($validated['options'] as $optionData) {
                $question->options()->create(['text' => $optionData['text']]);
            }
        }

        return response()->json(['data' => $question->load(['categories', 'options'])], 201);
    }

    public function show(Question $question)
    {
        return response()->json(['data' => $question->load(['categories', 'options'])]);
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'text' => 'sometimes|string|max:255',
            'order' => 'nullable|integer',
            'type' => 'sometimes|string|max:50',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
            'options' => 'nullable|array',
            'options.*.text' => 'required|string|max:255',
        ]);

        $question->update($validated);

        if (isset($validated['category_ids'])) {
            $question->categories()->sync($validated['category_ids']);
        }

        if ($request->has('options')) {
            $question->options()->delete(); // Remove old options
            if (is_array($validated['options'])) {
                foreach ($validated['options'] as $optionData) {
                    $question->options()->create(['text' => $optionData['text']]);
                }
            }
        }

        return response()->json(['data' => $question->load(['categories', 'options'])]);
    }

    public function destroy(Question $question)
    {
        $question->options()->delete();
        $question->categories()->detach();
        $question->delete();

        return response()->noContent();
    }
}
