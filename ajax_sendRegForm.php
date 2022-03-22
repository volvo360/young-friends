<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";
echo "<meta name=\"robots\" content=\"noindex\" />";

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

if (isset($_POST['email']) && !isset($_POST['sureName']))
{
	$table = "`".PREFIX."user`";
	
	$sql = "SELECT * FROM ".$table." WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	if (mysqli_num_rows($result) == 0)
	{
		echo "<input type = \"hidden\" id = \"result\" value = \"ok\">";
	}
	else
	{
		echo "<input type = \"hidden\" id = \"result\" value = \"error\">";
	}
	
	return true;
}

$langStrings = getLangstrings();
$ajax_sendRegForm = $langStrings['ajax_sendRegForm'];

foreach ($_POST as $key => $value)
{
	$insert[$key] = mysqli_real_escape_string($link, $value);
}

$table = "`".PREFIX."user`";
$table2 = "`".PREFIX."temp_user`";

$sql = "SELECT MAX(uid)+1 as uid FROM ".$table."";
$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

$uid = mysqli_fetch_array($result, MYSQLI_ASSOC)['uid'];

$sql = "DESCRIBE ".$table.";";
$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$db[$row['Field']] = $row['Field'];
}

$insert['mobile'] = preg_replace("/[^\+0-9]/", "", $insert['mobile']);
$insert['phone'] = preg_replace("/[^\+0-9]/", "", $insert['phone']);

if (!filter_var($insert['email'], FILTER_VALIDATE_EMAIL))
{
	echo __LINE__." Hmmmmmm<br>";
	return false;
}

if (strlen(trim($insert['password'])) < 6)
{
	echo __LINE__." Hmmmmmm<br>";
	return false;
}

if (trim($insert['password']) !== trim($insert['repPassword']))
{
	echo __LINE__." Hmmmmmm<br>";
	return false;
}

if (strlen($insert['mobile']) !== 10 && strlen($insert['mobile']) !== 13 && strlen($insert['mobile']) !== 14)
{
	echo __LINE__." Hmmmmmm<br>";
	return false;
}

$insertData['uid'] = $uid;

$insert['password'] = password_hash($insert['password']);

foreach ($insert as $key => $value)
{
	if (array_key_exists($key, $db))
	{
		$insertData[$key] = $value;
	}
}

$fields = implode(", ", array_keys($insertData));

$data = implode("', '", $insertData);

$sql = "INSERT INTO ".$table2." (".$fields.") VALUES ('".$data."')";
$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

$key = checkTable($table2);

$confirmUrl = $url."verifyEmail.php?passkey=".$key;

$emailTemplate = renderEmailTemplate();

$message = str_replace("~~confirmUrl~~", "<a href = \"".$confirmUrl."\">".$confirmUrl."</a>", $ajax_sendRegForm[1]);

$emailTemplate = str_replace("~~reciverName~~", "Hej ".ucfirst($insert['firstName'])." ".ucfirst(substr($insert['sureName'], 0,1)), $emailTemplate);
$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 100), $emailTemplate);

$regButton = "<tr>";
	$regButton .= "<td style=\"padding: 0 20px 20px;\">";
		$regButton .= "<table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">";
			$regButton .= "<tr>";
				$regButton .= "<td class=\"button-td button-td-primary\" style=\"border-radius: 4px; background: #222222;\">";
					$regButton .= "<a class=\"button-a button-a-primary\" href=\"".$confirmUrl."\" style=\"background: #222222; border: 1px solid #000000; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">".$ajax_sendRegForm[2]."</a>";
				$regButton .= "</td>";
			$regButton .= "</tr>";
		$regButton .= "</table>";
	$regButton .= "</td>";
$regButton .= "</tr>";

$emailTemplate = str_replace("~~button~~", $regButton, $emailTemplate);

$htmlMessage = str_replace("~~messageMail~~", $message, $emailTemplate);

$to = $insert['email'];
$subject = $ajax_sendRegForm[3];
$from = 'Young Friends<info@young-friends.org>';
 
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8;' . "\r\n";
 
// Create email headers
$headers .= 'From: '.$from."\r\n".
    'Reply-To: '.$from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
 
// Sending email
if(mail($to, $subject, $htmlMessage, $headers)){
    echo 'ok';
} else{
    echo 'error';
}