<?php
//offline
$c= mysqli_connect ("localhost" , "root" , "" , "medled") ;

/*online
    $c= mysqli_connect ("sql210.infinityfree.com" , "if0_37479146" , "IFKytCrgHV7CK" , "if0_37479146_medled") ;
    //connection values from : controle panel (CPanel) > Mysql Databases
                                home page > MYSQL Databases
*/

//mysqli_set_charset($c, "utf8mb4");
if(!$c){
    exit(mysqli_connect_error());
}

?>