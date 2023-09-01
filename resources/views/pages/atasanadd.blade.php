@extends('layout.main')

@section('content')
    <div class="page-content">
        <div class="stats-icon purple">
            <i class="iconly-boldProfile"></i>
        </div>
        <div class="page-heading">
            <h3>{{ $title }}</h3>
        </div>
        <section class="row">
            <div class="col-12 col-lg-12">
                <div class="row">
                    <div class="col-12 col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body px-2 py-3-4">
                                

                                <form class="form" method="post">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label for="feedback1" class="sr-only">Nama</label>
                                            <input type="text" id="feedback1" class="form-control" placeholder="Nama"
                                                name="nama">
                                        </div>
                                        
                                    </div>
                                    <div class="form-actions d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1">Submit</button>
                                        <button type="reset" class="btn btn-light-primary">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </section>
    </div>
    <script>
        // Time picker only
        $('#timepicker').datetimepicker({
            format: 'LT'
        });
    </script>
@endsection
