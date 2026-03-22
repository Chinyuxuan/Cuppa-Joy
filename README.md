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

 <img width="463" height="296" alt="image" src="https://github.com/user-attachments/assets/df2c290e-46ae-495e-8ac5-d551340278d9" />

<p align="center"> Figure 4.2 Cuppa Joy – Sign Up with existing email </p>

This is the figure to show a pop-up alert to mention that the email used to sign up has been used by another customer to create the account. Thus, the customer cannot create an account with the same email even if the email owner is the same person.

<img width="538" height="320" alt="image" src="https://github.com/user-attachments/assets/af3050bc-2f3c-4627-b60c-5fa18f91dfae" />

<p align="center">  Figure 4.3 Cuppa Joy – Sign Up with the wrong information</p>

This is the figure to show the wrong information. If the customer enters the wrong information, the red color error message will be shown.

 <img width="514" height="251" alt="image" src="https://github.com/user-attachments/assets/8c415971-56fb-478f-b167-2df622794877" />

<p align="center"> Figure 4.4 Cuppa Joy – Sign Up with the wrong first name</p>

This is the figure to show the pop-up alert if a customer enters the wrong first name format. The first name cannot contain the number.

<img width="530" height="269" alt="image" src="https://github.com/user-attachments/assets/09a69615-0c8f-4550-a0fd-91d3b17f54d2" />

<p align="center"> Figure 4.5 Cuppa Joy – Sign Up with the wrong last name</p>

This is the figure to show the pop-up alert if a customer enters the wrong last name.  The last name cannot contain the number.

 <img width="424" height="264" alt="image" src="https://github.com/user-attachments/assets/c8e96a5e-c2fc-4bdc-94b4-3da8868808f7" />

<p align="center"> Figure 4.6 Cuppa Joy – Sign Up with the wrong email</p>

This is the figure to show the pop-up alert if a customer enters the wrong email format. The email format must have “@” and after “.” Must have more than one character.

 <img width="566" height="214" alt="image" src="https://github.com/user-attachments/assets/9489fcbd-4a46-42e9-b16f-f4ffd2727b41" />

<p align="center"> Figure 4.7 Cuppa Joy – Sign Up with the wrong phone number</p>

This is the figure to show the pop-up alert if a customer enters the wrong phone number format. The phone number must be in the range of 9 – 11 digits and cannot contain the alphabet.

 <img width="505" height="364" alt="image" src="https://github.com/user-attachments/assets/b5dd3700-bbef-4476-b6ed-51972d625a95" />

<p align="center"> Figure 4.8 Cuppa Joy – Sign Up with the wrong password format</p>

This is the figure to show the alert if a customer enters the wrong password. The password must fulfill those requirements such as lowercase and uppercase, a number, and at least 8 characters.

<img width="330" height="453" alt="image" src="https://github.com/user-attachments/assets/377fa2a8-bf5a-4549-a84f-74ab67df2f0a" />

<p align="center">Figure 4.9 Cuppa Joy – Sign Up with not matching password</p>

This is the figure to show the pop-up alert if the customer enters a password that is not confirmed well.

<img width="556" height="383" alt="image" src="https://github.com/user-attachments/assets/07d272cc-a8b4-43bc-a3ab-ca8e21774033" />
 
<p align="center"> Figure 4.10 Cuppa Joy – Sign Up with the correct information</p>

This is the figure to show the correct information when a user creates an account.

 <img width="583" height="416" alt="image" src="https://github.com/user-attachments/assets/29a031db-f349-4edd-9d96-b3dfc3175f22" />

<p align="center"> Figure 4.11 Cuppa Joy – Sign Up Successfully</p>

This is the figure to show the pop-up message to show that the customer has created an account successfully and mention that the OTP code for verifying has been sent to the customer's mailbox.

<img width="864" height="301" alt="image" src="https://github.com/user-attachments/assets/5b7e07fd-d655-45e7-8f49-f64023e52644" />

<p align="center"> Figure 4.12 Cuppa Joy – Mailbox to receive the OTP code </p>

This is the figure to show the email that Cuppa Joy sent to the customer to give the OTP for verifying the customer account.

Verification page

<img width="469" height="315" alt="image" src="https://github.com/user-attachments/assets/72721438-12db-457d-8c8e-c229da110d80" />
 
<p align="center">Figure 4.13 Cuppa Joy – Verify account page</p>

