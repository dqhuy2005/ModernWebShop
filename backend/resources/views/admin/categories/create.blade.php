@extends('layouts.admin.app')

@section('title', 'Create Category')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-plus me-2"></i>Create New Category
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
                    <form action="{{ route('admin.categories.store') }}" method="POST" id="category-form">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="Enter category name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label fw-bold">
                                Slug
                            </label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                                name="slug" value="{{ old('slug') }}" placeholder="category-slug">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="language" class="form-label fw-bold">
                                Language
                            </label>
                            <select class="form-select @error('language') is-invalid @enderror" id="language"
                                name="language">
                                <option value="">Default</option>
                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>Tiếng Anh</option>
                                <option value="vi" {{ old('language') == 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create
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
        $(document).ready(function() {
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
