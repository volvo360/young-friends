<?php

session_start();

error_reporting(E_ERROR);
ini_set("display_errors", 1);

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";

echo "<meta name=\"robots\" content=\"noindex\" />";

if ($_SERVER['SERVER_NAME'] === "server01")
{
    $url = "//server01/flexshare/yf/";
    $path = "/var/flexshare/shares/yf/";
}

elseif ($_SERVER['SERVER_NAME'] === "localhost")
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

global $link;

if (isset ($_POST['mail']))
{	
	$user = mysqli_real_escape_string($link, $_POST['mail']);
	$pass = mysqli_real_escape_string($link, $_POST['password']);
	
	if ($_POST['autoLogin'])
	{
		setcookie ("YF_user", $user,time()+60*60*24*30, '/');
		setcookie ("YF_pass", $pass,time()+60*60*24*30, '/');	
	}
	
	$table = "`".PREFIX."user`";
	$table2 = "`".PREFIX."mission`";
	
	$sql= "SELECT *, ".$table.".uid as m_uid FROM ".$table." LEFT OUTER JOIN ".$table2." ON ".$table2.".uid = ".$table.".uid WHERE (username = '$user' OR email = '$user') ORDER BY level DESC";
    
	//echo $sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__." ".$sql.": ".mysqli_error ($link));
	
	if (mysqli_num_rows($result) > 0)
	{
        $row = mysqli_fetch_array($result);
        
        if ($row['password'] == md5($pass))
        {
			$passwordHash = password_hash($pass, PASSWORD_BCRYPT );
			
            $sql = "UPDATE ".$table." SET password = '".$passwordHash."' WHERE uid = '".$row['uid']."'";
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__." ".$sql.": ".mysqli_error ($link));
        }
        else if (password_verify("'".$pass."'", $row['password']))
        {
            //Do nothing
		}
		else if (password_verify($pass, $row['password']))
        {
			//Do nothing
        }
        else
        {
            echo "Felaktigt lösenord";
            return false;
        }
        
        if ($row['betalt'] >= date("Y-m-d"))
		{
			$_SESSION['uid'] = $row['m_uid'];
		}
		elseif ($row['testmember'] >= date ("Y-m-d"))
		{
			$_SESSION['uid'] = $row['m_uid'];
			$_SESSION['testmember'] = $row['m_uid'];
		}
		elseif ($row['testmember'] != "0000-00-00 00:00:00")
		{
			if  ($row['testmember'] <= date("Y-m-d H:i:s"))
			{
				session_destroy();
				echo "Ditt provmedlemskap har slutat att gälla!\nDu får överväga om du vill bli medlem.";
				//header("Location:program.php");
				/*echo "<script language=\"JavaScript\">\n"; 
				echo "alert(\"Ditt provmedlemskap har slutat att gälla!\nDu får överväga om du vill bli medlem.\");"; 
				
				echo "</script>";*/
				
				//exit();
			}
		}
		elseif ($row['testmember'] == "0000-00-00 00:00:00")
		{
			echo "Ditt provmedlemskap har ej att börjat gälla än. Du får avvakta tills någon aktiverar det i systemet.";	
		}
		else if ($row['betalt'] < date ("Y-m-d"))
		{
			echo "Du har ej avlagt medlemsavgiften för detta år!!!";

		}
		else
		{
			echo "Du har ej avlagt medlemsavgiften för detta år!!!";
			/* 
			echo "<script language=\"JavaScript\">\n"; 
			echo "alert(\"Du har ej avlagt medlemsavgiften för detta år!!!\");"; 
			echo "window.location = \"program.php\"";
			echo "</script>";
			/*setcookie ("YF[user]", "$user",0);
			setcookie ("YF[pass]", "$pass",0);
			exit();	*/
		}
		
		if ((int)$row['level'] >= 100)
        {
            $_SESSION['siteAdmin'] = 1;	
        }

        if ((int)$row['level'] >= 1)
        {
            $_SESSION['border'] = 1;
        }
		//echo "Sessions id är lika med ". $_SESSION['uid'];
		/*if ($_POST['cookie'])
		{
			echo __LINE__." ".$pass."<br>";
			
			setcookie ("YF_user", "$user",time()+60*60*24*30);
			setcookie ("YF_pass", "$pass",time()+60*60*24*30);
		}*/
		
		//För att uppdatera när vi loggade in senast
		$currentdate = date("Y-m-d H:i:s");
		$uid = $row['m_uid'];
		
        if (!empty($_SESSION['uid']))
		{
			$sql= "UPDATE ".PREFIX."user SET lastlogindate = '$currentdate' WHERE uid= ".$_SESSION['uid'];
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__." ".$sql.": ".mysqli_error ($link));
		}
		else if (!empty($_SESSION['testmember']))
		{
			$sql= "UPDATE ".PREFIX."user SET lastlogindate = '$currentdate' WHERE uid= ".$_SESSION['testmember'];
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__." ".$sql.": ".mysqli_error ($link));
		}
		
		if (!empty($_SESSION['uid']) || (!empty($_SESSION['testmember'])))
		{
			//$result= mysqli_query($link,$sql) or die ("Error: " .$sql ." ".mysqli_error ());
			$sql= "SELECT * FROM ".PREFIX."mission WHERE uid = '$uid'  AND level >= 1 ORDER BY level DESC";
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__." ".$sql.": ".mysqli_error ($link));
			
			if (($row=mysqli_fetch_array($result)))
			{
                if ((int)$row['level'] >= 100)
                {
                    $_SESSION['siteAdmin'] = 1;	
                }
				
                if ((int)$row['level'] >= 1)
                {
                    $_SESSION['border'] = 1;
                }
                //$_SESSION['testmember'] = $_SESSION['uid'];
			}
			
			/*if ($_POST['cookie'])
			{
				echo "OK";
			}
			else
			{
				echo "OK";
				if (!empty($_GET[link]))
				{
					
					$tem = "Location:" .$_GET[ret];
					?>
						<script language=javascript> 
						alert("Du vill med hjälp av automatik sändas till " +<?php echo $tem; ?> +"!!");
						//window.location = "../yf/program.php"
	
					</script>
					<?php 
					
					unset ($_SESSION['return']);
					header("$tem");					
				}
				else
				{
					//header("Location:program.php");	
				}
			}*/
			session_write_close();
			
			if (basename(__FILE__) == basename($_SERVER['PHP_SELF']))
			{
				echo "OK";
			}
		}
	}
	else
	{
		echo "<p class='content'>Felaktigt användarenamn eller lösenord! Tryck <a href='forgotenpassword.php'>här</a> om du har glömt ditt lösenord.<p>";
		
	}
}
else
{
	//header("Location:index.php");
}

?>