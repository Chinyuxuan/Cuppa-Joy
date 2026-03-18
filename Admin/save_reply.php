<?php
session_start();
include("../user/db_connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $messageId = $_POST['messageId'];
    $adminId = $_POST['adminId'];
    $replyMessage = $_POST['replyMessage'];

    $sql = "UPDATE contact_us SET Reply_Message = ?, Contact_Status = 'Replied', Add_By = ? WHERE Co_ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ssi', $replyMessage, $adminId, $messageId);

    if ($stmt->execute()) {
        $customerInfoQuery = "SELECT Email, Message FROM contact_us WHERE Co_ID = ?";
        $stmtCustomer = $con->prepare($customerInfoQuery);
        $stmtCustomer->bind_param('i', $messageId);
        $stmtCustomer->execute();
        $stmtCustomer->store_result();

        if ($stmtCustomer->num_rows > 0) {
            $stmtCustomer->bind_result($customerEmail, $originalMessage);
            $stmtCustomer->fetch();

            if (sendEmail($customerEmail, $replyMessage, $originalMessage)) {
                $_SESSION['message'] = "Reply sent successfully.";
                echo "<script>alert('The reply message sent to the customer email.');
                      window.location.replace('message.php');
                      </script>";
                exit;
            } else {
                $_SESSION['error'] = "Failed to send email. Please try again.";
            }
        } else {
            $_SESSION['error'] = "Customer information not found.";
        }

        $stmtCustomer->close();
    } else {
        $_SESSION['error'] = "Failed to send reply. Please try again.";
    }

    $stmt->close();
    header("Location: message.php");
    exit;
}

function sendEmail($customerEmail, $replyMessage, $originalMessage) {
    require("PHPMailer/PHPMailer.php"); 
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");

    $originalMessage = nl2br(htmlspecialchars($originalMessage));
    $replyMessage = nl2br(htmlspecialchars($replyMessage));


    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP(); 
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true; 
        $mail->Username   = 'cuppajoy88@gmail.com'; 
        $mail->Password   = 'imjo jrya kyki hgjt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587; 

        $mail->setFrom('cuppajoy88@gmail.com', 'Tok');
        $mail->addAddress($customerEmail);
        $IMAGE = '../user/assets/img/full-black.png';
        $mail->AddEmbeddedImage($IMAGE, 'website_logo');

        $mail->isHTML(true);
        $mail->Subject = 'Reply from Cuppa Joy';

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; font-size: 14px; width: 100%; color: #000;'>
                <div style='display: flex; flex-direction: row; justify-content: space-between; width: 100%;'>
                    <div style='width: 40%; color: #000;'>
                        <img src='cid:website_logo' alt='CuppaJoy' style='width: 200px;'>
                        <h2>Reply from Cuppa Joy</h2>
                    </div>
                    <div style='width: 60%; float: right; color: #000;'>
                        <p>CuppaJoy</p>
                        <p>123, Jalan Ayer Keroh Lama</p>
                        <p>Kampung Baru Ayer Keroh, 75450 Ayer Keroh,</p>
                        <p>Melaka, Malaysia</p>
                    </div>
                </div>
                <p>You received a reply for the message below:</p>
                <p><strong>Original Message:</strong></p>
                <p>$originalMessage</p>
                <p>Here is the reply from Cuppa Joy Cafe:</p>
                <p><strong>Reply:</strong></p>
                <p>$replyMessage</p>
                <p>We hope this reply addresses your question. Please feel free to visit our website for more information.</p>
                <p>Thank you,</p>
                <p>Cuppa Joy Team</p>
            </div>
        ";

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
        return false;
    }
}
?>
