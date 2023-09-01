@extends('layout.main')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-12">
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Tambah Penjualan</h4>
                            </div>
                            <div class="card-body">
                                <form action="/penjualan" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="pembeli">Pembeli</label>
                                        <input type="text" class="form-control" id="pembeli" name="pembeli" placeholder="Pembeli">
                                    </div>
                                    <div class="form-group">
                                        <label for="jumlah">Jumlah</label>
                                        <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="Jumlah">
                                    </div>
                                    <div class="form-group">
                                        <label for="harga">Harga</label>
                                        <input type="text" class="form-control" id="harga" name="harga" placeholder="Harga">
                                    </div>

                                    <input type="submit" class="btn btn-primary" value="Simpan">
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection