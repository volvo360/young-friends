<?php
// Låt användaren bli inloggad vi har en cookie på datorn...
if (!isset($_SESSION['id']))
{
	autologin();
}
//*****************************
//Funktion för att vissa bilden som ingår i pdf
function imagepblad($aid)
{
	global $link;
	
	$imagepath = "bilder/pblad/images/";
	$timagepath = $imagepath ."thumbs/";
	
	echo "<br>";
	$sql= "SELECT * FROM aktiviteter WHERE aid = $aid";
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	$rad=mysqli_fetch_array($result);
	$imageid = $rad['imageid'];
	if (isset($imageid))
	{
		$sql= "SELECT * FROM bilder_prog WHERE id = $imageid";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		$rad=mysqli_fetch_array($result);
		$id = $rad['id'];
		$_SESSION[bildid]  = $id;
	
		$typ = $rad['filext'];
		$path = $timagepath .$id .$typ;
		echo "<br>Följande bild används när pdf filen skapas: <br>";
		echo "<img src = $path ><br>";
		/*echo "Vill du ändra bild? ";?> <button OnClick = "javascript:unhide('showthumbs');">  Tryck här!</button> <?php*/
		echo "Vill du ändra bild? ";?> <form><input type=button value="Tryck här!" onClick="javascript:popUp('popup_chose_image.php')"> </form> <?php	
	}
	else
	{
		/*echo "Vill du lägga till en bild? ";?> <button OnClick = "javascript:unhide('showthumbs');">  Tryck här!</button> <?php	*/
		
		echo "Vill du lägga till en bild? ";?><form><input type=button value="Tryck här!" onClick="javascript:popUp('popup_chose_image.php')"></form> <?php	
	}
	echo "<div id = 'showthumbs' class='hidden'>";
    showthumbs($id);
	echo "</div>";
}

//*****************************
//Funktion för att visa alla bilder som vi har tillgängliga via databasen
function showthumbs($id)
{
	global $link;
	//echo "Hej!<br>";
	$imagepath = "bilder/pblad/images/";
	$timagepath = $imagepath ."thumbs/";
	$sql= "SELECT * FROM bilder_prog";
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	$i = "0";
	$maxrow = "3";
	echo "<form action = '" .$_SERVER['PHP_SELF'] ."' method = 'post' >"; 
	echo "<table border = '0'>";
	echo "<tr>";
	while ($nt=mysqli_fetch_array($result))
	{
		if ($i < $maxrow)
		{
			$fil = $timagepath .$nt[id] .$nt[filext];
			echo "<td>";
			if ($nt[id] == $id)
			{
				echo "<input name='id' type='radio' value= $nt[id] CHECKED>";
			}
			else
			{
				echo "<input name='id' type='radio' value= $nt[id]>";
			}
			echo "<img src = $fil>";
			echo "</td>";
			$i++;
		}
		if ($i == $maxrow)
		{
			echo "</tr><tr>";
			$i = "0";
		}
	}
	echo "</tr></table>";
	if (isset ($id))
	{
		echo "<input name='id' type='radio' value= '-1'> Ingen bild önskas!<br>";
	}
	else
	{
		echo "<input name='id' type='radio' value= '-1' CHECKED> Ingen bild önskas!<br>";
	}
	echo "<input name='submit' type='submit' value='Sumbit' class='form'>";
	echo "</form>";
}

//*****************************
//Funktion för att skriva nytt värde till databasen att vi har ändrat bild id

function updateimage($imageid, $aid)
{
	global $link;
	
	if ($imageid == '-1')
	{
		$sql= "UPDATE aktiviteter SET imageid = NULL WHERE aid = $aid";
	}
	else
	{
		$sql= "UPDATE aktiviteter SET imageid = '$imageid' WHERE aid = $aid";
	}
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
}


//**********************************
//Bara för att ersätta de tecken som gästboken ej vill följa övriga delar av hemsidan. Bör med lite tur bara vara en temporär lösning
function swereplace($in)
{
	$in = str_replace("å","&aring;",$in);
	$in = str_replace("Å","&Aring;",$in);
	$in = str_replace("ä","&auml;",$in);
	$in = str_replace("Ä","&Auml;",$in);
	$in = str_replace("ö","&ouml;",$in);
	$in = str_replace("Ö","&Ouml;",$in);
	return ($in);
}

