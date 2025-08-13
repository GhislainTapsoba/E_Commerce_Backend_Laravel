{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ECMS') }} - @yield('title', 'Administration')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .content-wrapper {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
        }
        .logo-border-shadow {
            border: 3px solid indigo;      /* Bordure indigo */
            border-radius: 8px;            /* Coins arrondis */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Ombre portée */
            padding: 5px;                  /* Un petit espace entre image et bordure */
            background-color: white;       /* Fond blanc pour contraste */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">{{ config('app.name', 'ECMS') }}</h4>
                        <small class="text-white-50">Administration</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Tableau de bord
                            </a>
                        </li>
                        
                        @can('orders.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                <i class="bi bi-cart-check me-2"></i>
                                Commandes
                            </a>
                        </li>
                        @endcan
                        
                        @can('customers.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                <i class="bi bi-people me-2"></i>
                                Clients
                            </a>
                        </li>
                        @endcan
                        
                        @can('delivery-zones.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('delivery-zones.*') ? 'active' : '' }}" href="{{ route('delivery-zones.index') }}">
                                <i class="bi bi-geo-alt me-2"></i>
                                Zones de livraison
                            </a>
                        </li>
                        @endcan
                        
                        @can('statistics.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('statistics.*') ? 'active' : '' }}" href="{{ route('statistics.index') }}">
                                <i class="bi bi-graph-up me-2"></i>
                                Statistiques
                            </a>
                        </li>
                        @endcan
                        
                        @can('notifications.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                                <i class="bi bi-bell me-2"></i>
                                Notifications
                            </a>
                        </li>
                        @endcan
                        
                        @can('users.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="bi bi-person-gear me-2"></i>
                                Utilisateurs
                            </a>
                        </li>
                        @endcan
                    </ul>
                    
                    <hr class="text-white-50">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-circle me-2"></i>
                                Mon profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper">
                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Administration')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::user()?->name ?? 'Invité' }}
                            </button>

                            <ul class="dropdown-menu">
                                <li><span class="dropdown-item-text small">{{ Auth::user()?->getRoleNames()->first() ?? 'Aucun rôle' }}</span></li>
                                <li><hr class="dropdown-divider"></li>

                                @if(Auth::check())
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon profil</a></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Déconnexion
                                        </a>
                                    </li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('login') }}">Connexion</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
