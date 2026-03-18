function closePopup()
{
    document.getElementById('editprofile').style.display = 'none';
    document.getElementById('editpassword').style.display = 'none';
}

function editform(){
    console.log("Editing profile...");

    var fname = document.getElementById('firstname').textContent;
    var lname = document.getElementById('lastname').textContent;
    var contno = document.getElementById('contno').textContent;
   
    document.getElementById('editFName').value = fname;
    document.getElementById('editLName').value = lname;
    document.getElementById('editContact').value = contno;
   

    document.getElementById('editprofile').style.display = 'block';
    document.getElementById('editpassword').style.display = 'none';
}

function submitForm(event) {
  const pwValue = pwField.value;
  const pw2Value = pwField2.value;

  if (!validateOldpasaAndNewpass ()) {
    event.preventDefault(); // Prevent form submission if passwords are the same
    console.log("Passwords same");
    swal({
      icon: 'error',
      title: 'Error',
      text: 'New password cannot same as current password.'
  });
    return;
}

  // Check if passwords match
  if (pwValue !== pw2Value) {
      // If passwords don't match, prevent form submission and display an error message
      event.preventDefault();
      console.log("Passwords do not match");
      swal({
        icon: 'error',
        title: 'Error',
        text: 'Passwords do not match. Please make sure your passwords match.'
    });
      return;
  }

  // Validate new password
  if (!validateNewPassword(pwValue)) {
      // If new password validation fails, prevent form submission and display an error message
      event.preventDefault();
      console.log("New password does not meet requirements");
      swal({
        icon: 'error',
        title: 'Error',
        text: 'Ensure new password must contain:\n- at least one lowercase letter\n- one uppercase letter\n- one number\n- at least 8 characters long.'
    });
      return;
  }



  console.log("Editing success...");
  document.getElementById("passwordfrm").submit(); // Submit the form if passwords match and new password meets requirements
}

function validateNewPassword(password) {
  // Define regular expressions for password validation
  const lowercaseRegex = /[a-z]/;
  const uppercaseRegex = /[A-Z]/;
  const numberRegex = /[0-9]/;

  // Check if password meets the criteria (at least one lowercase letter, one uppercase letter, one number, and at least 8 characters long)
  return lowercaseRegex.test(password) && uppercaseRegex.test(password) && numberRegex.test(password) && password.length >= 8;
}



function editpwform(){
    document.getElementById('editpassword').style.display = 'flex';
    document.getElementById('editpassword').style.flexDirection = 'column';
    document.getElementById('editprofile').style.display = 'none';
}

const eyeIcon0 = document.querySelector(".eye-icon0");
const pwField0 = document.getElementById("oldpass");
const eyeIcon = document.querySelector(".eye-icon1");
const pwField = document.getElementById("newpass");
const eyeIcon2 = document.querySelector(".eye-icon2");
const pwField2 = document.getElementById("newcfpass");

eyeIcon0.addEventListener("click", () => {
  togglePasswordVisibility(pwField0, eyeIcon0);
});

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


const passwordMatchStatus = document.getElementById("passwordMatchStatus");

pwField.addEventListener("input", checkPasswordsMatch);
pwField2.addEventListener("input", checkPasswordsMatch);


function checkPasswordsMatch() {
    const pwValue = pwField.value;
    const pw2Value = pwField2.value;

    if (pwValue == pw2Value) {
        passwordMatchStatus.textContent = "Passwords match";
        passwordMatchStatus.style.color = "green";
    } else {
        passwordMatchStatus.textContent = "Passwords do not match";
        passwordMatchStatus.style.color = "red";
    }
}

//check if same oldpass and newpass same, then cannot insert
function validateOldpasaAndNewpass () {
  var oldpass = document.getElementById('oldpass').value; // Corrected access to the element
  var newpass = document.getElementById('newpass').value; // Corrected access to the element

  if (oldpass === newpass) {
   
      return false;
  }

  return true;
}



//password validation

var myInput = document.getElementById("newpass");
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
  if(myInput.value.match(lowerCaseLetters)) {
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
  }

  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput.value.match(upperCaseLetters)) {
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput.value.match(numbers)) {
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }

  // Validate length
  if(myInput.value.length >= 8) { // Adjusted condition for length
    length.classList.remove("invalid");
    length.classList.add("valid");
    length.style.color = ""; // Reset color to default
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
    length.style.color = "red"; // Set color to red when length exceeds 15 characters
  }
}