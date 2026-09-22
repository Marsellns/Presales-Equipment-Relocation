<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim($__env->yieldContent('title', 'Equipment Relocation & Presales')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        body { background: #f6f8fb; }
        .feature-nav { background: #111827; }
        .feature-nav .navbar-brand, .feature-nav .nav-link { color: #fff; }
        .feature-nav .nav-link:hover, .feature-nav .nav-link.active { color: #f4b000; }
        .feature-main { min-height: calc(100vh - 56px); }
        .btn-brand { background: #e11d48; border-color: #e11d48; color: #fff; }
        .btn-brand:hover { background: #be123c; border-color: #be123c; color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg feature-nav">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('equipment-relocation.index') }}">Equipment Relocation &amp; Presales</a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#featureNav" aria-controls="featureNav" aria-expanded="false" aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="featureNav">
                @auth
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('equipment-relocation.*') ? 'active' : '' }}" href="{{ route('equipment-relocation.index') }}">Equipment Relocation</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('presales.*') ? 'active' : '' }}" href="{{ route('presales.index') }}">Presales</a></li>
                        @if (auth()->user()->hasRole('admin'))
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.user-approvals.index') }}">Persetujuan Akun</a></li>
                        @endif
                    </ul>
                    <div class="d-flex align-items-center gap-3 text-white">
                        <span class="small">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container-fluid px-4 py-4 feature-main">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
