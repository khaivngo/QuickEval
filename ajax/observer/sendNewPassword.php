<?php

/**
 * Updates password and notifies user with new password.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$newPass = $_POST['string'];

$to = $_POST['email'];
$subject = 'Your new password for QuickEval';
$message = 'Greetings,

This is an automatic generated mail for a new password from QuickEval:
New password: '. $newPass .'

Please wait a few minutes before refreshing and trying your new password.
We encourage you to login and change to your own secure password and delete this mail.
If you have not requested a new password please contact us.

Best,
QuickEval Team';
$headers = 'From: QuickEval team' . "\r\n" .
    'Reply-To: quickevlano@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);

?>