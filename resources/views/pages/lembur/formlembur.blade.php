@extends('layout.main')

<style>
    .textarea-wrapper {
        position: relative;
    }

    #charCount {
        position: absolute;
        bottom: 5px;
        right: 5px;
        font-size: 12px;
        color: gray;
    }
</style>
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        <h4>Form Lembur</h4>
                    </div>
                    {{-- <div class="col-6 text-end">
                        <a href="/" class="btn btn-info"><i class="bi bi-house"></i></a>
                    </div> --}}
                </div>
            </div>
            <div class="card-body px-2 py-3-4">
                <form class="form" action="/storelembur" method="post">
                    @csrf
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

                    @if (!$is_office)
                        <div class="form-group row">
                            {{-- <input type="time" id="jam_mulai" name="jam_mulai" value="1"> --}}
                            <div class="col-md-6 col-sm-12">
                                <label for="jam_mulai">Jam Mulai</label>
                                <input type="time" value=""
                                    class="form-control @error('jam_mulai') is-invalid @enderror" id="jam_mulai"
                                    name="jam_mulai">

                            </div>
                        </div>
                    @endif

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

                    <div class="form-group">
                        <input type="radio" class="form-check-input form-check-success" id="option1"
                            name="is_hari_libur" value="0" checked>
                        <label for="option1">Lembur di hari kerja</label>

                    </div>
                    <div class="form-group">
                        <input type="radio" class="form-check-input form-check-success" id="option2"
                            name="is_hari_libur" value="1">
                        <label for="option2">Lembur di hari libur</label>
                    </div>


                    <div class="form-actions d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-1">Submit</button>
                        <button type="reset" class="btn btn-light-primary">Cancel</button>
                    </div>
                </form>

            </div>

        </div>
        <div class="text-danger" style="margin-top: -25px"><strong>*Catatan : </strong> Pastikan kamu absen dengan benar
            , penghitungan waktu lembur menggunakan pembulatan kebawah.</div><br>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- <script>
        var textarea = document.getElementById("alasan");
        var charCount = document.getElementById("charCount");
        var maxLength = 100; // Jumlah karakter maksimal yang diperbolehkan

        textarea.addEventListener("input", function() {
            console.log('sss');
            textarea.value = textarea.value.slice(0, maxLength); // Menghapus karakter tambahan
            var remainingChars = maxLength - textarea.value.length;
            charCount.textContent = remainingChars + '/100';

        });

        // Mendapatkan elemen input waktu
        const timeInput = document.getElementById("jam_mulai");

        // Mendapatkan waktu yang ingin dinonaktifkan
        const disabledTime = "09:00";

        // Menambahkan event listener untuk memeriksa setiap perubahan waktu pada input
        timeInput.addEventListener("input", function() {
            var selectedTime = timeInput.value
            var startTime = "00:00";
            var endTime = "17:00";

            if (parseInt(selectedTime) >= parseInt(startTime) && parseInt(selectedTime) <= parseInt(endTime)) {
                alert("Waktu yang dipilih tidak tersedia");
                timeInput.value = '17:00';
            } else {
                timeInput.setCustomValidity("");
            }

        });
    </script> --}}
    <script>
        // Mendapatkan elemen input datetime-local
        var dateTimeInput = document.getElementById("tanggal");

        // Mendapatkan waktu saat ini
        var currentDateTime = new Date().toISOString().slice(0, 10);

        // Mengatur nilai default
        dateTimeInput.value = currentDateTime;
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        flatpickr("#tanggal", {
            minDate: yesterday,
            dateFormat: "Y-m-d",
        });
        flatpickr("#jam_mulai", {
            enableTime: true,
            time_24hr: true,
            noCalendar: true,
            dateFormat: "H:i",
            defaultDate: "16:00",
            // minTime: "16:00",
            // maxTime: "22:30",
        });
    </script>
@endsection
