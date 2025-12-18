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
                        <img src="{{ $user->image_url }}" alt="{{ $user->fullname }}"
                            class="img-fluid rounded-circle current-avatar">
                    </div>
                @endif

                <div class="mb-4 pb-3 border-bottom">
                    <label class="form-label fw-bold">
                        Avatar
                    </label>

                    <input type="hidden" id="image" name="image" value="{{ old('image', $user->image) }}"
                        class="@error('image') is-invalid @enderror">

                    <div id="image-preview"
                        class="mt-3 {{ old('image') && old('image') !== $user->image ? '' : 'd-none' }}">
                        <p class="text-success mb-2"><strong>New Avatar Preview:</strong></p>
                        <div class="preview-container">
                            <img src="{{ old('image') && old('image') !== $user->image ? asset('storage/' . old('image')) : '' }}"
                                alt="Preview" class="img-fluid preview-image">
                        </div>
                    </div>

                    <div id="upload-area" class="{{ old('image') && old('image') !== $user->image ? 'd-none' : '' }}">
                        <button type="button" class="btn btn-outline-primary btn-lg lfm-btn selected-avatar"
                            data-input="image" data-preview="holder">
                            {{ $user->image ? 'Change Avatar' : 'Select Avatar' }}
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
                        <input type="text" class="form-control @error('fullname') is-invalid @enderror" id="fullname"
                            name="fullname" value="{{ old('fullname', $user->fullname) }}" placeholder="Enter full name..."
                            required>
                        @error('fullname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label fw-bold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $user->email) }}" placeholder="Enter email address..."
                            required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label fw-bold">Phone</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Enter phone number..."
                            pattern="[0-9]{8,15}" title="Only numbers (0-9), 8-15 digits">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="birthday" class="form-label fw-bold">Birthday</label>
                        <input type="date" class="form-control @error('birthday') is-invalid @enderror" id="birthday"
                            name="birthday" value="{{ old('birthday', $user->birthday) }}">
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
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="Enter new password...">
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
                        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id"
                            required @if ($user->id === auth()->id() || strtolower($user->role->name ?? '') === 'admin') disabled @endif>
                            <option value="">-- Select Role --</option>
                            @foreach ($roles as $role)
                                @if (strtolower($role->name) !== 'admin' || $user->role_id == $role->id)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                        @if (strtolower($role->name) === 'admin')
                                            (Super Admin - Cannot be changed)
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        @if ($user->id === auth()->id())
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>You cannot change your own role.
                            </small>
                        @elseif(strtolower($user->role->name ?? '') === 'admin')
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            <small class="text-danger">
                                <i class="fas fa-shield-alt me-1"></i>Admin role cannot be changed. Only one super admin
                                allowed.
                            </small>
                        @endif

                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                <i class="fas fa-save me-2"></i>Update
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.lfm-btn').filemanager('image', {
                prefix: '/admin/filemanager'
            });

            $('#image').on('change', function() {
                const imagePath = $(this).val();
                const currentImage = '{{ $user->image ?? '' }}';

                if (imagePath && imagePath !== currentImage) {
                    const imageUrl = imagePath;
                    $('#image-preview img').attr('src', imageUrl);
                    $('#upload-area').addClass('d-none');
                    $('#image-preview').removeClass('d-none');
                }
            });
        });

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
            display: block;
            position: relative;
        }

        .preview-image {
            max-height: 200px;
            max-width: 200px;
        }

        #image-preview {
            animation: fadeIn 0.4s ease;
        }
    </style>
@endpush
