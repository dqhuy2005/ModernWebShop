@if ($orders->count() > 0)
    <div class="orders-list">
        @foreach ($orders as $order)
            <div class="order-card mb-3 border rounded p-3" data-order-id="{{ $order->id }}">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-1">
                            Đơn hàng #{{ $order->id }}
                        </h6>
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <div class="text-end">
                        @php
                            $statusConfig = [
                                'pending' => ['badge' => 'warning', 'text' => 'Chờ xử lý'],
                                'confirmed' => ['badge' => 'info', 'text' => 'Đã xác nhận'],
                                'processing' => ['badge' => 'primary', 'text' => 'Đang xử lý'],
                                'shipping' => ['badge' => 'secondary', 'text' => 'Đang giao'],
                                'completed' => ['badge' => 'success', 'text' => 'Hoàn thành'],
                                'cancelled' => ['badge' => 'danger', 'text' => 'Đã hủy'],
                            ];
                            $config = $statusConfig[$order->status] ?? ['badge' => 'secondary', 'text' => $order->status];
                        @endphp
                        <span class="badge bg-{{ $config['badge'] }}">
                            {{ $config['text'] }}
                        </span>
                    </div>
                </div>

                <div class="order-items">
                    @foreach ($order->orderDetails->take(2) as $detail)
                        <div class="d-flex align-items-center mb-2">
                            @if ($detail->product)
                                <img src="{{ $detail->product->image_url }}"
                                    alt="{{ $detail->product_name }}" class="rounded me-3"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-0">
                                    @if ($detail->product)
                                        <a href="{{ route('products.show', $detail->product->slug) }}"
                                            class="text-dark text-decoration-none">
                                            {{ $detail->product_name }}
                                        </a>
                                    @else
                                        {{ $detail->product_name }}
                                    @endif
                                </h6>
                                <small class="text-muted">Số lượng: {{ $detail->quantity }}</small>
                            </div>
                            <div class="text-end">
                                <span class="text-danger fw-semibold">{{ number_format($detail->total_price) }}₫</span>
                            </div>
                        </div>
                    @endforeach

                    @if ($order->orderDetails->count() > 2)
                        <p class="text-muted small mb-0">
                            <i class="fas fa-ellipsis-h me-1"></i>
                            Và {{ $order->orderDetails->count() - 2 }} sản phẩm khác
                        </p>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <div>
                        <span class="text-muted">Tổng tiền:</span>
                        <strong class="text-danger fs-5 ms-2">
                            {{ number_format($order->total_amount) }}₫
                        </strong>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('purchase.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i>Chi tiết
                        </a>
                        @if ($order->status === 'pending')
                            <button type="button" class="btn btn-sm btn-outline-danger cancel-order-btn"
                                data-order-id="{{ $order->id }}">
                                <i class="fas fa-times me-1"></i>Hủy đơn
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4 pagination-container">
        {{ $orders->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-shopping-bag" style="font-size: 5rem; color: #E9ECEF;"></i>
        <h5 class="fw-bold mt-3" style="color: #202732;">Chưa có đơn hàng</h5>
        @if (!$status)
            <a href="{{ route('home') }}" class="btn btn-danger">
                <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
            </a>
        @endif
    </div>
@endif
