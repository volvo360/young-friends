<?php
updateReplaceTable();
$table = "`".PREFIX."replacetable`";
checkTable($table);

if (!function_exists(fixutferror))
{
	function fixutferror($str) {
	  $a = array("Ã¶", "Ã¥", 'Ã¤', "Ã…", "Ã©", "Ã–", "Ã¥", "Ã„");
	  $b = array('ö', 'å', 'ä', "Å", "é", "Ö", "å", "Ä");
	  return str_replace($a, $b, $str);
	}
}

//https://stackoverflow.com/a/834355 2021-01-14

function startsWith( $haystack, $needle ) {
     $length = strlen( $needle );
     return substr( $haystack, 0, $length ) === $needle;
}

function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

function getSiteSettings()
{
    global $link;
    global $phrase;
    
    global $getSiteSettings;
    
    if (!empty($getSiteSettings))
    {
        return $getSiteSettings;
    }
    
    $table = "`".PREFIX."settings_site`";
    
    $sql = "SELECT * FROM ".$table."";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result))
    {
        $getSiteSettings[$row['data']] = $row['value'];
    }
    
    return $getSiteSettings;
}

function getLangstrings($displayLang = null)
{
	global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."lang`";
	
	/*$country = $settings['def_country'] = 'SE';
	
    $userSettings = getUserSettings();

    if (!empty($displayLang))
    {
        //Do nothing
    }
    if (isset($_SESSION['uid']))
    {
        $userSettings = getUserSettings();

        $displayLang = array_map("trim", explode(",", $userSettings['langService']));
    }

    if (empty($userSettings['langService']))
    {
        $data = array_map("trim", explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']));

        foreach ($data as $key => $value)
        {
            if (!startsWith($value, "q=" ))
            {
                $displayLang[] = substr($value,0,2);
            }
        }

        if (isset($_SESSION['userLang']) && isset($_SESSION['uid']))
        {
            $temp[] = $_SESSION['userLang'];
        }
        
        $temp2 = array_filter(array_unique($displayLang));
        $displayLang = array_merge($temp, $temp2);
    }
    else
    {    
        if (isset($_SESSION['userLang']) && !(isset($_SESSION['uid'])))
        {
            $temp[] = $_SESSION['userLang'];
            $temp2 = array_map("trim", explode(",", $userSettings['langService']));
            $displayLang = array_merge($temp, $temp2);
        }
        else
        {
             $displayLang  = array_map("trim", explode(",", $userSettings['langService']));
        }
    }

    $i = 0;
    
    if (isset($_SESSION['userLang']) && !isset($_SESSION['uid']))
    {
        $displayLang[] = $_SESSION['userLang'];
        $order[] = "WHEN lang = '".$_SESSION['userLang']."' THEN -1";
        $order_lang[] = "WHEN code = '".$$_SESSION['userLang']."' THEN -1";
        $i++;
    }
    
    $displayLang = array_map("trim", explode(",", $siteSettings['language']));
        
    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
        $i++;
    }

    if (!isset($_SESSION['userLang']))
    {
        $_SESSION['userLang'] = reset($displayLang);
    }
    
    /*echo __LINE__." ";
    print_r($order);
    echo "<br>";*/
    
	//We need all these sub-questions as the field with the language is also encrypted.
	
	$sql = "SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note FROM $table) AS q ORDER BY variable, arrayKey LIMIT 18446744073709551615) as k ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 10 WHEN lang = 'de' THEN 11 WHEN lang = 'fr' THEN 12 WHEN lang = 'it' THEN 13 ELSE 100 END LIMIT 18446744073709551615) as lang GROUP BY variable, arrayKey";
    $sql = "SELECT * FROM ".$table." GROUP BY variable, arrayKey ORDER BY variable, arrayKey";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$data[$row['variable']][(int)$row['arrayKey']] = $row['note'];	
	}
	
	return $data;
}

