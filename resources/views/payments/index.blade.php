@extends('layouts.app')

@section('content')
<style>
    .payments-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 3rem 1rem;
        text-align: center;
        color: white;
        margin-bottom: 3rem;
        border-radius: 0 0 50% 50% / 0 0 20px 20px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }
    
    .payments-hero h1 {
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .empty-state {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 4rem 2rem;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .empty-state-icon {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.1));
    }
    
    .payments-table-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .payments-table-container:hover {
        box-shadow: 0 15px 50px rgba(0,0,0,0.12);
    }
    
    .payments-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .payments-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .payments-table thead th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.95rem;
        border-bottom: 3px solid rgba(255,255,255,0.2);
    }
    
    .payments-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .payments-table tbody tr:hover {
        background: linear-gradient(90deg, #f9fafb 0%, #e5e7eb 100%);
        transform: scale(1.01);
    }
    
    .payments-table tbody td {
        padding: 1.25rem 1.5rem;
        font-size: 0.95rem;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-completed {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        color: #065f46;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #92400e;
    }
    
    .status-processing {
        background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        color: #1e3a8a;
    }
    
    .status-failed {
        background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);
        color: white;
    }
    
    .status-refunded {
        background: linear-gradient(135deg, #e0e0e0 0%, #9e9e9e 100%);
        color: #424242;
    }
    
    .view-btn {
        color: #667eea;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .view-btn:hover {
        color: #764ba2;
        transform: translateX(5px);
    }
    
    .new-payment-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        margin-top: 2rem;
    }
    
    .new-payment-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    }
</style>

<div class="payments-hero">
    <h1>💳 Payment History</h1>
    <p>Track all your transactions in one place</p>
</div>

<div class="container mx-auto px-4 pb-12">
    @if ($payments->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">💸</div>
            <h2 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">No Payments Yet</h2>
            <p style="color: #6b7280; font-size: 1.125rem; margin-bottom: 2rem;">Start your first transaction today!</p>
            <a href="{{ route('payments.create') }}" class="new-payment-btn">
                + Make Your First Payment
            </a>
        </div>
    @else
        <div class="payments-table-container">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td style="font-weight: 700; color: #667eea;">#{{ $payment->id }}</td>
                            <td style="color: #374151;">
                                {{ $payment->service?->name ?? '—' }}
                            </td>
                            <td style="font-weight: 700; color: #1f2937;">
                                {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                            </td>
                            <td style="color: #6b7280;">
                                {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                            </td>
                            <td>
                                <span class="status-badge status-{{ $payment->status }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td style="color: #6b7280;">
                                {{ $payment->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <a href="{{ route('payments.show', $payment) }}" class="view-btn">
                                    View Details →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($payments->hasPages())
            <div style="margin-top: 2rem;">
                {{ $payments->links() }}
            </div>
        @endif

        <!-- New Payment Button -->
        <div style="text-align: center;">
            <a href="{{ route('payments.create') }}" class="new-payment-btn">
                + New Payment
            </a>
        </div>
    @endif
</div>
@endsection