This is the figure to show the verify account page. This page has “Back to Sign In” that enables customers to go back to the Sign In page.

<img width="479" height="319" alt="image" src="https://github.com/user-attachments/assets/7dd83287-9b31-4590-af48-3c16738f45ee" />
 
<p align="center"> Figure 4.14 Cuppa Joy – Verify with wrong OTP</p>

This is the figure to show a pop-up alert to mention that the customer has entered the wrong OTP code.

<img width="866" height="433" alt="image" src="https://github.com/user-attachments/assets/fb9762c6-189a-4fcd-b9cc-baa9f63a7044" />
 
<p align="center">Figure 4.15 Cuppa Joy – Verify with correct OTP</p>

This is the figure to show a pop-up message to mention that the customer has entered the correct OTP code and the account has been verified successfully.

Sign In page

<img width="415" height="213" alt="image" src="https://github.com/user-attachments/assets/ae820e06-a24d-400a-95f6-2e1dd77703f1" />
 
<p align="center">Figure 4.16 Cuppa Joy – Sign In page</p>

This is the figure on the Sign In page of Cuppa Joy. If the customer doesn’t have an account, they can go to the left side to click “Sign Up” to navigate to the Sign-Up page. Then, customers also can click the logo to navigate the Home page without signing in account.
 
 <img width="536" height="369" alt="image" src="https://github.com/user-attachments/assets/9b701d5a-276f-4fe7-9ce3-a0f29b25a22c" />

<p align="center">Figure 4.17 Cuppa Joy – Sign In page with wrong information</p>

This is the figure to shows the wrong information of email and password when the customers sign in.

 <img width="469" height="320" alt="image" src="https://github.com/user-attachments/assets/116d19ff-6dda-4139-b817-385ed91858c9" />

<p align="center"> Figure 4.18 Cuppa Joy – Sign In with the wrong email</p>

This is the figure to show a pop-up alert to mention that the customer has entered the wrong email when signing in.

 <img width="509" height="361" alt="image" src="https://github.com/user-attachments/assets/2e1c6339-1c7a-4885-b563-2ae53f76d5ff" />

<p align="center"> Figure 4.19 Cuppa Joy – Sign In with the wrong password</p>

This is the figure to show a pop-up alert to mention that the customer has entered the wrong password when signing in.

<img width="509" height="361" alt="image" src="https://github.com/user-attachments/assets/1dc1ac78-a546-46d9-962e-07716847f5e0" />

Reverify account – If customer has forgotten to verify the account previously

 <img width="448" height="301" alt="image" src="https://github.com/user-attachments/assets/e392a3b8-c249-45cc-8542-2f1a740adbbc" />

<p align="center">Figure 4.20 Cuppa Joy – Reverify the account</p>

This is the figure to show the pop-up alert to mention the customer has forgotten to verify their account previously. So, the system will ask the customer to verify again before signing in.

 <img width="651" height="305" alt="image" src="https://github.com/user-attachments/assets/a54950ec-ccc8-43ae-b7ae-dea5a4ec0099" />

<p align="center">Figure 4.21 Cuppa Joy – Reverify the account</p>

This is the figure to show the pop-up message to show the email has been sent successfully to customers to enable them to receive the OTP code. After they click “OK”, the system will lead the customer to verify the account page again. The verification account process is the same as the previous verification process after the customer creates the account.

 <img width="449" height="163" alt="image" src="https://github.com/user-attachments/assets/bd8c51e3-8626-4ab4-9ba0-7a7f77a6db30" />

<p align="center"> Figure 4.22 Cuppa Joy – Reverify the account </p>

This is the figure to show the mailbox of customers that has received the OTP code for reverifying their account.

 <img width="865" height="326" alt="image" src="https://github.com/user-attachments/assets/44a66d9b-503f-4723-9b4d-d6eaf27c007d" />

<p align="center"> Figure 4.23 Cuppa Joy – Forgot Password</p>

This is the figure to show a link to enable customers reset their password if they have forgotten the password.

Recover Password page 

 <img width="381" height="251" alt="image" src="https://github.com/user-attachments/assets/1de5d96d-a16e-4366-9600-89520edd8b52" />

<p align="center">Figure 4.24 Cuppa Joy - Recover Password</p>

This is the figure to show the page that enables customers to enter their email to reset their password. The email will be used to send emails for reset password interfaces. If a customer doesn’t want to reset the password, they can click to go back Sign-In page.

 <img width="400" height="255" alt="image" src="https://github.com/user-attachments/assets/b994ff4c-c157-4ce5-905e-c6d827031bfc" />

