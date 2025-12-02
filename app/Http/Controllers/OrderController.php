<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    /**
     * Tampilkan halaman checkout
     */
    public function checkout()
    {
        // Cek apakah user sudah login
        if (!Session::has('user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk checkout.');
        }

        // Cart akan diambil dari localStorage via JavaScript
        return view('checkout');
    }

    /**
     * Proses order dan redirect ke pembayaran
     */
    public function processOrder(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_telp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'cart' => 'required|json' // Cart dikirim sebagai JSON dari frontend
        ]);

        try {
            DB::beginTransaction();

            $userId = Session::get('user_id');
            $cart = json_decode($validated['cart'], true);

            if (empty($cart)) {
                return back()->with('error', 'Keranjang belanja kosong.');
            }

            // Hitung total
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Cek apakah customer sudah ada
            $customer = DB::table('customer')->where('user_id', $userId)->first();

            if ($customer) {
                // Update data customer
                DB::table('customer')
                    ->where('customer_id', $customer->customer_id)
                    ->update([
                        'nama' => $validated['nama'],
                        'no_telp' => $validated['no_telp'],
                        'alamat' => $validated['alamat'],
                        'updated_at' => now()
                    ]);
                $customerId = $customer->customer_id;
            } else {
                // Insert customer baru
                $customerId = DB::table('customer')->insertGetId([
                    'user_id' => $userId,
                    'nama' => $validated['nama'],
                    'no_telp' => $validated['no_telp'],
                    'alamat' => $validated['alamat'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Insert order
            $orderId = DB::table('orders')->insertGetId([
                'customer_id' => $customerId,
                'order_date' => now(),
                'total_price' => $total,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Insert order details
            foreach ($cart as $item) {
                DB::table('ordersdetail')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            // Simpan order_id ke session
            Session::put('current_order_id', $orderId);
            Session::forget('cart'); // Hapus cart

            return redirect()->route('payment', $orderId);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Halaman pembayaran
     */
    public function payment($orderId)
    {
        $order = DB::table('orders')
            ->join('customer', 'orders.customer_id', '=', 'customer.customer_id')
            ->select('orders.*', 'customer.nama', 'customer.no_telp', 'customer.alamat')
            ->where('orders.order_id', $orderId)
            ->first();

        if (!$order) {
            return redirect()->route('menu')->with('error', 'Pesanan tidak ditemukan.');
        }

        $orderDetails = DB::table('ordersdetail')
            ->join('products', 'ordersdetail.product_id', '=', 'products.product_id')
            ->select('ordersdetail.*', 'products.product_name', 'products.image_url')
            ->where('ordersdetail.order_id', $orderId)
            ->get();

        return view('payment', compact('order', 'orderDetails'));
    }

    /**
     * Konfirmasi pembayaran
     */
    public function confirmPayment(Request $request, $orderId)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:debit,credit,transfer',
        ]);

        try {
            // Update status order (simulasi pembayaran berhasil)
            DB::table('orders')
                ->where('order_id', $orderId)
                ->update([
                    'status' => 'sedang dibuat',
                    'updated_at' => now()
                ]);

            Session::forget('current_order_id');

            return redirect()->route('order.status', $orderId)->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Halaman status order user
     */
    public function orderStatus($orderId)
    {
        $order = DB::table('orders')
            ->join('customer', 'orders.customer_id', '=', 'customer.customer_id')
            ->select('orders.*', 'customer.nama', 'customer.no_telp', 'customer.alamat')
            ->where('orders.order_id', $orderId)
            ->first();

        if (!$order) {
            return redirect()->route('menu')->with('error', 'Pesanan tidak ditemukan.');
        }

        $orderDetails = DB::table('ordersdetail')
            ->join('products', 'ordersdetail.product_id', '=', 'products.product_id')
            ->select('ordersdetail.*', 'products.product_name', 'products.image_url')
            ->where('ordersdetail.order_id', $orderId)
            ->get();

        return view('order-status', compact('order', 'orderDetails'));
    }

    /**
     * Daftar pesanan user
     */
    public function myOrders()
    {
        if (!Session::has('user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = Session::get('user_id');
        
        $orders = DB::table('orders')
            ->join('customer', 'orders.customer_id', '=', 'customer.customer_id')
            ->select('orders.*', 'customer.nama')
            ->where('customer.user_id', $userId)
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return view('my-orders', compact('orders'));
    }
}
