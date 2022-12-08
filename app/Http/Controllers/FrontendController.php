<?php

namespace App\Http\Controllers;

use Exception;
use Midtrans\Snap;
use App\Models\Cart;
use Midtrans\Config;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CheckoutRequest;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['galleries'])->take(6)->get(); //->take(10) kasih limit 10
        $ariveds = Product::with(['galleries'])->latest()->take(5)->get();
        $jumlah = [
            'jumlahMeja' => Product::where('name', 'LIKE', '%Meja%')->count(),
            'jumlahKursi' => Product::where('name', 'LIKE', '%Kursi%')->count(),
            'jumlahSofa' => Product::where('name', 'LIKE', '%Sofa%')->count(),
            'jumlahRak' => Product::where('name', 'LIKE', '%Rak%')->count(),
        ];

        return view('pages.frontend.index', compact('products', 'ariveds'), $jumlah);
    }

    public function ourProducts(Request $request)
    {
        $collection = [
            'judul' => 'Our Products',
            'slug' => 'our-products',
            'products' => Product::with(['galleries'])->get() //->take(10) kasih limit 10
        ];

        return view('pages.frontend.our-products', $collection);
    }

    public function meja(Request $request)
    {
        $collection = [
            'judul' => 'Meja',
            'slug' => 'meja',
            'products' => Product::with(['galleries'])->where('name', 'LIKE', '%Meja%')->get(), //->take(10) kasih limit 10
            'jumlahMeja' => Product::all()->count(),
        ];

        return view('pages.frontend.our-products', $collection);
    }

    public function kursi(Request $request)
    {
        $collection = [
            'judul' => 'Kursi',
            'slug' => 'kursi',
            'products' => Product::with(['galleries'])->where('name', 'LIKE', '%Kursi%')->get() //->take(10) kasih limit 10
        ];

        return view('pages.frontend.our-products', $collection);
    }

    public function sofa(Request $request)
    {
        $collection = [
            'judul' => 'Sofa',
            'slug' => 'sofa',
            'products' => Product::with(['galleries'])->where('name', 'LIKE', '%sofa%')->get() //->take(10) kasih limit 10
        ];

        return view('pages.frontend.our-products', $collection);
    }

    public function rak(Request $request)
    {
        $collection = [
            'judul' => 'Rak',
            'slug' => 'rak',
            'products' => Product::with(['galleries'])->where('name', 'LIKE', '%rak%')->get() //->take(10) kasih limit 10
        ];

        return view('pages.frontend.our-products', $collection);
    }

    // public function meja(Request $request)
    // {
    //     $judul = 'Meja';
    //     $products = Product::with(['galleries'])->where('name', 'LIKE', '%'.$judul.'%')->get(); //->take(10) kasih limit 10

    //     return view('pages.frontend.our-products', compact('products'));
    // }

    public function details(Request $request, $slug)
    {
        $product = Product::with(['galleries'])->where('slug', $slug)->firstOrFail();
        $recommendations = Product::with(['galleries'])->inRandomOrder()->take(4)->get();

        return view('pages.frontend.details', compact('product', 'recommendations'));
    }

    public function cartAdd(Request $request, $id)
    {
        Cart::create([
            'users_id' => Auth::user()->id,
            'products_id' => $id,
        ]);

        return redirect('cart');
    }

    public function cartDelete(Request $request, $id)
    {
        $item = Cart::findOrFail($id);

        $item->delete();

        return redirect('cart');
    }

    public function cart(Request $request)
    {
        $carts = Cart::with(['product.galleries'])->where('users_id', Auth::user()->id)->get();

        return view('pages.frontend.cart', compact('carts'));
    }

    public function checkout(CheckoutRequest $request)
    {
        $data = $request->all();

        // get data carts
        $carts = Cart::with(['product'])->where('users_id', Auth::user()->id)->get();

        // add to transaction data
        $data['users_id'] = Auth::user()->id;
        $data['total_price'] = $carts->sum('product.price');

        // create transaction
        $transaction = Transaction::create($data);

        // create transaction item
        foreach ($carts as $cart) {
            $item[] = TransactionItem::create([
                'transactions_id' => $transaction->id,
                'users_id' => $cart->users_id,
                'products_id' => $cart->products_id,
            ]);
        }

        // delete cart after
        Cart::where('users_id', Auth::user()->id)->delete();

        // konfigurasi
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        // setup variable midtrans
        $midtrans = [
            'transaction_details' => [
                'order_id' => 'RPZ-' . $transaction->id,
                'gross_amount' => (int) $transaction->total_price
            ],
            'customer_details' => [
                'first_name' => $transaction->name,
                'email' => $transaction->email
            ],
            'enabled_payments' => ['gopay', 'bank_transfer'],
            'vtwweb' => [],
        ];

        // payment procces
        try {
            // Get Snap Payment Page URL
            $paymentUrl = \Midtrans\Snap::createTransaction($midtrans)->redirect_url;

            $transaction->payment_url = $paymentUrl;
            $transaction->save();

            // Redirect to Snap Payment Page
            return redirect($paymentUrl);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function success(Request $request)
    {
        return view('pages.frontend.success');
    }
}
