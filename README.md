## 1) Project Description

Cuppa Joy is a multi-user system (customer, admin, riders) that is built to enhance user satisfaction.

This project describes a system that incorporates information cues to guide users through functions and processes such as sign-up procedures, ensuring efficient usage. Furthermore, emphasis will be placed on enhancing user-friendliness by implementing clear page layouts and confirmation messages, thereby minimizing confusion, and preventing unintended actions, such as accidental deletion or modification of information. By prioritizing ease of use and clarity, the system aims to significantly enhance user satisfaction and overall experience. 

## 2) Involving parts

**Total members: 3**

I am responsible for building the **customer module**, which includes the scopes of:

### o	Sign-Up page:

	Register an account if the user doesn’t have an account

	Verify the account after the customer Sign-Up

### o	Sign-In page:

	Sign in if the customer has an account

	Re-verify the account if the customer forgot to verify the account while signing up

	Reset password if a customer forgets password 

### o	Main page

	Show new items and 6 most popular items

	Show baristas

### o	Menu page:

	Show the menu list

	Select the category to display menu based on drinks or foods category

	Search items

### o	Add to Cart page:

	Add item to wish list

	Customize the items based on customer’s preference

	View the other customer's comments on the item

	Add item to cart 

	Show other foods or drinks

### o	Show Promo page:

	Show the available coupons 

### o	About Us page:

	Show vision, mission, objectives, and the barista team

### o	Wishlist page:

	Add the item from the wish list into cart

	Customize the item based on customers’ preference

	Remove the item from wish list

### o	Profile page

	Edit profile

	Change password

	Update address

### o	Contact Us page:

	Submit the message to Cuppa Joy

### o	Header of page:

	Customers can Sign out 

## 3) Tech/ Language

•	**Frontend**: HTML5, CSS3, SCSS, JavaScript.

•	**Backend**: PHP.

•	**Database**: MySQL (Managed via XAMPP/Apache and phpMyAdmin).

## 4) Visuals of pages

**Video**:

**Detailed screenshots**:

### Sign Up page

 <img width="866" height="433" alt="image" src="https://github.com/user-attachments/assets/6fb247b3-ba9f-408f-82d2-be0137b0052c" />

<p align="center"> Figure 4.1 Cuppa Joy – Sign Up </p>

This is the sign-up page of Cuppa Joy. It has fields of first name, last name, email, phone number, password, and confirm password. Then, if the customer has an account, they can click “Sing in” on the left side to go to the Sign-in page. If customers click the logo of Cuppa Joy, they can go to the Home page.

A pop-up alert will be shown to mention that the email used to sign up has been used by another customer to create the account. Thus, the customer cannot create an account with the same email even if the email owner is the same person.

If the customer enters the wrong information, the red color error message and pop-up alerts will be shown under eacch entry fields.

<img width="556" height="383" alt="image" src="https://github.com/user-attachments/assets/07d272cc-a8b4-43bc-a3ab-ca8e21774033" />
 
<p align="center"> Figure 4.2 Cuppa Joy – Sign Up with the correct information</p>

This is the figure to show the correct information when a user creates an account.

 <img width="583" height="416" alt="image" src="https://github.com/user-attachments/assets/29a031db-f349-4edd-9d96-b3dfc3175f22" />

<p align="center"> Figure 4.3 Cuppa Joy – Sign Up Successfully</p>

This is the figure to show the pop-up message to show that the customer has created an account successfully and mention that the OTP code for verifying has been sent to the customer's mailbox.

<img width="864" height="301" alt="image" src="https://github.com/user-attachments/assets/5b7e07fd-d655-45e7-8f49-f64023e52644" />

<p align="center"> Figure 4.4 Cuppa Joy – Mailbox to receive the OTP code </p>

This is the figure to show the email that Cuppa Joy sent to the customer to give the OTP for verifying the customer account.

### Verification page

<img width="469" height="315" alt="image" src="https://github.com/user-attachments/assets/72721438-12db-457d-8c8e-c229da110d80" />
 
<p align="center">Figure 4.5 Cuppa Joy – Verify account page</p>

This is the figure to show the verify account page. This page has “Back to Sign In” that enables customers to go back to the Sign In page.

A pop-up alert will be shown to mention that the customer has entered the wrong OTP code.

