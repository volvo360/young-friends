<?php
date_default_timezone_set('Europe/Stockholm');
if (!function_exists(fixutferror))
{
	function fixutferror($str) {
	  $a = array("Ã¶", "Ã¥", 'Ã¤', "Ã…", "Ã©", "Ã–", "Ã¥", "Ã„");
	  $b = array('ö', 'å', 'ä', "Å", "é", "Ö", "å", "Ä");
	  return str_replace($a, $b, $str);
	}
}

function calendar()
{
	global $link;
	echo "<div class = 'hidden-xs' id = calendar_area>";
	if (isset($_SESSION['uid']))
	{
		echo "Vill du addera en ny aktivitet eller editera en redan existerande aktivitet? Högerklicka på önskat datum eller aktivitet.<br><br>";
	}
	start2();
	
	echo "</div>";
	echo "<div class = 'hidden-sm hidden-md hidden-lg visible-xs'>";
	start3();
	echo "</div>";
	global $link;

	echo "<h3>Historiska aktiviteter</h3><br>";
	
	
	if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
	{
		$table = PREFIX."activities";
		$sql = "SELECT * FROM $table WHERE datum < CURDATE()  ORDER BY datum DESC LIMIT 5";	
	}
	else if (isset($_SESSION['uid']))
	{
		$table = PREFIX."activities";
		$table_2 = PREFIX."roles";
		$table_3 = PREFIX."mission";
		$sql = "SELECT * , $table.tableKey as tableKey FROM $table LEFT outer JOIN $table_2 ON $table_2.assignment_id = $table.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum < CURDATE() AND canceld = '0' ORDER BY datum DESC LIMIT 5";	
	}
	else
	{
		$table = PREFIX."activities";
		$outside = true;
		$sql = "SELECT * FROM $table WHERE datum < CURDATE() AND publi IS NULL AND canceld = '0' ORDER BY datum DESC LIMIT 5";		
	}
	
	$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	
	
	if (mysqli_num_rows($result) > 0)
	{
		echo "<div class=\"table-responsive\">";
  		echo "<table class=\"table\">";
			while ($row = mysqli_fetch_array($result))
			{
				echo "<tr><td width = 10% align=\"center\">" .$row['datum'] ."<br>";
				if ($row['time'] != "00:00:00")
				{
					$display = explode(":",  $row['tid']);
					echo $display[0] .":";
					if (empty($display[1]))
					{
						echo "00";	
					}
					else
					{
						echo $display[1];
					}
				} 
				echo "</td><td><a href = 'show-activity.php?aid=".$row['aid'] ."'>" .fixutferror($row['rubrik'])."</a></td></tr>";
			}
			echo "</table>";
		echo "</div>";
	}
	else
	{
		echo "Tyvärr inga historiska aktiviteter!!!<br>";	
	}
	
}

