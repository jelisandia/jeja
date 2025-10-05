<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $query = Product::query();

        // Search by name or description
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // Order by created_at desc (newest first)
        $query->orderBy('created_at', 'desc');

        $products = $query->paginate($perPage);

        return new ProductCollection($products);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Load purchases relationship to get purchase count
        $product->load('purchases');

        return new ProductResource($product);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate unique filenames
            $originalFilename = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
            $previewFilename = Str::uuid() . '.jpg';

            // Create directories if they don't exist
            Storage::makeDirectory('public/products/originals');
            Storage::makeDirectory('public/products/previews');

            // Store original image
            $originalPath = $request->file('image')->storeAs(
                'public/products/originals',
                $originalFilename
            );

            // Create preview with watermark using Intervention Image
            $imageManager = new ImageManager(new Driver());
            $image = $imageManager->read($request->file('image'));

            // Resize to preview size (max 800px width, maintain aspect ratio)
            $image->scaleDown(width: 800);

            // Add watermark
            $watermarkText = 'DIGITAL STORE - PREVIEW';
            $image->text($watermarkText, $image->width() / 2, $image->height() / 2, function ($font) {
                $font->filename(public_path('fonts/arial.ttf')); // You may need to add a font file
                $font->size(24);
                $font->color('rgba(255, 255, 255, 0.8)');
                $font->align('center');
                $font->valign('middle');
            });

            // Save preview image
            $previewPath = 'public/products/previews/' . $previewFilename;
            $image->toJpeg(80)->save(storage_path('app/' . $previewPath));

            // Create product in database
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'original_file_path' => 'products/originals/' . $originalFilename,
                'preview_file_path' => 'products/previews/' . $previewFilename,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => new ProductResource($product)
            ], 201);

        } catch (\Exception $e) {
            // Clean up uploaded files if database operation fails
            if (isset($originalPath)) {
                Storage::delete($originalPath);
            }
            if (isset($previewPath)) {
                Storage::delete($previewPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
