<?php 

if (!isset($_SESSION["user-auth"])) {
    header("Location: ?page=home");
}

$user_data = mysqli_fetch_array(
    mysqli_query($db_connection, (
        "SELECT * FROM homeowners WHERE user_id = '".$_SESSION["user-auth"]["id"]."'"
    ))
);

  if(array_key_exists("btn_update",$_POST)){
    $result =  mysqli_query($db_connection,(
        "UPDATE `homeowners` SET
            
                `first_name`   = '".mysqli_real_escape_string($db_connection,$_REQUEST["first_name"])."', 
                `last_name`    = '".mysqli_real_escape_string($db_connection,$_REQUEST["last_name"])."', 
                `phone`        = '".mysqli_real_escape_string($db_connection,$_REQUEST["phone"])."', 
                `house_number` = '".mysqli_real_escape_string($db_connection,$_REQUEST["house_number"])."', 
                `block`        = '".mysqli_real_escape_string($db_connection,$_REQUEST["block"])."', 
                `lot`          = '".mysqli_real_escape_string($db_connection,$_REQUEST["lot"])."', 
                `street_name`  = '".mysqli_real_escape_string($db_connection,$_REQUEST["street_name"])."', 
                `type`         = '".mysqli_real_escape_string($db_connection,$_REQUEST["type"])."', 
                `land_size`    = '".mysqli_real_escape_string($db_connection,$_REQUEST["land_size"])."', 
                `price`        = '".mysqli_real_escape_string($db_connection,$_REQUEST["price"])."'
                 WHERE user_id = '".$user_data["user_id"]."'
               
           "
    ));


    mysqli_query($db_connection, (
        "UPDATE users SET status = 'Pending',  updated_at = '".$current_date. " ". $current_time ."' WHERE id = '".$user_data["user_id"]."'"
    ));

    echo $result ? (  
        '<script>alert("Setup Completed, You are temporarily cannot access the portal until the administrator will activate your account, We will contact you shortly, Thank you."); location.href="?page=home#p_sign_in";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=home#p_sign_up";</script>' 
    );




  }

?>