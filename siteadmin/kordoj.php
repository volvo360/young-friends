<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";

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
include_once($path."common/theme.php");

function printVarTree()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printVarTree = $langStrings['printVarTree'];
    
    //updateTranslationTable();
    
    $table = "`".PREFIX."lang_var`";
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT node.* FROM ".$table." AS node ORDER BY node.variable;";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" >";
            echo "<div id = \"tree_".$replaceTable[PREFIX."lang_var"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."lang_var"]."\" data-replace_table = \"".$replaceTable[PREFIX.'lang_var']."\" style = \"max-height : 27vh; overflow : auto;\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."lang_var"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

                        if ($oldDepth > (int)(int)$row['depth'])
                        {
                            for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }

                        if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                        {
                            echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['variable']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['variable']."</li>";
                        }

                        $oldDepth = (int)$row['depth'];
                    }

                    if ($oldDepth > 0)
                    {
                        for ($i = 0; $i < ($oldDepth); $i++)
                        {
                            echo "</ul></li>";
                        }
                    }
                echo "</ul>";
            echo "</div><br>";

            echo "<form id = \"addForm_".$replaceTable[PREFIX."lang_var"]."\">";
                echo $printVarTree[1]."<br>";
                echo "<input type = \"text\" id = \"variable\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."lang_var"]."\" data-replace_table = \"".$replaceTable[PREFIX."lang_var"]."\">".$printVarTree[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 50vh; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."lang_var"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

    echo "</div>";
}

function printAllVar()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printAllVar = $langStrings['printAllVar'];
    
    $table = "`".PREFIX."lang`";
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT node.* FROM ".$table." AS node ORDER BY node.variable, node.arrayKey;";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    if (mysqli_num_rows($result) > 0)
    {
        echo "<table id = \"tablePrintAllVar\" class=\"DataTable table table-striped\" style=\"width:100%\">";    
        
        echo "<thead>";
            echo "<tr>";
                echo "<th>";
                    echo $printAllVar[1];
                echo "</th>";
        
                echo "<th>";
                    echo $printAllVar[2];
                echo "</th>";
        
                echo "<th>";
                    echo $printAllVar[3];
                echo "</th>";
            echo "</tr>";
        echo "</thead>";
        
        echo "<tbody>";
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<tr>";
                echo "<td>";
                    echo $row['variable'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['arrayKey'];
                echo "</td>";
            
                echo "<td class = \"jeditable\" id = \"note[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX.'lang']."\">";
                    echo $row['note'];
                echo "</td>";
            echo "</tr>";
        } 
        echo "<tbody>";
        
        echo "<tfoot>";
            echo "<tr>";
                echo "<th>";
                    echo $printAllVar[1];
                echo "</th>";
        
                echo "<th>";
                    echo $printAllVar[2];
                echo "</th>";
        
                echo "<th>";
                    echo $printAllVar[3];
                echo "</th>";
            echo "</tr>";
        echo "</tfoot>";
        
        echo "</table>";
    }
}

function printVar()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printVar = $langStrings['printVar'];
    
    $navs['printVarTree'] = $printVar[1];
    $navs['printAllVar'] = $printVar[2];
    
    $preselect = 'printVarTree';
    
    echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
        foreach ($navs as $key => $value)
        {
            echo "<li class=\"nav-item\" role=\"presentation\">";
                echo "<button class=\"nav-link";
                    if ($key == $preselect)
                    {
                        echo " "."active";
                    }
                echo "\" id=\"".$key."-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#".$key."\" type=\"button\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                    if ($key == $preselect)
                    {
                        echo "true";
                    }
                    else
                    {
                        echo "false";
                    }
                echo"\">".$value."</button>";
            echo "</li>";
        }
    echo "</ul>";
    
    echo "<div class=\"tab-content\" id=\"myTabContent\">";
        foreach ($navs as $key => $value)
        {
            echo "<div class=\"tab-pane fade";
                if ($key == $preselect)
                {
                    echo " "."show active";
                }
            echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\" style = \"max-height : 52vh; height : 52vh; overflow:auto;\">";
                echo "<br>";
                call_user_func($key);
            echo "</div>";
        }
        
    echo "</div>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $kordoj = $langStrings['kordoj'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$kordoj[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 60vh; height : 60vh; overflow:auto;\">";
                echo "<p>";
                    printVar();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>