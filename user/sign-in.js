const eyeIcon = document.querySelector(".eye-icon1");
const pwField = document.getElementById("pw");


eyeIcon.addEventListener("click", () => {
    togglePasswordVisibility(pwField, eyeIcon);
});


// Function to toggle password visibility
function togglePasswordVisibility(passwordField, eyeIcon) {
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.replace("bx-hide", "bx-show");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.replace("bx-show", "bx-hide");
    }
}