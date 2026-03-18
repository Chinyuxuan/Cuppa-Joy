// Function to open the "select address" form and close the "choose address" form without submitting the form
function openPopup(event) {
    // Prevent the default behavior of the event
    event.preventDefault();

    // Close the "choose address" form
    closePopupChoose();
    
    // Open the "select address" form
    document.getElementById('select-address').style.display = 'block';
}

// Function to close the popup
function closePopup() {
    document.getElementById('select-address').style.display = 'none';
}

function openPopupChoose() {
    document.getElementById('choose-address').style.display = 'block';
}

// Function to close the popup
function closePopupChoose() {
    document.getElementById('choose-address').style.display = 'none';
}

function saveadd(event) {
    // Prevent the default form submission behavior
    event.preventDefault();

    console.log("save address...");

    // Assuming you have input fields with IDs 'editAdd1', 'editAdd2', and 'editPost'
    var add1 = document.getElementById('editAdd1').value;
    var add2 = document.getElementById('editAdd2').value;
    var post = document.getElementById('editPost').value;
    
    // Assuming you want to set the input field values after retrieving them
    document.getElementById('editAdd1').value = add1;
    document.getElementById('editAdd2').value = add2;
    document.getElementById('editPost').value = post;

    // Close the popup after saving
    closePopup();
    
    // Submit the form programmatically
    submitForm(event);
}

// Assuming you have a button or input field for opening the "select address" form
var saveMoreBtn = document.getElementById("saveMorebtn");
saveMoreBtn.addEventListener("click", openPopup);

// Assuming you have a button or input field for submitting the form
var confirmBtn = document.getElementById("submitbtn");
confirmBtn.addEventListener("click", saveadd);

function submitForm(event) {
    event.preventDefault();
    console.log("form submitted successfully...");

    // Assuming you want to submit the form programmatically after preventing the default submission behavior
    document.getElementById("addressform").submit();
}

