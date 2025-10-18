@extends('layouts.admin.app')

@section('title', 'Edit Product - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Product: {{ $product->name }}
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>View Detail
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
        id="productForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Product Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $product->name) }}" placeholder="Enter product name..."
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5" placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-list me-1"></i>Specifications
                            </label>
                            <div id="specifications-container">
                                @php
                                    // Parse specifications correctly
                                    $specs = old('specifications');
                                    if (!$specs && $product->specifications) {
                                        // If specifications is a JSON string, decode it
                                        if (is_string($product->specifications)) {
                                            $specs = json_decode($product->specifications, true);
                                        } else {
                                            $specs = $product->specifications;
                                        }
                                    }
                                    $specCount = 0;
                                @endphp

                                @if (is_array($specs) && count($specs) > 0)
                                    @foreach ($specs as $key => $value)
                                        <div class="row g-2 mb-2 specification-row">
                                            <div class="col-5">
                                                <input type="text" class="form-control"
                                                    name="specifications[{{ $specCount }}][key]"
                                                    value="{{ is_array($value) ? $value['key'] ?? $key : $key }}"
                                                    placeholder="Key">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" class="form-control"
                                                    name="specifications[{{ $specCount }}][value]"
                                                    value="{{ is_array($value) ? $value['value'] ?? '' : $value }}"
                                                    placeholder="Value">
                                            </div>
                                            <div class="col-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-spec">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @php $specCount++; @endphp
                                    @endforeach
                                @else
                                    <div class="row g-2 mb-2 specification-row">
                                        <div class="col-5">
                                            <input type="text" class="form-control" name="specifications[0][key]"
                                                placeholder="Key">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" class="form-control" name="specifications[0][value]"
                                                placeholder="Value">
                                        </div>
                                        <div class="col-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-spec" disabled>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-specification">
                                <i class="fas fa-plus me-1"></i>Add Specification
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                name="category_id" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status"
                                    name="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hot Product</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_hot"
                                    name="is_hot" value="1" {{ old('is_hot', $product->is_hot) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image me-2"></i>Product Image
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($product->image)
                            <div class="mb-3 text-center">
                                <label class="form-label d-block">Current Image</label>
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="img-fluid rounded" style="max-height: 200px; border: 2px solid #dee2e6;"
                                    id="current-image">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                {{ $product->image ? 'Change Image' : 'Upload Image' }}
                            </label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Accepted: JPG, PNG, GIF, WEBP. Max: 2MB
                            </small>
                        </div>

                        <div id="image-preview" class="text-center d-none">
                            <div class="d-flex flex-column align-items-center">
                                <label class="form-label d-block">New Image Preview</label>
                                <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                    <i class="fas fa-times me-1"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        let specIndex = {{ $specCount ?? 1 }};

        // Add Specification Row
        $('#add-specification').on('click', function() {
            const newRow = `
            <div class="row g-2 mb-2 specification-row">
                <div class="col-5">
                    <input type="text" class="form-control" name="specifications[${specIndex}][key]" placeholder="Key">
                </div>
                <div class="col-6">
                    <input type="text" class="form-control" name="specifications[${specIndex}][value]" placeholder="Value">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm remove-spec">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
            $('#specifications-container').append(newRow);
            specIndex++;
        });

        // Remove Specification Row
        $(document).on('click', '.remove-spec', function() {
            if ($('.specification-row').length > 1) {
                $(this).closest('.specification-row').remove();
            }
        });

        // Image Preview
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview img').attr('src', e.target.result);
                    $('#image-preview').removeClass('d-none');
                    $('#current-image').css('opacity', '0.5');
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove Image
        function removeImage() {
            $('#image').val('');
            $('#image-preview').addClass('d-none');
            $('#image-preview img').attr('src', '');
            $('#current-image').css('opacity', '1');
        }

        // Form Validation
        $('#productForm').on('submit', function(e) {
            let isValid = true;

            if (!$('#name').val().trim()) {
                isValid = false;
                $('#name').addClass('is-invalid');
            }

            if (!$('#category_id').val()) {
                isValid = false;
                $('#category_id').addClass('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                toastr.error('Please fill in all required fields!');
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .specification-row {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        #image-preview img,
        #current-image {
            border: 2px solid #dee2e6;
            transition: opacity 0.3s ease;
        }
    </style>
@endpush
