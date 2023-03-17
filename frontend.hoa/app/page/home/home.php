<?php 

if(array_key_exists("btn_login",$_POST)){
    
        $result = mysqli_query($db_connection, (
            "SELECT * FROM users WHERE email = '".mysqli_real_escape_string($db_connection,$_REQUEST["email"])."' AND password  = '".mysqli_real_escape_string($db_connection,md5($_REQUEST["password"]))."' "
        ));
      
        if(mysqli_num_rows($result) == 1){

            $user_data = mysqli_fetch_array($result);

            if($user_data["role"] == 'Standard' ){

                switch( $user_data["status"]){
                    case 'Active':
                        $_SESSION["user-auth"] = $user_data;
                        header("Location: ?page=dashboard");
                        break;
                    case 'Not Setup':
                        $_SESSION["user-auth"] = $user_data;
                        header("Location: ?page=setup");
                        break;
                    case 'Inactive':
                        echo '<script>alert("Account is inactive, Please contact administrator.");</script>';
                        break;
                     case 'Pending':
                        echo '<script>alert("You are temporarily cannot access the portal until the administrator will activate your account.");</script>';
                        break;
    
    
                }
            }else{
                echo '<script>alert("Wrong Credential!"); location.href="?page=home#p_sign_in";</script>';
            }
           
           
        }else{
            echo '<script>alert("Wrong Credential!"); location.href="?page=home#p_sign_in";</script>';
        }
}

if(array_key_exists("btn_sign_up",$_POST)){

    if(mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM users WHERE email = '".$_REQUEST["email"]."'")) == 0){

        $get_user_id = mysqli_fetch_array( mysqli_query($db_connection,(
            "SELECT `auto_increment` 
             FROM INFORMATION_SCHEMA.TABLES
             WHERE table_name = 'users'
             AND table_schema = '".$DATABASE."' "
        )));

        $result = mysqli_query($db_connection,(
            "INSERT INTO `users`
            (
                `name`, 
                `email`, 
                `password`, 
                `role`, 
                `status`, 
                `created_at`, 
                `updated_at`
            ) 
            VALUES 
            (
                '".mysqli_real_escape_string($db_connection,$_REQUEST["name"])."',
                '".mysqli_real_escape_string($db_connection,$_REQUEST["email"])."',
                '".mysqli_real_escape_string($db_connection,md5($_REQUEST["password"]))."',
                'Standard',
                'Not Setup',
                '".$current_date ." ". $current_time."',
                '".$current_date ." ". $current_time."'
                
    
            )"
        ));

        mysqli_query($db_connection,(
            "INSERT INTO `homeowners`
                ( 
                    `user_id`, 
                    `first_name`, 
                    `last_name`, 
                    `phone`, 
                    `house_number`, 
                    `block`, 
                    `lot`, 
                    `street_name`, 
                    `type`, 
                    `land_size`, 
                    `price`
                )
                 VALUES 
                (
                    '". $get_user_id[0] ."',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )"
        ));

        echo $result ? (  
            '<script>alert("Signed Up!"); location.href="?page=home#p_sign_in";</script>' 
        ) : ( 
            '<script>alert("Error!"); location.href="?page=home#p_sign_up";</script>' 
        );

    }else{
        echo '<script>alert("Email is taken!"); location.href="?page=home#p_sign_up";</script>';
    }
   

    
}

  

?>