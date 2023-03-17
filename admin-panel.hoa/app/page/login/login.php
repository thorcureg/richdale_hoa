<?php 

if(array_key_exists("btn_login",$_POST)){
        $result = mysqli_query($db_connection, (
            "SELECT * FROM users WHERE email = '".mysqli_real_escape_string($db_connection,$_REQUEST["email"])."' AND password  = '".mysqli_real_escape_string($db_connection,md5($_REQUEST["password"]))."' "
        ));
      
        if(mysqli_num_rows($result) == 1){

            $user_data = mysqli_fetch_array($result);

            if($user_data["role"] == 'Administrator' ||  $user_data["role"] == 'Officer'){

                switch( $user_data["status"]){
                    case 'Active':
                        $_SESSION["admin-auth"] = $user_data;
                        header("Location: ?page=dashboard");
                        break;
                    case 'Not Setup':
    
                        break;
                    case 'Inactive':
                        echo '<script>alert("Account is inactive, Please contact administrator.");</script>';
                        break;
    
    
                }
            }else{
                echo '<script>alert("Wrong Credential!");</script>';
            }
           
           
        }else{
            echo '<script>alert("Wrong Credential!");</script>';
        }
}




?>