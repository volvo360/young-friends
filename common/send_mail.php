<?php
session_start();

define('max_header', 50);

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

function queMail($sender = null, $to = null, $header = null, $rawMessage = null, $message = null, $mail = null, $sms = null, $header_mail = null)
{
	if (empty($sender) && (int)$sender !== 0|| empty($to) || empty($header)|| empty($rawMessage) || empty($message) || empty($mail) && empty($sms) || empty($header_mail))
	{
		echo "Error : ".basename(__FILE__)."<br>";
	
		if (empty($sender) && (int)$sender !== 0)
		{
			var_dump($sender);
			echo "<br>";

			echo __LINE__." "."Error : ".basename(__FILE__)."<br>";
		}
		if (empty($to))
		{
			echo __LINE__." ". "Error : ".basename(__FILE__)."<br>";
		}

		if (empty($header))
		{
			echo __LINE__." ". "Error : ".basename(__FILE__)."<br>";
		}

		if (empty($rawMessage))
		{
			echo __LINE__." ". "Error : ".basename(__FILE__)."<br>";
		}

		if (empty($message))
		{
			echo __LINE__." ". "Error : ".basename(__FILE__)."<br>";
		}

		if (empty($header_mail))
		{
			echo __LINE__." ". "Error : ".basename(__FILE__)."<br>";
		}

		if (empty($mail) && empty($sms))
		{
			echo __LINE__." ". "Error : ".basename(__FILE__)."<br>";
		}
		return false;
	}
	
	global $link;
	
	$message = str_replace("~~button~~",'', $message);
	
	$table = "`".PREFIX."log`";
	
	$sql = "INSERT INTO ".$table." (`user`, `ip`, `date`, `to`, `header`, `rawMessage`, `message`, `sms`, `email`, `header_mail`) VALUES ('".$sender."', '".$_SERVER['REMOTE_ADDR']."', NOW(), '".$to."', '".$header."', '".mysqli_real_escape_string($link, $rawMessage)."', '".mysqli_real_escape_string($link, $message)."', '".$sms."', '".$mail."', '".$header_mail."')";
	//echo __LINE__." ".$sql."<br>";
	echo "<br>";
	$result = mysqli_query($link, $sql) or die (__LINE__." "."Error: ".mysqli_error ($link));
	
	//start_task(false);
}
?>