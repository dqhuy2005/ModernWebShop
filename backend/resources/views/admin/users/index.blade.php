@extends('layouts.admin.app')

@section('title', 'User Management - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>User Management : {{ $users->total() ?? 0 }}
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>

                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-2"></i>Import Excel
                </button>

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
                            <h4 class="mb-0" id="totalUsersCount">{{ $totalUsers ?? 0 }}</h4>
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
                            <h4 class="mb-0" id="activeUsersCount">{{ $activeUsers ?? 0 }}</h4>
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
                            <h4 class="mb-0" id="inactiveUsersCount">{{ $inactiveUsers ?? 0 }}</h4>
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
                            <h4 class="mb-0" id="deletedUsersCount">{{ $deletedUsers }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.users.form')

    <div id="users-table-container">
        @include('admin.users.table')
    </div>

@endsection

@push('scripts')
    <script>
        let userPagination;

        $(document).ready(function() {
            userPagination = new AjaxPagination({
                containerId: 'users-table-container',
                paginationSelector: 'nav[aria-label="Users pagination"]',
                onCountsUpdate: function(counts) {
                    if (counts.total !== undefined) $('#totalUsersCount').text(counts.total);
                    if (counts.active !== undefined) $('#activeUsersCount').text(counts.active);
                    if (counts.inactive !== undefined) $('#inactiveUsersCount').text(counts.inactive);
                    if (counts.deleted !== undefined) $('#deletedUsersCount').text(counts.deleted);
                },
                onError: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load page');
                    } else {
                        alert('Failed to load page');
                    }
                }
            });
        });

        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            userPagination.loadPage(url.toString());
        }

        function toggleStatus(userId) {
            if (!confirm('Are you sure you want to change the status of this user?')) return;

            $.ajax({
                url: '/admin/users/' + userId + '/toggle-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const row = $('#user-' + userId);
                        if (response.status === 1 || response.status === true) {
                            row.find("input[type='checkbox']").prop('checked', true);
                        } else {
                            row.find("input[type='checkbox']").prop('checked', false);
                        }

                        if (response.counts) {
                            $('#totalUsersCount').text(response.counts.total);
                            $('#activeUsersCount').text(response.counts.active);
                            $('#inactiveUsersCount').text(response.counts.inactive);
                            $('#deletedUsersCount').text(response.counts.deleted);
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Status updated');
                        }
                    } else if (!response.success) {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to update status!');
                    } else {
                        alert('Failed to update status!');
                    }
                }
            });
        }

        function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user? You can restore it later.')) return;

            $.ajax({
                url: '/admin/users/' + userId,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const row = $('#user-' + userId);
                    row.addClass('table-warning');
                    row.find('td').last().html(`
                        <div class="btn-group" role="group">
                            <a href="/admin/users/${userId}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-success" onclick="restoreUser(${userId})" title="Restore">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="forceDeleteUser(${userId})" title="Delete Permanently">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);

                    row.find('td').eq(6).html('<span class="badge bg-secondary">Deleted</span>');

                    if (response.counts) {
                        $('#totalUsersCount').text(response.counts.total);
                        $('#activeUsersCount').text(response.counts.active);
                        $('#inactiveUsersCount').text(response.counts.inactive);
                        $('#deletedUsersCount').text(response.counts.deleted);
                    }

                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'User deleted');
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to delete user!');
                    } else {
                        alert('Failed to delete user!');
                    }
                }
            });
        }

        function restoreUser(userId) {
            if (!confirm('Are you sure you want to restore this user?')) return;

            $.ajax({
                url: '/admin/users/' + userId + '/restore',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const row = $('#user-' + userId);
                        row.removeClass('table-warning');
                        row.find('td').last().html(`
                            <div class="btn-group" role="group">
                                <a href="/admin/users/${userId}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/admin/users/${userId}/edit" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteUser(${userId})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `);

                        const isChecked = (response.status === 1 || response.status === true) ? 'checked' : '';
                        row.find('td').eq(6).html(`
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="status-${userId}" ${isChecked} onchange="toggleStatus(${userId})" style="cursor: pointer;">
                            </div>
                        `);

                        if (response.counts) {
                            $('#totalUsersCount').text(response.counts.total);
                            $('#activeUsersCount').text(response.counts.active);
                            $('#inactiveUsersCount').text(response.counts.inactive);
                            $('#deletedUsersCount').text(response.counts.deleted);
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'User restored');
                        }
                    } else {
                        if (typeof toastr !== 'undefined') toastr.error(response.message ||
                            'Failed to restore');
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to restore user!');
                    } else {
                        alert('Failed to restore user!');
                    }
                }
            });
        }

        function forceDeleteUser(userId) {
            if (!confirm('Are you sure you want to PERMANENTLY delete this user? This action CANNOT be undone!')) return;
            if (!confirm('This will delete ALL user data permanently. Are you absolutely sure?')) return;

            $.ajax({
                url: '/admin/users/' + userId + '/force-delete',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#user-' + userId).remove();

                        if (response.counts) {
                            $('#totalUsersCount').text(response.counts.total);
                            $('#activeUsersCount').text(response.counts.active);
                            $('#inactiveUsersCount').text(response.counts.inactive);
                            $('#deletedUsersCount').text(response.counts.deleted);
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'User permanently deleted');
                        }
                    } else {
                        if (typeof toastr !== 'undefined') toastr.error(response.message ||
                            'Failed to delete permanently');
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to delete user!');
                    } else {
                        alert('Failed to delete user!');
                    }
                }
            });
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

        /* Loading state for AJAX pagination */
        #users-table-container {
            transition: opacity 0.3s ease;
        }

        #users-table-container.loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@endpush

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Users from Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Download the template below to see the required format</li>
                            <li>Fill in your user data following the template structure</li>
                            <li>Upload the completed Excel file</li>
                            <li>Maximum file size: 2MB</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('admin.users.import-template') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i>Download Excel Template
                        </a>
                    </div>

                    <div class="mb-3">
                        <label for="excel_file" class="form-label fw-bold">Select Excel File</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                        <small class="text-muted">Accepted formats: Excel (*.xlsx, *.xls)</small>
                    </div>

                    @if(session('import_errors'))
                        <div class="alert alert-warning">
                            <strong>Import Errors:</strong>
                            <ul class="mb-0 mt-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
