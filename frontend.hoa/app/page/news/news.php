<?php
if ($_SESSION["admin-auth"]["role"] != "Administrator") {
    header("Location: ?page=dashboard");
}



$news = mysqli_query($db_connection,"SELECT * FROM news ORDER BY id DESC");




if(array_key_exists("btn_add",$_POST)){


$result = mysqli_query($db_connection,(
    "
    INSERT INTO `news`(
        
        `title`, 
        `description`, 
        `created_at`, 
        `updated_at`
        )
         VALUES 
        (

    
        '".mysqli_real_escape_string($db_connection,$_REQUEST["title"])."',
        '".mysqli_real_escape_string($db_connection,$_REQUEST["description"])."',
        '".$current_date ." ". $current_time."',
        '".$current_date ." ". $current_time."'



        )
    "
));

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