//**********************************
//Bara för att de som ha cookie ska bli inloggad per automatik på ev sida.
function autologin()
{
	global $link;
	if (isset ($_COOKIE['YF']))
	{
		//header("Location: ".$_SERVER['SERVER_NAME']);

		foreach ($_COOKIE['YF'] as $name => $value) 
		{
			if (!isset ($user))
			{
				$user = $value;
			}
			else 
			{
				$pass = $value;
			}
		}
		$sql= "SELECT * FROM user WHERE username = '$user'  AND password = '$pass'";
		//echo __LINE__ ." " . basename(__FILE__)." ".$sql ."<br>";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		if (($rad=mysqli_fetch_array($result)))
		{
			if ($rad[betalt] >= date ("Y-m-d"))
			{
				$_SESSION['id'] = $rad[uid];
			}
			elseif ($rad[testmember] >= date ("Y-m-d"))
			{
				$_SESSION['id'] = $rad[uid];
				$_SESSION['testmember'] = $rad[uid];
			}
			elseif ($rad[testmember] <= date ("Y-m-d"))
			{
				echo "<script language=\"JavaScript\">\n"; 
				echo "alert(\"Din provperioden är över!\nDu får överväga om du vill bli medlem.\");\n"; 
				echo "</script>";
				setcookie ("YF[user]", "$user",0, '/');
				setcookie ("YF[pass]", "$pass",0, '/');
                exit();	
			}
			else
			{
				echo "<script language=\"JavaScript\">\n"; 
				echo "alert(\"Du har ej avlagt medlemsavgiften för detta år!!!\");\n"; 
				echo "</script>";
				setcookie ("YF[user]", "$user",0, '/');
				setcookie ("YF[pass]", "$pass",0, '/');
                exit();	
			}
			setcookie ("YF[user]", "$user",time()+60*60*24*30, '/');
			setcookie ("YF[pass]", "$pass",time()+60*60*24*30, '/');
			$_SESSION['id'] = $rad[uid];
			if ($rad['admin'] == 1)
			{
				$_SESSION[gbadmin] = 1;
				$_SESSION[adm] = 1;
			}
			
			//För att uppdatera när vi loggade in senast
			$currentdate = date("Y-m-d H:i:s");
			$uid = $rad[uid];
			$_SESSION['namn'] = utf8_encode($rad['fnamn']) ." " .utf8_encode($rad['enamn']);
			$_SESSION['usernamn'] = $rad['username'];
			$sql= "UPDATE user SET lastlogindate = '$currentdate' WHERE uid= $uid";
			$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
			$sql= "SELECT * FROM uppdrag WHERE uid = '$uid'  AND level >1";
			$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
			if (($rad=mysqli_fetch_array($result)))
			{
				$_SESSION['it-admin'] = 1;	
			}
			$ret = $PHP_SELF;
			//header("location: $ret");
		}
	}
}

//******************************
//Funktion för att korta av texter om de är förlånga

function Truncate ($str, $length, $trailing)
{
  if (strlen($str) > $length)
  {
	/* take off chars for the trailing */
    $length-=strlen($trailing);
	/* string exceeded length, truncate and add trailing dots */
    return substr($str,0,$length).$trailing;
  }
  else
  {
	/* string was already short enough, return the string */
    $res = $str;
  }        
  return $res;
} 

//******************************
//Funktion för att visa de senaste kommentarerna

function showlastcomment()
{
	global $link;
	//Kommentarer ska bara synnas på programsidan och för de som har ett konto..."
	$junk = $_SERVER['PHP_SELF'];
	$junk = stristr($junk, "logout.php");
	
	if (strlen($junk) == 0 && isset($_SESSION['id']))
	{
	
	$sql = "SELECT * FROM ( SELECT * FROM `kommentar`  ORDER BY kommentar.tid DESC LIMIT 100) AS temp INNER JOIN aktiviteter ON temp.aid = aktiviteter.aid WHERE publi = '' OR publi = '0' OR publi IS NULL GROUP BY temp.aid DESC ORDER BY temp.kid DESC LIMIT 5"; 
		
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		echo"<br>De fem senaste kommentarena som &auml;r kopplade till olika aktiviter: <br><hr>";
		echo "</hr><br>";
		
		while ($rad=mysqli_fetch_array($result))
		{
			if (!empty($rad['rubrik']))	
			{
				$rubrik = wordwrap($rad['rubrik'], 20, "\n", true);
				$rubrik = swereplace($rubrik);
				echo"<p class='commentText'><b>Rubrik : </b>" ."<a href='aktivitet.php?aid=" .$rad['aid'] ."#" .$rad['kid'] ."' class='commentText' >" .nl2br($rubrik) ."</a><br>";
			}
			$comment = Truncate($rad['kommentar'],100,"...");
			$comment = swereplace($comment);
			echo "<p class='commentText'><b>Kommentar : </b>" ."<a href='aktivitet.php?aid=" .$rad['aid'] ."#" .$rad['kid'] ."' class='commentText' >" .nl2br($comment) ."</a><br><br>";
		}
	}
		
}



