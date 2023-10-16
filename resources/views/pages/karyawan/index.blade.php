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
                    <div class="col-10">
                        <h4>Daftar Karyawan</h4>
                    </div>
                    <div class="col-md-2 d-flex flex-column-reverse">
                        <a href="users/create" class="btn btn-primary btn-sm">Tambah Karyawan</a>

                        {{-- </form> --}}
                    </div>
                </div>
            </div>
            <hr style="margin-top: -20px">
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
@endsection
