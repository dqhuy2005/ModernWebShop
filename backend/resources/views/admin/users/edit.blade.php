@extends('layouts.admin.app')

@section('title', 'Edit User - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-user-edit me-2"></i>Edit User
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" id="userForm">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-body">
                @if ($user->image)
                    <div class="mb-4 pb-3 border-bottom text-center">
                        <p class="text-muted mb-2"><strong>Current Avatar:</strong></p>
                        <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->fullname }}"
                            class="img-fluid rounded-circle current-avatar">
                    </div>
                @endif

                <div class="mb-4 pb-3 border-bottom">
                    <label class="form-label fw-bold">
                        <i class="fas fa-image me-2"></i>{{ $user->image ? 'Change Avatar' : 'Upload Avatar' }}
                    </label>

                    <input type="file" class="d-none @error('image') is-invalid @enderror"
                        id="image" name="image" accept="image/*" onchange="previewImage(event)">

                    <div id="upload-area" class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-lg" onclick="$('#image').click()">
                            <i class="fas fa-cloud-upload-alt me-2"></i>{{ $user->image ? 'Change Avatar' : 'Select Avatar' }}
                        </button>
                        <p class="text-muted mt-2 mb-0 small">Accepted: JPG, PNG, GIF, WEBP. Max: 2MB</p>
                    </div>

                    <div id="image-preview" class="text-center mt-3 d-none">
                        <p class="text-success mb-2"><strong><i class="fas fa-check-circle me-1"></i>New Avatar Preview:</strong></p>
                        <div class="preview-container">
                            <img src="" alt="Preview" class="img-fluid rounded-circle preview-image">
                        </div>
                        <button type="button" class="btn btn-sm btn-danger mt-3" onclick="removeImage()">
                            <i class="fas fa-trash-alt me-1"></i>Remove Avatar
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-3 ms-2" onclick="$('#image').click()">
                            <i class="fas fa-sync-alt me-1"></i>Change Avatar
                        </button>
                    </div>

                    @error('image')
                        <div class="text-danger mt-2 small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fullname" class="form-label fw-bold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('fullname') is-invalid @enderror"
                            id="fullname" name="fullname" value="{{ old('fullname', $user->fullname) }}"
                            placeholder="Enter full name..." required>
                        @error('fullname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label fw-bold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $user->email) }}"
                            placeholder="Enter email address..." required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label fw-bold">Phone</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                            id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            placeholder="Enter phone number...">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="birthday" class="form-label fw-bold">Birthday</label>
                        <input type="date" class="form-control @error('birthday') is-invalid @enderror"
                            id="birthday" name="birthday" value="{{ old('birthday', $user->birthday) }}">
                        @error('birthday')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold">
                            Password <span class="text-muted">(Leave blank to keep current)</span>
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Enter new password...">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">
                            Confirm Password
                        </label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" placeholder="Confirm new password...">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role_id" class="form-label fw-bold">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id"
                            name="role_id" required @if ($user->id === auth()->id()) disabled @endif>
                            <option value="">-- Select Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            <small class="text-muted">You cannot change your own role.</small>
                        @endif
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="language" class="form-label fw-bold">Language</label>
                        <select class="form-select" id="language" name="language">
                            <option value="vi" {{ old('language', $user->language) === 'vi' ? 'selected' : '' }}>Vietnamese</option>
                            <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="status"
                                name="status" value="1" {{ old('status', $user->status) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Update User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    toastr.error('File size must not exceed 2MB!');
                    $('#image').val('');
                    return;
                }

                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    toastr.error('Only JPG, PNG, GIF, and WEBP images are allowed!');
                    $('#image').val('');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview img').attr('src', e.target.result);
                    $('#upload-area').addClass('d-none');
                    $('#image-preview').removeClass('d-none');
                    toastr.success('New avatar selected successfully!');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            $('#image').val('');
            $('#image-preview').addClass('d-none');
            $('#upload-area').removeClass('d-none');
            $('#image-preview img').attr('src', '');
            toastr.info('New avatar removed');
        }

        $('#userForm').on('submit', function(e) {
            let isValid = true;

            if (!$('#fullname').val().trim()) {
                isValid = false;
                $('#fullname').addClass('is-invalid');
            }

            if (!$('#email').val().trim()) {
                isValid = false;
                $('#email').addClass('is-invalid');
            }

            if (!$('#role_id').val()) {
                isValid = false;
                $('#role_id').addClass('is-invalid');
            }

            if ($('#password').val() && $('#password').val() !== $('#password_confirmation').val()) {
                isValid = false;
                alert('Passwords do not match!');
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
        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        /* Current Avatar Styles */
        .current-avatar {
            max-height: 150px;
            border: 3px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .preview-container {
            display: inline-block;
            position: relative;
        }

        .preview-image {
            max-height: 200px;
            max-width: 200px;
            border: 2px solid #28a745;
            box-shadow: 0 4px 15px rgba(0, 0, -0, 0.2);
            transition: all 0.3s ease;
        }

        #image-preview {
            animation: fadeIn 0.4s ease;
        }
    </style>
@endpush
