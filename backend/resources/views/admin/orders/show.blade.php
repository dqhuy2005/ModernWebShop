@extends('layouts.admin.app')

@section('title', 'Order #' . $order->id . ' - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-file-invoice me-2"></i>Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </h1>
                <span class="badge bg-{{ $order->status_badge_color }} mt-2 px-3 py-2">
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Customer</label>
                            <div class="fw-bold">{{ $order->user->fullname }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <div class="fw-bold">{{ $order->user->email ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Phone</label>
                            <div class="fw-bold">{{ $order->user->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Order Date</label>
                            <div class="fw-bold">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Delivery Address</label>
                            <div class="fw-bold">{{ $order->address ?? 'No address' }}</div>
                        </div>
                        @if ($order->note)
                            <div class="col-12">
                                <label class="text-muted small">Note</label>
                                <div class="text-muted fst-italic">{{ $order->note }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Products</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Product</th>
                                    <th width="10%" class="text-center">Qty</th>
                                    <th width="15%" class="text-end">Unit Price</th>
                                    <th width="15%" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $index => $detail)
                                    <tr>
                                        <td class="align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                @if ($detail->product && $detail->product->image)
                                                    <img src="{{ asset('storage/' . $detail->product->image) }}"
                                                        alt="{{ $detail->product_name }}" class="rounded me-2"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $detail->product_name }}</div>
                                                    <small class="text-muted">ID: #{{ $detail->product_id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-primary">{{ $detail->quantity }}</span>
                                        </td>
                                        <td class="text-end align-middle">{{ $detail->formatted_unit_price }}</td>
                                        <td class="text-end align-middle">
                                            <strong class="text-success">{{ $detail->formatted_total_price }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Products:</span>
                        <span class="fw-bold">{{ $order->orderDetails->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Items:</span>
                        <span class="fw-bold">{{ $order->total_items }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">TOTAL:</h5>
                        <h4 class="mb-0 text-success">{{ $order->formatted_total_amount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: 1px solid #e9ecef;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
        }

        .card-header h5 {
            font-size: 1rem;
            font-weight: 600;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
        }

        @media (max-width: 991px) {
            .sticky-top {
                position: relative !important;
                top: 0 !important;
            }
        }
    </style>
@endpush
