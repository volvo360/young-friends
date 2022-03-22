<?php
ob_start();
session_start();
echo "<meta name=\"robots\" content=\"noindex\" />";
start();
function start()
{
	//Tabort eventuella cookies
		setcookie ("YF_user", "5",1, '/');
		setcookie ("YF_pass", "3",1, '/');	
		setcookie ("YF_user", "5",1);
		setcookie ("YF_pass", "3",1);	
		unset ($_SESSION['id']);
		//Förstör eventuella sessioner
		session_destroy();
		session_unset();
		header("Location:index.php");
		echo"<p class='content'>Du är nu utloggad. Inom femsekunder kommer du att sändas till vår startsida. <p><br>";
}

?>