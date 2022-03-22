<?php
ob_start();

if (!isset($_SESSION))
{
   session_start(); 
    include_once("common/db.php");
}
//error_reporting(E_ERROR);
//ini_set("display_errors", 1);

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__))
{	
	include_once($path."common/db.php");
	include_once($path."common/crypto.php");
	include_once($path."common/userData.php");
	include_once($path."common/calendar.php");
	include_once($path."common/funktioner-yf.php");
	echo "<!DOCTYPE html>";
	echo "<html lang=\"sv\">";
	
	echo "<head>";
		echo "<meta charset=\"utf-8\">";
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
		echo "<link rel=\"icon\" href=\"image/favicon.png\" type=\"image/vnd.microsoft.icon\"/>";
		echo "<meta name=\"description\" content=\"Young Friends är en ideell förening i Helsingborg som vänder sig till personer mellan 25 - 45 år, vi har medlemmar från Landskrona i söder och vi sträcker oss till Ängelholm i norr. Alla som vill utöka sin bekantskapskrets är välkomna................\">";
		echo "<meta name=\"keywords\" content=\"young, friends, singel, vänner, helsingborg, ängelholm, landskrona, aktiviteter, festa, utflykter, young friends, Young Friends, Vänpunkten, cafeträffar, caféträffar, \">";
		echo "<meta name=\"author\" content=\"\">";
	
		echo "<title>Young Friends</title>";
		echo "<link rel=\"stylesheet\" href=\"//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css\">";
		//echo "<script src=\"//code.jquery.com/jquery-1.10.2.js\"></script>";
		//echo "<script src=\"//code.jquery.com/ui/1.11.2/jquery-ui.js\"></script>";
		echo "<!-- Bootstrap Core CSS -->";
		echo "<link href=\"css/bootstrap.min.css\" rel=\"stylesheet\" type=\"text/css\">";
		echo "<script src=\"common/datepicker-sv.js\"></script>";
		echo "<script type=\"text/javascript\" src=\"js/bootstrap.js\"></script>";
	
	   
		echo "<script type=\"text/javascript\" src=\"javascript2.js\"></script>";
		echo "<script src=\"ajax.js\" type=\"text/javascript\"></script>";
		echo "<!-- Fonts -->";
		echo "<link href=\"font-awesome/css/font-awesome.min.css\" rel=\"stylesheet\" type=\"text/css\">";
		echo "<link href=\"css/nivo-lightbox.css\" rel=\"stylesheet\" />";
		echo "<link href=\"css/nivo-lightbox-theme/default/default.css\" rel=\"stylesheet\" type=\"text/css\" />";
		echo "<link href=\"css/animate.css\" rel=\"stylesheet\" />";
		echo "<!-- Squad theme CSS -->";
		echo "<link href=\"css/style.css\" rel=\"stylesheet\">";
		echo "<link href=\"color/default.css\" rel=\"stylesheet\">";
		
		echo "<style>";
			echo "textarea { vertical-align: top; }";
		echo "</style>";
	
	echo "</head>";
	echo "<body data-spy=\"scroll\">";
	
	
	echo "<div class=\"container\">";
				echo "<ul id=\"gn-menu\" class=\"gn-menu-main\">";
					echo "<li class=\"gn-trigger\">";
						echo "<a class=\"gn-icon gn-icon-menu\"><span>Menu</span></a>";
						echo "<nav class=\"gn-menu-wrapper\">";
							echo "<div class=\"gn-scroller\">";
								echo "<ul class=\"gn-menu\">";
									echo "<!--";
									echo "<li class=\"gn-search-item\">";
										echo "<input placeholder=\"Search\" type=\"search\" class=\"gn-search\">";
										echo "<a class=\"gn-icon gn-icon-search\"><span>Search</span></a>";
									echo "</li>-->";
									echo "<li>";
										echo "<a href=\"index.php#service\" class=\"gn-icon gn-icon-download\">Aktiviteter</a>";
									echo "</li>";
									echo "<li>";
										echo "<a href=\"more_info.php\" class=\"gn-icon gn-icon-download\">Om föreningen</a>";
									echo "</li>";
									
									echo "<li>";
										echo "<a href=\"faq.php\" class=\"gn-icon gn-icon-download\">Vanliga frågor</a>";
									echo "</li>";
									echo "<li>";
										echo "<a href=\"stadgar.php\" class=\"gn-icon gn-icon-download\">Stadgar</a>";
									echo "</li>";
									
									if (!empty($_SESSION['id']) && !isset($_SESSION['testmember']))
									{
										echo "<li><a href=\"new_activity.php\" class=\"gn-icon gn-icon-cog\">Editera/Ny aktivitet</a></li>";
										echo "<li><a href=\"matrikel.php\" class=\"gn-icon gn-icon-download\">Matrikel</a></li>";
										echo "<li>";
											echo "<a href=\"sms.php\" class=\"gn-icon gn-icon-archive\">SMS/Mail</a>";
										echo "</li>";
										echo "<li>";
											echo "<a href=\"tournament.php\" class=\"gn-icon gn-icon-cog\">Bangolfsturnering</a>";
										echo "</li>";
										echo "<li>";
											echo "<a href=\"personal.php\" class=\"gn-icon gn-icon-cog\">Inställningar</a>";
										echo "</li>";
										if (!empty($_SESSION['it-admin']))
										{
											echo "<li>";
												echo "<a href=\"adminsite.php\" class=\"gn-icon gn-icon-cog\">Site-admin</a>";
											echo "</li>";
											echo "<li>";
												echo "<a href=\"adminroller.php\" class=\"gn-icon gn-icon-cog\">Roller-admin</a>";
											echo "</li>";
										}
									}
									else if (isset($_SESSION['testmember']))
									{
										echo "<li>";
											echo "<a href=\"personal.php\" class=\"gn-icon gn-icon-cog\">Inställningar</a>";
										echo "</li>";
									}
									else
									{
										echo "<li>";
											echo "<a href=\"reg.php\" class=\"gn-icon gn-icon-cog\">Skapa konto</a>";
										echo "</li>";	
									}
									
								echo "</ul>";
							echo "</div><!-- /gn-scroller -->";
						echo "</nav>";
					echo "</li>";
					
					if (!isset($_SESSION['id']))
					{
						echo "<li><a id=\"login\" href=\"http://www.young-friends.org\">Logga in</a></li>";
					}
					else
					{
						echo "<li><a href=\"logout.php\">Logga ut</a></li>";
					}
					
					echo " <li><ul>";
					echo "<li><a href=\"index.php\">Young Friends</a></li>";
					echo "</ul></li>";
					echo "<!--";
					echo "<li><ul class=\"company-social\">";
								echo "<li class=\"social-facebook\"><a href=\"#\" target=\"_blank\"><i class=\"fa fa-facebook\"></i></a></li>";
								echo "<li class=\"social-twitter\"><a href=\"#\" target=\"_blank\"><i class=\"fa fa-twitter\"></i></a></li>";
								echo "<li class=\"social-dribble\"><a href=\"#\" target=\"_blank\"><i class=\"fa fa-dribbble\"></i></a></li>";
								echo "<li class=\"social-google\"><a href=\"#\" target=\"_blank\"><i class=\"fa fa-google-plus\"></i></a></li>";
							echo "</ul>	</li>";
					 echo "-->";
				echo "</ul>";
				
		echo "</div>";
		echo "<!-- Section: intro -->";
	  echo "<div class=\"container\">";
				echo "<!--<div class=\"row\">-->";
				echo "<br><br><br><br>";
					echo "<div class=\"col-md-12 col-lg-12 bg-yf\">";
			
						echo "<br>";
								start();
							
						echo "</div>";
				
				echo "<!--</div>-->";
			echo "</div>";
		echo "<footer>";
			echo "<div class=\"container\">";
				echo "<div class=\"row\">";
					echo "<div class=\"col-md-12 col-lg-12\">";
			
						echo "<p>Copyright &copy; 2014-".date("Y")." <a href=\"http://www.young-friends.org\">Young Friends</a>, Helsingborg</p>";
					echo "</div>";
				echo "</div>";	
			echo "</div>";
		echo "</footer>";
	
		echo "<!-- Core JavaScript Files -->";
		echo "<script src=\"js/jquery.easing.min.js\"></script>";	
		echo "<script src=\"js/classie.js\"></script>";
		echo "<script src=\"js/gnmenu.js\"></script>";
		echo "<script src=\"js/jquery.scrollTo.js\"></script>";
		echo "<script src=\"js/nivo-lightbox.min.js\"></script>";
		echo "<script src=\"js/stellar.js\"></script>";
		echo "<!-- Custom Theme JavaScript -->";
	  
		echo "<script src=\"common/javascript.js\"></script>";
		echo "<script src=\"js/custom.js\"></script>";
		
		echo "<script>";
		echo "$(function() {";
			echo "$( \"#datumstart\" ).datepicker( $.datepicker.regional[ \"sv\" ] );";
			echo "$( \"#datumstart\" ).datepicker();";
			
			echo "$( \"#datumstop\" ).datepicker( $.datepicker.regional[ \"sv\" ] );";
			echo "$( \"#datumstop\" ).datepicker();";
			
			echo "$( \"#permdatumstart\" ).datepicker( $.datepicker.regional[ \"sv\" ] );";
			echo "$( \"#permdatumstart\" ).datepicker();";
			
			echo "$( \"#permdatumstop\" ).datepicker( $.datepicker.regional[ \"sv\" ] );";
			echo "$( \"#permdatumstop\" ).datepicker();";
		echo "});";
		echo "</script>";
	echo "</body>";
	
	
	echo "</body>";
	echo "</html>";
}

//*****************************
/*

$_SESSION[tid]
$_SESSION['nextdate']
$_SESSION[datumstop] 
$_SESSION[activity]
$_SESSION[week]
$_SESSION[weekday]
$_SESSION['repeting']

*/
//******************************
//För att bestäma första datumet som en aktivitet infaller på

global $link, $block_dates, $rep_type;

function calcdate($repeting_activity = 0, $remotestart = '', $remotestop = '')
{
    global $link, $block_dates, $rep_type;
	
	$table = "`".PREFIX."block_date`";
	$table2 = "`".PREFIX."activities`";
	$table3 = "`".PREFIX."types`";
	
	//Vid användning av vår bakgrundsskript
	if (empty($_POST['repeting']))
	{
		$_POST['repeting'] = $repeting_activity;
	}
	
	$t = 0;
	if (isset($_SESSION['nextdate'][0]))
	{
		$t = count($_SESSION['nextdate'], COUNT_RECURSIVE); 
	}
	if ($_POST['repeting'] != 0)
	{
		$datumstart = $_POST['permdatumstart'];
		$datumstop = $_POST['permdatumstop'];
		
		//Vid användning av vår bakgrundsskript
		if (empty($_POST['permdatumstart']))
		{
			$datumstart = $remotestart;
		}
		if (empty($_POST['permdatumstop']))
		{
			$datumstop = $remotestop;
		}
		$t_id = $_POST['repeting'];
		
		//Hämta ut dagar som är blockerade av någon anledning
		$sql= "SELECT * FROM ".$table." WHERE (blockDate BETWEEN '".$datumstart."' AND '".$datumstop."')";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$block_dates[$rad['passadate']] = $row['comment'];	
		}
		
		$sql= "SELECT * FROM ".$table2." WHERE (datum BETWEEN '".$datumstart."' AND '".$datumstop."') AND rep_activity_type = '".$t_id."'";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$block_dates[$rad['datum']] = $row['comment'];	
		}
		
		$sql= "SELECT * FROM ".$table3." WHERE t_id = $t_id";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC );
		
		$weekday = $row['weekday'];
		if ($weekday >= 7)
		{
			$weekday = 0;	
		}
		$week = $row['week'];
		$tid = $row['time'];
		$_SESSION['tid'] = $tid;
		$datumstart = $_POST['permdatumstart'];
		$datumstop = $_POST['permdatumstop'];
		
		//Vid användning av vår bakgrundsskript
		if (empty($_POST['permdatumstart']))
		{
			$datumstart = $remotestart;
		}
		if (empty($_POST['permdatumstop']))
		{
			$datumstop = $remotestop;
		}
		
		//echo "datumstart = $datumstart datumstop = $datumstop week = $week<br>";
		$store = $datumstart;
		$datumstart = explode("-",$datumstart);
		$tempdate = explode(" ",date("w W", mktime(0,0,0,$datumstart[1], 1, $datumstart[0])));
		$datumstart = explode("-",date("Y-m-d", mktime(0,0,0,$datumstart[1], 1, $datumstart[0])));
		
		$datumstop = explode("-",$datumstop);
		$temp = date("t", mktime(0,0,0,$datumstop[1], 1, $datumstop[0]));
		$datumstop =date("Y-m-d", mktime(0,0,0,$datumstop[1], $temp, $datumstop[0]));
		$activity = $_POST['repeting'];	
		//echo"datumstop = $datumstop <br>";
		$_SESSION['tid'] = $nt['time'];
		
		$rep_type = $_POST['repeting'];
		unset($_POST['repeting']);
		
		//Temp lösning
		unset($_SESSION['nextdate']);
		unset($_SESSION['datumstop']); 
		unset($_SESSION['activity']);
		unset($_SESSION['week']);
		unset($_SESSION['weekday']);
		unset($_SESSION['repeting']);
		unset($_POST['submit']);
	}
	
	else if ($_POST['weekday'] != 0)
	{
		$weekday = $_POST['weekday'];
		
		if ($weekday == 7)
		{
			$weekday = 0;	
		}
		$week = $_POST['week'] ;
		$datumstart = $_POST['datumstart'];
		$store = $datumstart;
		
		$datumstop = $_POST['datumstop'];
		$t_id = $_POST['activity'];
		$activity = $_POST['activity'];	
		$datumstart = explode("-",$datumstart);
		
		if($_POST['checkbox'])
		{
			$datumstart[2] = 1;
			$tempdate = explode(" ",date("w W", mktime(0,0,0,$datumstart[1], '1', $datumstart[0])));
			$datumstart = explode("-",date("Y-m-d", mktime(0,0,0,$datumstart[1], '1', $datumstart[0])));
		} 
		else
		{
			$tempdate = explode(" ",date("w W", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0])));
		}
	}
	else if (isset($_SESSION['nextdate']))
	{
		//Om vi vill bestämma datum med hjälp av andra parameterar kan det ske här
		$datumstart = $_SESSION['nextdate'][$t-1];
		$datumstart = explode("-",$datumstart);
		//Öka med en vecka för att ska kunna bestämma nästa datum....
		$datumstart = date("Y-m-d", mktime(0,0,0,$datumstart[1], $datumstart[2]+7, $datumstart[0]));
		$store = $datumstart;
		$datumstop = $_SESSION['datumstop']; 
		$activity = $_SESSION['activity'];
		$week = $_SESSION['week'];
		$weekday = $_SESSION['weekday'];
		$datumstart = explode("-",$datumstart);
		
		$tempdate = explode(" ",date("w W", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0])));
		unset($_POST['submit']);
		
	}
	
	if (!isset($_SESSION['nextdate']))
	{
		$_SESSION['datumstop'] = $datumstop; 
		$_SESSION['activity'] = $activity;
		$_SESSION['week'] = $week;
		$_SESSION['weekday'] = $weekday;
		$_SESSION['repeting'] = $t_id ;
	}
	
	//Bestäma första kommande veckodag, justera även eventull vecka som det handlar om
	
	$tempweekday = $tempdate[0];
	if ($tempweekday == $weekday)
	{
		$tempweek = $tempdate[1];
	}
	else if ($tempweekday < $weekday)
	{
		$tempdayweek = $weekday;
		$datumstart[2] = $datumstart[2]  + (-$tempweekday + $weekday); //Flytta fram datumet till rätt veckodag....
		$tempweek =date("W", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0]));
	}
	else
	{
		$datumstart[2] = $datumstart[2] + 7 + (-$tempweekday + $weekday); //Flytta fram aktuellt datum en vecka samt justera till rätt veckodag....
		$tempweek =date("W", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0]));
	}
	
	switch ($week){
	case 1:
		//Gör inget då ska inträffa varje vecka
		break;
	case 2:
		//Jämna veckor
		if ($tempweek % 2 != 0)
		{
			$tempweek = $tempweek + 1;
			$datumstart[2] = $datumstart[2] + 7; //Flytta fram aktuellt datum en vecka...
		}
		else
		{
			//Gör ingen justering	
		}
		break;
	case 3; 
		//Udda veckor
		if ($tempweek % 2 == 0)
		{
			$tempweek = $tempweek + 1;
			$datumstart[2] = $datumstart[2] + 7; //Flytta fram aktuellt datum en vecka...
		}
		else
		{
			//Gör ingen justering	
		}
		break;
	case 4:
		//Sista söndagen, hur ska detta lösas? Före alla beräkningar innan?
		// Vi måste först bestämma datum för den första söndagen 
		if ($tempweekday == 0)
		{
			$datumstart[2] = $datumstart[2];
			//echo "Första söndagen som vi räknar ifrån är " .$datumstart[2] ."<br>";
		}
		else
		{
			$datumstart[2] = $datumstart[2] + 7 - $tempweekday;
			//echo "Första söndagen som vi räknar ifrån är " .$datumstart[2] ."<br>";
		}
		$start = $datumstart[2];
		$temp = $start + 1;
		//echo " start = $start temp = $temp ";
		
		while ($start < $temp )	
		{
			if ($temp > $start + 1)
			{
				$start = $temp;
			}
			$datumstart[2] = $datumstart[2] + 7;
			$temp = date("d", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0]));
			
			//echo " start = $start temp = $temp ";
		}
		$datumstart[2] = $start;
		//echo "Sista söndagen bör infalla på $start <br>";
		break;
	case 5:
		//Gör inget då det handlar om första veckan
		break;
	case 6:
		//2:a veckan
		$datumstart[2] = $datumstart[2] + 7; //Flytta fram aktuellt datum en vecka...
		break;
	case 7:
		//3:de veckan
		$datumstart[2] = $datumstart[2] + 14; //Flytta fram aktuellt datum en vecka...
		break;
	case 8:
		//4:de veckan
		$datumstart[2] = $datumstart[2] + 21; //Flytta fram aktuellt datum en vecka...
		break;
	case 9:
		//4:de veckan
		$datumstart[2] = $datumstart[2] + 28; //Flytta fram aktuellt datum en vecka...
		break;
	}
	
	//Testa och se så att det är inom angiven månad
	$test = explode("-",$store);
	$temp = date("Y-m-d", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0]));
	$temp2 = explode("-",$temp);
	$temp3 = $temp;
	$temp = date("Ymd", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0]));
	$temp_test25 = date("Y-m-d", mktime(0,0,0,$datumstart[1], $datumstart[2], $datumstart[0]));
	$temp2 = str_replace("-","",$datumstop);
	
	if ($temp > $temp2)
	{
		unset($_SESSION['datumstop']); 
		unset($_SESSION['activity']);
		unset($_SESSION['week']);
		unset($_SESSION['weekday']);
		unset($_POST['submit']);
		reg(NULL, $rep_type);
	}
	else
	{
		$t25 = $temp_test25;
		$_SESSION['nextdate'][] = $temp3;	
		calcdate();
	}
}


