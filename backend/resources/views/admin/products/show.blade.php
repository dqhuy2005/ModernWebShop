@extends('layouts.admin.app')

@section('title', 'Product Detail - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-box me-2"></i>Product Detail: {{ $product->name }}
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Product Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td width="200" class="fw-bold">Product ID:</td>
                                <td><span class="badge bg-primary">#{{ $product->id }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Product Name:</td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Category:</td>
                                <td>
                                    @if ($product->category)
                                        <span class="badge bg-info">
                                            <i class="fas fa-folder me-1"></i>
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No Category</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Description:</td>
                                <td>
                                    @if ($product->description)
                                        <div class="text-muted">{{ $product->description }}</div>
                                    @else
                                        <em class="text-muted">No description</em>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    @if ($product->status)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Hot Product:</td>
                                <td>
                                    @if ($product->is_hot)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-fire me-1"></i>Hot
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Normal</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Language:</td>
                                <td>
                                    @if ($product->language)
                                        <span class="badge bg-dark">{{ strtoupper($product->language) }}</span>
                                    @else
                                        <em class="text-muted">Not specified</em>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Views:</td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-eye me-1"></i>
                                        {{ number_format($product->views ?? 0) }}
                                    </span>
                                </td>
                            </tr>
                            @if ($product->parent_id)
                                <tr>
                                    <td class="fw-bold">Parent Product:</td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $product->parent_id) }}"
                                            class="text-decoration-none">
                                            <i class="fas fa-link me-1"></i>
                                            Product #{{ $product->parent_id }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($product->specifications)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Specifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Key</th>
                                        <th width="60%">Value</th>
                                    </tr>
                                </thead>

                                @php
                                    $product->specifications = json_decode($product->specifications, true);
                                @endphp

                                <tbody>
                                    @foreach ($product->specifications as $key => $value)
                                        <tr>
                                            <td class="fw-bold">{{ is_array($value) ? $value['key'] ?? $key : $key }}
                                            </td>
                                            <td>{{ is_array($value) ? $value['value'] ?? '' : $value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Timestamps
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-plus-circle fa-2x text-success"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Created At</small>
                                    <strong>{{ $product->created_at->format('d M Y, H:i') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $product->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-edit fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Last Updated</small>
                                    <strong>{{ $product->updated_at->format('d M Y, H:i') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $product->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image me-2"></i>Product Image
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                    @else
                        <div class="bg-light rounded p-5">
                            <i class="fas fa-image fa-4x text-muted"></i>
                            <p class="text-muted mt-3 mb-0">No image available</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-{{ $product->status ? 'success' : 'secondary' }}"
                            onclick="toggleStatus({{ $product->id }})">
                            <i class="fas fa-toggle-{{ $product->status ? 'on' : 'off' }} me-2"></i>
                            {{ $product->status ? 'Active' : 'Inactive' }}
                        </button>

                        <button type="button" class="btn btn-{{ $product->is_hot ? 'warning' : 'outline-warning' }}"
                            onclick="toggleHot({{ $product->id }}, this)">
                            <i class="fas fa-fire me-2"></i>
                            {{ $product->is_hot ? 'Hot Product' : 'Mark as Hot' }}
                        </button>

                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Product
                        </a>

                        <button type="button" class="btn btn-info" onclick="duplicateProduct({{ $product->id }})">
                            <i class="fas fa-copy me-2"></i>Duplicate
                        </button>

                        <button type="button" class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="fas fa-shopping-cart text-primary me-2"></i>
                            <span>In Carts</span>
                        </div>
                        <strong>{{ $product->carts->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <i class="fas fa-receipt text-success me-2"></i>
                            <span>Orders</span>
                        </div>
                        <strong>{{ $product->orderDetails->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-eye text-info me-2"></i>
                            <span>Total Views</span>
                        </div>
                        <strong>{{ number_format($product->views ?? 0) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle Status
        function toggleStatus(productId) {
            if (confirm('Are you sure you want to change the status of this product?')) {
                $.ajax({
                    url: '/admin/products/' + productId + '/toggle-status',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to update status!');
                    }
                });
            }
        }

        // Toggle Hot Status
        function toggleHot(productId, button) {
            $.ajax({
                url: '/admin/products/' + productId + '/toggle-hot',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to toggle hot status!');
                }
            });
        }

        // Delete Product
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone!')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/products/' + productId;

                var methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                var tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                form.appendChild(tokenInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Duplicate Product
        function duplicateProduct(productId) {
            toastr.info('Duplicate feature coming soon!');
        }
    </script>
@endpush

@push('styles')
    <style>
        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .table-borderless td {
            padding: 0.75rem;
        }

        .table-borderless tr:not(:last-child) {
            border-bottom: 1px solid #f1f1f1;
        }

        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }
    </style>
@endpush
