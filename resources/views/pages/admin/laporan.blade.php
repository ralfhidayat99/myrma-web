@extends('layout.main')


<style>
    .dropzone {
        position: relative;
        width: 100%;
        height: 125px;
        border: 2px dashed #ccc;
        text-align: center;
        padding: 50px;
        font-size: 20px;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .dropzone.dragover {
        background-color: #f1f1f1;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .dropzone.hover {
        border-color: #77ccf7da;
    }

    .splash-animation {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 170, 255, 0.2);
        animation: splash 0.5s ease;
        pointer-events: none;
    }

    @keyframes splash {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .filename {
        font-size: 16px;
        color: #fff;
        background-color: #000;
        padding: 8px;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filenameText {
        color: #fff;
    }

    .filename {
        opacity: 0;
        transition: opacity 2s ease-in-out;
    }

    .filename.show {
        opacity: 1;
    }

    .remove-button {
        background: none;
        border: none;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        margin-left: 8px;
    }

    .remove-button:hover {
        color: #ff0000;
    }
</style>





@section('content')
    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
    @endphp
    <div class="row">
        <div class="col-12 col-lg-12 col-md-12">
            <div class="card">

                <div class="card-body px-2 py-3-4">
                    <div class="card">
                        <form action="{{ route('laporan.generate') }}" method="POST" id="formGenrate">
                            @csrf
                            <div class="card-header">

                                <h4>
                                    Genrate laporan</h4>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Periode</span>
                                    <input type="text" class="form-control" placeholder="Pilih Tanggal"
                                        id="periodePicker" name="month">
                                    {{-- <select name="month" id="monthFilter" class="form-control">
                                        @foreach ($months as $key => $month)
                                            <option value="{{ $month }}" {{ $bulanIni == $month ? 'selected' : '' }}>
                                                {{ Carbon::parse(date('Y-m', strtotime($month . ' -1 month')) . '-25')->isoFormat('D MMMM Y') . ' - ' . Carbon::parse($month . '-24')->isoFormat('D MMMM Y') }}
                                            </option>
                                        @endforeach
                                    </select> --}}

                                </div>
                            </div>
                            <div class="card-content" style="margin-top:-50px">
                                <div class="card-body">
                                    <p class="card-text">Upload file absensi disini
                                    </p>
                                    <!-- Basic file uploader -->
                                    {{-- <input type="file" name="excel_file"> --}}
                                    {{-- <input type="file" name="excel_file"> --}}
                                    <div id="dropzone1" class="dropzone" style="margin-top:-20px">
                                        <span id="dragText1">Drag and drop file here, or click to
                                            browse</span>
                                        <input name="absen1" type="file" id="fileInput1" style="display: none;">
                                        <div id="filename1" class="filename" style="display: none">
                                            <span id="filenameText"></span>

                                        </div>
                                        <div id="splash" class="splash-animation"></div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="card-text">Upload file absensi disini</p>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <div class="spinner-border text-danger" role="status" id="absen2Loading"
                                                style="display: none">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="dropzone2" class="dropzone">
                                        <span id="dragText2">Drag and drop file here, or click to browse</span>
                                        <input name="absen2" type="file" id="fileInput2" style="display: none;"
                                            @required(true)>
                                        <div id="filename2" class="filename" style="display: none">
                                            <span id="filenameText"></span>
                                        </div>
                                        <div id="splash" class="splash-animation"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="submit" value="Genrate" class="btn btn-success">

                            </div>
                        </form>

                        <a href="{{ url('laporan/test') }}" class="btn btn-warning">Test Generate</a>
                    </div>

                </div>
            </div>
        </div>


    </div>
    <script src="{{ url('assets/js/laporan.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        flatpickr("#periodePicker", {
            // minDate: "today",
            mode: "range",
            dateFormat: "d/m/Y",
        });
    </script>
    {{-- <style>
        .basic-filepond {
            width: 100%;
            /* Lebar yang diinginkan */
            height: 200px;
            /* Tinggi yang diinginkan */
        }
    </style> --}}
@endsection