<img width="428" height="348" alt="image" src="https://github.com/user-attachments/assets/072c36e2-623b-411b-98be-034c6dba8c52" />

<p align="center">Figure 4.25 Cuppa Joy - Recover Password with the wrong email format</p>

This is the figure to show the error message and pop-up alert when a customer enters the wrong email format.

<img width="866" height="385" alt="image" src="https://github.com/user-attachments/assets/b9a53fb5-f960-44c9-be58-4eff58c1e22b" />
 
<p align="center">Figure 4.26 Cuppa Joy - Recover Password with not existing email</p>

This is the figure to show the pop-up alert when customers enter the non-existent email in the database.

 <img width="864" height="363" alt="image" src="https://github.com/user-attachments/assets/f061fff0-3b90-483c-af5d-3c1b3ebcac1c" />

<p align="center"> Figure 4.27 Cuppa Joy – Email to send page for resetting the password</p>

This is the figure to show the mailbox after the customer enters the correct email to reset the password. Customer can click the “Reset Password” link to reset their password.

Reset Password page

 <img width="534" height="519" alt="image" src="https://github.com/user-attachments/assets/b8ebef71-75ce-46a7-9c41-4a73c45b30ce" />

<p align="center">Figure 4.28 Cuppa Joy - Reset Password</p>

This is the figure to show the page that enables customers to enter a new password and confirm it again by resetting the password. If customers don’t want to reset their password, they can go back to the Sign-In page by clicking “Back to Sign In”.

 <img width="564" height="369" alt="image" src="https://github.com/user-attachments/assets/0e10d8e4-bea7-4434-9a97-eb25b2f680d7" />

<p align="center">Figure 4.29 Cuppa Joy - Reset Password with the same password</p>

This is the figure to show the customer has entered the new password that same as the old password.

 <img width="515" height="460" alt="image" src="https://github.com/user-attachments/assets/aca9e56c-6af9-43ad-82f5-2c00fe0a1aca" />

<p align="center">Figure 4.30 Cuppa Joy - Reset Password with the same password</p>

This is the figure to show the pop-up alert when the customer tries to update the new password that same as the old password. The password cannot be updated even if the new password was confirmed correctly.

 <img width="423" height="293" alt="image" src="https://github.com/user-attachments/assets/2d033bcb-e742-41f6-b945-e17a53b83644" />

<p align="center">Figure 4.31 Cuppa Joy - Reset Password with wrong password information</p>

This is the figure to show the customer has entered the wrong password format and the system has shown the message to ask the customer to create the password based on the requirements such as a lowercase and an uppercase, minimum 8 characters, and contain a number.

<img width="486" height="473" alt="image" src="https://github.com/user-attachments/assets/2aff7e6c-e31a-487a-bdb0-12381042721f" />
 
<p align="center">Figure 4.32 Cuppa Joy - Reset Password with not matched password</p>

This is the figure to show the pop-up alert when the customer doesn’t match the new password and confirms the password.

 <img width="438" height="280" alt="image" src="https://github.com/user-attachments/assets/3bf5c3e5-5cb6-41b5-a07a-2b57c47ac7d6" />

<p align="center">Figure 4.33 Cuppa Joy - Reset Password with correct password information</p>

This is the figure to show the customer has entered the correct password format and the password is matched with the confirm password. Then, the new password is not the same as the old password. Thus, the customer can reset the password.

 <img width="616" height="311" alt="image" src="https://github.com/user-attachments/assets/a775beda-2a69-4062-a443-0967d621e30e" />

<p align="center">Figure 4.34 Cuppa Joy - Reset Password successfully</p>

This is the figure to show the pop-up message to show the customer has reset the password successfully.

Home page – Not Sign In/Sign In

<img width="623" height="284" alt="image" src="https://github.com/user-attachments/assets/397afdc0-808d-4359-aea5-faa41588f788" />
<img width="528" height="185" alt="image" src="https://github.com/user-attachments/assets/3f19eb0f-a21f-4c63-8f14-6a88113ff929" />
<img width="440" height="315" alt="image" src="https://github.com/user-attachments/assets/6d782843-fcab-4081-b218-258ebfe7bfb3" />
<img width="620" height="179" alt="image" src="https://github.com/user-attachments/assets/a3f51b5f-ac52-4703-8303-069b599fb228" />
<img width="830" height="54" alt="image" src="https://github.com/user-attachments/assets/d4c9e1e8-a69d-4466-87f8-195fb4dc6488" />

