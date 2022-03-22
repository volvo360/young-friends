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
include_once($path."common/theme.php");

if (!isset($_SESSION['uid']))
{
	header('Location: '.$url);
}

function printMailSMS()
{
    global $link;
    
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
    
    $langStrings = getLangstrings();
    $printMailSMS = $langStrings['printMailSMS'];
	
	$table = "`".PREFIX."groups`";
	
	$table2 = "`".PREFIX."user`";

	$sql = "SELECT * FROM ".$table." WHERE visible > 0";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$groups[$row['tableKey']] = $row['note'];
	}
	
	$sql = "SELECT * FROM ".$table2." WHERE autoId = '".$_SESSION['uid']."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$member = $row['mobile'];
	}
	
	$members = getMembers();
	
	echo "<div class = \"row\">";
		
		echo "<div class = \"col-md-6\">";
			echo "<form id = \"formSendMessageSMS\">";
				echo "<h3>".$printMailSMS[1]."</h3>";

				if (count($groups) > 0)
				{
					echo "<h4>".$printMailSMS[4]."</h4>";
					echo "<p>";

						if (count($groups) > 1)
						{
							echo "<div class=\"form-check checkbox-slider--default\">";
								echo "<label>";
									echo "<input type=\"checkbox\" id = \"selectAllSMSGroup\"><span> ".$printMailSMS[3]."</span>";
								echo "</label>";
							echo "</div><br><br>";
						}

						foreach ($groups as $key => $value)
						{
							echo "<div class=\"form-check checkbox-slider--b\">";
								echo "<label>";
									echo "<input type=\"checkbox\" class = \"selectAllSMSGroup\" id = \"groupSMS[".$key."]\"><span> ".$value."</span>";
								echo "</label>";
							echo "</div>";
						}

					echo "</p>";
				}

				echo "<hr>";

				echo "<p>";

					if (count($members) > 0)
					{
						echo "<h4>".$printMailSMS[8]."</h4>";
						echo "<p>";

							if (count($members) > 1)
							{
								echo "<div class=\"form-check checkbox-slider--default\">";
									echo "<label>";
										echo "<input type=\"checkbox\" id = \"selectAllSMSMemeber\" checked><span> ".$printMailSMS[5]."</span>";
									echo "</label>";
								echo "</div><br><br>";
							}

							foreach ($members as $key => $value)
							{
								echo "<div class=\"form-check checkbox-slider--b\">";
									echo "<label>";
										echo "<input type=\"checkbox\" class = \"selectAllSMSMemeber\" id = \"memberSMS[".$key."]\" checked><span> ".$value."</span>";
									echo "</label>";
								echo "</div>";
							}

						echo "</p>";
					}
				echo "</p>";

				echo "<hr>";

				echo "<p>";
					echo "<div class=\"form-check checkbox-slider--b\">";
						echo "<label>";
							echo "<input type=\"checkbox\" id = \"ownSenderSMS\" checked><span> ".$printMailSMS[7]."</span>";
						echo "</label>";
					echo "</div><br>";

					echo "<div id = \"areaSMSheader\" style = \"display : none;\">";
						echo $printMailSMS[6]. " :<br>";
						echo "<input type = \"text\" id = \"headerSMS\" value = \"".$member."\" disabled><br><br>";
						echo "<input type = \"hidden\" id = \"masterHeaderSMS\" value = \"".$member."\" disabled>";
					echo "</div>";

					echo $printMailSMS[9]. " :<br>";
					echo "<textarea id = \"messageSMS\" class = \"form-control\" rows = \"5\" maxlength=\"160\">";
					echo "</textarea><br>";
					echo $printMailSMS[10]." "."<input type = \"text\" id = \"countSMS\" value = \"160\" size = \"3\" disabled autocomplete=\"off\">"." ".$printMailSMS[11];

				echo "</p>";

				echo "<p>";

					echo "<button class = \"btn btn-secondary\" id = \"copy2Mail\" disabled>";
						echo "->";
					echo "</button>";

				echo "</p>";
				echo "</form>";
			echo "</div>";

			echo "<div class = \"col-md-6\">";
	
				echo "<form id = \"formSendMessageMail\">";
				echo "<h3>".$printMailSMS[2]."</h3>";

				if (count($groups) > 0)
				{
					echo "<h4>".$printMailSMS[4]."</h4>";
					echo "<p>";

						if (count($groups) > 1)
						{
							echo "<div class=\"form-check checkbox-slider--default\">";
								echo "<label>";
									echo "<input type=\"checkbox\" id = \"selectAllMailGroup\"><span> ".$printMailSMS[3]."</span>";
								echo "</label>";
							echo "</div><br><br>";
						}

						foreach ($groups as $key => $value)
						{
							echo "<div class=\"form-check checkbox-slider--b\">";
								echo "<label>";
									echo "<input type=\"checkbox\" class = \"selectAllMailGroup\" id = \"mailGroup[".$key."]\"><span> ".$value."</span>";
								echo "</label>";
							echo "</div>";
						}

					echo "</p>";
				}

				echo "<hr>";

				echo "<p>";

					if (count($members) > 0)
					{
						echo "<h4>".$printMailSMS[8]."</h4>";
						echo "<p>";

							if (count($members) > 1)
							{
								echo "<div class=\"form-check checkbox-slider--default\">";
									echo "<label>";
										echo "<input type=\"checkbox\" id = \"selectAllMailMemeber\" checked><span> ".$printMailSMS[5]."</span>";
									echo "</label>";
								echo "</div><br><br>";
							}

							foreach ($members as $key => $value)
							{
								echo "<div class=\"form-check checkbox-slider--b\">";
									echo "<label>";
										echo "<input type=\"checkbox\" class = \"selectAllMailMemeber\" id = \"mailMember[".$key."]\" checked><span> ".$value."</span>";
									echo "</label>";
								echo "</div>";
							}

						echo "</p>";
					}
				echo "</p>";

				echo "<hr>";

				echo "<p>";

					echo "<div class=\"form-check checkbox-slider--b\">";
						echo "<label>";
							echo "<input type=\"checkbox\" id = \"memberSenderEmail\" checked><span> ".$printMailSMS[13]."</span>";
						echo "</label>";
					echo "</div><br>";

					echo $printMailSMS[12]. " :<br>";
					echo "<input type = \"text\" id = \"headerMail\" class = \"form-control\" value = \"YF : \"><br>";

					echo $printMailSMS[9]. " :<br>";
					echo "<textarea id = \"messageMail\" class = \"tinyMceArea form-control\" rows = \"10\">";
					echo "</textarea><br>";
					//echo $printMailSMS[10]." "."<input type = \"text\" id = \"countSMS\" value = \"160\" size = \"3\" disabled>"." ".$printMailSMS[11];

				echo "</p>";

				echo "<p>";

					echo "<button class = \"btn btn-secondary\" id = \"copy2SMS\" disabled>";
						echo "<-";
					echo "</button>";

				echo "</p>";
				echo "</form>";
			echo "</div>";

		echo "<p>";
			echo "<button class = \"btn btn-secondary form-control\" id = \"sendMessages\" data-form = \"formSendMessage\" disabled>";
				echo $printMailSMS[14];
			echo "</button>";
		echo "</p>";

		
	
	echo "</div>";
    
    
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $mail = $langStrings['mail'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$mail[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-heigth : 61vh; height :61vh; overflow : auto;\">";
                echo "<p>";
                    printMailSMS();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>