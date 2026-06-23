<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategorySectorController extends Controller
{
    /**
     * List sectors linked to a category with pivot data.
     */
    public function index(Category $category): JsonResponse
    {
        $sectors = $category->sectors()->get()->map(function ($sector) {
            return [
                'id' => $sector->id,
                'name' => $sector->name,
                'place' => $sector->place,
                'pivot_id' => $sector->pivot->id,
                'base_vacancies' => $sector->pivot->base_vacancies,
                'raffle_vacancies' => $sector->pivot->raffle_vacancies,
            ];
        });

        return response()->json(['data' => $sectors]);
    }

    /**
     * Link a sector to a category.
     */
    public function store(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'sector_id' => ['required', 'integer', 'exists:sectors,id'],
            'base_vacancies' => ['required', 'integer', 'min:0'],
            'raffle_vacancies' => ['nullable', 'integer', 'min:0'],
        ]);

        // Check if already linked
        if ($category->sectors()->where('sector_id', $validated['sector_id'])->exists()) {
            return response()->json(['message' => 'Este setor já está vinculado a esta categoria.'], 422);
        }

        $category->sectors()->attach($validated['sector_id'], [
            'base_vacancies' => $validated['base_vacancies'],
            'raffle_vacancies' => $validated['raffle_vacancies'] ?? $validated['base_vacancies'],
        ]);

        return response()->json(['message' => 'Setor vinculado com sucesso.'], 201);
    }

    /**
     * Update pivot data (vacancies) for a category-sector link.
     */
    public function update(Request $request, Category $category, int $sectorId): JsonResponse
    {
        $validated = $request->validate([
            'base_vacancies' => ['sometimes', 'integer', 'min:0'],
            'raffle_vacancies' => ['sometimes', 'integer', 'min:0'],
        ]);

        $category->sectors()->updateExistingPivot($sectorId, $validated);

        return response()->json(['message' => 'Vagas atualizadas com sucesso.']);
    }

    /**
     * Detach a sector from a category.
     */
    public function destroy(Category $category, int $sectorId): JsonResponse
    {
        $category->sectors()->detach($sectorId);

        return response()->json(null, 204);
    }
}
