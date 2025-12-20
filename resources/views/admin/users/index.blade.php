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

                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('admin.users.form')
        </div>

        <div id="users-table-container">
            @include('admin.users.table')
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/table-sort.js') }}"></script>
    <script>
        let userPagination;
        let userTableSort;

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

            userTableSort = new TableSort({
                containerId: 'users-table-container',
                sortBy: '{{ request('sort_by') }}',
                sortOrder: '{{ request('sort_order', 'desc') }}',
                paginationInstance: userPagination,
            });

            window.tableSort = userTableSort;
        });

        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            userPagination.loadPage(url.toString());
        }

        function toggleStatus(userId) {
            ConfirmModal.show('Are you sure you want to change the status of this user?', function() {
                toggleStatusAjax(userId);
            }, {
                confirmText: 'Confirm',
                cancelText: 'Cancel',
                icon: 'fas fa-toggle-on',
                iconColor: '#17a2b8'
            });
        }

        function toggleStatusAjax(userId) {
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
            ConfirmModal.warning('Are you sure you want to delete this user? You can restore it later.', function() {
                deleteUserAjax(userId);
            });
        }

        function deleteUserAjax(userId) {
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

                    row.find('td').eq(5).html('<span class="badge bg-secondary">Deleted</span>');

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
            ConfirmModal.show('Are you sure you want to restore this user?', function() {
                restoreUserAjax(userId);
            }, {
                confirmText: 'Confirm',
                cancelText: 'Cancel',
                icon: 'fas fa-undo',
                iconColor: '#28a745'
            });
        }

        function restoreUserAjax(userId) {
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
                        row.find('td').eq(5).html(`
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="status-${userId}" ${isChecked} onclick="event.preventDefault(); toggleStatus(${userId});" style="cursor: pointer;">
                            </div>
                        `);

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
            ConfirmModal.show(
                'Are you sure you want to PERMANENTLY delete this user?<br><small class="text-danger">This action CANNOT be undone and will delete ALL user data permanently.</small>',
                function() {
                    forceDeleteUserAjax(userId);
                }, {
                    confirmText: 'Confirm',
                    cancelText: 'Cancel',
                    icon: 'fas fa-exclamation-triangle',
                    iconColor: '#dc3545'
                }
            );
        }

        function forceDeleteUserAjax(userId) {
            $.ajax({
                url: '/admin/users/' + userId + '/force-delete',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#user-' + userId).remove();

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

        #users-table-container {
            transition: opacity 0.3s ease;
        }

        #users-table-container.loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@endpush

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
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls"
                            required>
                        <small class="text-muted">Accepted formats: Excel (*.xlsx, *.xls)</small>
                    </div>

                    @if (session('import_errors'))
                        <div class="alert alert-warning">
                            <strong>Import Errors:</strong>
                            <ul class="mb-0 mt-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach (session('import_errors') as $error)
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
