<div class="border-top mt-3 pt-3">
    @if ($orders->count() > 0)
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-hover align-middle" id="ordersTable">
                <thead class="table-light">
                    <tr>
                        <th width="8%"
                            class="sortable {{ request('sort_by') === 'id' ? request('sort_order', 'desc') : '' }}"
                            onclick="sortTable('id')">
                            Order ID <i
                                class="fas fa-sort{{ request('sort_by') === 'id' ? (request('sort_order') === 'asc' ? '-up' : '-down') : '' }}"></i>
                        </th>
                        <th width="18%">Customer</th>
                        <th width="12%"
                            class="text-center sortable {{ request('sort_by') === 'status' ? request('sort_order', 'desc') : '' }}"
                            onclick="sortTable('status')">
                            Status <i
                                class="fas fa-sort{{ request('sort_by') === 'status' ? (request('sort_order') === 'asc' ? '-up' : '-down') : '' }}"></i>
                        </th>
                        <th width="15%"
                            class="text-end sortable {{ request('sort_by') === 'total_amount' ? request('sort_order', 'desc') : '' }}"
                            onclick="sortTable('total_amount')">
                            Total Amount <i
                                class="fas fa-sort{{ request('sort_by') === 'total_amount' ? (request('sort_order') === 'asc' ? '-up' : '-down') : '' }}"></i>
                        </th>
                        <th width="10%" class="text-center">Items</th>
                        <th width="12%"
                            class="sortable {{ request('sort_by') === 'created_at' ? request('sort_order', 'desc') : '' }}"
                            onclick="sortTable('created_at')">
                            Created Date <i
                                class="fas fa-sort{{ request('sort_by') === 'created_at' ? (request('sort_order') === 'asc' ? '-up' : '-down') : '' }}"></i>
                        </th>
                        <th width="15%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr id="order-{{ $order->id }}" class="{{ $order->deleted_at ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $order->id }}</strong>
                            </td>

                            <td>
                                <div>
                                    <p class="mb-0 fw-bold">{{ $order->user->fullname ?? 'N/A' }}</p>
                                    @if ($order->user && $order->user->email)
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    @endif
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-{{ $order->status_badge_color }} px-3 py-2">
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <td class="text-end">
                                <strong class="text-success">{{ $order->formatted_total_amount }}</strong>
                            </td>

                            <td class="text-center">
                                <span class="text-dark">{{ $order->total_items }}</span>
                            </td>

                            <td>
                                <small>{{ $order->created_at->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                            </td>

                            <td class="text-center">
                                @if ($order->deleted_at)
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-success"
                                            onclick="restoreOrder({{ $order->id }})" title="Restore">
                                            <i class="fas fa-undo"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="forceDeleteOrder({{ $order->id }})" title="Delete Permanently">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="deleteOrder({{ $order->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center m-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label for="per_page_bottom" class="text-muted mb-0" style="white-space: nowrap;">
                        Per page:
                    </label>
                    <select name="per_page" id="per_page_bottom" class="form-select form-select-sm" style="width: 75px;"
                        onchange="changePerPage(this.value)">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <nav aria-label="Orders pagination">
                {{ $orders->appends(request()->query())->links('vendor.pagination.custom-bootstrap-5') }}
            </nav>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h5 class="text-muted">No Orders Found</h5>
            <p class="text-muted mb-4">
                @if (request()->has('search'))
                    No orders match your search criteria. Try adjusting your search.
                @else
                    Start by creating your first order.
                @endif
            </p>
        </div>
    @endif
</div>

@push('styles')
    <style>
        /* Table Container */
        .table-responsive {
            position: relative;
        }

        /* Table Styles */
        #ordersTable thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }

        #ordersTable tbody tr {
            transition: all 0.2s ease;
        }

        /* Deleted Row Highlight */
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        /* Sortable Headers */
        .sortable {
            cursor: pointer;
            user-select: none;
            position: relative;
            transition: all 0.2s ease;
        }

        .sortable:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .sortable i {
            font-size: 0.7rem;
            margin-left: 4px;
            opacity: 0.5;
        }

        .sortable.asc,
        .sortable.desc {
            color: #0d6efd;
        }

        .sortable.asc i,
        .sortable.desc i {
            opacity: 1;
            color: #0d6efd;
        }

        /* Button Group */
        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }

        /* Badge Styles */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Pagination */
        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: #0d6efd;
            padding: 0.375rem 0.75rem;
            text-decoration: none;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin: 0 2px;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
            z-index: 1;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
            opacity: 0.5;
        }

        /* Per Page Selector */
        .form-select-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        /* Empty State */
        .fa-shopping-cart {
            opacity: 0.3;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function sortTable(column) {
            const url = new URL(window.location.href);
            const currentSortBy = url.searchParams.get('sort_by');
            const currentSortOrder = url.searchParams.get('sort_order') || 'desc';

            if (currentSortBy === column) {
                url.searchParams.set('sort_order', currentSortOrder === 'asc' ? 'desc' : 'asc');
            } else {
                url.searchParams.set('sort_by', column);
                url.searchParams.set('sort_order', 'desc');
            }

            window.location.href = url.toString();
        }

        function deleteOrder(orderId) {
            ConfirmModal.warning('Are you sure you want to cancel this order?', function() {
                deleteOrderAjax(orderId);
            });
        }

        function deleteOrderAjax(orderId) {
            $.ajax({
                url: `/admin/orders/${orderId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const $row = $(`#order-${orderId}`);

                    $row.find('.badge')
                        .removeClass('bg-warning bg-secondary bg-primary bg-info bg-success')
                        .addClass('bg-danger')
                        .text('CANCELLED');

                    toastr.success(response.message || 'Order cancelled successfully!');
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to cancel order!');
                }
            });
        }

        function restoreOrder(orderId) {
            ConfirmModal.show('Are you sure you want to restore this order?', function() {
                restoreOrderAjax(orderId);
            }, {
                confirmText: 'Confirm',
                cancelText: 'Cancel',
                icon: 'fas fa-undo',
                iconColor: '#28a745'
            });
        }

        function restoreOrderAjax(orderId) {
            $.ajax({
                url: `/admin/orders/${orderId}/restore`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    toastr.success(response.message || 'Order restored successfully!');
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to restore order!');
                }
            });
        }

        function forceDeleteOrder(orderId) {
            if (result.isConfirmed && result.value) {
                toastr.info('Force delete not implemented yet');
            } else if (result.isConfirmed) {
                toastr.warning('Please confirm by checking the box');
            }
        }
    </script>
@endpush
