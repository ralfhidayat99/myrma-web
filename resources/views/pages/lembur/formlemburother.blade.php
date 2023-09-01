@extends('layout.main')
<link rel="stylesheet" href="assets/extensions/choices.js/public/assets/styles/choices.css" />


@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h4>Form Lembur</h4>
                </div>
                <div class="col-6 text-end">
                    <a href="/" class="btn btn-info"><i class="bi bi-house"></i></a>
                </div>
            </div>
        </div>
        <div class="card-body px-2 py-3-4">


            <form class="form" action="/storelemburother" method="post">
                @csrf
                <div class="form-group">

                </div>
                <!--Time picker -->
                <div class="form-group row">
                    <div class="col-md-6 col-sm-12">
                        <label class="control-label" for="tanggal">Tanggal</label>
                        <input type="date" value="" class="form-control @error('tanggal') is-invalid @enderror"
                            id="tanggal" name="tanggal">
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="form-group form-label-group">
                    <label for="alasan">Alasan</label>

                    <div class="textarea-wrapper">
                        <textarea id="alasan" rows="3" class="form-control" name="alasan"></textarea>
                        <span id="charCount"></span>
                    </div>
                    @error('alasan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group form-label-group">
                    <label for="alasan">Untuk</label>
                    <div class="form-group">
                        <select class="choices form-select multiple-remove" multiple="multiple" name="untuk[]">
                            @foreach ($employee as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <input type="radio" class="form-check-input form-check-success" id="option1" name="is_hari_libur"
                        value="0" checked>
                    <label for="option1">Lembur di hari kerja</label>

                </div>
                <div class="form-group">
                    <input type="radio" class="form-check-input form-check-success" id="option2" name="is_hari_libur"
                        value="1">
                    <label for="option2">Lembur di hari libur</label>
                </div>
        </div>



        <div class="form-actions d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-1">Submit</button>
            <button type="reset" class="btn btn-light-primary">Cancel</button>
        </div>
        </form>

    </div>
    <div class="text-danger" style="margin-top: -25px"><strong>*Catatan : </strong> Pastikan kamu absen dengan benar
        , penghitungan waktu lembur menggunakan pembulatan kebawah.</div><br>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        var textarea = document.getElementById("alasan");
        var charCount = document.getElementById("charCount");
        var maxLength = 100; // Jumlah karakter maksimal yang diperbolehkan

        textarea.addEventListener("input", function() {
            console.log('sss');
            textarea.value = textarea.value.slice(0, maxLength); // Menghapus karakter tambahan
            var remainingChars = maxLength - textarea.value.length;
            charCount.textContent = remainingChars + '/100';

        });
    </script>
    <script>
        // Mendapatkan elemen input datetime-local
        var dateTimeInput = document.getElementById("tanggal");

        // Mendapatkan waktu saat ini
        var currentDateTime = new Date().toISOString().slice(0, 10);

        // Mengatur nilai default
        dateTimeInput.value = currentDateTime;
        flatpickr("#tanggal", {
            // minDate: "today",
            dateFormat: "Y-m-d",
        });
    </script>
    <script src="assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script src="assets/static/js/pages/form-element-select.js"></script>
@endsection
