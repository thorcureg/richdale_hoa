<?php 
  $house_info = mysqli_fetch_array(mysqli_query($db_connection,(
    "SELECT * FROM  homeowners WHERE user_id = '".$_SESSION["user-auth"]["id"]."'"
)));

?>