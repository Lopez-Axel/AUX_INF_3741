<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener categorías'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean'
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category->load('products'));
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'status' => 'sometimes|boolean'
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        // Verificar si la categoría tiene productos asociados
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos asociados'
            ], 409);
        }

        $category->delete();
        return response()->json(null, 204);
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.status' => 'required|boolean'
        ]);

        $categories = [];
        foreach ($validated['categories'] as $categoryData) {
            $categories[] = Category::create($categoryData);
        }

        return response()->json($categories, 201);
    }
}
