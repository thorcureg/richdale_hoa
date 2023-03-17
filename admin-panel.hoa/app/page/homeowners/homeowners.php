<?php 

if(isset($_SESSION["ho_sid"]) && !empty($_SESSION["ho_sid"])){
    $users = mysqli_query($db_connection,(
        "SELECT * FROM users WHERE 
        name = '".$_SESSION["ho_sid"]."' 
        OR email = '".$_SESSION["ho_sid"]."'
        AND role = 'Standard'
        AND status = 'Active'
        ORDER BY id DESC"
    ));
}else{
    $users = mysqli_query($db_connection,"SELECT * FROM users WHERE role = 'Standard' AND status = 'Active'  ORDER BY id DESC");
}


if(array_key_exists("btn_search",$_POST)){
    $_SESSION["ho_sid"] = $_REQUEST["id"];
    header("Location: ?page=homeowners");

}

if(array_key_exists("btn_view",$_POST)){
    $user_data = mysqli_query($db_connection,"SELECT * FROM users WHERE id = '".$_REQUEST["id"]."'");
    $_SESSION["ho_vid"] = mysqli_fetch_array($user_data);
    if($_SESSION["ho_vid"]["status"] == "Active" && $_SESSION["ho_vid"]["role"] == "Standard"  ){
        $_SESSION["ho_hvid"] = mysqli_fetch_array(mysqli_query($db_connection,(
            "SELECT * FROM  homeowners WHERE user_id = '".$_SESSION["ho_vid"]["id"]."'"
        )));
     
    }else{
        if(isset($_SESSION["ho_hvid"])){
            unset($_SESSION["ho_hvid"]);
        }
    }
    header("Location: ?page=homeowners#p_option2");

}


