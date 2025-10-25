@extends('layouts.admin.app')

@section('title', 'Create New Product - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Create New Product
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="Enter product name..." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">
                                Price (VNĐ) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('price') is-invalid @enderror"
                                    id="price_display" placeholder="Enter price..."
                                    value="{{ old('price') ? number_format(old('price'), 0, ',', '.') : '' }}">
                                <input type="hidden" id="price" name="price" value="{{ old('price', 0) }}">
                                <span class="input-group-text">₫</span>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5" placeholder="Enter product description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Specifications</label>
                            <div id="specifications-container">
                                <div class="row g-2 mb-2 specification-row">
                                    <div class="col-5">
                                        <input type="text" class="form-control" name="specifications[0][key]"
                                            placeholder="Key (e.g., Color)">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" name="specifications[0][value]"
                                            placeholder="Value (e.g., Red)">
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-spec" disabled>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
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
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                name="category_id" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status"
                                    value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hot Product</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_hot"
                                    name="is_hot" value="1" {{ old('is_hot') ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Product Image</label>
                            <div class="custom-file-upload">
                                <div id="image-preview" class="text-center d-none">
                                    <img src="" alt="Preview" class="img-fluid rounded"
                                        style="max-height: 300px; padding: 4px; border: 1px solid #ddd;">
                                </div>
                                <input type="file" class="d-none @error('image') is-invalid @enderror" id="image"
                                    name="image" accept="image/*" onchange="previewImage(event)">
                                <button type="button" class="btn-select-image w-100 mt-3"
                                    onclick="document.getElementById('image').click()">
                                    Select image
                                </button>
                            </div>

                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary" name="action" value="save">
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                    <button type="submit" class="btn btn-success" name="action" value="save_and_continue">
                        <i class="fas fa-plus me-2"></i>Save & Add Another
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
        let specIndex = 1;

        function formatPrice(value) {
            const numericValue = value.replace(/[^0-9]/g, '');

            if (!numericValue) {
                return '';
            }

            return new Intl.NumberFormat('vi-VN').format(parseInt(numericValue));
        }

        function validatePrice(value) {
            const price = parseInt(value.replace(/[^0-9]/g, ''));

            if (isNaN(price)) {
                return {
                    valid: false,
                    error: 'Price must be a number'
                };
            }

            if (price < 0) {
                return {
                    valid: false,
                    error: 'Price must be a positive number'
                };
            }

            if (price > 999999999) {
                return {
                    valid: false,
                    error: 'Price exceeds limit (999,999,999 ₫)'
                };
            }

            return {
                valid: true,
                value: price
            };
        }

        function displayFormattedPrice(price) {
            if (price === 0 || !price) {
                return 'Liên hệ';
            }
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(price);
        }

        $('#price_display').on('input', function() {
            const value = $(this).val();
            const formatted = formatPrice(value);

            $(this).val(formatted);

            const validation = validatePrice(formatted);

            if (validation.valid) {
                $('#price').val(validation.value);
                $('#price-formatted').text(displayFormattedPrice(validation.value));
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else if (formatted === '') {
                $('#price').val(0);
                $(this).removeClass('is-invalid is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });

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

        $(document).on('click', '.remove-spec', function() {
            $(this).closest('.specification-row').remove();
        });

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview img').attr('src', e.target.result);
                    $('#image-preview').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            $('#image').val('');
            $('#image-preview').addClass('d-none');
            $('#image-preview img').attr('src', '');
        }

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

            const priceValue = parseInt($('#price').val());
            if (isNaN(priceValue) || priceValue < 0) {
                isValid = false;
                $('#price_display').addClass('is-invalid');
                toastr.error('Please enter a valid product price!');
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

        .btn-select-image {
            font-weight: 600;
            font-size: 16px;
            padding: 15px;
            border: none;
            background-color: #4b8df8;
            color: #fff;
        }

        .btn-select-image i {
            font-size: 18px;
        }

        .custom-file-upload {
            margin-bottom: 10px;
        }
    </style>
@endpush
