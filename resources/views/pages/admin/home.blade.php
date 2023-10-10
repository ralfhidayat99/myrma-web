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
                    <div class="col-4">
                        <h4 class="mt-2">Daftar Lemburan</h4>
                    </div>
                    <div class="col-md-8">

                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Periode </span>
                            <input type="text" class="form-control" placeholder="Pilih Tanggal" id="periodePicker"
                                name="month" value="{{ $bulanIni }}">
                            {{-- <select name="month" id="monthFilter" class="form-control">
                                @foreach ($months as $key => $month)
                                    <option value="{{ date('Y-m', strtotime($month)) }}"
                                        {{ $bulanIni == $month ? 'selected' : '' }}>
                                        {{ Carbon::parse(date('Y-m', strtotime($month . ' -1 month')) . '-25')->isoFormat('D MMMM Y') . ' - ' . Carbon::parse($month . '-24')->isoFormat('D MMMM Y') }}
                                    </option>
                                @endforeach
                            </select> --}}
                            <a class="btn btn-outline-info" type="button" href="#" id="myButton"><i
                                    class="fa fa-search" aria-hidden="true"></i></a>
                            <a class="btn btn-outline-success" type="button"
                                href="{{ route('admin.laporan', ['month' => $bulanIni]) }}" id="myButton">Excel</a>
                        </div>

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
                                <th width="5%">St</th>
                                <th>Nama</th>
                                <th>Alasan</th>
                                <th width="20%">Tanggal</th>
                                <th width="20%">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $lemburan)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>

                                    <td class="text-center" style="padding-top: 18px">
                                        @if ($lemburan->approve == 1)
                                            <a href="#" data-toggle="tooltip" data-placement="right"
                                                title="{{ $lemburan->approver }}"> <i
                                                    class="fa-solid fa-circle-check text-success fa-xl"></i></a>
                                        @endif
                                        @if ($lemburan->approve == 2)
                                            <a href="#" data-toggle="tooltip" data-placement="right"
                                                title="{{ $lemburan->approver }}"><i
                                                    class="fa-solid fa-circle-xmark text-danger fa-xl"></i></a>
                                        @endif
                                    </td>
                                    <td>{{ $lemburan->name }}</td>
                                    <td>{{ $lemburan->alasan }}</td>
                                    <td>{{ $lemburan->tanggal }}</td>
                                    <td>{{ $lemburan->tgl_dibuat }}</td>
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
