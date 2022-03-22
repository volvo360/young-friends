<?php

    error_reporting(E_ALL);

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
    include_once($path."estraro/showAjax.php");
    echo __LINE__." ".$path."estraro/showAjax.php"."<br>";

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
	
	if (!isset($_SESSION['uid']))
    {
        header ("Location:".$url);
    }
        
    global $link;

    $replaceTable = getReplaceTable();
     
    $langStrings = getLangstrings();
    $showAjax_accounting_year = $langStrings['showAjax_accounting_year'];

    $table = "`".PREFIX."accounting`"; 
    $table2 = "`".PREFIX."accounting_year`";
        
    $sql = "SELECT *, t1.tableKey as accountKey FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t1.accounting = t2.autoId WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['tableKey'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
        
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $tableKey = $row['tableKey'];
        $accountKey = $row['accountKey'];
        
        $file = $row['tableKey']."/source/".$row['vertificateFiles'];
        $activeYear = $row['year'];
		$status = $row['status'];
        
        $note = $row['note'];
        
        $income = $row['income'];
        $expence = $row['expence'];
        
        $date = $row['date'];
    }
        
    $langStrings = getLangstrings();

    $ajax_showVertificateFiles = $langStrings['ajax_showVertificateFiles'];

    echo "<div id = \"modalHeader\">";
        echo $ajax_showVertificateFiles[1];
    echo "</div>";

    echo "<div id = \"modalBody\">";
        echo "<form id = editAccountPost>";
            if (((int)$cashier == (int)$_SESSION['uid'] || $_SESSION['siteAdmin']) && $status == "active")
            {
                echo "<select class = \"selectpicker\" id = \"accountType\">";
                    if (!is_null($income) && $income!= 0)
                    {
                        echo "<option value = \"income\" selected>".$showAjax_accounting_year[2]."</option>";
                        echo "<option value = \"expence\">".$showAjax_accounting_year[3]."</option>";
                    }
                    else 
                    {
                        echo "<option value = \"income\">".$showAjax_accounting_year[2]."</option>";
                        echo "<option value = \"expence\" selected>".$showAjax_accounting_year[3]."</option>";
                    }
                    
                echo "</select>";

                echo " &nbsp;".$showAjax_accounting_year[4]." ";

                echo "<input type=\"text\" placeholder=\"notering\" id=\"note\" value = \"".$note."\"size=\"50\"> ";

                echo "<span id = \"defaultType\">";
                    if (!is_null($income) && $income != 0)
                    {
                        echo "<input type = number min = 0 step = \".01\" value = \"".$income."\" id = \"amount\"> kr";
                    }
                    else 
                    {
                        echo "<input type = number min = 0 step = \".01\" value = \"".$expence."\" id = \"amount\"> kr";
                    }
                echo "</span>";
                echo "<br><br>";

                echo "<div id = \"verifiedFileArea\"></div>";
                
                echo "<input type = \"hidden\" id = \"updateAccountingPost\" value = \"".$accountKey."\">";
                
                echo "<input type = \"hidden\" id = \"keyAccountYear\" value = \"".$tableKey."\">";

                echo "<button class = \"btn btn-secondary addVerified\" data-replace_table = \"".$replaceTable[PREFIX."accounting_year"]."\" data-replace_key = \"".$tableKey."\">".$showAjax_accounting_year[13]."</button><br><br>";

                echo $showAjax_accounting_year[7]." <input type = \"date\" min = \"".$activeYear."-01-01"."\" max = \"".$activeYear."-12-31"."\"  value = \"".$date."\" id = \"accountDate\"><br><br>";

                echo "<button class = \"btn btn-secondary form-control updatePostAccounting\" data-replace_table = \"".$replaceTable[PREFIX."accounting"]."\">".$showAjax_accounting_year[23]."</button><br><br>";
                
                echo "<button class = \"btn btn-sm btn-danger updatePostAccounting\" >".$showAjax_accounting_year[24]."</button><br><br>";
            }
        echo "</form>";
    echo "</div>";

    echo "<div id = \"modalFooter\">";
        echo "<button type=\"button\" class=\"btn btn-secondary closeModal\" data-dismiss=\"modal\">".$ajax_showVertificateFiles[2]."</button>";
    echo "</div>";
?>