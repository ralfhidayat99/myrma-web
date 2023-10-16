@extends('layout.main')

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header ">
                <div class="row">
                    <div class="col-10">
                        <h4>Tambah Karyawan</h4>
                    </div>

                </div>
            </div>
            <hr style="margin-top: -20px">
            <div class="card-body" style="width: 100%">
                <form action="{{ url('users') }}" method="POST">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-1">
                        <input type="text" class="form-control form-control-xl @error('nama') is-invalid @enderror"
                            placeholder="Nama" name="name">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-1">
                        <input type="text" class="form-control form-control-xl @error('jabatan') is-invalid @enderror"
                            placeholder="Jabatan" name="jabatan">
                        <div class="form-control-icon">
                            <i class="fa-sharp fa-solid fa-briefcase"></i>
                        </div>
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-1">
                        <input type="text" class="form-control form-control-xl @error('departemen') is-invalid @enderror"
                            placeholder="Departemen" name="departemen">
                        <div class="form-control-icon">
                            <i class="fa-solid fa-users-rectangle"></i>
                        </div>
                        @error('departemen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-1">
                        <select name="divisi" class="form-control form-control-xl" id="divisi">
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
                        <select name="id_atasan" class="form-control form-control-xl" id="atasan">
                            <option value="">-Supervisor-</option>
                            @foreach ($supervisor as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-control-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-1">
                        <input type="text" class="form-control form-control-xl @error('username') is-invalid @enderror"
                            placeholder="Username" name="username">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- <div class="form-group position-relative has-icon-left mb-1">
                        <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror"
                            placeholder="Password" name="password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-3">Simpan</button>
                </form>

            </div>
        </div>
    </section>
@endsection
