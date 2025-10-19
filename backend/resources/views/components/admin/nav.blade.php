<aside class="admin-sidebar bg-dark text-white">
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="fas fa-user"></i>
                    <span>User</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    <i class="fas fa-box"></i>
                    <span>Product</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                   href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>

            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Quản lý người dùng</span>
                </a>
            </li> --}}

            {{-- <li class="nav-item mt-3">
                <div class="nav-section-title px-3 py-2 text-muted small">
                    <i class="fas fa-chart-line"></i> BÁO CÁO & THỐNG KÊ
                </div>
            </li> --}}

            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                   href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Báo cáo</span>
                </a>
            </li> --}}

            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"
                   href="{{ route('admin.analytics.index') }}">
                    <i class="fas fa-analytics"></i>
                    <span>Phân tích</span>
                </a>
            </li> --}}
        </ul>
    </nav>
</aside>

<style>
    .admin-sidebar {
        width: 250px;
        min-height: calc(100vh - 60px);
        position: fixed;
        top: 60px;
        left: 0;
        overflow-y: auto;
        z-index: 100;
    }

    .admin-sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 20px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .admin-sidebar .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
        border-left-color: #0d6efd;
    }

    .admin-sidebar .nav-link.active {
        color: #fff;
        background-color: rgba(13, 110, 253, 0.3);
        border-left-color: #0d6efd;
    }

    .admin-sidebar .nav-link i {
        width: 20px;
        margin-right: 10px;
    }

    .nav-section-title {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* Scrollbar styling */
    .admin-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .admin-sidebar::-webkit-scrollbar-track {
        background: #1a1a1a;
    }

    .admin-sidebar::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 3px;
    }

    .admin-sidebar::-webkit-scrollbar-thumb:hover {
        background: #777;
    }
</style>
