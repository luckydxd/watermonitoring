document
    .getElementById("toggle-password")
    .addEventListener("click", function () {
        const passwordField = document.getElementById("password");
        const icon = this.querySelector("i");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("ti-eye-off");
            icon.classList.add("ti-eye");
        } else {
            passwordField.type = "password";
            icon.classList.remove("ti-eye");
            icon.classList.add("ti-eye-off");
        }
    });
