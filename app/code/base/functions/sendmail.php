<?php
//Load PHPMailer
//The below path is relative to the page where this file is being inlcuded
require_once '../base/lib/PHPMailerAutoload.php';

function send_mail($s_name,$s_email,$r_name,$r_email,$subject,$content,$simg) {

	$thisdomain = strpos($r_email,"@prevrod.com");

	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               			// Enable verbose debug output
	$mail->isSMTP();                                      			// Set mailer to use SMTP
	$mail->Host = 'mail.prevrod.com';								// Specify main and backup SMTP servers				
	$mail->SMTPAuth = true;                               			// Enable SMTP authentication
	$mail->Username = 'no-reply@prevrod.com';        				// SMTP username
	$mail->Password = '12345Noreply';                     			// SMTP password
	$mail->SMTPSecure = 'ssl';                          			// Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                                  			// TCP port to connect to

	$mail->setFrom($s_email, $s_name);
	$mail->addAddress($r_email, $r_name);     						// Add a recipient
	//$mail->addAddress('ellen@example.com');              			// Name is optional
	if ($thisdomain === false)	{
		$mail->addReplyTo('e-rod@prevrod.com', 'Suporte E-Rod');
	}
	//$mail->addCC('cc@example.com');
	//$mail->addBCC('bcc@example.com');
	$mail->AddEmbeddedImage($simg, "logo");							// Add Embedded Image
	$mail->isHTML(true); 											// Set email format to HTML
	$mail->CharSet = 'UTF-8';                               		

	$mail->Subject =  $subject;
	$mail->Body    =  $content;
	//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$mail->send()) {
	    $status = false;
	} else {
	    $status = true;
	}

	return $status;
}
?>