function getLangstringsArray($variable = null, $langs = null)
{
	global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	if (empty($variable) || empty($langs))
	{
		echo __LINE__." ".basename(__FILE__)." ERROR variable = ".$variable."<br>";
		return false;
	}
	
	foreach ($langs as $key => $value)
	{
		$where[] = "lang = '".$value."'";
		
		$orderBy[] = "WHEN lang = '".$value."' THEN ".$key;
	}
	
	$orderBy[] = "ELSE 100 END";
	
	
	$table = "`".PREFIX."translation`";
	
	//$sql = "SELECT * FROM (SELECT * FROM (SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note FROM $table HAVING variable = '".$variable."' AND ".implode(" OR ", $lang)." ORDER BY variable, arrayKey LIMIT 18446744073709551615) as k ORDER BY CASE WHEN lang = '".$lang."' THEN 1 WHEN lang = 'en' THEN 2 WHEN lang = 'de' THEN 3 WHEN lang = 'fr' THEN 4 WHEN lang = 'it' THEN 5 ELSE 10 END LIMIT 18446744073709551615) as lang GROUP BY variable, arrayKey";
	
	$sql = "SELECT * FROM (SELECT * FROM(SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note FROM $table WHERE variable = AES_ENCRYPT('".$variable."', SHA2('".$phrase."', 512))) as p WHERE (".implode(" OR ", $where).") ORDER BY arrayKey LIMIT 18446744073709551615) as k ORDER BY CASE ".implode(" ", $orderBy);
	//echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$data[$row['arrayKey']][$row['lang']] = $row['note'];
	}

	return $data;
}

function getReplaceLang($mode = true)
{
    global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."languages`";
	
	$sql = "SELECT tableKey, code FROM ".$table;
    
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		if ($mode)
		{
			$replace[$row['code']] = $row['tableKey'];	
		}
		else
		{
			$replace[$row['tableKey']] = $row['code'];
		}
	}
    
    return $replace;
}

function getReplaceTranslationVar($mode = true)
{
    global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."translation_var`";
	
	$sql = "SELECT * FROM ".$table ." ORDER BY variable";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		if ($mode)
		{
			$replace[$row['variable']] = $row['tableKey'];	
		}
		else
		{
			$replace[$row['tableKey']] = $row['variable'];
		}
	}
	
	return $replace;
}

function updateTranslationTable()
{
	global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."lang`";
	$table2 = "`".PREFIX."lang_var`";
	
	//Extract existing variable from translation table
	
	$sql = "SELECT * FROM (SELECT variable FROM $table LIMIT 18446744073709551615) AS lang1 GROUP BY lang1.variable ORDER BY lang1.variable";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$masterVar[$row['variable']] = $row['variable'];
	}
	
	//Extract existing variable from translation replace table
	
	$sql = "SELECT * FROM ".$table2;
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$existingVar[$row['variable']] = $row['variable'];
	}
	
	//Get diff between the two tables
	
	if (is_array($existingVar))
	{
		$diff = array_diff($masterVar, $existingVar);
	}
	else
	{
		$diff = $masterVar;
	}
	
	//If nececery insert new vars
	
	if (is_array($diff))
	{
		foreach ($diff as $key => $value)
		{
			$sql = "INSERT INTO ".$table2." (variable) VALUES ('".$value."')";
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
		}
		
		checkTable($table2);
	}
}

function checkTable($table = null, $always = true)
{
	global $link;
	global $link_k;
	
	if (!$always)
	{
		return true;
	}
	
	//Function for creating uniqe replacekeys in a table, safty first....
	if ((substr($table, 0,6) === "`ep_k_") || (substr($table, 0,5) === "ep_k_"))
	{
		$sql = "SELECT * FROM ".$table." WHERE tableKey IS NULL OR tableKey = ''";
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link_k));

		while ($row = mysqli_fetch_array($result))
		{
			$run = true;

			do
			{
				$tableKey = generateStrongPassword(15);
				$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$tableKey."'";
				$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link_k));

				if (mysqli_num_rows($result2) === 0)
				{
					$sql = "UPDATE ".$table ." SET tableKey = '".$tableKey."' WHERE autoId = '".$row['autoId']."'";
					$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link_k));

					$run = false;
				}

			} while ($run);
		}
		
		return $tableKey;
	}
	else
	{
		$sql = "SELECT * FROM ".$table." WHERE tableKey IS NULL OR tableKey = ''";
		$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result))
		{
			$run = true;

			do
			{
				$tableKey = generateStrongPassword(14);

				$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$tableKey."'";
				$result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

				if (mysqli_num_rows($result2) === 0)
				{
					$sql = "UPDATE ".$table ." SET tableKey = '".$tableKey."' WHERE autoId = '".$row['autoId']."'";

					$result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

					$run = false;
				}

			} while ($run);
		}
		
		return $tableKey;
	}
}

