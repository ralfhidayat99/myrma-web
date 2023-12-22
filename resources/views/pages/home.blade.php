@extends('layout.main')


@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header ">
                <div class="row">
                    <div class="col-md-3 col-sm-12 mt-2">
                        <h4>Daftar Lemburan</h4>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="input-group mb-3">
                            <input type="text" id="tglRange" name="daterange" class="form-control"
                                placeholder="Pilih Tanggal.." />
                            <a class="btn btn-outline-info" type="button" href="#" id="myButton"><i
                                    class="fa fa-search" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 text-end">
                        <a href="{{ url('/lembur') }}" class="btn btn-info" style="text-decoration: none"> Ajukan lembur</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-lg">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th width="25%">Alasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($lemburan->items() as $item)
                                <tr class="{{ $item->is_lewat_hari == 1 ? 'lewatHari' : '' }}">
                                    <td class="col-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-lg">

                                                @php
                                                    $img = $item->approve == 1 ? '/smile.png' : ($item->approve == 2 ? '/sad.jpg' : ($item->approve == 3 ? '/close.png' : '/clock.png'));
                                                @endphp
                                                <img src="{{ url('/assets/images/faces') . $img }}" alt="rtes">
                                            </div>
                                            <p class="font-bold ms-3 mb-0">
                                                {{ $item->approve == 1 ? 'Disetujui' : ($item->approve == 2 ? 'Ditolak' : ($item->approve == 3 ? 'Dibatalkan' : 'Menunggu')) }}
                                            </p>
                                        </div>
                                        <small class="text-danger">{{ $item->declined_reason }}</small>
                                    </td>
                                    <td class="col-3">
                                        <p class="mb-0">{{ $item->tanggal }}</p>
                                    </td>
                                    <td class="">
                                        <p class="mb-0">{{ $item->alasan }}</p>
                                    </td>

                                    <td class="col-1">
                                        @if ($item->approve != 3)
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button id="warning" class="btn btn-danger" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Batalkan Lembur"
                                                    onclick="cancelForm({{ $item->id }})"><i
                                                        class='fas fa-times text-white'></i></button>
                                                <button id="warning" class="btn btn-warning" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Ubah"
                                                    onclick="showUpdateFormModal({{ $item }})"><i
                                                        class='fas fa-edit text-white'></i></button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="card-footer" style="margin-top: -20px">
                <div class="pagination">
                    <ul>
                        <!-- Tampilkan tautan ke halaman sebelumnya jika tersedia -->
                        @if ($lemburan->onFirstPage())
                            <li class="disabled"><span>&laquo;</span></li>
                        @else
                            <li><a href="{{ $lemburan->previousPageUrl() }}" rel="prev">&laquo;</a></li>
                        @endif

                        <!-- Tampilkan tautan ke setiap halaman -->
                        @foreach ($lemburan->getUrlRange(1, $lemburan->lastPage()) as $page => $url)
                            @if ($page == $lemburan->currentPage())
                                <li class="active"><span>{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        <!-- Tampilkan tautan ke halaman berikutnya jika tersedia -->
                        @if ($lemburan->hasMorePages())
                            <li><a href="{{ $lemburan->nextPageUrl() }}" rel="next">&raquo;</a></li>
                        @else
                            <li class="disabled"><span>&raquo;</span></li>
                        @endif
                    </ul>
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
                    <h5 class="modal-title" id="lemburanModalLabel">Lembur sampai Besok ?</h5>
                    <button type="button" class="close" onclick="$('#lemburanModal').modal('hide')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ url('lewathari') }}" method="post" id="formUpdateLembur">
                    @csrf
                    <div class="modal-body">
                        <p>Lembur sampai besok ?.</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_lewat_hari" id="inlineRadio2"
                                value="0" @checked(true)>
                            <label class="form-check-label" for="inlineRadio2">Tidak</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_lewat_hari" id="inlineRadio1"
                                value="1">
                            <label class="form-check-label" for="inlineRadio1">Ya</label>
                        </div>
                        <input type="hidden" name="id" id="idLembur">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-warning"
                            onclick="$('#lemburanModal').modal('hide')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .lewatHari {
            background-color: #ffd6d6;
        }

        .pagination {
            display: inline-block;
        }

        .pagination ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .pagination li {
            display: inline;
            margin-right: 5px;
        }

        .pagination li a,
        .pagination li span {
            color: #000;
            padding: 8px 12px;
            text-decoration: none;
        }

        .pagination li.active span {
            font-weight: bold;
        }

        .pagination li.disabled span {
            color: #888;
            cursor: not-allowed;
        }

        .pagination li.disabled a {
            pointer-events: none;
            cursor: not-allowed;
            opacity: 0.6;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        flatpickr("#tglRange", {
            mode: "range",
            // minDate: "today",
            dateFormat: "Y-m-d",
        });
    </script>
    <script>
        // If you want to use tooltips in your project, we suggest initializing them globally
        // instead of a "per-page" level.
        document.addEventListener(
            "DOMContentLoaded",
            function() {
                var tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            },
            false
        );
    </script>

    <script>
        function cancelForm(id) {
            Swal.fire({
                icon: "warning",
                title: "Peringatan",
                text: "jika dibatalkan lemburan kamu tidak akan terdata di laporan lembur",
                showCancelButton: true,
                confirmButtonText: 'Batalkan',
                cancelButtonText: 'Ngga jadi'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('/batallembur/' + id, '_self')
                }
            })
        }

        function showUpdateFormModal(dataLembur) {
            $('#lemburanModalLabel').text(dataLembur.alasan);
            $('#idLembur').val(dataLembur.id);
            $('#lemburanModal').modal('show');

        }


        let searchBtn = document.getElementById('myButton');
        searchBtn.addEventListener('click', function() {
            console.log('pressss');
            var tglValue = $('#tglRange').val();
            if (tglValue != '') {
                var url = "{{ url('list') }}/" + tglValue;
                $('#myButton').attr('href', url);
            }
        });
    </script>
@endsection
