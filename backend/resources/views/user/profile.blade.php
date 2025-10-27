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
                                <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('assets/imgs/default-avatar.png') }}"
                                    alt="Avatar" class="rounded-circle" id="avatarPreview"
                                    style="width: 120px; height: 120px; object-fit: cover;">
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
                                                <div class="custom-select-wrapper">
                                                    <input type="text" class="form-control custom-select-input"
                                                        id="birthday_day_display" readonly>
                                                    <input type="hidden" id="birthday_day" name="birthday_day">
                                                    <ul class="custom-select-dropdown" id="birthday_day_dropdown">
                                                        @for ($i = 1; $i <= 31; $i++)
                                                            <li data-value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                                {{ $i }}</li>
                                                        @endfor
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="custom-select-wrapper">
                                                    <input type="text" class="form-control custom-select-input"
                                                        id="birthday_month_display" readonly>
                                                    <input type="hidden" id="birthday_month" name="birthday_month">
                                                    <ul class="custom-select-dropdown" id="birthday_month_dropdown">
                                                        @for ($i = 1; $i <= 12; $i++)
                                                            <li data-value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                                {{ $i }}</li>
                                                        @endfor
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="custom-select-wrapper">
                                                    <input type="text" class="form-control custom-select-input"
                                                        id="birthday_year_display" readonly>
                                                    <input type="hidden" id="birthday_year" name="birthday_year">
                                                    <ul class="custom-select-dropdown" id="birthday_year_dropdown">
                                                        @for ($i = date('Y'); $i >= 1940; $i--)
                                                            <li data-value="{{ $i }}">{{ $i }}</li>
                                                        @endfor
                                                    </ul>
                                                </div>
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

        .form-select {
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-select:hover {
            border-color: #adb5bd;
        }

        .custom-select-wrapper {
            position: relative;
            width: 100%;
        }

        .custom-select-input {
            cursor: pointer;
            background-color: #fff;
            padding-right: 2rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .custom-select-input:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            outline: 0;
        }

        .custom-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            display: none;
            max-height: 200px;
            overflow-y: auto;
            margin: 0;
            padding: 0;
            list-style: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.25rem 0.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .custom-select-dropdown.show {
            display: block;
        }

        .custom-select-dropdown li {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
        }

        .custom-select-dropdown li:hover {
            background-color: #f8f9fa;
        }

        .custom-select-dropdown li.selected {
            background-color: #dc3545;
            color: #fff;
        }

        .custom-select-dropdown::-webkit-scrollbar {
            width: 8px;
        }

        .custom-select-dropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 0 0 0.25rem 0;
        }

        .custom-select-dropdown::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .custom-select-dropdown::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            function initCustomSelect(inputId, dropdownId, hiddenId) {
                const $input = $(`#${inputId}`);
                const $dropdown = $(`#${dropdownId}`);
                const $hidden = $(`#${hiddenId}`);

                $input.on('click', function(e) {
                    e.stopPropagation();
                    $('.custom-select-dropdown').not($dropdown).removeClass('show');
                    $dropdown.toggleClass('show');
                });

                $dropdown.find('li').on('click', function(e) {
                    e.stopPropagation();
                    const value = $(this).data('value');
                    const text = $(this).text();

                    $input.val(text);
                    $hidden.val(value);

                    $dropdown.find('li').removeClass('selected');
                    $(this).addClass('selected');

                    $dropdown.removeClass('show');
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.custom-select-wrapper').length) {
                        $dropdown.removeClass('show');
                    }
                });
            }

            initCustomSelect('birthday_day_display', 'birthday_day_dropdown', 'birthday_day');
            initCustomSelect('birthday_month_display', 'birthday_month_dropdown', 'birthday_month');
            initCustomSelect('birthday_year_display', 'birthday_year_dropdown', 'birthday_year');

            const existingBirthday = '{{ $user->birthday ?? '' }}';
            if (existingBirthday) {
                const parts = existingBirthday.split('-');
                if (parts.length === 3) {
                    const year = parts[0];
                    const month = parts[1];
                    const day = parts[2];

                    $('#birthday_year').val(year);
                    $('#birthday_year_display').val(year);
                    $(`#birthday_year_dropdown li[data-value="${year}"]`).addClass('selected');

                    $('#birthday_month').val(month);
                    $('#birthday_month_display').val(parseInt(month));
                    $(`#birthday_month_dropdown li[data-value="${month}"]`).addClass('selected');

                    $('#birthday_day').val(day);
                    $('#birthday_day_display').val(parseInt(day));
                    $(`#birthday_day_dropdown li[data-value="${day}"]`).addClass('selected');
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
