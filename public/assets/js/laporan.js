document.addEventListener("DOMContentLoaded", () => {
    const dropzones = document.getElementsByClassName("dropzone");
    const fileInputs = document.getElementsByClassName("fileInput");
    const splashAnimation = document.getElementById("splash");
    const period = document.getElementById("periodePicker");

    for (let i = 0; i < dropzones.length; i++) {
        const element = dropzones[i];
        element.addEventListener("dragover", (e) => {
            e.preventDefault();
            element.classList.add("dragover");
        });
    
        element.addEventListener("dragleave", () => {
            element.classList.remove("dragover");
        });

        element.addEventListener("drop", (e) => {
            e.preventDefault();
            element.classList.remove("dragover");
            element.classList.remove("hover");
            splashAnimation.style.display = "block";
            // console.log(period.value);
            let periode = this.getPeriod(period.value)
            console.log('index : '+periode);
            handleFile(e.dataTransfer.files[0]);
    
            // Buat klon elemen input tipe file
            const newFileInput = element.cloneNode(true);
    
            // Reset nilai file pada elemen form
            // fileInput1.form.reset();
    
            // Tambahkan atribut 'value' pada klon elemen input tipe file
            newFileInput.value = null;
            // console.log(newFileInput);
            newFileInput.files = e.dataTransfer.files;
    
            // Gantikan elemen input tipe file dengan klon yang baru
            element.parentNode.replaceChild(newFileInput, element);
            // element.appendChild(newFileInput);
            // Atur nilai file pada klon elemen input tipe file
        });
    
        element.addEventListener("click", () => {
            fileInputs[i].click();
            // fileInput.file
        });
        element.addEventListener("change", (e) => {
            handleFile(e.target.files[0]);
        });
        function handleFile(file) {
            if (file) {
                console.log('File selected:', file);
    
                // splashAnimation.style.display = 'none';
                // Lakukan tindakan yang diperlukan dengan file yang dipilih di sini
                // prosesAbsen(file, true);
            }
        }
    }

})

function handleFile(params) {
    
}

function getPeriod(tgl) {
    // Tanggal awal dan akhir dalam format "Y-m-d"
    var bln = tgl.split(' to ');
    var tanggal_awal, tanggal_akhir;

    if (bln.length > 1) {
        tanggal_awal = bln[0];
        tanggal_akhir = bln[1];
    } else {
        tanggal_awal = bln[0];
        tanggal_akhir = bln[0];
    }

    // Buat objek Date untuk tanggal awal dan akhir
    var datetime_awal = new Date(tanggal_awal);
    var datetime_akhir = new Date(tanggal_akhir);
    console.log('awal ' + tanggal_awal);

    // Inisialisasi array untuk menyimpan periode tanggal
    var periode_tanggal = [];

    // Iterasi melalui setiap periode
    while (datetime_awal <= datetime_akhir) {
        // Tanggal awal periode
        var tanggal_awal_periode = datetime_awal.toISOString().slice(0, 10);

        // Tanggal akhir periode
        var tahun = datetime_awal.getFullYear();
        var bulan = datetime_awal.getMonth() + 1;
        var tanggal_akhir_periode = tahun + '-' + (bulan < 10 ? '0' : '') + bulan + '-' + new Date(tahun, bulan, 0).getDate();

        // Jika tanggal akhir periode melebihi tanggal akhir rentang, atur tanggal akhir ke tanggal akhir rentang
        if (datetime_akhir < new Date(tanggal_akhir_periode)) {
            tanggal_akhir_periode = tanggal_akhir;
        }

        // Tambahkan pasangan tanggal awal dan tanggal akhir ke dalam array
        periode_tanggal.push([tanggal_awal_periode, tanggal_akhir_periode]);

        // Pindah ke bulan berikutnya
        datetime_awal.setMonth(datetime_awal.getMonth() + 1);
    }
    console.log(periode_tanggal);

    return periode_tanggal;
}
