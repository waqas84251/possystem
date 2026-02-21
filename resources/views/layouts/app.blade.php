<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS System')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛒</text></svg>">
    <!-- Add your CSS and JS links here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo-icon-container {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .logo-icon-container i {
            color: white;
        }
        .brand-text {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .badge-manual {
            background-color: #6c757d;
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: white;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    {{-- Header Component --}}
    @include('layouts.header')
    
    {{-- Main Content --}}
    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar Component --}}
            @include('layouts.sidebar')
            
            {{-- Page Content --}}
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @yield('content')
            </main>
        </div>
    </div>
    
    {{-- Footer Component --}}
    @include('layouts.footer')
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>