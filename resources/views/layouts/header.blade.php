<header class="navbar navbar-expand-lg navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <div class="container-fluid">
        {{-- Sidebar Toggle Button --}}
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Brand Logo --}}
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 d-flex align-items-center" href="{{ url('/') }}">
            <div class="logo-icon-container me-2">
                <i class="fas fa-cash-register"></i>
            </div>
            {{ $businessName }}
        </a>

        {{-- Mobile Search --}}
        <div class="d-lg-none">
            <form class="d-flex">
                <input class="form-control form-control-dark me-2" type="search" placeholder="Search..." aria-label="Search">
                <button class="btn btn-outline-light" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        {{-- Check if user is authenticated --}}
        @auth
        <div class="navbar-nav ms-auto">
            {{-- Search (Desktop) --}}
            <div class="d-none d-lg-block me-3">
                <form class="d-flex">
                    <input class="form-control form-control-dark" type="search" placeholder="Search products..." aria-label="Search">
                    <button class="btn btn-outline-light ms-2" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            {{-- Notifications --}}
            @can('view-reports')
            <div class="nav-item dropdown me-2">
                <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @if(isset($lowStockCount) && $lowStockCount > 0)
                    <span class="badge bg-danger badge-notification">{{ $lowStockCount }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    @if(isset($lowStockCount) && $lowStockCount > 0)
                    <li><a class="dropdown-item" href="{{ route('inventory.low-stock') }}"><i class="fas fa-exclamation-triangle text-warning me-2"></i> {{ $lowStockCount }} low stock item(s)</a></li>
                    @endif
                    <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-cart text-success me-2"></i> Recent sales activity</a></li>
                    @if(isset($recentCustomers) && $recentCustomers > 0)
                    <li><a class="dropdown-item" href="{{ route('customers.index') }}"><i class="fas fa-user-plus text-info me-2"></i> {{ $recentCustomers }} new customer(s)</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                </ul>
            </div>
            @endcan

            {{-- User Profile --}}
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset(Auth::user()->avatar) }}" alt="Profile" class="rounded-circle me-1" style="width: 24px; height: 24px; object-fit: cover;">
                    @else
                        <i class="fas fa-user-circle me-1"></i> 
                    @endif
                    {{ Auth::user()->name }}
                    <span class="badge bg-secondary ms-1" data-role="{{ Auth::user()->role }}">{{ ucfirst(Auth::user()->role) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><h6 class="dropdown-header">Signed in as {{ Auth::user()->name }}</h6></li>
                    <li><span class="dropdown-item-text">
                        <small class="text-muted">Role: {{ ucfirst(Auth::user()->role) }}</small>
                    </span></li>
                    <li><hr class="dropdown-divider"></li>
                    
                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i> My Profile</a></li>
                    
                    @can('manage-settings')
                    <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i> System Settings</a></li>
                    @endcan
                    
                    @can('manage-users')
                    <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="fas fa-users me-2"></i> User Management</a></li>
                    @endcan
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="{{ route('login') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Sign out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        @else
        {{-- Show login/register buttons for guests --}}
        <div class="navbar-nav ms-auto">
            <div class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">
                    <i class="fas fa-user-plus me-1"></i> Register
                </a>
            </div>
        </div>
        @endauth
    </div>
</header>

<style>
    .badge-notification {
        position: absolute;
        top: 3px;
        right: 3px;
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
    }
    .navbar-nav .dropdown-toggle {
        outline: none;
    }
    .form-control-dark {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }
    .form-control-dark::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    .form-control-dark:focus {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    }
    
    /* Role-specific badge colors */
    .badge[data-role="admin"] {
        background-color: #dc3545 !important;
    }
    .badge[data-role="manager"] {
        background-color: #0d6efd !important;
    }
    .badge[data-role="cashier"] {
        background-color: #198754 !important;
    }
    .badge[data-role="superadmin"] {
        background-color: #6f42c1 !important;
    }
    .logo-icon-container {
        width: 35px;
        height: 35px;
        border-radius: 8px;
    }
    .logo-icon-container i {
        font-size: 1.2rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleBadges = document.querySelectorAll('.badge[data-role]');
        roleBadges.forEach(badge => {
            const role = badge.getAttribute('data-role');
            badge.classList.add('bg-secondary');
        });
    });
</script>