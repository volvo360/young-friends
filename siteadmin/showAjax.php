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

    if ($replaceTable[$_POST['replaceTable']] === PREFIX.'menu')
    {
        showAjax_menu();
    }
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'menu_siteAdmin')
    {
        showAjax_siteAdmin();        
    }
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'lang_var')
    {
        showAjax_langVar();        
    }
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'lang')
    {
        showAjax_langVar();        
    }
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'groups')
    {
        showAjax_groups();        
    }
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'roles')
    {
        showAjax_roles();        
    }
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'statutes')
    {
        showAjax_statutes();        
    }    
	else if ($replaceTable[$_POST['id']] === PREFIX.'block_date')
    {
        showAjax_block_date();        
    } 
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'settings_site')
    {
        showAjax_settings_site();        
    } 
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'faq')
    {
        showAjax_faq();        
    } 
	
}

function showAjax_siteAdmin()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_menu = $langStrings['showAjax_menu'];
    
    $visible[-1] = $showAjax_menu[3];
    $visible[0] = $showAjax_menu[4];
    $visible[1] = $showAjax_menu[5];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."menu_siteAdmin`";
    
	checkTable($table);
	
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[1]."</label>";
            
            echo "<div class=\"col-sm-10\">";
                echo "<input type = text id =\"note[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu_siteAdmin"]."\" value = \"".$row['note']."\">";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"folder[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[6]."</label>";
            
            echo "<div class=\"col-sm-10\">";
                echo "<input type = text id =\"folder[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu_siteAdmin"]."\" value = \"".$row['folder']."\">";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"file[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[7]."</label>";
            
            echo "<div class=\"col-sm-10\">";
                echo "<input type = text id =\"file[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu_siteAdmin"]."\" value = \"".$row['file']."\">";
            echo "</div>";
        echo "</div>";
    }
}

function showAjax_menu()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_menu = $langStrings['showAjax_menu'];
    
    $visible[-1] = $showAjax_menu[3];
    $visible[0] = $showAjax_menu[4];
    $visible[1] = $showAjax_menu[5];
	$visible[2] = $showAjax_menu[8];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."menu`";
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[1]."</label>";
            
            echo "<div class=\"col-sm-10\">";
                echo "<input type = text id =\"note[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu"]."\" value = \"".$row['note']."\">";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"displayMenu[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">$showAjax_menu[2]</label>";
            
            echo "<div class=\"col-sm-10\">";
                echo "<select class = \"selectpicker show-tick\" id = \"displayMenu[".$row['tableKey']."]\" data-width = \"50%\" data-replace_table = \"".$replaceTable[PREFIX."menu"]."\">";
                    foreach ($visible as $key => $value)
                    {
                        echo "<option value = \"".$key."\"";
                        
                            if ($key == (int)$row['displayMenu'])
                            {
                                echo " "."selected";
                            }
                        
                        echo ">".$value."</option>";
                    }
                echo "</select>";
            echo "</div>";
        echo "</div>";
        
        echo "<form id = \"ajaxForm_addMenu\">";
        
            echo "<div class=\"row mb-3\">";
                echo "<label for=\"folder[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[6]."</label>";

                echo "<div class=\"col-sm-10\">";
                    echo "<input type = text id =\"folder[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu"]."\" value = \"".$row['folder']."\">";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"row mb-3\">";
                echo "<label for=\"file[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[7]."</label>";

                echo "<div class=\"col-sm-10\">";
                    echo "<input type = text id =\"file[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu"]."\" value = \"".$row['file']."\">";
                echo "</div>";
            echo "</div>";
        echo "</form>";
    }
}

