<?php
if ($_SESSION["admin-auth"]["role"] != "Administrator") {
    header("Location: ?page=dashboard");
}



$news = mysqli_query($db_connection,"SELECT * FROM news ORDER BY id DESC");




if(array_key_exists("btn_add",$_POST)){


    $expiry_time = date("Y-m-d H:i:s", strtotime("+1 minute")); // calculate expiry time as 1 minute after the current time

$result = mysqli_query($db_connection, (
    "INSERT INTO `news`(
        `title`, 
        `description`, 
        `created_at`, 
        `updated_at`, 
        `expiry_at`
    )
    VALUES (
        '".mysqli_real_escape_string($db_connection,$_REQUEST["title"])."',
        '".mysqli_real_escape_string($db_connection,$_REQUEST["description"])."',
        '".$current_date ." ". $current_time."',
        '".$current_date ." ". $current_time."',
        '".$expiry_time."'
    )
"));

// delete expired records after 1 minute
$delete_time = date("Y-m-d H:i:s", strtotime("+1 minute")); // calculate delete time as 1 minute after the current time
$delete_result = mysqli_query($db_connection, "DELETE FROM `news` WHERE `expiry_at` <= '".$current_date." ".$current_time."'");

echo $result ? (  
        '<script>alert("Added!"); location.href="?page=news";</script>' 
    ) : ( 
        '<script>alert("Error!"); location.href="?page=news";</script>' 
    );
    
   

    
}

if(array_key_exists("btn_delete",$_POST)){


    $result = mysqli_query($db_connection,(
        "
        DELETE FROM news WHERE id = '".$_REQUEST["id"]."'
    
        "
    ));
    
    echo $result ? (  
            '<script>alert("Deleted!"); location.href="?page=news";</script>' 
        ) : ( 
            '<script>alert("Error!"); location.href="?page=news";</script>' 
        );
        
       
    
        
    }
    


?>