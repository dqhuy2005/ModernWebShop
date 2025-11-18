@extends('layouts.user.app')

@section('title', 'Thông tin cá nhân - ModernWebShop')

@section('content')
    <div class="profile-section py-5" style="background-color: #F8F9FA;">
        <div class="container">
            <div class="row mb-3">
                <div class="col-lg-3 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-4 text-center">
                            <div class="profile-avatar mb-3 position-relative">

                                @if ($user->image)
                                    {{-- Prioritize uploaded image from database --}}
                                    <img src="{{ $user->image_url }}" alt="Avatar" class="rounded-circle" id="avatarPreview"
                                        style="width: 120px; height: 120px; object-fit: cover;"
                                        onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-placeholder rounded-circle d-none align-items-center justify-content-center"
                                        style="width: 120px; height: 120px; background-color: #f0f0f0; color: #6c757d; font-size: 48px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @elseif ($user->isOAuthUser())
                                    {{-- Fallback to OAuth avatar if no uploaded image --}}
                                    <img src="{{ $user->oauthAccounts->first()->avatar ?? $user->image }}" alt="Avatar" class="rounded-circle" id="avatarPreview"
                                        style="width: 120px; height: 120px; object-fit: cover;"
                                        onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-placeholder rounded-circle d-none align-items-center justify-content-center"
                                        style="width: 120px; height: 120px; background-color: #f0f0f0; color: #6c757d; font-size: 48px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @else
                                    {{-- Default avatar icon --}}
                                    <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center"
                                        id="avatarPreview"
                                        style="width: 120px; height: 120px; background-color: #f0f0f0; color: #6c757d; font-size: 48px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <img src="" alt="Avatar" class="rounded-circle d-none" id="avatarImage"
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                @endif
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
                                <a href="{{ route('profile.index') }}" class="btn btn-sm btn-danger active"
                                    style="border: none;">
                                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                </a>
                                <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-outline-secondary"
                                    style="border: none;">
                                    <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal" style="border: none;">
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
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="fullname" class="form-label fw-semibold">
                                            Tên <span class="text-danger">*</span>
                                        </label>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="fullname" name="fullname"
                                            value="{{ $user->fullname }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="email" class="form-label fw-semibold">Email</label>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="email" class="form-control" id="email"
                                            value="{{ $user->email }}" readonly style="color: #ccc">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="{{ $user->phone ?? '' }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">Ngày sinh</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <select class="form-select" id="birthday_day" name="birthday_day">
                                                    <option value="">Ngày</option>
                                                    @for ($i = 1; $i <= 31; $i++)
                                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-select" id="birthday_month" name="birthday_month">
                                                    <option value="">Tháng</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-select" id="birthday_year" name="birthday_year">
                                                    <option value="">Năm</option>
                                                    @for ($i = date('Y'); $i >= 1940; $i--)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-2">
                                        <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                                    </div>

                                    <div class="col-6">
                                        <textarea class="form-control" id="address" name="address" rows="3"
                                            placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ $user->address ?? '' }}</textarea>
                                    </div>

                                    <div class="col-12 my-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <button type="submit" class="btn btn-danger px-4" id="saveBtn">
                                                    Lưu
                                                </button>
                                            </div>

                                            <small class="text-muted">
                                                Tham gia: {{ $user->created_at->format('d/m/Y') }}
                                            </small>
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
        .form-control:focus,
        .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .btn-danger.active {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .profile-avatar {
            display: inline-block;
        }

        .avatar-placeholder {
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .avatar-placeholder:hover {
            background-color: #e9ecef !important;
            border-color: #adb5bd;
        }

        .form-select {
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-select:hover {
            border-color: #adb5bd;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize birthday selects with existing value
            const existingBirthday = '{{ $user->birthday ?? '' }}';
            if (existingBirthday) {
                const parts = existingBirthday.split('-');
                if (parts.length === 3) {
                    const year = parts[0];
                    const month = parts[1];
                    const day = parts[2];

                    $('#birthday_year').val(year);
                    $('#birthday_month').val(month);
                    $('#birthday_day').val(day);
                }
            }

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
                        // Show image and hide placeholder
                        const $img = $('#avatarImage').length ? $('#avatarImage') : $('#avatarPreview');
                        const $placeholder = $('.avatar-placeholder');

                        if ($img.length) {
                            $img.attr('src', event.target.result).removeClass('d-none').show();
                            $placeholder.addClass('d-none').hide();
                        } else {
                            $('#avatarPreview').attr('src', event.target.result).removeClass('d-none')
                                .show();
                        }
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

                const day = $('#birthday_day').val();
                const month = $('#birthday_month').val();
                const year = $('#birthday_year').val();

                if (day && month && year) {
                    const birthday = `${year}-${month}-${day}`;
                    formData.append('birthday', birthday);
                } else {
                    formData.append('birthday', '');
                }

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
                                // Update profile page avatar
                                const $img = $('#avatarImage').length ? $('#avatarImage') : $('#avatarPreview');
                                const $placeholder = $('.avatar-placeholder');

                                if ($img.length) {
                                    $img.attr('src', response.image_url).removeClass('d-none').show();
                                    $placeholder.addClass('d-none').hide();
                                }

                                // Update header avatar dynamically
                                const $headerAvatar = $('#headerAvatar');
                                if ($headerAvatar.length) {
                                    $headerAvatar.attr('src', response.image_url).removeClass('d-none').show();
                                    $headerAvatar.siblings('.rounded-circle.bg-danger').addClass('d-none').hide();
                                }
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

                $('#changePasswordForm input').removeClass('is-invalid');
                $('#changePasswordForm .invalid-feedback').remove();

                if (!$('#current_password').val() || !$('#new_password').val() || !$(
                        '#new_password_confirmation').val()) {
                    toastr.error('Vui lòng điền đầy đủ thông tin');
                    return;
                }

                if ($('#new_password').val() !== $('#new_password_confirmation').val()) {
                    toastr.error('Mật khẩu xác nhận không khớp');
                    $('#new_password, #new_password_confirmation').addClass('is-invalid');
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

                        $('#changePasswordForm input').removeClass('is-invalid');
                        $('#changePasswordForm .invalid-feedback').remove();

                        if (xhr.status === 400) {
                            $('#current_password').addClass('is-invalid');
                            $('#current_password').after(
                                '<div class="invalid-feedback d-block">' +
                                (xhr.responseJSON.message ||
                                    'Mật khẩu hiện tại không đúng') +
                                '</div>');
                            toastr.error(xhr.responseJSON.message ||
                                'Mật khẩu hiện tại không đúng');
                        } else if (xhr.status === 422) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;

                                Object.keys(errors).forEach(key => {
                                    const $field = $('#' + key);
                                    if ($field.length) {
                                        $field.addClass('is-invalid');

                                        errors[key].forEach(error => {
                                            $field.after(
                                                '<div class="invalid-feedback d-block">' +
                                                error + '</div>');
                                        });
                                    }
                                });
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            } else {
                                toastr.error('Dữ liệu không hợp lệ');
                            }
                        } else if (xhr.status === 401) {
                            toastr.error('Vui lòng đăng nhập lại');
                            setTimeout(() => {
                                window.location.href = '{{ route('login') }}';
                            }, 2000);
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.html(originalText);
                    }
                });
            });

            $('#changePasswordForm input').on('input', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });

            $('#changePasswordModal').on('hidden.bs.modal', function() {
                $('#changePasswordForm')[0].reset();
                $('#changePasswordForm input').removeClass('is-invalid');
                $('#changePasswordForm .invalid-feedback').remove();
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
