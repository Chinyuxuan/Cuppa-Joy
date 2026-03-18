function closePopup(){
    document.getElementById('editprofile').style.display = 'none';
    document.getElementById('select-address').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}

function editform(){
    console.log("Editing profile...");

    var fname = document.getElementById('editFName').textContent;
    var lname = document.getElementById('editLName').textContent;
    var contno = document.getElementById('editContact').textContent;
   
    document.getElementById('editFName').value = fname;
    document.getElementById('editLName').value = lname;
    document.getElementById('editContact').value = contno;
   

    document.getElementById('overlay').style.display = 'block';
    document.getElementById('editprofile').style.display = 'block';
    document.getElementById('editpassword').style.display = 'none';
}

    // Validate phone number
    var phone = document.getElementById("editContact");
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

    function validateFirstName() {
      var input = document.getElementById("editFName");
      var errorSpan = document.getElementById("name-error-first");
      var regex = /^[a-zA-Z\s]+$/;  // Allows alphabetic characters and spaces
  
      if (input.value === "") {
        errorSpan.textContent = ""; // Clears any previous error message
        return true; // Empty input is considered valid with no error message
    }

    if (input.value.trim() === "") {
        errorSpan.textContent = "Only alphabetic characters and space are allowed.";
        errorSpan.style.color = "red";
        return false; // Indicates validation failure for spaces only
    }

  
      if (!input.value.match(regex)) {
          errorSpan.textContent = "Only alphabetic characters and space are allowed.";
          errorSpan.style.color = "red";
          return false; // Indicates validation failure
      } else {
          errorSpan.textContent = ""; // Clears any previous error message
          return true; // Indicates validation success
      }
  }
  
  function validateLastName() {
      var input = document.getElementById("editLName");
      var errorSpan = document.getElementById("name-error-last");
      var regex = /^[a-zA-Z\s]+$/;  // Allows alphabetic characters and spaces
  
      if (input.value === "") {
        errorSpan.textContent = ""; // Clears any previous error message
        return true; // Empty input is considered valid with no error message
    }

    if (input.value.trim() === "") {
        errorSpan.textContent = "Only alphabetic characters and space are allowed.";
        errorSpan.style.color = "red";
        return false; // Indicates validation failure for spaces only
    }

  
      if (!input.value.match(regex)) {
          errorSpan.textContent = "Only alphabetic characters and space are allowed.";
          errorSpan.style.color = "red";
          return false;
      } else {
          errorSpan.textContent = "";
          return true;
      }
  }

  function validateAddress1() {
    const address1 = document.getElementById('editAdd1').value.trim();
    var address1span = document.getElementById("address1-error");

    if (address1 === "") {
      address1span.textContent = "Adress 1 cannot be empty.";
      address1span.style.color = "red";
        return false;
    }else
    {
      address1span.textContent = "";
      return true;
    }
   
}

function validateAddress2() {
    const address2 = document.getElementById('editAdd2').value.trim();
    var address2span = document.getElementById("address2-error");

    if (address2 === "") {
      address2span.textContent = "Address 2 cannot be empty.";
      address2span.style.color = "red";
        return false;
    }else
    {
      address2span.textContent = "";
      return true;
    }
}


  function validateForm(event) {
    // Call the validation functions and check their return values
    const isAddress1Valid = validateAddress1();
    const isAddress2Valid = validateAddress2();

    if (!isAddress1Valid) {
        event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid Address 1',
            text: 'Please ensure that address 1 is valid.'
        });
        return false; // Return false to indicate form should not submit
    }

    if (!isAddress2Valid) {
        event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid Address 2',
            text: 'Please ensure that address 2 is valid.'
        });
        return false; // Return false to indicate form should not submit
    }

    return true; // Return true to allow form submission
}

    
    

function submitForm(event) 
    {
      // Validate both names using the specific functions
    var validFirstName = validateFirstName();
    var validLastName = validateLastName();

    // If either name validation fails, show an error message and prevent form submission
    if (!validFirstName ) {
		event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid Name',
            text: 'Please ensure that firstname are valid.'
        });
        return false; // Return false to indicate form should not submit
    }

	    // If either name validation fails, show an error message and prevent form submission
    if (!validLastName) {
		event.preventDefault();
        swal({
            icon: 'error',
            title: 'Invalid Name',
            text: 'Please ensure that lastname are valid.'
        });
      }
        

      // Check phone validation
      if (!validatePhone()) {
          event.preventDefault(); // Prevent the form from submitting by default
          console.log("Invalid phone");
          swal({
            icon: 'error',
            title: 'Invalid Phone',
            text: 'Please make sure your phone number is valid.'
        });
          return false; // Exit the function without submitting the form
      }
  
      // If phone validation passes, allow the form submission
      console.log("Editing success...");
      return true;

      
  }
  

function editpwform(){
    document.getElementById('editpassword').style.display = 'flex';
    document.getElementById('editpassword').style.flexDirection = 'column';
    document.getElementById('editprofile').style.display = 'none';
}

const eyeIcon = document.querySelector(".eye-icon1");
const pwField = document.getElementById("newpass");
const eyeIcon2 = document.querySelector(".eye-icon2");
const pwField2 = document.getElementById("newcfpass");


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

//address pop form
function openAddPopup() {
  // Open the "select address" form
  document.getElementById('overlay').style.display = 'block';
  document.getElementById('select-address').style.display = 'block';
}

function saveadd(event) {
  // Prevent the default form submission behavior
  event.preventDefault();

  console.log("save address...");

  // Assuming you have input fields with IDs 'editAdd1', 'editAdd2', and 'editPost'
  var add1 = document.getElementById('editAdd1').value;
  var add2 = document.getElementById('editAdd2').value;
  var post = document.getElementById('editCity').value;
  
  // Assuming you want to set the input field values after retrieving them
  document.getElementById('editAdd1').value = add1;
  document.getElementById('editAdd2').value = add2;
  document.getElementById('editCity').value = post;

  // Close the popup after saving
  closePopup();
  
  // Submit the form programmatically
  submitFormAdd(event);
}

function submitFormAdd(event) {
  event.preventDefault();
  console.log("form submitted successfully...");

  // Assuming you want to submit the form programmatically after preventing the default submission behavior
  document.getElementById("addressform").submit();

   // Check if phone is valid
  
}



