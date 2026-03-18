<?php 
 
// Product Details 
$itemNumber = "DP12345"; 
$itemName = "Demo Product"; 
$itemPrice = 75;  
$currency = "MYR"; 
 
/* PayPal REST API configuration 
 * You can generate API credentials from the PayPal developer panel. 
 * See your keys here: https://developer.paypal.com/dashboard/ 
 */ 
define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production 
define('PAYPAL_SANDBOX_CLIENT_ID', 'Adqt-xW6a8bZvnw9S39YLGpR5reaOGasw62-KbedAexehIygJ5Lx4rT6kBn_F_3rFvFZaZzJlyoq5Dk3'); 
define('PAYPAL_SANDBOX_CLIENT_SECRET', 'ENwsF6sx8-uCPgfywcO9wJcBznWStaSq9pqrfgemg-1f0t_jm6YqVanKfdBZpBdFg0NVrWBaeB5znKNy'); 
define('PAYPAL_PROD_CLIENT_ID', 'Adqt-xW6a8bZvnw9S39YLGpR5reaOGasw62-KbedAexehIygJ5Lx4rT6kBn_F_3rFvFZaZzJlyoq5Dk3'); 
define('PAYPAL_PROD_CLIENT_SECRET', 'ENwsF6sx8-uCPgfywcO9wJcBznWStaSq9pqrfgemg-1f0t_jm6YqVanKfdBZpBdFg0NVrWBaeB5znKNy'); 
  
 
?>