<?php
// CLASSES REQUIRED
require 'r/PHPMailerAutoload.php';

// CREATE A NEW PHPMailer instance
$mail = new PHPMailer;

// TELL PHPMailer TO USE SMTP
$mail->isSMTP();

// ENABLE SMTP DEBUGGING
	// 0 = off (for production)
	// 1 = client messages
	// 2 = client and server messages
$mail->SMTPDebug = 2;

// ASK FOR HTML-FRIENDLY DEBUG OUTPUT
$mail->Debugoutput = 'html';

// SET THE HOST NAME FOR THE MAIL SERVER
$mail->Host = 'smtp.gmail.com';
	// use
	// $mail->Host = gethostbyname('smtp.gmail.com');
	// if your network does not support SMTP over IPv6

// SET THE SMTP PORT NUMBER (587 FOR AUTHENTICATED TLS, A.K.A RFC4409 SMTP SUBMISSION)
$mail->Port = 587;

// SET THE ENCRYPTION SYSTEM TO USE - SSL(DEPRECATED) OR TLS
$mail->SMTPSecure = 'tls';

// WHETHER TO USE SMTP AUTHENTICATION
$mail->SMTPAuth = true;

// USERNAME TO USE FOR SMTP AUTHENTICATION - USE FULL EMAIL ADDRESS FOR GMAIL
$mail->Username = "fauzitalha@gmail.com";

// PASSWORD FOR THE EMAIL
$mail->Password = "passwordkase7727";

// SET WHO THE MESSAGE IS TO BE SENT FROM
$mail->setFrom('ebrainjuggle@gmail.com', 'EBRAINJUGGLE');

// SET ALTERNATIVE REPLY
$mail->addReplyTo('ebrainjuggle_2@gmail.com', 'EBJ-SECONDARY');

// SET WHO THE MESSGE IS TO BE SENT TO
$mail->addAddress('fauzitalha@live.com', 'Lutaaya Fauzi');

// SUBJECT
$mail->Subject = 'Test Password Email';

// BODY
$mail->Body = 'Your new password is has been set to \n%6780DGE_EEE_JNMUIO';

// SENDING THE MESSAGE
if(!$mail->send()){
	echo "Miler Error: ". $mail->ErrorInfo;
}else{
	echo "Message Sent!";
}


?>