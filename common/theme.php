<?php

session_start();

if ($_SERVER['HTTP_HOST'] == "young-friends.org")
{
	header("Location: https://www.young-friends.org");
}

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

function getMenuData()
{
    global $link;
	
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
		
		//echo __LINE__." ".$pass."<br>";
		
		include_once($path."ajaxLogin.php");
	}
	
	else if (isset($_COOKIE["YF_user"]) && !isset($_SESSION['uid'])) 
	{
		$_POST['mail'] = $_COOKIE["YF_user"];
		$_POST['password'] = $_COOKIE["YF_pass"];
		
		$user = mysqli_real_escape_string($link, $_POST['mail']);
		$pass = mysqli_real_escape_string($link, $_POST['password']);
		//echo __LINE__." ".$pass."<br>";
		include_once($path."ajaxLogin.php");
	}
    
    $table = "`".PREFIX."menu`";
	
    if (isset($_SESSION['border']) || isset($_SESSION['siteAdmin']))
    {
        $where = "AND (node.displayMenu = 0 OR node.displayMenu = 1 OR node.displayMenu = 2  OR node.displayMenu = 3)";
    }
	else if (isset($_SESSION['testmember']))
    {
        $where = "AND (node.displayMenu = 0 OR node.displayMenu = 3)";
    }
    else if (isset($_SESSION['uid']))
    {
        $where = "AND (node.displayMenu = 0 OR node.displayMenu = 1  OR node.displayMenu = 3)";
    }
	
    else
    {
        $where = "AND (node.displayMenu = 0 OR node.displayMenu = -1)";
    }
    
    $sql = "SELECT node.*, (COUNT(parent.lft) - 1) AS depth
        FROM ".$table." AS node,
                ".$table." AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt ".$where." 
        GROUP BY node.lft
        ORDER BY node.lft;";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $rowData[] = $row;
    }
    
    return $rowData;
}

function getMenuSiteAdminData()
{
    global $link;
    
    $table = "`".PREFIX."menu_siteAdmin`";
    
    $sql = "SELECT node.*, (COUNT(parent.lft) - 1) AS depth
        FROM ".$table." AS node,
                ".$table." AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt
        GROUP BY node.lft
        ORDER BY node.lft;";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $rowData[] = $row;
    }
    
    return $rowData;
}

function getMenu()
{
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
    
    $menu = getMenuData();
    
    echo "<ul>";        
        $oldDepth = 0;

        foreach ($menu as $key => $value)
        {
            if ($oldDepth > (int)$value['depth'])
            {
                for ($i = 0; $i < ($oldDepth - (int)$value['depth']); $i++ )
                {
                    echo "</ul></li>";
                }
            }

            if (((int)$value['lft'] + 1) < (int)$value['rgt'])
            {
                echo "<li class=\"dropdown\"><a href=\"#\"><span>".$value['note']."</span> <i class=\"bi bi-chevron-down\"></i></a>";
                    echo "<ul>";
            }
            else
            {
                echo "<li><a class=\"nav-link";
                    if(strpos($value['file'], "#") !== false)
                    {
                        echo " "."scrollto";
                    }
                    if (basename(__FILE__) == basename($value['file']))
                    {
                        echo " "."active";
                    }
                echo "\" href=\"".$url;
                    if (!empty($value['folder']))
                    {
                        echo trim($value['folder'])."/";
                    }
                echo $value['file']."\">".$value['note']."</a></li>";
            }
            
            $oldDepth = (int)$value['depth'];
        }
    
        for ($i = 0; $i < $oldDepth ; $i++ )
        {
            echo "</ul></li>";
        }
    
        if ((int)$_SESSION['siteAdmin'] > 0)
        {
            $menu = getMenuSiteAdminData();
            
            $oldDepth = 0;

            foreach ($menu as $key => $value)
            {
                if ($oldDepth > (int)$value['depth'])
                {
                    for ($i = 0; $i < $oldDepth - (int)$value['depth']; $i++ )
                    {
                        echo "</ul></li>";
                    }
                }

                if (((int)$value['lft'] + 1) < (int)$value['rgt'])
                {
                    echo "<li class=\"dropdown\"><a href=\"#\"><span>".$value['note']."</span> <i class=\"bi bi-chevron-down\"></i></a>";
                        echo "<ul>";
                }
                else
                {
                    echo "<li><a class=\"nav-link";
                        if(strpos($value['file'], "#") !== false)
                        {
                            echo " "."scrollto";
                        }
                        if (basename(__FILE__) == basename($value['file']))
                        {
                            echo " "."active";
                        }
                    echo "\" href=\"".$url;
                        if (!empty($value['folder']))
                        {
                            echo trim($value['folder'])."/";
                        }
                    echo $value['file']."\">".$value['note']."</a></li>";
                }
                $oldDepth = (int)$value['depth'];
            }
            for ($i = 0; $i < $oldDepth ; $i++ )
            {
                echo "</ul></li>";
            }
        }
    echo "</ul>";
}

