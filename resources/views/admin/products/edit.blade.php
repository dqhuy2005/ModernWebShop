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
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
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
                            <label for="slug" class="form-label fw-bold">
                                Slug <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                                name="slug" value="{{ old('slug', $product->slug) }}" placeholder="product-slug-here" required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">
                                Price (VNĐ) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('price') is-invalid @enderror"
                                    id="price_display" placeholder="Nhập giá sản phẩm..."
                                    value="{{ old('price') ? number_format(old('price'), 0, ',', '.') : number_format($product->price ?? 0, 0, ',', '.') }}">
                                <input type="hidden" id="price" name="price"
                                    value="{{ old('price', $product->price ?? 0) }}">
                                <span class="input-group-text">₫</span>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">
                                Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5" placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Specifications
                            </label>
                            <div id="specifications-container">
                                @php
                                    $specs = old('specifications');
                                    if (!$specs && $product->specifications) {
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
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status"
                                    name="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hot Product</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_hot"
                                    name="is_hot" value="1" {{ old('is_hot', $product->is_hot) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <label for="" class="form-label fw-bold">Product Images</label>

                        <div class="mb-3">
                            <div class="existing-images d-flex gap-2 flex-wrap mb-2">
                                @foreach ($product->images as $img)
                                    <div class="img-thumb text-center position-relative" id="image-{{ $img->id }}">
                                        <img src="{{ asset('storage/' . $img->path) }}" alt="{{ $product->name }}"
                                            class="img-fluid rounded" style="max-height:120px; border:1px solid #ddd; padding:4px;" />
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image-btn"
                                            data-image-id="{{ $img->id }}"
                                            data-product-id="{{ $product->id }}"
                                            style="padding: 2px 6px; font-size: 12px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <small class="d-block text-muted mt-1">Order: {{ $img->sort_order }}</small>
                                    </div>
                                @endforeach
                            </div>

                            <div class="custom-file-upload">
                                <div id="images-preview" class="d-flex gap-2 flex-wrap"></div>

                                <input type="file" class="d-none @error('images.*') is-invalid @enderror" id="images"
                                    name="images[]" accept="image/*" multiple onchange="previewImages(event)">
                                <button type="button" class="btn-select-image w-100 mt-3"
                                    onclick="$('#images').trigger('click')">
                                    Select images
                                </button>
                            </div>

                            @error('images.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

    <script>
        let descriptionEditor;

        ClassicEditor
            .create(document.querySelector('#description'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'alignment', '|',
                        'numberedList', 'bulletedList', '|',
                        'outdent', 'indent', '|',
                        'link', 'blockQuote', 'insertTable', '|',
                        'imageUpload', 'mediaEmbed', '|',
                        'undo', 'redo', '|',
                        'code', 'codeBlock', 'htmlEmbed'
                    ],
                    shouldNotGroupWhenFull: true
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                    ]
                },
                fontSize: {
                    options: [
                        'tiny', 'small', 'default', 'big', 'huge'
                    ]
                },
                alignment: {
                    options: ['left', 'center', 'right', 'justify']
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                },
                image: {
                    toolbar: [
                        'imageStyle:inline',
                        'imageStyle:block',
                        'imageStyle:side',
                        '|',
                        'toggleImageCaption',
                        'imageTextAlternative'
                    ]
                },
                link: {
                    decorators: {
                        openInNewTab: {
                            mode: 'manual',
                            label: 'Open in a new tab',
                            attributes: {
                                target: '_blank',
                                rel: 'noopener noreferrer'
                            }
                        }
                    }
                },
                placeholder: 'Enter detailed product description here...',
                language: 'en'
            })
            .then(editor => {
                descriptionEditor = editor;
                console.log('CKEditor initialized successfully!');

                editor.model.document.on('change:data', () => {
                    document.querySelector('#description').value = editor.getData();
                });
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });

        let specIndex = {{ $specCount ?? 1 }};

        $('#name').on('input', function() {
            const name = $(this).val();
            const slug = name
                .toLowerCase()
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

        function formatPrice(value) {
            const numericValue = value.replace(/[^0-9]/g, '');
            if (!numericValue) return '';
            return new Intl.NumberFormat('vi-VN').format(parseInt(numericValue));
        }

        function validatePrice(value) {
            const price = parseInt(value.replace(/[^0-9]/g, ''));
            if (isNaN(price)) return {
                valid: false,
                error: 'Giá phải là số'
            };
            if (price < 0) return {
                valid: false,
                error: 'Giá phải là số dương'
            };
            if (price > 999999999) return {
                valid: false,
                error: 'Giá vượt quá giới hạn'
            };
            return {
                valid: true,
                value: price
            };
        }

        function displayFormattedPrice(price) {
            if (price === 0 || !price) return 'Liên hệ';
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
            if ($('.specification-row').length > 1) {
                $(this).closest('.specification-row').remove();
            }
        });

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

        function previewImages(event) {
            const files = event.target.files;
            $('#images-preview').empty();
            if (!files || !files.length) return;

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const $img = $(`<div class="img-thumb"><img src="${e.target.result}" class="img-fluid rounded" style="max-height:120px; border:1px solid #ddd; padding:4px;"/></div>`);
                    $('#images-preview').append($img);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeImage() {
            $('#image').val('');
            $('#image-preview').addClass('d-none');
            $('#image-preview img').attr('src', '');
            $('#current-image').css('opacity', '1');
        }

        $('#productForm').on('submit', function(e) {
            let isValid = true;

            if (descriptionEditor) {
                document.querySelector('#description').value = descriptionEditor.getData();
            }

            $('#name, #slug, #category_id, #price_display').removeClass('is-invalid');

            if (!$('#name').val().trim()) {
                isValid = false;
                $('#name').addClass('is-invalid');
            }

            if (!$('#slug').val().trim()) {
                isValid = false;
                $('#slug').addClass('is-invalid');
                toastr.error('Slug is required!');
            }

            if (!$('#category_id').val()) {
                isValid = false;
                $('#category_id').addClass('is-invalid');
            }

            const priceValue = parseInt($('#price').val());
            if (isNaN(priceValue) || priceValue < 0) {
                isValid = false;
                $('#price_display').addClass('is-invalid');
                toastr.error('Vui lòng nhập giá sản phẩm hợp lệ!');
            }

            if (!isValid) {
                e.preventDefault();
                toastr.error('Please fill in all required fields!');
            }
        });

        $('.delete-image-btn').on('click', function(e) {
            e.preventDefault();
            const imageId = $(this).data('image-id');
            const productId = $(this).data('product-id');
            const $imageItem = $(`#image-${imageId}`);

            if (!confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
                return;
            }

            $.ajax({
                url: `/admin/products/${productId}/images/${imageId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $imageItem.css('opacity', '0.5');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Image deleted successfully!');
                        $imageItem.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        toastr.error(response.message || 'Failed to delete image');
                        $imageItem.css('opacity', '1');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred while deleting the image');
                    $imageItem.css('opacity', '1');
                }
            });
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

        .btn-select-image {
            font-weight: 600;
            font-size: 16px;
            padding: 15px 30px;
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

        /* CKEditor Custom Styles */
        .ck-editor__editable {
            min-height: 300px;
            max-height: 500px;
            overflow-y: auto;
        }

        .ck.ck-editor__main > .ck-editor__editable {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .ck.ck-editor__main > .ck-editor__editable:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .ck.ck-toolbar {
            border: 1px solid #dee2e6 !important;
            border-bottom: none !important;
            background-color: #f8f9fa !important;
            border-radius: 0.375rem 0.375rem 0 0 !important;
        }

        .ck.ck-toolbar .ck-toolbar__items {
            flex-wrap: wrap;
        }

        /* Hide original textarea */
        #description {
            display: none;
        }
    </style>
@endpush
