<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reegistrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/compiled/css/auth.css" />
    <link rel="stylesheet" href="./assets/compiled/css/app.css" />
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css">
</head>

<body>
    <div id="auth">

        <div class="row h-100" style="margin-top: -60px;">
            <div class="col-lg-6 col-12 mx-auto">
                <div id="auth-left">

                    <h4 class="auth-title fs-1">Sign Up</h4>
                    <p class="auth-subtitle mb-3 fs-5">Input your data to register.</p>

                    <form action="register" method="POST">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-1">
                            <input type="text"
                                class="form-control form-control-xl @error('nama') is-invalid @enderror"
                                placeholder="Nama" name="name">
                            <div class="form-control-icon">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-1">
                            <input type="text"
                                class="form-control form-control-xl @error('jabatan') is-invalid @enderror"
                                placeholder="Jabatan" name="jabatan">
                            <div class="form-control-icon">
                                <i class="fa-sharp fa-solid fa-briefcase"></i>
                            </div>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-1">
                            <input type="text"
                                class="form-control form-control-xl @error('departemen') is-invalid @enderror"
                                placeholder="Departemen" name="departemen">
                            <div class="form-control-icon">
                                <i class="fa-solid fa-users-rectangle"></i>
                            </div>
                            @error('departemen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-1">
                            <select name="divisi" class="form-control" id="divisi">
                                <option value="produksi">Produksi</option>
                                <option value="kantor & umum">Kantor & Umum</option>

                            </select>
                            <div class="form-control-icon">
                                <i class="fa fa-users" aria-hidden="true"></i>
                            </div>
                            @error('divisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-1" placeholder="Atasan">
                            <select name="id_atasan" class="form-control" id="atasan">
                                <option value="">-Supervisor-</option>
                                @foreach ($atasans as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-control-icon">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-1">
                            <input type="text"
                                class="form-control form-control-xl @error('username') is-invalid @enderror"
                                placeholder="Username" name="username">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-1">
                            <input type="password"
                                class="form-control form-control-xl @error('password') is-invalid @enderror"
                                placeholder="Password" name="password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-1">
                            <input type="password"
                                class="form-control form-control-xl @error('password') is-invalid @enderror"
                                placeholder="Confirm Password" name="cpassword">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-3">Sign Up</button>
                    </form>
                    <div class="text-center mt-2 text-lg fs-4">
                        <p class='text-gray-600'>Already have an account? <a href="{{ url('/login') }}"
                                class="font-bold">Log
                                in</a>.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
