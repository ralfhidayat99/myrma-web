<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mazer Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ url('assets/vendors/iconly/bold.css') }}">

    <link rel="stylesheet" href="{{ url('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendors/sweetalert2/sweetalert2.min.css') }}">

    <link rel="shortcut icon" href="{{ url('assets/images/favicon.svg') }}" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


</head>

<body>
    <div id="">

        <div id="col-8 mx-auto">

            <div class="page-content">
                <div class="col-12 col-xl-8 mx-auto mt-3">
                    <div class="row">
                        <div class="col-6">
                            <h3>Lemburan RMA</h3>
                        </div>
                        <h6 class="col-6 text-end">{{ auth()->user()->name }}, <a href="{{ url('logout') }}"
                                class="text-secondary">Keluar</a> <br>

                            <div id="spvLabel">
                                spv : {{ session('spv') }}
                            </div>
                        </h6>
                    </div>
                    <hr>
                    @yield('content')
                </div>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted col-8 mx-auto">
                    <div class="float-start">
                        <p>{{ date('Y') }} &copy; Mazer</p>
                    </div>

                    {{-- <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                                href="#">Mas IT</a></p>
                    </div> --}}
                </div>
            </footer>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> --}}
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ url('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ url('assets/js/bootstrap.bundle.min.js') }}"></script>

    {{-- <script src="assets/js/extensions/sweetalert2.js"></script> --}}
    <script src="{{ url('assets/vendors/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script src="{{ url('assets/js/mazer.js') }}"></script>

</body>

</html>
