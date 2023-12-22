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
                    <table id="table1" class="table table-bordered table-striped table-hover" style="cursor: pointer;">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="5%">St</th>
                                <th>Nama</th>
                                <th>Alasan</th>
                                <th width="20%">Tanggal</th>
                                <th width="10%">jam_mulai</th>
                                <th width="20%">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $lemburan)
                                <tr onclick="showOption({{ $lemburan }})"
                                    class="{{ $lemburan->is_lewat_hari == '1' ? 'bg-info text-white' : '' }}">
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
                                        @if ($lemburan->approve == 3)
                                            <a href="#" data-toggle="tooltip" data-placement="right"
                                                title="{{ $lemburan->approver }}"><i
                                                    class="fa-solid fa-ban text-warning fa-xl"></i></a>
                                        @endif
                                    </td>
                                    <td>{{ $lemburan->name }}</td>
                                    <td>{{ $lemburan->alasan }}</td>
                                    <td>{{ $lemburan->tanggal }}</td>
                                    <td>{{ $lemburan->jam_mulai }}</td>
                                    <td>{{ $lemburan->tgl_dibuat }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
    <!-- Modal -->
    <div class="modal fade" id="lemburanModal" tabindex="-1" role="dialog" aria-labelledby="lemburanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lemburanModalLabel">Lemburan</h5>
                    <button type="button" class="close" onclick="$('#lemburanModal').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ url('updatelemburstatus') }}" method="post" id="formUpdateLembur">
                    @csrf
                    <div class="modal-body">
                        <p id="alasanLembur">...</p>
                        <input type="hidden" name="status" id="statusLembur">
                        <input type="hidden" name="id" id="idLembur">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="updateLemburStatus('1')">Approve</button>
                        <button type="button" class="btn btn-danger" onclick="updateLemburStatus('2')">Decline</button>
                        <button type="button" class="btn btn-warning" onclick="updateLemburStatus('3')">Cancel</button>
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
        function showOption(dataLembur) {
            console.log(dataLembur);
            let optionModal = document.getElementById('lemburanModal');
            $('#lemburanModalLabel').text(dataLembur.name);
            $('#alasanLembur').text(dataLembur.alasan);
            $('#idLembur').val(dataLembur.id);
            $('#lemburanModal').modal('show');
            // Swal.fire({
            //     title: dataLembur.name,
            //     text: dataLembur.alasan,
            //     showDenyButton: true,
            //     showCancelButton: true,
            //     confirmButtonText: 'Batalkan',
            //     denyButtonText: `Setujui`,
            // }).then((result) => {
            //     /* Read more about isConfirmed, isDenied below */
            //     if (result.isConfirmed) {
            //         updateLemburStatus('gg');
            //     } else if (result.isDenied) {
            //         Swal.fire('Changes are not saved', '', 'info')
            //     }
            // })
        }

        function updateLemburStatus(status) {
            $('#statusLembur').val(status);
            $('#formUpdateLembur').submit();

        }
    </script>
@endsection
