@extends('base.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-2">Complete your payment</h4>
                    <p class="text-muted mb-4">
                        Invoice: <strong>{{ $order->invoice_number }}</strong><br>
                        Total: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                    </p>

                    <div class="d-grid gap-2">
                        <button id="pay-button" type="button" class="btn btn-primary btn-lg">
                            Pay Now
                        </button>
                        <a class="btn btn-outline-secondary" href="{{ route('store') }}">Back to Store</a>
                    </div>

                    <p class="small text-muted mt-3 mb-0">
                        If the popup doesn’t open, please allow popups and click “Pay Now” again.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @php
        $isProduction = (bool) config('midtrans.is_production');
        $snapUrl = $isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp

    <script src="{{ $snapUrl }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        (function () {
            const snapToken = @json($snapToken);
            const statusUrl = @json(route('payment_status', $order->id));

            function redirectToStatus() {
                window.location.href = statusUrl;
            }

            function openSnap() {
                if (!snapToken) {
                    alert('Missing payment token.');
                    return;
                }

                if (typeof snap === 'undefined') {
                    alert('Failed to load payment script. Please refresh and try again.');
                    return;
                }

                snap.pay(snapToken, {
                    onSuccess: redirectToStatus,
                    onPending: redirectToStatus,
                    onError: redirectToStatus,
                    onClose: function () {
                        // User closed the popup — keep order pending.
                        redirectToStatus();
                    }
                });
            }

            document.getElementById('pay-button')?.addEventListener('click', openSnap);

            // Auto-open on load to match typical checkout UX.
            window.addEventListener('load', function () {
                openSnap();
            });
        })();
    </script>
@endsection
