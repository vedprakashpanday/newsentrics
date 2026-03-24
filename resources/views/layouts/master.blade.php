<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="@yield('meta_keywords')">
    <link rel="icon" type="image/png" href="{{ asset('uploads/logo/newsentric.jpg') }}">
    <title>Newsentric Admin - @yield('title', 'Dashboard')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    
    
    <style>


        /* Base Styling */
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        
        /* Layout Wrappers */
        .wrapper { display: flex; width: 100%; align-items: stretch; min-height: 100vh; }
        
        /* Sidebar Styling */
        #sidebar { 
            min-width: 260px; max-width: 260px; 
            background: #212529; color: #fff; 
            transition: all 0.3s; z-index: 1040; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        #sidebar .sidebar-brand { padding: 1.2rem 1.5rem; font-size: 1.25rem; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.1); }
        #sidebar .nav-link { color: rgba(255,255,255,0.75); padding: 0.8rem 1.5rem; font-weight: 500; transition: all 0.2s; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); border-left: 4px solid #0d6efd; }
        #sidebar .nav-link i { font-size: 1.1rem; }
        
        /* Submenu Styling */
        #sidebar .collapse-inner { background: #343a40; border-radius: 0; padding: 0.5rem 0; }
        #sidebar .collapse-item { color: rgba(255,255,255,0.7); padding: 0.5rem 1.5rem 0.5rem 3rem; display: block; text-decoration: none; font-size: 0.9rem; transition: 0.2s; }
        #sidebar .collapse-item:hover, #sidebar .collapse-item.active { color: #fff; background: rgba(255,255,255,0.05); }

        /* Main Content */
        #content { width: 100%; min-width: 0; transition: all 0.3s; display: flex; flex-direction: column; }
        
        /* Top Navbar */
        .topbar { background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); z-index: 1030; height: 65px; }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; position: fixed; height: 100vh; overflow-y: auto; }
            #sidebar.active { margin-left: 0; }
            #content.active { margin-left: 0; } /* Keep full width on mobile when sidebar open */
            .overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1035; }
            .overlay.active { display: block; }
        }
    </style>
    @stack('styles') 
</head>
<body>

    <div class="wrapper">
        <div class="overlay" id="sidebarOverlay"></div>

        <nav id="sidebar">
            <div class="sidebar-brand d-flex align-items-center justify-content-center text-white text-decoration-none">
                <i class="bi bi-lightning-charge-fill text-primary me-2 fs-4"></i>
                <span>Newsentric <sup class="text-primary small">Admin</sup></span>
            </div>

           <ul class="nav flex-column mt-3">
    
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/news*') ? 'active' : 'collapsed' }} d-flex justify-content-between align-items-center" 
           href="#collapsePosts" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->is('admin/news*') ? 'true' : 'false' }}">
            <div><i class="bi bi-journal-text me-2"></i> Posts</div>
            <i class="bi bi-chevron-down small transition-transform" style="{{ request()->is('admin/news*') ? 'transform: rotate(180deg);' : '' }}"></i>
        </a>
        
        <div class="collapse {{ request()->is('admin/news*') ? 'show' : '' }}" id="collapsePosts">
            <div class="collapse-inner ps-3 mt-2">
                <a class="nav-link py-1 {{ request()->routeIs('admin.news.create') ? 'active fw-bold text-primary' : '' }}" href="{{ route('admin.news.create') }}">
                    <i class="bi bi-plus-circle me-1"></i> Add Manual News
                </a>
                <a class="nav-link py-1 {{ request()->routeIs('admin.news.index') ? 'active fw-bold text-primary' : '' }}" href="{{ route('admin.news.index') }}">
                    <i class="bi bi-inbox me-1"></i> Fetched News
                    @php $pendingNewsCount = \App\Models\News::where('status', 0)->count(); @endphp
                    @if($pendingNewsCount > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $pendingNewsCount }}</span>
                    @endif
                </a>
                <a class="nav-link py-1 {{ request()->routeIs('admin.news.manage') ? 'active fw-bold text-primary' : '' }}" href="{{ route('admin.news.manage') }}">
                    <i class="bi bi-list-task me-1"></i> Manage All News
                </a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <i class="bi bi-tags me-2"></i> Categories
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('admin/comments*') ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <i class="bi bi-chat-left-dots-fill me-2"></i> Comments
                </div>
                @php $pendingCount = \App\Models\Comment::where('status', 0)->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                        {{ $pendingCount }}
                    </span>
                @endif
            </div>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
            <i class="bi bi-file-earmark-text me-2"></i> Manage Pages
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.trending') }}" class="nav-link {{ request()->routeIs('admin.trending') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow me-2"></i> Trending News
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
            <i class="bi bi-gear me-2"></i> Settings
        </a>
    </li>

</ul>
        </nav>

        <div id="content">
            
            <nav class="navbar navbar-expand topbar px-4 d-flex justify-content-between align-items-center">
                <button type="button" id="sidebarCollapse" class="btn btn-light bg-white border shadow-sm">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-bold d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 35px; height: 35px;">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <span class="d-none d-lg-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item" href="/" target="_blank"><i class="bi bi-globe me-2 text-muted"></i> View Website</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger fw-bold"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid p-4 flex-grow-1">
                @yield('content')
            </div>

            <footer class="bg-white py-3 mt-auto border-top">
                <div class="container-fluid text-center text-muted small">
                    &copy; {{ date('Y') }} Newsentric Admin Panel. Crafted with <i class="bi bi-heart-fill text-danger"></i>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Toggle sidebar
            $('#sidebarCollapse, #sidebarOverlay').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#sidebarOverlay').toggleClass('active');
            });

            // Arrow rotation for dropdowns
            $('[data-bs-toggle="collapse"]').on('click', function() {
                $(this).find('.bi-chevron-down').css('transform', $(this).hasClass('collapsed') ? 'rotate(180deg)' : 'rotate(0deg)');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>