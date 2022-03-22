<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";

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
include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/theme.php");
include_once($path."common/modal.php");
include_once($path."common/calendar.php");

$langStrings = getLangstrings();
$printHeader = $langStrings['printHeader'];
  

echo "<head>";
  echo "<meta charset=\"utf-8\">";
  echo "<meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\">";

  echo "<title>Young Friends - YF</title>";
    
  echo "<meta content=\"\" name=\"description\" content = \"".$printHeader[2]."\">";
  echo "<meta content=\"\" name=\"keywords\" content = \"".$printHeader[1]."\">";


  echo "<!-- Favicons -->";
  echo "<link href=\"".$url."img/yf.png\" rel=\"icon\">";
  echo "<link href=\"".$url."img/yf.png\" rel=\"apple-touch-icon\">";

  echo "<!-- Google Fonts -->";
  echo "<link href=\"https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i\" rel=\"stylesheet\">";

  echo "<!-- Vendor CSS Files -->";
  echo "<link href=\"".$url."assets/vendor/aos/aos.css\" rel=\"stylesheet\">";
  echo "<link href=\"".$url."assets/vendor/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">";
  echo "<link href=\"".$url."assets/vendor/bootstrap-icons/bootstrap-icons.css\" rel=\"stylesheet\">";
  echo "<link href=\"".$url."assets/vendor/boxicons/css/boxicons.min.css\" rel=\"stylesheet\">";
  echo "<link href=\"".$url."assets/vendor/glightbox/css/glightbox.min.css\" rel=\"stylesheet\">";
  echo "<link href=\"".$url."assets/vendor/remixicon/remixicon.css\" rel=\"stylesheet\">";
  echo "<link href=\"".$url."assets/vendor/swiper/swiper-bundle.min.css\" rel=\"stylesheet\">";

	echo "<link href=\"".$url."ext/dataTables/datatables.min.css\" rel=\"stylesheet\">";
	echo "<link href=\"".$url."ext/fancytree/dist/skin-win8/ui.fancytree.min.css\" rel=\"stylesheet\">";
	echo "<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css\">";
	echo "<link href=\"".$url."ext/titatoggle/dist/titatoggle-dist-min.css\" rel=\"stylesheet\">";

  echo "<!-- Template Main CSS File -->";
  echo "<link href=\"".$url."assets/css/style.css\" rel=\"stylesheet\">";

  echo "<!-- =======================================================";
  echo "* Template Name: Presento - v3.2.0";
  echo "* Template URL: https://bootstrapmade.com/presento-bootstrap-corporate-template/";
  echo "* Author: BootstrapMade.com";
  echo "* License: https://bootstrapmade.com/license/";
  echo "======================================================== -->";
echo "</head>";

