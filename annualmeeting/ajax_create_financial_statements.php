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
include_once($path."common/send_mail.php");
include_once($path."common/emailTemplate.php");

$replaceTable = getReplaceTable(false);


if ($replaceTable[$_SESSION['replaceTable']] == PREFIX."accounting_year")
{
	ajax_create_financial_statements();
}

function ajax_create_financial_statements()
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
	
	$siteSetting = getSiteSettings();
	
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
	
	$sql = "SELECT *, t1.tableKey as aduitKey FROM ".$table." AS t1 INNER JOIN ".$table2." t2 ON t1.year = t2.year WHERE t2.tableKey = '".mysqli_real_escape_string($link, $_SESSION['lockAauditReport'])."'";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$year = $row['year'];
	}
	
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
			$this->Cell(0, 30, 'Årsredovisning '.$year, 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
	
	$table11 = "`".PREFIX."roles`";
	$table12 = "`".PREFIX."mission`";
	$table13 = "`".PREFIX."user`";
	
	$sql = "SELECT * FROM ".$table11." t11 INNER JOIN ".$table12." t12 ON t11.assignment_id = t12.assignment_id INNER JOIN ".$table13." t13 ON t13.uid = t12.uid WHERE extra = 'secretery'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$secretery = $row;
	}
	
	$emailTemplate = renderEmailTemplate();
		
	$name = ucfirst($secretery['firstName'])." ".ucfirst($secretery['sureName']);
	
	$mail = $secretery['email'];

	$mobile = $secretery['mobile'];

	$mailHeader = $ajax_create_financial_statements[1];

	$message = $ajax_create_financial_statements[2];

	//$revisionURL = "<a href = ".$url."revision.php?account=".$_POST['replaceKey'].">".$url."revision.php?account=".$_POST['replaceKey']."</a>";

	/*$revisionButton = "<tr>";
		$revisionButton .= "<td style=\"padding: 0 20px 20px;\">";
			$revisionButton .= "<table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">";
				$revisionButton .= "<tr>";
					$revisionButton .= "<td class=\"button-td button-td-primary\" style=\"border-radius: 4px; background: #222222;\">";
						$revisionButton .= "<a class=\"button-a button-a-primary\" href=\"".$url."revision.php?account=".$_POST['replaceKey']."\" style=\"background: #222222; border: 1px solid #000000; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">".$informAuditor[5]."</a>";
					$revisionButton .= "</td>";
				$revisionButton .= "</tr>";
			$revisionButton .= "</table>";
		$revisionButton .= "</td>";
	$revisionButton .= "</tr>";*/

	$message = mysqli_real_escape_string($link, $message);

	$emailTemplate = str_replace("~~button~~", '', $emailTemplate);

	$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

	$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

	$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

	$headerMail =  "MIME-Version: 1.0" . "\r\n";
	$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
	queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);

	$mailHeader = $ajax_create_financial_statements[3];

	$message = $ajax_create_financial_statements[4];

	$mail = $mobile."@".$siteSetting['domain_email2sms'];;

	$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
	queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
}
?>