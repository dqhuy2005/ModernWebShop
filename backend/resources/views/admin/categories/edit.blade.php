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
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Category Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" id="category-form">
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
                                Slug <small class="text-muted fw-normal">(Auto-generated if empty)</small>
                            </label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                                name="slug" value="{{ old('slug', $category->slug) }}" placeholder="category-slug">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label fw-bold">Parent Category</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id"
                                name="parent_id">
                                <option value="">-- No Parent (Root Category) --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Category Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Products:</span>
                        <strong>{{ $category->products()->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Created:</span>
                        <strong>{{ $category->created_at->format('d/m/Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Last Updated:</span>
                        <strong>{{ $category->updated_at->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Tips
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <i class="fas fa-check text-success me-1"></i>
                        Update the category name carefully as it affects product organization.
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="fas fa-check text-success me-1"></i>
                        Keep the slug URL-friendly for better SEO.
                    </p>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-check text-success me-1"></i>
                        High-quality images improve category visibility.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-generate slug from name
            $('#name').on('input', function() {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/--+/g, '-')
                    .trim();
                $('#slug').val(slug);
            });
        });
    </script>
@endpush