echo "<body>";

  echo "<!-- ======= Header ======= -->";
  echo "<header id=\"header\" class=\"fixed-top d-flex align-items-center\">";
    echo "<div class=\"container d-flex align-items-center\">";
      //echo "<h1 class=\"logo me-auto\"><a href=\"index.php\">Presento<span>.</span></a></h1>";
      echo "<!-- Uncomment below if you prefer to use an image logo -->";
      echo "<a href=\"".$url."index.php\" class=\"logo me-auto\"><img src=\"".$url."img/logo.png\" alt=\"\"></a>";

      echo "<nav id=\"navbar\" class=\"navbar order-last order-lg-0\">";
            getMenu();        
        echo "<i class=\"bi bi-list mobile-nav-toggle\"></i>";
      echo "</nav><!-- .navbar -->";

      //echo "<a href=\"#about\" class=\"get-started-btn scrollto\">Get Started</a>";
    echo "</div>";
  echo "</header><!-- End Header -->";

  echo "<!-- ======= Hero Section ======= -->";
    $langStrings = getLangstrings();
    $index_hero = $langStrings['index_hero'];
  echo "<section id=\"hero\" class=\"d-flex align-items-center\">";

    echo "<div class=\"container\" data-aos=\"zoom-out\" data-aos-delay=\"100\">";
      echo "<div class=\"row\">";
        echo "<div class=\"col-xl-6\">";
          echo "<h1>".$index_hero[1]."</h1>";
          echo "<h2>".$index_hero[2]."</h2>";
          echo "<a href=\"#about\" class=\"btn-get-started scrollto\">".$index_hero[3]."</a>";
        echo "</div>";
      echo "</div>";
    echo "</div>";

  echo "</section><!-- End Hero -->";

  echo "<main id=\"main\">";

    echo "<!-- ======= Tabs Section ======= -->";
    $langStrings = getLangstrings();
    $index_about = $langStrings['index_about'];
    echo "<section id=\"about\" class=\"about\">";
      echo "<div class=\"container\" data-aos=\"fade-up\">";
        echo "<div class=\"row\">";
          echo "<div class=\"col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0\">";
            echo "<h3>".$index_hero[1]."</h3>";
            echo "<p>";
              echo $index_about[2];
          echo "</div>";
          echo "<div class=\"col-lg-6 order-1 order-lg-2 text-center\">";
            echo "<img src=\"".$url."/img/karnan.jpg\" alt=\"\" class=\"img-fluid\">";
          echo "</div>";
        echo "</div>";
      echo "</div>";
    echo "</section><!-- End Tabs Section -->";

    echo "<!-- ======= Tabs Section ======= -->";
    echo "<section id=\"activities\" class=\"tabs\">";
        $langStrings = getLangstrings();
        $index_activities = $langStrings['index_activities'];
      echo "<div class=\"container \" data-aos=\"fade-up\" >";
		echo "<div class = \"d-none  d-sm-none d-md-block\">";
			echo "<ul class=\"nav nav-tabs row d-flex\">";

				echo "<li class=\"nav-item col-3\">";
					echo "<a class=\"nav-link active show\" data-bs-toggle=\"tab\" data-bs-target=\"#currentActivities\">";
						echo "<h4>".$index_activities[1]."</h4>";
					echo "</a>";
				echo "</li>";

				echo "<li class=\"nav-item col-3\">";
					echo "<a class=\"nav-link\" data-bs-toggle=\"tab\" data-bs-target=\"#historicalActivities\">";
						echo "<h4>".$index_activities[2]."</h4>";
					echo "</a>";
				echo "</li>";

			echo "</ul>";

			echo "<div class=\"tab-content\">";
			  echo "<div class=\"tab-pane active show\" id=\"currentActivities\">";
				echo "<div class=\"row\">";
				  echo "<div class=\"col-lg-12 order-2 order-lg-1 mt-3 mt-lg-0\" data-aos=\"fade-up\" data-aos-delay=\"100\">";
					echo "<div id = \"ajaxResult\">";
					   start2();
                    echo "</div>";
				echo "</div>";
			  echo "</div>";
			  echo "<div class=\"tab-pane\" id=\"historicalActivities\">";
				echo "<div class=\"row\">";
				  echo "<div class=\"col-lg-12 order-2 order-lg-1 mt-3 mt-lg-0\">";
					if (isset($_SESSION['uid']) && isset($_SESSION['siteAdmin']))
						{
							$table = PREFIX."activities";
							$sql = "SELECT * FROM $table WHERE datum < CURDATE()  ORDER BY datum DESC LIMIT 10";	
						}
						else if (isset($_SESSION['uid']))
						{
							$table = PREFIX."activities";
							$table_2 = PREFIX."roles";
							$table_3 = PREFIX."mission";
							$sql = "SELECT * FROM $table LEFT outer JOIN $table_2 ON $table_2.assignment_id = $table.publi LEFT OUTER JOIN $table_3 ON $table_3.assignment_id = $table_2.assignment_id WHERE datum < CURDATE() AND canceld = '0' ORDER BY datum DESC LIMIT 10";	
						}
						else
						{
							$table = PREFIX."activities";
							$outside = true;
							$sql = "SELECT * FROM $table WHERE datum < CURDATE() AND publi IS NULL AND canceld = '0' ORDER BY datum DESC LIMIT 10";		
						}

						$result= mysqli_query($link,$sql ) or die ('Error: '.mysqli_error ($link));	

						if (mysqli_num_rows($result) > 0)
						{
							echo "<div class=\"table-responsive\">";
							echo "<table class=\"table\">";
								while ($row = mysqli_fetch_array($result))
								{
									echo "<tr><td width = 10% align=\"center\">" .$row['datum'] ."<br>";
									if ($row['time'] != "00:00:00" && !empty($row['tid']))
									{
										$display = explode(":",  $row['tid']);
										echo $display[0] .":";
										if (empty($display[1]) )
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
					echo "</div>";
				echo "</div>";
			  echo "</div>";
			//echo "</div>";
			echo "</div>";
      echo "</div>";
	echo "<div class = \"d-block d-md-none\">";
		start3();
	echo "</div>";
    echo "</section><!-- End Tabs Section -->";

    echo "<!-- ======= Frequently Asked Questions Section ======= -->";
    $langStrings = getLangstrings();
    $index_faq = $langStrings['index_faq'];
    echo "<section id=\"faq\" class=\"faq\">";
      echo "<div class=\"container\" data-aos=\"fade-up\">";
        echo "<div class=\"section-title\">";
          echo "<h2>".$index_faq[1]."</h2>";
        echo "</div>";

        echo "<ul class=\"faq-list accordion\" data-aos=\"fade-up\">";
            $table = "`".PREFIX."faq`";
    
            checkTable($table);

            $replaceTable = getReplaceTable();

            $sql = "SELECT node.*, (COUNT(parent.lft) - 1) AS depth
                        FROM ".$table." AS node,
                                ".$table." AS parent
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        GROUP BY node.lft
                        ORDER BY node.lft;";
            //echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                echo "<li>";
                    echo "<a data-bs-toggle=\"collapse\" class=\"collapsed\" data-bs-target=\"#".$row['tableKey']."\">".$row['question']." <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-x icon-close\"></i></a>";
                    
                    echo "<div id=\"".$row['tableKey']."\" class=\"collapse\" data-bs-parent=\".faq-list\">";
                        echo "<p>";
                            echo $row['answer'];
                        echo "</p>";
                    echo "</div>";
                echo "</li>";
            }
          

          /*echo "<li>";
            echo "<a data-bs-toggle=\"collapse\" data-bs-target=\"#faq2\" class=\"collapsed\">Feugiat scelerisque varius morbi enim nunc faucibus a pellentesque? <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-x icon-close\"></i></a>";
            echo "<div id=\"faq2\" class=\"collapse\" data-bs-parent=\".faq-list\">";
              echo "<p>";
                echo "Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.";
              echo "</p>";
            echo "</div>";
          echo "</li>";

          echo "<li>";
            echo "<a data-bs-toggle=\"collapse\" data-bs-target=\"#faq3\" class=\"collapsed\">Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi? <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-x icon-close\"></i></a>";
            echo "<div id=\"faq3\" class=\"collapse\" data-bs-parent=\".faq-list\">";
              echo "<p>";
                echo "Eleifend mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Faucibus pulvinar elementum integer enim. Sem nulla pharetra diam sit amet nisl suscipit. Rutrum tellus pellentesque eu tincidunt. Lectus urna duis convallis convallis tellus. Urna molestie at elementum eu facilisis sed odio morbi quis";
              echo "</p>";
            echo "</div>";
          echo "</li>";

          echo "<li>";
            echo "<a data-bs-toggle=\"collapse\" data-bs-target=\"#faq4\" class=\"collapsed\">Ac odio tempor orci dapibus. Aliquam eleifend mi in nulla? <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-x icon-close\"></i></a>";
            echo "<div id=\"faq4\" class=\"collapse\" data-bs-parent=\".faq-list\">";
              echo "<p>";
                echo "Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.";
              echo "</p>";
            echo "</div>";
          echo "</li>";

          echo "<li>";
            echo "<a data-bs-toggle=\"collapse\" data-bs-target=\"#faq5\" class=\"collapsed\">Tempus quam pellentesque nec nam aliquam sem et tortor consequat? <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-x icon-close\"></i></a>";
            echo "<div id=\"faq5\" class=\"collapse\" data-bs-parent=\".faq-list\">";
              echo "<p>";
                echo "Molestie a iaculis at erat pellentesque adipiscing commodo. Dignissim suspendisse in est ante in. Nunc vel risus commodo viverra maecenas accumsan. Sit amet nisl suscipit adipiscing bibendum est. Purus gravida quis blandit turpis cursus in";
              echo "</p>";
            echo "</div>";
          echo "</li>";

          echo "<li>";
            echo "<a data-bs-toggle=\"collapse\" data-bs-target=\"#faq6\" class=\"collapsed\">Tortor vitae purus faucibus ornare. Varius vel pharetra vel turpis nunc eget lorem dolor? <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-x icon-close\"></i></a>";
            echo "<div id=\"faq6\" class=\"collapse\" data-bs-parent=\".faq-list\">";
              echo "<p>";
                echo "Laoreet sit amet cursus sit amet dictum sit amet justo. Mauris vitae ultricies leo integer malesuada nunc vel. Tincidunt eget nullam non nisi est sit amet. Turpis nunc eget lorem dolor sed. Ut venenatis tellus in metus vulputate eu scelerisque. Pellentesque diam volutpat commodo sed egestas egestas fringilla phasellus faucibus. Nibh tellus molestie nunc non blandit massa enim nec.";
              echo "</p>";
            echo "</div>";
          echo "</li>";*/

        echo "</ul>";

      echo "</div>";
    echo "</section><!-- End Frequently Asked Questions Section -->";

    echo "<!-- ======= Contact Section ======= -->";
    echo "<section id=\"contact\" class=\"contact\">";
      echo "<div class=\"container\" data-aos=\"fade-up\">";

		$langStrings = getLangstrings();
    	$contact = $langStrings['contact'];

        echo "<div class=\"section-title\">";
          echo "<h2>".$contact[1]."</h2>";
          echo "<p>".$contact[2]."</p>";
        echo "</div>";

        echo "<div class=\"row\" data-aos=\"fade-up\" data-aos-delay=\"100\">";
          echo "<div class=\"col-lg-12\">";
            echo "<form action=\"forms/contact.php\" id = \"contactForm\" method=\"post\" role=\"form\" class=\"php-email-form\">";
              echo "<div class=\"row\">";
                echo "<div class=\"col form-group\">";
                  echo "<input type=\"text\" name=\"name\" class=\"form-control\" id=\"name\" placeholder=\"".$contact[3]."\" required>";
                echo "</div>";
                echo "<div class=\"col form-group\">";
                  echo "<input type=\"email\" class=\"form-control\" name=\"email\" id=\"email\" placeholder=\"".$contact[4]."\" required>";
                echo "</div>";
              echo "</div>";
              echo "<div class=\"form-group\">";
                echo "<input type=\"text\" class=\"form-control\" name=\"subject\" id=\"subject\" placeholder=\"".$contact[5]."\" required>";
              echo "</div>";
              echo "<div class=\"form-group\">";
                echo "<textarea class=\"form-control tinyMceArea\" name=\"message\" rows=\"5\" placeholder=\"".$contact[6]."\" required></textarea>";
              echo "</div>";
              echo "<div class=\"my-3\">";
                echo "<div class=\"loading\">Loading</div>";
                echo "<div class=\"error-message\"></div>";
                echo "<div class=\"sent-message\">Your message has been sent. Thank you!</div>";
              echo "</div>";
              echo "<div class=\"text-center\"><button type=\"submit\" class = \"sendContactMessage\">".$contact[7]."</button></div>";
            echo "</form>";
          echo "</div>";

        echo "</div>";

      echo "</div>";
    echo "</section><!-- End Contact Section -->";

  echo "</main><!-- End #main -->";

 

    echo "<div class=\"container d-md-flex py-4\">";

      echo "<div class=\"me-md-auto text-center text-md-start\">";
        echo "<div class=\"copyright\">";
          echo "&copy; Copyright <strong><span>Young Friends 2001 - ".date("Y")."</span></strong>. Alla rätigheter reserverade!";
        echo "</div>";
        echo "<div class=\"credits\">";
          echo "<!-- All the links in the footer should remain intact. -->";
          echo "<!-- You can delete the links only if you purchased the pro version. -->";
          echo "<!-- Licensing information: https://bootstrapmade.com/license/ -->";
          echo "<!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/presento-bootstrap-corporate-template/ -->";
          echo "Tema \"Presento\" av\" <a href=\"https://bootstrapmade.com/\" target= \"_blank\">BootstrapMade</a>";
        echo "</div>";
      
    echo "</div>";

	print_modal();
  echo "</footer><!-- End Footer -->";

  echo "<a href=\"#\" class=\"back-to-top d-flex align-items-center justify-content-center\"><i class=\"bi bi-arrow-up-short\"></i></a>";

  echo "<!-- Vendor JS Files -->";
	echo "<script src=\"".$url."ext/jquery/jquery.min.js\"></script>";
	echo "<script src=\"".$url."ext/jquery/jquery-ui.min.js\"></script>";
  echo "<script src=\"".$url."assets/vendor/aos/aos.js\"></script>";
  echo "<script src=\"".$url."assets/vendor/bootstrap/js/bootstrap.bundle.min.js\"></script>";
  echo "<script src=\"".$url."assets/vendor/glightbox/js/glightbox.min.js\"></script>";
  echo "<script src=\"".$url."assets/vendor/isotope-layout/isotope.pkgd.min.js\"></script>";
  echo "<script src=\"".$url."assets/vendor/php-email-form/validate.js\"></script>";
  echo "<script src=\"".$url."assets/vendor/purecounter/purecounter.js\"></script>";
  echo "<script src=\"".$url."assets/vendor/swiper/swiper-bundle.min.js\"></script>";

	echo "<script src=\"".$url."ext/dataTables/datatables.min.js\"></script>";
	echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js\"></script>";
	echo "<script src=\"".$url."ext/selectpicker/dist/js/i18n/defaults-sv_SE.min.js\"></script>";
	echo "<script src=\"".$url."ext/fancytree/dist/jquery.fancytree-all-deps.min.js\"></script>";
	echo "<script src=\"".$url."ext/tinymce/js/tinymce/tinymce.min.js\"></script>";
	echo "<script src=\"".$url."ext/tinymce/js/tinymce/jquery.tinymce.min.js\"></script>";
	echo "<script src=\"".$url."ext/jquery_jeditable/dist/jquery.jeditable.min.js\"></script>";
	echo "<script src=\"https://www.google.com/recaptcha/api.js?render=6LcjdZ0UAAAAACnmmP5s65vXAVUc7KLJxSaDi4lF\"></script>";
	
  echo "<!-- Template Main JS File -->";
  echo "<script src=\"".$url."assets/js/main.js\"></script>";
echo "<script src=\"".$url."index.js\"></script>";

echo "</body>";

echo "</html>";

?>