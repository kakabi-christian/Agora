<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agora Coopérative')</title>
    
    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --secondary: #f093fb;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
            --white: #ffffff;
            --gray: #6c757d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }
        
        .nav-menu a:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        .nav-menu .active {
            background-color: rgba(255,255,255,0.2);
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        /* Card */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .card-header {
            border-bottom: 2px solid var(--light);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header h2 {
            color: var(--primary);
            font-size: 24px;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background-color: var(--gray);
            color: white;
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: var(--success);
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: var(--danger);
            color: #721c24;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: var(--warning);
            color: #856404;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: var(--info);
            color: #0c5460;
        }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            font-size: 12px;
            font-weight: 600;
            border-radius: 12px;
            text-transform: uppercase;
        }
        
        .badge-success {
            background-color: var(--success);
            color: white;
        }
        
        .badge-warning {
            background-color: var(--warning);
            color: var(--dark);
        }
        
        .badge-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .badge-info {
            background-color: var(--info);
            color: white;
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark);
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-menu {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .container {
                padding: 0 1rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <a href="{{ url('/') }}" class="logo">
                🌾 Agora Coopérative
            </a>
            
            <ul class="nav-menu">
                @guest
                    <li><a href="{{ url('/') }}">Accueil</a></li>
                    <li><a href="{{ route('demandes.create') }}">Adhérer</a></li>
                    <li><a href="{{ route('login') }}">Connexion</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Tableau de bord</a></li>
                    <li><a href="{{ route('profil.show') }}" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}">Mon Profil</a></li>
                    <li><a href="{{ route('ressources.index') }}" class="{{ request()->routeIs('ressources.*') ? 'active' : '' }}">Ressources</a></li>
                    
                    @if(auth()->user()->role === 'administrateur')
                        <li><a href="{{ route('admin.demandes.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Administration</a></li>
                    @endif
                    
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Déconnexion</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                ✗ {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Erreurs de validation :</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <p>&copy; {{ date('Y') }} Agora Coopérative. Tous droits réservés.</p>
        <p style="font-size: 14px; margin-top: 0.5rem; opacity: 0.8;">
            Coopérative agricole et de développement local
        </p>
    </footer>
    
    @stack('scripts')
</body>
</html>
