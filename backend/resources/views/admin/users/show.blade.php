@extends('layouts.admin.app')

@section('title', 'User Details - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-user me-2"></i>User Details
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>User Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="200">ID:</th>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <th>Full Name:</th>
                                <td>
                                    <strong>{{ $user->fullname }}</strong>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-primary ms-2">You</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>
                                    @if($user->phone)
                                        <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Birthday:</th>
                                <td>
                                    @if($user->birthday)
                                        {{ \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') }}
                                        <span class="text-muted">(Age: {{ \Carbon\Carbon::parse($user->birthday)->age }})</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td>
                                    @if($user->role)
                                        <span class="badge {{ $user->role->id == 1 ? 'bg-danger' : 'bg-info' }}">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Language:</th>
                                <td>
                                    @if($user->language === 'vi')
                                        <span class="badge bg-success">Vietnamese</span>
                                    @elseif($user->language === 'en')
                                        <span class="badge bg-primary">English</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge {{ $user->status ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Timestamps
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="200">Created At:</th>
                                <td>{{ $user->created_at->format('d/m/Y H:i:s') }} ({{ $user->created_at->diffForHumans() }})</td>
                            </tr>
                            <tr>
                                <th>Updated At:</th>
                                <td>{{ $user->updated_at->format('d/m/Y H:i:s') }} ({{ $user->updated_at->diffForHumans() }})</td>
                            </tr>
                            @if($user->deleted_at)
                                <tr>
                                    <th>Deleted At:</th>
                                    <td class="text-danger">
                                        {{ $user->deleted_at->format('d/m/Y H:i:s') }} ({{ $user->deleted_at->diffForHumans() }})
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>User Activity Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                <h4 class="mb-0">{{ $user->carts->count() }}</h4>
                                <p class="text-muted mb-0">Cart Items</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-receipt fa-2x text-success mb-2"></i>
                                <h4 class="mb-0">{{ $user->orders->count() }}</h4>
                                <p class="text-muted mb-0">Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image me-2"></i>User Avatar
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($user->image)
                        <img src="{{ asset('storage/' . $user->image) }}"
                             alt="{{ $user->fullname }}"
                             class="img-fluid rounded-circle mb-3"
                             style="max-height: 200px; border: 4px solid #e9ecef;">
                    @else
                        <div class="avatar-placeholder mb-3" style="width: 200px; height: 200px; margin: 0 auto;">
                            <i class="fas fa-user fa-5x text-muted"></i>
                        </div>
                    @endif
                    <p class="text-muted mb-0">{{ $user->fullname }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user->trashed())
                            <button type="button" class="btn btn-success" onclick="restoreUser({{ $user->id }})">
                                <i class="fas fa-undo me-2"></i>Restore User
                            </button>
                            <button type="button" class="btn btn-danger" onclick="forceDeleteUser({{ $user->id }})">
                                <i class="fas fa-trash-alt me-2"></i>Delete Permanently
                            </button>
                        @else
                            <button type="button" class="btn btn-{{ $user->status ? 'warning' : 'success' }}" onclick="toggleStatus({{ $user->id }})">
                                <i class="fas fa-toggle-{{ $user->status ? 'on' : 'off' }} me-2"></i>
                                {{ $user->status ? 'Deactivate' : 'Activate' }} User
                            </button>

                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit User
                            </a>

                            @if($user->id !== auth()->id())
                                <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
                                    <i class="fas fa-trash me-2"></i>Delete User
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-2"></i>Cannot Delete Yourself
                                </button>
                            @endif
                        @endif

                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle Status
        function toggleStatus(userId) {
            if (confirm('Are you sure you want to change the status of this user?')) {
                $.ajax({
                    url: `/admin/users/${userId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message || 'Failed to update status');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        }

        // Delete User
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: `/admin/users/${userId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.href = '{{ route('admin.users.index') }}', 1000);
                        } else {
                            toastr.error(response.message || 'Failed to delete user');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        }

        // Restore User
        function restoreUser(userId) {
            if (confirm('Are you sure you want to restore this user?')) {
                $.ajax({
                    url: `/admin/users/${userId}/restore`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message || 'Failed to restore user');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        }

        // Force Delete User
        function forceDeleteUser(userId) {
            const confirmation = prompt('This action is PERMANENT and cannot be undone. Type "DELETE" to confirm:');
            if (confirmation === 'DELETE') {
                $.ajax({
                    url: `/admin/users/${userId}/force-delete`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.href = '{{ route('admin.users.index') }}', 1000);
                        } else {
                            toastr.error(response.message || 'Failed to delete user permanently');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            } else if (confirmation !== null) {
                toastr.warning('Confirmation text did not match. Action cancelled.');
            }
        }
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

        .avatar-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border: 4px solid #e9ecef;
            border-radius: 50%;
        }
    </style>
@endpush