<p align="center">Figure 4.35 Cuppa Joy – Home Page (Not sign in/Sign In)</p>

This is the figure to show the Cuppa Joy Home page, whether customers have to sign in or not sign in, the home page can be viewed by customers. The difference between customers who have signed in and not signed in is the header. The home page will show the new drinks or foods, popular drinks or foods, and the baristas.

 <img width="490" height="339" alt="image" src="https://github.com/user-attachments/assets/920eefd5-e2a0-4269-b88a-35ab8742c870" />

<p align="center">Figure 4.36 Cuppa Joy –Header (Sign in)</p>

This is the figure to show the page header if the customer signs in. The header will show the pages, cart, wish list, profile, and “Sign Out” button. Then, also shows the logo that enables customers to go back to the home page when they in other pages.

 <img width="865" height="61" alt="image" src="https://github.com/user-attachments/assets/b2f3ed00-9223-4e5b-8ed8-c984f2356e84" />

<p align="center">Figure 4.37 Cuppa Joy – Sign Out confirmation message</p>

This is the pop-up confirmation message to ask the customer to confirm to log out after the customer clicks the “Sign Out” button. If the customer clicks “cancel”, it has nothing happens, else if the customer clicks “Yes, log me out”, the system will log the customer out from the system.

<img width="443" height="328" alt="image" src="https://github.com/user-attachments/assets/47b36aba-0879-490e-acae-33bd15c06f66" />

<p align="center">Figure 4.38 Cuppa Joy –Header (Not sign in)</p>

This is the figure to show the page header if the customer has not signed in yet. The header will show the pages, cart, wish list, and “Sign in” or “Sign up” buttons. Then, also shows the logo that enables customers to go back to the home page when they in other pages.
 
 <img width="434" height="294" alt="image" src="https://github.com/user-attachments/assets/29fc7ac8-b06f-4dbb-9170-99f8159c0421" />
<img width="395" height="270" alt="image" src="https://github.com/user-attachments/assets/705d365e-3d50-47cf-bf47-ee91569891c6" />
<img width="866" height="871" alt="image" src="https://github.com/user-attachments/assets/6c602686-7ac8-4ec7-a3bb-2000c321d434" />
 
<p align="center">Figure 4.39 Cuppa Joy – Sign-in confirmation message (Not Sign In)</p>

These are the pop-up confirmation messages to ask the customer to confirm to sign in after the customer clicks the cart page icon, wish list page icon, and order history page. If the customer clicks “cancel”,it has nothing happens, else if the customer clicks “Sign In”, the system will lead the customer to the Sign in page.

User Profile

 <img width="526" height="459" alt="image" src="https://github.com/user-attachments/assets/f588c662-f924-4474-abaa-f0350954f72c" />

<p align="center">Figure 4.40 Cuppa Joy – Profile page</p>

This is the figure to show the customer profile page. It shows some customer information and enables customers to edit profiles, change passwords, and add addresses.

 <img width="434" height="366" alt="image" src="https://github.com/user-attachments/assets/b34f71dd-1657-443a-9e04-df0812079252" />

<p align="center">Figure 4.41 Cuppa Joy – Edit profile</p>

This is the figure to show the pop-up form to enable the customer to edit the profile. Customers can edit their first name, last name, and phone number only.

 <img width="480" height="324" alt="image" src="https://github.com/user-attachments/assets/d5701b21-9114-4af8-a9a8-fd6c2a0afbd4" />

<p align="center">Figure 4.42 Cuppa Joy – Edit profile with wrong information</p>

This is the figure to show the error message when a customer enters the wrong information.

 <img width="480" height="320" alt="image" src="https://github.com/user-attachments/assets/2b994b4c-acf0-4470-a669-7b38b2174f2f" />

<p align="center">Figure 4.43 Cuppa Joy – Edit profile with the wrong first name</p>

This is the figure to show the pop-up alert when the customer enters the wrong first name. The customer's first name cannot contain a number.

 <img width="443" height="293" alt="image" src="https://github.com/user-attachments/assets/c4167217-e36b-4c7d-ac6c-d0e2d91c63b1" />

