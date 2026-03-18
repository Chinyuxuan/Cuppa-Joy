<?php
    include("db_connection.php");
    require_once 'PayPalConfig.php'; 
    require "Mail/phpmailer/PHPMailerAutoload.php";
    // $custid = $_SESSION['customer_id'];
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $custid = $_SESSION['customer_id'];
	$oid = $_SESSION['O_ID'];
    $currentDate = date("Y-m-d");
    date_default_timezone_set('UTC');
    $sql = "SELECT * FROM `customer` WHERE C_ID = '$custid'";
	$gotResult = mysqli_query($con, $sql);

	if($gotResult){
		if(mysqli_num_rows($gotResult)>0){
			while($row = mysqli_fetch_array($gotResult)){

				$firstname = $row['C_Firstname'];
				$lastname = $row['C_Lastname'];
				$phno = $row['C_ContactNumber'];
				$Email = $row['C_Email'];
				// $bod = $row['C_DOB'];
				$password = $row['C_PW'];
			}
		}
	}
    $statuslogo = "";
    $statusMsg = "";

	// Assuming the product ID is stored in a column named 'P_ID'
    $CartNo = "SELECT * FROM cart WHERE C_ID = ? AND C_Status = 'No-Paid'";
    $stmt = $con->prepare($CartNo);
    $stmt->bind_param("i", $custid);
    $stmt->execute();
    $result = $stmt->get_result();
	// Fetch the CT_ID from the result set
    if($row = $result->fetch_assoc()){
        $CT_ID = $row['CT_ID'];
        $pmcode = $row['Promo_ID'];
    }
    


    $sql1 = "SELECT * FROM Cart_Item WHERE CT_ID = ?";
    $stmt1 = $con->prepare($sql1);
    $stmt1->bind_param("i", $CT_ID);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $cartitems = [];
    if($result1->num_rows > 0){
        while($row1 = $result1->fetch_assoc()){
            $cartitems[] = $row1;
        }
    }

    $sql5 = "SELECT * FROM reservation WHERE O_ID = ?";
	$stmt5 = $con->prepare($sql5);
	$stmt5->bind_param("i", $oid);
	$stmt5->execute();
	$result5 = $stmt5->get_result();
	$row5 = $result5->fetch_assoc();
	$ttl = number_format($row5['Total'], 2);

    function sendEmail($Email, $name, $cartitems, $con, $ttl) {

        $mail = new PHPMailer;
        try {
            $mail->isSMTP();                                            
            $mail->Host       = 'smtp.gmail.com';                     
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = 'cuppajoy88@gmail.com';                     
            $mail->Password   = 'imjo jrya kyki hgjt';                               
            $mail->SMTPSecure = 'tls';          
            $mail->Port       = 587;                                    
        
            //Recipients
            $mail->setFrom('cuppajoy88@gmail.com', 'Tok');
            $mail->addAddress($Email);
            $IMAGE = '../user/assets/img/full-black.png';
            $mail->AddEmbeddedImage($IMAGE, 'website_logo');
            $mail->isHTML(true);                                  
            $mail->Subject = 'Receipt for order';
            $body = "
            <div style='font-family: Arial, sans-serif; font-size: 14px; width: 100%; color: #000;'>
                <div style='display: flex; flex-direction: row; justify-content: space-between; width: 100%'>
                    <div style='width: 60%; color: #000;' >
                        <img src='cid:website_logo' alt='CuppaJoy' style='width: 200px;'>
                        <h2>Order Receipt</h2>
                    </div>
                    <div style='width: 40%; float: right; color: #000;'>
                        <p>CuppaJoy</p>
                        <p>123, Jalan Ayer Keroh Lama</p>
                        <p>Kampung Baru Ayer Keroh, 75450 Ayer Keroh,</p>
                        <p>Melaka, Malaysia</p>
                    </div>
                </div>
                <div>
                    <p>Name: $name</p>
                    <p>Email: $Email</p>
                </div>
                <div>
                    <table border='1' cellspacing='0' cellpadding='5' style='width: 100%; border-collapse: collapse;'>
                        <thead>
                            <tr>
                                <th style='padding: 5px; text-align: left;'>Quantity</th>
                                <th style='padding: 5px; text-align: left;'>Product</th>
                                <th style='padding: 5px; text-align: left;'>Price</th>
                            </tr>
                        </thead>
                        <tbody>";
                        // Iterate through cart items to add them to the table
                        $subtotal = 0;
                        // echo '<pre>';
                        // print_r($cartitems);
                        // echo '</pre>';
                        foreach ($cartitems as $cartitem) {
                            $sql22 = "SELECT * FROM product WHERE P_ID =" . $cartitem['P_ID'];
                            $result22 = mysqli_query($con, $sql22);
                            if($result22){
                                while($row = mysqli_fetch_array($result22)){
                                    $pname = $row['P_Name'];
                                    $pprice = $row['P_Price'];
                                    $quantity = $cartitem['Qty'];
                        
                                    $body .= "
                                    <tr>
                                        <td style='padding: 5px;'>$quantity</td>
                                        <td style='padding: 5px;'>$pname";
                        
                                    // Fetch customization details for each cart item
                                    $sql6 = 'SELECT * FROM Details WHERE c_item_id = ? ORDER BY customize_id ASC';
                                    $stmt6 = $con->prepare($sql6);
                                    $stmt6->bind_param('i', $cartitem['CI_ID']);
                                    $stmt6->execute();
                                    $result6 = $stmt6->get_result();
                                    $customitems = [];
                                    if ($result6->num_rows > 0) {
                                        while ($row6 = $result6->fetch_assoc()) {
                                            $customitems[] = $row6;
                                        }
                                    }
                        
                                    $addprice = 0;
                                    foreach ($customitems as $customitem) {
                                        $sql7 = 'SELECT custom.*, cc.CC_Group, cc.compulsory_status 
                                                FROM customization AS custom 
                                                INNER JOIN customize_category AS cc 
                                                ON custom.CC_ID = cc.CC_ID 
                                                WHERE custom.Custom_ID = ?';
                                        $stmt7 = $con->prepare($sql7);
                                        $stmt7->bind_param('i', $customitem['customize_id']);
                                        $stmt7->execute();
                                        $result7 = $stmt7->get_result();
                                        while ($row3 = $result7->fetch_assoc()) {
                                            $customname = $row3['Custom_Name'];
                                            $customprice = $row3['Custom_Price'];
                                            $typecc = ($row3['compulsory_status'] == "yes") ? "ccrequire" . $row3['CC_ID'] : "";
                                            $body .= "<br><span class='customitem {$typecc}'> - {$customname}</span>";
                                            $addprice += $customprice;
                                        }
                                    }
                                    $priceperitem = $pprice + $addprice;
                                    $priceperitem = number_format($priceperitem, 2);
                                    $subtotal = $subtotal + $priceperitem;
                                    $subtotal = number_format($subtotal, 2);
                        
                                    $body .= "</td>
                                                <td style='padding: 5px;'>$priceperitem</td>
                                    </tr>";
                                }
                            }
                        }
                        
                                // Close the table and add the subtotal
                                $body .= "
                                        </tbody>
                                    </table>
                                </div>
                                <div style='margin-top: 20px;'>
                                    <p>Subtotal: RM $subtotal</p>
                                    <p>Total(including discount and delivery fee): RM $ttl</p>
                                    <p>Please visit us again.</p>
                                    <p>Thank you</p>
                                </div>
                            </div>";
            // echo $body;
            $mail->Body = $body;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->send();
            return true;
        } catch (Exception $e) {
          echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
          return false; // Failed to send email
        }
    }

    if(!empty($_POST['stripeToken'])){
        //get token, card and user info from the form
        $token  = $_POST['stripeToken'];
        $name = $_POST['name'];
        $card_num = str_replace(' ', '', $_POST['card_num']);
        $card_num = (int)$card_num;
        $card_cvc = $_POST['cvc'];
        $card_exp_month = $_POST['exp_month'];
        $card_exp_year = $_POST['exp_year'];
        
        //include Stripe PHP library
        require_once('stripe-php-14.1.0/init.php');
        
        //set api key
        $stripe = array(
            "secret_key"      => "sk_test_51P417pCwq26I0BhNf4hJIuZpZFjqXnvpFF4ZOJ8oQFutGWhXbswrX9mBXL5rT9Xyb0iI3kdRwe4ody7KUSn9Cg1G005UdeFAe1",
            "publishable_key" => "pk_test_51P417pCwq26I0BhNGEdkBzymyafNdECdelrpVbIQgXZJnws9Nc4cES3EnJ1PYUWbi5I64o4TiOoxLJPFQNs4o9PK00qjVWpTIb"
        );
        
        \Stripe\Stripe::setApiKey($stripe['secret_key']);
        
        //add customer to stripe
        $customer = \Stripe\Customer::create(array(
            'email' => $Email,
            'source'  => $token
        ));
        
        //item information
        $itemName = "Premium Script CodexWorld";
        $itemNumber = "PS123456";
        $itemPrice = 55;
        $currency = "myr";
        $orderID = "SKA92712382139";
        $amountInCents = intval($ttl * 100);

		// Charge a credit or a debit card
		$charge = \Stripe\Charge::create(array(
			'customer' => $customer->id,
			'amount'   => $amountInCents, // Pass the converted amount
			'currency' => $currency,
			'description' => $itemName,
			'metadata' => array(
				'order_id' => $oid
			)
		));
        
        //retrieve charge details
        $chargeJson = $charge->jsonSerialize();

        //check whether the charge is successful
        if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){
            //order details 
            $amount = $chargeJson['amount'];
            $balance_transaction = $chargeJson['balance_transaction'];
            $currency = $chargeJson['currency'];
            $status2 = $chargeJson['status'];
            $date = date("Y-m-d");
			$time = date("H:i:s");
			$pmstatus = "paid";
			$pmmethod = "card";

			
			$sql6 = "INSERT INTO payment (Date, Time, O_ID, PM_Status, PM_Method) VALUES (?, ?, ?, ?, ?)";
			$stmt6 = $con->prepare($sql6);
			$stmt6 -> bind_param("ssiss", $date, $time, $oid, $pmstatus, $pmmethod);
			$result6 = $stmt6->execute();
			$pmid = mysqli_insert_id($con);
            
            //include database config file
            // include_once 'dbConfig.php';

            $sql7 = "SELECT R_ID FROM rider WHERE R_Status = 'Active'";
            $result7 = $con->query($sql7);
            $riders = [];
            if ($result7->num_rows > 0) {
                while ($row7 = $result7->fetch_assoc()) {
                    $riders[] = $row7['R_ID']; // Store only the rider IDs
                }
            }
            $rid = null;
            $taskqty = PHP_INT_MAX;
            foreach($riders as $rider){
                $sql8 = "SELECT COUNT(*) AS numbertask FROM reservation WHERE R_ID = ? AND Date = ?";
                $stmt8 = $con->prepare($sql8);
                $stmt8 -> bind_param("ss", $rider, $date);
                $stmt8->execute();
                $result8 = $stmt8->get_result(); // Fetch the result
                $row8 = $result8->fetch_assoc();
                $num = $row8['numbertask'];
                if($num < $taskqty){
                    $rid = $rider;
                    $taskqty = $num;
                }
            }

            $sql9 = "UPDATE reservation SET R_ID = ? WHERE O_ID = ?";
            $stmt9 = $con->prepare($sql9);
            $stmt9 -> bind_param("si", $rid, $oid);
            $result9 = $stmt9->execute();

            $sql10 = "UPDATE cart SET C_Status = ? WHERE CT_ID = ?";
            $stmt10 = $con->prepare($sql10);
            $cstatus = "Paid";
            $stmt10 -> bind_param("si", $cstatus, $CT_ID);
            $result10 = $stmt10->execute();

            if(!is_null($pmcode)){
                $sql28 = "INSERT INTO promo_history (Promo_ID, Cus_ID) VALUES (?, ?)";
                $stmt28 = $con->prepare($sql28);
                $stmt28 -> bind_param("ii", $pmcode, $custid);
                $result28 = $stmt28->execute();
            }
            
            //insert tansaction data into the database
            $sql = "INSERT INTO stripepay(name, email, card_num, card_cvc, card_exp_month, card_exp_year,
					paid_amount, paid_amount_currency, txn_id, payment_status, created, modified, PM_ID) VALUES
					('".$name."','".$Email."','".$card_num."','".$card_cvc."','".$card_exp_month."',
					'".$card_exp_year."','".$amount."','".$currency."','".$balance_transaction."'
					,'".$status2."','".$date."','".$time."','".$pmid."')";
            $insert = $con->query($sql);
            $last_insert_id = $con->insert_id;
            

            //if order inserted successfully
            if($last_insert_id && $status2 == 'succeeded'){
                $statuslogo = '<i class="fa-solid fa-circle-check" style="color: #2ecc71;"></i>';
                $statusMsg = "<h4>The transaction was successful.</h4>";
                sendEmail($Email, "$firstname $lastname", $cartitems, $con, $ttl);
            }else{
                $statuslogo = '<i class="fa-solid fa-circle-exclamation" style="color: #d03333;"></i>';
                $statusMsg = "<h4>Transaction has been failed</h4>";
            }
        }else{
            $statuslogo = '<i class="fa-solid fa-circle-exclamation" style="color: #d03333;"></i>';
            $statusMsg = "<h4>Transaction has been failed</h4>";
        }
    }
    // else{
    //     $statusMsg = "Form submission error.......";
    // }
    $status = 'error'; 
    
    // Check whether the payment ID is not empty 
    if(!empty($_GET['checkout_ref_id'])){ 
        $payment_txn_id  = base64_decode($_GET['checkout_ref_id']); 
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $pmstatus = "paid";
        $pmmethod = "PayPal";
        $name = $firstname . $lastname;
        // Fetch transaction data from the database 
        // $sqlQ = "SELECT id,payer_id,payer_name,payer_email,payer_country,order_id,transaction_id,paid_amount,paid_amount_currency,payment_source,payment_status,created FROM transactions WHERE transaction_id = ?"; 
        // $stmt = $db->prepare($sqlQ);  
        // $stmt->bind_param("s", $payment_txn_id); 
        // $stmt->execute(); 
        // $stmt->store_result();

        $sql7 = "SELECT R_ID FROM rider WHERE R_Status = 'Active'";
        $result7 = $con->query($sql7);
        $riders = [];
        if ($result7->num_rows > 0) {
            while ($row7 = $result7->fetch_assoc()) {
                $riders[] = $row7['R_ID']; // Store only the rider IDs
            }
        }
        $rid = null;
        $taskqty = PHP_INT_MAX;
        foreach($riders as $rider){
            $sql8 = "SELECT COUNT(*) AS numbertask FROM reservation WHERE R_ID = ? AND Date = ?";
            $stmt8 = $con->prepare($sql8);
            $stmt8 -> bind_param("ss", $rider, $date);
            $stmt8->execute();
            $result8 = $stmt8->get_result(); // Fetch the result
            $row8 = $result8->fetch_assoc();
            $num = $row8['numbertask'];
            if($num < $taskqty){
                $rid = $rider;
                $taskqty = $num;
            }
        }

        $sql9 = "UPDATE reservation SET R_ID = ? WHERE O_ID = ?";
        $stmt9 = $con->prepare($sql9);
        $stmt9 -> bind_param("si", $rid, $oid);
        $result9 = $stmt9->execute();

        $sql10 = "UPDATE cart SET C_Status = ? WHERE CT_ID = ?";
        $stmt10 = $con->prepare($sql10);
        $cstatus = "Paid";
        $stmt10 -> bind_param("si", $cstatus, $CT_ID);
        $result10 = $stmt10->execute();

        if(!is_null($pmcode)){
            $sql28 = "INSERT INTO promo_history (Promo_ID, Cus_ID) VALUES (?, ?)";
            $stmt28 = $con->prepare($sql28);
            $stmt28 -> bind_param("ii", $pmcode, $custid);
            $result28 = $stmt28->execute();
        }

        $sql6 = "INSERT INTO payment (Date, Time, O_ID, PM_Status, PM_Method) VALUES (?, ?, ?, ?, ?)";
        $stmt6 = $con->prepare($sql6);
        $stmt6 -> bind_param("ssiss", $date, $time, $oid, $pmstatus, $pmmethod);
        $result6 = $stmt6->execute();
        $pmid = mysqli_insert_id($con);
    
        if(!empty($pmid)){ 
            // Get transaction details 
            // $stmt->bind_result($payment_ref_id, $payer_id, $payer_name, $payer_email, $payer_country, $order_id, $transaction_id, $paid_amount, $paid_amount_currency, $payment_source, $payment_status, $created); 
            // $stmt->fetch(); 
            
            $statuslogo = '<i class="fa-solid fa-circle-check" style="color: #2ecc71;"></i>';
            $statusMsg = "<h4>The transaction was successful.</h4>";
            sendEmail($Email, "$firstname $lastname", $cartitems, $con, $ttl);
            // echo "Payment successful. Receipt sent.";
        }else{ 
            $statuslogo = '<i class="fa-solid fa-circle-exclamation" style="color: #d03333;"></i>';
            $statusMsg = "<h4>Transaction has been failed</h4>"; 
        } 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Responsive Bootstrap4 Shop Template, Created by Imran Hossain from https://imransdesign.com/">

    <!-- favicon -->
    <link rel="shortcut icon" type="image/png" href="assets/img/smile-black.png">
	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
	<!-- fontawesome -->
	<link rel="stylesheet" href="assets/css/all.min.css">
	<!-- bootstrap -->
	<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
	<!-- owl carousel -->
	<link rel="stylesheet" href="assets/css/owl.carousel.css">
	<!-- magnific popup -->
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<!-- animate css -->
	<link rel="stylesheet" href="assets/css/animate.css">
	<!-- mean menu css -->
	<link rel="stylesheet" href="assets/css/meanmenu.min.css">
	<!-- main style -->
	<link rel="stylesheet" href="assets/css/main.css">
	<!-- responsive -->
	<link rel="stylesheet" href="assets/css/responsive.css">
    <script src="https://kit.fontawesome.com/4dd5e87a71.js" crossorigin="anonymous"></script>

    <title>Payment Receipt - Cuppa Joy</title>
</head>
<body>
    <div class="donepay-container">
        <div class="donepay-box">
            <div class="header-logo">
                <div class="donelogo">
                    <?php echo $statuslogo; ?>
                </div>
                <div class="doneword">
                    <?php echo $statusMsg; ?>
                </div>
            </div>
            <div class="paymentdetails">
                <div class="odrdetail">
                    <?php
                        foreach($cartitems as $cartitem){
                            $sql2 = "SELECT * FROM product WHERE P_ID =" . $cartitem['P_ID'];
                            $result2 = mysqli_query($con, $sql2);
                            if($result2){
                                while($row = mysqli_fetch_array($result2)){
                                    $pname = $row['P_Name'];
                                    echo '<div class="prorow">';
                                    echo '<div class="qty">x'.$cartitem['Qty'].'</div>';
                                    echo '<div class="pro">';
                                    echo '<div class="pname">' . $pname .'</div>';
                                    echo '<div class="pcustom">';
                                    $sql3 = "SELECT * FROM Details WHERE c_item_id  = ? ORDER BY customize_id ASC";
                                    $stmt3 = $con->prepare($sql3);
                                    $stmt3->bind_param("i", $cartitem['CI_ID']);
                                    $stmt3->execute();
                                    $result3 = $stmt3->get_result();
                                    $customitems = [];
                                    if($result3->num_rows > 0){
                                        while($row3 = $result3->fetch_assoc()){
                                            $customitems[] = $row3;
                                        }
                                    }
                                    foreach($customitems as $customitem){
                                        $sql4 = "SELECT custom.*, cc.CC_Group, cc.compulsory_status FROM customization AS custom 
                                                INNER JOIN customize_category AS cc 
                                                ON custom.CC_ID = cc.CC_ID 
                                                WHERE custom.Custom_ID = " . $customitem['customize_id'];
                                        $result4 = mysqli_query($con, $sql4);
                                        if($result4){
                                            while($row2 = mysqli_fetch_array($result4)){
                                                $customname = $row2['Custom_Name'];
                                                echo '<span> - ' .$customname. '</span>';
                                            }
                                        }
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                            
                        }
                    ?>
                    <!-- <div class="prorow">
                        <div class="qty">1</div>
                        <div class="pro">
                            <div class="pname">Latte</div>
                            <div class="pcustom">
                                <span> - Ice level 0%</span>
                                <span> - Ice level 0%</span>
                                <span> - Ice level 0%</span>
                                <span> - Ice level 0%</span>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="otherdetail">
                    <div class="row">Total Payment: <span class="right showprice">RM <?php echo number_format($ttl, 2)?></span></div>
                    <div class="row">Name: <span class="right"><?php echo $name; ?></span></div>
                    <div class="row">Order ID: <span class="right"><?php echo $oid; ?></span></div>
                    <!-- <div></div> -->
                </div>
            </div>
            <div class="btngroup">
                <a href="history.php" id="backToMerchant" class="boxed-btn">Back To Merchants Site (<span id="timer" class="timer">10</span>)</a>
            </div>
        </div>
    </div>


    <!-- jquery -->
	<script src="assets/js/jquery-1.11.3.min.js"></script>
	<!-- bootstrap -->
	<script src="assets/bootstrap/js/bootstrap.min.js"></script>
	<!-- count down -->
	<script src="assets/js/jquery.countdown.js"></script>
	<!-- isotope -->
	<script src="assets/js/jquery.isotope-3.0.6.min.js"></script>
	<!-- waypoints -->
	<script src="assets/js/waypoints.js"></script>
	<!-- owl carousel -->
	<script src="assets/js/owl.carousel.min.js"></script>
	<!-- magnific popup -->
	<script src="assets/js/jquery.magnific-popup.min.js"></script>
	<!-- mean menu -->
	<script src="assets/js/jquery.meanmenu.min.js"></script>
	<!-- sticker js -->
	<script src="assets/js/sticker.js"></script>
	<!-- main js -->
	<script src="assets/js/main.js"></script>
	<!-- Stripe JavaScript library -->
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <script>
        var secondsRemaining = 10; // Set the initial countdown time in seconds

        // Function to update the timer display
        function updateTimer() {
           document.getElementById('timer').textContent = secondsRemaining;
           secondsRemaining--;

           if (secondsRemaining < 0) {
               clearInterval(timerInterval);
               window.location.href = 'history.php';
           }
        }

        // Start the countdown timer
        var timerInterval = setInterval(updateTimer, 1000); // Update timer every second
    </script>
</body>
</html>