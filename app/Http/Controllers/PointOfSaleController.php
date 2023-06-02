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

        // Mengambil total harga dari sesi atau database
        $totalPrice = session()->get('total_price', 0);

        // Mengirim data produk dan total harga ke halaman point-of-sale
        return view('point-of-sale.point-of-sale', compact('products', 'totalPrice'));
    }

    public function addToTotal(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Mendapatkan data produk dari sesi atau database
        $products = session()->get('selected_products', []);
        $selectedProduct = Arr::first($products, function ($item) use ($productId) {
            return $item['id'] == $productId;
        });

        if ($selectedProduct) {
            $selectedProduct['quantity']++;
        } else {
            $selectedProduct = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1
            ];
            $products[] = $selectedProduct;
        }

        session()->put('selected_products', $products);

        // Menghitung total harga dari produk yang telah ditambahkan
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product['price'] * $product['quantity'];
        }

        // Menyimpan total harga ke dalam sesi atau database
        session()->put('total_price', $totalPrice);

        return response()->json(['message' => 'Product added to total', 'totalPrice' => $totalPrice], 200);
    }
}

