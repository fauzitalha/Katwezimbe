<?php
/**
 * This example shows sending a message using PHP's mail() function.
 */

require 'r/class.phpmailer.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Set who the message is to be sent from
$mail->setFrom('fauzitalha@gmail.com', 'Lutaaya Fauzi');

//Set an alternative reply-to address
//$mail->addReplyTo('replyto@example.com', 'First Last');

//Set who the message is to be sent to
$mail->addAddress('fauzitalha@live.com', 'Lex Luther');


//Set the subject line
$mail->Subject = 'The test';

// the body
$mail->Body = 'Username: lutaaya \n Password: 3464$$473';


//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}

?>