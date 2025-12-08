<header class="admin-header bg-white shadow-sm">
    <nav class="navbar navbar-light px-4">
        <div class="d-flex align-items-center">
            <button class="btn btn-link d-lg-none me-2" id="sidebarToggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="fas fa-store"></i> CMS - ModernWebShop
            </a>
        </div>

        <div class="ms-auto">
            <span class="text-muted">
                <i class="fas fa-user-shield"></i> {{ Auth::user()->fullname }}
            </span>
        </div>
    </nav>
</header>

<style>
    #sidebarToggle {
        color: #333;
        font-size: 1.25rem;
        padding: 0.25rem 0.5rem;
        text-decoration: none;
    }

    #sidebarToggle:hover {
        color: #0d6efd;
    }

    #sidebarToggle:focus {
        box-shadow: none;
    }

    @media (min-width: 769px) {
        #sidebarToggle {
            display: none !important;
        }
    }
</style>
