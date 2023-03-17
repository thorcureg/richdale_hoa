<?php 

$users_overall = mysqli_query($db_connection, "SELECT * FROM users");
$users_active = mysqli_query($db_connection, "SELECT * FROM users WHERE status = 'Active'");
$users_inactive = mysqli_query($db_connection, "SELECT * FROM users WHERE status = 'Inactive'");
$users_not_setup = mysqli_query($db_connection, "SELECT * FROM users WHERE status = 'Not Setup'");
$users_pending = mysqli_query($db_connection, "SELECT * FROM users WHERE status = 'Pending'");
$users_admin = mysqli_query($db_connection, "SELECT * FROM users WHERE role = 'Administrator'");
$users_officer = mysqli_query($db_connection, "SELECT * FROM users WHERE role = 'Officer'");
$users_standard = mysqli_query($db_connection, "SELECT * FROM users WHERE role = 'Standard'");


$payments_overall = mysqli_query($db_connection, "SELECT * FROM payments");
$payments_pending = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Pending'");
$payments_cancelled = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Cancelled'");
$payments_paid = mysqli_query($db_connection, "SELECT * FROM payments WHERE status = 'Paid'");

$res_overall = mysqli_query($db_connection, "SELECT * FROM reservations");
$res_pending = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Pending'");
$res_cancelled = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Cancelled'");
$res_completed = mysqli_query($db_connection, "SELECT * FROM reservations WHERE status = 'Completed'");
$res_pay = mysqli_query($db_connection, "SELECT * FROM payments ORDER BY id ASC");
$res_reserve = mysqli_query($db_connection, "SELECT * FROM reservations  ORDER BY id ASC");

$res_overall = mysqli_query($db_connection, "SELECT * FROM reservations");
while($row = mysqli_fetch_array($res_completed)){
    $total_balance += $row["payment"];  
}


while($row = mysqli_fetch_array($payments_overall)){
    $total_balancee += $row["amount"];  
}


?>