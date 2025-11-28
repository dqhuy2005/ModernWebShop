@extends('layouts.admin.app')

@section('title', 'Edit Category')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Category
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" id="category-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $category->name) }}" placeholder="Enter category name"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label fw-bold">
                                Slug
                            </label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                                name="slug" value="{{ old('slug', $category->slug) }}" placeholder="category-slug">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Category Image</label>
                            <div class="custom-file-upload">
                                <div id="image-preview" class="text-center {{ $category->image ? '' : 'd-none' }} mb-2" style="position: relative;">
                                    <img src="{{ $category->image_url }}"
                                         alt="Preview"
                                         class="img-fluid rounded"
                                         style="max-height: 300px; padding: 4px; border: 1px solid #ddd;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                        onclick="removeImage()" style="margin: 5px;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="image" name="image" value="{{ old('image', $category->image) }}" class="@error('image') is-invalid @enderror">
                                <button type="button" class="btn-select-image w-100 mt-3 lfm-btn" data-input="image" data-preview="holder">
                                    <i class="fas fa-image me-2"></i>Select Image
                                </button>
                            </div>
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#name').on('input', function() {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .replace(/Đ/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#slug').val(slug);
        });

        function removeImage() {
            $('#image').val('');
            $('#image-preview').addClass('d-none');
            $('#image-preview img').attr('src', '');
        }

        $(document).ready(function() {
            $('.lfm-btn').filemanager('image', {prefix: '/admin/filemanager'});

            $('#image').on('change', function() {
                const imagePath = $(this).val();
                if (imagePath) {
                    const imageUrl = imagePath;
                    $('#image-preview img').attr('src', imageUrl);
                    $('#image-preview').removeClass('d-none');
                }
            });

            $('#name, #slug').on('input', function() {
                $(this).removeClass('is-invalid');
            });

            $('#category-form').on('submit', function(e) {
                let isValid = true;

                if ($('#name').val().trim() === '') {
                    isValid = false;
                    $('#name').addClass('is-invalid');
                    if (!$('#name').next('.invalid-feedback').length) {
                        $('#name').after('<div class="invalid-feedback d-block">Category name is required.</div>');
                    }
                } else {
                    $('#name').removeClass('is-invalid');
                    $('#name').next('.invalid-feedback').remove();
                }

                const slug = $('#slug').val().trim();
                if (slug !== '' && !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slug)) {
                    isValid = false;
                    $('#slug').addClass('is-invalid');
                    if (!$('#slug').next('.invalid-feedback').length) {
                        $('#slug').after('<div class="invalid-feedback d-block">Slug must contain only lowercase letters, numbers, and hyphens.</div>');
                    }
                } else {
                    $('#slug').removeClass('is-invalid');
                    $('#slug').next('.invalid-feedback').remove();
                }

                return isValid;
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .btn-select-image {
            font-weight: 600;
            font-size: 16px;
            padding: 15px;
            border: none;
            background-color: #4b8df8;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-select-image i {
            font-size: 18px;
        }

        .custom-file-upload {
            margin-bottom: 10px;
        }

        #image-preview img {
            transition: all 0.3s ease;
        }
    </style>
@endpush
