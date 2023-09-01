document.addEventListener("DOMContentLoaded", () => {
    const dropzone1 = document.getElementById("dropzone1");
    const dropzone2 = document.getElementById("dropzone2");
    var fileInput1 = document.getElementById("fileInput1");
    var fileInput2 = document.getElementById("fileInput2");
    const dragText1 = document.getElementById("dragText1");
    const dragText2 = document.getElementById("dragText2");
    const filenameText1 = document.getElementById("filename1");
    const filenameText2 = document.getElementById("filename2");
    const splashAnimation = document.getElementById("splash");

    let laporan1, laporan2;

    dropzone1.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropzone1.classList.add("dragover");
    });

    dropzone1.addEventListener("dragleave", () => {
        dropzone1.classList.remove("dragover");
    });

    dropzone1.addEventListener("drop", (e) => {
        e.preventDefault();
        dropzone1.classList.remove("dragover");
        dropzone1.classList.remove("hover");
        splashAnimation.style.display = "block";
        // console.log(e.dataTransfer.files);
        handleFile1(e.dataTransfer.files[0]);

        // Buat klon elemen input tipe file
        const newFileInput = fileInput1.cloneNode(true);

        // Reset nilai file pada elemen form
        // fileInput1.form.reset();

        // Tambahkan atribut 'value' pada klon elemen input tipe file
        newFileInput.value = null;
        // console.log(newFileInput);
        newFileInput.files = e.dataTransfer.files;

        // Gantikan elemen input tipe file dengan klon yang baru
        fileInput1.parentNode.replaceChild(newFileInput, fileInput1);
        // dropzone1.appendChild(newFileInput);
        // Atur nilai file pada klon elemen input tipe file
    });

    dropzone1.addEventListener("click", () => {
        fileInput1.click();
        // fileInput.file
    });
    fileInput1.addEventListener("change", (e) => {
        handleFile1(e.target.files[0]);
    });
    function handleFile1(file) {
        if (file) {
            // console.log('File selected:', file);

            // splashAnimation.style.display = 'none';
            // Lakukan tindakan yang diperlukan dengan file yang dipilih di sini
            prosesAbsen(file, true);
        }
    }
    //==========================================================================
    dropzone2.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropzone2.classList.add("dragover");
    });

    dropzone2.addEventListener("dragleave", () => {
        dropzone2.classList.remove("dragover");
    });

    dropzone2.addEventListener("drop", (e) => {
        e.preventDefault();
        dropzone2.classList.remove("dragover");
        dropzone2.classList.remove("hover");
        splashAnimation.style.display = "block";

        handleFile2(e.dataTransfer.files[0]);

        // Buat klon elemen input tipe file
        const newFileInput = fileInput2.cloneNode(true);

        // Reset nilai file pada elemen form
        // fileInput2.form.reset();

        // Tambahkan atribut 'value' pada klon elemen input tipe file
        newFileInput.value = null;
        // console.log(newFileInput);
        newFileInput.files = e.dataTransfer.files;

        // Gantikan elemen input tipe file dengan klon yang baru
        fileInput2.parentNode.replaceChild(newFileInput, fileInput2);
        // dropzone2.appendChild(newFileInput);
        // Atur nilai file pada klon elemen input tipe file
    });

    dropzone2.addEventListener("click", () => {
        fileInput2.click();
        // fileInput.file
    });

    fileInput2.addEventListener("change", (e) => {
        handleFile2(e.target.files[0]);
    });

    function handleFile2(file) {
        if (file) {
            // console.log('File selected:', file);

            // splashAnimation.style.display = 'none';
            // Lakukan tindakan yang diperlukan dengan file yang dipilih di sini
            prosesAbsen(file, false);
        }
    }

    function prosesAbsen(file, first) {
        document.getElementById("absen2Loading").style.display = "inline-block";
        let filter = document.getElementById("monthFilter");
        const formData = new FormData();
        formData.append("filter", filter.value);
        formData.append("first", first);

        if (first) {
            formData.append("absen1", file);
        } else {
            formData.append("absen2", file);
        }

        fetch("/api/absen-pertama", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                let form = document.getElementById("formGenrate");
                if (data["first"]) {
                    laporan1 = data["lembur"];
                    form.appendChild(addInput("laporan1", laporan1));
                } else {
                    laporan2 = data["lembur"];
                    form.appendChild(addInput("laporan2", laporan2));
                }
                console.log(data);
                // Lakukan manipulasi data atau tindakan lain sesuai kebutuhan Anda di sini
                if (data["status"] == 200) {
                    Swal.fire({
                        title: "Success!",
                        text: data["message"],
                        icon: "success",
                        confirmButtonText: "Tutup",
                    });
                    if (first) {
                        fileInput1.file = file;
                        filenameText1.textContent = file.name;
                        filenameText1.classList.add("show");
                        filenameText1.style.display = "inline-block";
                        dragText1.style.display = "none";
                    } else {
                        fileInput2.file = file;
                        filenameText2.textContent = file.name;
                        filenameText2.classList.add("show");
                        filenameText2.style.display = "inline-block";
                        dragText2.style.display = "none";
                    }
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: data["message"],
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

    function addInput(name, value) {
        let input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = JSON.stringify(value);
        return input;
    }
});
