<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛒</text></svg>">

        <title>{{ config('app.name', 'POS System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            body { font-family: 'Instrument Sans', sans-serif; background-color: #FDFDFC; color: #1b1b18; margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
            header { padding: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem; }
            main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem; }
            .card { background: white; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden; display: flex; width: 100%; max-width: 900px; min-height: 500px; border: 1px solid #e3e3e0; }
            .card-left { flex: 1; padding: 3rem; display: flex; flex-direction: column; justify-content: center; }
            .card-right { flex: 1; background: linear-gradient(135deg, #0d6efd, #0dcaf0); color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem; text-align: center; }
            .welcome-icon { font-size: 5rem; margin-bottom: 1.5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2)); }
            h1 { font-size: 2.5rem; font-weight: 700; margin: 0 0 0.5rem 0; }
            p { color: #706f6c; line-height: 1.6; margin-bottom: 2rem; }
            .btn { display: inline-block; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
            .btn-primary { background: #1b1b18; color: white; border: 1px solid #1b1b18; }
            .btn-primary:hover { background: #000; }
            .btn-outline { border: 1px solid #e3e3e0; color: #1b1b18; }
            .btn-outline:hover { border-color: #1b1b18; }
            .nav-link { color: #706f6c; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
            .nav-link:hover { color: #1b1b18; }
            
            @media (prefers-color-scheme: dark) {
                body { background-color: #0a0a0a; color: #ededec; }
                .card { background: #161615; border-color: #3e3e3a; }
                h1 { color: #ededec; }
                p { color: #a1a09a; }
                .btn-outline { border-color: #3e3e3a; color: #ededec; }
                .btn-outline:hover { border-color: #ededec; }
                .nav-link { color: #a1a09a; }
                .nav-link:hover { color: #ededec; }
            }
            @media (max-width: 768px) {
                .card { flex-direction: column-reverse; }
                .card-left, .card-right { padding: 2rem; }
            }
        </style>
    </head>
    <body>
        <header>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
                    @endif
                @endauth
            @endif
        </header>
        <main>
            <div class="card">
                <div class="card-left">
                    <h1>Management System</h1>
                    <p>Streamline your business operations with our comprehensive Point of Sale solution. Manage products, track sales, and generate detailed reports with ease.</p>
                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('login') }}" class="btn btn-primary px-5">Login Now</a>
                        <a href="#" class="btn btn-outline">Learn More</a>
                    </div>
                </div>
                <div class="card-right">
                    <div class="welcome-icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">{{ config('app.name', 'POS System') }}</div>
                    <div style="opacity: 0.9;">Your Complete Retail Partner</div>
                </div>
            </div>
        </main>
        <footer style="padding: 2rem; text-align: center; font-size: 0.8rem; color: #706f6c;">
            &copy; {{ date('Y') }} {{ config('app.name', 'POS System') }}. All rights reserved.
        </footer>
    </body>
</html>