function showAjax_langVar()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_langVar = $langStrings['showAjax_langVar'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."lang_var`";
    $table2 = "`".PREFIX."lang`";
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $variable = $row['variable'];
    }
    
    $sql = "SELECT * FROM ".$table2." WHERE variable = '".$variable."' ORDER BY arrayKey";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    if (mysqli_num_rows($result) > 0)
    {
        echo "<table id=\"table_showAjax_langVar\" class=\"DataTable table table-striped\" style=\"width:100%\">";
            echo "<thead>";
                echo "<tr>";
                    echo "<th>";
                        echo $showAjax_langVar[1];
                    echo "</th>";

                    echo "<th>";
                        echo $showAjax_langVar[2];
                    echo "</th>";

                    echo "<th>";
                        echo $showAjax_langVar[3];
                    echo "</th>";
                echo "</tr>";
            echo "</thead>";
        
            echo "<tbody>";
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    echo "<tr>";
                        echo "<td>";
                            echo $row['variable'];
                        echo "</td>";
                    
                        echo "<td>";
                            echo $row['arrayKey'];
                        echo "</td>";
                    
                        echo "<td id = \"note[".$row['tableKey']."]\" class = \"jeditable\" data-replace_table = \"".$replaceTable[PREFIX."lang"]."\">";
                            echo $row['note'];
                        echo "</td>";
                    echo "</tr>";
                } 
            echo "</tbody>";
        
            echo "<tfoot>";
                echo "<tr>";
                    echo "<th>";
                        echo $showAjax_langVar[1];
                    echo "</th>";

                    echo "<th>";
                        echo $showAjax_langVar[2];
                    echo "</th>";

                    echo "<th>";
                        echo $showAjax_langVar[3];
                    echo "</th>";
                echo "</tr>";
            echo "</foot>";
        echo "</table>";
    }
    
    echo "<form id = \"ajaxForm_langString\">";
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"note\" class=\"col-sm-2 col-form-label\">".$showAjax_langVar[4]."</label>";
        
            echo "<div class=\"col-sm-10\">";
                echo "<textarea class=\"form-control\" id=\"note\">";
                echo "</textarea>";
            echo "</div>";
        echo "</div>";
    
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"note\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
        
            echo "<div class=\"col-sm-10\">";
                echo "<button type = \"button\" id = \"replace_table\" class = \"btn btn-secondary form-control\" data-replace_var = \"".$_POST['id']."\" data-replace_table = \"".$replaceTable[PREFIX.'lang']."\" data-target_div = \"ajax_".$replaceTable[PREFIX.'lang_var']."\">".$showAjax_langVar[5]."</button>";
            echo "</div>";
        echo "</div>";
    echo "</form>";
}

function showAjax_groups()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_groups = $langStrings['showAjax_groups'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."groups`";
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_groups[1]."</label>";
            
            echo "<div class=\"col-sm-10\">";
                echo "<input type = text id =\"note[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."groups"]."\" value = \"".$row['note']."\">";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"folder[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
            
            echo "<div class=\"col-sm-10\">";
                echo "<div class=\"form-check checkbox-slider--b\">";
                    echo "<label>";
                        echo "<input type=\"checkbox\"";
        
                            if ((int)$row['visible'] > 0)
                            {
                                echo " "."checked";
                            }
                        echo "><span>"." ".$showAjax_groups[2]."</span>";
                    echo "</label>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }
}

function showAjaxAdmin_roles($data = null)
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjaxAdmin_roles = $langStrings['showAjaxAdmin_roles'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."roles`";
    $table2 = "`".PREFIX."mission`";
    $table10 = "`".PREFIX."user`";
    
    $sql = "SELECT *, t10.tableKey as userTableKey FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t1.assignment_id = t2.assignment_id INNER JOIN ".$table10." t10 ON t10.uid = t2.uid WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $res[$row['userTableKey']] = $row['userTableKey'];
    }
    
    $members = getMembers();
    
    echo "<div class=\"row mb-3\">";
        echo "<label for=\"assignment_name[".$data['tableKey']."]\" class=\"col-sm-4 col-form-label\">".$showAjaxAdmin_roles[1]."</label>";

        echo "<div class=\"col-sm-8\">";
            reset($data);
            echo "<select id =\"uid[".reset($data)['tableKey']."]\" class=\"selectpicker show-tick\" data-live-search = \"true\" data-replace_table = \"".$replaceTable[PREFIX."mission"]."\" ";
                if (((int)reset($data)['maxNumber'] == 0) || empty(reset($data)['maxNumber']))
                {
                    echo " "."multiple";
                }
            echo " data-width = \"100%\">";
                foreach ($members as $key => $value)
                {
                    echo "<option value = \"".$key."\"";
                        if (array_key_exists($key, $res))
                        {
                            echo " "."selected";
                        }
                    echo ">".$value."</option>";
                }
            echo "</select>";
        echo "</div>";
    echo "</div>";
}