//*******************************
//Funktion för att hälsa medlemar välkommna

function medlem()
{
	global $link;
	// Låt användaren bli inloggad vi har en cookie på datorn...
	if (!isset($_SESSION['id']))
	{
		autologin();
	}
	
	if (isset ($_SESSION['namn']))
	{
		$medlem = $_SESSION['username'] ." ("  .$_SESSION['namn'] .")";
		$medelm = swereplace($medlem);
		echo"<p ALIGN = 'right' class='content'>V&auml;lkommen <a href='../personal.php'>" .$_SESSION['username'] ." ("  .$_SESSION['namn'] .")</a> <class='content1'><br><br> ";echo "<p ALIGN = 'right' class='content'>";
		echo "<table border=\"0\">";
		echo "<tr>";
		if (isset($_SESSION['border']))
		{
			echo "<td valign = bottom><a href='../border.php'><img src=\"../image/Buttons/styrelse.png\" alt=\"Styrelsen\" ></a></td>";
		} 
		if (isset($_SESSION['it-admin']))
		{
			echo "<td valign = bottom><a href='../border.php'><img src=\"../image/Buttons/styrelse.png\" alt=\"Styrelsen\" ></a></td>";
			echo "<td><a href='../adminroller.php'><img src=\"../image/Buttons/postadm.png\" alt=\"admin poster\" ></a><br>";
			echo "<a href='../adminsite.php'><img src=\"../image/Buttons/siteadm.png\" alt=\"Siteamdin\" /></a></td>";	
		} 
				
		echo "<td><a href='../personal.php'> <img src=\"../image/Buttons/uppgifter.png\"  alt=\"Personlig info\" border = '0'/></td>";
		echo "<td></a> <a href='../logout.php'> <img src=\"../image/Buttons/utlogg.png\" alt=\"Logga ut\" border = '0'/></a></td></tr>";
		echo "</table>";
		echo "<br>";
		
	}
	else if ($_SESSION['id'])
	{	
		$id =$_SESSION['id'];
		$sql= "SELECT * FROM user WHERE uid=$id";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		$nt=mysqli_fetch_array($result);
		//För att slå samman förnamn och efternamn
		$namn = $nt['fnamn'] ." " .$nt['enamn'];
		$_SESSION['namn'] = $namn;
		$_SESSION['usernamn'] = $nt['username'];
		$sql= "SELECT * FROM uppdrag WHERE uid = '$uid'  AND level >1";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		if (($rad=mysqli_fetch_array($result)))
		{
			$_SESSION['it-admin'] = 1;	
		}
		$sql= "SELECT * FROM uppdrag WHERE uid = '$uid'  AND level = 1";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		if (($rad=mysqli_fetch_array($result)))
		{
			$_SESSION['border'] = 1;	
		}
		echo"<p ALIGN = 'right' class='content'>V&auml;lkommen <a href='../../personal.php'>" .$nt['username'] ." ("  .$namn .")</a><p><br>";	
	}
	else
	{
		echo"<p ALIGN = 'right' class='content'>V&auml;lkommen g&auml;st till v&aring;r hemsida!<p>";	
	}
}
//***************
//Trimma till antal

function trimantal($in)
{
	$out = preg_replace("/[^0-9]/", '', $in); 
	return $out;
	
}

//***************
//Trimma till tidsangivelsen

