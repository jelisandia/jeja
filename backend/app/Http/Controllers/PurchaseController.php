<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseCollection;
use App\Http\Resources\PurchaseResource;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the authenticated user's purchases.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        $purchases = $request->user()
            ->purchases()
            ->with('product')
            ->orderBy('purchase_timestamp', 'desc')
            ->paginate($perPage);

        return new PurchaseCollection($purchases);
    }

    /**
     * Store a newly created purchase.
     */
    public function store(Request $request, Product $product)
    {
        $user = $request->user();

        // Check if user already purchased this product
        $existingPurchase = Purchase::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingPurchase) {
            return response()->json([
                'success' => false,
                'message' => 'You have already purchased this product',
                'data' => new PurchaseResource($existingPurchase)
            ], 409);
        }

        try {
            DB::beginTransaction();

            // Create the purchase
            $purchase = Purchase::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'purchase_timestamp' => now(),
            ]);

            // Load the product relationship
            $purchase->load('product');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product purchased successfully',
                'data' => new PurchaseResource($purchase)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
