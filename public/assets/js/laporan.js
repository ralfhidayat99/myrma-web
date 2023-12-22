document.addEventListener("DOMContentLoaded", () => {
    const dropzones = document.getElementsByClassName("dropzone");
    const fileInputs = document.getElementsByClassName("fileInput");
    const filenameTexts = document.getElementsByClassName("filename");
    const dragTexts = document.getElementsByClassName("dragText");
    const splashAnimation = document.getElementById("splash");
    const period = document.getElementById("periodePicker");
    let periode = [];
    let dataLembur = [];

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
            periode = this.getPeriod(period.value)
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
                prosesAbsen(file, periode[i]);
            }
        }
        function prosesAbsen(file, periode) {
            document.getElementById("absen2Loading").style.display = "inline-block";
            const formData = new FormData();
            formData.append("absen", file);
            formData.append("filter", periode);
            formData.append("key", i);
            console.log('periode :' +periode);
        
            fetch("/api/absen-pertama", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    // console.log(data);
                    dataLembur.push(data["lembur"]);
                    console.log(dataLembur);
                    let form = document.getElementById("formGenrate");
                        laporan = data["lembur"];
                        form.appendChild(this.addInput("laporan"+(i+1), laporan));
                    // Lakukan manipulasi data atau tindakan lain sesuai kebutuhan Anda di sini
                    if (data["status"] == 200) {
                        Swal.fire({
                            title: "Success!",
                            text: data["message"],
                            icon: "success",
                            confirmButtonText: "Tutup",
                        });
                        fileInputs[i].file = file;
                            filenameTexts[i].textContent = file.name;
                            filenameTexts[i].classList.add("show");
                            filenameTexts[i].style.display = "inline-block";
                            dragTexts[i].style.display = "none";
                        
                    } else {
                        Swal.fire({
                            title: data["message"],
                            text: data["userNotFound"],
                            icon: "error",
                            confirmButtonText: "Tutup",
                        });
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                })
                .finally(() => {
                    // Menyembunyikan indikator loading setelah proses selesai
                    document.getElementById("absen2Loading").style.display = "none";
                });
        }
        
    }

})

function addInput(name, value) {
    let input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = JSON.stringify(value);
    return input;
}

function getPeriod(tgl) {
    // Tanggal awal dan akhir dalam format "d-m-Y"
    var bln = tgl.split(' to ');
    var tanggal_awal, tanggal_akhir;

    if (bln.length > 1) {
        tanggal_awal = bln[0];
        tanggal_akhir = bln[1];
    } else {
        tanggal_awal = bln[0];
        tanggal_akhir = bln[0];
    }

    // Ubah format tanggal awal dan tanggal akhir ke "Y-m-d"
    var tglAwalParts = tanggal_awal.split('-');
    tanggal_awal = tglAwalParts[2] + '-' + tglAwalParts[1] + '-' + tglAwalParts[0];

    var tglAkhirParts = tanggal_akhir.split('-');
    tanggal_akhir = tglAkhirParts[2] + '-' + tglAkhirParts[1] + '-' + tglAkhirParts[0];

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
        var akhir_bulan = new Date(tahun, bulan, 0).getDate();
        var tanggal_akhir_periode = tahun + '-' + (bulan < 10 ? '0' : '') + bulan + '-' + (akhir_bulan < 10 ? '0' : '') + akhir_bulan;

        // Jika tanggal akhir periode melebihi tanggal akhir rentang, atur tanggal akhir ke tanggal akhir rentang
        if (datetime_akhir < new Date(tanggal_akhir_periode)) {
            tanggal_akhir_periode = tanggal_akhir;
        }

        // Ubah format tanggal awal dan tanggal akhir kembali ke "d-m-Y"
        var tglAwalISO = tanggal_awal_periode.split('-');
        tanggal_awal_periode = tglAwalISO[2] + '-' + tglAwalISO[1] + '-' + tglAwalISO[0];

        var tglAkhirISO = tanggal_akhir_periode.split('-');
        tanggal_akhir_periode = tglAkhirISO[2] + '-' + tglAkhirISO[1] + '-' + tglAkhirISO[0];

        // Tambahkan pasangan tanggal awal dan tanggal akhir ke dalam array
        periode_tanggal.push([tanggal_awal_periode, tanggal_akhir_periode]);

        // Pindah ke bulan berikutnya
        datetime_awal.setMonth(datetime_awal.getMonth() + 1);
        datetime_awal.setDate(1); // Atur tanggal ke 1 untuk menghindari perubahan yang tidak diinginkan di bulan berikutnya
    }

    return periode_tanggal;
}



