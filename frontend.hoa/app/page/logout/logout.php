<?php 

if(isset($_SESSION["user-auth"])){
    unset($_SESSION["user-auth"]);
}

header("Location: ?page=home#p_sign_in");

?>