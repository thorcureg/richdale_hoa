<?php 


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

    $option1 = mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM reservations WHERE type = 'Tennis Court' AND date = '".$_SESSION["res_vid"]."' AND status = 'Pending'"));
    $option2 = mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM reservations WHERE type = 'Basketball Court' AND date = '".$_SESSION["res_vid"]."' AND status = 'Pending'"));
    $option3 = mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM reservations WHERE type = 'Swimming Pool (Day Swimming)' AND date = '".$_SESSION["res_vid"]."' AND status = 'Pending'"));  
    $option4 = mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM reservations WHERE type = 'Swimming Pool (Night Swimming)' AND date = '".$_SESSION["res_vid"]."' AND status = 'Pending'"));  
    if($option3 == 0){
        $option3 = mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM reservations WHERE type = 'Swimming Pool (Day Swimming)' AND date = '".$_SESSION["res_vid"]."' AND status = 'Completed'"));
    }
    if($option4 == 0){
        $option4 = mysqli_num_rows(mysqli_query($db_connection,"SELECT * FROM reservations WHERE type = 'Swimming Pool (Night Swimming)' AND date = '".$_SESSION["res_vid"]."' AND status = 'Completed'"));  
    
    }

   

}


if(array_key_exists("btn_reserve",$_POST)){

    if($_REQUEST["type"] == "Swimming Pool (Day Swimming)"){
        $payment = 5000;
    } else if($_REQUEST["type"] == "Swimming Pool (Night Swimming)"){
        $payment = 6000;
    }else{
        $payment = 0;
    }

    $result1 = mysqli_query($db_connection,(
        "INSERT INTO `reservations` (
            `user_id`,
            `type`,
            `date`,
            `time`,
            `payment`,
            `note`,
            `feedback`,
            `status`,
            `start_time`,
            `end_time`,
            `created_at`,
            `updated_at`
        ) VALUES (
            '".$_SESSION["user-auth"]["id"]."',
            '".mysqli_real_escape_string($db_connection,$_REQUEST["type"])."',
            '".mysqli_real_escape_string($db_connection,$_SESSION["res_vid"])."',
            '".date("H:i", strtotime($_REQUEST["time"]))." ',
            '".$payment."',
            '".mysqli_real_escape_string($db_connection,$_POST["note"])."',
            '',
            'Pending',
            '".mysqli_real_escape_string($db_connection,date("h:i ", strtotime($_REQUEST["start_time"])))."',
            '".mysqli_real_escape_string($db_connection,date("h:i a", strtotime($_REQUEST["end_time"])))."',
            '".$current_date . " " . $current_time."',
            '".$current_date . " " . $current_time."'
        )"
        )
    );

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




?>