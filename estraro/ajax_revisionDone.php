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

$_SESSION['replaceTable'] = $_POST['replaceTable'];

if ($replaceTable[$_POST['replaceTable']] == PREFIX."accounting_year")
{
	ajax_revisionDone();
}

function lockAauditReport()
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
	
	$table = "`".PREFIX."audit_report`";
	$table2 = "`".PREFIX."accounting_year`";
	
	$sql = "SELECT *, t1.tableKey as aduitKey FROM ".$table." AS t1 INNER JOIN ".$table2." t2 ON t1.year = t2.year WHERE t2.tableKey = '".mysqli_real_escape_string($link, $_POST['lockAauditReport'])."'";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$note = $row['note'];
		
		$auditKey = $row['auditKey'];
		
		$_SESSION['lockAauditReport'] = $agenda['tableKey'] = mysqli_real_escape_string($link, $_POST['lockAauditReport']);
	}
	
	// Include the main TCPDF library (search for installation path).

	require_once($path.'annualmeeting/tcpdf/tcpdf.php');

	// Extend the TCPDF class to create custom Header and Footer
	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
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

			// Logo
			$image_file = K_PATH_IMAGES.'logo_example.jpg';
			//echo $image_file."<br>";
			$image_file = $path.'img/yf.png';
			//echo $image_file."<br>";
			$this->Image($image_file, 20, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			// Set font
			$this->SetFont('helvetica', 'B', 20);
			// Title
			//$this->Cell(0, 30, 'Dagordning för årsmöte', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle('TCPDF Example 003');
	$pdf->SetSubject('TCPDF Tutorial');
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	$pdf->setPrintFooter(false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('times', 'BI', 12);

	// add a page
	$pdf->AddPage();

	// set some text to print
	$txt = <<<EOD

	EOD;

	setlocale(LC_ALL, "sv_SE");
	
	// print a block of text using Write()
	$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
	//$pdf->writeHTML($txt, true,0 , true, 15);

	$pdf->writeHTML("<br><br>".$note, true, 0, true, 0);
	
	// ---------------------------------------------------------
	mkdir($path."annualmeeting/files/temp/", 0777, true);

	//Close and output PDF document
	$pdf->Output($path."annualmeeting/files/temp/".'Revisionsberättelse.pdf', 'F');

	//============================================================+
	// END OF FILE
	//============================================================+
	
	header("Location: ".$url."annualmeeting/ajax_create_financial_statements.php");
	
}

function ajax_revisionDone()
{
	if ($_POST['lockAauditReport'])
	{
		lockAauditReport();
		return false;
	}
	
	global $link;
	
	$langStrings = getLangstrings();
    $ajax_revisionDone = $langStrings['ajax_revisionDone'];
	
	$replaceTable = getReplaceTable();
	
	echo "<div id = \"modalHeader\">";
		$ajax_revisionDone[1];
	echo "</div>";

	echo "<div id = \"modalBody\">";
		
		$table = "`".PREFIX."audit_report`";
		$table2 = "`".PREFIX."accounting_year`";
	
		$table10 = "`".PREFIX."user`";
		$table20 = "`".PREFIX."mission`";
		$table21 = "`".PREFIX."roles`";

		$sql = "SELECT * FROM ".$table20." t20 INNER JOIN ".$table21." t21 ON t20.assignment_id = t21.assignment_id INNER JOIN ".$table10." t10 ON t10.autoId = t20.uid WHERE extra = 'auditor'";
		$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$auditor[$row['uid']] = $row;
		}
    
		$sql = "SELECT * FROM ".$table2." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAccountYear'])."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
		$temp = array_flip(array_map("tim", explode(",", $row['revisionBy'])));
	
		$createAauditReport = false; 
	
		if ((int)$row['revisionBy'] !== -1)
		{
			//Do nothing for the moment, insert info to database lower in code
		}
		else
		{
			if (!array_key_exists($_SESSION['uid'], $temp))
			{
				$temp[$_SESSION['uid']] = $_SESSION['uid'];
				
				if (count($temp) == count($auditor))
				{
					$createAauditReport = true;
				}
			}
		}
	
		if (count($auditor) == 1 || $createAauditReport)
		{	
			$sql = "SELECT * FROM ".$table." AS t1 INNER JOIN ".$table2." t2 ON t1.year = t2.year WHERE t2.tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAccountYear'])."'";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

			if (mysqli_num_rows($result) == 0)
			{
				$table3 = "`".PREFIX."audit_report_default`";

				$sql = "SELECT * FROM ".$table2." t1 WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAccountYear'])."'";
				$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

				$year = $row['year'];

				$sql = "SELECT * FROM ".$table3." ORDER BY year DESC LIMIT 1";
				$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

				$siteSettings = getSiteSettings();

				$row['note'] = str_replace(array("~~associationName~~", "~~accountYear~~", "~~revisionCity~~", "~~revisionDate~~"), array($siteSettings['associationName'], $year, $siteSettings['associationPlace'], date("Y-m-d")),  $row['note']);

				if (count($auditor) == 1)
				{
					$row['note'] = str_replace("~~auditorName~~" , reset($auditor)['firstName']." ".reset($auditor)['sureName'], $row['note']);
				}
				else
				{
					$table = "<table>";
					foreach ($auditor as $key => $value)
					{
						if ($key == 0 || $key %3 == 0)
						{
							if ($key !== 0)
							{
								echo "</tr>";
							}

							$table .= "<tr>";
						}
						$table .= "<td>".$value['firstName']." ".$value['sureName']."</td>";
					}
					$table .= "</tr>";
					$table .= "</table>";

					$row['note'] = str_replace("~~auditorName~~" , $table, $row['note']);
				}

				if (count($auditor) == 1)
				{
					$row['note'] = str_replace("Vi ", "Jag ",  $row['note']);
					$row['note'] = str_replace("vi ", "jag ",  $row['note']);

					$row['note'] = str_replace("oss ", "mig ",  $row['note']);
					$row['note'] = str_replace("Vår ", "Min ",  $row['note']);
					$row['note'] = str_replace("vår ", "min ",  $row['note']);

					$row['note'] = str_replace("våra ", "minna ",  $row['note']);
					$row['note'] = str_replace("Vårt ", "Mitt ",  $row['note']);
				}

				$sql = "INSERT INTO ".$table." (year, note) VALUES ('".$year."', '".$row['note']."')";
				$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

				$tableKey = checkTable($table);

				$row['tableKey'] = $tableKey;
				
				if ((int)$row['revisionBy'] !== -1)
				{
					$temp = array_flip(array_map("tim", explode(",", $row['revisionBy'])));

					if (!array_key_exists($_SESSION['uid'], $temp))
					{
						$revisionBy = $row['revisionBy'].", ".$_SESSION['uid'];

						$sql = "UPDATE ".$table2." SET revisionBy = '".$revisionBy."' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAccountYear'])."'";
						$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
					}
				}
				else
				{
					$sql = "UPDATE ".$table2." SET revisionBy = '".$_SESSION['uid']."' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAccountYear'])."'";
					$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
				}
			}
			else
			{	
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			}

			echo "<textarea id = note[".$row['tableKey']."] class = \"tinyMceArea\" data-replace_table = \"".$replaceTable[PREFIX."audit_report"]."\">";
				echo $row['note'];
			echo "</textarea>";
		}
		else
		{
			echo $ajax_revisionDone[2];
		}
		if ((int)$row['revisionBy'] !== -1)
		{
			$temp = array_flip(array_map("tim", explode(",", $row['revisionBy'])));

			if (!array_key_exists($_SESSION['uid'], $temp))
			{
				$revisionBy = $row['revisionBy'].", ".$_SESSION['uid'];

				$sql = "UPDATE ".$table2." SET revisionBy = '".$revisionBy."' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAccountYear'])."'";
				$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
			}
		}
		else
		{
			$sql = "UPDATE ".$table2." SET revisionBy = '".$_SESSION['uid']."' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAccountYear'])."'";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		}
	
	echo "</div>";
	
	echo "<div id = \"modalFooter\">";
		echo "<button type=\"button\" class=\"btn btn-secondary closeModal\" data-dismiss=\"modal\">".$ajax_revisionDone[3]."</button>";
		echo "<button type=\"button\" class=\"btn btn-success lockAauditReport\" data-replace_table = \"".$replaceTable[PREFIX.'accounting_year']."\" data-account_key = \"".$_POST['replaceAccountYear']."\" data-dismiss=\"modal\">".$ajax_revisionDone[4]."</button>";
	echo "</div>";
	
}	