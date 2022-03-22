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

echo __LINE__." ".$path."<br>";

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/theme.php");
include_once($path."common/modal.php");

function printLoginUser()
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
    $printLoginUser = $langStrings['printLoginUser'];
    
    echo "<form id = \"loginForm\">";
    
        echo "<div class=\"row mb-3\">";
            echo "<img src = \"".$url."/img/orchids.jpg\" >";
        echo "</div>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"mail\" class=\"col-sm-2 col-form-label\">".$printLoginUser[2]."</label>";
            echo "<div class=\"col-sm-5\">";
                echo "<input type=\"text\" class=\"form-control\" id=\"mail\">";
            echo "</div>";
        echo "</div>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"password\" class=\"col-sm-2 col-form-label\">".$printLoginUser[3]."</label>";
            echo "<div class=\"col-sm-5\">";
                echo "<input type=\"password\" class=\"form-control\" id=\"password\">";
            echo "</div>";
        echo "</div>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"autoLogin\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
            echo "<div class=\"col-sm-10\">";
                echo "<div class=\"checkbox checkbox-slider--b\">";
                    echo "<label>";
                        echo "<input type=\"checkbox\" id = \"autoLogin\"><span>&nbsp;".$printLoginUser[4]."</span>";
                    echo "</label>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"loginUser\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
            echo "<div class=\"col-sm-5\">";
                echo "<button class=\"btn btn-secondary form-control\" id=\"loginUser\">".$printLoginUser[5]."</button>";
            echo "</div>";
        echo "</div><br>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"password\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
            echo "<div class=\"col-sm-5\">";
                echo "<button class=\"btn btn-info form-control\" id=\"resetPassword\">".$printLoginUser[6]."</button>";
            echo "</div>";
        echo "</div>";
    
    echo "</form>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $printLoginUser = $langStrings['printLoginUser'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$printLoginUser[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" data-aos=\"fade-up\">";
                echo "<p>";
                    printLoginUser();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    print_modal();
    printFooter();
	
}

?>