function showAjaxAdmin_adminRoles($data = null)
{
    $langStrings = getLangstrings();
    $showAjaxAdmin_adminRoles = $langStrings['showAjaxAdmin_adminRoles'];
    
    $row = reset($data);
    
    echo "<div class=\"row mb-3\">";
        echo "<label for=\"assignment_name[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxAdmin_adminRoles[1]."</label>";

        echo "<div class=\"col-sm-10\">";
            echo "<input type = text id =\"assignment_name[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."roles"]."\" value = \"".$row['assignment_name']."\">";
        echo "</div>";
    echo "</div>";
    
    echo "<div class=\"row mb-3\">";
        echo "<label for=\"maxNumber[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxAdmin_adminRoles[2]."</label>";

        echo "<div class=\"col-sm-10\">";
            echo "<input type = \"number\" min = \"0\" id =\"maxNumber[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."roles"]."\" value = \"".$row['maxNumber']."\">";
        echo "</div>";
    echo "</div>";
    
    echo "<div class=\"row mb-3\">";
        echo "<label for=\"	visible[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

        echo "<div class=\"col-sm-10\">";
            echo "<div class=\"form-check checkbox-slider--b\">";
                echo "<label>";
                    echo "<input type=\"checkbox\"";
                        if ((int)$row['visible'])
                        {
                            echo " "."checked";
                        }
                    echo " class = \"syncData\" data-replace_table = \"".$replaceTable[PREFIX."roles"]."\"><span>"." ".$showAjaxAdmin_adminRoles[3]."</span>";
                echo "</label>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
}

function showAjax_roles()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_roles = $langStrings['showAjax_roles'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."roles`";
    $table2 = "`".PREFIX."mission`";
    $table10 = "`".PREFIX."user`";
    
    $sql = "SELECT * FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t1.assignment_id = t2.assignment_id WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $rowData[] = $row;
    }
    
    $navs['showAjaxAdmin_roles'] = $showAjax_roles[1];
    $navs['showAjaxAdmin_adminRoles'] = $showAjax_roles[2];
    
    $preselect = 'showAjaxAdmin_roles';
    
    echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
        foreach ($navs as $key => $value)
        {
            echo "<li class=\"nav-item\" role=\"presentation\">";
                echo "<button class=\"nav-link";
                    if ($key == $preselect)
                    {
                        echo " "."active";
                    }
                echo "\" id=\"".$key."-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#".$key."\" type=\"button\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                    if ($key == $preselect)
                    {
                        echo "true";
                    }
                    else
                    {
                        echo "false";
                    }
                echo"\">".$value."</button>";
            echo "</li>";
        }
    echo "</ul>";
    
    echo "<div class=\"tab-content\" id=\"myTabContent\">";
        foreach ($navs as $key => $value)
        {
            echo "<div class=\"tab-pane fade";
                if ($key == $preselect)
                {
                    echo " "."show active";
                }
            echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
                echo "<br>";
                call_user_func($key, $rowData);
            echo "</div>";
        }
        
    echo "</div>";
}

