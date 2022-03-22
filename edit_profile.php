<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";

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

if (isset($_COOKIE["YF"]["user"]) && !isset($_SESSION['uid'])) 
{
	$_POST['mail'] = $_COOKIE["YF"]["user"];
	$_POST['password'] = $_COOKIE["YF"]["pass"];

	$user = mysqli_real_escape_string($link, $_POST['mail']);
	$pass = mysqli_real_escape_string($link, $_POST['password']);

	include_once($path."ajaxLogin.php");
}

else if (isset($_COOKIE["YF_user"]) && !isset($_SESSION['uid'])) 
{
	$_POST['mail'] = $_COOKIE["YF_user"];
	$_POST['password'] = $_COOKIE["YF_pass"];

	$user = mysqli_real_escape_string($link, $_POST['mail']);
	$pass = mysqli_real_escape_string($link, $_POST['password']);

	include_once($path."ajaxLogin.php");
}

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/theme.php");

function printEdit_profile()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printEdit_profile = $langStrings['printEdit_profile'];
    
	$replaceTable = getReplaceTable();
	
    echo "<form id = \"loginForm\">";
    
        /*echo "<div class=\"row mb-3\">";
            echo "<img src = \"".$url."/img/orchids.jpg\" >";
        echo "</div>";*/
	
	$table = "`".PREFIX."user`";
	
	if (!empty($_POST['userTableKey']))
	{
		$sql= "SELECT * FROM ".$table." WHERE tableKey =  '".mysqli_real_escape_string($link, $_POST['userTableKey'])."'";
	}
	else
	{
		$sql= "SELECT * FROM ".$table." WHERE uid =  '".$_SESSION['uid']."'";
	}
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC))
	{
	    echo "<div class=\"row mb-3\">";
            echo "<label for=\"firstName[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[1]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"firstName[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['firstName']."\">";
            echo "</div>";
        
			echo "<label for=\"sureName[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[2]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"sureName[".$row['tableKey']."]\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['sureName']."\">";
            echo "</div>";
        echo "</div>";
		
		 echo "<div class=\"row mb-3\">";
            echo "<label for=\"address[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[5]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"address[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['address']."\">";
            echo "</div>";
        
			echo "<label for=\"zip[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[6]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"zip[".$row['tableKey']."]\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['zip']."\">";
            echo "</div>";
        echo "</div>";
		
		echo "<div class=\"row mb-3\">";
            echo "<label for=\"city[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[7]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"city[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['city']."\">";
            echo "</div>";
        
			echo "<label for=\"email[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[8]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"email[".$row['tableKey']."]\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['email']."\">";
            echo "</div>";
        echo "</div>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"phone[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[3]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"phone[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['phone']."\">";
            echo "</div>";
        
			echo "<label for=\"mobile[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[4]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control syncData\" id=\"mobile[".$row['tableKey']."]\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['mobile']."\">";
            echo "</div>";
        echo "</div>";
		
		echo "<div class=\"row mb-3\">";
            echo "<label for=\"password[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[9]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"password\" class=\"form-control syncData\" id=\"password[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".""."\">";
            echo "</div>";
        
			echo "<label for=\"repPassword[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[10]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"password\" class=\"form-control syncData\" id=\"repPassword[".$row['tableKey']."]\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".""."\">";
            echo "</div>";
        echo "</div>";
		
		echo "<div class=\"row mb-3\">";
            echo "<label for=\"deleteAccount\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
            echo "<div class=\"col-sm-10\">";
				if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
				{
					echo "<button type=\"button\" class=\"btn btn-warning form-control deleteAccountPersonal\" id=\"deleteAccount[".$row['tableKey']."]\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\">".$printEdit_profile[11]."</button>";
				}
				else
				{
					$members = getMembers(false);
					
					if ($members[$row['tableKey']] !== $_SESSION['uid'])
					{					
						echo "<button type=\"button\" class=\"btn btn-warning form-control deleteAccount\" id=\"deleteAccount[".$row['tableKey']."]\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\">".$printEdit_profile[11]."</button>";
					}
					else
					{
						echo $printEdit_profile[12];
					}
				}
                
            echo "</div>";
        
        echo "</div>";
	}
    echo "</form>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $edit_profile = $langStrings['edit_profile'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$edit_profile[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" data-aos=\"fade-up\" style = \"height :61vh; max-height :61vh; overflow:auto;\">";
                echo "<p>";
                    printEdit_profile();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}
?>