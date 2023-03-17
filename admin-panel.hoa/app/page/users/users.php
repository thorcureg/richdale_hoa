<?php
if ($_SESSION["admin-auth"]["role"] != "Administrator") {
    header("Location: ?page=dashboard");
}


    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require  $config["MAILER"] .'PHPMailer.php';
    require $config["MAILER"] .'SMTP.php';
    require $config["MAILER"].'Exception.php';

if(isset($_SESSION["users_sid"]) && !empty($_SESSION["users_sid"])){
    $users = mysqli_query($db_connection,(
        "SELECT * FROM users WHERE 
        name LIKE '%".$_SESSION["users_sid"]."%' OR
        email LIKE '%".$_SESSION["users_sid"]."%' OR
        role LIKE '%".$_SESSION["users_sid"]."%' OR
        status LIKE '%".$_SESSION["users_sid"]."%'
        ORDER BY id DESC"
    ));
}else{
    $users = mysqli_query($db_connection,"SELECT * FROM users ORDER BY id DESC");
}

if(array_key_exists("btn_search",$_POST)){
    $_SESSION["users_sid"] = $_REQUEST["id"];
    header("Location: ?page=users");

}

if(array_key_exists("btn_view",$_POST)){
    $user_data = mysqli_query($db_connection,"SELECT * FROM users WHERE id = '".$_REQUEST["id"]."'");
    $_SESSION["users_vid"] = mysqli_fetch_array($user_data);
    if($_SESSION["users_vid"]["status"] == "Pending" || $_SESSION["users_vid"]["status"] == "Active" || $_SESSION["users_vid"]["status"] == "Inactive" && $_SESSION["users_vid"]["role"] == "Standard"  ){
        $_SESSION["users_hvid"] = mysqli_fetch_array(mysqli_query($db_connection,(
            "SELECT * FROM  homeowners WHERE user_id = '".$_SESSION["users_vid"]["id"]."'"
        )));
    }else{
        if(isset($_SESSION["users_hvid"])){
            unset($_SESSION["users_hvid"]);
        }
    }
    header("Location: ?page=users#p_option2");

}

if(array_key_exists("btn_update",$_POST)){

        $result1 = mysqli_query($db_connection,"UPDATE users SET name = '".mysqli_real_escape_string($db_connection,$_REQUEST["name"])."' , updated_at = '".$current_date. " ". $current_time ."' WHERE id = '".$_SESSION["users_vid"]["id"]."' ");
    
        echo $result1 ? (  
            '<script>alert("Updated!"); location.href="?page=users";</script>' 
        ) : ( 
            '<script>alert("Error!"); location.href="?page=users";</script>' 
        );
}

if(array_key_exists("btn_change_password",$_POST)){

    $result1 = mysqli_query($db_connection,"UPDATE users SET password = '".mysqli_real_escape_string($db_connection,md5($_REQUEST["password"]))."' , updated_at = '".$current_date. " ". $current_time ."' WHERE id = '".$_SESSION["users_vid"]["id"]."' ");

    echo $result1 ? (  
        '<script>alert("Updated!"); location.href="?page=users";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=users";</script>' 
    );
}

if(array_key_exists("btn_inactive",$_POST)){

    $result1 = mysqli_query($db_connection,"UPDATE users SET status = 'Inactive' , updated_at = '".$current_date. " ". $current_time ."' WHERE id = '".$_SESSION["users_vid"]["id"]."' ");

    echo $result1 ? (  
        '<script>alert("Updated!"); location.href="?page=users";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=users";</script>' 
    );
}


if(array_key_exists("btn_active",$_POST)){

    $result1 = mysqli_query($db_connection,"UPDATE users SET status = 'Active' , updated_at = '".$current_date. " ". $current_time ."' WHERE id = '".$_SESSION["users_vid"]["id"]."' ");

    if($_SESSION["users_vid"]["role"] == "Standard"){
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->SMTPDebug  = 0;  
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = $config["MAILER_USERNAME"];
        $mail->Password   = $config["MAILER_PASSWORD"];
        $mail->IsHTML(true);
        $mail->AddAddress($_SESSION["users_vid"]["email"], $_SESSION["users_vid"]["name"]);
        $mail->SetFrom($config["MAILER_USERNAME"], "Homeowners Association System");
        $mail->Subject = "Account Activation";
        $content = "
            <h1>Dear ".$_SESSION["users_vid"]["name"]."</h1>
            <p><b>Your account has been activated.Thank you for signing up to Richdale Homeowners Online. <br><br>
            Kindly take note of the folling remainders below:<br>
            1. Reservation <br>
            The payment for reservation must be settled on or before the date of reservation.<br>
            If there are property damage done by the people who made the reservation, it shall be added to their penalties.<br>
            Richdale subdivision will not be responsible for any loss or damage of personal items during the period of reservation.<br> 
            The subdivision will not be responsible of personal injuries that may occur<br><br>

            2.Right to Terminate Access<br>
            HOA Member Services reserves the right to monitor use of this Site to determine compliance with these Terms of Use,<br>
            as well as the right to edit, refuse to post or remove any information or materials, in whole or in part, at its sole discretion.<br>
            HOA Member Services reserves the right to terminate your access to any or all of the<br>
            Communication Services at any time without notice for any reason whatsoever.<br><br>

            This TERMS OF USE AGREEMENT (hereinafter “Agreement”) is made by and between HOA Member Services for Richdale Subdivision, <br>
            and you, the user of this Website and the information and resources made available on it (hereinafter “You, Your, User, or Member). <br>
            This Agreement contains the complete terms and conditions that govern the use of the HOA Member Services Website (“Website” or “Site”).<br><br>

            This is a system-generated e-mail. Please do not reply.</b></p>
        ";
        $mail->MsgHTML($content); 
        if(!$mail->Send()) {
          
            echo $result1 ? (  
                '<script>alert("Email not sent!"); location.href="?page=users";</script>' 
            ) : ( 
                '<script>alert("Error!"); location.href="?page=users";</script>' 
            );
        } else {
            echo $result1 ? (  
                '<script>alert("Updated!"); location.href="?page=users";</script>' 
            ) : ( 
                '<script>alert("Error!"); location.href="?page=users";</script>' 
            );
        }

        
    }else{
        echo $result1 ? (  
            '<script>alert("Updated!"); location.href="?page=users";</script>' 
        ) : ( 
            '<script>alert("Error!"); location.href="?page=users";</script>' 
        );
    }

  
    
}



if(array_key_exists("btn_add",$_POST)){

    $status = ( $_REQUEST["role"] == "Standard" ? "Not Setup" : "Active");

    if(mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM users WHERE email = '".$_REQUEST["email"]."'")) == 0){
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
                '".mysqli_real_escape_string($db_connection,$_REQUEST["role"])."',
                '".$status."',
                '".$current_date ." ". $current_time."',
                '".$current_date ." ". $current_time."'
                
    
            )"
        ));
    
       echo $result ? (  
                '<script>alert("Added!"); location.href="?page=users";</script>' 
            ) : ( 
                '<script>alert("Error!"); location.href="?page=users";</script>' 
            );
    }else{
        echo '<script>alert("Email is taken!"); location.href="?page=users";</script>';
    }
   

    
}

?>