function showAjax_statutes()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_statutes = $langStrings['showAjax_statutes'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."statutes`";
    
    checkTable($table);
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ((int)$row['active'] == 0 || empty($row['active']))
        {
            echo "<div class=\"row mb-3\">";
                echo "<label for=\"validFrom[".$row['tableKey']."]\" class=\"col-sm-3 col-form-label\">".$showAjax_statutes[1]."</label>";
                
                echo "<div class=\"col-sm-3\">";
                    echo "<input type=\"date\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."statutes"]."\" id=\"validFrom[".$row['tableKey']."]\" value = \"".$row['validFrom']."\">";
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"row mb-3\">";
                echo "<label for=\"active[".$row['tableKey']."]\"\" class=\"col-sm-3 col-form-label\">"."&nbsp;"."</label>";
                
                echo "<div class=\"col-sm-9\">";
                    echo "<div class=\"form-check checkbox-slider--b\">";
                        echo "<label>";
                            echo "<input type=\"checkbox\" id = \"active[".$row['tableKey']."]\" class = \"validFrom\" data-replace_table = \"".$replaceTable[PREFIX."statutes"]."\" data-target_div = \"ajax_".$replaceTable[PREFIX."statutes"]."\"><span> ".$showAjax_statutes[2]."</span>";
                        echo "</label>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"row mb-3\">";
                echo "<label for=\"active[".$row['tableKey']."]\"\" class=\"col-sm-3 col-form-label\">"."&nbsp;"."</label>";
                
                echo "<div class=\"col-sm-9\">";
                    echo "<button class = \"btn btn-secondary removeStatutes\" id = \"removeStatutes[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."statutes"]."\">".$showAjax_statutes[4]."</button>";
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"row mb-3\">";
                echo "<label for=\"	validFrom\" class=\"col-sm-3 col-form-label\">".$showAjax_statutes[3]."</label>";
                
                echo "<div class=\"col-sm-9\">";
                    echo "<textarea id = \"statues[".$row['tableKey']."]\" class = \"tinyMceArea\" data-replace_table = \"".$replaceTable[PREFIX."statutes"]."\">";
                        echo $row['statutes'];
                    echo "</textarea>";
                echo "</div>";
            echo "</div>";
        }
        else
        {
           echo "<div class=\"row mb-3\">";
                echo "<label for=\"validFrom[".$row['tableKey']."]\" class=\"col-sm-3 col-form-label\">".$showAjax_statutes[1]."</label>";
                
                echo "<div class=\"col-sm-4\">";
                    echo $row['validFrom'];
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"row mb-3\">";
                echo "<label for=\"statues[".$row['tableKey']."]\" class=\"col-sm-3 col-form-label\">".$showAjax_statutes[3]."</label>";
                
                echo "<div class=\"col-sm-9\">";
                    echo $row['statutes'];
                echo "</div>";
            echo "</div>";
        }
    }
    
    echo "<input type = \"hidden\" id = \"replaceKey\" value = \"".$_POST['id']."\">";
}

function showAjax_settings_site()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_settings_site = $langStrings['showAjax_settings_site'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."settings_site`";
    
    $tableHeader['data'] = $showAjax_settings_site[1];
    $tableHeader['value'] = $showAjax_settings_site[2];
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $groupName = $row['groupName'];
    }
    
	if ($groupName == "bank")
	{
		echo "<h2>".$showAjax_settings_site[3]."</h2>";
	}
	else if ($groupName == "mail")
	{
		echo "<h2>".$showAjax_settings_site[5]."</h2>";
	}
	else if ($groupName == "sms")
	{
		echo "<h2>".$showAjax_settings_site[4]."</h2>";
	}
	else if ($groupName == "mail_sms_generally")
	{
		echo "<h2>".$showAjax_settings_site[6]."</h2>";
	}
	
    $sql = "SELECT * FROM ".$table." WHERE groupName = '".$groupName."' ORDER BY data";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    if (mysqli_num_rows($result) > 0)
    {
        echo "<table id=\"tablex_settings_site\" class=\"DataTable table table-striped\" style=\"width:100%\">";
            
            echo "<thead>";
                echo "<tr>";
                    foreach ($tableHeader as $key => $value)
                    {
                        echo "<th id = \"".$key."\">";
                        
                            echo $value;
                        echo "</th>";
                    }
                echo "</tr>";
            echo "</thead>";
        
            echo "<tbody>";
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    echo "<tr>";
                        foreach ($tableHeader as $key => $value)
                        {
                            echo "<td id = \"".$key."[".$row['tableKey']."]\"";
                                if ($key == "value")
                                {
                                    echo " "."class = \"editable_textarea\" data-replace_table = \"".$replaceTable[PREFIX."settings_site"]."\"";
                                }
                            echo ">";
                                echo $row[$key];
                            echo "</td>";
                        }
                    echo "</tr>";
                }
            echo "</tbody>";
        
            echo "<tfoot>";
                echo "<tr>";
                    foreach ($tableHeader as $key => $value)
                    {
                        echo "<th id = \"".$key."\">";
                        
                            echo $value;
                        echo "</th>";
                    }
                echo "</tr>";
            echo "</tfoot>";
        echo "</table>";
    }
}

