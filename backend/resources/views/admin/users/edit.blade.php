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

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>User Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullname" class="form-label">
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
                                <label for="email" class="form-label">
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
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                    placeholder="Enter phone number...">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="birthday" class="form-label">Birthday</label>
                                <input type="date" class="form-control @error('birthday') is-invalid @enderror"
                                    id="birthday" name="birthday" value="{{ old('birthday', $user->birthday) }}">
                                @error('birthday')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    Password <span class="text-muted">(Leave blank to keep current)</span>
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Enter new password...">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Confirm Password
                                </label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Confirm new password...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="role_id" class="form-label">
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

                        <div class="mb-3">
                            <label class="form-label">Status</label>
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

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image me-2"></i>User Avatar
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($user->image)
                            <div class="text-center mb-3">
                                <p class="text-muted mb-2">Current Avatar:</p>
                                <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->fullname }}"
                                    class="img-fluid rounded-circle"
                                    style="max-height: 150px; border: 3px solid #e9ecef;">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                {{ $user->image ? 'Change Avatar' : 'Upload Avatar' }}
                            </label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Accepted: JPG, PNG, GIF, WEBP. Max: 2MB
                            </small>
                        </div>

                        <div id="image-preview" class="text-center d-none">
                            <p class="text-muted mb-2">New Avatar:</p>
                            <img src="" alt="Preview" class="img-fluid rounded-circle"
                                style="max-height: 150px; border: 3px solid #28a745;">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                <i class="fas fa-times me-1"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Image Preview
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview img').attr('src', e.target.result);
                    $('#image-preview').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove Image
        function removeImage() {
            $('#image').val('');
            $('#image-preview').addClass('d-none');
            $('#image-preview img').attr('src', '');
        }

        // Form Validation
        $('#userForm').on('submit', function(e) {
            let isValid = true;

            // Check required fields
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

            // Check password confirmation (only if password is filled)
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

        #image-preview img {
            border: 3px solid #28a745 !important;
        }
    </style>
@endpush