<p align="center">Figure 4.44 Cuppa Joy – Edit profile with the wrong last name</p>

This is the figure to show the pop-up alert when a customer enters the wrong last name. The customer's last name cannot contain a number.

 <img width="523" height="355" alt="image" src="https://github.com/user-attachments/assets/8eaa2465-bed3-4819-acc1-da6c84df8d82" />

<p align="center">Figure 4.45 Cuppa Joy – Edit profile with wrong phone number</p>

This is the figure to show the pop-up alert when a customer enter the wrong phone number. Customer phone numbers cannot contain the alphabet and only can in the range of 9 – 11 digits.

 <img width="865" height="211" alt="image" src="https://github.com/user-attachments/assets/d84d13f8-10c1-4ee4-9469-5c2777f3a0fc" />
<img width="540" height="438" alt="image" src="https://github.com/user-attachments/assets/cd657c9e-9764-49b4-af6a-17924aa25af4" />

<p align="center">Figure 4.46 Cuppa Joy – Edit profile with successfully</p>

This is the figure to show the pop-up message when a customer edit a profile successfully. The table shows the customer information has been updated.

  <img width="539" height="213" alt="image" src="https://github.com/user-attachments/assets/26cf79fc-0e0c-47a4-8560-d30e9360d33c" />
  <img width="594" height="374" alt="image" src="https://github.com/user-attachments/assets/9e3d6425-df4b-4d19-abca-e059366e15b7" />

<p align="center">Figure 4.47 Cuppa Joy – Add address</p>

This is the figure to show the pop-up form when the customer wants to add an address. The customer can enter address 1, and address 2, and select the city in the area of 75450 postcode only.

<img width="455" height="288" alt="image" src="https://github.com/user-attachments/assets/e92512ba-ce98-446c-ae46-a7b1505c6533" />
<img width="470" height="305" alt="image" src="https://github.com/user-attachments/assets/7992c5b6-c3b2-4fab-b154-a926985a86bf" />
<img width="554" height="486" alt="image" src="https://github.com/user-attachments/assets/cd844433-d932-451f-89ba-384b2c6d431c" />

<p align="center">Figure 4.48 Cuppa Joy – Add address with wrong information</p>

This is the figure to show the error message and pop-up alert when a customer adds an address with the wrong information. Address 1 and address 2 cannot be empty.

 <img width="509" height="334" alt="image" src="https://github.com/user-attachments/assets/4ca5af56-d104-4c88-85e7-80f290980cde" />

<p align="center">Figure 4.49 Cuppa Joy – Add address with correct information</p>

This is the figure to show the customer add address with the correct information. 

<img width="530" height="176" alt="image" src="https://github.com/user-attachments/assets/70dba802-6cf8-4224-969d-e2266f0a488c" />
<img width="596" height="326" alt="image" src="https://github.com/user-attachments/assets/1969067d-99bf-4241-8802-b8784725b2a8" />

<p align="center">Figure 4.50 Cuppa Joy – Add address with correct information</p>

This is the figure to show the pop-up message when the customer add an address successfully and the address table is updated too.
 
 <img width="579" height="169" alt="image" src="https://github.com/user-attachments/assets/4bc4bd0a-af34-4932-a94f-972d2faf193f" />
<img width="838" height="375" alt="image" src="https://github.com/user-attachments/assets/f4bb5e80-baeb-40d4-a219-0cb03afc696b" />

<p align="center">Figure 4.51 Cuppa Joy – Delete address</p>

This is the figure to show the pop-up confirmation message when the customer deletes the address, and the address table is updated too. When the customer clicks “cancel”, it has nothing happens, else the address will be deleted.
Change Password page

 <img width="409" height="526" alt="image" src="https://github.com/user-attachments/assets/5c43d35d-ca8a-4739-a0b4-aa38f91e984c" />

<p align="center">Figure 4.52 Cuppa Joy – Change Password page</p>

This is the figure to show the form to change the password. If customers don’t want to change anymore, they can click “Back to profile” to go back.

<img width="469" height="309" alt="image" src="https://github.com/user-attachments/assets/73ca8908-7fa7-4ca6-ab88-a34e296863b8" />
<img width="473" height="90" alt="image" src="https://github.com/user-attachments/assets/d6887df3-8b09-4158-ae48-ad3c0e234c5c" />

<p align="center">Figure 4.53 Cuppa Joy - Change Password with the same password</p>

