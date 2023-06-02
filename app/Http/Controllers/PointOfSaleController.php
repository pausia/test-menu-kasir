<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Arr;

class PointOfSaleController extends Controller
{
    public function index()
    {
        // Mendapatkan data produk dari database
        $products = Product::all();

        // Mengirim data produk ke halaman point-of-sale
        return view('point-of-sale.point-of-sale', compact('products'));
    }
    public function addToTotal(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Simpan data produk yang ditambahkan ke dalam sesi atau database
        // Sesuaikan kode ini dengan logika penyimpanan data Anda
        // Contoh menggunakan sesi:
        $selectedProducts = session()->get('selected_products', []);
        $selectedProduct = Arr::first($selectedProducts, function ($item) use ($productId) {
            return $item['id'] == $productId;
        });

        if ($selectedProduct) {
            $selectedProduct['quantity']++;
        } else {
            $selectedProduct = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 0
            ];
            $selectedProducts[] = $selectedProduct;
        }

        session()->put('selected_products', $selectedProducts);

        return response()->json(['message' => 'Product added to total'], 200);
    }



}