A pop-up message will be shown to mention that the customer has entered the correct OTP code and the account has been verified successfully.

### Sign In page

<img width="415" height="213" alt="image" src="https://github.com/user-attachments/assets/ae820e06-a24d-400a-95f6-2e1dd77703f1" />
 
<p align="center">Figure 4.6 Cuppa Joy – Sign In page</p>

This is the figure on the Sign In page of Cuppa Joy. If the customer doesn’t have an account, they can go to the left side to click “Sign Up” to navigate to the Sign-Up page. Then, customers also can click the logo to navigate the Home page without signing in account.

A pop-up alert will be shown to mention that the customer has entered the wrong email or password when signing in.

### Reverify account – If customer has forgotten to verify the account previously

 <img width="448" height="301" alt="image" src="https://github.com/user-attachments/assets/e392a3b8-c249-45cc-8542-2f1a740adbbc" />

<p align="center">Figure 4.7 Cuppa Joy – Reverify the account</p>

This is the figure to show the pop-up alert to mention the customer has forgotten to verify their account previously. So, the system will ask the customer to verify again before signing in.

So the pop-up message will be shown to show the email has been sent successfully to customers to enable them to receive the OTP code again. After they click “OK”, the system will lead the customer to verify the account page again. The verification account process is the same as the previous verification process after the customer creates the account.

 <img width="865" height="326" alt="image" src="https://github.com/user-attachments/assets/44a66d9b-503f-4723-9b4d-d6eaf27c007d" />

<p align="center"> Figure 4.8 Cuppa Joy – Forgot Password</p>

This is the figure to show a link to enable customers reset their password if they have forgotten the password.

### Recover Password page 

 <img width="381" height="251" alt="image" src="https://github.com/user-attachments/assets/1de5d96d-a16e-4366-9600-89520edd8b52" />

<p align="center">Figure 4.9 Cuppa Joy - Recover Password</p>

This is the figure to show the page that enables customers to enter their email to reset their password. The email will be used to send emails for reset password interfaces. If a customer doesn’t want to reset the password, they can click to go back Sign-In page.

The error message and pop-up alert appear when a customer enters the wrong email format or enter the non-existent email in the database.

 <img width="864" height="363" alt="image" src="https://github.com/user-attachments/assets/f061fff0-3b90-483c-af5d-3c1b3ebcac1c" />

<p align="center"> Figure 4.10 Cuppa Joy – Email to send page for resetting the password</p>

This is the figure to show the mailbox after the customer enters the correct email to reset the password. Customer can click the “Reset Password” link to reset their password.

### Reset Password page

 <img width="534" height="519" alt="image" src="https://github.com/user-attachments/assets/b8ebef71-75ce-46a7-9c41-4a73c45b30ce" />

<p align="center">Figure 4.11 Cuppa Joy - Reset Password</p>

This is the figure to show the page that enables customers to enter a new password and confirm it again by resetting the password. If customers don’t want to reset their password, they can go back to the Sign-In page by clicking “Back to Sign In”.

If customer has entered the new password that same as the old password, the pop-up alert appear when the customer tries to update the new password that same as the old password. The password cannot be updated even if the new password was confirmed correctly.

If the customer has entered the wrong password format, the system has shown the message to ask the customer to create the password based on the requirements such as a lowercase and an uppercase, minimum 8 characters, and contain a number.

The pop-up alert appear when the customer doesn’t match the new password and confirms the password.

 <img width="438" height="280" alt="image" src="https://github.com/user-attachments/assets/3bf5c3e5-5cb6-41b5-a07a-2b57c47ac7d6" />

<p align="center">Figure 4.12 Cuppa Joy - Reset Password with correct password information</p>

This is the figure to show the customer has entered the correct password format and the password is matched with the confirm password. Then, the new password is not the same as the old password. Thus, the customer can reset the password.

### Home page – Not Sign In/Sign In

