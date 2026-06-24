<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryQuestionController extends Controller
{
    /**
     * List questions linked to a category.
     */
    public function index(Category $category): JsonResponse
    {
        $questions = $category->questions()->get()->map(function ($question) {
            return [
                'id' => $question->id,
                'text' => $question->text,
                'order' => $question->order,
                'type' => $question->type,
                'pivot_id' => $question->pivot->id,
            ];
        });

        return response()->json(['data' => $questions]);
    }

    /**
     * Link a question to a category.
     */
    public function store(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
        ]);

        // Check if already linked
        if ($category->questions()->where('question_id', $validated['question_id'])->exists()) {
            return response()->json(['message' => 'Esta pergunta já está vinculada a esta categoria.'], 422);
        }

        $category->questions()->attach($validated['question_id']);

        return response()->json(['message' => 'Pergunta vinculada com sucesso.'], 201);
    }

    /**
     * Detach a question from a category.
     */
    public function destroy(Category $category, int $questionId): JsonResponse
    {
        $category->questions()->detach($questionId);

        return response()->json(null, 204);
    }
}
