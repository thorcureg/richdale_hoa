<?php 

if(isset($_SESSION["payments_sid"]) && !empty($_SESSION["payments_sid"])){
    $payments = mysqli_query($db_connection,(
        "SELECT * FROM payments WHERE 
        type LIKE '%".$_SESSION["payments_sid"]."%' OR
        note LIKE '%".$_SESSION["payments_sid"]."%' OR
        status LIKE '%".$_SESSION["payments_sid"]."%' OR
        created_at LIKE '%".$_SESSION["payments_sid"]."%' OR
        updated_at LIKE '%".$_SESSION["payments_sid"]."%' 
        WHERE 
        user_id = '".$_SESSION["user-auth"]["id"]."'
        ORDER BY id DESC"
    ));
}else{
    $payments = mysqli_query($db_connection,"SELECT * FROM payments WHERE user_id = '".$_SESSION["user-auth"]["id"]."' ORDER BY id DESC");
}

if(array_key_exists("btn_search",$_POST)){
    $_SESSION["payments_sid"] = $_REQUEST["id"];
    header("Location: ?page=payments");

}

if(array_key_exists("btn_cancel",$_POST)){

    $result = mysqli_query($db_connection, (
        "UPDATE payments set status = 'Cancelled' , updated_at = '".$current_date. " " . $current_time."' WHERE id = '".$_REQUEST["id"]."'"    
     )
    );
    
    echo $result ? (  
        '<script>alert("Cancelled!"); location.href="?page=payments";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=payments";</script>' 
    );

}

if(array_key_exists("btn_view",$_POST)){
 
   
        $_SESSION["payments_hvid2"] = mysqli_fetch_array(mysqli_query($db_connection,(
            "SELECT * FROM  payments WHERE id = '".$_REQUEST["id"]."'"
        )));
    
    header("Location: ?page=payments#p_option2");

}

if(isset($_FILES['image'])){
    $temp = explode(".", $_FILES["image"]["name"]);
    $newfilename = round(microtime(true)) . '.' . end($temp);
    $file_destination =  $config["FS"]. 'static.hoa'. DS .'uploads'. DS .'payments' . DS . $newfilename;
    move_uploaded_file($_FILES["image"]["tmp_name"], $file_destination);
 
        $result = mysqli_query($db_connection,(
            "INSERT INTO `payments`
            (
               
                `user_id`, 
                `type`, 
                `amount`,
                `note`,  
                `file_name`,  
                `status`, 
                `created_at`, 
                `updated_at`
            ) 
            VALUES 
            (   
                '".$_SESSION["user-auth"]["id"]."',
                '".mysqli_real_escape_string($db_connection,$_REQUEST["type"])."',
                '".mysqli_real_escape_string($db_connection,$_REQUEST["amount"])."',
                '".mysqli_real_escape_string($db_connection,$_REQUEST["note"])."',
                '".$newfilename."',
                'Pending',
                '".$current_date ." ". $current_time."',
                '".$current_date ." ". $current_time."'
                
    
            )"
        ));
    
       echo $result ? (  
                '<script>alert("Added!"); location.href="?page=payments";</script>' 
            ) : ( 
                '<script>alert("Error!"); location.href="?page=payments";</script>' 
            );
   
   

    
}





?>