<img width="623" height="284" alt="image" src="https://github.com/user-attachments/assets/397afdc0-808d-4359-aea5-faa41588f788" />
<img width="528" height="185" alt="image" src="https://github.com/user-attachments/assets/3f19eb0f-a21f-4c63-8f14-6a88113ff929" />
<img width="440" height="315" alt="image" src="https://github.com/user-attachments/assets/6d782843-fcab-4081-b218-258ebfe7bfb3" />
<img width="620" height="179" alt="image" src="https://github.com/user-attachments/assets/a3f51b5f-ac52-4703-8303-069b599fb228" />
<img width="830" height="54" alt="image" src="https://github.com/user-attachments/assets/d4c9e1e8-a69d-4466-87f8-195fb4dc6488" />

<p align="center">Figure 4.13 Cuppa Joy – Home Page (Not sign in/Sign In)</p>

This is the figure to show the Cuppa Joy Home page, whether customers have to sign in or not sign in, the home page can be viewed by customers. The difference between customers who have signed in and not signed in is the header. The home page will show the new drinks or foods, popular drinks or foods, and the baristas.

The header will show the pages, cart, wish list, profile, and “Sign Out” button. Then, also shows the logo that enables customers to go back to the home page when they in other pages.

 <img width="865" height="61" alt="image" src="https://github.com/user-attachments/assets/b2f3ed00-9223-4e5b-8ed8-c984f2356e84" />

<p align="center">Figure 4.14 Cuppa Joy – Sign Out confirmation message</p>

This is the pop-up confirmation message to ask the customer to confirm to log out after the customer clicks the “Sign Out” button. If the customer clicks “cancel”, it has nothing happens, else if the customer clicks “Yes, log me out”, the system will log the customer out from the system.

<img width="443" height="328" alt="image" src="https://github.com/user-attachments/assets/47b36aba-0879-490e-acae-33bd15c06f66" />

<p align="center">Figure 4.15 Cuppa Joy –Header (Not sign in)</p>

This is the figure to show the page header if the customer has not signed in yet. The header will show the pages, cart, wish list, and “Sign in” or “Sign up” buttons. Then, also shows the logo that enables customers to go back to the home page when they in other pages.
 
These are the pop-up confirmation messages to ask the customer to confirm to sign in after the customer clicks the cart page icon, wish list page icon, and order history page. If the customer clicks “cancel”,it has nothing happens, else if the customer clicks “Sign In”, the system will lead the customer to the Sign in page.

### User Profile

 <img width="526" height="459" alt="image" src="https://github.com/user-attachments/assets/f588c662-f924-4474-abaa-f0350954f72c" />

<p align="center">Figure 4.16 Cuppa Joy – Profile page</p>

This is the figure to show the customer profile page. It shows some customer information and enables customers to edit profiles, change passwords, and add addresses.

 <img width="434" height="366" alt="image" src="https://github.com/user-attachments/assets/b34f71dd-1657-443a-9e04-df0812079252" />

<p align="center">Figure 4.17 Cuppa Joy – Edit profile</p>

This is the figure to show the pop-up form to enable the customer to edit the profile. Customers can edit their first name, last name, and phone number only.

The error message and pop-up alerts appear when a customer enters the wrong information.

 <img width="865" height="211" alt="image" src="https://github.com/user-attachments/assets/d84d13f8-10c1-4ee4-9469-5c2777f3a0fc" />
<img width="540" height="438" alt="image" src="https://github.com/user-attachments/assets/cd657c9e-9764-49b4-af6a-17924aa25af4" />

<p align="center">Figure 4.18 Cuppa Joy – Edit profile with successfully</p>

This is the figure to show the pop-up message when a customer edit a profile successfully. The table shows the customer information has been updated.

  <img width="539" height="213" alt="image" src="https://github.com/user-attachments/assets/26cf79fc-0e0c-47a4-8560-d30e9360d33c" />
  <img width="594" height="374" alt="image" src="https://github.com/user-attachments/assets/9e3d6425-df4b-4d19-abca-e059366e15b7" />

<p align="center">Figure 4.19 Cuppa Joy – Add address</p>

This is the figure to show the pop-up form when the customer wants to add an address. The customer can enter address 1, and address 2, and select the city in the area of 75450 postcode only.

The error message and pop-up alert when a customer adds an address with the wrong information. Address 1 and address 2 cannot be empty.

 <img width="509" height="334" alt="image" src="https://github.com/user-attachments/assets/4ca5af56-d104-4c88-85e7-80f290980cde" />

<p align="center">Figure 4.20 Cuppa Joy – Add address with correct information</p>

