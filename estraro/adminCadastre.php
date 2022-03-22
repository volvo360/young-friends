<?php

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
include_once($path."common/theme.php");
include_once($path."common/modal.php");
	

function renderOldMembers($membersData = null, $newYear = false)
{
	$replaceTable = getReplaceTable();
	
	$langStrings = getLangstrings();
    $renderCurrentMembers = $langStrings['renderCurrentMembers'];
	
	$th['payed']= $renderCurrentMembers[1];
	$th['firstName'] = $renderCurrentMembers[2];
	$th['sureName'] = $renderCurrentMembers[3];
	$th['address'] = $renderCurrentMembers[4];
	$th['city'] = $renderCurrentMembers[5];
	//$th['phone'] = $showAdminCadastre[6];
	$th['mobile'] = $renderCurrentMembers[7];
	$th['email'] = $renderCurrentMembers[8];
	$th['personal'] = $renderCurrentMembers[9];
	
	echo "<table class = \"table table-responsive DataTable\" witdht = \"100%\">";
		echo "<thead>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</thead>";
	
		echo "<tbody>";
			foreach ($membersData as $key => $value)
			{
				echo "<tr>";
					foreach ($th as $key2 => $value2)
					{
						if ($key2 == "personal")
						{
							$members = getMembers();
							
							echo "<td >";
								echo "<button class = \"btn btn-secondary editPersonal\" id = \"userTableKey[".$value['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX.'user']."\">".$renderCurrentMembers[9]."</button>";
							echo "</td>";
						}
						else if ($key2 == "payed")
						{
							echo "<td >";
								echo "<div class=\"form-check checkbox-slider--b\">";
									echo "<label>";
										echo "<input type=\"checkbox\" class = \"paymentMember\" id = \"uid[".$value['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX.'user']."\"";
											if ($value['betalt'] > (date("Y-m-d", strtotime('+1 year'))))
											{
												echo " "."checked";
											}
							
											else if (!empty($newYear))
											{
												//Do nothing, new member year
											}
											 
										echo "><span> ".$renderCurrentMembers[1]."</span>";
									echo "</label>";
								echo "</div>";
							echo "</td>";
						}
						else
						{
							echo "<td id = \"".$key2."\">".$value[$key2]."</td>";
						}
					}
				echo "</tr>";
			}
		echo "</tbody>";
	
		echo "<tfoot>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</tfoot>";
	echo "</table>";
}

function renderCurrentMembers($membersData = null, $newYear = false)
{
	$replaceTable = getReplaceTable();
	
	$langStrings = getLangstrings();
    $renderCurrentMembers = $langStrings['renderCurrentMembers'];
	
	$th['payed']= $renderCurrentMembers[1];
	$th['firstName'] = $renderCurrentMembers[2];
	$th['sureName'] = $renderCurrentMembers[3];
	$th['address'] = $renderCurrentMembers[4];
	$th['city'] = $renderCurrentMembers[5];
	//$th['phone'] = $showAdminCadastre[6];
	$th['mobile'] = $renderCurrentMembers[7];
	$th['email'] = $renderCurrentMembers[8];
	$th['personal'] = $renderCurrentMembers[9];
	
	echo "<table class = \"table table-responsive DataTable\" witdht = \"100%\">";
		echo "<thead>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</thead>";
	
		echo "<tbody>";
			foreach ($membersData as $key => $value)
			{
				echo "<tr>";
					foreach ($th as $key2 => $value2)
					{
						if ($key2 == "personal")
						{
							$members = getMembers();
							
							echo "<td >";
								echo "<button class = \"btn btn-secondary editPersonal\" id = \"userTableKey[".$value['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX.'user']."\">".$renderCurrentMembers[9]."</button>";
							echo "</td>";
						}
						else if ($key2 == "payed")
						{
							echo "<td >";
								echo "<div class=\"form-check checkbox-slider--b\">";
									echo "<label>";
										echo "<input type=\"checkbox\" class = \"paymentMember\" id = \"uid[".$value['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX.'user']."\"";
											if ($value['betalt'] > (date("Y-m-d", strtotime('+1 year'))))
											{
												echo " "."checked";
											}
							
											else if (!empty($newYear))
											{
												//Do nothing, new member year
											}
											 
										echo "><span> ".$renderCurrentMembers[1]."</span>";
									echo "</label>";
								echo "</div>";
							echo "</td>";
						}
						else
						{
							echo "<td id = \"".$key2."\">".$value[$key2]."</td>";
						}
					}
				echo "</tr>";
			}
		echo "</tbody>";
	
		echo "<tfoot>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</tfoot>";
	echo "</table>";
}

function renderNewAccount($membersData = null)
{
	$replaceTable = getReplaceTable();
	
	$langStrings = getLangstrings();
    $renderCurrentMembers = $langStrings['renderCurrentMembers'];
	
	$th['payed']= $renderCurrentMembers[1];
	$th['firstName'] = $renderCurrentMembers[2];
	$th['sureName'] = $renderCurrentMembers[3];
	$th['address'] = $renderCurrentMembers[4];
	$th['city'] = $renderCurrentMembers[5];
	//$th['phone'] = $showAdminCadastre[6];
	$th['mobile'] = $renderCurrentMembers[7];
	$th['email'] = $renderCurrentMembers[8];
	$th['personal'] = $renderCurrentMembers[9];
	$th['testMember'] = $renderCurrentMembers[10];
	
	echo "<table class = \"table table-responsive DataTable\" witdht = \"100%\">";
		echo "<thead>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</thead>";
	
		echo "<tbody>";
			foreach ($membersData as $key => $value)
			{
				echo "<tr>";
					
				
					foreach ($th as $key2 => $value2)
					{
						if ($key2 == "personal")
						{
							$members = getMembers();
							
							echo "<td >";
								echo "<button class = \"btn btn-secondary editPersonal\" id = \"userTableKey[".$value['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX.'user']."\">".$renderCurrentMembers[9]."</button>";
							echo "</td>";
						}
						else if ($key2 == "payed")
						{
							echo "<td >";
								echo "<div class=\"form-check checkbox-slider--b\">";
									echo "<label>";
										echo "<input type=\"checkbox\" class = \"paymentMember\" id = \"uid[".$value['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX.'user']."\"";
											if ($value['betalt'] > date("Y-m-d"))
											{
												echo " "."checked";
											}
							
											elseif (!empty($newYear))
											{
												//Do nothing, new member year
											}
											 
										echo "><span> ".$renderCurrentMembers[1]."</span>";
									echo "</label>";
								echo "</div>";
							echo "</td>";
						}
						else if ($key2 == "testMember")
						{
							echo "<td>";
							
							if (!empty($value['testmember']))
							{
								echo $value['testmember']."&nbsp;&nbsp;&nbsp;";
							}
							
							
							if (date($value['testmember'], strtotime("-3 day")) <= date("Y-m-d") || empty($value['testmember']))
							{
								echo "<button id = \"".$key2."[".$value['tableKey']."]\"  class = \"btn btn-secondary testMember\"data-replace_table = \"".$replaceTable[PREFIX.'user']."\">".$renderCurrentMembers[10]."</<button></td>";
							}
							else
							{
								echo "<td></td>";
							}
						}
						else
						{
							echo "<td id = \"".$key2."\">".$value[$key2]."</td>";
						}
					}
				echo "</tr>";
			}
		echo "</tbody>";
	
		echo "<tfoot>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</tfoot>";
	echo "</table>";
}

function rerenderNewAccount()
{
	global $link;
	
	$table = "`".PREFIX."user`";
	
	$sql = "SELECT * FROM ".$table." WHERE regdate >= DATE_SUB(NOW(), INTERVAL 3 MONTH) AND betalt IS NULL OR betalt = '0000-00-00'";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $membersData3[$row['autoId']] = $row;
	}
	
	echo "<div class=\"accordion-body\">";
		renderNewAccount($membersData3, 0);
	echo "</div>";
}

function showAdminCadastre()
{
	global $link;
	
	$replaceTable = getReplaceTable();
	
	$langStrings = getLangstrings();
    $showAdminCadastre = $langStrings['showAdminCadastre'];
	
	$table = "`".PREFIX."user`";
	
	$table10 = "`".PREFIX."groups`";
	$table11 = "`".PREFIX."roles`";
	$table12 = "`".PREFIX."mission`";
	
	checkTable($table);
    
	$sql = "SELECT * FROM ".$table." WHERE betalt > CURDATE() AND betalt > DATE_SUB(CURDATE(), INTERVAL 3 MONTH) ORDER BY firstName, sureName";
	//$sql = "SELECT * FROM ".$table." WHERE betalt > CURDATE() ORDER BY firstName, sureName";
	
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $membersData2[$row['autoId']] = $row;
	}
	
	if (date("Y")."-03-31" > date("Y-m-d"))
	{
		$year = date("Y");
		$sql = "SELECT * FROM ".$table." WHERE YEAR(betalt) = '".$year."' ORDER BY firstName, sureName";
		$newYear = 1;
	}
	else
	{
		
	}
	
	$sql = "SELECT * FROM ".$table." WHERE betalt > CURDATE() ORDER BY firstName, sureName";
	
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $membersData[$row['autoId']] = $row;
	}
	
	$sql = "SELECT * FROM ".$table." WHERE betalt < CURDATE() ORDER BY firstName, sureName";
	
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $membersData4[$row['autoId']] = $row;
	}
	
	/*$th['payed']= $showAdminCadastre[1];
	$th['firstName'] = $showAdminCadastre[2];
	$th['sureName'] = $showAdminCadastre[3];
	$th['address'] = $showAdminCadastre[4];
	$th['city'] = $showAdminCadastre[5];
	//$th['phone'] = $showAdminCadastre[6];
	$th['mobile'] = $showAdminCadastre[7];
	$th['email'] = $showAdminCadastre[8];
	$th['personal'] = $showAdminCadastre[9];*/
	
	$sql = "SELECT * FROM ".$table." WHERE regdate >= DATE_SUB(NOW(), INTERVAL 3 MONTH) AND betalt IS NULL OR betalt = '0000-00-00'";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $membersData3[$row['autoId']] = $row;
	}
	
	if (count($membersData2) > 0)
	{
		echo "<div class=\"accordion\" id=\"accordionStatues\">";
			if (count($membersData3) > 0)
			{
				echo "<div class=\"accordion-item\">";
					echo "<h2 class=\"accordion-header\" id=\"headingNew\">";
						echo "<button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapseNew\" aria-expanded=\"false\" aria-controls=\"collapseNew\">";
							echo $showAdminCadastre[3];
						echo "</button>";
					echo "</h2>";
					echo "<div id=\"collapseNew\" class=\"accordion-collapse collapse \" aria-labelledby=\"headingNew\" data-bs-parent=\"#accordionStatues\">";
						echo "<div class=\"accordion-body\">";
							renderNewAccount($membersData3, 0);
						echo "</div>";
					echo "</div>";
				echo "</div>";
			}
		
			echo "<div class=\"accordion-item\">";
				echo "<h2 class=\"accordion-header\" id=\"headingOld\">";
					echo "<button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapseOld\" aria-expanded=\"false\" aria-controls=\"collapseOld\">";
						echo $showAdminCadastre[1];
					echo "</button>";
				echo "</h2>";
				echo "<div id=\"collapseOld\" class=\"accordion-collapse collapse \" aria-labelledby=\"headingOld\" data-bs-parent=\"#accordionStatues\">";
					echo "<div class=\"accordion-body\">";
						renderOldMembers($membersData4, 0);
					echo "</div>";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"accordion-item\">";
				echo "<h2 class=\"accordion-header\" id=\"headingCurrent\">";
					echo "<button class=\"accordion-button\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapeseCurrent\" aria-expanded=\"true\" aria-controls=\"collapeseCurrent\">";
						echo $showAdminCadastre[2];
					echo "</button>";
				echo "</h2>";
				echo "<div id=\"collapeseCurrent\" class=\"accordion-collapse collapse show\" aria-labelledby=\"headingCurrent\" data-bs-parent=\"#accordionStatues\">";
					echo "<div class=\"accordion-body\">";
						renderCurrentMembers($membersData, $newYear);
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		
		//renderCurrentMembers($membersData, $newYear);
	}
	else if (count($membersData3) > 0)
	{
		echo "<div class=\"accordion\" id=\"accordionStatues\">";
			echo "<div class=\"accordion-item\">";
				echo "<h2 class=\"accordion-header\" id=\"headingNew\">";
					echo "<button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapseNew\" aria-expanded=\"false\" aria-controls=\"collapseNew\">";
						echo $showAdminCadastre[3];
					echo "</button>";
				echo "</h2>";
				echo "<div id=\"collapseNew\" class=\"accordion-collapse collapse \" aria-labelledby=\"headingNew\" data-bs-parent=\"#accordionStatues\">";
					echo "<div class=\"accordion-body\">";
						renderNewAccount($membersData3, 0);
					echo "</div>";
				echo "</div>";
			echo "</div>";
		
			echo "<div class=\"accordion-item\">";
				echo "<h2 class=\"accordion-header\" id=\"headingCurrent\">";
					echo "<button class=\"accordion-button\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapeseCurrent\" aria-expanded=\"true\" aria-controls=\"collapeseCurrent\">";
						echo $showAdminCadastre[2];
					echo "</button>";
				echo "</h2>";
				echo "<div id=\"collapeseCurrent\" class=\"accordion-collapse collapse show\" aria-labelledby=\"headingCurrent\" data-bs-parent=\"#accordionStatues\">";
					echo "<div class=\"accordion-body\">";
						renderCurrentMembers($membersData, $newYear);
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		
		//renderCurrentMembers($membersData, $newYear);
	} 
	
	else
	{
		renderCurrentMembers($membersData, $newYear);
	}
	
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $adminCadastre = $langStrings['adminCadastre'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$adminCadastre[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 61vh; height : 61vh; overflow : auto;\">";
                echo "<p>";
                    showAdminCadastre();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
	print_modal();
    printFooter();
	
	echo "<script src=\"".$url."estraro/estraro.js\"></script>";
}

?>