function updateReplaceTable($mode = true)
{
	global $link;
    global $link_k;
	
	if (!$mode)
	{
		return true;	
	}
	
	/*if (!empty($link_k))
	{
		$sql = "SHOW TABLES;";
        
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));
		
		$resync = false;
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			foreach ($row as $key => $value)
			{
				if ("`".$value."`" !== PREFIX_K."replacetable")
				{
					$sql = "SELECT * FROM ".PREFIX_K."replacetable WHERE replaceTable = '".$value."'"; 
					$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));
					
					if (mysqli_num_rows($result2) === 0)
					{
						$sql = "INSERT INTO ".PREFIX_K."replacetable (replaceTable) VALUES ('".$value."')";
						$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));
						
						$resync = true;
					}
                    
				}
			}
            checkTable(PREFIX_K."replacetable");
        }
		
		if ($resync)
		{
			checkTable(PREFIX_K."replacetable");
		}
	}*/
		
	$sql = "SHOW TABLES;";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
    
	$resync = false;
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		foreach ($row as $key => $value)
		{
			if ("`".$value."`" !== PREFIX."replacetable")
			{
				$sql = "SELECT * FROM ".PREFIX."replacetable WHERE replacetable = '".$value."'"; 
                $result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
				
				if (mysqli_num_rows($result2) === 0)
				{
					$sql = "INSERT INTO ".PREFIX."replacetable (replaceTable) VALUES ('".$value."')";
                    $result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
					
					$resync = true;
                    //checkTable(PREFIX."replacetable");
				}
			}
		}
	}
	
	if ($resync)
	{
		checkTable(PREFIX."replacetable");
	}
}

function getReplaceTable($mode = true)
{   
    global $link;
    global $link_k;
	
	//updateReplaceTable();
	if (defined('PREFIX_K'))
	{
		$table = "`".PREFIX_K."replacetable`";

		$sql = "SELECT * FROM ".$table;
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($mode)
			{
				$data[$row['replaceTable']] = $row['tableKey'];
			}
			else
			{
				$data[$row['tableKey']] = $row['replaceTable'];
			}
		}
	}
    $table = "`".PREFIX."replacetable`";
    
    $sql = "SELECT * FROM ".$table;
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ($mode)
        {
            $data[$row['replaceTable']] = $row['tableKey'];
        }
        else
        {
            $data[$row['tableKey']] = $row['replaceTable'];
        }
    }
    
    return $data;  
}


function getCountryes()
{
	global $link;
	global $link_k;

	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."country`";
    
    $sql = "SELECT * FROM ".$table;
    //echo $sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$country[$row['country_code']] = $row['country_name'];
	}
	
	return $country;
}

function getDisplayLang($mode = true) 
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;
    
    $table = "`".PREFIX."translation`";
    
    $table10 = "`".PREFIX."languages`";
    
    if (empty($userSettings['langService']))
    {
        $data = array_map("trim", explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']));

        foreach ($data as $key => $value)
        {
            if (!startsWith($value, "q=" ))
            {
                $displayLang[] = substr($value,0,2);
            }
        }

        $displayLang = array_filter(array_unique($displayLang));
    }
   

    $i = 0;
    
    $displayLang = array_map("trim", explode(",", $siteSettings['language']));
        
    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
        $i++;
    }
    
    $sql = "SELECT * FROM (SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang FROM ".$table.") as k GROUP BY lang";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $where_lang[] = "code = '".$row['lang']."'";
    }
   
    $sql = "SELECT * FROM ".$table10." WHERE (".implode(" OR ", $where_lang).") ORDER BY CASE ".implode(" ", $order_lang)." WHEN code = 'en' THEN 11 WHEN code = 'de' THEN 12 WHEN code = 'fr' THEN 13 WHEN code = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615";
    //echo __LINE__." ".$sql."<br>";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result))
    {
        if ($mode)
        {
            $langs[$row['tableKey']] = $row['Local language name'];
        }
        else
        {
            $langs[$row['tableKey']] = $row['Code'];
        }
    }
    
    return $langs;
}

function getMembers($mode = true)
{
    global $link;
    
    $table = "`".PREFIX."user`";
    
    $sql = "SELECT * FROM ".$table." WHERE betalt > CURDATE() AND uid > 0 ORDER BY firstName, sureName";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result))
    {
        if ($mode)
        {
            $members[$row['tableKey']] = trim($row['firstName']). " ".trim($row['sureName']);
        }
        else
        {
            $members[$row['tableKey']] = $row['uid'];
        }
    }
    
    return $members;
}

function getUserData($id = null)
{
    if ($id == null)
    {
        return false;
    }
    
    global $link;
    
    $table = "`".PREFIX."user`";
    
    $sql = "SELECT * FROM ".$table." WHERE id = '".mysqli_real_escape_string($link, $id)."'";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ($row['autoansv'] == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>