This is the figure to show the customer add address with the correct information. 
 
The pop-up confirmation message appear when the customer deletes the address, and the address table is updated too. When the customer clicks “cancel”, it has nothing happens, else the address will be deleted.

### Change Password page

 <img width="409" height="526" alt="image" src="https://github.com/user-attachments/assets/5c43d35d-ca8a-4739-a0b4-aa38f91e984c" />

<p align="center">Figure 4.21 Cuppa Joy – Change Password page</p>

This is the figure to show the form to change the password. If customers don’t want to change anymore, they can click “Back to profile” to go back.

If the customer changes the same password, the system will show a pop-up alert to mention customer the old and the current password cannot be the same. Even though the password was confirmed correctly. Besides, the user cannot change their password if the old password is wrong.

The system will mention the requirements to form the new password and also show the alert.

### Menu page – Not Sign In/Sign In

 <img width="858" height="100" alt="image" src="https://github.com/user-attachments/assets/954dd750-49f6-43e0-acb4-6e85c8ec2fe3" />

<p align="center">Figure 4.22 Cuppa Joy – Menu page (Not Sign In / Sign In)</p>

This is the figure to show the menu page of the system. The menu is the same for customers when they are not signed in yet or signed in.

The page will show the variance in drinks or food categories for customers. Customers can navigate the categories by clicking the left and right arrows.

The page of the "All", menu will show 12 items. Then the menu will show all items even if the item is not available currently. The not-available items will have a grey color filter and customers cannot click it to view product details. Customers can click “Prev” to go back to the previous page, and “Next” for the next page.

The page of selected category, menu will show 12 items. Then the menu will show all items of that category even if the item is not available currently. The not available items also have the grey color filter and customers cannot click it to view product details. Customers can click “Prev” to go back to the previous page, and “Next” for the next page. Then at the top of the item list, the system shows what category exactly the customer has chosen.

<img width="724" height="1283" alt="image" src="https://github.com/user-attachments/assets/537d14d4-e458-499f-bd89-79b48b617214" />

<p align="center">Figure 4.23 Cuppa Joy – Menu page with searching function</p>

This is the figure to show the search results after customers have searched keywords for some items. For example, if a customer enters “sm”, the system will show the item that contains “sm”. The searched items list will change back to the original items list when customers clear the input they enter in the search field.

### Product Details page  – Not Sign In/Sign In
 
 <img width="866" height="980" alt="image" src="https://github.com/user-attachments/assets/501219d5-a257-492d-9543-f9860ee850c4" />
<img width="866" height="980" alt="image" src="https://github.com/user-attachments/assets/98274d0f-4f48-4226-a07b-ef5ca395c364" />

<p align="center">Figure 4.24 Cuppa Joy – Product details page with customization or no customization available</p>

This is the figure to show the item after the customer clicks the item from the menu. On this page, it will show some item information, an “Add to wishlist” button, customization for customizable items, quantity an “Add to cart” button, and the other foods or drinks. If this item has been commented on by other customers, the comments also will be shown above the “Add to cart” button. This page can be viewed if the customer is not signed in or signed in.

The pop-up confirmation message appear to customers after they intend to add the item to their wish list or cart. This only occurs when the customer is not signed in yet.

<img width="386" height="196" alt="image" src="https://github.com/user-attachments/assets/75b80814-b662-42ab-8b6b-ab621eaec88b" />

<p align="center">Figure 4.25 Cuppa Joy – Product details page (Add to wish list)</p>

This is the figure to show the “Add to wish list” button has been used, which means that this item has been added and kept in the wish list already. If the customer clicks the button again, the customer can go to the wish list page.

The pop-up alert appear when the customer didn’t add quantity (quantity is 0) and the quantity is greater than 12 after they click “Add to cart”. The system has limited the customers to one cart cannot contain 12 items due to delivery reasons.

The pop-up alert appear when the customer adds the item to the cart and checks the cart whether has over 12 items already. So, this alert will be shown when the cart has over 12 items after the new quantity that the customer wants to add is added to the existing item quantity in the cart.  For example, a cart currently has 6 items already, so if the customer wants to add more than 7 items, it is not allowed.

