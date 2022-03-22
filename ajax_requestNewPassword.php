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
include_once($path."common/send_mail.php");
include_once($path."common/emailTemplate.php");

function requestNewPassword()
{
	global $link;
	
	$langStrings = getLangstrings();
	$requestNewPassword = $langStrings['requestNewPassword'];
	
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
	
	$table = "`".PREFIX."user`";
	$table10 = "`".PREFIX."reset_password`";
	
	$mail = $_POST['requestPassword'];
	
	if (filter_var($mail, FILTER_VALIDATE_EMAIL))
	{
		$mail = mysqli_real_escape_string($link, $mail);
		
		$sql = "SELECT * FROM ".$table." WHERE email = '".$mail."'";
		$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

		if (mysqli_num_rows($result) > 0)
		{
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$user = $row;
			}
			
			$header = $requestNewPassword[1];
			
			$message = $requestNewPassword[2];
			
			$emailTemplate = renderEmailTemplate();
			
			$reciver = ucfirst($user['firstName'])." ". ucfirst(substr($user['sureName'],0,1));
			
			$emailTemplate = str_replace("~~reciverName~~", "Hej ".$reciver."!", $emailTemplate);
			$emailTemplate = str_replace("~~previewMessage~~", $message, $emailTemplate);
			$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);
			
			$sql = "INSERT INTO ".$table10." (uid, validTo) VALUES ('".$user['uid']."', INTERVAL 2 DAY + NOW())";
			//echo __LINE__." ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			$key = checkTable($table10);
			
			$resetUrl = $url."setNewPassword.php?tableKey=".$key;
			
			$emailTemplate = str_replace("~~resetLink~~", "<a href = \"".$resetUrl."\">".$resetUrl."</a>", $emailTemplate);
			
			$reqButton = "<tr>";
				$reqButton .= "<td style=\"padding: 0 20px 20px;\">";
					$reqButton .= "<table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">";
						$reqButton .= "<tr>";
							$reqButton .= "<td class=\"button-td button-td-primary\" style=\"border-radius: 4px; background: #222222;\">";
								$reqButton .= "<a class=\"button-a button-a-primary\" href=\"".$resetUrl."\" style=\"background: #222222; border: 1px solid #000000; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">".$requestNewPassword[3]."</a>";
							$reqButton .= "</td>";
						$reqButton .= "</tr>";
					$reqButton .= "</table>";
				$reqButton .= "</td>";
			$reqButton .= "</tr>";

			$emailTemplate = str_replace("~~button~~", $reqButton, $emailTemplate);

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

			$to = $user['email'];
			
			// Sending email
			if(mail($to, $header, $htmlMessage, $headers)){
				echo 'ok';
			} else{
				echo 'error';
			}
			
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME']))
{
	$langStrings = getLangstrings();
	$ajax_requestNewPassword = $langStrings['ajax_requestNewPassword'];
	
	if (isset($_POST['requestPassword']))
	{
		requestNewPassword();
		
		echo "<div id = \"modalHeader\">";
			echo $ajax_requestNewPassword[1];
		echo "</div>";

		echo "<div id = \"modalBody\">";
			echo $ajax_requestNewPassword[5];
		echo "</div>";

		echo "<div id = \"modalFooter\">";
			echo "<button type=\"button\" class=\"btn btn-secondary closeModal\" data-dismiss=\"modal\">".$ajax_requestNewPassword[4]."</button>";
		echo "</div>";
		
		return false;	
	}
	
	echo "<div id = \"modalHeader\">";
		echo $ajax_requestNewPassword[1];
	echo "</div>";
	
	echo "<div id = \"modalBody\">";
		echo $ajax_requestNewPassword[2]."<input type = \"text\" style = \"width : 50%\" id = \"requestPassword\"><br><br>";
		echo "<div class=\"d-grid gap-2\">";
			echo "<button type=\"button\" class=\"btn btn-secondary sendPasswordRequest\">".$ajax_requestNewPassword[3]."</button>";
	  	echo "</div>";
		
	echo "</div>";
	
	echo "<div id = \"modalFooter\">";
		echo "<button type=\"button\" class=\"btn btn-secondary closeModal\" data-dismiss=\"modal\">".$ajax_requestNewPassword[4]."</button>";
	echo "</div>";
	//renderActityBody();
}
?>