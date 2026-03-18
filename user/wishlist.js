$(document).ready(function() 
{
    $('.view-details-btn').click(function(event) {
        event.preventDefault(); // Prevent default link behavior
        var productId = $(this).data('product-id'); // Get the product ID
        openPopup(productId); // Open the popup form with the product ID
    });

    // Event handler for change event of checkboxes
    $('body').on('change', 'input[type="checkbox"]', function() {
        var checkedCheckboxes = $('input[type="checkbox"]:checked').map(function() {
            return $(this).val();
        }).get();
        console.log('Checked checkboxes:', checkedCheckboxes);

        // Add a temporary indicator to the label to visually confirm if it's checked
        $(this).next('label').toggleClass('checked', $(this).is(':checked'));
    });

    // Event handler for change event of radio buttons
    $('body').on('change', 'input[type="radio"]', function() {
        var checkedRadio = $('input[type="radio"]:checked').val();
        console.log('Checked radio button:', checkedRadio);

        // Add a temporary indicator to the label to visually confirm if it's checked
        $('input[type="radio"]').next('label').removeClass('checked');
        $('input[type="radio"]:checked').next('label').addClass('checked');
    });
});

//-----------------------------------------------------------------------------------------------------
//open pop up form and display the overlay
function openPopup(productId) 
{
    document.getElementById('overlay').style.display = 'block';
    $('#addCart').show(); // Show the popup form
    $('#addCartForm input[name="product_id"]').val(productId); // Set the product ID in the form
    $('#customization-container').html(''); // Clear the customization container


    $.ajax({
        url: 'fetch_cust_popup.php',
        type: 'GET',
        data: { product_id: productId },
        dataType: 'json',
        success: function(response) {
            console.log('AJAX Success:', response); // Log the response data
            
            // Generate HTML for customization options
            var radioHTML = '';
            var checkboxHTML = '';
    
            // Process radio options with "yes" compulsory status
            $.each(response, function(ccID, customizationGroup) {
                if (customizationGroup['Compulsory_Status'] == 'yes') {
                    radioHTML += '<h4>' + customizationGroup['CC_Group'] + '</h4>';
                    radioHTML += '<span class="text-danger compulsoryError" id="compulsoryError' + ccID + '">* Pick one option.</span>';
    
                    $.each(customizationGroup['Customizations'], function(index, customization) {
                        var inputId = 'radio' + ccID + index;
    
                        radioHTML += '<div class="inputGroup">';
                        radioHTML += '<input type="radio" id="' + inputId + '" name="customizations[' + ccID + ']" value="' + customization['Custom_ID'] + '" required>';

                        radioHTML += '<label for="' + inputId + '">' + customization['Custom_Name'] + ' + RM ' + parseFloat(customization['Custom_Price']).toFixed(2) + '</span></label>';
                        radioHTML += '</div>';
                    });
                }
            });
    
            // Process checkbox options with "no" compulsory status
            $.each(response, function(ccID, customizationGroup) {
                if (customizationGroup['Compulsory_Status'] == 'no') {
                    checkboxHTML += '<h4>' + customizationGroup['CC_Group'] + '</h4>';
                    checkboxHTML += '<span class="text-danger compulsoryError" '+ ccID + '">* Optional.</span>';
                    $.each(customizationGroup['Customizations'], function(index, customization) {
                        var inputId = 'checkbox' + ccID + index;
    
                        checkboxHTML += '<div class="inputGroup">';
                        checkboxHTML += '<input type="checkbox" id="' + inputId + '" name="customizations[' + ccID + '][]" value="' + customization['Custom_ID'] + '" data-group-name="' + customizationGroup['CC_Group'] + '">';
                        checkboxHTML += '<label for="' + inputId + '">' + customization['Custom_Name'] + ' + RM ' + parseFloat(customization['Custom_Price']).toFixed(2) + '</label>';
                        checkboxHTML += '</div>';
                    });
                }
            });
    
            $('#customization-container').html(radioHTML + checkboxHTML);
            $('#addCartForm').fadeIn(); // Show the popup form
        },
    
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText); // Log any errors
        }
    });//display the radio and checkbox
    
    // Reset the quantity input field to 0
$('.count').val(0);
}

//----------------------------------------------------------------------------------
//close the form and overlay
function closePopup() {
    document.getElementById('overlay').style.display = 'none';

    $('#addCart').hide(); // Hide the popup form
    // Reset the quantity input field to 0
    $('.count').val(0);
}

//----------------------------------------------------------------------------------

document.addEventListener("DOMContentLoaded", function() {
    // Add event listeners to the "View Detail" buttons or links
    var viewDetailButtons = document.querySelectorAll('.view-detail-button');
    viewDetailButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Reset the quantity input field to its default value
            document.querySelector('.count').value = 0;
        });
    });
});

//--------------------------------------------------------------------------
//delete the wishlist
    $(document).ready(function() 
    {
        // Handle form submission for deleting wishlist items
        $('.delete-form').submit(function(event) {
            // Prevent default form submission
            event.preventDefault();
            
            var form = $(this);
            var wishlistItemId = form.find('input[name="wishlist_item_id"]').val(); // Get the wishlist item ID
            
            // Send AJAX request to delete the wishlist item
            $.ajax({
                type: 'POST',
                url: 'delete_wishlist.php',
                data: { wishlist_item_id: wishlistItemId },
                success: function(response) {
                    // Reload the page or update the wishlist section as needed
                    location.reload(); // Reload the page
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log any errors
                }
            });
        });

    });

//----------------------------------------------------------------------------
//+ or - the quantity
		$(document).ready(function(){
		$(document).on('click','.plus',function(){
			$('.count').val(parseInt($('.count').val()) + 1);
		});
		$(document).on('click','.minus',function(){
			$('.count').val(Math.max(parseInt($('.count').val()) - 1,0));
		});
	});







