<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Listagem pública de produtos para a landing page.
     */
    public function publicIndex(): JsonResponse
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Listagem autenticada de produtos para gerenciamento.
     */
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(
            Product::orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Faz upload de imagem de produto para o disco público (somente administradores).
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store('products', 'public');
        $url = rtrim($request->getSchemeAndHttpHost(), '/') . '/storage/' . $path;

        return response()->json([
            'url' => $url,
            'path' => 'storage/' . $path,
        ], 201);
    }

    /**
     * Cadastra um novo produto (somente administradores).
     */
    public function store(Request $request): ProductResource
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'image' => $validated['image'] ?? '',
            'price' => $validated['price'],
        ]);

        return ProductResource::make($product);
    }

    /**
     * Exibe um produto específico.
     */
    public function show(Product $product): ProductResource
    {
        return ProductResource::make($product);
    }

    /**
     * Atualiza um produto existente (somente administradores).
     */
    public function update(Request $request, Product $product): ProductResource
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);

        return ProductResource::make($product->fresh());
    }

    /**
     * Remove um produto (somente administradores).
     */
    public function destroy(Request $request, Product $product): Response
    {
        $this->authorizeAdmin($request);

        $product->delete();

        return response()->noContent();
    }

    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();
        if (!$user || !($user->is_admin ?? false)) {
            abort(403, 'Acesso restrito a administradores.');
        }
    }
}

