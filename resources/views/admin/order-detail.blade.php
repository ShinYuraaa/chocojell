<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Choco Jell Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="icon" href="{{ asset('img/logo.png') }}" />
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
                <h2>Choco Jell Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item">
                    <span><img src="{{ asset('img/dashboard.png') }}" alt="dashboard" style="width: 20px; height: 20px;"></span> Dashboard
                </a>
                <a href="{{ route('admin.products') }}" class="nav-item">
                    <span><img src="{{ asset('img/produk.png') }}" alt="produk" style="width: 20px; height: 20px;"></span> Produk
                </a>
                <a href="{{ route('admin.orders') }}" class="nav-item active">
                    <span><img src="{{ asset('img/pesanan.png') }}" alt="pesanan" style="width: 20px; height: 20px;"></span> Pesanan
                </a>
                <a href="{{ route('index') }}" class="nav-item" target="_blank">
                    <span><img src="{{ asset('img/home.png') }}" alt="home" style="width: 20px; height: 20px;"></span> Ke Halaman Utama
                </a>
                <a href="{{ route('admin.logout.get') }}" class="nav-item">
                    <span><img src="{{ asset('img/logout.png') }}" alt="logout" style="width: 20px; height: 20px;"></span> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h1>Detail Pesanan #{{ $order->order_id }}</h1>
                <a href="{{ route('admin.orders') }}" class="btn-secondary">← Kembali</a>
            </header>

            <div class="order-detail-container">
                <!-- Order Info Card -->
                <div class="detail-card">
                    <h2>Informasi Pesanan</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">ID Pesanan:</span>
                            <span class="value">#{{ $order->order_id }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Status:</span>
                            <span class="value">
                                <span class="status-badge status-{{ str_replace(' ', '-', $order->status) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Tanggal Order:</span>
                            <span class="value">{{ date('d F Y, H:i', strtotime($order->created_at)) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Total Pembayaran:</span>
                            <span class="value" style="color: #4ecdc4; font-weight: bold; font-size: 1.2em;">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <h3 style="margin-top: 20px;">Update Status Pesanan</h3>
                    <form action="{{ route('admin.order.updateStatus', $order->order_id) }}" method="POST" class="status-update-form">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="status-select-large">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="sedang dibuat" {{ $order->status == 'sedang dibuat' ? 'selected' : '' }}>👨‍🍳 Sedang Dibuat</option>
                            <option value="dalam perjalanan" {{ $order->status == 'dalam perjalanan' ? 'selected' : '' }}>🚚 Dalam Perjalanan</option>
                            <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                            <option value="dibatalkan" {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
                        </select>
                        <button type="submit" class="btn-primary"><img src="{{ asset('img/update.png') }}" alt="update" style="width: 20px; height: 20px; vertical-align: middle;"> Update Status</button>
                    </form>
                </div>

                <!-- Customer Info Card -->
                <div class="detail-card">
                    <h2>Informasi Customer</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Nama:</span>
                            <span class="value">{{ $order->customer_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Email:</span>
                            <span class="value">{{ $order->customer_email ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Telepon:</span>
                            <span class="value">{{ $order->no_telp ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Items Card -->
                <div class="detail-card">
                    <h2>Detail Produk</h2>
                    <div class="order-items">
                        @foreach($orderDetails as $item)
                        <div class="order-item">
                            <div class="item-image">
                                <img src="{{ asset($item->image_url ?? 'img/logo.png') }}" alt="{{ $item->product_name }}">
                            </div>
                            <div class="item-details">
                                <h3>{{ $item->product_name }}</h3>
                                <p class="item-price">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                            </div>
                            <div class="item-total">
                                <strong>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="order-total">
                        <span>Total:</span>
                        <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
