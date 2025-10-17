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
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i></i>Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center border-end">
                    <div class="avatar-container">
                        @if ($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->fullname }}"
                                class="user-avatar rounded-circle mb-3">
                        @else
                            <div class="avatar-placeholder rounded-circle mb-3">
                                <i class="fas fa-user fa-5x text-muted"></i>
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $user->fullname }}</h5>
                        @if ($user->id === auth()->id())
                            <span class="badge bg-primary mb-2">You</span>
                        @endif
                        <p class="text-muted mb-0">
                            <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                        </p>
                    </div>
                </div>

                <div class="col-md-9">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th width="200" class="text-muted">
                                    <i class="fas fa-id-card me-2"></i>User ID:
                                </th>
                                <td><strong>#{{ $user->id }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-user me-2"></i>Full Name:
                                </th>
                                <td><strong>{{ $user->fullname }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-envelope me-2"></i>Email:
                                </th>
                                <td>
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-phone me-2"></i>Phone:
                                </th>
                                <td>
                                    @if ($user->phone)
                                        <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-birthday-cake me-2"></i>Birthday:
                                </th>
                                <td>
                                    @if ($user->birthday)
                                        {{ \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') }}
                                        <span class="text-muted">({{ \Carbon\Carbon::parse($user->birthday)->age }} years
                                            old)</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-user-tag me-2"></i>Role:
                                </th>
                                <td>
                                    @if ($user->role)
                                        <span class="badge {{ $user->role->id == 1 ? 'bg-danger' : 'bg-info' }}">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-toggle-on me-2"></i>Status:
                                </th>
                                <td>
                                    <span class="badge {{ $user->status ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas fa-circle me-1"></i>{{ $user->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <hr class="my-2">
                                </td>
                            </tr>

                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-clock me-2"></i>Created At:
                                </th>
                                <td>
                                    {{ $user->created_at->format('d/m/Y H:i:s') }}
                                    <span class="text-muted">({{ $user->created_at->diffForHumans() }})</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">
                                    <i class="fas fa-sync me-2"></i>Updated At:
                                </th>
                                <td>
                                    {{ $user->updated_at->format('d/m/Y H:i:s') }}
                                    <span class="text-muted">({{ $user->updated_at->diffForHumans() }})</span>
                                </td>
                            </tr>
                            @if ($user->deleted_at)
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-trash me-2"></i>Deleted At:
                                    </th>
                                    <td class="text-danger">
                                        {{ $user->deleted_at->format('d/m/Y H:i:s') }}
                                        <span>({{ $user->deleted_at->diffForHumans() }})</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

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

        /* Avatar Container */
        .avatar-container {
            padding: 20px;
        }

        .user-avatar {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 4px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .avatar-placeholder {
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border: 4px solid #e9ecef;
            margin: 0 auto;
        }

        /* Table styling */
        .table tbody tr th {
            padding: 12px 8px;
            font-weight: 600;
        }

        .table tbody tr td {
            padding: 12px 8px;
        }

        /* Border between avatar and info */
        .border-end {
            border-right: 2px solid #e9ecef !important;
        }

        /* Badge icons */
        .badge i {
            font-size: 0.75rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .border-end {
                border-right: none !important;
                border-bottom: 2px solid #e9ecef !important;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }

            .user-avatar,
            .avatar-placeholder {
                width: 150px;
                height: 150px;
            }

            .table tbody tr th,
            .table tbody tr td {
                display: block;
                width: 100%;
                padding: 8px;
            }

            .table tbody tr th {
                padding-bottom: 4px;
            }

            .table tbody tr td {
                padding-top: 0;
                padding-bottom: 12px;
            }
        }
    </style>
@endpush
