@extends('layouts.admin.app')

@section('title', 'Category Details')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-list me-2"></i>Category Details
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Category Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td width="30%" class="text-muted fw-bold">ID:</td>
                                <td>{{ $category->id }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Name:</td>
                                <td><strong>{{ $category->name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Slug:</td>
                                <td><code class="text-muted">{{ $category->slug }}</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Parent Category:</td>
                                <td>
                                    @if ($category->parent)
                                        <span class="badge bg-info">{{ $category->parent->name }}</span>
                                    @else
                                        <span class="text-muted">Root Category</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Status:</td>
                                <td>
                                    @if ($category->deleted_at)
                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Created At:</td>
                                <td>{{ $category->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold">Updated At:</td>
                                <td>{{ $category->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($category->products->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-box me-2"></i>Products ({{ $category->products->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="15%">Image</th>
                                        <th width="35%">Name</th>
                                        <th width="20%">Price</th>
                                        <th width="20%" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category->products as $product)
                                        <tr>
                                            <td><strong>{{ $product->id }}</strong></td>
                                            <td>
                                                @if ($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                        alt="{{ $product->name }}" class="img-thumbnail"
                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                                        style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                    class="text-decoration-none">
                                                    <strong>{{ $product->name }}</strong>
                                                </a>
                                            </td>
                                            <td>
                                                <strong class="text-primary">
                                                    {{ number_format($product->price, 0, ',', '.') }} VND
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                @if ($product->status)
                                                    <span class="badge bg-success">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <div class="text-muted small">Total Products</div>
                            <h3 class="mb-0 text-primary">{{ $category->products->count() }}</h3>
                        </div>
                        <div class="stats-icon bg-primary-soft">
                            <i class="fas fa-box text-primary"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <div class="text-muted small">Active Products</div>
                            <h3 class="mb-0 text-success">{{ $category->products->where('status', 1)->count() }}</h3>
                        </div>
                        <div class="stats-icon bg-success-soft">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Inactive Products</div>
                            <h3 class="mb-0 text-secondary">{{ $category->products->where('status', 0)->count() }}</h3>
                        </div>
                        <div class="stats-icon bg-secondary-soft">
                            <i class="fas fa-times-circle text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if ($category->children && $category->children->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-sitemap me-2"></i>Sub Categories
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($category->children as $child)
                                <li class="list-group-item px-0">
                                    <a href="{{ route('admin.categories.show', $child) }}"
                                        class="text-decoration-none">
                                        <i class="fas fa-angle-right me-2"></i>{{ $child->name }}
                                    </a>
                                    <span class="badge bg-info float-end">
                                        {{ $child->products->count() }} products
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-secondary-soft {
            background-color: rgba(108, 117, 125, 0.1);
        }

        .table-borderless td {
            padding: 0.75rem 0;
        }
    </style>
@endpush
