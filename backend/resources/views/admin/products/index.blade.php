@extends('layouts.admin.app')

@section('title', 'Quản lý sản phẩm - Admin Panel')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-box me-2"></i>Quản lý sản phẩm
            </h1>
            <p class="text-muted mb-0">Danh sách tất cả sản phẩm trong hệ thống</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
            </a>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Danh mục</label>
                <select name="category" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="1">Điện tử</option>
                    <option value="2">Thời trang</option>
                    <option value="3">Gia dụng</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="1">Hoạt động</option>
                    <option value="0">Ngừng hoạt động</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Đặt lại
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="10%">Hình ảnh</th>
                        <th width="20%">Tên sản phẩm</th>
                        <th width="15%">Danh mục</th>
                        <th width="12%">Giá</th>
                        <th width="8%">Tồn kho</th>
                        <th width="10%">Trạng thái</th>
                        <th width="10%">Ngày tạo</th>
                        <th width="10%" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 20; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>
                            <img src="https://via.placeholder.com/50" alt="Product" class="img-thumbnail">
                        </td>
                        <td>
                            <strong>Sản phẩm {{ $i }}</strong>
                            <br>
                            <small class="text-muted">SKU: SP{{ str_pad($i, 5, '0', STR_PAD_LEFT) }}</small>
                        </td>
                        <td>{{ ['Điện tử', 'Thời trang', 'Gia dụng'][array_rand(['Điện tử', 'Thời trang', 'Gia dụng'])] }}</td>
                        <td>
                            <strong>{{ number_format(rand(100000, 5000000)) }}₫</strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ rand(0, 100) > 10 ? 'success' : 'danger' }}">
                                {{ rand(0, 100) }}
                            </span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       {{ rand(0, 1) ? 'checked' : '' }}
                                       onchange="toggleStatus({{ $i }})">
                            </div>
                        </td>
                        <td>{{ now()->subDays($i)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.products.show', $i) }}" 
                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $i) }}" 
                                   class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="deleteProduct({{ $i }})" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleStatus(productId) {
        if (confirm('Bạn có chắc muốn thay đổi trạng thái sản phẩm này?')) {
            // AJAX call to toggle status
            alert('Đã cập nhật trạng thái sản phẩm ' + productId);
            
            /*
            $.ajax({
                url: '/admin/products/' + productId + '/toggle-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Đã cập nhật trạng thái!');
                }
            });
            */
        }
    }
    
    function deleteProduct(productId) {
        if (confirm('Bạn có chắc muốn xóa sản phẩm này? Hành động này không thể hoàn tác!')) {
            // Submit delete form
            alert('Xóa sản phẩm ' + productId);
            
            /*
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/products/' + productId;
            
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
            */
        }
    }
</script>
@endpush
