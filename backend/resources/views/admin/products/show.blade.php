@extends('layouts.admin.app')

@section('title', 'Product Detail - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="border-header">
                <h1 class="h3 mb-0 border-header-name">
                    <i class="fas fa-box me-2"></i>Product Detail: {{ $product->name }}
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
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
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Inactive
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
                            <tr>
                                <td class="fw-bold">Created At:</td>
                                <td>
                                    <i class="fas fa-calendar-plus text-success me-2"></i>
                                    <strong>{{ $product->created_at->format('d M Y, H:i') }}</strong>
                                    <span class="text-muted ms-2">({{ $product->created_at->diffForHumans() }})</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Last Updated:</td>
                                <td>
                                    <i class="fas fa-calendar-edit text-warning me-2"></i>
                                    <strong>{{ $product->updated_at->format('d M Y, H:i') }}</strong>
                                    <span class="text-muted ms-2">({{ $product->updated_at->diffForHumans() }})</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @if (
                $product->specifications &&
                    (is_array($product->specifications) ? count($product->specifications) > 0 : !empty($product->specifications)))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Specifications</h5>
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
                                    // Parse specifications safely
                                    $specs = $product->specifications;
                                    if (is_string($specs)) {
                                        $specs = json_decode($specs, true);
                                    }
                                    if (!is_array($specs)) {
                                        $specs = [];
                                    }
                                @endphp

                                <tbody>
                                    @forelse ($specs as $key => $value)
                                        <tr>
                                            <td class="fw-bold">{{ is_array($value) ? $value['key'] ?? $key : $key }}
                                            </td>
                                            <td>{{ is_array($value) ? $value['value'] ?? '' : $value }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">
                                                <em>No specifications available</em>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">

                <div class="card-body">
                    <label class="form-label fw-bold text-start">Product Image</label>

                    <div class="text-center">
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
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .border-header .border-header-name {
            max-width: 1200px;
        }

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
