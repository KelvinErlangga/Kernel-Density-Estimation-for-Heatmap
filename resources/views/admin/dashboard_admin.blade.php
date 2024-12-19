@extends('admin.master_admin')
@section('title', 'Dashboard | CVRE GENERATE')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 bg-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-md font-weight-bold text-white text-uppercase mb-1">
                                Template Curriculum Vitae
                            </div>
                            <div class="h1 mb-0 font-weight-bold text-white">
                                {{$countTemplateCV}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('admin.template_curriculum_vitae.index')}}">
                                <i class="fas fa-clipboard-list fa-3x text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 bg-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-md font-weight-bold text-white text-uppercase mb-1">
                                Template Cover Letter
                            </div>
                            <div class="h1 mb-0 font-weight-bold text-white">
                                {{$countTemplateCL}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('admin.template_cover_letter.index')}}">
                                <i class="fas fa-clipboard-list fa-3x text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 bg-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-md font-weight-bold text-white text-uppercase mb-1">
                                Data Keahlian
                            </div>
                            <div class="h1 mb-0 font-weight-bold text-white">
                                {{$skills}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('admin.skills.index')}}">
                                <i class="fas fa-book fa-3x text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 bg-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-md font-weight-bold text-white text-uppercase mb-1">
                                Data Pekerjaan
                            </div>
                            <div class="h1 mb-0 font-weight-bold text-white">
                                {{$jobs}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('admin.users-admin')}}">
                                <i class="fas fa-id-badge fa-3x text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 bg-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-md font-weight-bold text-white text-uppercase mb-1">
                                Rekomendasi Keahlian
                            </div>
                            <div class="h1 mb-0 font-weight-bold text-white">
                                {{$recommendedSkills}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('admin.users-admin')}}">
                                <i class="fas fa-users-cog fa-3x text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 bg-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-md font-weight-bold text-white text-uppercase mb-1">
                                Data Pengguna
                            </div>
                            <div class="h1 mb-0 font-weight-bold text-white">
                                {{$userPelamar}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('admin.users-admin')}}">
                                <i class="fas fa-users fa-3x text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection