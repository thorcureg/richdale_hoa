<?php 

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require  $config["MAILER"] .'PHPMailer.php';
    require $config["MAILER"] .'SMTP.php';
    require $config["MAILER"].'Exception.php';

$month = isset($_SESSION["cal_res_month"]) && !empty($_SESSION["cal_res_month"]) ? $_SESSION["cal_res_month"] : date('m',strtotime($current_date));
$year  = isset($_SESSION["cal_res_year"])  && !empty($_SESSION["cal_res_year"])  ? $_SESSION["cal_res_year"]  : date('Y',strtotime($current_date));
$days = cal_days_in_month(CAL_GREGORIAN,$month,$year);



if(array_key_exists("btn_calender_view",$_POST)){
    $_SESSION["cal_res_month"] = date('m',strtotime($_REQUEST["date"]));
    $_SESSION["cal_res_year"]  = date('Y',strtotime($_REQUEST["date"]));

    header("Location: ?page=reservations");
}


if(array_key_exists("btn_view_days",$_POST)){
    $_SESSION["res_vid"] = $_REQUEST["id"];
    header("Location: ?page=reservations#p_view_day");
}


if(isset($_SESSION["res_vid"] ) && !empty($_SESSION["res_vid"] )){

    $ress = mysqli_query($db_connection,"SELECT   reservations.*, users.name, users.email FROM reservations LEFT JOIN users on reservations.user_id = users.id WHERE reservations.date = '".$_SESSION["res_vid"]."' ORDER BY reservations.id DESC");
}

// if(mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM reservations WHERE status = 'Pending' ")) != 0 ){
//     $res_flag_pen = 1;
// }else{
//     $res_flag_pen = 0;
// }


if(array_key_exists("btn_reserve",$_POST)){

    if($_REQUEST["type"] == "Swimming Pool (Day Swimming)"){
        $payment = 5000;
    } else if($_REQUEST["type"] == "Swimming Pool (Night Swimming)"){
        $payment = 6000;
    }else{
        $payment = 0;
    }

    $result1 = mysqli_query($db_connection,(
        "INSERT INTO `reservations`( 
            `user_id`, 
            `type`, 
            `date`, 
            `time`, 
            `payment`,
            `note`, 
            `feedback`, 
            `status`, 
            `created_at`, 
            `updated_at`
            ) 
             VALUES
            (
            '".$_SESSION["user-auth"]["id"]."',
            '".mysqli_real_escape_string($db_connection,$_REQUEST["type"])."',
            '".$_SESSION["res_vid"]."',
            '".mysqli_real_escape_string($db_connection,$_REQUEST["time"])."',
            '".$payment."',
            '".mysqli_real_escape_string($db_connection,$_REQUEST["note"])."',
            '',
            'Pending',
            '".$current_date . " " . $current_time."',
            '".$current_date . " " . $current_time."'
            )"
    ));

    echo $result1 ? (  
        '<script>alert("Your reservation request has been sent, we will contact you shortly, thank you."); location.href="?page=reservations";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=reservations";</script>' 
    );

}

if(array_key_exists("btn_cancel_res",$_POST)){

    $result1 = mysqli_query($db_connection,(
        "UPDATE reservations SET status = 'Cancelled' , updated_at = '".$current_date . " " . $current_time."' WHERE id = '".$res_data["id"]."' "
    ));

    echo $result1 ? (  
        '<script>alert("Cancelled!"); location.href="?page=reservations";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=reservations";</script>' 
    );

}

if(array_key_exists("btn_update_res",$_POST)){

    $result1 = mysqli_query($db_connection,(
        "UPDATE reservations SET feedback ='".mysqli_real_escape_string($db_connection,$_REQUEST["feedback"])."' , status = '".mysqli_real_escape_string($db_connection,$_REQUEST["status"])."' , updated_at = '".$current_date . " " . $current_time."' WHERE id = '".$_REQUEST["id"]."' "
    ));

    $get_user = mysqli_fetch_array(
        (mysqli_query($db_connection,"SELECT * FROM users WHERE id = '".$_REQUEST["uid"]."'"))
    );
    $get_reservations = mysqli_fetch_array(
        (mysqli_query($db_connection,"SELECT * FROM reservations WHERE id = '".$_REQUEST["id"]."'"))
    );

    if($_REQUEST["status"] == "Completed"){
        
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
    $mail->AddAddress($get_user["email"], $get_user["name"]);
    $mail->SetFrom($config["MAILER_USERNAME"], "Homeowners Association System");
    $mail->Subject = "Reservation Request Approval";
    $content = "
        <h1>Hi ".$get_user["name"]."!</h1>
        <p>We're happy to inform you that your reservation request has been approved.</p>
        <table style='border-collapse: separate; border-spacing: 0; width: 100%;'>
            <tr>
                <th colspan='2' style='background-color: #3D3BDD; color:#FFF; padding: 10px;'>Reservation Details</th>
            </tr>
            <tr style='background-color:#F9F9F9'>
                <td style='padding: 8px; font-weight: bold;'>Payment Confirmation:</td>
                <td style='padding: 8px;'>".$get_reservations["payment"]."</td>
            </tr>
            <tr>
                <td style='padding: 8px; font-weight: bold;'>Reservation Type:</td>
                <td style='padding: 8px;'>".$get_reservations["type"]."</td>
            </tr>
            <tr style='background-color:#F9F9F9'>
                <td style='padding: 8px; font-weight: bold;'>Schedule:</td>
                <td style='padding: 8px;'>".date('F d, Y h:i a', strtotime($get_reservations["date"] . ' ' . $get_reservations["time"]))."</td>
            </tr>
            <tr>
                <td style='padding: 8px; font-weight: bold;'>Reservation ID:</td>
                <td style='padding: 8px;'>RSRV-".$get_reservations["id"]."</td>
            </tr>
        </table>
        <p>You can use this email as a Proof of transaction and/or Receipt if needed.</p>
        <br>
        <p>Thank you for using Richdale Homeowners Online for making a reservation.</p>
        <p>This is a system-generated e-mail. Please do not reply.</p>
    ";

    
    


    
    $mail->MsgHTML($content); 
    if(!$mail->Send()) {
      
        echo $result1 ? (  
            '<script>alert("Email not sent!"); location.href="?page=reservations";</script>' 
        ) : ( 
            '<script>alert("Error!"); location.href="?page=reservations";</script>' 
        );
    } else {
        echo $result1 ? (  
            '<script>alert("Updated!"); location.href="?page=reservations";</script>' 
        ) : ( 
            '<script>alert("Error!"); location.href="?page=reservations";</script>' 
        );
    }


    }else{
        echo $result1 ? (  
            '<script>alert("Updated!"); location.href="?page=reservations";</script>' 
        ) : ( 
            '<script>alert("Error!"); location.href="?page=reservations";</script>' 
        );
    
    }

   

 

}
