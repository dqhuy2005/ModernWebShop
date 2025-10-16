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
            <div class="col-lg-12">
                <div class="card mb-4">
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
                            <label for="name" class="form-label">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="Enter product name..." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5" placeholder="Enter product description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-list me-1"></i>Specifications
                            </label>
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

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status"
                                    value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hot Product</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_hot" name="is_hot"
                                    value="1" {{ old('is_hot') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_hot">
                                    Mark as Hot
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                Product Image
                            </label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                name="image" accept="image/*" onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Accepted: JPG, PNG, GIF, WEBP. Max: 2MB
                            </small>
                        </div>

                        <div id="image-preview" class="text-center d-none">
                            <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                <i class="fas fa-times me-1"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end col-lg-12 gap-3">
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
    </form>
@endsection

@push('scripts')
    <script>
        let specIndex = 1;

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
            $(this).closest('.specification-row').remove();
        });

        // Image Preview
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

        // Remove Image
        function removeImage() {
            $('#image').val('');
            $('#image-preview').addClass('d-none');
            $('#image-preview img').attr('src', '');
        }

        // Form Validation
        $('#productForm').on('submit', function(e) {
            let isValid = true;

            // Check required fields
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

        #image-preview img {
            border: 2px solid #dee2e6;
        }
    </style>
@endpush
