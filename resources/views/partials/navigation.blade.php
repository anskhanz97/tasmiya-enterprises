    @once<link rel="stylesheet" href="{{ asset('css/notifications.css') }}?v={{ filemtime(public_path('css/notifications.css')) }}">@endonce
    <nav class="navbar" id="navbar">
        <div class="navbar-container">
            <a href="{{ route('home') }}" class="navbar-brand">
                <span>🏢</span> Tasmiya
            </a>

            <button type="button" class="nav-mobile-trigger" aria-label="Open site navigation" aria-controls="mobileNav" aria-expanded="false">☰</button>
            <div class="nav-mobile-menu" id="mobileNav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('services.index') }}">Services</a>
                <a href="{{ route('team.index') }}">Team</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact.create') }}">Contact</a>
            </div>

            <div class="navbar-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('services.index') }}">Services</a>
                <a href="{{ route('team.index') }}">Team</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact.create') }}">Contact</a>
                
                {{-- User Menu --}}
                @auth
                    @can('viewAny', \App\Models\Lead::class)
                        @include('notifications.bell')
                    @endcan
                    <div class="user-menu">
                        <button type="button" class="user-menu-trigger" aria-controls="userDropdown" aria-expanded="false" aria-label="Open account menu">
                            <div class="user-avatar">{{ auth()->user()->initials }}</div>
                            <span>{{ auth()->user()->name }}</span>
                        </button>

                        <div class="user-dropdown" id="userDropdown">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                            @can('viewAny', \App\Models\Lead::class)<a href="{{ route('leads.index') }}">Lead Management</a>@endcan
                            @can('viewAny', \App\Models\Lead::class)<a href="{{ route('notifications.index') }}">Notifications</a>@endcan
                            <a href="{{ route('payments.index') }}">💳 Payments</a>
                            <a href="{{ route('contact.create') }}">📧 Contact</a>
                            @if(auth()->user()->profile)
                                <a href="{{ route('profiles.services.index', auth()->user()->profile) }}">💼 Add / Manage Services</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit">Sign Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Login</a>
                @endauth
            </div>
        </div>
    </nav>
