<?php 

if(isset($_SESSION["admin-auth"])){
    unset($_SESSION["admin-auth"]);
}

header("Location: ?page=login");

?>