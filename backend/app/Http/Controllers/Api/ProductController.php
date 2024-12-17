<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'supplier']);

        // Aplicar filtros
        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('low_stock')) {
            $query->lowStock();
        }

        if ($request->has('near_expiration')) {
            $query->nearExpiration($request->get('expiration_days', 90));
        }

        // Paginación
        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $products,
            'alerts' => [
                'low_stock' => Product::lowStock()->count(),
                'expiring_soon' => Product::nearExpiration()->count()
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'expiration_date' => 'required|date|after:today',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'active_compound' => 'nullable|string|max:255',
            'prescription_required' => 'boolean',
            'storage_conditions' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode'
        ]);

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto creado exitosamente',
            'data' => $product->load(['category', 'supplier'])
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'min_stock' => 'sometimes|integer|min:0',
            'expiration_date' => 'sometimes|date|after:today',
            'category_id' => 'sometimes|exists:categories,id',
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'active_compound' => 'nullable|string|max:255',
            'prescription_required' => 'boolean',
            'storage_conditions' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id
        ]);

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto actualizado exitosamente',
            'data' => $product->load(['category', 'supplier'])
        ]);
    }

    public function updateStock(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255'
        ]);

        $newStock = $product->stock + $validated['adjustment'];
        
        if ($newStock < 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stock insuficiente'
            ], 422);
        }

        $product->update(['stock' => $newStock]);

        return response()->json([
            'status' => 'success',
            'message' => 'Stock actualizado exitosamente',
            'data' => [
                'previous_stock' => $product->stock - $validated['adjustment'],
                'adjustment' => $validated['adjustment'],
                'new_stock' => $product->stock
            ]
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $product->load(['category', 'supplier']),
            'alerts' => [
                'low_stock' => $product->stock <= $product->min_stock,
                'expiring_soon' => $product->expiration_date->lte(now()->addDays(90))
            ]
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->stock > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se puede eliminar el producto porque aún tiene stock disponible'
            ], 409);
        }

        try {
            $product->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Producto eliminado exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el producto'
            ], 500);
        }
    }
}