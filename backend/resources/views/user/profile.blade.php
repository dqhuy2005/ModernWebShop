@extends('layouts.user.app')

@section('title', 'Thông tin cá nhân - ModernWebShop')

@section('content')
    <div class="profile-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4 text-center">
                            <div class="profile-avatar mb-3 position-relative">
                                <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('assets/imgs/default-avatar.png') }}"
                                    alt="Avatar" class="rounded-circle" id="avatarPreview"
                                    style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #dc3545;">
                                <label for="imageInput"
                                    class="position-absolute bottom-0 end-0 bg-danger text-white rounded-circle"
                                    style="width: 35px; height: 35px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="imageInput" accept="image/*" class="d-none">
                            </div>
                            <h5 class="fw-bold mb-1" style="color: #202732;">{{ $user->fullname }}</h5>
                            <p class="text-muted small mb-3">{{ $user->email }}</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('profile.index') }}" class="btn btn-sm btn-danger active">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                                <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key me-2"></i>Đổi mật khẩu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4" style="color: #202732;">
                                Thông tin cá nhân
                            </h5>

                            <form id="profileForm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="fullname" class="form-label fw-semibold">
                                            Họ và tên <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="fullname" name="fullname"
                                            value="{{ $user->fullname }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control" id="email"
                                            value="{{ $user->email }}" readonly>
                                        <small class="text-muted">Email không thể thay đổi</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="{{ $user->phone ?? '' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="birthday" class="form-label fw-semibold">Ngày sinh</label>
                                        <input type="date" class="form-control" id="birthday" name="birthday"
                                            value="{{ $user->birthday ?? '' }}">
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"
                                            placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ $user->address ?? '' }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <small class="text-muted">
                                            Tham gia: {{ $user->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>

                                    <div class="col-12">
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <button type="submit" class="btn btn-danger px-4" id="saveBtn">
                                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-key me-2"></i>Đổi mật khẩu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">
                                Mật khẩu hiện tại <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-semibold">
                                Mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required
                                minlength="8">
                            <small class="text-muted">Tối thiểu 8 ký tự</small>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label fw-semibold">
                                Xác nhận mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" required minlength="8">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="changePasswordBtn">
                        <i class="fas fa-check me-2"></i>Đổi mật khẩu
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .btn-danger.active {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        .profile-avatar {
            display: inline-block;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#imageInput').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 2048 * 1024) {
                        toastr.error('Kích thước ảnh không được vượt quá 2MB');
                        return;
                    }

                    if (!file.type.match('image.*')) {
                        toastr.error('Vui lòng chọn file ảnh');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#avatarPreview').attr('src', event.target.result);
                    };
                    reader.readAsDataURL(file);

                    submitProfileForm();
                }
            });

            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                submitProfileForm();
            });

            function submitProfileForm() {
                const $saveBtn = $('#saveBtn');
                const originalText = $saveBtn.html();
                $saveBtn.prop('disabled', true);
                $saveBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...');

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('fullname', $('#fullname').val());
                formData.append('phone', $('#phone').val());
                formData.append('address', $('#address').val());
                formData.append('birthday', $('#birthday').val());

                const imageFile = $('#imageInput')[0].files[0];
                if (imageFile) {
                    formData.append('image', imageFile);
                }

                $.ajax({
                    url: '{{ route('profile.update') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);

                            if (response.image_url) {
                                $('#avatarPreview').attr('src', response.image_url);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    },
                    complete: function() {
                        $saveBtn.prop('disabled', false);
                        $saveBtn.html(originalText);
                    }
                });
            }

            $('#changePasswordBtn').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();

                if (!$('#current_password').val() || !$('#new_password').val() || !$(
                        '#new_password_confirmation').val()) {
                    toastr.error('Vui lòng điền đầy đủ thông tin');
                    return;
                }

                if ($('#new_password').val() !== $('#new_password_confirmation').val()) {
                    toastr.error('Mật khẩu xác nhận không khớp');
                    return;
                }

                $btn.prop('disabled', true);
                $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

                $.ajax({
                    url: '{{ route('profile.change-password') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        current_password: $('#current_password').val(),
                        new_password: $('#new_password').val(),
                        new_password_confirmation: $('#new_password_confirmation').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#changePasswordModal').modal('hide');
                            $('#changePasswordForm')[0].reset();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 400) {
                            toastr.error(xhr.responseJSON.message);
                        } else if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.html(originalText);
                    }
                });
            });

            $('#phone').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                $(this).val(value);
            });
        });
    </script>
@endpush
