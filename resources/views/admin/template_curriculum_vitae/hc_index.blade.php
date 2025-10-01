@extends('admin.master_admin')

@section('title', 'Template CV | CVRE GENERATE')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Template Curriculum Vitae</h1>

        <!-- Card -->
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <!-- Tambah Template -->
                    <a href="{{ route('admin.template_curriculum_vitae.create') }}"
                       class="btn btn-primary mb-3">
                        Tambah Template CV
                    </a>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="30%" class="text-center">Gambar Template</th>
                                    <th class="text-center">Nama Template</th>
                                    <th width="25%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templateCurriculumVitaes as $templateCV)
                                    <tr>
                                        <td>
                                            <img src="{{ Storage::url($templateCV->thumbnail_curriculum_vitae) }}"
                                                 alt="{{ $templateCV->template_curriculum_vitae_name }}"
                                                 class="img-fluid rounded"
                                                 style="max-height: 420px;">
                                        </td>
                                        <td class="align-middle text-center">
                                            {{ $templateCV->template_curriculum_vitae_name }}
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <!-- Edit -->
                                                <a href="{{ route('admin.template_curriculum_vitae.edit', $templateCV->id) }}"
                                                   class="btn btn-sm btn-outline-primary mr-2">
                                                    <i class="far fa-fw fa-edit"></i> Edit
                                                </a>

                                                <!-- Nonaktif / Aktifkan -->
                                                @if(!$templateCV->trashed())
                                                    <form action="{{ route('admin.template_curriculum_vitae.destroy', $templateCV->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan template ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-fw fa-ban"></i> Nonaktifkan
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.template_curriculum_vitae.restore', $templateCV->id) }}"
                                                          method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-fw fa-check"></i> Aktifkan
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive -->

                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "ordering": true,
                "lengthChange": true,
                "pageLength": 10
            });
        });
    </script>
@endpush