This is the figure to show the customer has entered the new password that same as the old password. If the customer changes the same password, the system will show a pop-up alert to mention customer the old and the same password cannot be the same. Even though the password was confirmed correctly.

<img width="455" height="304" alt="image" src="https://github.com/user-attachments/assets/b9c48d9c-c835-46a5-85a2-57dba9d0f3ae" />
<img width="323" height="508" alt="image" src="https://github.com/user-attachments/assets/31b3bb10-abe3-4420-8f2a-b99599939569" />

<p align="center">Figure 4.54 Cuppa Joy - Change Password with the wrong old password</p>

This is the figure to show the customer has entered the wrong old password. Thus, the system will mention that the old password entered is wrong.

 <img width="339" height="280" alt="image" src="https://github.com/user-attachments/assets/001b5e05-73dc-455f-873a-478c4d87543d" />

<p align="center">Figure 4.55 Cuppa Joy - Change Password with wrong password information</p>

This is the figure to show the customer has entered the wrong password information. Thus, the system will mention the requirements to form the new password.

 <img width="339" height="224" alt="image" src="https://github.com/user-attachments/assets/1c33501f-cfbb-4ed4-a951-ba1038f06bd6" />
<img width="810" height="1235" alt="image" src="https://github.com/user-attachments/assets/5f112af2-8d98-41d8-8c15-6d5176d898ba" />
 
<p align="center">Figure 4.56 Cuppa Joy - Change Password with wrong password information</p>

This is the figure to show the pop-up alert when the customer creates the password without following the requirements such as lowercase and uppercase, minimum 8 characters, and contain a number, and the new password does not match the confirmed password.


Menu page – Not Sign In/Sign In

 <img width="858" height="100" alt="image" src="https://github.com/user-attachments/assets/954dd750-49f6-43e0-acb4-6e85c8ec2fe3" />

<p align="center">Figure 4.57 Cuppa Joy – Menu page (Not Sign In / Sign In)</p>

This is the figure to show the menu page of the system. The menu is the same for customers when they are not signed in yet or signed in.

<img width="865" height="158" alt="image" src="https://github.com/user-attachments/assets/1d990521-2ce2-4dc2-8094-077e81b3382f" />
<img width="866" height="858" alt="image" src="https://github.com/user-attachments/assets/07764b5f-b044-4930-ac03-46d8d452b698" />

<p align="center">Figure 4.58 Cuppa Joy – Menu page with different categories</p>

This is the figure to show the variance in drinks or food categories for customers. Customers can navigate the categories by clicking the left and right arrows.

 <img width="761" height="898" alt="image" src="https://github.com/user-attachments/assets/3dbd2353-60e4-441c-bc6a-a128f832adcb" />

<p align="center">Figure 4.59 Cuppa Joy – Menu page with multiple pagination</p>

This is the figure to show multiple pages for all categories. One page of the menu will show 12 items. Then the menu will show all items even if the item is not available currently. The not-available items will have a grey color filter and customers cannot click it to view product details. Customers can click “Prev” to go back to the previous page, and “Next” for the next page.

 <img width="865" height="530" alt="image" src="https://github.com/user-attachments/assets/72c31d66-c7a2-4e0e-8854-81646ea2e967" />

<p align="center">Figure 4.60 Cuppa Joy – Menu page with categories and multiple pagination</p>

This is the figure to show multiple pages for the selected category. One page of the menu will show 12 items. Then the menu will show all items of that category even if the item is not available currently. The not available items also have the grey color filter and customers cannot click it to view product details. Customers can click “Prev” to go back to the previous page, and “Next” for the next page. Then at the top of the item list, the system shows what category exactly the customer has chosen.

<img width="724" height="1283" alt="image" src="https://github.com/user-attachments/assets/537d14d4-e458-499f-bd89-79b48b617214" />

<p align="center">Figure 4.61 Cuppa Joy – Menu page with searching function</p>

This is the figure to show the search results after customers have searched keywords for some items. For example, if a customer enters “sm”, the system will show the item that contains “sm”. The searched items list will change back to the original items list when customers clear the input they enter in the search field.

Product Details page  – Not Sign In/Sign In
 
 <img width="866" height="980" alt="image" src="https://github.com/user-attachments/assets/501219d5-a257-492d-9543-f9860ee850c4" />
<img width="866" height="980" alt="image" src="https://github.com/user-attachments/assets/98274d0f-4f48-4226-a07b-ef5ca395c364" />

