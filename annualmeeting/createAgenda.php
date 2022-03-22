<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

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
        $this->Cell(0, 30, 'Dagordning för årsmöte', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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

$table = "`".PREFIX."annualMeeting_agenda`";
$table10 = "`".PREFIX."user`";

$sql = "SELECT * FROM ".$table." ORDER BY meetingDay DESC LIMIT 1";
$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$agenda = $row;
}


// set some text to print
$txt = <<<EOD

EOD;

setlocale(LC_ALL, "sv_SE");
$txt = "Young Friends\r\n\r\n".ucfirst(utf8_encode(strftime('%A', strtotime($agenda['meetingDay']))))." ".$agenda['meetingDay']. ", plats : ".$agenda['place'].", klockan ".substr($agenda['time'], 0,5)."\r\n\r\n\r\n";
//echo utf8_encode(strftime('l', strtotime($agenda['meetingDay'])));

// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
//$pdf->writeHTML($txt, true,0 , true, 15);


$pdf->writeHTML($agenda['note'], true, 0, true, 0);

// ---------------------------------------------------------
mkdir($path."annualmeeting/files/".$agenda['tableKey']."/source/", 0777, true);
mkdir($path."annualmeeting/files/".$agenda['tableKey']."/thumbs/", 0777, true);

//Close and output PDF document
$pdf->Output($path."annualmeeting/files/".$agenda['tableKey']."/source/".'agenda.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+


// Recipient 
$to = 'andwallin@gmail.com'; 
 
// Sender 
$from = 'info@young-friends.org'; 
$fromName = 'Young Friends'; 
 
// Email subject 
$subject = 'PHP Email with Attachment by CodexWorld';  

// Header for sender info 
$headers = "From: $fromName"." <".$from.">"; 

// Email body content 
$message = $htmlContent = ' 
    <h3>PHP Email with Attachment by CodexWorld</h3> 
    <p>This email is sent from the PHP script with attachment.</p> 
'; 

// Boundary 

$file = $path."annualmeeting/files/".$agenda['tableKey']."/source/".'agenda.pdf'; 

$semi_rand = md5(time());  
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
 
// Headers for attachment  
$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
 
// Multipart boundary  

 
$files = array_diff(scandir($path."annualmeeting/files/".$agenda['tableKey']."/source/"), array('..', '.'));

foreach ($files as $key => $file)
{
	$semi_rand = md5(time());  
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	
	$file = $path."annualmeeting/files/".$agenda['tableKey']."/source/".$file;
	// Preparing attachment 
	if(!empty($file) > 0){ 
		if(is_file($file)){ 
			$message2 .= "--{$mime_boundary}\n"; 
			$fp =    @fopen($file,"rb"); 
			$data =  @fread($fp,filesize($file)); 

			@fclose($fp); 
			$data = chunk_split(base64_encode($data)); 
			$message2 .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" .  
			"Content-Description: ".basename($file)."\n" . 
			"Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" .  
			"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
		} 
	} 
	$message2 .= "--{$mime_boundary}--"; 
}

if (!empty($files))
{
	$langStrings = getLangstrings();
	$annualmeeting = $langStrings['annualmeeting'];

	$sql = "SELECT * FROM ".$table10." WHERE betalt > CURDATE() AND uid > 0 ORDER BY firstName, sureName";
	$sql = "SELECT * FROM ".$table10." WHERE betalt > CURDATE() AND uid = 1 ORDER BY firstName, sureName";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 

	while ($row = mysqli_fetch_array($result))
	{
		$emailTemplate = renderEmailTemplate();

		$name = ucfirst($row['firstName'])." ".ucfirst($row['sureName']);

		$mail = $row['email'];

		$mobile = $row['mobile'];

		$mailHeader = $annualmeeting[3];

		$message = mysqli_real_escape_string($link, $annualmeeting[4]);

		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($htmlContent, 0, 150), $emailTemplate);
		
		$emailTemplate = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
		"Content-Transfer-Encoding: 7bit\n\n" . $emailTemplate . "\n\n";  

		$headerMail = 'From: Young Friends <info@young-friends.org>' . "\r\n";

		$headerMail .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		$headerMail = 'From: Young Friends <info@young-friends.org>' . "\r\n";
		$headerMail .=  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
		// More headers

		queMail(0, $mail, $mailHeader, $message, $emailTemplate.$message2, 1, null, $headerMail);
	}

	include_once($path."tasks.php");
	start_task(false);
}