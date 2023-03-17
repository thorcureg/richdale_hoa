<?php 

$payments_overall = mysqli_query($db_connection, "SELECT * FROM payments WHERE user_id = '".$_SESSION["user-auth"]["id"]."'");
$payments_pending = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Pending' AND user_id = '".$_SESSION["user-auth"]["id"]."'");
$payments_cancelled = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Cancelled' AND user_id = '".$_SESSION["user-auth"]["id"]."'");
$payments_paid = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Paid' AND user_id = '".$_SESSION["user-auth"]["id"]."'");
$res_overall = mysqli_query($db_connection, "SELECT * FROM reservations");
$res_pending = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Pending' AND user_id = '".$_SESSION["user-auth"]["id"]."'");
$res_cancelled = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Cancelled' AND user_id = '".$_SESSION["user-auth"]["id"]."'");
$res_completed = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Completed' AND user_id = '".$_SESSION["user-auth"]["id"]."'");

$res_pending = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Pending' AND user_id = '".$_SESSION["user-auth"]["id"]."'");

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

$req_pay = mysqli_query($db_connection, "SELECT * FROM payment_updates WHERE status = 'Pending' AND  user_id = '".$_SESSION["user-auth"]["id"]."'");
$req_pay_count = mysqli_query($db_connection, "SELECT * FROM payment_updates WHERE status = 'Pending' AND  user_id = '".$_SESSION["user-auth"]["id"]."'");
$req_pay_paid_count = mysqli_query($db_connection, "SELECT * FROM payment_updates WHERE status = 'Paid' AND  user_id = '".$_SESSION["user-auth"]["id"]."'");

while($row = mysqli_fetch_array($req_pay_count)){
    $total_balance += $row["amount"];  
}

while($row = mysqli_fetch_array($req_pay_paid_count)){
    $total_balance += $row["amount"];  
}

$hoa_pending_pay = mysqli_fetch_array( mysqli_query($db_connection, "SELECT * FROM homeowners WHERE  user_id = '".$_SESSION["user-auth"]["id"]."'"));




$res_pending_pay = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Pending' AND user_id = '".$_SESSION["user-auth"]["id"]."' ORDER BY id DESC");
$res_pay = mysqli_query($db_connection, "SELECT * FROM payments WHERE user_id = '".$_SESSION["user-auth"]["id"]."' ORDER BY id DESC");

$total_balance = $total_balance + ($hoa_pending_pay["price"] * $hoa_pending_pay["land_size"]);

$total_balance = $total_balance - $total_spent;
?>