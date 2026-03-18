
// Eye icons and password fields
const eyeIcon1 = document.querySelector(".eye-icon1");
const eyeIcon2 = document.querySelector(".eye-icon2");
const pwField = document.getElementById("newpass");
const pwField2 = document.getElementById("newcfpass");

// Toggle password visibility
eyeIcon1.addEventListener("click", () => {
    togglePasswordVisibility(pwField, eyeIcon1);
});

eyeIcon2.addEventListener("click", () => {
    togglePasswordVisibility(pwField2, eyeIcon2);
});

function togglePasswordVisibility(passwordField, eyeIcon) {
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.replace("bx-hide", "bx-show");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.replace("bx-show", "bx-hide");
    }
}

// Password match status
const passwordMatchStatus = document.getElementById("passwordMatchStatus");

pwField.addEventListener("input", checkPasswordsMatch);
pwField2.addEventListener("input", checkPasswordsMatch);

function checkPasswordsMatch() {
    const pwValue = pwField.value;
    const pw2Value = pwField2.value;

    if (pwValue === pw2Value) {
        passwordMatchStatus.textContent = "Passwords match";
        passwordMatchStatus.style.color = "green";
    } else {
        passwordMatchStatus.textContent = "Passwords do not match";
        passwordMatchStatus.style.color = "red";
    }
}

// Password validation
var myInput = document.getElementById("newpass");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");

myInput.onfocus = function () {
    document.getElementById("message").style.display = "block";
}

myInput.onblur = function () {
    document.getElementById("message").style.display = "none";
}

myInput.onkeyup = function () {
    var lowerCaseLetters = /[a-z]/g;
    if (myInput.value.match(lowerCaseLetters)) {
        letter.classList.remove("invalid");
        letter.classList.add("valid");
    } else {
        letter.classList.remove("valid");
        letter.classList.add("invalid");
    }

    var upperCaseLetters = /[A-Z]/g;
    if (myInput.value.match(upperCaseLetters)) {
        capital.classList.remove("invalid");
        capital.classList.add("valid");
    } else {
        capital.classList.remove("valid");
        capital.classList.add("invalid");
    }

    var numbers = /[0-9]/g;
    if (myInput.value.match(numbers)) {
        number.classList.remove("invalid");
        number.classList.add("valid");
    } else {
        number.classList.remove("valid");
        number.classList.add("invalid");
    }

    if (myInput.value.length >= 8 ) {
        length.classList.remove("invalid");
        length.classList.add("valid");
        length.style.color = "";
    } else {
        length.classList.remove("valid");
        length.classList.add("invalid");
        length.style.color = "red";
    }
}

//if match can update
/* Function to submit the form after validating passwords*/
function submitForm(event) {
    // Prevent form submission before validation
    
    const pwValue = pwField.value;
    const pw2Value = pwField2.value;

    // Check if passwords match
    if (pwValue !== pw2Value) {
        // If passwords don't match, prevent form submission and display an error message
        event.preventDefault();
        console.log("Passwords do not match"); // Debugging statement
        swal({
            icon: 'error',
            title: 'Error',
            text: 'Passwords do not match. Please make sure your passwords match.'
        });

        return; // Exit the function without submitting the form
    }
}