function printHeader()
{
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
	
	$langStrings = getLangstrings();
    $printHeader = $langStrings['printHeader'];
    
    echo "<!DOCTYPE html>";
    echo "<html lang=\"sv\">";
	
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
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
      echo "<link href=\"".$url."ext/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">";
      echo "<link href=\"".$url."assets/vendor/bootstrap-icons/bootstrap-icons.css\" rel=\"stylesheet\">";
      echo "<link href=\"".$url."assets/vendor/boxicons/css/boxicons.min.css\" rel=\"stylesheet\">";
      echo "<link href=\"".$url."assets/vendor/glightbox/css/glightbox.min.css\" rel=\"stylesheet\">";
      echo "<link href=\"".$url."assets/vendor/remixicon/remixicon.css\" rel=\"stylesheet\">";
      echo "<link href=\"".$url."assets/vendor/swiper/swiper-bundle.min.css\" rel=\"stylesheet\">";

      echo "<!-- Template Main CSS File -->";
      echo "<link href=\"".$url."assets/css/style.css\" rel=\"stylesheet\">";
    
        echo "<link href=\"".$url."ext/dataTables/datatables.min.css\" rel=\"stylesheet\">";
        echo "<link href=\"".$url."ext/fancytree/dist/skin-win8/ui.fancytree.min.css\" rel=\"stylesheet\">";
        echo "<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css\">";
        echo "<link href=\"".$url."ext/titatoggle/dist/titatoggle-dist-min.css\" rel=\"stylesheet\">";
        echo "<link href=\"".$url."ext/fontawesome/css/all.css\" rel=\"stylesheet\">";

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
          //echo "<h1 class=\"logo me-auto\"><a href=\"".$url."index.php\">Presento<span>.</span></a></h1>";
          echo "<!-- Uncomment below if you prefer to use an image logo -->";
          echo "<a href=\"index.php\" class=\"logo me-auto\"><img src=\"".$url."img/logo.png\" alt=\"\"></a>";

          echo "<nav id=\"navbar\" class=\"navbar order-last order-lg-0\">";
              getMenu();
            echo "<i class=\"bi bi-list mobile-nav-toggle\"></i>";
          echo "</nav><!-- .navbar -->";

        echo "</div>";
      echo "</header><!-- End Header -->";
}

  /*<main id=\"main\">

    <!-- ======= Breadcrumbs ======= -->
    

    <section class=\"inner-page\">
      <div class=\"container\" data-aos=\"fade-up\">
        <p>
          Example inner page template
        </p>
      </div>
    </section>

  </main><!-- End #main -->*/


function printFooter()
{
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
    
      echo "<!-- ======= Footer ======= -->";
      echo "<footer id=\"footer\">";

        echo "<div class=\"container d-md-flex py-4\">";

          echo "<div class=\"me-md-auto text-center text-md-start\">";
            echo "<div class=\"copyright\">";
              echo "&copy; Copyright 2001 - ".date("Y")." <strong><span>Young Friends</span></strong>. Alla rätigheter är reserverade!";
            echo "</div>";
            echo "<div class=\"credits\">";
              echo "<!-- All the links in the footer should remain intact. -->";
              echo "<!-- You can delete the links only if you purchased the pro version. -->";
              echo "<!-- Licensing information: https://bootstrapmade.com/license/ -->";
              echo "<!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/presento-bootstrap-corporate-template/ -->";
              echo "Temma \"Presento\" av <a href=\"https://bootstrapmade.com/\" target = \"_blank\">BootstrapMade</a>";
            echo "</div>";
          echo "</div>";
          
        echo "</div>";
      echo "</footer><!-- End Footer -->";

      echo "<a href=\"#\" class=\"back-to-top d-flex align-items-center justify-content-center\"><i class=\"bi bi-arrow-up-short\"></i></a>";

      echo "<!-- Vendor JS Files -->";
    
        echo "<script src=\"".$url."ext/jquery/jquery.min.js\"></script>";
        echo "<script src=\"".$url."ext/jquery/jquery-ui.min.js\"></script>";
    
      echo "<script src=\"".$url."assets/vendor/aos/aos.js\"></script>";
      echo "<script src=\"".$url."ext/bootstrap/js/bootstrap.bundle.min.js\"></script>";
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
    	echo "<script href=\"".$url."ext/fontawesome/js/all.js\" rel=\"stylesheet\">";

      echo "<!-- Template Main JS File -->";
      echo "<script src=\"".$url."assets/js/main.js\"></script>";
        echo "<script src=\"".$url."index.js\"></script>";

    echo "</body>";

    echo "</html>";
}