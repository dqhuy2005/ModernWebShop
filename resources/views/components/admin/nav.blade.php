<aside class="admin-sidebar bg-dark text-white">
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard.index') }}">
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
                    <span>Order</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                    href="{{ route('admin.categories.index') }}">
                    <i class="fas fa-list"></i>
                    <span>Category</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.filemanager.*') ? 'active' : '' }}"
                    href="{{ route('admin.filemanager.index') }}">
                    <i class="fas fa-folder-open"></i>
                    <span>File Manager</span>
                </a>
            </li>

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

        <!-- Logout button at bottom -->
        <div class="sidebar-footer mt-auto">
            <form action="{{ route('admin.logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="submit" class="nav-link logout-btn w-100 text-start border-0">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>

<!-- Mobile sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<style>
    .admin-sidebar {
        width: 250px;
        min-height: calc(100vh - 60px);
        position: fixed;
        top: 60px;
        left: 0;
        overflow-y: auto;
        z-index: 1001;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        height: 100%;
        flex: 1;
    }

    .sidebar-footer {
        margin-top: auto;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1rem;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 60px;
        left: 0;
        width: 100%;
        height: calc(100vh - 60px);
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        transition: opacity 0.3s ease;
    }

    @media (max-width: 768px) {
        .admin-sidebar {
            transform: translateX(-100%);
        }

        .admin-sidebar.show {
            transform: translateX(0);
        }

        .sidebar-overlay.show {
            display: block;
        }
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

    .logout-btn {
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 20px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        background-color: transparent;
        cursor: pointer;
    }

    .logout-btn:hover {
        color: #fff;
        background-color: rgba(220, 53, 69, 0.2);
        border-left-color: #dc3545;
    }

    .logout-btn i {
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
