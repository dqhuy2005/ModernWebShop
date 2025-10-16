@extends('layouts.admin.app')

@section('title', 'User Management - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>User Management : {{ $users->total() }}
                </h1>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-primary-soft rounded">
                                <i class="fas fa-users fa-lg text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h4 class="mb-0">{{ $users->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-success-soft rounded">
                                <i class="fas fa-check-circle fa-lg text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active</h6>
                            <h4 class="mb-0">{{ $users->where('status', true)->where('deleted_at', null)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-danger-soft rounded">
                                <i class="fas fa-times-circle fa-lg text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Inactive</h6>
                            <h4 class="mb-0">{{ $users->where('status', false)->where('deleted_at', null)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg bg-warning-soft rounded">
                                <i class="fas fa-trash fa-lg text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Deleted</h6>
                            <h4 class="mb-0">{{ $users->whereNotNull('deleted_at')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.users.form')

    @include('admin.users.table')

@endsection

@push('scripts')
    <script>
        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        function toggleStatus(userId) {
            if (confirm('Are you sure you want to change the status of this user?')) {
                $.ajax({
                    url: '/admin/users/' + userId + '/toggle-status',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to update status!');
                    }
                });
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? You can restore it later.')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/users/' + userId;

                var methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                var tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                form.appendChild(tokenInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function restoreUser(userId) {
            if (confirm('Are you sure you want to restore this user?')) {
                $.ajax({
                    url: '/admin/users/' + userId + '/restore',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success('User restored successfully!');
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error('Failed to restore user!');
                    }
                });
            }
        }

        function forceDeleteUser(userId) {
            if (confirm('Are you sure you want to PERMANENTLY delete this user? This action CANNOT be undone!')) {
                if (confirm('This will delete ALL user data permanently. Are you absolutely sure?')) {
                    $.ajax({
                        url: '/admin/users/' + userId + '/force-delete',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success('User permanently deleted!');
                            location.reload();
                        },
                        error: function(xhr) {
                            toastr.error('Failed to delete user!');
                        }
                    });
                }
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .avatar-lg {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        nav[aria-label="Users pagination"] {
            display: flex;
            justify-content: center;
        }

        nav[aria-label="Users pagination"] .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
    </style>
@endpush
