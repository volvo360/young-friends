<?php

session_start();

error_reporting(E_ALL);

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

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
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

        $replaceTable = getReplaceTable(false);

        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'accounting')
        {
            showAjax_updateAccountingPost();
        }
    }

    function showAjax_updateAccountingPost()
    {
        global $link;
        
        $table = PREFIX.'accounting';
        
        if (!empty($_POST['deleteAccountPost']))
        {
            $sql = "DELETE FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['updateAccountingPost'])."'"; 
        echo __LINE__ ." ".$sql."<br>";
        
        $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
        }
        
        
        $note = mysqli_real_escape_string($link, $_POST['note']);
        
        if (empty($note))
        {
            return false;
        }
        
        $date = mysqli_real_escape_string($link, $_POST['accountDate']);
        
        if ($_POST['accountType'] == "expence")
        {
            $sql = "UPDATE ".$table." SET income = NULL, expence = '".mysqli_real_escape_string($link, $_POST['amount'])."'";
        }
        else if ($_POST['accountType'] == "income")
        {
            $sql = "UPDATE ".$table." SET expence = NULL, income = '".mysqli_real_escape_string($link, $_POST['amount'])."'";
        }
        else
        {
            return false;
        }
        
        $sql .= " ".", note = '".$note."', date = '".$date."' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['updateAccountingPost'])."'"; 
        echo __LINE__ ." ".$sql."<br>";
        
        $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    }
