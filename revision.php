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

if (empty($_GET['account']))
{
	header('Location: '.$url);
}

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/theme.php");
include_once($path."common/modal.php");

function printRevision()
{
	global $link;
	
	include_once("./estraro/showAjax.php");
	
	$_POST['id'] = mysqli_real_escape_string($link, $_GET['account']);
	
	showAjax_accounting_year();
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $revision = $langStrings['revision'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$revision[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 59vh; height : 59vh; overflow :auto;\" >";
                echo "<p>";
                    printRevision();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    print_modal();
    printFooter();
	echo "<script src=\"".$url."estraro/estraro.js\"></script>";
}

?>