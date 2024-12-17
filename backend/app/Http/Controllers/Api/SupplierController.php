<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = Supplier::with('products')->get();
        return response()->json($suppliers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500'
        ]);

        $supplier = Supplier::create($validated);
        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json($supplier->load('products'));
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500'
        ]);

        $supplier->update($validated);
        return response()->json($supplier);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        // Verificar si el proveedor tiene productos asociados
        if ($supplier->products()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el proveedor porque tiene productos asociados'
            ], 409);
        }

        $supplier->delete();
        return response()->json(null, 204);
    }
}