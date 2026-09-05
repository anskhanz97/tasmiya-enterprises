@extends('layouts.app')

@section('content')
<style>
    .payment-hero {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        padding: 3.5rem 1rem;
        text-align: center;
        color: white;
        margin-bottom: 3rem;
        border-radius: 0 0 50% 50% / 0 0 20px 20px;
        box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
    }
    
    .payment-hero h1 {
        font-size: 2.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .payment-hero p {
        font-size: 1.125rem;
        opacity: 0.95;
    }
    
    .payment-form-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        padding: 2.5rem;
        transition: all 0.3s ease;
    }
    
    .payment-form-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 70px rgba(0,0,0,0.15);
    }
    
    .form-group-enhanced {
        margin-bottom: 2rem;
    }
    
    .form-group-enhanced label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    
    .form-group-enhanced input,
    .form-group-enhanced select,
    .form-group-enhanced textarea {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    
    .form-group-enhanced input:focus,
    .form-group-enhanced select:focus,
    .form-group-enhanced textarea:focus {
        outline: none;
        border-color: #11998e;
        background: white;
        box-shadow: 0 0 0 4px rgba(17, 153, 142, 0.1);
        transform: translateY(-2px);
    }
    
    .payment-method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .payment-method-option {
        position: relative;
    }
    
    .payment-method-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .payment-method-label {
        display: block;
        padding: 1.5rem 1rem;
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        text-align: center;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .payment-method-option input[type="radio"]:checked + .payment-method-label {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border-color: #11998e;
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(17, 153, 142, 0.3);
    }
    
    .payment-method-label:hover {
        border-color: #11998e;
        transform: translateY(-2px);
    }
    
    .submit-btn-payment {
        width: 100%;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 1.25rem;
        border: none;
        border-radius: 12px;
        font-size: 1.125rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(17, 153, 142, 0.3);
        margin-top: 1rem;
    }
    
    .submit-btn-payment:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(17, 153, 142, 0.4);
    }
    
    .submit-btn-payment:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .info-box {
        background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
        border-left: 4px solid #11998e;
        padding: 1.5rem;
        border-radius: 12px;
        margin-top: 2rem;
    }
    
    .info-box h3 {
        color: #00695c;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.125rem;
    }
    
    .info-box ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .info-box ul li {
        color: #00796b;
        padding: 0.5rem 0;
        font-size: 0.95rem;
    }
    
    .error-alert {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        animation: shake 0.5s ease;
    }
</style>

<div class="payment-hero">
    <h1>💳 Make a Payment</h1>
    <p>Secure and fast payment processing</p>
</div>

<div class="container mx-auto px-4 pb-12">
    <div class="max-w-3xl mx-auto">

        @if ($errors->any())
            <div class="error-alert">
                <h3 style="font-weight: 700; margin-bottom: 0.5rem;">⚠️ Validation Error</h3>
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="paymentForm" class="payment-form-container">
            @csrf

            <!-- Service Selection (Optional) -->
            <div class="form-group-enhanced">
                <label for="service_id">
                    🛍️ Service (Optional)
                </label>
                <select id="service_id" name="service_id">
                    <option value="">Select a service or enter custom amount</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                            {{ $service->name }} - PKR {{ number_format($service->price, 2) }}
                        </option>
                    @endforeach
                </select>
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">💡 Service amounts are pre-set and cannot be changed</p>
            </div>

            <!-- Amount -->
            <div class="form-group-enhanced">
                <label for="amount">
                    💰 Amount <span style="color: #ef4444;">*</span>
                </label>
                <div style="display: flex; gap: 0;">
                    <input 
                        type="number" 
                        id="amount" 
                        name="amount" 
                        step="0.01" 
                        min="0.01" 
                        required 
                        placeholder="0.00"
                        style="border-radius: 12px 0 0 12px;"
                    >
                    <select id="currency" name="currency" style="width: auto; min-width: 120px; border-radius: 0 12px 12px 0; border-left: 0;">
                        <option value="PKR">PKR</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                    </select>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="form-group-enhanced">
                <label>
                    💳 Payment Method <span style="color: #ef4444;">*</span>
                </label>
                <div class="payment-method-grid">
                    <div class="payment-method-option">
                        <input type="radio" name="payment_method" value="card" id="pm_card" required>
                        <label for="pm_card" class="payment-method-label">
                            💳<br>Credit/Debit Card
                        </label>
                    </div>
                    <div class="payment-method-option">
                        <input type="radio" name="payment_method" value="bank_transfer" id="pm_bank">
                        <label for="pm_bank" class="payment-method-label">
                            🏦<br>Bank Transfer
                        </label>
                    </div>
                    <div class="payment-method-option">
                        <input type="radio" name="payment_method" value="cash" id="pm_cash">
                        <label for="pm_cash" class="payment-method-label">
                            💵<br>Cash Payment
                        </label>
                    </div>
                    <div class="payment-method-option">
                        <input type="radio" name="payment_method" value="check" id="pm_check">
                        <label for="pm_check" class="payment-method-label">
                            📋<br>Check
                        </label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group-enhanced">
                <label for="description">
                    📝 Description (Optional)
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3" 
                    placeholder="Add any notes about this payment..."
                ></textarea>
            </div>

            <!-- Stripe Elements Container (for card payments) -->
            <div id="cardElementContainer" class="form-group-enhanced hidden">
                <label>💳 Card Details</label>
                <div id="card-element" style="padding: 1rem; border: 2px solid #e5e7eb; border-radius: 12px; background: #f9fafb;"></div>
                <div id="card-errors" style="color: #ef4444; font-size: 0.875rem; margin-top: 0.75rem;"></div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                id="submitBtn"
                class="submit-btn-payment"
            >
                🔒 Proceed to Payment
            </button>
        </form>

        <!-- Summary Card -->
        <div class="info-box">
            <h3>💡 Payment Information</h3>
            <ul>
                <li>✓ All payments are securely processed</li>
                <li>✓ Card payments are handled by Stripe</li>
                <li>✓ You will receive a confirmation email</li>
                <li>✓ Refunds are processed within 5-7 business days</li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Load Stripe.js
    const stripe = Stripe('{{ config("services.stripe.public") }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card');

    // Get form elements
    const form = document.getElementById('paymentForm');
    const cardElementContainer = document.getElementById('cardElementContainer');
    const amountInput = document.getElementById('amount');
    const serviceSelect = document.getElementById('service_id');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');

    // Handle payment method change
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'card') {
                cardElementContainer.classList.remove('hidden');
                if (!cardElement.getElement()) {
                    cardElement.mount('#card-element');
                }
            } else {
                cardElementContainer.classList.add('hidden');
            }
        });
    });

    // Handle service selection
    serviceSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            amountInput.value = selectedOption.dataset.price || '';
            amountInput.disabled = true;
        } else {
            amountInput.disabled = false;
            amountInput.value = '';
        }
    });

    // Handle card errors
    cardElement.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const paymentMethod = formData.get('payment_method');
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';

        try {
            // Create payment via backend
            const response = await fetch('{{ route("payments.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    service_id: formData.get('service_id') || null,
                    amount: parseFloat(formData.get('amount')),
                    currency: formData.get('currency'),
                    payment_method: paymentMethod,
                    description: formData.get('description'),
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Payment failed');
            }

            // For card payments, use Stripe
            if (paymentMethod === 'card') {
                const paymentIntent = data.client_secret;
                const { error, paymentIntent: confirmedIntent } = await stripe.confirmCardPayment(
                    paymentIntent,
                    {
                        payment_method: {
                            card: cardElement,
                        },
                    }
                );

                if (error) {
                    throw new Error(error.message);
                }

                // Redirect to payment confirmation
                window.location.href = '{{ route("payments.show", ":id") }}'.replace(':id', data.payment_id);
            } else {
                // For other methods, redirect to payment details
                window.location.href = '{{ route("payments.show", ":id") }}'.replace(':id', data.payment_id);
            }
        } catch (error) {
            alert('Payment Error: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Proceed to Payment';
        }
    });
</script>
@endsection
