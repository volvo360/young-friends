<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";
echo "<meta name=\"robots\" content=\"noindex\" />";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

    // Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6LcjdZ0UAAAAAEO95zzQ_mANHAHVrvMq9dePGYyp';
    $recaptcha_response = $_POST['recaptcha_response'];

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->score >= 0.5) {
        // Verified - send email
    } else {
        // Not verified - show form error
        return false;
    }

} 

if ($_SERVER['SERVER_NAME'] === "server01")
    {
        $url = "//server01/flexshare/yf/";
        $path = "/var/flexshare/shares/yf/";
    }
    else if ($_SERVER['SERVER_NAME'] === "localhost")
    {
        $url = "//localhost/yf/";
        $path = $_SERVER['DOCUMENT_ROOT']."/yf/";
    }
    else
    {
        $url = "//www.young-friends.org/";
        $path = $_SERVER['DOCUMENT_ROOT']."/";
    }

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/emailTemplate.php");
include_once($path."common/send_mail.php");

global $link;

$emailTemplate = renderEmailTemplate();
				
$langStrings = getLangstrings();
$ajax_sendContactForm = $langStrings['ajax_sendContactForm'];

$mail = mysqli_real_escape_string($link, $_POST['email']);

$mailHeader = $ajax_sendContactForm[1];

$message = $ajax_sendContactForm[2];

$name = mysqli_real_escape_string($link, $_POST['name']);

$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

$headerMail =  "MIME-Version: 1.0" . "\r\n";
$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);

$mailHeader = $ajax_sendContactForm[3];

$message = $ajax_sendContactForm[4];
$mail = mysqli_real_escape_string($link, $_POST['email']);
$name2 = $ajax_sendContactForm[5];

$message = str_replace("~~senderName~~", $name, $message);
$message = str_replace("~~contactHeader~~", mysqli_real_escape_string($link, $_POST['subject']), $message);
$message = str_replace("~~contactMessage~~", mysqli_real_escape_string($link, $_POST['message']), $message);

$name = mysqli_real_escape_string($link, $_POST['name']);

$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name2."!", $emailTemplate);

$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

$headerMail =  "MIME-Version: 1.0" . "\r\n";
$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
$headerMail .= 'Reply-To: '.$name . "<".$mail.">\r\n";
$headerMail .= 'X-Mailer: PHP/' . phpversion();
$mail = "info@young-friends.org";
queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);

/*include_once($path."tasks.php");
start_task(false);*/