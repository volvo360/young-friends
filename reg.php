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

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/theme.php");

function printReg()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printReg = $langStrings['printReg'];
    
    echo "<h2>".$printReg[1]."</h2>";
	
	echo "<p>".$printReg[2]."</p><br>";
	
	$printEdit_profile = $langStrings['printEdit_profile'];
    
	$replaceTable = getReplaceTable();
	
    echo "<p><form id = \"createAccount\">";
    
        /*echo "<div class=\"row mb-3\">";
            echo "<img src = \"".$url."/img/orchids.jpg\" >";
        echo "</div>";*/
	
	$table = "`".PREFIX."user`";
	
	echo "<div class=\"row mb-3\">";
            echo "<label for=\"firstName\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[1]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"firstName\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['firstName']."\" required>";
            echo "</div>";
        
			echo "<label for=\"sureName\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[2]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"sureName\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['sureName']."\" required>";
            echo "</div>";
        echo "</div>";
		
		 echo "<div class=\"row mb-3\">";
            echo "<label for=\"address\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[5]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"address\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['address']."\" required>";
            echo "</div>";
        
			echo "<label for=\"zip\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[6]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"zip\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['zip']."\" required>";
            echo "</div>";
        echo "</div>";
		
		echo "<div class=\"row mb-3\">";
            echo "<label for=\"city\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[7]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"city\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['city']."\" required>";
            echo "</div>";
        
			echo "<label for=\"email\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[8]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"email\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['email']."\" required>";
            echo "</div>";
        echo "</div>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"phone\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[3]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"phone\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['phone']."\">";
            echo "</div>";
        
			echo "<label for=\"mobile\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[4]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"mobile\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" value = \"".$row['mobile']."\" required>";
            echo "</div>";
        echo "</div>";
		
		echo "<div class=\"row mb-3\">";
            echo "<label for=\"password\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[9]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"password\" class=\"form-control\" id=\"password\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" required>";
            echo "</div>";
        
			echo "<label for=\"repPassword\" class=\"col-sm-2 col-form-label\">".$printEdit_profile[10]."*</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<input type=\"password\" class=\"form-control\" id=\"repPassword\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\" required>";
            echo "</div>";
        echo "</div>";
		
		echo "<div class=\"row mb-3\">";
            echo "<label for=\"deleteAccount\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
            echo "<div class=\"col-sm-10\">";
				echo "<button type=\"submit\" class=\"btn btn-secondary form-control createAccount\" id=\"createAccount\"  data-replace_table = \"".$replaceTable[PREFIX."user"]."\">".$printReg[3]."</button>";
			echo "</div>";
        echo "</div>";
	echo "</form></p>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $reg = $langStrings['reg'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$reg[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height :61vh; height :61vh; overflow:auto;\">";
                echo "<p>";
                    printReg();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
	echo "<script src=\"https://www.google.com/recaptcha/api.js?render=6LcjdZ0UAAAAACnmmP5s65vXAVUc7KLJxSaDi4lF\"></script>";
	
    printFooter();
	
	echo "<input type=\"hidden\" name=\"recaptcha_response\" id=\"recaptchaResponse\">";
}

?>