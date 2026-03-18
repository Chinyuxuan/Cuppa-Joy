<script src='https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js'></script>

    const eyeIcon = document.querySelector(".eye-icon1");
    const pwField = document.getElementById("pw");
    const eyeIcon2 = document.querySelector(".eye-icon2");
    const pwField2 = document.getElementById("confirmpw");
    const passwordMatchStatus = document.getElementById("passwordMatchStatus");

    eyeIcon.addEventListener("click", () => {
        togglePasswordVisibility(pwField, eyeIcon);
    });

    eyeIcon2.addEventListener("click", () => {
        togglePasswordVisibility(pwField2, eyeIcon2);
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

    // Check password match or not
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

    document.getElementById("myForm").addEventListener("submit", function(event) {
        // Call verifyPassword function to validate password
        if (!verifyPassword() || !checkPasswordsMatch()) {
            event.preventDefault(); // Prevent form submission if password validation fails
        }
    });

    //password validation

    var myInput = document.getElementById("pw");
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var number = document.getElementById("number");
    var length = document.getElementById("length");

    // When the user clicks on the password field, show the message box
    myInput.onfocus = function() {
        document.getElementById("message").style.display = "block";
    }

    // When the user clicks outside of the password field, hide the message box
    myInput.onblur = function() {
        document.getElementById("message").style.display = "none";
    }

    // When the user starts to type something inside the password field
    myInput.onkeyup = function() {
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if (myInput.value.match(lowerCaseLetters)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
        }

        // Validate capital letters
        var upperCaseLetters = /[A-Z]/g;
        if (myInput.value.match(upperCaseLetters)) {
            capital.classList.remove("invalid");
            capital.classList.add("valid");
        } else {
            capital.classList.remove("valid");
            capital.classList.add("invalid");
        }

        // Validate numbers
        var numbers = /[0-9]/g;
        if (myInput.value.match(numbers)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
        } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
        }

        // Validate length
        if (myInput.value.length >= 8) { // Adjusted condition for length
            length.classList.remove("invalid");
            length.classList.add("valid");
            length.style.color = ""; // Reset color to default
        } else {
            length.classList.remove("valid");
            length.classList.add("invalid");
            length.style.color = "red"; // Set color to red when length exceeds 15 characters
        }
    }

    function submitForm(event) {
        // Rest of your function remains unchanged
        const pwValue = pwField.value;
        const pw2Value = pwField2.value;

         // Validate both names
            if (!validateName(document.getElementById('firstname'), 'name-error-first') || 
            !validateName(document.getElementById('lastname'), 'name-error-last')) {
                event.preventDefault();
                swal({
                    icon: 'error',
                    title: 'Invalid Name',
                    text: 'Please ensure that names are valid.'
                });
                return;
            }

        // Check if passwords match
        if (pwValue !== pw2Value) {
            // If passwords don't match, prevent form submission and display an error message
            event.preventDefault();
            swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Passwords do not match. Please make sure your passwords match.'
            });
            return; // Exit the function without submitting the form
        }

        // Check if email is valid
        if (!validateEmail()) {
            // If email is not valid, prevent form submission and display an error message
            event.preventDefault();
            swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Please make sure your email is valid.'
            });
            return; // Exit the function without submitting the form
        }

        // Check if phone is valid
        if (!validatePhone()) {
            // If email is not valid, prevent form submission and display an error message
            event.preventDefault();
            swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Please make sure your phone number is valid.'
            });
            return; // Exit the function without submitting the form
        }

        // Check if phone is valid
        if (!validateDOB()) {
            // If email is not valid, prevent form submission and display an error message
            event.preventDefault();
            swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Please make sure your birthday is valid.'
            });
            return; // Exit the function without submitting the form
        }

        // If passwords match, you can optionally submit the form programmatically
        // event.target.submit();
    }

    //email validation
    var email = document.getElementById("useremail");
    var email_error = document.getElementById("email-error")

    function validateEmail() {
        if(!email.value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/))
  {
    email_error.innerHTML="Please make sure your email is valid"
    email_error.style.color="red";
    return false;
  }

        email_error.innerHTML = "";
        return true;
    }

    // Validate phone number
    var phone = document.getElementById("phno");
    var phone_error = document.getElementById("phone-error");

    function validatePhone() {
        // Check if the phone number field is empty
        if (phone.value.trim() === "") {
            phone_error.innerHTML = ""; // Clear the error message
            return true; // Return true as there is no error
        }

        // Check if the phone number matches the expected format
        if (!phone.value.match(/^\d{9,11}$/)) {
            phone_error.innerHTML = "Mobile Number should be 9 to 11 digits";
            phone_error.style.color = "red";
            return false;
        }

        // If the phone number format is correct, clear the error message
        phone_error.innerHTML = "";
        return true;
    }


    // Validate date of birth
    var dobInput = document.getElementById("birthday");
    var dobError = document.getElementById("dob-error");

    function validateDOB() {
        // Get the value of the date of birth input
        var dobValue = dobInput.value;

        // Check if the date format is valid (dd-mm-yyyy)
        var dobParts = dobValue.split('-');
        if (dobParts.length !== 3) {
            dobError.innerHTML = "Please enter a valid date (dd-mm-yyyy)";
            dobError.style.color = "red";
            return false;
        }

        var day = parseInt(dobParts[0], 10); // Extract day
        var month = parseInt(dobParts[1], 10); // Extract month
        var year = parseInt(dobParts[2], 10); // Extract year

        // Check if the date components are valid
        if (isNaN(day) || isNaN(month) || isNaN(year) ||
            day < 1 || day > 31 ||
            month < 1 || month > 12 ||
            year < 1900 || year > new Date().getFullYear()) {
            dobError.innerHTML = "Please enter a valid date (dd-mm-yyyy)";
            dobError.style.color = "red";
            return false;
        }

        // Convert the date of birth to a Date object
        var dobDate = new Date(year, month - 1, day); // Subtract 1 from month to convert to 0-based index

        // Calculate the minimum allowed date (17 years ago from today)
        var minAllowedDate = new Date();
        minAllowedDate.setFullYear(minAllowedDate.getFullYear() - 17);

        // Check if the date of birth is at least 17 years ago
        if (dobDate > minAllowedDate) {
            dobError.innerHTML = "You must be at least 17 years old.";
            dobError.style.color = "red";
            return false;
        }

        // If the date of birth is valid and meets the minimum age requirement, clear the error message
        dobError.innerHTML = "";
        return true;
    }

    function validateName(input, errorId) {
        var regex = /^[a-zA-Z]+$/;  // This regex correctly matches only alphabetic characters
        var errorSpan = document.getElementById(errorId);
    
        if (input.value.trim() === "") {
            errorSpan.textContent = ""; // Clears any previous error message
            return true; // Empty input is technically valid by HTML form standards (if you want it to be non-empty, this should be adjusted)
        }
    
        if (!input.value.match(regex)) {
            errorSpan.textContent = "Only alphabetic characters are allowed.";
            errorSpan.style.color = "red";
            return false; // Indicates validation failure
        } else {
            errorSpan.textContent = ""; // Clears any previous error message
            return true; // Indicates validation success
        }
    }
    
    
