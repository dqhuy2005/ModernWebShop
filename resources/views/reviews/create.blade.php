@extends('layouts.user.app')

@section('title', 'Đánh giá sản phẩm - ' . $product->name)

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchase.index') }}">Đơn hàng</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchase.show', $order->id) }}">Đơn hàng
                                #{{ $order->id }}</a></li>
                        <li class="breadcrumb-item active">Đánh giá sản phẩm</li>
                    </ol>
                </nav>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            <div>
                                <h5 class="mb-1">{{ $product->name }}</h5>
                                <p class="text-muted mb-0">Đơn hàng: #{{ $order->id }}</p>
                                <p class="text-success mb-0"><small>✓ Đã mua hàng</small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-star me-2"></i>Đánh giá sản phẩm
                        </h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data"
                            id="reviewForm">
                            @csrf

                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold required">
                                    Đánh giá của bạn <span class="text-danger">*</span>
                                </label>
                                <div class="rating-input">
                                    <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating', 5) }}">
                                    <div class="star-rating">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <i class="star fas fa-star" data-rating="{{ $i }}"></i>
                                        @endfor
                                    </div>
                                    <span class="rating-text ms-3">Tuyệt vời</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">
                                    Tiêu đề đánh giá (Tùy chọn)
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" maxlength="200"
                                    placeholder="VD: Sản phẩm rất tốt, đáng giá tiền">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label fw-bold required">
                                    Nội dung đánh giá <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="5"
                                    minlength="10" maxlength="2000" placeholder="Hãy chia sẻ trải nghiệm của bạn về sản phẩm này (tối thiểu 10 ký tự)"
                                    required>{{ old('comment') }}</textarea>
                                <small class="text-muted">
                                    <span id="charCount">0</span>/2000 ký tự
                                </small>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Thêm hình ảnh (Tùy chọn)
                                </label>
                                <div class="upload-box" id="imageUploadBox">
                                    <input type="file" name="images[]" id="imageInput"
                                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple
                                        class="d-none">
                                    <label for="imageInput" class="upload-label">
                                        <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                        <p class="mb-0">Nhấn để chọn hình ảnh</p>
                                        <small class="text-muted">Tối đa 5 ảnh, mỗi ảnh không quá 2MB</small>
                                    </label>
                                </div>
                                <div id="imagePreview" class="image-preview mt-2"></div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Thêm video (Tùy chọn)
                                </label>
                                <div class="upload-box" id="videoUploadBox">
                                    <input type="file" name="videos[]" id="videoInput"
                                        accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" multiple
                                        class="d-none">
                                    <label for="videoInput" class="upload-label">
                                        <i class="fas fa-video fa-3x text-muted mb-2"></i>
                                        <p class="mb-0">Nhấn để chọn video</p>
                                        <small class="text-muted">Tối đa 2 video, mỗi video không quá 10MB</small>
                                    </label>
                                </div>
                                <div id="videoPreview" class="video-preview mt-2"></div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                </button>
                                <a href="{{ route('purchase.show', $order->id) }}"
                                    class="btn btn-outline-secondary btn-lg">
                                    Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .star-rating {
                display: inline-flex;
                flex-direction: row-reverse;
                font-size: 2rem;
                cursor: pointer;
            }

            .star-rating .star {
                color: #ddd;
                transition: color 0.2s;
            }

            .star-rating .star:hover,
            .star-rating .star:hover~.star,
            .star-rating .star.active,
            .star-rating .star.active~.star {
                color: #ffc107;
            }

            .rating-text {
                font-size: 1.1rem;
                font-weight: 500;
                color: #666;
            }

            .upload-box {
                border: 2px dashed #ddd;
                border-radius: 8px;
                padding: 30px;
                text-align: center;
                transition: all 0.3s;
                cursor: pointer;
            }

            .upload-box:hover {
                border-color: #007bff;
                background-color: #f8f9fa;
            }

            .upload-label {
                cursor: pointer;
                margin: 0;
            }

            .image-preview,
            .video-preview {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .preview-item {
                position: relative;
                width: 120px;
                height: 120px;
                border-radius: 8px;
                overflow: hidden;
                border: 2px solid #ddd;
            }

            .preview-item img,
            .preview-item video {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .preview-remove {
                position: absolute;
                top: 5px;
                right: 5px;
                background: rgba(255, 0, 0, 0.8);
                color: white;
                border: none;
                border-radius: 50%;
                width: 25px;
                height: 25px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .preview-remove:hover {
                background: rgba(255, 0, 0, 1);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Star Rating
            const stars = document.querySelectorAll('.star');
            const ratingValue = document.getElementById('ratingValue');
            const ratingText = document.querySelector('.rating-text');

            const ratingTexts = {
                5: 'Tuyệt vời',
                4: 'Hài lòng',
                3: 'Bình thường',
                2: 'Không hài lòng',
                1: 'Rất tệ'
            };

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    ratingValue.value = rating;
                    updateStars(rating);
                    ratingText.textContent = ratingTexts[rating];
                });
            });

            function updateStars(rating) {
                stars.forEach(star => {
                    if (star.getAttribute('data-rating') <= rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }

            // Initialize with default rating
            updateStars(ratingValue.value);

            // Character Counter
            const commentTextarea = document.getElementById('comment');
            const charCount = document.getElementById('charCount');

            commentTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });

            // Image Preview
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            let selectedImages = [];

            imageInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);

                if (selectedImages.length + files.length > 5) {
                    alert('Bạn chỉ có thể tải lên tối đa 5 hình ảnh');
                    return;
                }

                files.forEach((file, index) => {
                    if (file.size > 2 * 1024 * 1024) {
                        alert(`File ${file.name} vượt quá 2MB`);
                        return;
                    }

                    selectedImages.push(file);
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="preview-remove" data-index="${selectedImages.length - 1}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                        imagePreview.appendChild(div);
                    };

                    reader.readAsDataURL(file);
                });
            });

            // Remove Image
            imagePreview.addEventListener('click', function(e) {
                if (e.target.closest('.preview-remove')) {
                    const button = e.target.closest('.preview-remove');
                    const index = button.getAttribute('data-index');
                    button.closest('.preview-item').remove();
                    selectedImages.splice(index, 1);

                    // Update DataTransfer
                    const dt = new DataTransfer();
                    selectedImages.forEach(file => dt.items.add(file));
                    imageInput.files = dt.files;
                }
            });

            // Video Preview (similar logic)
            const videoInput = document.getElementById('videoInput');
            const videoPreview = document.getElementById('videoPreview');
            let selectedVideos = [];

            videoInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);

                if (selectedVideos.length + files.length > 2) {
                    alert('Bạn chỉ có thể tải lên tối đa 2 video');
                    return;
                }

                files.forEach((file) => {
                    if (file.size > 10 * 1024 * 1024) {
                        alert(`File ${file.name} vượt quá 10MB`);
                        return;
                    }

                    selectedVideos.push(file);
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                <video src="${URL.createObjectURL(file)}" controls></video>
                <button type="button" class="preview-remove" data-index="${selectedVideos.length - 1}">
                    <i class="fas fa-times"></i>
                </button>
            `;
                    videoPreview.appendChild(div);
                });
            });

            // Remove Video
            videoPreview.addEventListener('click', function(e) {
                if (e.target.closest('.preview-remove')) {
                    const button = e.target.closest('.preview-remove');
                    const index = button.getAttribute('data-index');
                    button.closest('.preview-item').remove();
                    selectedVideos.splice(index, 1);

                    const dt = new DataTransfer();
                    selectedVideos.forEach(file => dt.items.add(file));
                    videoInput.files = dt.files;
                }
            });

            // Form validation
            document.getElementById('reviewForm').addEventListener('submit', function(e) {
                if (commentTextarea.value.trim().length < 10) {
                    e.preventDefault();
                    alert('Nội dung đánh giá phải có ít nhất 10 ký tự');
                    commentTextarea.focus();
                }
            });
        </script>
    @endpush
@endsection
