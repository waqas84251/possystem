<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            {{-- Dashboard --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="fas fa-home me-2"></i>
                        Dashboard
                    </a>
                </li>

               {{-- Products --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="fas fa-box me-2"></i>
                    Products
                    <span class="badge bg-primary rounded-pill float-end">{{ $productCount ?? 0 }}</span>
                </a>
            </li>
          {{--category--}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('categories*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="fas fa-tags me-2"></i>
                    Categories
                </a>
            </li>
            {{-- Sales --}}
        <li class="nav-item">
            <a class="nav-link {{ Request::is('sales*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                <i class="fas fa-shopping-cart me-2"></i>
                Sales
            </a>
        </li>

            {{-- Customers --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                    <i class="fas fa-users me-2"></i>
                    Customers
                </a>
            </li>
            {{-- Inventory --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('inventory*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                    <i class="fas fa-warehouse me-2"></i>
                    Inventory
                </a>
            </li>
            {{-- Reports --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('reports*') ? 'active' : '' }}" href="{{ route('reports.sales') }}">
                    <i class="fas fa-chart-bar me-2"></i>
                    Reports
                </a>
            </li>
            {{-- Divider --}}
            <li class="nav-item mt-3">
                <hr class="dropdown-divider">
            </li>

            {{-- Settings --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('settings*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                    <i class="fas fa-cog me-2"></i>
                    Settings
                </a>
            </li>
            {{-- Help --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('help*') ? 'active' : '' }}" href="{{ route('help.index') }}">
                    <i class="fas fa-question-circle me-2"></i>
                    Help & Support
                </a>
            </li>
        </ul>

        {{-- Quick Stats --}}
        <div class="mt-4 p-3 bg-white rounded">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Today's Stats</span>
            </h6>
            <div class="small text-muted px-3">
                <div class="d-flex justify-content-between">
                    <span>Sales:</span>
                    <span class="fw-bold">{{ $currencySymbol }}{{ number_format($todayTotalSales ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Orders:</span>
                    <span class="fw-bold">{{ $todayOrderCount ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Customers:</span>
                    <span class="fw-bold">{{ $totalCustomerCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sidebar {
        min-height: calc(100vh - 56px);
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, 0.1);
        padding-top: 56px;
    }

    .sidebar .nav-link {
        color: #333;
        padding: 0.75rem 1rem;
        border-left: 3px solid transparent;
        transition: all 0.3s;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(67, 97, 238, 0.1);
        color: #4361ee;
        border-left: 3px solid #4361ee;
    }

    .sidebar .nav-link.active {
        background-color: rgba(67, 97, 238, 0.15);
        color: #4361ee;
        font-weight: 600;
        border-left: 3px solid #4361ee;
    }

    .sidebar .nav-link i {
        width: 20px;
        text-align: center;
    }

    .sidebar-heading {
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .sidebar .badge {
        font-size: 0.7rem;
    }

    @media (max-width: 767.98px) {
        .sidebar {
            position: fixed;
            top: 56px;
            left: -100%;
            width: 250px;
            height: calc(100vh - 56px);
            overflow-y: auto;
            transition: left 0.3s;
            z-index: 1000;
        }
        
        .sidebar.show {
            left: 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle sidebar toggle on mobile
        const sidebar = document.getElementById('sidebarMenu');
        const sidebarToggle = document.querySelector('[data-bs-target="#sidebarMenu"]');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 768 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    });
</script>