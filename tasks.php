 <meta name="robots" content="noindex" />

<?php
error_reporting(E_ALL);
session_start();

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/repetingie.php");

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
	start_task();
}

function start_task($repeting = true)
{
	//$repeting = true;
	$mail = true;
	$sms = true;

	global $link;
		
	$table = "`".PREFIX."settings_site`";
	
	$table2 = "`".PREFIX."cron_log`";
	
	$check["block_time_sms_start"] = "block_time_sms_start";
	$check["block_time_sms_end"] = "block_time_sms_end";
	$check["block_time_mail_start"] = "block_time_mail_start";
	$check["block_time_mail_end"] = "block_time_mail_end";
	$check["block_text"] = "block_text";
	
	$check_data = implode("' OR ".$table.".data = '", $check);

	$sql = "SELECT * FROM ".$table." WHERE ".$table.".data = '" .$check_data ."'";
    
	$result= mysqli_query($link, $sql) or die ('Error: '. __LINE__." ".$sql ." ".mysqli_error ($link));
	while ($rad = mysqli_fetch_array($result))
	{
		$settings[$rad['data']] = $rad['value'];	
	}
	
	$block_time_sms_start = date("Hi", strtotime($settings['block_time_sms_start']));
	$block_time_sms_end = date("Hi", strtotime($settings['block_time_sms_end']));
	$block_time_mail_start = date("Hi", strtotime($settings['block_time_mail_start']));
	$block_time_mail_end = date("Hi", strtotime($settings['block_time_mail_end']));
	
	$table = "cron_log";
	$event = "Cron jobbet anropat....";
	
	$sql = "INSERT INTO $table2 (log_date, event) VALUES (NOW(), '".$event."')";
    
	$result= mysqli_query($link, $sql) or die ('Error: '. __LINE__." ".$sql ." ".mysqli_error ($link));
	echo __LINE__." Ska eventuellt köra återkommande aktiviter....<br>";
	if ($_POST['repeting'] || $repeting)	
	{
		run_repeting();
	}
	echo __LINE__." Ska eventuellt köra mail....<br>";
	if ($_POST['mail'] || $mail)	
	{
		echo __LINE__." Ska eventuellt köra mail....<br>";
		run_mail($block_time_mail_start, $block_time_mail_end);
	}
	
	if ($_POST['sms'] || $sms)	
	{
		echo __LINE__." Ska eventuellt köra sms....<br>";
		run_sms($block_time_sms_start, $block_time_sms_end);
	}
}

function lastday($month = '', $year = '') {
   if (empty($month)) {
      $month = date('m');
   }
   if (empty($year)) {
      $year = date('Y');
   }
   $result = strtotime("{$year}-{$month}-01");
   $result = strtotime('-1 second', strtotime('+3 month', $result));
   return date('Y-m-d', $result);
}

function run_repeting()
{
	global $link;
	
	$start_date = date("Y-m-d");
	$end_date = lastday();
	
	unset($_SESSION['nextdate']);
	calcdate(2, $start_date, $end_date);
	unset($_SESSION['nextdate']);
}

function run_mail($block_time_mail_start = NULL, $block_time_mail_end = NULL)
{
	global $link;
	
	$time_now = date("Hi");
	
	$block_mail = false;
	
	if ($time_now >= $block_time_mail_start && $time_now <= $block_time_mail_end)
	{
		$block_mail = true;	
	}
	else if ( $block_time_mail_end >= $time_now )
	{
		$block_mail = true;	
	}
	
	if ($block_mail == false)
	{
		$table = "`".PREFIX."log`";
		$table2 = "`".PREFIX."user`";
		
		$sql = "SELECT *, t1.autoId as autoId FROM ".$table." t1 RIGHT JOIN ".$table2." t2 ON t1.user = t2.uid WHERE (sent IS NULL OR sent = '' OR sent = '0000-00-00 00:00:00') AND t1.email = '1'";	
		$result= mysqli_query($link, $sql) or die ('Error: '. __LINE__." ".$sql ." ".mysqli_error ($link));
		
		while ($rad = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$to = $rad['to'];
			$subject = $rad['header'];
			$message = $rad['message'];
			$from = $rad['email'];
			$headers = 'From: '.$rad['firstName']. " ".$rad['sureName'].'<'.$from . ">;\r\n" . 
 				'Reply-To: '.$from . ";\r\n" .
 				'X-Mailer: PHP/' . phpversion().";";
            $headers = $rad['header_mail'];
            /*
			echo __LINE__." ".$to."<br>";
			echo __LINE__." ".$subject."<br>";
			//echo __LINE__." ".$message."<br>";
			echo __LINE__." ".$headers."<br>";/**/
			
			if (mail($to, $subject, $message, $headers, '-finfo@young-friends.org'))
			{
				$sql2 = "UPDATE $table SET sent = NOW() WHERE autoId = '".$rad['autoId']."'";
				echo __LINE__." ".$sql2."<br>";
				$result_2= mysqli_query($link, $sql2) or die ('Error: '. __LINE__." ".$sql2 ." ".mysqli_error ($link));
			}
		}
	}
}

function run_sms($block_time_sms_start = NULL, $block_time_sms_end = NULL)
{
	global $link;
		
	$time_now = date("Hi");
	$block_sms = false;
	
	if ($time_now >= $block_time_sms_start && $time_now <= $block_time_sms_end)
	{
		$block_sms = true;	
	}
	else if ( $block_time_sms_end >= $time_now )
	{
		$block_sms = true;	
	}	
	
	if ($block_sms == false)
	{
		$table = "`".PREFIX."log`";
		$table2 = "`".PREFIX."user`";
		$sql = "SELECT *, t1.autoId as autoId FROM ".$table." t1 JOIN ".$table2." t2 ON t1.user = t2.uid WHERE (sent IS NULL OR sent = ''  OR '0000-00-00') AND sms = '1'";
		$result= mysqli_query($link, $sql) or die ('Error: '. __LINE__." ".$sql ." ".mysqli_error ($link));
		
		/*if (mysqli_num_rows($result) > 0)
		{
			$table3 = "cron_log";
			$event = "Blockerade sms sända.....";
	
			$sql = "INSERT INTO $table3 (log_date, event) VALUES (NOW(), '".$event."')";
			$result2= mysqli_query($link, $sql) or die ('Error: '. __LINE__." ".$sql ." ".mysqli_error ($link));	
		}*/
		
		while ($rad = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			
			$to = $rad['to'];
			$subject = $rad['header'];
			$message = $rad['message'];
			$from = $rad['epost'];
			$headers = "From: info@young-friends.org\r\n  
 				Reply-To: info@young-friends.org\r\n" .
 				'X-Mailer: PHP/' . phpversion();
			$headers = $rad['header_mail'];
			
			if (mail($to, $subject, $message, $headers))
			{
				$sql2 = "UPDATE $table SET sent = NOW() WHERE autoId = '".$rad['autoId']."'";
				$result_2= mysqli_query($link, $sql2) or die ('Error: '. __LINE__." ".$sql2 ." ".mysqli_error ($link));
			}
		}
	}
}
?>