function start3()
{
	global $link;
	
	$mont[1] = "Januari";
	$mont[2] = "Februari"; //Feb
	$mont[3]= "Mars";
	$mont[4] = "April";
	$mont[5] = "Maj"; //Maj
	$mont[6] = "Juni"; //Juni
	$mont[7] = "Juli"; //Juli
	$mont[8] = "Augusti"; //Aug
	$mont[9] = "September"; //Sep
	$mont[10] = "Oktober"; //Okt
	$mont[11] = "November"; //Nov
	$mont[12] = "December"; //Dec
	
	$mont[1] = "Jan";
	$mont[2] = "Feb"; //Feb
	$mont[3]= "Mar";
	$mont[4] = "Apr";
	$mont[5] = "Maj"; //Maj
	$mont[6] = "Jun"; //Juni
	$mont[7] = "Jul"; //Juli
	$mont[8] = "Aug"; //Aug
	$mont[9] = "Sep"; //Sep
	$mont[10] = "Okt"; //Okt
	$mont[11] = "Nov"; //Nov
	$mont[12] = "Dec"; //Dec	
	
	$day[0] = "Sön";
	$day[1]	= "Mån";
	$day[2]	= "Tis";
	$day[3]	= "Ons";
	$day[4]	= "Tor";
	$day[5]	= "Fre";
	$day[6]	= "Lör";
	$day[7] = "Sön";
	//http://se2.php.net/manual/en/function.easter-days.php
	$easter_day =  date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days(),   date("Y")));
	
	$good_friday =  date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days()-2,   date("Y")));
	$easter_saturday = date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days()-1,   date("Y")));
	$easter_monday =  date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days()+1,   date("Y")));
	$date = explode("-", $easter_day);
	/*
	Kristi himmelsfärdsdag	 sjätte torsdagen efter påskdagen
	*/
	$ascension =  date("Y-m-d",mktime(0, 0, 0, $date[1], $date[2]+39, $date[0]));
	/*
	pingstdagen	 sjunde söndagen efter påskdagen
	*/
	$pentecost = $pentecost = date("N",mktime(0, 0, 0, 6, 20, $date[0]));
	if ($pentecost == 6)
	{
		$pentecost = date("Y-m-d",mktime(0, 0, 0, 6, 20, $date[0]));
		$midsommer =  date("Y-m-d",mktime(0, 0, 0, 6, 19, $date[0]));
	}
	else if ($pentecost < 6)
	{
		$days = 6-$pentecost;
		$pentecost = date("Y-m-d",mktime(0, 0, 0, 6, 20+$days, $date[0]));
		$midsommer =  date("Y-m-d",mktime(0, 0, 0, 6, 20+$days-1, $date[0]));
	}
	
	$all_saints_day = date("N",mktime(0, 0, 0, 10, 31, $date[0]));
	if ($all_saints_day == 6)
	{
		$all_saints_day = date("Y-m-d",mktime(0, 0, 0, 10, 31, $date[0]));
		
	}
	else if ($all_saints_day < 6)
	{
		$days = 6-$all_saints_day;
		$all_saints_day = date("Y-m-d",mktime(0, 0, 0, 10, 31+$days, $date[0]));
		
	}
	
	
	/*
	midsommardagen den 20-26 juni	 den lördag som infaller under tidena
	*/
	$midsummer_day = date("N",mktime(0, 0, 0, $date[1], $date[2]+49, $date[0]));
	
	
	$year = date("Y");
	$public_holidays[$year.'-01-01'] = "Nyårsdagen";
	$other_holidays[$year.'-01-05'] = "Trettondagsafton";
	$public_holidays[$year.'-01-06'] = "Trettondedagen jul";
	$public_holidays[$good_friday] = "Långfredag";
	$public_holidays[$easter_day] = "Påskdagen";
	$public_holidays[$easter_monday] = "Annandag påsk";
	$public_holidays[$ascension] = "Kristi himilfärdsdag";
	$public_holidays[$pentecost] = "Pingstdagen";
	
	$other_holidays[$easter_saturday] = "Påskafton";
	$other_holidays[$midsommer] = "Midsommarafton";
	$other_holidays[$year.'-12-24'] = "Julafton";
	$other_holidays[$year.'-12-31'] = "Nyårsafton";
	
	
	$public_holidays[$year.'-06-06'] = "Nationaldagen";
	$public_holidays[$pentecost] = "Midsommardagen";
	
	$public_holidays[$all_saints_day] = "Allahelgonadag";
	
	$public_holidays[$year.'-12-25'] = "Juldagen";
	$public_holidays[$year.'-12-26'] = "Annandag jul ";
	
	$flag_days[$year.'-01-01'] = "Nyårsdagen";
	$flag_days[$year.'-01-28'] = "Konungens namnsdag";
	$flag_days[$year.'-03-12'] = "Kronprinsessans namnsdag";
	$flag_days[$easter_day] = "Påskdagen";
	$flag_days[$year.'04-30'] =  "Konungens födelsedag";
	$flag_days[$year.'05-01'] =  "Första Maj";
	$flag_days[$pentecost] = "Pingstdagen";
	$flag_days[$year.'-06-06'] = "Sveriges nationaldag och svenska flaggans dag";
	$flag_days[$midsommer] = "Midsommardagen";
	$flag_days[$year.'-07-14'] = "Kronprinsessans födelsedag";
	$flag_days[$year.'-08-08'] = "Drottningens namnsdag";
	$flag_days[$year.'-10-24'] = "FN-dagen";
	$flag_days[$year.'-11-06'] = "Gustav Adolfsdagen";
	$flag_days[$year.'-12-10'] = "Nobeldagen";
	$flag_days[$year.'-12-23'] = "Drottningens födelsedag";
	$flag_days[$year.'-12-25'] = "Juldagen";

	

	global $link;
	if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
	{
		$table = PREFIX."activities";
		$sql = "SELECT * FROM $table WHERE datum >= CURDATE() ORDER BY datum ASC";	
	}
	else if (isset($_SESSION['uid']))
	{
		$table = PREFIX."activities";
		$table_2 = PREFIX."roles";
		$table_3 = PREFIX."mission";
		$sql = "SELECT *, $table.tableKey as tableKey FROM $table LEFT outer JOIN $table_2 ON $table_2.assignment_id = $table.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum >= CURDATE() AND canceld = '0' ORDER BY datum ASC";	
	}
	else
	{
		$table = PREFIX."activities";
		$sql = "SELECT * FROM $table WHERE datum >= CURDATE() AND publi IS NULL AND canceld = '0' ORDER BY datum ASC";		
	}
	
	
	
	$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	
	while ($row = mysqli_fetch_array($result))
	{
		$activity[$row['datum']][] = $row['tableKey'];
		$end_activity[$row['datum']][$row['datum']] = $row['aid'];
		$header[$row['tableKey']] = fixutferror($row['rubrik']);
		$time[$row['tableKey']] = $row['tid'];	
		$valid_for[$row['tableKey']] = array_map('trim', explode(",", $row['valid_for']));
		$outside[$row['tableKey']] = $row['outside'];
	}
	foreach ($end_activity as $start_date => $value_1)
	{
		foreach ($end_activity[$start_date] as $end_date => $value_2)
		{
			if ($end_date != "0000-00-00" && !empty($end_date))
			{
				$datetime1 = new DateTime( $start_date);
				$datetime2 = new DateTime( $end_date);
				$interval = $datetime1->diff($datetime2);
				
				//echo $interval->format('%d');
				$days_2[$start_date][] = $interval->format('%d');
				for ($i = 1; $i <= $interval->format('%d'); $i++)
				{
					$date = date("Y-m-d", strtotime($start_date." +$i day"));
					$activity[$date][] = $value_2;	
				}
			}
			
			
		}
	}
	
	for ($x = 0; $x <= 2; $x++)
	{
		for ($d = 1; $d <= 31; $d++)
		{
			$y = date("Y-m",mktime(0, 0, 0, date("m")+$x, 1,   date("Y")));
			if ($d <= 9)
			{
				$y = $y."-0".$d;
			}
			else
			{
				$y = $y."-" .$d;	
			}
			foreach ($activity[$y] as $id => $value )
			{
				if (isset($valid_for[$value]))
				{
					$print = true;
					if (!$print)
					{
						foreach ($group as $a)
						{
							$search = "~".$a;
							if (array_search($search, $valid_for[$value]) !== false)
							{
								$print = true;
								break;	
							}
						}
					}
					
					if (!$print)
					{
						foreach ($pof as $a)
						{
							$search = $a;
							if (array_search($search, $valid_for[$value]) !== false)
							{
								$print = true;
								break;	
							}
						}
					}
					
					if (!$print)
					{
						$search = "-" .$_SESSION['user_id'];
						if (array_search($search, $valid_for[$value]) !== false)
						{
							$print = true;
							break;	
						}
					}
					
					if ($print)
					{
						$temp = explode("-", $y);
						$weekday = date("N",mktime(0, 0, 0, $temp[1], $temp[2], $temp[0]));
						$temp[2] = ltrim($temp[2], 0);
						$temp[1] = ltrim($temp[1], 0);
						echo $day[$weekday] ." " .$temp[2] ." " .$mont[$temp[1]];
						//echo $y ." ";
						if (array_search("~^0", $valid_for[$value]) !== false)
						{
							if (($time[$value] != "00:00:00") && !empty($time[$value] ))
							{
								$display = explode(":",  $time[$value]);
								echo $display[0] .":";
								if (empty($display[1]))
								{
									echo "00";	
								}
								else
								{
									echo $display[1];
								}
							}
							echo " " .$header[$value]." ";
						}
						else
						{
							echo "<a class = \"showActivity\" id = \"aid[".$value."]\">";
							if (($time[$value] != "00:00:00") && !empty($time[$value] ))
							{
								$display = explode(":",  $time[$value]);
								echo $display[0] .":";
								if (empty($display[1]))
								{
									echo "00";	
								}
								else
								{
									echo $display[1];
								}
							}
							echo " " .$header[$value]."</a><br><br>";
						}
					}
				}
				else
				{
					echo "<a class = \"showActivity\" id = \"aid[".$value."]\">";
					if (($time[$value] != "00:00:00") && !empty($time[$value] ))
					{
						$display = explode(":",  $time[$value]);
						echo $display[0] .":";
						if (empty($display[1]))
						{
							echo "00";	
						}
						else
						{
							echo $display[1];
						}
					}
					echo " " .$header[$value]."</a><br><br>";
				}
			}

		}
	}
}
function start2()
{
	global $link;
	
	$mont[1] = "Januari";
	$mont[2] = "Februari"; //Feb
	$mont[3]= "Mars";
	$mont[4] = "April";
	$mont[5] = "Maj"; //Maj
	$mont[6] = "Juni"; //Juni
	$mont[7] = "Juli"; //Juli
	$mont[8] = "Augusti"; //Aug
	$mont[9] = "September"; //Sep
	$mont[10] = "Oktober"; //Okt
	$mont[11] = "November"; //Nov
	$mont[12] = "December"; //Dec	
	
	//http://se2.php.net/manual/en/function.easter-days.php
	$easter_day =  date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days(),   date("Y")));
	
	$good_friday =  date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days()-2,   date("Y")));
	$easter_saturday = date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days()-1,   date("Y")));
	$easter_monday =  date("Y-m-d",mktime(0, 0, 0, 3, 21+easter_days()+1,   date("Y")));
	$date = explode("-", $easter_day);
	/*
	Kristi himmelsfärdsdag	 sjätte torsdagen efter påskdagen
	*/
	$ascension =  date("Y-m-d",mktime(0, 0, 0, $date[1], $date[2]+39, $date[0]));
	/*
	pingstdagen	 sjunde söndagen efter påskdagen
	*/
	$pentecost = $pentecost = date("N",mktime(0, 0, 0, 6, 20, $date[0]));
	if ($pentecost == 6)
	{
		$pentecost = date("Y-m-d",mktime(0, 0, 0, 6, 20, $date[0]));
		$midsommer =  date("Y-m-d",mktime(0, 0, 0, 6, 19, $date[0]));
	}
	else if ($pentecost < 6)
	{
		$days = 6-$pentecost;
		$pentecost = date("Y-m-d",mktime(0, 0, 0, 6, 20+$days, $date[0]));
		$midsommer =  date("Y-m-d",mktime(0, 0, 0, 6, 20+$days-1, $date[0]));
	}
	
	$all_saints_day = date("N",mktime(0, 0, 0, 10, 31, $date[0]));
	if ($all_saints_day == 6)
	{
		$all_saints_day = date("Y-m-d",mktime(0, 0, 0, 10, 31, $date[0]));
		
	}
	else if ($all_saints_day < 6)
	{
		$days = 6-$all_saints_day;
		$all_saints_day = date("Y-m-d",mktime(0, 0, 0, 10, 31+$days, $date[0]));
		
	}
	
	
	/*
	midsommardagen den 20-26 juni	 den lördag som infaller under tidena
	*/
	$midsummer_day = date("N",mktime(0, 0, 0, $date[1], $date[2]+49, $date[0]));
	
	
	$year = date("Y");
	$public_holidays[$year.'-01-01'] = "Nyårsdagen";
	$other_holidays[$year.'-01-05'] = "Trettondagsafton";
	$public_holidays[$year.'-01-06'] = "Trettondagen jul";
	$public_holidays[$good_friday] = "Långfredag";
	$public_holidays[$easter_day] = "Påskdagen";
	$public_holidays[$easter_monday] = "Annandag påsk";
	$public_holidays[$ascension] = "Kristi himilfärdsdag";
	$public_holidays[$pentecost] = "Pingstdagen";
	
	$other_holidays[$easter_saturday] = "Påskafton";
	$other_holidays[$midsommer] = "Midsommarafton";
	$other_holidays[$year.'-12-24'] = "Julafton";
	$other_holidays[$year.'-12-31'] = "Nyårsafton";
	
	
	$public_holidays[$year.'-06-06'] = "Nationaldagen";
	$public_holidays[$pentecost] = "Midsommardagen";
	
	$public_holidays[$all_saints_day] = "Allahelgonadag";
	
	$public_holidays[$year.'-12-25'] = "Juldagen";
	$public_holidays[$year.'-12-26'] = "Annandag jul ";
	
	$flag_days[$year.'-01-01'] = "Nyårsdagen";
	$flag_days[$year.'-01-28'] = "Konungens namnsdag";
	$flag_days[$year.'-03-12'] = "Kronprinsessans namnsdag";
	$flag_days[$easter_day] = "Påskdagen";
	$flag_days[$year.'04-30'] =  "Konungens födelsedag";
	$flag_days[$year.'05-01'] =  "Första Maj";
	$flag_days[$pentecost] = "Pingstdagen";
	$flag_days[$year.'-06-06'] = "Sveriges nationaldag och svenska flaggans dag";
	$flag_days[$midsommer] = "Midsommardagen";
	$flag_days[$year.'-07-14'] = "Kronprinsessans födelsedag";
	$flag_days[$year.'-08-08'] = "Drottningens namnsdag";
	$flag_days[$year.'-10-24'] = "FN-dagen";
	$flag_days[$year.'-11-06'] = "Gustav Adolfsdagen";
	$flag_days[$year.'-12-10'] = "Nobeldagen";
	$flag_days[$year.'-12-23'] = "Drottningens födelsedag";
	$flag_days[$year.'-12-25'] = "Juldagen";

	/*
	var_dump($flag_days);
	echo "<br>";
	
	var_dump($public_holidays);
	echo "<br>";
	*/
    
    global $link;
	
	$table = PREFIX."activities";
	
	checkTable($table);
	
	if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
	{
		$table = PREFIX."activities";
		$sql = "SELECT * FROM $table WHERE datum >= CURDATE() ORDER BY datum ASC";	
	}
	else if (isset($_SESSION['uid']))
	{
		$table = PREFIX."activities";
		$table_2 = PREFIX."roles";
		$table_3 = PREFIX."mission";
		$sql = "SELECT * FROM $table LEFT outer JOIN $table_2 ON $table_2.assignment_id = $table.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum >= CURDATE() AND canceld = '0' ORDER BY datum ASC";	
	}
	else
	{
		$table = PREFIX."activities";
		$sql = "SELECT * FROM $table WHERE datum >= CURDATE() AND publi IS NULL AND canceld = '0' ORDER BY datum ASC";		
	}
	
	//echo $sql ."<br>";
	$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	
	while ($row = mysqli_fetch_array($result))
	{
		$activity[$row['datum']][] = $row['tableKey'];
		$end_activity[$row['datum']][$row['datum']] = $row['aid'];
		$header[$row['tableKey']] = fixutferror($row['rubrik']);
		$time[$row['tableKey']] = $row['tid'];	
		$valid_for[$row['tableKey']] = array_map('trim', explode(",", $row['valid_for']));
		$outside[$row['tableKey']] = $row['outside'];
	}
	
	foreach ($end_activity as $start_date => $value_1)
	{
		foreach ($end_activity[$start_date] as $end_date => $value_2)
		{
			if ($end_date != "0000-00-00" && !empty($end_date))
			{
				$datetime1 = new DateTime( $start_date);
				$datetime2 = new DateTime( $end_date);
				$interval = $datetime1->diff($datetime2);
				
				//echo $interval->format('%d');
				$days_2[$start_date][] = $interval->format('%d');
				for ($i = 1; $i <= $interval->format('%d'); $i++)
				{
					$date = date("Y-m-d", strtotime($start_date." +$i day"));
					$activity[$date][] = $value_2;	
				}
			}
			
			
		}
	}
	
	if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
	{
		$table = PREFIX."activities";
		$sql = "SELECT * FROM $table WHERE datum >= CURDATE() ORDER BY datum ASC";	
	}
	else if (isset($_SESSION['uid']))
	{
		$table = PREFIX."activities";
		$table_2 = PREFIX."roles";
		$table_3 = PREFIX."mission";
		$sql = "SELECT * FROM $table LEFT outer JOIN $table_2 ON $table_2.assignment_id = $table.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum >= CURDATE() AND canceld = '0' ORDER BY datum ASC";	
	}
	else
	{
		$table = PREFIX."activities";
		$sql = "SELECT * FROM $table WHERE datum >= CURDATE() AND publi IS NULL AND canceld = '0' ORDER BY datum ASC";		
	}
	//echo $sql ."<br>";
	$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	
	if (mysqli_num_rows($result) == 0)
	{
		echo "Oj, då!!! Det verkar inte finnas några inplanerade aktiviteter för stunden, detta kommer att åtgärdas snarast!<br>";	
	}
	else
	{
		echo "<div class=\"table-responsive\">";
			
			echo "<table class = \"table table-striped table-bordered table-hover \">";
				
				//Aktuell månad
				
				$end_date = date("Y-m-t");
				if ($end_date <= date("Y-m-d"))
				{
					$end_date =date("Y-m-d", strtotime('next month'));	
				}
				if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
				{
					$table = PREFIX."activities";
                    
					$sql = "SELECT * FROM $table WHERE datum >= CURDATE() AND datum <= '$end_date' ORDER BY datum ASC";	
				}
				else if (isset($_SESSION['uid']))
				{
					$table = PREFIX."activities";
					$table_2 = PREFIX."roles";
					$table_3 = PREFIX."mission";
                    
					$sql = "SELECT *, t1.tableKey as tableKey FROM $table t1 LEFT outer JOIN $table_2 ON $table_2.assignment_id = t1.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum >= CURDATE() AND datum <= '$end_date' AND canceld = '0'  ORDER BY datum ASC";	
				}
				else
				{
					$table = PREFIX."activities";
					$sql = "SELECT * FROM $table WHERE datum >= CURDATE() AND datum <= '$end_date' AND publi IS NULL AND canceld = '0' ORDER BY datum ASC";		
				}
		
				//echo $sql ."<br>";
				$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	
				if (mysqli_num_rows($result) > 0)
				{
					unset($activity, $end_activity,$header, $time, $valid_for, $outside);
					while ($row = mysqli_fetch_array($result))
					{
						$activity[$row['datum']][] = $row['tableKey'];
						$end_activity[$row['datum']][$row['datum']] = $row['tableKey'];
						$header[$row['tableKey']] = fixutferror($row['rubrik']);
						$time[$row['tableKey']] = $row['tid'];	
						$valid_for[$row['tableKey']] = array_map('trim', explode(",", $row['valid_for']));
						$outside[$row['tableKey']] = $row['outside'];
						$canceld[$row['tableKey']] = (int)$row['canceld'];
					}
					
					foreach ($end_activity as $start_date => $value_1)
					{
						foreach ($end_activity[$start_date] as $end_date => $value_2)
						{
							if ($end_date != "0000-00-00" && !empty($end_date))
							{
								$datetime1 = new DateTime( $start_date);
								$datetime2 = new DateTime( $end_date);
								$interval = $datetime1->diff($datetime2);
								
								//echo $interval->format('%d');
								$days_2[$start_date][] = $interval->format('%d');
								for ($i = 1; $i <= $interval->format('%d'); $i++)
								{
									$date = date("Y-m-d", strtotime($start_date." +$i day"));
									$activity[$date][] = $value_2;	
								}
							}
							
							
						}
					}
					echo "<tr>";
						$mounth =  date("n",mktime(0, 0, 0, date("m"), 1,   date("Y")));
						echo "<td cola = 8 align=\"center\"><h2>" .$mont[$mounth] ."</h2></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<th>Vecka</th><th>Måndag</th><th>Tisdag</th><th>Onsdag</th><th>Torsdag</th><th>Fredag</th><th>Lördag</th><th><font color=\"red\">Söndag</font></th>";
					echo "</tr>";
					echo "<tr>";	
						$start2 = date("Y-m-d",mktime(0, 0, 0, date("m")+0, 1,   date("Y")));
						$start = date("N",mktime(0, 0, 0, date("m")+0, 1,   date("Y")));
						$end = date("t",mktime(0, 0, 0, date("m")+0, 1,   date("Y")));
						$week =ltrim(date("W",mktime(0, 0, 0, date("m")+0,1,   date("Y"))), 0);
						echo "<td>$week</td>";
						$d = 1;
						for ($x = 1; $x <= 6; $x++)
						{
							for ($i = 1; $i <= 7; $i++)
							{
								if (($d > $end))
								{
									continue;
								}
								if ($i < $start && $x == 1)
								{
									
									{
										echo "<td>&nbsp;</td>";	
									}
								}
								else
								{
									if ($d <= $end)
									{
										$y = date("Y-m",mktime(0, 0, 0, date("m")+0, date("d"),   date("Y")));
										if ($d <= 9)
										{
											$y = $y."-0".$d;
										}
										else
										{
											$y = $y."-" .$d;	
										}
										
										if ($d ==  date("d"))
										{
											if (isset($_SESSION['uid']))
											{
												echo "<td class = \"clickMe\" bgcolor = 'lightblue' id = \"".$y."\">";
											}
											else
											{
												echo "<td bgcolor = 'lightblue'>";	
											}
										}
										else
										{
											if (isset($_SESSION['uid']) && $d >= date("d"))
											{
												echo "<td class = \"clickMe\" id = \"".$y."\">";
											}
											else
											{
												echo "<td>";
											}
										}
											
											if (isset($public_holidays[$y]))
											{
												echo "<font color = \"red\">".$d ."</font> ";
												if (isset($flag_days[$y]))
												{
													echo " <img src=\"img/Flag_of_Sweden_svg.png\" alt=\"Flaggdag\"/> ";
												}
												echo "<font color = \"grey\">" .$public_holidays[$y] ."</font><br>";
											}
											
											elseif ($i == 7)
											{
												echo "<font color = \"red\">".$d ."</font>";
												if (isset($other_holidays[$y]))
												{
													echo " <font color = \"grey\">" .$other_holidays[$y] ."</font>";
												}
												echo "<br>";
											}
											else
											{
												echo $d ." ";
												if (isset($other_holidays[$y]))
												{
													echo " <font color = \"grey\">" .$other_holidays[$y] ."</font>";
												}
												echo "<br>";
											}
											if (isset($activity[$y]))
											{
											
												if (array_key_exists('SA',$admin_group))
												{
													$print = true;	
												}
												$print = true;	
												foreach ($activity[$y] as $id => $value )
												{
													if (isset($valid_for[$value]))
													{
														if (!$print)
														{
															foreach ($group as $a)
															{
																$search = "~".$a;
																if (array_search($search, $valid_for[$value]) !== false)
																{
																	$print = true;
																	break;	
																}
															}
														}
														
														if (!$print)
														{
															foreach ($pof as $a)
															{
																$search = $a;
																if (array_search($search, $valid_for[$value]) !== false)
																{
																	$print = true;
																	break;	
																}
															}
														}
														
														if (!$print)
														{
															$search = "-" .$_SESSION['user_id'];
															if (array_search($search, $valid_for[$value]) !== false)
															{
																$print = true;
																break;	
															}
														}
														
														if ($print)
														{
															
															if (array_search("~^0", $valid_for[$value]) !== false)
															{
																if (($time[$value] != "00:00:00") && !empty($time[$value] ))
																{
																	$display = explode(":",  $time[$value]);
																	echo $display[0] .":";
																	if (empty($display[1]) && !empty($display[0]))
																	{
																		echo "00";	
																	}
																	else
																	{
																		echo $display[1];
																	}
																}
																echo " " .$header[$value];
															}
															else
															{
																
																echo "<a class = \"showActivity\" id = \"aid[".$value."]\">";
																if ((int)$canceld[$value] == 1)
																{
																	echo " <font color = \"blue\">";	
																}
																if (($time[$value] != "00:00:00") && !empty($time[$value] ))
																{
																	$display = explode(":",  $time[$value]);
																	echo $display[0] .":";
																	if (empty($display[1]) && !empty($display[0]))
																	{
																		echo "00";	
																	}
																	else
																	{
																		echo $display[1];
																	}
																}
																echo " " .$header[$value];
																if ((int)$canceld[$value] == 1)
																{
																	echo "</font>";	
																}
																echo "</a> ";
																
															}
														}
													}
													else
													{
														echo "<aclass = \"showActivity\"  id = \"aid[".$value."]\">";
														if ((int)$canceld[$value] == 1)
														{
															echo " <font color = \"blue\">";	
														}
														if (($time[$value] != "00:00:00") && !empty($time[$value] ))
														{
															$display = explode(":",  $time[$value]);
															echo $display[0] .":";
															if (empty($display[1]) && !empty($display[0]))
															{
																echo "00";	
															}
															else
															{
																echo $display[1];
															}
														}
														echo " " .$header[$value];
														if ((int)$canceld[$value] == 1)
														{
															echo " <font color = \"blue\">";	
														}
														echo "</a> ";
													}
												}
											}
										echo "</td>";
										$d++;
									}
									else
									{
										$d++;
										echo "<td>&nbsp</td>";	
									}
								}	
							}
							echo "</tr>";
							if ($d > $end)
							{
								continue;
							}
							$week =ltrim(date("W",mktime(0, 0, 0, date("m")+0,$d,   date("Y"))), 0);
							if ($d <= $end)
							{
								echo "<tr><td>$week</td>";
							}
						}
						
					echo "</tr>";
					
				}
			echo "</table>";
		echo "</div>";
		
		echo "<div class=\"table-responsive\">";
			
			echo "<table class = \"table table-striped table-bordered table-hover \">";
				//Aktuell månad +1
				
				$year = date("Y");
				$month = date("m")+1;
				if ($month > 12)
				{
					$month -=12;
					$year += 1;	
				}
				
				$start_date =  date("Y-m-d",mktime(0, 0, 0, date("m")+1, 1,   date("Y")));
				$start_date =  date("Y-m-d",mktime(0, 0, 0, $month, 1,   $year));
				$end_date = $mounth =  date("Y-m-t",mktime(0, 0, 0, $month, 1,   $year));
				//echo $start_date ."<br>";
				//echo $end_date ."<br>";
				
				if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
				{
					$table = PREFIX."activities";
					$sql = "SELECT * FROM $table WHERE datum >= '$start_date' AND datum <= '$end_date' ORDER BY datum ASC";	
				}
				else if (isset($_SESSION['uid']))
				{
					$table = PREFIX."activities";
					$table_2 = PREFIX."roles";
					$table_3 = PREFIX."mission";
					$sql = "SELECT *, t1.tableKey as tableKey FROM $table t1 LEFT outer JOIN $table_2 ON $table_2.assignment_id = t1.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum >= CURDATE() AND datum <= '$end_date' AND canceld = '0'  ORDER BY datum ASC";	
				}
				else
				{
					$table = PREFIX."activities";
					$sql = "SELECT * FROM $table WHERE datum >= '$start_date' AND datum <= '$end_date' AND publi IS NULL AND canceld = '0' ORDER BY datum ASC";		
				}
				//echo $sql ."<br>";
				$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	
				if (mysqli_num_rows($result) > 0)
				{
					unset($activity, $end_activity,$header, $time, $valid_for, $outside);
					while ($row = mysqli_fetch_array($result))
					{
						$activity[$row['datum']][] = $row['tableKey'];
						$end_activity[$row['datum']][$row['datum']] = $row['tableKey'];
						$header[$row['tableKey']] = fixutferror($row['rubrik']);
						$time[$row['tableKey']] = $row['tid'];	
						$valid_for[$row['tableKey']] = array_map('trim', explode(",", $row['valid_for']));
						$outside[$row['tableKey']] = $row['outside'];
					}
					foreach ($end_activity as $start_date => $value_1)
					{
						foreach ($end_activity[$start_date] as $end_date => $value_2)
						{
							if ($end_date != "0000-00-00" && !empty($end_date))
							{
								$datetime1 = new DateTime( $start_date);
								$datetime2 = new DateTime( $end_date);
								$interval = $datetime1->diff($datetime2);
								
								//echo $interval->format('%d');
								$days_2[$start_date][] = $interval->format('%d');
								for ($i = 1; $i <= $interval->format('%d'); $i++)
								{
									$date = date("Y-m-d", strtotime($start_date." +$i day"));
									$activity[$date][] = $value_2;	
								}
							}
							
							
						}
					}
					echo "<tr>";
						$mounth =  date("n",mktime(0, 0, 0, date("m")+1, 1,   date("Y")));
						echo "<td cola = 8 align=\"center\"><h2>" .$mont[$mounth] ."</h2></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<th>Vecka</th><th>Måndag</th><th>Tisdag</th><th>Onsdag</th><th>Torsdag</th><th>Fredag</th><th>Lördag</th><th><font color=\"red\">Söndag</font></th>";
					echo "</tr>";
					echo "<tr>";	
						$start2 = date("Y-m-d",mktime(0, 0, 0, date("m")+1, 1,   date("Y")));
						$start = date("N",mktime(0, 0, 0, date("m")+1, 1,   date("Y")));
						$end = date("t",mktime(0, 0, 0, date("m")+1, 1,   date("Y")));
						
						if ( date("m") == 12)
						{
							$year = date("Y")+1;
							$week =ltrim(date("W", mktime(0, 0, 0, 1 , 1, $year)), 0);
						}
						else
						{
							$week =ltrim(date("W", mktime(0, 0, 0, date("m")+1  , 1, date("Y"))), 0);
						}
						echo "<td>$week</td>";
						$d = 1;
						for ($x = 1; $x <= 6; $x++)
						{
							for ($i = 1; $i <= 7; $i++)
							{
								if (($d > $end))
								{
									continue;
								}
								if ($i < $start && $x == 1)
								{
									echo "<td>&nbsp;</td>";	
								}
								else
								{
									if ($d <= $end)
									{
											if ( date("m") == 12)
											{
												$year = date("Y");
												$y = ltrim(date("Y-m", mktime(0, 0, 0, date("m")+1  , 1,$year)), 0);
											}
											else
											{
												$y = ltrim(date("Y-m", mktime(0, 0, 0, date("m")+1  , 1, date("Y"))), 0);
											}
											if ($d <= 9)
											{
												$y = $y."-0".$d;
											}
											else
											{
												$y = $y."-" .$d;	
											}
										
											if (isset($_SESSION['uid']))
											{
												echo "<td class = \"clickMe\" id = \"".$y."\">";
											}
											else
											{
												echo "<td>";
											}
											//$y = date("Y-m",mktime(0, 0, 0, date("m")+1, date("d"),   date("Y")));
											/*if (date("m") == 1)
											{
												$y = date('Y-m', strtotime("+30 days"));	
												$y = $week =ltrim(date("Y-m", mktime(0, 0, 0, date("m")+1  , 1, date("Y"))), 0);
											}
											else
											{
												$y = date('Y-m', strtotime("+30 days"));
											}*/
											
											if (isset($public_holidays[$y]))
											{
												echo "<font color = \"red\">".$d ."</font> ";
												if (isset($flag_days[$y]))
												{
													echo " <img src=\"img/Flag_of_Sweden_svg.png\" alt=\"Flaggdag\"/> ";
												}
												echo "<font color = \"grey\">" .$public_holidays[$y] ."</font><br>";
											}
											
											elseif ($i == 7)
											{
												echo "<font color = \"red\">".$d ."</font>";
												if (isset($other_holidays[$y]))
												{
													echo " <font color = \"grey\">" .$other_holidays[$y] ."</font>";
												}
												echo "<br>";
											}
											else
											{
												echo $d ." ";
												if (isset($other_holidays[$y]))
												{
													echo " <font color = \"grey\">" .$other_holidays[$y] ."</font>";
												}
												echo "<br>";
											}
											
											if (isset($activity[$y]))
											{
												//echo __LINE__." ".__FILE__." $y<br>";
												if (array_key_exists('SA',$admin_group))
												{
													$print = true;	
												}
												$print = true;	
												foreach ($activity[$y] as $id => $value )
												{
													if (isset($valid_for[$value]))
													{
														if (!$print)
														{
															foreach ($group as $a)
															{
																$search = "~".$a;
																if (array_search($search, $valid_for[$value]) !== false)
																{
																	$print = true;
																	break;	
																}
															}
														}
														
														if (!$print)
														{
															foreach ($pof as $a)
															{
																$search = $a;
																if (array_search($search, $valid_for[$value]) !== false)
																{
																	$print = true;
																	break;	
																}
															}
														}
														
														if (!$print)
														{
															$search = "-" .$_SESSION['user_id'];
															if (array_search($search, $valid_for[$value]) !== false)
															{
																$print = true;
																break;	
															}
														}
														
														if ($print)
														{
															//echo __LINE__." ".__FILE__."<br>";
															if (array_search("~^0", $valid_for[$value]) !== false)
															{
																if (($time[$value] != "00:00:00") && !empty($time[$value] ))
																{
																	$display = explode(":",  $time[$value]);
																	echo $display[0] .":";
																	if (empty($display[1]) && !empty($display[0]))
																	{
																		echo "00";	
																	}
																	else
																	{
																		echo $display[1];
																	}
																}
																echo " " .$header[$value] ." ";
															}
															else
															{
																echo "<a class = \"showActivity\" id = \"aid[".$value."]\">";
																if ((int)$canceld[$value] == 1)
																{
																	echo " <font color = \"blue\">";	
																}
																if (($time[$value] != "00:00:00") && !empty($time[$value] ))
																{
																	$display = explode(":",  $time[$value]);
																	echo $display[0] .":";
																	if (empty($display[1]) && !empty($display[0]))
																	{
																		echo "00";	
																	}
																	else
																	{
																		echo $display[1];
																	}
																}
																echo " " .$header[$value];
																if ((int)$canceld[$value] == 1)
																{
																	echo " </font>";	
																}
																echo "</a> ";
															}
														}
													}
													else
													{
														echo "<a class = \"showActivity\" id = \"aid[".$value."]\">";
														if ((int)$canceld[$value] == 1)
														{
															echo " <font color = \"blue\">";	
														}
														if (($time[$value] != "00:00:00") && !empty($time[$value] ))
														{
															$display = explode(":",  $time[$value]);
															echo $display[0] .":";
															if (empty($display[1]) && !empty($display[0]))
															{
																echo "00";	
															}
															else
															{
																echo $display[1];
															}
														}
														echo " " .$header[$value];
														if ((int)$canceld[$value] == 1)
														{
															echo " </font>";	
														}
														echo "</a> ";
													}
												}
											}
										echo "</td>";
										$d++;
									}
									else
									{
										$d++;
										echo "<td>&nbsp</td>";	
									}
								}	
							}
							echo "</tr>";
							if ($d > $end)
							{
								continue;
							}
							$week++;
							if ($week >53 || date("W",mktime(0, 0, 0, date("m")+1,$d,   date("Y")))  == 1 )
							{
								$week = 1;	
							}
							if ($d <= $end)
							{
								echo "<tr><td>$week</td>";
							}
						}
						
					echo "</tr>";
					
				}
			echo "</table>";
		echo "</div>";
		
		echo "<div class=\"table-responsive\">";
			
			echo "<table class = \"table table-striped table-bordered table-hover \">";
				//Aktuell månad +2
				
				$year = date("Y");
				$month = date("m")+2;
				if ($month > 12)
				{
					$month -=12;
					$year += 1;	
				}
				
				$start_date =  date("Y-m-d",mktime(0, 0, 0, date("m")+2, 1,   date("Y")));
				$start_date =  date("Y-m-d",mktime(0, 0, 0, $month, 1,   $year));
				$end_date = $mounth =  date("Y-m-t",mktime(0, 0, 0, $month, 1,   $year));
				
				if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
				{
					$table = PREFIX."activities";
					$sql = "SELECT * FROM $table WHERE datum >= '$start_date' AND datum <= '$end_date' ORDER BY datum ASC";	
				}
				else if (isset($_SESSION['uid']))
				{
					$table = PREFIX."activities";
					$table_2 = PREFIX."roles";
					$table_3 = PREFIX."mission";
					$sql = "SELECT *, t1.tableKey as tableKey FROM $table t1 LEFT outer JOIN $table_2 ON $table_2.assignment_id = t1.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum >= CURDATE() AND datum <= '$end_date' AND canceld = '0'  ORDER BY datum ASC";
				}
				else
				{
					$table = PREFIX."activities";
					$sql = "SELECT * FROM $table WHERE datum >= '$start_date' AND datum <= '$end_date' AND publi IS NULL AND canceld = '0' ORDER BY datum ASC";		
				}
				//echo $sql ."<br>";
				$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	
				if (mysqli_num_rows($result) > 0)
				{
					unset($activity, $end_activity,$header, $time, $valid_for, $outside);
					while ($row = mysqli_fetch_array($result))
					{
						
						$activity[$row['datum']][] = $row['tableKey'];
						$end_activity[$row['datum']][$row['datum']] = $row['tableKey'];
						$header[$row['tableKey']] = fixutferror($row['rubrik']);
						$time[$row['tableKey']] = $row['tid'];	
						$valid_for[$row['tableKey']] = array_map('trim', explode(",", $row['valid_for']));
						$outside[$row['tableKey']] = $row['outside'];
					}
					foreach ($end_activity as $start_date => $value_1)
					{
						foreach ($end_activity[$start_date] as $end_date => $value_2)
						{
							if ($end_date != "0000-00-00" && !empty($end_date))
							{
								$datetime1 = new DateTime( $start_date);
								$datetime2 = new DateTime( $end_date);
								$interval = $datetime1->diff($datetime2);
								
								//echo $interval->format('%d');
								$days_2[$start_date][] = $interval->format('%d');
								for ($i = 1; $i <= $interval->format('%d'); $i++)
								{
									$date = date("Y-m-d", strtotime($start_date." +$i day"));
									$activity[$date][] = $value_2;	
								}
							}
							
							
						}
					}
					
					echo "<tr>";
						$mounth =  date("n",mktime(0, 0, 0, date("m")+2, 1,   date("Y")));
						echo "<td cola = 8 align=\"center\"><h2>" .$mont[$mounth] ."</h2></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<th>Vecka</th><th>Måndag</th><th>Tisdag</th><th>Onsdag</th><th>Torsdag</th><th>Fredag</th><th>Lördag</th><th><font color=\"red\">Söndag</font></th>";
					echo "</tr>";
					echo "<tr>";	
						$start2 = date("Y-m-d",mktime(0, 0, 0, date("m")+2, 1,   date("Y")));
						$start = date("N",mktime(0, 0, 0, date("m")+2, 1,   date("Y")));
						$end = date("t",mktime(0, 0, 0, date("m")+2, 1,   date("Y")));
						 
						
						if ( date("m") == 11)
						{
							$year = date("Y")+1;
							$week =ltrim(date("W", mktime(0, 0, 0, 1 , 1, $year)), 0);
						}
						else if ( date("m") == 12)
						{
							$year = date("Y")+1;
							$week =ltrim(date("W", mktime(0, 0, 0, 2 , 1, $year)), 0);
						}
						else if ( date("m") == 1)
						{
							$week =ltrim(date("W", mktime(0, 0, 0,  date("n")+2, 1, date("Y"))), 0);
							
						}
						else
						{
							$week =ltrim(date("W", mktime(0, 0, 0,  (date("n")+2), "1",date("Y"))), 0);
						}
						echo "<td>$week </td>";
						$d = 1;
						for ($x = 1; $x <= 6; $x++)
						{
							for ($i = 1; $i <= 7; $i++)
							{
								if (($d > $end))
								{
									continue;
								}
								if ($i < $start && $x == 1)
								{
									echo "<td>&nbsp;</td>";	
								}
								else
								{
									if ($d <= $end)
									{
											if ( date("m") == 12)
											{
												$year = date("Y")+1;
												$y = ltrim(date("Y-m", mktime(0, 0, 0, date("m")+2  , 1,$year)), 0);
											}
											else
											{
												$y = ltrim(date("Y-m", mktime(0, 0, 0, date("m")+2  , 1, date("Y"))), 0);
											}
											if ($d <= 9)
											{
												$y = $y."-0".$d;
											}
											else
											{
												$y = $y."-" .$d;	
											}
										
											if (isset($_SESSION['uid']))
											{
												echo "<td class = \"clickMe\" id = \"".$y."\">";
											}
											else
											{
												echo "<td>";
											}
											
											if ( date("m") == 11)
											{
												$year = date("Y")+2;
												$y = ltrim(date("Y-m", mktime(0, 0, 0, date("m")+2  , 1, $year)), 0);
											}
											if ( date("m") == 12)
											{
												$year = date("Y")+1;
												$y = ltrim(date("Y-m", mktime(0, 0, 0, date("m")+2  , 1, date("Y"))), 0);
											}
											else
											{
												$y = ltrim(date("Y-m", mktime(0, 0, 0, date("m")+2  , 1, date("Y"))), 0);
											}
											if ($d <= 9)
											{
												$y = $y."-0".$d;
											}
											else
											{
												$y = $y."-" .$d;	
											}
											if (isset($public_holidays[$y]))
											{
												echo "<font color = \"red\">".$d ."</font> ";
												if (isset($flag_days[$y]))
												{
													echo " <img src=\"img/Flag_of_Sweden_svg.png\" alt=\"Flaggdag\"/> ";
												}
												echo "<font color = \"grey\">" .$public_holidays[$y] ."</font><br>";
											}
											
											elseif ($i == 7)
											{
												echo "<font color = \"red\">".$d ."</font>";
												if (isset($other_holidays[$y]))
												{
													echo " <font color = \"grey\">" .$other_holidays[$y] ."</font>";
												}
												echo "<br>";
											}
											else
											{
												echo $d ." ";
												if (isset($other_holidays[$y]))
												{
													echo " <font color = \"grey\">" .$other_holidays[$y] ."</font>";
												}
												echo "<br>";
											}
											if (isset($activity[$y]))
											{
											
												if (array_key_exists('SA',$admin_group))
												{
													$print = true;	
												}
												$print = true;	
												foreach ($activity[$y] as $id => $value )
												{
													if (isset($valid_for[$value]))
													{
														if (!$print)
														{
															foreach ($group as $a)
															{
																$search = "~".$a;
																if (array_search($search, $valid_for[$value]) !== false)
																{
																	$print = true;
																	break;	
																}
															}
														}
														
														if (!$print)
														{
															foreach ($pof as $a)
															{
																$search = $a;
																if (array_search($search, $valid_for[$value]) !== false)
																{
																	$print = true;
																	break;	
																}
															}
														}
														
														if (!$print)
														{
															$search = "-" .$_SESSION['user_id'];
															if (array_search($search, $valid_for[$value]) !== false)
															{
																$print = true;
																break;	
															}
														}
														
														if ($print)
														{
															
															if (array_search("~^0", $valid_for[$value]) !== false)
															{
																if (($time[$value] != "00:00:00") && !empty($time[$value] ))
																{
																	$display = explode(":",  $time[$value]);
																	echo $display[0] .":";
																	if (empty($display[1]) && !empty($display[0]))
																	{
																		echo "00";	
																	}
																	else
																	{
																		echo $display[1];
																	}
																}
																echo " " .$header[$value] ." ";
															}
															else
															{
																echo "<a class = \"showActivity\" id = \"aid[".$value."]\">";
																if ((int)$canceld[$value] == 1)
																{
																	echo " <font color = \"blue\">";	
																}
																if (($time[$value] != "00:00:00") && !empty($time[$value] ))
																{
																	$display = explode(":",  $time[$value]);
																	echo $display[0] .":";
																	if (empty($display[1]) && !empty($display[0]))
																	{
																		echo "00";	
																	}
																	else
																	{
																		echo $display[1];
																	}
																}
																echo " " .$header[$value];
																if ((int)$canceld[$value] == 1)
																{
																	echo "</font>";	
																}
																echo "</a> ";
															}
														}
													}
													else
													{
														echo "<a class = \"showActivity\" id = \"aid[".$value."]\">";
														if ((int)$canceld[$value] == 1)
														{
															echo " <font color = \"blue\">";	
														}
														if (($time[$value] != "00:00:00") && !empty($time[$value] ))
														{
															$display = explode(":",  $time[$value]);
															echo $display[0] .":";
															if (empty($display[1]) && !empty($display[0]))
															{
																echo "00";	
															}
															else
															{
																echo $display[1];
															}
														}
														echo " " .$header[$value];
														if ((int)$canceld[$value] == 1)
														{
															echo "</font>";	
														}
														echo "</a> ";
													}
												}
											}
										echo "</td>";
										$d++;
									}
									else
									{
										$d++;
										echo "<td>&nbsp</td>";	
									}
								}	
							}
							echo "</tr>";
							if ($d > $end)
							{
								continue;
							}
							$week++;
							if ($week >53 || date("W",mktime(0, 0, 0, date("m")+2,$d,   date("Y")))  == 1 )
							{
								$week = 1;	
							}
							if ($d <= $end)
							{
								echo "<tr><td>".$week."</td>";
							}
						}
						
					echo "</tr>";
					
				}
				
				
			echo "</table>";
		echo "</div>";
    echo "</div>";    
	}
}

?>