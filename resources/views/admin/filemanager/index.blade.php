@extends('layouts.admin.app')

@section('title', 'File Manager - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-folder-open me-2"></i>File Manager
                </h1>
                <p class="text-muted mb-0 mt-1">Manage your files and images</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <iframe src="{{ route('unisharp.lfm.show') }}" style="width: 100%; height: calc(100vh - 200px); border: none;"
                id="lfm-iframe"></iframe>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #lfm-iframe {
            min-height: 600px;
        }

        .admin-content {
            padding-bottom: 0 !important;
        }
    </style>
@endpush
