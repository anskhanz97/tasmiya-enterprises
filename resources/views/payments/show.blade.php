@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-800">✓ {{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-800">✗ {{ session('error') }}</p>
            </div>
        @endif

        <!-- Payment Details Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold mb-6">Payment Details</h1>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <!-- Payment ID -->
                <div>
                    <p class="text-sm text-gray-600">Payment ID</p>
                    <p class="text-lg font-semibold text-gray-900">#{{ $payment->id }}</p>
                </div>

                <!-- Status -->
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                        @if($payment->status === 'completed') bg-green-100 text-green-800
                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($payment->status === 'processing') bg-blue-100 text-blue-800
                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                        @elseif($payment->status === 'refunded') bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>

                <!-- Amount -->
                <div>
                    <p class="text-sm text-gray-600">Amount</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                </div>

                <!-- Payment Method -->
                <div>
                    <p class="text-sm text-gray-600">Payment Method</p>
                    <p class="text-lg font-semibold text-gray-900">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</p>
                </div>
            </div>

            <hr class="my-6">

            <!-- Additional Details -->
            <div class="space-y-4">
                @if($payment->service)
                    <div>
                        <p class="text-sm text-gray-600">Service</p>
                        <p class="text-gray-900">{{ $payment->service->name }}</p>
                    </div>
                @endif

                @if($payment->description)
                    <div>
                        <p class="text-sm text-gray-600">Description</p>
                        <p class="text-gray-900">{{ $payment->description }}</p>
                    </div>
                @endif

                <div>
                    <p class="text-sm text-gray-600">Payer</p>
                    <p class="text-gray-900">{{ $payment->user->name }} ({{ $payment->user->email }})</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Payment Date</p>
                    <p class="text-gray-900">{{ $payment->created_at->format('M d, Y \a\t H:i A') }}</p>
                </div>

                @if($payment->paid_at)
                    <div>
                        <p class="text-sm text-gray-600">Paid At</p>
                        <p class="text-gray-900">{{ $payment->paid_at->format('M d, Y \a\t H:i A') }}</p>
                    </div>
                @endif

                @if($payment->stripe_payment_intent_id)
                    <div>
                        <p class="text-sm text-gray-600">Stripe Intent ID</p>
                        <p class="text-gray-900 font-mono text-xs">{{ $payment->stripe_payment_intent_id }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            @if($payment->isPending() && $payment->payment_method === 'card')
                <form action="{{ route('payments.confirm', $payment) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition">
                        ✓ Confirm Payment
                    </button>
                </form>
            @endif

            @if($payment->canBeRefunded())
                <button onclick="showRefundModal()" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 rounded-lg transition">
                    ↩ Request Refund
                </button>
            @endif

            <a href="{{ route('payments.index') }}" class="block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 rounded-lg transition">
                ← Back to Payments
            </a>
        </div>

        <!-- Invoice/Receipt Section -->
        @if($payment->isCompleted())
            <div class="bg-gray-50 rounded-lg p-6 mt-8">
                <h2 class="text-xl font-bold mb-4">📄 Receipt</h2>
                <div class="bg-white p-4 rounded border border-gray-200">
                    <p class="text-sm text-gray-600">Invoice #{{ $payment->invoice_number ?? $payment->id }}</p>
                    <p class="text-sm text-gray-600">Date: {{ $payment->created_at->format('M d, Y') }}</p>
                    <p class="text-2xl font-bold my-4">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                    <p class="text-sm text-gray-600">Status: Paid</p>
                </div>
                <button onclick="window.print()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    🖨 Print Receipt
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold mb-4">Request Refund</h2>
        
        <form action="{{ route('payments.refund', $payment) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <select name="reason" id="reason" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="requested_by_customer">Requested by Customer</option>
                    <option value="duplicate">Duplicate Charge</option>
                    <option value="fraudulent">Fraudulent</option>
                    <option value="general">Other</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Explain why you need a refund..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeRefundModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                    Submit Refund Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showRefundModal() {
        document.getElementById('refundModal').classList.remove('hidden');
    }

    function closeRefundModal() {
        document.getElementById('refundModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('refundModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRefundModal();
        }
    });
</script>
@endsection
