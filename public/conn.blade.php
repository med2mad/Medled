<?php

$c= mysqli_connect ("localhost" , "root" , "" , "medled") ;
/*online
    $c= mysqli_connect ("sql203.epizy.com" , "epiz_32587616" , "t93O5tXyNvVG" , "epiz_32587616_test") ;
    //connection values from : controle panel (CPanel) > Mysql Databases
*/
//mysqli_set_charset($c, "utf8mb4");
if(!$c){
    exit(mysqli_connect_error());
}

?>