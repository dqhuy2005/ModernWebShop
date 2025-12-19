@extends('layouts.admin.app')

@section('title', 'Order Management - Admin Panel')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>Order Management : {{ $orders->total() ?? 0 }}
                </h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>

                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('admin.orders.form')
        </div>

        <div id="orders-table-container">
            @include('admin.orders.table')
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/table-sort.js') }}"></script>
    <script>
        let orderPagination;
        let orderTableSort;

        $(document).ready(function() {
            orderPagination = new AjaxPagination({
                containerId: 'orders-table-container',
                paginationSelector: 'nav[aria-label="Orders pagination"]',
                onCountsUpdate: function(counts) {
                    if (counts.total !== undefined) $('#totalOrdersCount').text(counts.total);
                    if (counts.pending !== undefined) $('#pendingOrdersCount').text(counts.pending);
                    if (counts.confirmed !== undefined) $('#confirmedOrdersCount').text(counts
                        .confirmed);
                    if (counts.processing !== undefined) $('#processingOrdersCount').text(counts
                        .processing);
                    if (counts.shipping !== undefined) $('#shippingOrdersCount').text(counts.shipping);
                    if (counts.completed !== undefined) $('#completedOrdersCount').text(counts
                        .completed);
                    if (counts.cancelled !== undefined) $('#cancelledOrdersCount').text(counts
                        .cancelled);
                },
                onError: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load page');
                    } else {
                        alert('Failed to load page');
                    }
                }
            });

            orderTableSort = new TableSort({
                containerId: 'orders-table-container',
                sortBy: '{{ request('sort_by') }}',
                sortOrder: '{{ request('sort_order', 'desc') }}',
                paginationInstance: orderPagination,
                onAfterSort: function(column, order) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Sorted by ' + column + ' (' + order + ')');
                    }
                }
            });

            window.tableSort = orderTableSort;
        });

        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1);
            orderPagination.loadPage(url.toString());
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

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-secondary-soft {
            background-color: rgba(108, 117, 125, 0.1);
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .card {
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Loading state for AJAX pagination */
        #orders-table-container {
            transition: opacity 0.3s ease;
        }

        #orders-table-container.loading {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
@endpush