function showAjax_faq()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_faq = $langStrings['showAjax_faq'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."faq`";
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'"; 
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"question[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_faq[1]."</label>";

            echo "<div class=\"col-sm-10\">";
                echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."faq"]."\" id=\"question[".$row['tableKey']."]\" value = \"".$row['question']."\">";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"row mb-3\">";
            echo "<label for=\"answer[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_faq[2]."</label>";

            echo "<div class=\"col-sm-10\">";
                echo "<textarea id = \"answer[".$row['tableKey']."]\" class = \"tinyMceArea\" data-replace_table = \"".$replaceTable[PREFIX."faq"]."\">";
                    echo $row['answer'];
                echo "</textarea>";
            echo "</div>";
        echo "</div>";
    }
}

function showAjax_block_date()
{
	global $link;
	
	global $link;
    
    $langStrings = getLangstrings();
    $showAjax_block_date = $langStrings['showAjax_block_date'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."block_date`";
	
	checkTable($table);
    
    $sql = "SELECT * FROM ".$table." WHERE blockDate >= CURDATE() OR LENGTH(blockDate) <= 5 ORDER BY blockDate";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	echo "<h2>".$showAjax_block_date[3]."</h2>";
	
	echo "<table class = \"table table-striped\">";
		echo "<thead>";
			echo "<tr>";
				echo "<th>";
					$showAjax_block_date[1];
				echo "</th>";
	
				echo "<th>";
					$showAjax_block_date[2];
				echo "</th>";
			echo "</tr>";
		echo "</thead>";
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				echo "<tr>";
					echo "<td class = \"jeditable\" id = \"blockDate[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."block_date"]."\">";
						echo $row['blockDate'];
					echo "</td>";
				
					echo "<td>";
						echo $row['note'];
					echo "</td>";
				echo "</tr>";
			}
		echo "<tbody>";
	
		echo "</tbody>";
	echo "</table>";
	
	echo "<div class=\"row mb-3\">";
		echo "<label for=\"blockDate\" class=\"col-sm-2 col-form-label\">".$showAjax_block_date[4]."</label>";
		echo "<div class=\"col-sm-10\">";
			echo "<input type=\"date\" size = 10 min = \"".date("Y-m-d")."\" value = \"".date("Y-m-d")."\"id=\"blockDate\">";
		echo "</div>";
	echo "</div>";
	
	echo "<div class=\"row mb-3\">";
		echo "<label for=\"note\" class=\"col-sm-2 col-form-label\">".$showAjax_block_date[5]."</label>";
		echo "<div class=\"col-sm-10\">";
			echo "<input type=\"text\" class=\"form-control\" id=\"note\">";
		echo "</div>";
	echo "</div>";
	
	echo "<div class=\"row mb-3\">";
		echo "<label for=\"note\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
		echo "<div class=\"col-sm-10\">";
			echo "<button class=\"btn btn-secondary form-control\" id=\"addBlockDate\" data-replace_table = \"".$replaceTable[PREFIX."block_date"]."\" data-target_div = \"ajax_".$replaceTable[PREFIX.'settings_site']."\" data-table_key = \"".$_POST['id']."\" >".$showAjax_block_date[6]."</button>";
		echo "</div>";
	echo "</div>";
}
?>