@extends('pelamar.dashboard.master_user')
@section('title', 'Akun | CVRE GENERATE')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h5 class="mb-0 font-weight-bold text-primary">Dashboard / Pengaturan / <span class="text-dark">Akun</span></h5>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-body m-10">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="{{asset('assets/akun/profil.png')}}" alt="Profile Picture" class="img-fluid rounded-circle mb-3 mx-auto d-block" style="width: 150px" />
                            <h5 class="font-weight-bold mb-2">{{Auth::user()->name}}</h5>
                            <!-- <p>UI/UX Designer</p> -->
                            <ul class="nav flex-column mt-8">
                                <li class="nav-item">
                                    <a class="nav-link active text-primary font-weight-bold m-3" href="#">Akun</a>
                                    <hr />
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-secondary m-3" href="#">Aktivitas</a>
                                    <hr />
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-secondary m-3" href="#">Lainnya</a>
                                    <hr />
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-8">
                            <div class="form-container" style="border: 1px solid; padding: 50px; border-radius: 8px">
                                <h4 class="mb-4 text-center font-weight-bold" style="font-size: 1.725rem; color: black">Akun</h4>
                                <form>
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="name" value="{{Auth::user()->name}}" />
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Alamat Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="johndoe@gmail.com" value="{{Auth::user()->email}}" />
                                    </div>
                                    <!-- 
                                    <div class="form-group">
                                        <label for="phone">Nomor Handphone</label>
                                        <input type="text" class="form-control" id="phone" placeholder="+628202192321" value="" />
                                    </div> -->

                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" placeholder="*********" value="{{Auth::user()->password}}" />
                                    </div>

                                    <div class="form-group">
                                        <label for="language">Tampilan Bahasa</label>
                                        <select class="form-control" id="language">
                                            <option selected>Indonesia</option>
                                            <option>English</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary d-block mx-auto mt-10">Simpan Perubahan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection