<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Choco Jell</title>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
    <link rel="icon" href="{{ asset('img/logo.png') }}" />
</head>
<body>
    <header class="navbar" style="position: fixed; top: 0; width: 100%; z-index: 1000; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <span>Choco Jell</span>
        </div>
    </header>

    <div class="payment-container">
        <div class="payment-section">
            <h2>💳 Pembayaran - Order #{{ $order->order_id }}</h2>

            @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
            @endif

            <!-- Ringkasan Pesanan -->
            <div class="order-summary">
                <h3 style="color: #00888a; margin-bottom: 15px;">Detail Pesanan</h3>
                <div class="summary-row">
                    <span>Nama Penerima:</span>
                    <strong>{{ $order->nama }}</strong>
                </div>
                <div class="summary-row">
                    <span>No. Telepon:</span>
                    <strong>{{ $order->no_telp }}</strong>
                </div>
                <div class="summary-row">
                    <span>Alamat:</span>
                    <strong>{{ $order->alamat }}</strong>
                </div>
                <div class="summary-row">
                    <span>Total Pembayaran:</span>
                    <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                </div>
            </div>
                </div>
            </div>

            <!-- Product List -->
            <div class="product-list">
                <h3 style="color: #00888a; margin: 20px 0 15px;">Items yang Dibeli:</h3>
                @foreach($orderDetails as $detail)
                <div class="product-item">
                    <img src="{{ asset($detail->image_url ?? 'img/logo.png') }}" alt="{{ $detail->product_name }}">
                    <div class="product-info">
                        <h4>{{ $detail->product_name }}</h4>
                        <p class="product-quantity">Jumlah: {{ $detail->quantity }}</p>
                        <p class="product-price">Rp {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Metode Pembayaran -->
            <form action="{{ route('payment.confirm', $order->order_id) }}" method="POST" id="paymentForm">
                @csrf
                <div class="payment-methods">
                    <h3 style="color: #00888a; margin: 30px 0 20px;">Pilih Metode Pembayaran:</h3>
                    
                    <label class="payment-option" onclick="selectPayment('debit')">
                        <input type="radio" name="payment_method" value="debit" required>
                        <div class="payment-icon"></div>
                        <div class="payment-details">
                            <h4>Kartu Debit</h4>
                            <p>Pembayaran langsung dari rekening bank Anda</p>
                        </div>
                    </label>

                    <label class="payment-option" onclick="selectPayment('credit')">
                        <input type="radio" name="payment_method" value="credit" required>
                        <div class="payment-icon"></div>
                        <div class="payment-details">
                            <h4>Kartu Kredit</h4>
                            <p>Visa, Mastercard, JCB</p>
                        </div>
                    </label>

                    <label class="payment-option" onclick="selectPayment('transfer')">
                        <input type="radio" name="payment_method" value="transfer" required>
                        <div class="payment-icon"></div>
                        <div class="payment-details">
                            <h4>Transfer Bank</h4>
                            <p>BCA, Mandiri, BNI, BRI</p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="btn-pay" id="btnPay" disabled>
                    Total Harga Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </button>
            </form>

            <p style="text-align: center; color: #999; margin-top: 20px; font-size: 0.9rem;">
                Pembayaran Anda aman dan terenkripsi
            </p>
        </div>
    </div>

    <script>
        function selectPayment(method) {
            // Remove all selected classes
            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked method
            event.currentTarget.classList.add('selected');
            
            // Enable pay button
            document.getElementById('btnPay').disabled = false;
        }

        // Form submit handler (simulasi pembayaran)
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const btnPay = document.getElementById('btnPay');
            btnPay.innerHTML = 'Memproses Pembayaran';
            btnPay.disabled = true;
        });
    </script>
</body>
</html>
