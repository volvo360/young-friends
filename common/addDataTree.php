<?php
error_reporting(E_ALL);
session_start();

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


$replaceTable = getReplaceTable(false);

function insertIntoTable($table1 = null, $table2 = null, $type = "default")
{
	global $link;
	global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	/*$lang = $settings['def_lang'] = 'sv';
	$country = $settings['def_country'] = 'SE';
	
	$sub_field['lang'] = $settings['def_lang'];*/
	
    unset($_POST['tree']);
    
	foreach ($_POST as $key => $value)
	{
		if ($key !== "replaceTable" && $key !== "projectReplaceKey")
		{
			if ($type =="default")
			{
				$sub_field[mysqli_real_escape_string($link, $key)] = mysqli_real_escape_string($link, $value);
			}
			else if ($type === "customer")
			{
				$sub_field[mysqli_real_escape_string($link_k, $key)] = mysqli_real_escape_string($link_k, $value);
			}
		}
	}
	
	
	$sql = "DESCRIBE ".$table1;
    
    if ($type =="default")
	{
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	$validInt['TINYINT'] = "TINYINT";
	$validInt['SMALLINT'] = "SMALLINT";
	$validInt['MEDIUMINT'] = "MEDIUMINT";
	$validInt['INT'] = "INT";
	$validInt['BIGINT'] = "BIGINT";
	
	$block = false;
	
	while ($row = mysqli_fetch_array($result))
	{
		if ($row['Field'] !== "autoId" && !$block)
		{
			foreach ($validInt as $key => $value)
			{
				if (strpos(strtolower($row['Type']), strtolower($key)) !== false) {
					$block = true;
					$field = $row['Field'];
				}
			}
		}
	}
	$sql = "SELECT CASE WHEN MAX(".$field.") IS NULL THEN 1 ELSE MAX(".$field.")+1 END as ".$field." FROM ".$table1.";";
	if ($type === "default")
	{
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	while ($row = mysqli_fetch_array($result))
	{
		$insertId = reset($row);
	}	
	$sql = "INSERT INTO ".$table1." (".$field.") VALUES ('".$insertId."')"; 
	
	if ($type === "default")
	{
		$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
    
    if (!empty($table2))
    {
        $sql = "INSERT INTO ".$table2." (";
            unset ($data);

            $data[] = $field;
            foreach ($sub_field as $key => $value)
            {
                $data[] =$key;
            }
            $sql .= implode(", ",$data).")";
        $sql .= " "."VALUES (";

            unset ($data);

            $data[] = "'".$insertId."'";

            foreach ($sub_field as $key => $value)
            {
                $temp = "AES_ENCRYPT('".$value."', SHA2('";

                if ($type =="default")
                {
                    $temp .=  $phrase;
                }
                else if ($type === "customer")
                {
                    $temp .=  $phrase_k;
                }

                $temp .= "',512))";

                $data[] = $value;

            }
        $sql .= implode(", ",$data).")";

        if ($type === "default")
        {
            $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        }
        else if ($type === "customer")
        {
            $result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
        }
    }
	else
    {
        if (is_array($sub_field))
        {
            foreach ($sub_field as $key => $value)
            {

                $set[] = $key ." = '".$value."'";
            }

            $sql = "UPDATE ".$table1. " SET ".implode(", ", $set )." WHERE autoId = ".mysqli_insert_id($link);
            if ($type === "default")
            {
                $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            }
            else if ($type === "customer")
            {
                $result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
            }
        }
    }
        
	checkTable($table1);
    if (!empty($table2))
    {
	   checkTable($table2);
    }
	
	$sql = "SELECT * FROM ".$table1." WHERE ".$field." = '".$insertId."'";
	if ($type === "default")
	{
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	while ($row = mysqli_fetch_array($result))
	{
		/*echo "<div id = \"replaceKey\">";
			echo $row['tableKey'];
		echo "</div>";*/
        
        return $row['tableKey'];
	}
}

function addPostTable_translation()
{
	$table1 = PREFIX."translation_var";
	$table2 = PREFIX."servotablo_permission_lang";
	
	global $link;
	
	global $phrase;
	
	$sql = "SELECT * FROM ".$table1 ." WHERE variable = '".mysqli_real_escape_string($link, $_POST['note'])."'";
	//echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
            return $row['tableKey'];
		}
	}
	
	$sql = "INSERT INTO ".$table1." (variable) VALUES ('".mysqli_real_escape_string($link, $_POST['note'])."')";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	return checkTable($table1);
}


$replaceTable = getReplaceTable(false);

if ($replaceTable[$_POST['replaceTable']] === PREFIX."translation")
{
    $tableKey = addPostTable_translation();
}
else if ($replaceTable[$_POST['replaceTable']] === PREFIX."statutes")
{
	if ((int)$_POST['copyStatutes'] > 0)
    {
        $table1 = "`".$replaceTable[$_POST['replaceTable']]."`";
        
        $sql = "SELECT * FROM ".$table1." WHERE active > 0 ORDER BY validFrom DESC LIMIT 1";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $statutes = $row['statutes'];
        }
        
        $sql = "INSERT INTO ".$table1." (statutes, validFrom) VALUES ('".$statutes."', '".mysqli_real_escape_string($link, $_POST['validFrom'])."')";
        echo __LINE__ ." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $sql = "UPDATE ".$table1." SET statutesId = autoId";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $tableKey = checkTable($table1);
    }
    else
    {
        $sql = "INSERT INTO ".$table1." (validFrom) VALUES ('".mysqli_real_escape_string($link, $_POST['validFrom'])."')";
        echo __LINE__ ." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $sql = "UPDATE ".$table1." SET statutesId = autoId";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $tableKey = checkTable($table1);
    }
}

else
{
    $table1 = "`".$replaceTable[$_POST['replaceTable']]."`";
    $table2 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
    
    $tableKey = insertIntoTable($table1, null, "default");
}

if (!empty($tableKey))
{
	echo "<div id = \"replaceKey\">";
		echo $tableKey;
	echo "</div>";
    
    /*if (is_array($data))
    {
        echo "<div id = \"blockInsertTree\">";
            echo "false";
        echo "</div>";
    }*/
}

?>