<p align="center">Figure 4.62 Cuppa Joy – Product details page with customization or no customization available</p>

This is the figure to show the item after the customer clicks the item from the menu. On this page, it will show some item information, an “Add to wishlist” button, customization for customizable items, quantity an “Add to cart” button, and the other foods or drinks. If this item has been commented on by other customers, the comments also will be shown above the “Add to cart” button. This page can be viewed if the customer is not signed in or signed in.

<img width="418" height="345" alt="image" src="https://github.com/user-attachments/assets/113c618c-0f00-4000-bac8-c3f655d4501d" />
<img width="420" height="349" alt="image" src="https://github.com/user-attachments/assets/6ace2b60-12e5-4ca7-9714-87c4f054b685" />

<p align="center">Figure 4.63 Cuppa Joy – Product details page without sign-in</p>

This is the figure to show the pop-up confirmation message to customers after they intend to add the item to their wish list or cart. This only occurs when the customer is not signed in yet.

<img width="386" height="196" alt="image" src="https://github.com/user-attachments/assets/75b80814-b662-42ab-8b6b-ab621eaec88b" />

<p align="center">Figure 4.64 Cuppa Joy – Product details page (Add to wish list)</p>

This is the figure to show the “Add to wish list” button has been used, which means that this item has been added and kept in the wish list already. If the customer clicks the button again, the customer can go to the wish list page.

 <img width="433" height="281" alt="image" src="https://github.com/user-attachments/assets/770be5bd-eed4-498b-ab48-7831cb903bbd" />
<img width="466" height="315" alt="image" src="https://github.com/user-attachments/assets/56f4ff19-3ed3-42bb-a77e-f174bf723490" />

<p align="center">Figure 4.65 Cuppa Joy – Product details page (Add to cart)</p>

This is the figure to show the pop-up alert when the customer didn’t add quantity (quantity is 0) and the quantity is greater than 12 after they click “Add to cart”. The system has limited the customers to one cart cannot contain 12 items due to delivery reasons.

<img width="489" height="153" alt="image" src="https://github.com/user-attachments/assets/61f4c443-0127-49e7-a88a-ebf487723ab8" />
<img width="486" height="316" alt="image" src="https://github.com/user-attachments/assets/d7d26b7e-7b8f-4b55-87f6-b5102916e634" />

<p align="center">Figure 4.66 Cuppa Joy – Product details page (Add to cart)</p>

This is the figure to show the pop-up alert when the customer adds the item to the cart and checks the cart whether has over 12 items already. So, this alert will be shown when the cart has over 12 items after the new quantity that the customer wants to add is added to the existing item quantity in the cart.  For example, a cart currently has 6 items already, so if the customer wants to add more than 7 items, it is not allowed.

<img width="866" height="446" alt="image" src="https://github.com/user-attachments/assets/bdcc871d-6508-42a2-8a60-7f7337f26639" />
<img width="425" height="359" alt="image" src="https://github.com/user-attachments/assets/0e3d2b59-975a-4bb4-baa1-9b804c1ef0ee" />

<p align="center">Figure 4.67 Cuppa Joy – Product details page (Add to cart)</p>

This is the figure to show the pop-up alert when the customer adds the item to the cart without selecting the compulsory customization category. The compulsory customization will show a sentence “Pick one option” in the customization category. For the non-compulsory customization, the customer can choose multiple customizations or directly ignore them, such as topping. For example, the compulsory customizations are ice level, sugar level, and size.

<img width="473" height="311" alt="image" src="https://github.com/user-attachments/assets/67716457-d868-4c0f-8cf0-02ed1896789d" />

<p align="center">Figure 4.68 Cuppa Joy – Product details page (Add to cart)</p>

This is the figure to show the pop-up confirmation message to ask the customer to confirm whether to add the same item to the cart. If customers click “cancel”, means that they don’t want to add the same item, else the item will be added to the cart and increment the cart item quantity.

Wish list page

 <img width="866" height="855" alt="image" src="https://github.com/user-attachments/assets/7a5a16ef-7648-4f86-85f6-4d7a9c92f682" />

<p align="center">Figure 4.69 Cuppa Joy – Wish list page </p>

This is the figure to show the wish list page that contains the items that customers have added from the product detail page. Customers can add the item to the cart from the wish list. If the item is not available currently, the item will filter with a grey color and can be removed the item.

 <img width="866" height="300" alt="image" src="https://github.com/user-attachments/assets/c5f413bc-a8d7-4745-acaf-8d3c9c2514bf" />

