<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>@yield('title')</title>

    <!-- Font Awesome -->
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" href="{{asset('assets/icons/logo.svg')}}" type="image/x-icon">

    <!-- Custom styles -->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">

    <!-- Override font to Poppins -->
    <style>
        body, html {
            font-family: 'Poppins', sans-serif !important;
        }
    </style>

    @stack('style')
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-white sidebar sidebar-light accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('welcome')}}">
                <div class="sidebar-brand-icon">
                    <img src="{{asset('assets/homepage/logo.png')}}" alt="Logo" class="img-fluid" style="width: 139px" />
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0" />

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link text-dark" href="{{route('pelamar.dashboard.index')}}">
                    <i class="fas fa-fw fa-home text-dark"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Nav Item - Info Lowongan -->
            <li class="nav-item active">
                <a class="nav-link text-dark" href="{{route('pelamar.dashboard.lowongan.index')}}">
                    <i class="fas fa-fw fa-briefcase text-dark"></i>
                    <span>Info Lowongan</span>
                </a>
            </li>

            <!-- Nav Item - Heatmap Lowongan -->
            <li class="nav-item active">
                <a class="nav-link text-dark" href="{{route('pelamar.dashboard.heatmap.index')}}">
                    <i class="fas fa-fw fa-map text-dark"></i>
                    <span>Heatmap Lowongan</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Nav Item - Kelola -->
            <li class="nav-item">
                <a class="nav-link collapsed text-dark" href="#" data-toggle="collapse" data-target="#collapseTwo"
                   aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-sliders-h text-dark"></i>
                    <span>Kelola</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white collapse-inner rounded">
                        <a class="collapse-item text-dark" href="{{route('pelamar.dashboard.curriculum_vitae.index')}}">
                            <i class="fas fa-fw fa-id-card text-dark"></i> Curriculum Vitae
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Pengaturan -->
            <li class="nav-item">
                <a class="nav-link collapsed text-dark" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                   aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-cogs text-dark"></i>
                    <span>Pengaturan</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white collapse-inner rounded">
                        <a class="collapse-item text-dark" href="{{route('pelamar.dashboard.akun.index')}}">
                            <i class="fas fa-fw fa-user text-dark"></i> Akun
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="collapse-item text-dark" href="route('logout')"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-fw fa-sign-out-alt text-dark"></i> Keluar
                            </a>
                        </form>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block" />

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column" style="background-color:#f5f8ff">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- User Info -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    Hi, {{Auth::user()->name}}
                                </span>
                                <img class="img-profile rounded-circle" src="{{asset('assets/img/undraw_profile.svg')}}" />
                            </a>
                            <!-- Dropdown -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Page Content -->
                @yield('content')
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>CVRE Generate &copy; 2024</span>
                    </div>
                </div>
            </footer>
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="btn btn-primary" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            Logout
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable("#dataTables");
    </script>

    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="{{asset('js/sb-admin-2.min.js')}}"></script>

    @stack('scripts')
</body>
</html>
