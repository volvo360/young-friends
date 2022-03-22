<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";

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
include_once($path."common/theme.php");
include_once($path."common/send_mail.php");
include_once($path."common/emailTemplate.php");

function printVerifyEmail()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printVerifyEmail = $langStrings['printVerifyEmail'];
    
	$tableKey = mysqli_real_escape_string($link, $_GET['passkey']);
	
	if (empty($tableKey))
	{
		echo "<b>".$printVerifyEmail[1]."</b>";
		return false;
	}
	
	$table = "`".PREFIX."temp_user`";
	$table2 = "`".PREFIX."user`";
	
	$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$tableKey."'";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) == 0)
	{
		echo "<b>".$printVerifyEmail[2]."</b>";
	}
	else
	{
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		unset($row['autoId'], $row['tableKey']);
		
		foreach ($row as $key => $value)
		{
			$valueInsert[] = "'".$value."'";
		}
		
		$fields = array_keys($row);
		
		$sql = "INSERT INTO ".$table2." (".implode(", ", $fields).") VALUES (".implode(", ", $valueInsert).")";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		checkTable($table2);
		
		$sql = "DELETE FROM ".$table." WHERE tableKey = '".$tableKey."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		echo "<b>".$printVerifyEmail[3]."</b>";
		
		$sql = "SELECT * FROM ".$table2." WHERE uid = 0";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		$row2 = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$emailTemplate = renderEmailTemplate();
		
		$message = str_replace("~~newUser~~", "\"".$row['firstName']." ".$row['sureName']."\"", $printVerifyEmail[5]);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".ucfirst($row2['firstName'])." ".ucfirst($row2['sureName'])."!", $emailTemplate);
		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 100), $emailTemplate);
		$emailTemplate = str_replace("~~button~~", '', $emailTemplate);
		
		$htmlMessage = str_replace("~~messageMail~~", $message, $emailTemplate);
		
		$header_Mail =  "MIME-Version: 1.0" . "\r\n";
		$header_Mail .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$header_Mail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		$header_Mail .= 'Reply-To: '.$row['email'] . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	
		$mail = $row2['email'];
		
		$headerMail = $printVerifyEmail[4];
		
		//echo $htmlMessage;
		
		queMail(0, $mail, $headerMail, $message, $htmlMessage, 1, null, $header_Mail);
		
		echo "<br><br>".$printVerifyEmail[6];
		
		
	}
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $verifyEmail = $langStrings['verifyEmail'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$verifyEmail[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" data-aos=\"fade-up\" style = \"height :59vh; max-height :59vh; overflow:auto;\">";
                echo "<p>";
                    printVerifyEmail();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>
<br>
