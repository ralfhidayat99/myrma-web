@extends('layout.main')

<link rel="stylesheet" href="{{ url('assets/extensions/simple-datatables/style.css') }}" />

<link rel="stylesheet" href="{{ url('./assets/compiled/css/table-datatable.css') }}" />

@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header ">
                <div class="row">
                    <div class="col-8">
                        <h4>Daftar Karyawan</h4>
                    </div>
                    @if (session()->has('kalibrasi'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading">Success</h4>
                            <p>{{ session('kalibrasi') }}</p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="col-md-4 d-flex flex-column-reverse">

                        <div class="btn-group mb-3" role="group" aria-label="Basic example">
                            <a href="users/create" type="button" class="btn btn-primary btn-sm">Tambah</a>
                            <a href="#" type="button" class="btn btn-success btn-sm" onclick="showKalibrasiModal()">
                                Kalibrasi
                            </a>
                        </div>
                        {{-- <a href="users/create" class="btn btn-primary btn-sm">Tambah Karyawan</a> --}}
                    </div>
                </div>
            </div>
            <hr style="margin-top:
                                -20px">
            <div class="card-body" style="width: 100%">

                <div class="table-responsive">
                    <table id="table1" class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th>departemen</th>
                                <th>divisi</th>
                                <th>jabatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $lemburan)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>

                                    <td>{{ $lemburan->name }}</td>
                                    <td>{{ $lemburan->departemen }}</td>
                                    <td>{{ $lemburan->divisi }}</td>
                                    <td>{{ $lemburan->jabatan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="kalibrasiModal" tabindex="-1" role="dialog" aria-labelledby="kalibrasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kalibrasiModalLabel">Upload data absensi terbaru</h5>
                    <button type="button" class="close" onclick="$('#kalibrasiModal').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="users/kalibrasi" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="file" name="absen" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ url('assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ url('assets/static/js/pages/simple-datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <script>
        $('#myButton').click(function() {
            var selectValue = $('#periodePicker').val();
            var url = "{{ url('lemburan') }}/" + selectValue;
            $('#myButton').attr('href', url);
        });

        flatpickr("#periodePicker", {
            // minDate: "today",
            mode: "range",
            dateFormat: "d-m-Y",
        });
    </script>
    <script>
        function showKalibrasiModal() {
            $('#kalibrasiModal').modal('show');
        }
    </script>
@endsection
