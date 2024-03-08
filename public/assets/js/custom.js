// fungsi untuk phone angka sajas
document
    .getElementById("phone_number")
    .addEventListener("keypress", function (event) {
        const keyCode = event.keyCode;
        if (keyCode < 48 || keyCode > 57) {
            event.preventDefault();
        }
    });

// Fungsi untuk menampilkan atau menyembunyikan kata sandi
document
    .getElementById("togglePassword")
    .addEventListener("click", function () {
        const passwordInput = document.getElementById("password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            this.textContent = "Sembunyikan";
        } else {
            passwordInput.type = "password";
            this.textContent = "Tampilkan";
        }
    });