//******************************
//För att testa och se om vi har info i alla de fält som krävs...

function test($a ,$b ,$c ,$d ,$e ,$f1 ,$f2)
{
	global $link;
	
	$ta = count($a);
	if ($ta == 0)
	{
		return false;
	}
	$y = -1;
	
	foreach ($a as $z)
	{
		$y++;
		
		if (!empty($a[$y]) && !empty($b[$y]) /*&& !empty($c[$y])*/ && !empty($d[$y])&& !empty($e[$y]) && (!empty($f1[$y]) || !empty($f2[$y])))
		{
			//Gör inget då det finns info i fält som krävs
		}
		else
		{
			echo "a = $a[$y] b = $b[$y] c = $c[$y] d = $d[$y] e = $e[$y] f1 = $f1[$y] f2 = $f2[$y]<br>";
			echo"Klarade INTE steg $y<br>";
			return false;
		}
	}
	return true;
}

//******************************
//För att rega de olika aktiviterna
function reg($run = 0, $rep_typ = 0)
	{
		global $link;
		
		//Saknas det sessions id sänd till sidan för inloggning	
		if (isset($_POST['places']))
		{
			unset($_POST['places']);
			header("Location:adminplatser.php");
		}
		
		$table = "`".PREFIX."block_date`";
		$table2 = "`".PREFIX."activities`";
		$table3 = "`".PREFIX."types`";
		$table4 = "`".PREFIX."texter`";
		$table5 = "`".PREFIX."user`";
		$table6 = "`".PREFIX."registered`";
	
		//Hämta ut dagar som är blockerade av någon anledning
	
		$year = date("Y")."-";
		$year2 = date('Y', strtotime('+1 year'))."-";
	
		$sql= "SELECT * FROM (SELECT CASE WHEN LENGTH(blockdate) = 5 THEN CONCAT('".$year."', blockDate) ELSE blockDate END as blockDate  FROM ".$table.") as t WHERE (	blockDate BETWEEN '".reset($_SESSION['nextdate'])."' AND '".end($_SESSION['nextdate'])."')";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		while ($row =mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$block_dates[$row['blockDate']] = $row['comment'];	
		}
	
		$sql= "SELECT * FROM (SELECT CASE WHEN LENGTH(blockdate) = 5 THEN CONCAT('".$year2."', blockDate) END as blockDate FROM ".$table.") as t WHERE (blockDate BETWEEN '".reset($_SESSION['nextdate'])."' AND '".end($_SESSION['nextdate'])."')";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		while ($row =mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$block_dates[$row['blockDate']] = $row['comment'];	
		}
		
		$sql= "SELECT * FROM ".$table2." WHERE (datum BETWEEN '".reset($_SESSION['nextdate'])."' AND '".end($_SESSION['nextdate'])."') AND rep_activity_type = '".$_SESSION['repeting']."'";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		while ($rad=mysqli_fetch_array($result))
		{
			$block_dates[$rad['datum']] = $rad['comment'];	
		}
		
		//Hämta all data som har sänt till sidan, varför det inte fungerar med array of post är en bra fråga.... *Löst, ses nedan*
		$rubrik = $_POST['rubrik'];
		$aktivitet = $_POST['aktivitet'];
		$kostnad = $_POST['kostnad'];
		$datum = $_POST['datum'];
		$tid = $_POST['tid'];
		$plats = $_POST['plats'];
		$place = $_POST['place'];
		$ansvarig =	$_POST['ansvarig'];
		$senastanml = $_POST['senastanml'];
		$checkboxcomment = $_POST['checkboxcomment'];
		$comentlength = $_POST['comentlength'];
		$kryss = $_POST['kryss'];
		$banmn = $_POST['banmn'];
		$senastanml = $_POST['senastanml'];
		$pdf = $_POST['pdf'];

		if (test($rubrik, $aktivitet, $kostnad, $datum, $tid, $plats, $place))
		{
			echo"Vi har passerat testen, vi har den info som krävs!<br>";
			$run = 1;
		}
		else if (basename($_SERVER['SCRIPT_FILENAME']) == "tasks.php")
		{
			$t_id = $_SESSION['repeting'];
			$p = count($_SESSION['nextdate']);
		
			$sql= "SELECT * FROM ".$table3." WHERE t_id = '$t_id'";
			$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
			$rad=mysqli_fetch_array($result);

			$image = $rad[t_id_image];
			$pdf = $rad[pdf_anm];
			$tid2 = $rad['time'];

			$sql= "SELECT * FROM ".$table4." WHERE id_types = '".$t_id."' AND del = 0 ORDER BY rand() LIMIT ".$p;
			$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
			
			$o = 0;
			while ($rad=mysqli_fetch_array($result))
			{
				$m_rub[$o] = $rad['text_rubrik'];
				$m_akt[$o] = $rad['text_aktivitet'];
				$o++;
			}
			
			if (count ($rub) == 1)
			{
				$m_rub[0] = $m_rub[0];
				$m_akt[0] = $m_akt[0];
			}
			
			$s = count ($m_rub);
			$ff = 0;
			
			foreach ($_SESSION['nextdate'] as $z => $x)
			{
				if (!array_key_exists($x, $block_dates) && (strtotime($x) >= strtotime(date("Ymd"))) )
				{
					$t = rand(0, $s-1);
					$ff++;
					$rubrik[] = $m_rub[$t];
					$aktivitet[] = $m_akt[$t];
					//$sql2 ="INSERT INTO aktiviteter(rubrik,aktivitet,pris,datum,tid, ansvarig, comments, editdate, banml, senastanml, maxantal, plats, imageid, pdf_anm, rep_activity_type) VALUES ('".$m_rub[$t]."','".$m_akt[$t]."','$pris','".$x."','".$tid2."','$ansvarig2', '$comment', '$commentdate', '$banml2', '$senastanml2', '$maxantal2', '$plats2', '$image_id', '$pdf', '".$t_id."')";
                    
                    $sql2 ="INSERT INTO ".$table2."(rubrik,aktivitet,pris,datum,tid, ansvarig, comments, editdate, banml, senastanml, maxantal, plats, imageid, pdf_anm, rep_activity_type) VALUES ('".$m_rub[$t]."','".$m_akt[$t]."','$pris','".$x."','".$tid2."',NULL, NULL, NULL, 0, NULL, '0', '$plats2', NULL, '$pdf', '".$t_id."')";
                    
					$result= mysqli_query($link, $sql2) or die (__LINE__." Error : ".$sql2 ." ".mysqli_error ($link));
				}
			}
			
			return true;
		
		}
		else
		{
			echo "<h1>Återkommande aktiviter</h1>";
			//echo"Vi har EJ den info som krävs!<br>";	
		}
		
		//Om vi ska skriva in infon till databasen
		if ($run == 1)
		{
			//Loopa igenom alla aktivitetsdagar som ska regas i databasen
			$i = -1;
			foreach ($rubrik as $z)
			{
				echo __LINE__ ." hmmmmm.....<br>";
				$i++;
				//Snygga till datum och tid, samt även lite inför test av dessa
				$datum2 = trimdate($datum[$i]);
				$senastanml2 = trimdate($senastanml[$i]);
				$test = str_replace("-","",$datum2);
				$tid2 = trimtime($tid[$i]);
				$temp = date("Ymd");
				
				if ($test >= $temp)
				//Testa och se om vi ett datum för aktivteten som finns i framtiden
				{
					//För att lägga till de dagar som vi ska kunna kommentera en aktivitet
					if ($checkboxcomment[$i])
					{
						if ($comentlength[$i] == "0")
						{
							$commentdate = date("Y-m-h", mktime(0,0,0,0,0,0));
						}
						else if ($comentlength[$i] == "1")
						{
							$commentdate = explode("-",$datum2);
							$commentdate = date("Y-m-d",mktime(0,0,0,$commentdate[1],$commentdate[2]+7,$commentdate[0]));
						}
						else if ($comentlength[$i] == "2")
						{
							$commentdate = explode("-",$datum2);
							$commentdate = date("Y-m-d",mktime(0,0,0,$commentdate[1],$commentdate[2]+14,$commentdate[0]));
						}
							else if ($comentlength[$i] == "3")
						{
							$commentdate = explode("-",$datum2);
							$commentdate = date("Y-m-d",mktime(0,0,0,$commentdate[1],$commentdate[2]+21,$commentdate[0]));
						}
							else if ($comentlength['$i'] == "4")
						{
							$commentdate = explode("-",$datum2);
							$commentdate = date("Y-m-d",mktime(0,0,0,$commentdate[1],$commentdate[2]+28,$commentdate[0]));
						}
						
						$comment = "1";
					}
					else
					{
						$comment = "0";
					}
					
					//Testa och se om det är en giltig tid som har angivits
					if (($test >= date("Ymd")))
					{
						//Testa och se om datum för senaste anmälan är innan aktiviten ska genomföras
						$testsenastanml = str_replace("-","",$senastanml2);
						if ($test > $testsenastanml)
						{
							$rubrik2=$rubrik[$i];
							$aktivitet2 = $aktivitet[$i];
							
							$kostnad2 = $kostnad[$i];
							$temp = strlen($kostnad2);
							$kostnad2 = preg_replace("[^0-9]", "", $kostnad2);
							
							if (($temp > strlen($kostnad2)))
							{
								$message = "Kostnaden som du angav innehöll tecken!\nDet redigerade värdet som kommer att sparas är " .$kostnad2 ." !";
		
								$message = preg_replace("/\r?\n/", "\\n", addslashes($message));
		  
								echo "<script type=\"text/javascript\">\n";
								echo " alert(\"$message\");\n";
								echo "</script>\n\n";
							}
							else if (strlen($kostnad2) == 0)
							{
								$kostnad = 0;
							}
							
							$ansvarig2 = $ansvarig[$i];
							
							$pris = $kostnad2;
							
							//Om vi har bindandeanmäl
							if (isset($banmn[$i]))
							{
								$banml2 = '1';
							}
							else
							{
								$banml2 = '0';	
							}
							
							$maxantal2 = $maxantal[$i];
							$maxantal2 = trimantal($maxantal2);
							if (!empty($plats[$i]))
							{
								$plats2 = $plats[$i];
							}
							else
							{
								$p_id = $place[$i];
								$sql= "SELECT * FROM places WHERE p_id = $p_id";
								$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
								$rad=mysqli_fetch_array($result);
								$plats2 = $rad['p_place'];
								$plats2 =$plats2 .", " .$rad['p_adress'] .", " .$rad['p_ort'];
								
								//echo "Plats som kommer att spara i databasen är $plats2";
								
							}
							echo"<p class='content'>Information kommer att sparas, var god och vänta!!!</p>";
							//Spara infromtation till databasen
							$anders[] = $_SESSION['repeting_image'];
							foreach($anders as $a)
							{
								echo "bildid = $a <br>";	
							}
							$image_id = $_SESSION['repeting_image'][$i];
							//Spara infromtation till databasen
							$sql2 ="INSERT INTO ".$table2."(rubrik,aktivitet,pris,datum,tid, ansvarig, comments, editdate, banml, senastanml, maxantal, plats, imageid, pdf_anm) VALUES ('$rubrik2','$aktivitet2','$pris','$datum2','$tid2','$ansvarig2', '$comment', '$commentdate', '$banml2', '$senastanml2', '$maxantal2', '$plats2', '$image_id', '$pdf')";
							$result= mysqli_query($link, $sql2) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
							//Plocka ut id för aktiviteten och sätt att ansvarig som anmäld om vi ska göra detta
							echo "Ansvarig är $ansvarig2 <br>";
							if (!empty($ansvarig2))
							{
								echo "Ansvarig är $ansvarig2 <br>";

								$query3="SELECT * FROM ".$table2." WHERE ansvarig = '$ansvarig2' ORDER BY aid DESC";
								$result3 = mysqli_query ($link, $query3);
								$nt3=mysqli_fetch_array($result3);

								$aid = $nt3[aid];
								
								$query="SELECT * FROM ".$table5." WHERE uid = '$ansvarig2'";
								$result = mysqli_query ($link, $query)  or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
								$nt=mysqli_fetch_array($result);

								if ($nt['autoansv'] == '1')
								{
									$curentdate = date("Y-m-h H:i:s");
									$sql= "INSERT INTO ".$table6." (aid, uid, anmald) VALUES ('$aid', '$ansvarig2', '$curentdate')";
									$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
								}
							}
							
						}
						else
						{
							?>
							<script language=javascript> 
							alert("Du har angivet att datum för senast anmälan som är efter det att aktiviteten ska genomföras!!!");
							</script>
							<?php
						}
					}
					else
					{
						?>
						<script language=javascript> 
						alert("Du har angivet att datum som har passerat!!!");
						</script>
						<?php
					}
				}
			}
			unset ($_POST['submit']);
			unset ($_POST['datum']);
			unset ($_POST['aktivitet']);
			unset ($_POST['rubrik']);
			unset($_SESSION[tid]);
			unset($_SESSION['nextdate']);
			unset($_SESSION[datumstop]); 
			unset($_SESSION[activity]);
			unset($_SESSION[week]);
			unset($_SESSION[weekday]);
			unset($_SESSION['repeting']);
			unset($_POST['submit']);
			if (!isset ($_SESSION[retrun]))
			{
				?>
					<script language=javascript> 
					alert("Aktivitetetn har sparats, du kommer nu att sändas vidare till programmet!!!");
					window.location = "index.php#service"
		
					</script>
				<?php
			}
			else
			{
				header("Location:repetingie.php");
				exit;
			}
		}
			
		$id = $_SESSION[id];
		
		$query = "SELECT * FROM ".$table5." WHERE uid = '$id'";
		$result = mysqli_query ($link, $query);
		$nt=mysqli_fetch_array($result);
		
		$admin =$nt['admin'];
		
		if (empty($t_id) && !empty($_POST['repeting']))
		{
			$t_id = mysqli_real_escape_string($link, $_POST['repeting']);	
		}
		else if (empty($t_id) &&!empty($_SESSION['repeting']))
		{
			$t_id = $_SESSION['repeting'];	
		}
		else if (empty($t_id))
		{
			$t_id = $rep_typ;	
		}
		else
		{
		
		}
		$p = count($_SESSION['nextdate'])	;
		
		$sql= "SELECT * FROM ".".$table3."." WHERE t_id = '$t_id'";
		$result = mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		$row = mysqli_fetch_array($result);
		
		$image = $row['t_id_image'];
		$pdf = $row[pdf_anm];
		
		$sql= "SELECT * FROM ".$table4." WHERE id_types = '".$t_id."' AND del = 0 ORDER BY rand() LIMIT ".$p;
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		
		while ($row=mysqli_fetch_array($result))
		{
			$m_rub[] = $row['text_rubrik'];
			$m_akt[] = $row['text_aktivitet'];
		}
		
		//För att avgöra om det finns någon färdiga texter till aktiviteten som vi kan använda oss av
		if (!empty($m_rub))
		{
			$predefinedtext = 1;
		}
		else
		{
			$predefinedtext = 0;
		}
	
		if (count ($m_rub) == 1)
		{
			$rub[] = $m_rub[0];
			$akt[] = $m_akt[0];
		}
	
		while (count ($rub) < $p +1)
		{
			$t = rand(0, $p-1);
			$rub[] = $m_rub[$t];
			$akt[] = $m_akt[$t];
		}
	
		$temp = count ($rub);
		
		//Varför vill inte javascriptet som döljer text fungera tillsamans med formuläret som skapas utanför????
		echo "<form action='".$_SERVER['php_self']."' method='post' name = 'f1'>";
		echo "<input type = hidden name = pdf value = $pdf>";
		//Visa inte raderna för fördefinerade rubriker om det inte finns några sådana
		/*if ($predefinedtext == 1)
		{
			echo "<table>";
			echo "<tr><td><p class='content'>Rubrik:<p></td><td><input type='text' name='rubri' size = '40' value='"; if (!empty($rubri)) {echo $rubri;} else {echo $rub[0];}; echo "'> * </td></tr>";
			echo "<tr><td valign='top'><p class='content'>Aktivitet:<p></td><td><textarea name='aktivite' rows='5' cols='40' wrap='physical'>"; if (!empty($aktivite)) {echo $aktivite ;} else {echo $akt[0];} echo "</textarea> * </td></tr>";
			echo "</table>";
			/* echo "Om du vill justera egenskaper för aktiviteten tryck "; ?>  <button OnClick = "javascript:unhide('details<?php echo $t ?>'); > här </button><br> 
			echo "<br>Önskas annat innehåll på rubrikerna ovan går det bra att justera dessa nedan genom att editera egenskaperna för den aktuella dagen.<br><br>";
		}*/
		//Räkanare för att ha koll på vilken aktivitet som det handlar om
		
		$done = false;
		$t = -1;
		$k = $_SESSION['nextdate'];
		echo "Vill du editera de fördefinerade texterna som finns till denna typ av aktivitet? <button id = edit_activy_text class = \"btn btn-default\">Editera</button><br>";
	
		foreach ($k as $p)
		{
			$t++;
			$temp = date("Ymd");
			$temp2 = str_replace("-","",$_SESSION['nextdate'][$t]);
			//echo "temp2 = $temp2 temp = $temp<br>";
			
			if ($temp2 >= $temp && !array_key_exists($_SESSION['nextdate'][$t], $block_dates))
			{
				$done = true;
				if ($t != 0 || ($predefinedtext != 0))
				{
					echo"<hr />";
				}
				
				 echo "Fält märkta med en * är obligatoriska!<br>";
				 if ($predefinedtext == 1)
				{	
				 
				 echo "<div id = 'details". $t ."'>";
				}
				 echo "<table width = \"99%\" class = 'table'>";
				echo "<tr><td><p class='content'>Rubrik:<p></td><td><input type='text' name='rubrik[]' size = '40' value='"; if (!empty($rub[$t])) {echo $rub[$t];} else {echo $rub[0];}; echo "'> * </td></tr>";
				
				echo "<tr><td valign='top'><p class='content'>Aktivitet:<p></td><td><textarea name='aktivitet[]' rows='5' cols='40' wrap='physical'>"; if (!empty($akt[$t])) {echo $akt[$t] ;} else {echo $akt[0];} echo "</textarea> * </td></tr>";
				echo "</table>";
	
				echo "<table width = \"99%\" class = 'table'>";	 
				echo "<tr><td><p class='content'>Kostnad : <p></td><td><input type='text' name='kostnad[]' value='" .$kostnad[$t] ."'> * Endast sifror, andra tecken tas bort! <p> </td></tr>";
				
				/*echo "<tr><td>"; ?> <a href="javascript:OpenCal('datum[<?php echo $t ?>]');"> <?php echo "<p class='content'>Datum<p></td></a><td><p class='content'>" ?> <input type='text' id='datum[<?php echo $t ?>]' maxlength = '10' name='datum[<?php echo $t ?>]'  size='14' value='<?php if (!empty($_POST['datum[$t]'])) {echo $_POST['datum[$t]'] ;} else {echo $_SESSION['nextdate'][$t];} ?>' onClick="javascript:OpenCal('datum[<?php echo $t ?>]');"><?php echo " * Ska anges i följande format YYYY-MM-DD<p></td></tr>";*/
				echo "<tr><td>"; ?> <a href="javascript:OpenCal('datum[]');"> <?php echo "<p class='content'>Datum<p></td></a><td><p class='content'>" ?> <input type='text' id='datum[]' maxlength = '10' name='datum[]'  size='14' value='<?php if (!empty($_POST['datum[$t]'])) {echo $_POST['datum[$t]'] ;} else {echo $_SESSION['nextdate'][$t];} ?>' onClick="javascript:OpenCal('datum[]');"><?php echo " * Ska anges i följande format YYYY-MM-DD<p></td></tr>";
			   echo "<tr><td><p class='content'>Tid<p></td><td><input type= 'text' name='tid[]' value='"; if (!empty($tid[$t])) {echo $tid[$t] ;} else {echo $_SESSION['tid'];} echo "'> * </td></tr>";
				
				echo "<tr><td><p class='content'>Plats : <p></td><td><input type= 'text' name='plats[]' value='" .$plats[$t] ."'> * ";
				
				$table = "`".PREFIX."places`";
				$table2 = "`".PREFIX."types`";
				$table3 = "`".PREFIX."table_places`";
				
				$sql= "SELECT COUNT(*) FROM ".$table." JOIN ".$table2." ON ta_id_types = t_id JOIN ".$table." ON placeId = ta_id_places WHERE ta_id_types = $t_id";
				$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
				$rad=mysqli_fetch_array($result);
				
				if ($rad[0] > 0)
				{
					$sql= "SELECT * FROM ".$table." JOIN ".$table2." ON ta_id_types = t_id JOIN ".$table." ON p_id = ta_id_places WHERE ta_id_types = $t_id";
					$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
					
					echo "Eller välj här : ";
					echo "<select name='place[]'>";
					while($rad=mysqli_fetch_array($result))
					{
						if ($rad['p_id'] == $place[$t])
						{
							echo "<option value=".$rad['p_id']." SELECTED> ".$rad['p_place']."</option>";
						}
						else
						{
							echo "<option value=".$rad['p_id']."> ".$rad['p_place']."</option>";
						}
					}
					echo "</select>";// Closing of list box 
				
				}
				echo "</td></tr>";
			
			
					echo "<tr><td><p class='content'>Ansvarig : <p></td><td>";				
					
					$query="SELECT * FROM user WHERE synlig = '0' AND betalt > '". date("Y-m-d")."' ORDER BY fnamn";
					$result = mysqli_query ($link, $query);
					echo "<select name=ansvarig[]>";
					
					while($nt=mysqli_fetch_array($result))
					{
						//För att slå samman förnamn och efternamn
						$namn = utf8_encode($nt['fnamn']) ." " .utf8_encode($nt['enamn']);
						if ($nt['uid'] == $ansvarig[$t])
						{
							echo "<option value=$nt[uid] SELECTED> $namn</option>"; 
						}
						else
						{
							echo "<option value=$nt[uid]>$namn</option>";
						}
					}
					echo "</select>";// Closing of list box 
					echo "</td></tr>";
					echo "</table>";
				
				
				//För att vi ska kunna lägga till en bild till en aktivitet
				$_SESSION['repeting_image'][$t] = $image;
								
				if ($predefinedtext == 1)
				{	
				?>
				<div id = 'ddetails<?php echo $t ?>' class='hidden'>
			   	<?php
				}
				/*echo "Om du vill justera utökade egenskaper för aktiviteten tryck "; ?>  <a href = "javascript:unhide('dddetails<?php echo $t ?>');" > <img src="image/Buttons/here.png" width="100" height="25" alt="här"> </a><br> <?php*/
				echo "</div>";
				?>
				<div id = 'dddetails<?php echo $t ?>' class='hidden'>
				 <?php
				
				//Bara för att vi ska ha kryssrutan för kommentarer som standard i kyssad
				if (!isset($_POST['kryss[$t]']) || (isset($checkboxcomment[$t])))
				{
					?>
					<p class="content"><input name="checkboxcomment[<?php echo $t ?>]" type="checkbox" id="checkboxcomment" value="checkboxcomment" checked="checked" onClick="tog(this,'comentlength[<?php echo $t ?>]')"> Man ska kunna ge kommentater till och med <select name="comentlength[<?php echo $t ?>]" id="comentlength[<?php echo $t ?>]">
					<option value = '0'>Oändligt</option>
					<option value = '1'>1 vecka</option>
					<option value = '2' selected>2 veckor</option>
					<option value = '3' >3 veckor</option>
					<option value = '4' >4 veckor</option>
				   </select> efter att aktiviteten har genomförts. Bocka av rutan om du önskar ej möjlighet att ge kommentarer!<p>
				   <input type = "hidden" name ="kryss[<?php echo $t ?>]" value = "1">
				  <?php
				}
				else
				{
					?>
					<p class="content"><input name="checkboxcomment[<?php echo $t ?>]" type="checkbox" id="checkboxcomment" value="checkboxcomment"   onClick="tog(this,'comentlength[<?php echo $t ?>]')"> Man ska kunna ge kommentater till och med <select name="comentlength[<?php echo $t ?>]" id="comentlength[<?php echo $t ?>]">
					<option value = '0'>Oändligt</option>
					<option value = '1'>1 vecka</option>
					<option value = '2' selected>2 veckor</option>
					<option value = '3' >3 veckor</option>
					<option value = '4' >4 veckor</option>
					</select> efter att aktiviteten har genomförts. Bocka av rutan om du önskar ej möjlighet att ge kommentarer!<p>
					<input type = "hidden" name ="kryss[<?php echo $t ?>]" value = "0">
					<?php 
					
					//Dölj antalet dagar som vi kan ge kommentarer på aktivitet
					echo "<script type=\"text/javascript\">\n";
					echo "tog(this,'comentlength');\n";
					echo "</script>\n\n";
						
				}
				 if (isset ($_POST['pdf_anm']) || !isset($_POST[hiden_pdf]))
				{
					 echo "<p class='content'><input name='pdf_anm' type='checkbox' checked id='pdf_anm' value='pdf_anm'> Det ska skapas ett blad för anmälan i pdf form.<input type = hidden name = hiden_pdf value = 1><br>";
				}
				else
				{
					echo "<p class='content'><input name='pdf_anm' type='checkbox' id='pdf_anm' value='pdf_anm'> Det ska skapas ett blad för anmälan i pdf form.<input type = hidden name = hiden_pdf value = 1><br>";
				}
				
				if (isset ($banmn[$t]))
				{
					 echo "<p class='content'><input name='banmn[]' type='checkbox' checked id='banmn' value='banml'> Anmälan är bindande<br>";
				}
				else
				{
					echo "<p class='content'><input name='banmn[]' type='checkbox' id='banmn' value='banml'> Anmälan är bindande<br>";
				}
				?>
				<p class='content'>Anmälan måste ske senast (om inget senastedatum - lämna blankt) <input type='text' id='senastanml' name='senastanml[<?php echo $t ?>]' size='10'value='<?php echo $senastanml[$t]; ?>' onClick='javascript:OpenCal("senastanml[<?php echo $t ?>]");'> Ska anges enligt YYYY-MM-DD <p>
				<?php
				echo"<p class='content'>Hur många kan deltaga i aktiviteten, tomt för obegränsat antal.<input type='text' id='maxantal$t' name='maxantal[]' maxlength = '3' size='3' value='" .$maxantal[$t] ."'><p>";
				
				echo"</div>";
			}
		} //slut på foreach
		
		if (!$done)
		{
			echo "<br><p class = \"content\">Det finns redan registerade aktiviteter av denna typ på sidan, du får gå in och editera varje aktivitet istället!!!<br><br>";	
		}
		else
		{
			
		}
		echo "När registeringen är klar, kommer du att direkt sändas åter till första sidan.<br>";
		echo "<table class=\"table\">";
        echo "<tr><td><input type='submit' class = \"btn btn-default\"  name='submit' value='Spara' ></td>";      
        echo "<td><button class = \"btn btn-default\"   alt=\"Ångra\" value='Ångra' onClick=\"reset()\">Ångra</button></td>";
		echo "<td><INPUT TYPE='submit' class = \"btn btn-default\"  name='cancel' value='Avbryt'></td>";
        
		$uid = $_SESSION['id'];
		$sql="SELECT * FROM user WHERE uid = '".$uid."'";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		$nt=mysqli_fetch_array($result);
		if ( $nt['admin']== "1")
		{
			
			echo "<td><input class=\"btn btn-default\" type=\"submit\" value=\"Editera platser\" name=\"places\"></td>"; 
		}
		echo "</tr>";
		echo "</table>";
		echo "<input type = \"hidden\" id = repeting_type_id value = \"".$t_id."\">";
        echo "</form>";

}