<p align="center">Figure 4.70 Cuppa Joy – Wish list page that item is not customizable</p>

This is the figure to show the wish list item that cannot be customized. Thus, the customer can only add the quantity and add to the cart.

 <img width="865" height="483" alt="image" src="https://github.com/user-attachments/assets/73b041ec-fc5a-499e-9d1a-cc4ec5176d8c" />

<p align="center">Figure 4.71 Cuppa Joy – Wish list page that item is customizable</p>

This is the figure to show the wish list item that can be customized. Thus, the customer can choose the customization based on the compulsory customization category or not compulsory to add to the cart.

 <img width="433" height="281" alt="image" src="https://github.com/user-attachments/assets/bd3c30c8-372d-49d5-9e9b-294dc976fc78" />
<img width="466" height="315" alt="image" src="https://github.com/user-attachments/assets/f5358062-a4fe-4d6a-8cb4-c28b22278a3c" />

<p align="center">Figure 4.72 Cuppa Joy – Wish list page (Add to cart)</p>

This is the figure to show the pop-up alert when the customer didn’t add quantity (quantity is 0) and the quantity is greater than 12 after they click “Add to cart”. The system has limited the customers to one cart cannot contain 12 items due to delivery reasons.


Figure 4.73 Cuppa Joy – Wishlist page (Add to cart)
This is the figure to show the pop-up alert when the customer adds the item to the cart and checks whether the cart has over 12 items already. So, this alert will be shown when the cart has over 12 items after the new quantity that the customer wants to add is added to the existing item quantity in the cart.  If the current cart has 4 items already, the customer cannot add 9 items into the cart.

 
Figure 4.74 Cuppa Joy – Wishlist page (Add to cart)
This is the figure to show the pop-up confirmation message to ask the customer to confirm whether to add the same item to the cart. If the customer clicks “cancel”, means that they don’t want to add the same item, else the item will be added to the cart and increment the cart item quantity.

Show promo page 

 
Figure 4.75 Cuppa Joy – Show promo (Not Sign In)
This is the figure to show the coupons for all customers. When the customer is not signed in yet, the promo will show based on before the end date.

 
Figure 4.76 Cuppa Joy – Show promo (Sign In)
This is the figure to show the coupons after customers sign in. The coupons will be shown after checking what coupons are not used yet by this customer. If the coupons have been used, they won’t show on the coupons list after the customer signs in.
About us page 
 

Figure 4.77 Cuppa Joy – About us
This is the figure to show the About Us page that describes the vision, mission, and objective of Cuppa Joy. Also, introduce the Cuppa Joy’s baristas.
 
Contact us page 
  
 
Figure 4.78 Cuppa Joy – Contact us
This is the figure to show the Contact Us page that contains the form, shop hours, contact information, and the map of the Cuppa Joy.

 
Figure 4.79 Cuppa Joy – Contact us (Not Sign In)
This is the figure to show the Contact Us form that the customer has not signed in yet, so the customer needs to enter all the information needed.
 
 
 
Figure 4.80 Cuppa Joy – Contact us (Not Sign In)
This is the figure to show the error message and pop-up alert when a customer enter the wrong first name. The first name cannot contain the number.

 
 
Figure 4.81 Cuppa Joy – Contact us (Not Sign In)
This is the figure to show the error message and pop-up alert when a customer enters the wrong last name. The last name cannot contain a number.


 
 
Figure 4.82 Cuppa Joy – Contact us (Not Sign In)
This is the figure to show the error message and pop-up alert when a customer enter the wrong email. The email must be correct, contain “@” and after “.” must have at least 2 characters.


 
 
Figure 4.83 Cuppa Joy – Contact us (Not Sign In)
This is the figure to show the error message and pop-up alert when a customer enters the wrong phone number. The phone number must contain 9 – 11 digits and cannot contain the alphabet.

 
Figure 4.84 Cuppa Joy – Contact us (Not Sign In)
This is the figure to show the pop-up alert when customers have missed entering the fields.

 
Figure 4.85 Cuppa Joy – Contact us (Sign In)
This is the figure to show the Contact Us form that the customer is signed in to, so the customer does not need to enter all the information. Customers need to enter the subject and message only. This is because the personal information has been retrieved from the database if the customer has signed in.