if(isset($_SESSION["ho_vid"]) && !empty($_SESSION["ho_vid"])){
    $payments = mysqli_query($db_connection,(
        "SELECT * FROM  payments WHERE user_id = '".$_SESSION["ho_vid"]["id"]."' ORDER BY id DESC"
    ));
    $payment_req = mysqli_query($db_connection,(
        "SELECT * FROM  payment_updates WHERE user_id = '".$_SESSION["ho_vid"]["id"]."' ORDER BY id DESC"
    ));

    #homeowner dashboard
    $payments_overall = mysqli_query($db_connection, "SELECT * FROM payments WHERE user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $payments_pending = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Pending' AND user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $payments_cancelled = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Cancelled' AND user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $payments_paid = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Paid' AND user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $res_overall = mysqli_query($db_connection, "SELECT * FROM reservations");
    $res_pending = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Pending' AND user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $res_cancelled = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Cancelled' AND user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $res_completed = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Completed' AND user_id = '".$_SESSION["ho_vid"]["id"]."'");
    
    $res_pending = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Pending' AND user_id = '".$_SESSION["ho_vid"]["id"]."'");
    
    $total_spent = 0;
    $total_balance = 0;
    
    
    while($row = mysqli_fetch_array($payments_paid)){
        $total_spent += $row["amount"];  
    }
    
    while($row = mysqli_fetch_array($res_pending)){
        $total_balance += $row["payment"];  
    }
    
    while($row = mysqli_fetch_array($res_completed)){
        $total_balance += $row["payment"];  
    }
    
    $req_pay = mysqli_query($db_connection, "SELECT * FROM payment_updates WHERE status = 'Pending' AND  user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $req_pay_count = mysqli_query($db_connection, "SELECT * FROM payment_updates WHERE status = 'Pending' AND  user_id = '".$_SESSION["ho_vid"]["id"]."'");
    $req_pay_paid_count = mysqli_query($db_connection, "SELECT * FROM payment_updates WHERE status = 'Paid' AND  user_id = '".$_SESSION["ho_vid"]["id"]."'");
    
    while($row = mysqli_fetch_array($req_pay_count)){
        $total_balance += $row["amount"];  
    }
    
    while($row = mysqli_fetch_array($req_pay_paid_count)){
        $total_balance += $row["amount"];  
    }
    
    $hoa_pending_pay = mysqli_fetch_array( mysqli_query($db_connection, "SELECT * FROM homeowners WHERE  user_id = '".$_SESSION["ho_vid"]["id"]."'"));
    
    
    
    
    $res_pending_pay = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Pending' AND user_id = '".$_SESSION["ho_vid"]["id"]."' ORDER BY id DESC");
    $res_pay = mysqli_query($db_connection, "SELECT * FROM payments WHERE user_id = '".$_SESSION["ho_vid"]["id"]."' ORDER BY id DESC");
    
    $total_balance = $total_balance + ($hoa_pending_pay["price"] * $hoa_pending_pay["land_size"]);
    
    $total_balance = $total_balance - $total_spent;

    #dashboard end


}

if(array_key_exists("btn_paid",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payments set status = 'Paid' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Paid!"); location.href="?page=homeowners";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners";</script>' 
    );

}

if(array_key_exists("btn_paid_req",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payment_updates set status = 'Paid' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Paid!"); location.href="?page=homeowners";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners";</script>' 
    );

}

if(array_key_exists("btn_feedback",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payments set feedback = '".mysqli_real_escape_string($db_connection,$_REQUEST["feedback"])."' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Sent!"); location.href="?page=homeowners#p_payments";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners#p_payments";</script>' 
    );

}

if(array_key_exists("btn_cancel",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payments set status = 'Cancelled' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Cancelled!"); location.href="?page=homeowners";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners";</script>' 
    );

}

if(array_key_exists("btn_cancel_req",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payment_updates set status = 'Cancelled' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Cancelled!"); location.href="?page=homeowners";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners";</script>' 
    );

}


if(array_key_exists("btn_pending",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payments set status = 'Pending' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Updated!"); location.href="?page=homeowners";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners";</script>' 
    );

}

if(array_key_exists("btn_pending_req",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payment_updates set status = 'Pending' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Updated!"); location.href="?page=homeowners";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners";</script>' 
    );

}

if(array_key_exists("btn_view_payment",$_POST)){
 
   
    $_SESSION["payments_hvid"] = mysqli_fetch_array(mysqli_query($db_connection,(
        "SELECT * FROM  payments WHERE id = '".$_REQUEST["id"]."'"
    )));

header("Location: ?page=homeowners#p_option3");

}


if(array_key_exists("btn_view_payment_req",$_POST)){
 
   
    $_SESSION["payments_hvid"] = mysqli_fetch_array(mysqli_query($db_connection,(
        "SELECT * FROM  payment_updates WHERE id = '".$_REQUEST["id"]."'"
    )));

header("Location: ?page=homeowners#p_rec_list_op1");

}

if(array_key_exists("btn_view_payment_notif",$_POST)){
 
   
    $_SESSION["payments_hvid"] = mysqli_fetch_array(mysqli_query($db_connection,(
        "SELECT * FROM  payments WHERE id = '".$_REQUEST["id"]."'"
    )));

    $user_data = mysqli_query($db_connection,"SELECT * FROM users WHERE id = '".$_REQUEST["u_id"]."'");
    $_SESSION["ho_vid"] = mysqli_fetch_array($user_data);
    if($_SESSION["ho_vid"]["status"] == "Active" && $_SESSION["ho_vid"]["role"] == "Standard"  ){
        $_SESSION["ho_hvid"] = mysqli_fetch_array(mysqli_query($db_connection,(
            "SELECT * FROM  homeowners WHERE user_id = '".$_SESSION["ho_vid"]["id"]."'"
        )));
     
    }else{
        if(isset($_SESSION["ho_hvid"])){
            unset($_SESSION["ho_hvid"]);
        }
    }
    header("Location: ?page=homeowners#p_option3");

}

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
                 WHERE user_id = '".$_SESSION["ho_hvid"]["user_id"]."'
               
           "
    ));


    mysqli_query($db_connection, (
        "UPDATE users SET updated_at = '".$current_date. " ". $current_time ."' WHERE id = '".$_SESSION["ho_hvid"]["user_id"]."'"
    ));

    echo $result ? (  
        '<script>alert("Updated"); location.href="?page=homeowners#p_details";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners#p_details";</script>' 
    );




  }


if(array_key_exists("payment_req",$_POST)){

    $result =  mysqli_query($db_connection,(
        "
        INSERT INTO `payment_updates`( 
            `user_id`, 
            `title`, 
            `amount`, 
            `description`, 
            `status`, 
            `created_at`, 
            `updated_at`
            ) 
             VALUES 
            (
               
                '".$_SESSION["ho_hvid"]["user_id"]."',
                '".mysqli_real_escape_string($db_connection,$_REQUEST["title"])."',
                '".mysqli_real_escape_string($db_connection,$_REQUEST["amount"])."',
                '".mysqli_real_escape_string($db_connection,$_REQUEST["description"])."',
                'Pending',
                '".$current_date . " ". $current_time."',
                '".$current_date . " ". $current_time."'
            )       
        
        "
    ));


   

    echo $result ? (  
        '<script>alert("Added"); location.href="?page=homeowners";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=homeowners";</script>' 
    );
    

}



?>