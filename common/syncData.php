<?php
	session_start();
    error_reporting(E_ALL);
    
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

    function findConnectionField($table = null, $type = "default")
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
		$sql = "DESCRIBE ".$table;

        if ($type === "default")
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
						return $row['Field'];
					}
				}
			}
		}
		
		return $field;
	}

	function updateTable($table10 = null, $table11 = null, $mode = 'default')
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
        $replaceLang = getReplaceLang(false);

        if (isset($_POST['jEditable']))
		{
			$temp = array_map("trim",explode("[", $_POST['id']));
		
			foreach ($temp as $key => &$value)
			{
				$value = trim($value,"]");
			}
			
			// Fix to make sure that data that jEditable sends to this script ends up in the correct format for our script. This sends id and value as two different variables while we want them together
			$temp = array_map("trim",explode("[", $_POST['id']));
			foreach ($temp as $key => &$value)
			{
				$value = trim($value,"]");
			}
			
			$temp2 = explode("_", $temp[1]);
            
			if (array_key_exists(1,$temp2))
			{
				$_POST[$temp[0]][$temp2[1]] = $_POST['value'];
			}
			else
			{
				$_POST[$temp[0]][$temp[1]] = $_POST['value'];
			}
			echo $_POST['value'];
			unset($_POST['id'], $_POST['value'], $_POST['jEditable']);
        }
		/*echo __LINE__." ";
        print_r($_POST);
        echo "<br>";*/
        
		foreach ($_POST as $key => $value)
		{
            if ($key !== "replaceTable" && $key !== "replaceLang")
			{
                $sql = "SHOW COLUMNS FROM ".$table10." LIKE '".$key."' ;";
                if ($mode == 'default')
				{
					$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
				}
				else if ($mode == 'customer')
				{
					$result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
				}
				
				if (mysqli_num_rows($result) > 0)
				{
					$table2 = $table10;
				}
				else {
					$sql = "SHOW COLUMNS FROM ".$table11." LIKE '".$key."' ;";
                    
                    if ($mode == 'default')
					{
						$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
					}
					else if ($mode == 'customer')
					{
						$result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
					}
					
					if (mysqli_num_rows($result) > 0)
					{
						$table2 = $table11;
					}
					else
					{
                        continue;
					}
				}
				
				if (is_array($value))
				{
            		foreach ($value as $key_sub => $value_sub)
					{
						$sql = "SELECT * FROM ".$table2." WHERE tableKey = '".$key_sub."'";
                        if ($mode == 'default')
						{
							$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
						}
						else if ($mode == 'customer')
						{
							$result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
						}
                        if (mysqli_num_rows($result) > 0)
						{
							if ($mode == 'default')
							{
								/*if ($key !== "folder" && $key !== "file" && $key !== "projectType" && $key !== "extra" && $key !== "fileExtension")
								{
									$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link, $key)." = AES_ENCRYPT('".mysqli_real_escape_string($link, trim($value_sub))."', SHA2('".$phrase."', 512)) WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
								}
								else*/
								{
									//$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link, $key)." = '".mysqli_real_escape_string($link, trim($value_sub))."' WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
                                    $sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link, $key)." ='".mysqli_real_escape_string($link, trim($value_sub))."' WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
                                    //echo __LINE__." ".$sql."<br>";
								}
                                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
							}
							else if ($mode == 'customer')
							{
								if ($key !== "folder" && $key !== "file" && $key !== "projectType" && $key !== "minAccountId" && $key !== "headerType" && $key !== "start" && $key !== "end" && $key !== "fileExtension")
								{
									$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link_k, $key)." = AES_ENCRYPT('".mysqli_real_escape_string($link_k, trim($value_sub))."', SHA2('".$phrase_k."', 512)) WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
								}
								else
								{
									$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link_k, $key)." = '".mysqli_real_escape_string($link_k, trim($value_sub))."' WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
								}
                                //echo __LINE__." ".$sql."<br>";
                                $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
							}
						}
						else
						{     
                            if ($mode === "default")
                            {
                                $field = findConnectionField($table10, "default");
                            }
                            else
                            {
                                $field = findConnectionField($table10, "customer");
                            }
                            
                            if(strpos($key_sub, "_") !== false)
                            {
                                $temp4 = array_map("trim", explode("_", $key_sub));
                                
                                $sql = "SELECT * FROM ".$table10 ." WHERE tableKey = '".$temp4[0]."'";
                                echo __LINE__." ".$sql."<br>";
                                if ($mode == 'default')
                                {
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                } 
                                
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                {
                                    $temp2 = $row[$field];
                                }
                                
                                $replaceLang = getReplaceLang(false);
                                
                                if ($mode == 'default')
                                {
                                    $sql = "SELECT * FROM ".$table11." WHERE ".$field." = '".$temp2."' AND lang = AES_ENCRYPT('".$replaceLang[$temp4[1]]."', SHA2('".$phrase."', 512))";
                                    echo __LINE__." ".$sql."<br>";
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $sql = "SELECT * FROM ".$table11." WHERE ".$field." = '".$temp2."' AND lang = AES_ENCRYPT('".$replaceLang[$temp4[1]]."', SHA2('".$phrase_k."', 512))";
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                } 
                                
                                if (mysqli_num_rows($result) > 0)
                                {
                                    while ($row = mysqli_fetch_array($result))
                                    {
                                        $tableKey = $row['tableKey'];
                                    }
                                    
                                    if ($mode == 'default')
                                    {
                                        $sql = "UPDATE ".$table11." SET ".$key." = AES_ENCRYPT('".mysqli_real_escape_string($link, $value_sub)."', SHA2('".$phrase."', 512)) WHERE tableKey = '".$tableKey."'";
                                        echo __LINE__." ".$sql."<br>";
                                        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                    }
                                    else if ($mode == 'customer')
                                    {
                                        $sql = "UPDATE ".$table11." SET ".$key." = AES_ENCRYPT('".mysqli_real_escape_string($link, $value_sub)."', SHA2('".$phrase_k."', 512)) WHERE tableKey = '".$tableKey."'";
                                        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                    } 
                                }
                                else
                                {
                                    if ($mode == 'default')
                                    {
                                        $sql = "INSERT INTO ".$table11." (".$field.", ".$key.", lang) VALUES ('".$temp2."', AES_ENCRYPT('".mysqli_real_escape_string($link, $value_sub)."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link, $replaceLang[$temp4[1]])."', SHA2('".$phrase."', 512)))";
                                        echo __LINE__." ".$sql."<br>";
                                        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                    }
                                    else if ($mode == 'customer')
                                    {
                                        $sql = "INSERT INTO ".$table11." (".$field.", '".$key.", lang) VALUES ('".$temp2."', AES_ENCRYPT('".mysqli_real_escape_string($link_, $value_sub)."', SHA2('".$phrase_k."', 512)) , AES_ENCRYPT('".mysqli_real_escape_string($link, $replaceLang[$temp4[1]])."', SHA2('".$phrase_k."', 512)))";
                                        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                    } 
                                    checkTable($table11, $mode);
                                    continue;
                                }
                                
                            }
                            
                            else if (isset($_POST['replaceLang']))
                            {
                                $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".$key_sub."'";
                                if ($mode == 'default')
                                {
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                } 
                            }
                            else
                            {
                                $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".$key_sub."'";
                                //echo __LINE__." ".$sql."<br>";
                                if ($mode == 'default')
                                {
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                }
                            }
							
                            if (mysqli_num_rows($result) == 0)
							{
								$id = key($_SESSION['replaceTempKey'][$key_sub]);
								$id = array_search($key_sub, $_SESSION['replaceTempKey'] );
							}
							else
								
							{
								while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
								{
									$id = $row[$field];
								}
							}
							
							if ($mode == 'default')
							{
								if (!empty($field))
								{
									$sql = "INSERT INTO ".$table2." (".$field.", lang, ".mysqli_real_escape_string($link, $key).") VALUES ('".$id."',AES_ENCRYPT('".mysqli_real_escape_string($link, $replaceLang[$_POST['replaceLang']])."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link, trim($value_sub))."', SHA2('".$phrase."', 512)))";
								}
								else
								{
									$sql = "INSERT INTO ".$table2." (lang, ".mysqli_real_escape_string($link, $key).") VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $key_sub)."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link, trim($value_sub))."', SHA2('".$phrase."', 512)))";
								}
                                
                                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
							}
							else if ($mode == 'customer')
							{
								if (!empty($field))
								{
									$sql = "INSERT INTO ".$table2." (".$field.",lang, ".mysqli_real_escape_string($link_k, $key).") VALUES ('".$id."',AES_ENCRYPT('".mysqli_real_escape_string($link_k, $replaceLang[$_POST['replaceLang']])."', SHA2('".$phrase_k."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link_k, trim($value_sub))."', SHA2('".$phrase_k."', 512)))";
								}
								else
								{
									$sql = "INSERT INTO ".$table2." (lang, ".mysqli_real_escape_string($link_k, $key).") VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link_k, $key_sub)."', SHA2('".$phrase_k."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link_k, trim($value_sub))."', SHA2('".$phrase_k."', 512)))";
                                }
                                $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
							}
                            checkTable($table11, $mode);
						}
						//echo $sql."<br>";
					}
				}
            }
		}
	}

    function sync_mission()
    {
        global $link;
        
        $table = "`".PREFIX."roles`";
        $table2 = "`".PREFIX."mission`";
        
        $table10 = "`".PREFIX."user`";
        
        $members = getMembers(false);
        
        echo __LINE__." ";
        print_r($_POST['uid']);
        echo "<br>";
        
        if (is_array($_POST['uid']))
        {
            $mission = mysqli_real_escape_string($link, key($_POST['uid']));
            
            foreach ($_POST['uid'] as $key => $value)
            {
                foreach ($value as $key_sub => $sub_value)
                {
                    $uid[] = $members[mysqli_real_escape_string($link, $sub_value)];
                }
            }
        }
        else
        {
            $mission = mysqli_real_escape_string($link, key($_POST['uid']));
            $uid = $members[reset($_POST['uid'])];
        }
        
        $sql = "SELECT * FROM ".$table." WHERE tableKey = '".$mission."'";
        echo __LINE__." ".$sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        
        while ($row = mysqli_fetch_array($result))
        {
            $assignment_id = $row['assignment_id'];
            $maxNumber = $row['maxNumber'];
            $level = $row['level'];
        }
        
        $sql = "SELECT * FROM ".$table2." WHERE assignment_id = '".$assignment_id."'";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        
        if (mysqli_num_rows($result) > 1)
        {
           while ($row = mysqli_fetch_array($result))
            {
                $current[] = $row['uid'];
            } 
        }
        else
        {
            while ($row = mysqli_fetch_array($result))
            {
                $current = $row['uid'];
            }
        }
        
        if (is_array($current))
        {
            if (is_array($uid))
            {
                $insert = array_diff($uid, $current);
                
                $delete = array_diff($current, $uid);
            }
            else
            {
                $temp = array_search($current);
            
                unset($current[$temp]);
            }
        }
        else
        {
            if (is_array($uid))
            {
                $insert = $uid;
            }
            else
            {
                $temp = array_search($current);
            
                unset($current[$temp]);

                $insert = $current;
            }
            
        }
        
        if (count($uid) > 1)
        {
            if ((int)$maxNumber > count($uid))
            {
                return false;
            }
        }
        
        if (!empty($insert))
        {
            if (is_array($insert))
            {
                foreach ($insert as $key => $value)
                {
                    $insertData[] = "("."'".$assignment_id."', '".$value."', '".$level."')";
                }
                
                $sql = "INSERT INTO ".$table2." (assignment_id, uid, level) VALUES ".implode($insertData, ", ");
            }
            else
            {
                $sql = "INSERT INTO ".$table2." (assignment_id, uid, level) VALUES ("."'".$assignment_id."', '".$value."', '".$insert."')";;
            }
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        }
        
        if (!empty($delete))
        {
            if (is_array($delete))
            {
                foreach ($delete as $key => $value)
                {
                    $where[] = "(uid = '".$value."')";
                }
                
                $sql = "DELETE FROM ".$table2." WHERE assignment_id = '".$assignment_id."' AND ( ".implode($where, " OR ").")";
            }
            else
            {
                $sql = "DELETE FROM ".$table2." WHERE assignment_id = '".$assignment_id."' AND uid = '".$delte."'";
            }
            
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        }
        
    }

	function sync_user()
    {
        global $link;
        
        $table = "`".PREFIX."user`";
		
		$replaceTable = getReplaceTable(false);
		
		if (isset($_POST['password']))
		{
			if (isset($_POST['repPassword']))
			{
				$password = trim(reset($_POST['password']));
				
				$repPassword = trim(reset($_POST['repPassword']));
				
				if ((strlen($password) >= 6) && ($password == $repPassword))
				{
					$key = key(reset($_POST['password']));
					
					$_POST['password'][$key] = $password;
					
					unset ($_POST['repPassword']);
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		
		if (isset($_POST['mobile']))
		{
			$_POST['mobile'] = preg_replace("/[^0-9]/", "", $_POST['mobile']);
		}
		
		$table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
        $table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        updateTable($table10, null, 'default');
        
	}

	function sync_border_protocol()
	{
		global $link;

		$table = "`".PREFIX."border_protocol`";
		$table2 = "`".PREFIX."border_agenda`";

		$sql = "SELECT * FROM ".$table. " WHERE tableKey = '".key($_POST['meetingDay'])."'";
		//echo __LINE__." ".$sql."<br>";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$metingId = $row['autoId'];
		}

		$members = getMembers(false);

		foreach ($_POST as $key => $value)
		{
			unset($temp);

			if ($key == "tableKey")
			{
				continue;
			}

			$sql = "SHOW COLUMNS FROM ".$table." LIKE '".$key."' ;";
			//echo __LINE__." ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

			if (mysqli_num_rows($result) > 0)
			{
				foreach ($value as $sub_key => $sub_value)
				if (is_array($sub_value))
				{	
					foreach ($sub_value as $sub3_key => $sub3_value)
					{
						if (array_key_exists($sub3_value, $members))
						{
							$temp[] = $members[$sub3_value];
						}
						else
						{
							$temp[] = $sub3_value;
						}
					}

					$sub_value2 = mysqli_real_escape_string($link, implode(",", $temp));
				}
				else
				{
					if (array_key_exists($sub_value, $members))
					{
						$sub_value2 = mysqli_real_escape_string($link, $members[$sub_value]);
					}
					else
					{
						$sub_value2 = mysqli_real_escape_string($link, $sub_value);
					}

				}

				$sql = "UPDATE ".$table." SET ".$key." = '".$sub_value2."' WHERE metingId = '".$metingId."'";
				//echo __LINE__." ".$sql."<br>";
				$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
			}
			else
			{
				$sql = "SHOW COLUMNS FROM ".$table2." LIKE '".$key."';";
				$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

				if (mysqli_num_rows($result) > 0)
				{
					foreach ($value as $sub_key => $sub_value)
					if (is_array($sub_value))
					{
						$sub_value2 = mysqli_real_escape_string($link, implode(",", $sub_value));
					}
					else
					{
						$sub_value2 = mysqli_real_escape_string($link, $sub_value);
					}

					$sql = "UPDATE ".$table2." SET ".$key." = '".$sub_value2."' WHERE autoId = '".$metingId."'";
					$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
				}
			}
		}
	}

	
	function sync_annualMeeting_agenda()
	{
		global $link;

		$table = "`".PREFIX."annualMeeting_agenda`";
		
		if (isset($_POST['responsible']))
		{
			$members = getMembers(false);
			
			$sql = "UPDATE ".$table. " SET responsible = '".$members[reset($_POST['responsible'])]."' WHERE tableKey = '".key($_POST['responsible'])."'";
			$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		}
		else
		{
			updateTable($table, null, 'default');
		}
	}

	function sync_annualMeeting_protocol()
	{
		global $link;

		$table = "`".PREFIX."annualMeeting_protocol`";
		$table2 = "`".PREFIX."annualMeeting_agenda`";

		foreach ($_POST as $key => $value)
		{
			if ($key == "replaceTable")
			{
				continue;
			}
			else
			{
				$key = key($value);
			}
		}
		
		$sql = "SELECT * FROM ".$table. " WHERE tableKey = '".$key."'";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$metingId = $row['autoId'];
		}

		$members = getMembers(false);

		foreach ($_POST as $key => $value)
		{
			unset($temp);

			if ($key == "tableKey")
			{
				continue;
			}

			$sql = "SHOW COLUMNS FROM ".$table." LIKE '".$key."' ;";
			//echo __LINE__." ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

			if (mysqli_num_rows($result) > 0)
			{
				foreach ($value as $sub_key => $sub_value)
				if (is_array($sub_value))
				{	
					foreach ($sub_value as $sub3_key => $sub3_value)
					{
						if (array_key_exists($sub3_value, $members))
						{
							$temp[] = $members[$sub3_value];
						}
						else
						{
							$temp[] = $sub3_value;
						}
					}

					$sub_value2 = mysqli_real_escape_string($link, implode(",", $temp));
				}
				else
				{
					if (array_key_exists($sub_value, $members))
					{
						$sub_value2 = mysqli_real_escape_string($link, $members[$sub_value]);
					}
					else
					{
						$sub_value2 = mysqli_real_escape_string($link, $sub_value);
					}

				}

				$sql = "UPDATE ".$table." SET ".$key." = '".$sub_value2."' WHERE meetingId = '".$metingId."'";
				//echo __LINE__." ".$sql."<br>";
				$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
			}
			else
			{
				$sql = "SHOW COLUMNS FROM ".$table2." LIKE '".$key."';";
				$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

				if (mysqli_num_rows($result) > 0)
				{
					foreach ($value as $sub_key => $sub_value)
					if (is_array($sub_value))
					{
						$sub_value2 = mysqli_real_escape_string($link, implode(",", $sub_value));
					}
					else
					{
						$sub_value2 = mysqli_real_escape_string($link, $sub_value);
					}

					$sql = "UPDATE ".$table2." SET ".$key." = '".$sub_value2."' WHERE autoId = '".$metingId."'";
					$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
				}
			}
		}
	}

	$replaceTable = getReplaceTable(false);

    if (empty($_POST['replaceTable']))
    {
        return true;
    }

    if ($replaceTable[$_POST['replaceTable']] === PREFIX."mission")
	{
        sync_mission();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."user")
	{
        sync_user();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."border_protocol") 
	{
		sync_border_protocol();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."annualMeeting_agenda") 
	{
		sync_annualMeeting_agenda();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."annualMeeting_protocol") 
	{
		sync_annualMeeting_protocol();
	}
    else
    {
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
        $table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        updateTable($table10, null, 'default');
    }
?>