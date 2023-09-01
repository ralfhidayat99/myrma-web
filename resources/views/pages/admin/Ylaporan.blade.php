@extends('layout.main')


<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">


@section('content')
    <div class="page-heading">
        <div class="col-12 col-md-12">
            <div class="card">
                <form action="{{ route('lemburan.read') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header">
                        <h4>Genrate laporan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body" id="card-form">
                            <p class="card-text">Upload file absensi disini
                            </p>
                            <!-- Basic file uploader -->
                            <input type="file" name="fileinput">
                            <input type="file" class="basic-filepond" id="fileInput">
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="submit" value="Genrate" class="btn btn-success">

                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- filepond validation -->
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

    <!-- image editor -->
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-filter/dist/filepond-plugin-image-filter.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>

    <!-- toastify -->
    <script src="assets/vendors/toastify/toastify.js"></script>

    <!-- filepond -->
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        // register desired plugins...
        FilePond.registerPlugin(
            // validates the size of the file...
            FilePondPluginFileValidateSize,
            // validates the file type...
            FilePondPluginFileValidateType,

            // calculates & dds cropping info based on the input image dimensions and the set crop ratio...
            FilePondPluginImageCrop,
            // preview the image file type...
            FilePondPluginImagePreview,
            // filter the image file
            FilePondPluginImageFilter,
            // corrects mobile image orientation...
            FilePondPluginImageExifOrientation,
            // calculates & adds resize information...
            FilePondPluginImageResize,
        );

        // Filepond: Basic
        const fileExcel = document.querySelector('.basic-filepond');
        const pond = FilePond.create(fileExcel, {
            allowImagePreview: false,
            allowMultiple: false,
            allowFileEncode: false,
            required: true,

        });

        // Access the underlying FilePond root element
        const filePondElement = pond.element;

        let fileInput = document.getElementById('fileInput');

        filePondElement.addEventListener("drop", function(e) {
            // Prevent the default behavior
            e.preventDefault();

            // Access the dropped files
            const files = e.dataTransfer.files;

            // Process the dropped files
            // You can perform any necessary operations with the dropped files here

            // Buat klon elemen input tipe file
            var inputElement = document.createElement("input");

            // Atur atribut tipe file
            inputElement.setAttribute("type", "file");

            // Atur atribut nama
            inputElement.setAttribute("name", "excel_file");

            // Reset nilai file pada elemen form

            // Tambahkan atribut 'value' pada klon elemen input tipe file
            inputElement.value = null;
            // console.log(newFileInput);
            inputElement.file = files[0];
            fileInput.setAttribute("name", "excel_file");
            fileInput.file = files[0];
            filePondElement.file = files[0];
            // inputElement.value = "yterer";

            var targetElement = document.getElementById(
                "card-form"); // Ganti "targetDiv" dengan ID elemen target Anda
            targetElement.appendChild(inputElement)

            console.log(filePondElement);
            // Gantikan elemen input tipe file dengan klon yang baru
        });
    </script>
@endsection