//******************************
//Själva huvudprogrammet
function start()
{
	global $link;
	
	if (isset ($_POST['retrun']) && isset($_POST['start']))
	{
		//För att säta värdet 1 i databasen för att veta om vi ska per automatik återkomma till startsidan för återkommande aktiviteter
		if (!isset ($_SESSION[retrun]))
		{
			$uid = $_SESSION['id'];
			$query="UPDATE user SET retrun = 1 WHERE uid = $uid";
			$result= mysqli_query($link, $query) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		}
		$_SESSION[retrun] = 1;	
		
	}
	else if (isset($_POST['start']))
	{
		if (isset ($_SESSION[retrun]))
		{
			$uid = $_SESSION['id'];
			$query="UPDATE user SET retrun = 0 WHERE uid = $uid";
			$result= mysqli_query($link, $query) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		}
		unset($_SESSION[retrun]);	
		
	}
	if (!isset($_SESSION['id']))
	{
		header("Location:login.php");
	}
	if (isset($_POST['places']))
	{
		unset($_POST['places ']);
		header("Location:adminplatser.php");
	}
	if (isset($_POST['places2']))
	{
		echo "Hej på dig! <br>";
		unset($_POST['places2']);
		header("Location:adminplatser.php");
	}

	if (isset ($_POST['cancel']))
	{
		unset($_SESSION[tid]);
		unset($_SESSION['nextdate']);
		unset($_SESSION[datumstop]); 
		unset($_SESSION[activity]);
		unset($_SESSION[week]);
		unset($_SESSION[weekday]);
		unset($_SESSION['repeting']);
		unset($_POST['submit ']);
		unset($_SESSION[retrun]);
	}
	

	if (isset ($_POST['submit']))
	{
		unset($_POST['submit']);
		reg(NULL, $_POST['repeting']);
		$reg =1 ;
	}
	
	else if (isset($_SESSION['nextdate'][0]))
	{
		unset($_POST['start']);
		reg();
		$reg =1 ;
	}
	else if (isset($_POST['start']))
	{
		if (($_POST['repeting'] > 0) )
		{
			$permdatumstart = $_POST['permdatumstart'];
			$permdatumstop = $_POST['permdatumstop'];
			$permdatumstart = str_replace("-","",$permdatumstart);
			$permdatumstop = str_replace("-","",$permdatumstop);
			if ($permdatumstart < $permdatumstop)
			{
				calcdate();
				$reg =1 ;
			}
			else
			{
				?>
            		<script language=javascript> 
            		alert("Du har angivit ett felaktigt slutdatum!!!");
            		</script>
            	<?php	
			}
			
		}
		else if (($_POST['weekday'] != 20) && ($_POST['week'] != 0) && (!empty($_POST['datumstart'])) && (!empty($_POST['datumstop'])) && ($_POST['activity'] != 0))
		{
			if($_POST['checkbox'])
			{
				$datumstart = explode("-",$_POST['datumstart']);
				$datumstart = $datumstart[0]  .$datum[1] ."01";
			} 
			else
			{
				$datumstart = str_replace("-","", $_POST['datumstart']);
			}
			$datumstop = str_replace("-","", $_POST['datumstop']);
			
			//Bara för att vi ska kunna testa om det är en giltig månad
			$datumtest = explode("-", $_POST['datumstart']);
			$datumtest = $datumtest[0] .$datumtest[1];
			$curdate = date("Ym");
			if ($datumtest >= $curdate)
			{
				if ( $datumstart < $datumstop )
				{
					calcdate();
					$reg =1 ;
				}
				else
				{
					?>
            		<script language=javascript> 
            		alert("Du har angivit ett felaktigt slutdatum!!!");
            		</script>
            		<?php	
				}
			}
			else
			{
			?>
            <script language=javascript> 
            alert("Du har angivit ett felaktigt startdatum!!!");
            </script>
            <?php
			}
			
		}
		else
		{
			?>
            <script language=javascript> 
            alert("Du har glömt att editera något av fälten!!!");
            </script>
            <?php
		}
	}

	if ($_POST['submit '])
	{
		unset($_POST['submit ']);
		reg('', $_POST['repeting']);
		$reg =1 ;
		
	}

	if (!isset($reg))
	{
		?>
		<div id = 'add1' class='unhidden2'>
        <?php
		echo "<form action='" .$_SERVER['PHP_SELF'] ."' method='post' >";
		$sql= "SELECT * FROM types WHERE t_id > 1 ORDER BY t_typ";
		$result= mysqli_query($link, $sql) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		
		 $temp = "testar";// . $t;
		
		echo"Välj här bland de vanligt återkommande aktiviterna som brukar ske i föreningen : <select name=repeting ></option>";
		echo "<option value=-1> </option>";
		
		while($nt=mysqli_fetch_array($result))
		{	
			if ($nt['t_id'] == $_POST['repeting'])
			{
				echo "<option value='" .$nt['t_id'] ."' SELECTED> " .fixutferror($nt['t_typ']) ."</option>";
			}
			else
			{
				echo "<option value='" .$nt['t_id'] ."'> " .fixutferror($nt['t_typ']) ."</option>";
			}
		}
		
		echo "</select><br><br>";
		?>
		Med start datum <input type="text" id='permdatumstart' maxlength = '10' name="permdatumstart" size="14"value="<?php if (!empty($_POST['permdatumstart'])) {echo $_POST['permdatumstart'] ;} else {}?>"> och slutdatum <input type="text" id='permdatumstop' maxlength = '10' name="permdatumstop" size="14"value="<?php if (!empty($_POST['permdatumstop'])) {echo $_POST['permdatumstop'] ;} else {}?>"><br><br>
		
		<?php
		echo "Om du vill lägga till fler återkommande aktiviteter, då kan du göra det under knappen editera plaster.<br><br>";
		echo "<input type='submit' name = 'start' value='Starta' class = 'btn btn-default'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class = 'btn btn-default' name='places'  value='Editera platser' ><br><br>";
		
		//Hämta ut databasen om vi ska återkomma till denna sida eller ej
		$uid = $_SESSION['id'];
		$query="SELECT * FROM user WHERE uid = $uid";
		$result= mysqli_query($link, $query) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		$nt=mysqli_fetch_array($result);
		
		if ($nt[retrun] == '1')
		{
			echo "<input name = 'retrun' id = 'retrun' type ='checkbox' checked  value = 'retrun'> Jag vill komma åter till denna sida när registreingen är klar, ingen övrig bekräftelse kommer! <br>";
		}
		else 
		{
			echo "<input name = 'retrun' id = 'retrun' type ='checkbox' value = 'retrun'> Jag vill komma åter till denna sida när registreingen är klar, ingen övrig bekräftelse kommer! <br>";
		}
		
		echo "<br><br>";
		echo "</form>";
		
		echo "Om du vill registerare en återkommande aktivitet som ej finns med ovan, tryck "; ?>  <a href = "javascript:unhide('add'); unhide('add1')" class = 'btn btn-default' type = 'button'> Här</a><br><br><br> <?php 
		?>
		</div>
		<div id='add' class='hidden'>
		<?php
		echo "Vill du växla åter till de fasta aktiviter som fanns innan, tryck "; ?>  <a href = "javascript:unhide('add'); unhide('add1')" class = 'btn btn-default' type = 'button'>Här </a><br><br> <?php 
		echo"För att kunna lägga till flera aktiviter av samma typ, var vänliga och ange följande information nedan. Sidan räknar själv fram datumet när en aktiivtet infaller basert på det startdatum som du anger. <br><br>";
		echo "<form action='" .$_SERVER['PHP_SELF'] ."' method='post' >";
		
		echo "Aktiviteten infaller på <select name=weekday ></option>";
		// printing the list box select command
			
			echo "<option value=20> </option>";
			if ($_POST['weekday'] == 1)
			{
				echo "<option value=1 SELECTED>Måndag</option>";
			}
			else
			{
				echo "<option value=1>Måndag</option>";
			}
			if ($_POST['weekday'] == 2)
			{
				echo "<option value=2 SELECTED>Tisdag</option>";
			}
			else
			{
				echo "<option value=2>Tisdag</option>";
			}
			if ($_POST['weekday'] == 3)
			{
				echo "<option value=3 SELECTED>Onsdag</option>";
			}
			else
			{
				echo "<option value=3>Onsdag</option>";
			}
			if ($_POST['weekday'] == 4)
			{
				echo "<option value=4 SELECTED>Torsdag</option>";
			}
			else
			{
				echo "<option value=4>Torsdag</option>";
			}
			if ($_POST['weekday'] == 5)
			{
				echo "<option value=5 SELECTED>Freag</option>";
			}
			else
			{
				echo "<option value=5>Fredag</option>";
			}
			
			if ($_POST['weekday'] == 6)
			{
				echo "<option value=6 SELECTED>Lördag</option>";
			}
			else
			{
				echo "<option value=6>Lördag</option>";
			}
			if ($_POST['weekday'] == 7)
			{
				echo "<option value=7 SELECTED>Söndag</option>";
			}
			else
			{
				echo "<option value=7>Söndag</option>";
			}
						
		echo "</select> ";// Closing of list box 
		
		echo " varje <select name=week ></option>";
		// printing the list box select command
		
			if (!isset($_POST['week']))
			{		
			echo "<option value=0 SELECTED> </option>";
			}
			else 
			{
				echo "<option value=0 > </option>";
			}
			if ($_POST['week'] == 1)
			{
				echo "<option value=1 SELECTED>Varje vecka </option>";
			}
			else
			{
				echo "<option value=1>Varje vecka</option>";
			}
			if ($_POST['week'] == 2)
			{
				echo "<option value=2 SELECTED>Jämn vecka </option>";
			}
			else
			{
				echo "<option value=2>Jämn vecka</option>";
			}
			if ($_POST['week'] == 3)
			{
				echo "<option value=3 SELECTED>Udda vecka</option>";
			}
			else
			{
				echo "<option value=3>Udda vecka</option>";
			}
			if ($_POST['week'] == 4)
			{
				echo "<option value=4 SELECTED>Sista veckodagen</option>";
			}
			else
			{
				echo "<option value=4>Sista veckodagen </option>";
			}
			if ($_POST['week'] == 5)
			{
				echo "<option value=5 SELECTED>1:a veckodagen</option>";
			}
			else
			{	
				echo "<option value=5>1:a veckodagen </option>";
			}
			if ($_POST['week'] == 6)
			{
				echo "<option value=6 SELECTED>2:a veckodagen</option>";
			}
			else
			{	
				echo "<option value=6>2:a veckodagen </option>";
			}
			if ($_POST['week'] == 7)
			{
				echo "<option value=7 SELECTED>3:de veckodagen</option>";
			}
			else
			{	
				echo "<option value=7>3:de veckodagen</option>";
			}
			if ($_POST['week'] == 8)
			{
				echo "<option value=8 SELECTED>4:de veckodagen</option>";
			}
			else
			{	
				echo "<option value=8>4:de veckodagen</option>";
			}
			if ($_POST['week'] == 9)
			{	
				echo "<option value=9 SELECTED>5:e veckodagen</option>";
			}
			else
			{	
				echo "<option value=9>5:e veckodagen</option>";
			}
		echo "</select> <br><br>";// Closing of list box 
		
		?>
			Med start datum <input type="text" id='datumstart' maxlength = '10' name="datumstart" id="datumstart" size="14"value="<?php if (!empty($_POST['datumstart'])) {echo $_POST['datumstart'] ;} else {}?>"> och slutdatum <input type="text" id='datumstop' id = 'datumstop'maxlength = '10' name="datumstop" size="14"value="<?php if (!empty($_POST['datumstop'])) {echo $_POST['datumstop'] ;} else {}?>"><br><br> 
		<?php
		if (!isset($_POST['kryss']) || (isset($_POST['checkbox'])))
		{
			
			echo "<input name='checkbox' type='checkbox' id='checkbox' value='checkbox' checked='checked'>  Start datum ska vara den förste oberonde av datum och slut datum den sista dagen i månaden. <input type = 'hidden' name ='kryss' value = '1'>";
          
		}
		else
		{
			
			echo "<input name='checkbox' type='checkbox' id='checkboxcomment' value='checkbox'> Start datum ska vara den förste oberonde av datum och slut datum den sista dagen i månaden. <input type = 'hidden' name ='kryss' value = '0'>";
           	 
		}
		echo "<input type = 'hidden' name ='activity' value = '1'>";
		
		
		echo "<input type='submit' name = 'start' value='Starta' class ='btn btn-default'> <input type='submit' class ='btn btn-default' name='places' value='Editera platser' ><br><br>";
		if (!isset($_POST['kryss']))
		{
			echo "<input type = 'hidden' name ='kryss' value = '1'>";
		}
		
		//Hämta info från databasen om vi ska återkomma till denna sida eller ej
		$uid = $_SESSION['id'];
		$query="SELECT * FROM user WHERE uid = $uid";
		$result= mysqli_query($link, $query) or die (__LINE__." Error : ".$sql ." ".mysqli_error ($link));
		$nt=mysqli_fetch_array($result);
		
		if ($nt[retrun] == '1')
		{
			echo "<p class='content'><input name = 'retrun' id = 'retrun' type ='checkbox' checked  value = 'retrun'> Jag vill komma åter till denna sida när registreingen är klar, ingen övrig bekräftelse kommer! <br>";
		}
		else 
		{
			echo "<p class='content'><input name = 'retrun' id = 'retrun' type ='checkbox' value = 'retrun'> Jag vill komma åter till denna sida när registreingen är klar, ingen övrig bekräftelse kommer! <br>";
		}
		
		echo "</form>";
	echo "</div>";
    }

}
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__))
{
	include_once ("lg-modal.php");
}
?>