<img width="866" height="446" alt="image" src="https://github.com/user-attachments/assets/bdcc871d-6508-42a2-8a60-7f7337f26639" />
<img width="425" height="359" alt="image" src="https://github.com/user-attachments/assets/0e3d2b59-975a-4bb4-baa1-9b804c1ef0ee" />

<p align="center">Figure 4.26 Cuppa Joy – Product details page (Add to cart)</p>

This is the figure to show the pop-up alert when the customer adds the item to the cart without selecting the compulsory customization category. The compulsory customization will show a sentence “Pick one option” in the customization category. For the non-compulsory customization, the customer can choose multiple customizations or directly ignore them, such as topping. For example, the compulsory customizations are ice level, sugar level, and size.

The pop-up confirmation message appear to ask the customer to confirm whether to add the same item to the cart. If customers click “cancel”, means that they don’t want to add the same item, else the item will be added to the cart and increment the cart item quantity.

### Wish list page

 <img width="866" height="855" alt="image" src="https://github.com/user-attachments/assets/7a5a16ef-7648-4f86-85f6-4d7a9c92f682" />

<p align="center">Figure 4.27 Cuppa Joy – Wish list page </p>

This is the figure to show the wish list page that contains the items that customers have added from the product detail page. Customers can add the item to the cart from the wish list. If the item is not available currently, the item will filter with a grey color and can be removed the item.

The wish list item that cannot be customized -> the customer can only add the quantity and add to the cart.

The wish list item that can be customized -> the customer can choose the customization based on the compulsory customization category or not compulsory to add to the cart.

The pop-up alert appear when the customer didn’t add quantity (quantity is 0) and the quantity is greater than 12 after they click “Add to cart”. The system has limited the customers to one cart cannot contain 12 items due to delivery reasons.

The pop-up alert appear when the customer adds the item to the cart and checks whether the cart has over 12 items already. So, this alert will be shown when the cart has over 12 items after the new quantity that the customer wants to add is added to the existing item quantity in the cart.  If the current cart has 4 items already, the customer cannot add 9 items into the cart.

The pop-up confirmation message appear to ask the customer to confirm whether to add the same item to the cart. If the customer clicks “cancel”, means that they don’t want to add the same item, else the item will be added to the cart and increment the cart item quantity.

### Show promo page 

<img width="866" height="698" alt="image" src="https://github.com/user-attachments/assets/d0731127-2de9-4a76-af63-0dd9dc8879b2" />

<p align="center"> Figure 4.28 Cuppa Joy – Show promo (Not Sign In)</p>

This is the figure to show the coupons for all customers. When the customer is not signed in yet, the promo will show based on before the end date.

If customer sign in, the coupons will be shown after user signing in for checking what coupons are not used yet by this customer. If the coupons have been used, they won’t show on the coupons list after the customer signs in.

### About us page 
 
<img width="728" height="1265" alt="image" src="https://github.com/user-attachments/assets/a2d12a15-26ac-4841-b8ea-5037ac35173f" />

<p align="center">  Figure 4.29 Cuppa Joy – About us </p>

This is the figure to show the About Us page that describes the vision, mission, and objective of Cuppa Joy. Also, introduce the Cuppa Joy’s baristas.
 
### Contact us page 
  
 <img width="741" height="845" alt="image" src="https://github.com/user-attachments/assets/4f88c61d-0d4a-4347-af22-b0c5649341e8" />
<img width="738" height="284" alt="image" src="https://github.com/user-attachments/assets/611374db-4c65-45a8-8ba4-1c51bef145e5" />
<img width="740" height="109" alt="image" src="https://github.com/user-attachments/assets/00d175f0-d367-4b11-8838-43694c050793" />

<p align="center">  Figure 4.30 Cuppa Joy – Contact us </p>

This is the figure to show the Contact Us page that contains the form, shop hours, contact information, and the map of the Cuppa Joy.

If the customer has not signed in yet, so the customer needs to enter all the information needed. The error message and pop-up alert appear when a customer enter the wrong information format. The first name and last name cannot contain the number. The email must be correct, contain “@” and after “.” must have at least 2 characters. The phone number must contain 9 – 11 digits and cannot contain the alphabet. They must fill up all fields.

If the customer signs in, so the customer does not need to enter all the information. Customers need to enter the subject and message only. This is because the personal information has been retrieved from the database if the customer has signed in.



