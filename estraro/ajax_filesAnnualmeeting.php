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
include_once($path."common/emailTemplate.php");
include_once($path."common/send_mail.php");

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

function syncTempFiles()
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
		
	if (!file_exists($path.$_SESSION['folder']."source/Bokslut.php"))
	{
		$langStrings = getLangstrings();
		$showAjax_accounting_year = $langStrings['showAjax_accounting_year'];
		$ajax_create_financial_statements = $langStrings['ajax_create_financial_statements'];

		$th['verifierId'] = $showAjax_accounting_year[8];
		$th['note'] = $showAjax_accounting_year[9];
		$th['income'] = $showAjax_accounting_year[10];
		$th['expence'] = $showAjax_accounting_year[11];

		$table = "`".PREFIX."audit_report`";
		$table2 = "`".PREFIX."accounting_year`";
		$table3 = "`".PREFIX."accounting`";

		$html = "<style>
		  table {
			border-collapse: collapse;
		  }
		  td,
		  th {
			padding: 10px;
			border-bottom: 2px solid #000000;
			text-align: center;
		  }
		</style><br><br>";

		$year = date("Y", strtotime('-1 year'));
		
		$sql = "SELECT * FROM ".$table3." t3 INNER JOIN ".$table2." t2 ON t3.accounting = t2.autoId WHERE t2.year = '".$year."' ORDER BY t3.autoId";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$data[] = $row;
		}

		$i = 1;

		$sql = "SELECT * FROM ".$table2." WHERE year = '".$year."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$startBalance = $row['startBalance'];

			$html .= $showAjax_accounting_year[1]." ".$row['startBalance']." kr<br><br>";
		}

		$accounting = reset($data)['accounting'];

		$sql = "SELECT SUM(income) as income, SUM(expence) as expence FROM ".$table3." WHERE accounting = '".$accounting."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$income = $row['income'];

			$expence = $row['expence'];
		}

		$html .= "<table>";
			$html .="<thead>";
				$html .= "<tr>";
					foreach ($th as $key => $value)
					{
						$html .= "<th>". $value."</th>";
					}
				$html .= "</tr>";
			$html .= "</thead>";

			$html .= "<tbody>";

			foreach ($data as $key => $value)
			{	
				$html .= "<tr>";

				foreach ($th as $sub_key => $sub_value)
				{
					if ($sub_key == "verifierId")
					{
						$html .= "<td>".$i."</td>";
						$i++;
					}
					else
					{
						$html .= "<td>".$value[$sub_key]."</td>";
					}	
				}

				$html .= "</tr>";
			}

			$html ."</tbody>";

		$html .= "</table><br><br>";

		$html .= $showAjax_accounting_year[14]." ".$income." kr<br><br>";
		$html .= $showAjax_accounting_year[15]." ".$expence." kr<br><br>";

		$sum = (float)$startBalance+(float)$income-(float)$expence;

		$html .= $showAjax_accounting_year[18]." <b>".$sum." kr</b><br><br>";

		echo $html."<br>";

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
				$this->Cell(0, 30, 'Årsredovisning '+$year, 0, false, 'C', 0, '', 0, false, 'M', 'M');
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

		$pdf->writeHTML("<br><br>".$html, true, 0, true, 0);

		// ---------------------------------------------------------
		mkdir($path."annualmeeting/files/temp/", 0777, true);

		//Close and output PDF document
		echo __LINE__." ".$path."annualmeeting/files/temp/".'Bokslut.pdf';
		$pdf->Output($path."annualmeeting/files/temp/".'Bokslut.pdf', 'F');

		//============================================================+
		// END OF FILE
		//============================================================+
	}
	
	$directory = $path.'annualmeeting/files/temp/';
	$scanned_directory = array_diff(scandir($directory), array('..', '.'));
	
	foreach ($scanned_directory as $key => $value)
	{
		rename($directory.$value, $path.$_SESSION['folder']."source/".$value);
	}
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME']))
{
	$_SESSION['folder'] ="annualmeeting/files/".$_POST['replaceAgenda']."/";
	
	
	mkdir($path.$_SESSION['folder']."source", 0777, true);
	mkdir($path.$_SESSION['folder']."thumbs", 0777, true);
	
	if ($_POST['syncAgenda'])
	{
		syncTempFiles();
	}
	
	echo "<div id = \"modalHeader\">";
	
	echo "</div>";
	
	echo "<div id = \"modalBody\">";
		echo "<iframe src=\"".$url."ResponsiveFilemanager/filemanager/dialog.php?field_id=name\" style = \"height : 60vh; width : 100%;\"></iframe>";
		echo "<input type = \"hidden\" id = \"syncAnnualmeeting\" value = \"".$_POST['replaceAgenda']."\">";
	echo "</div>";
}