function trimtime($timein)
{
	$timeout =  preg_replace("/[^0-9]/", '', $timein); 
	
	$length = strlen($timeout);
	if ($length == 0 ||($length > 6))
	{
		?>
		<script language=javascript> 
		alert("Du har angett en tid som är felaktig!!!"); 
		</script>
		<?php
		$timeout = "0000";
	}
	
	elseif ($length < 3)
	{
		?>
		<script language=javascript> 
		alert("Du har angett en tid som är förkort!!!\nKontrollera att sidan har tolkat tiden rätt!"); 
		</script>
		<?php
		if ($length == 1)
		{
			$timeout = "0" .$timeout;	
		}
		$timeout = $timeout ."00";
	}	
	elseif ($length < 4)
	{
		?>
		<script language=javascript> 
		alert("Du har angett en tid som är förkort!!!\nKontrollera att sidan har tolkat tiden rätt!"); 
		</script>
		<?php
		$timeout ="0" .$timeout;
		
	}	
	//else
	{
		//Testa och se om vi har rätt tid på timmar och minuter
		if (substr($timeout,0,2) > 23)
		{
			?>
			<script language=javascript> 
			alert("Du har angett ett timatl som är felaktig!!! \nDet kommer att ändras till 00"); 
			</script>
			<?php
			$timeout =  "00" .substr($timeout,2,2);
		}
		if (substr($timeout,2,2) > 59)
		{
			?>
			<script language=javascript> 
			alert("Du har angett ett minutal som är felaktig!!! \nDet kommer att ändras till 00"); 
			</script>
			<?php
			$timeout = substr($timeout,0,2) ."00";
		}
		
		//För att infoga semikolon mellan timmarna och minuterna
		$timeout = substr($timeout,0,2) .":" .substr($timeout,2,2);
	}
	return $timeout;
}

//*****************************
//Funktion för att testa och trimma till datum som har matats in

function trimdate($datum)
{
	$tt = $datum;
	if (strlen($datum) == 10)
	{
		$t = $tt;
		$datum=str_replace("/","-",$datum);
		$t = str_replace("/","-",$t);
		$t = str_split ($t);
		if ($t[4] == "-")
		{
			$datum = $tt;
			$datum = explode("-",$datum);
			$datum = date("Y-m-d",mktime(0,0,0,$datum[1],$datum[2],$datum[0]));
		}
	}
	else if (strlen($datum) == 8)
	{
		$t = $tt;
		$datum=str_replace("/","-",$datum);
		$t = str_replace("/","-",$t);
		$t = str_split ($t);
		echo "t = $t[2] <br>";
		if ($t[2] == "-")
		{
			$datum = $tt;
			$datum = explode("-",$datum);
			$datum = date("Y-m-d",mktime(0,0,0,$datum[1],$datum[2],$datum[0]));
		}
		else
		{
			$year = substr($datum,0,4);
			$month = substr($datum,4,2); 
			$day = substr($datum,6,2); 
			$datum = date("Y-m-d", mktime(0,0,0, $month, $day, $year));
		}
	}
	else if (strlen($datum) == 6)
	{
		$t = $tt;
		$datum=str_replace("/","-",$datum);
		$t = str_replace("/","-",$t);
		
		if (substr($t,2,1) == "-")
		{
			$datum = $tt;
			$datum = explode("-",$datum);
			$datum = date("Y-m-d",mktime(0,0,0,$datum[1],$datum[2],$datum[0]));
		}
		else
		{
			$year = substr($t,0,2);
			$month = substr($t,2,2); 
			$day = substr($t,4,2); 
			$datum = date("Y-m-d", mktime(0,0,0, $month, $day, $year));
		}
	}
	return $datum;
}

//För att göra Avbrytningar när vi ha med a href

function wordWrapIgnoreHTML($string, $length = 45, $wrapString = "\n") 
   { 
     $wrapped = ''; 
     $word = ''; 
     $html = false; 
     $string = (string) $string; 
     for($i=0;$i<strlen($string);$i+=1) 
     { 
       $char = $string[$i]; 
       
       /** HTML Begins */ 
       if($char === '<') 
       { 
         if(!empty($word)) 
         { 
           $wrapped .= $word; 
           $word = ''; 
         } 
         
         $html = true; 
         $wrapped .= $char; 
       } 
       
       /** HTML ends */ 
       elseif($char === '>') 
       { 
         $html = false; 
         $wrapped .= $char; 
       } 
       
       /** If this is inside HTML -> append to the wrapped string */ 
       elseif($html) 
       { 
         $wrapped .= $char; 
       } 
       
       /** Whitespace characted / new line */ 
       elseif($char === ' ' || $char === "\t" || $char === "\n") 
       { 
         $wrapped .= $word.$char; 
         $word = ''; 
       } 
       
       /** Check chars */ 
       else 
       { 
         $word .= $char; 
         
         if(strlen($word) > $length) 
         { 
           $wrapped .= $word.$wrapString; 
           $word = ''; 
         } 
       } 
     } 

    if($word !== ''){ 
        $wrapped .= $word; 
    } 
     
     return $wrapped; 
   } 

?>