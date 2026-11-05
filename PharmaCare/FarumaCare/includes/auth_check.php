<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('Location:C:\xampp\htdocs\php\PharmaCare\FarumaCare\login.php');//
    exit;
}



?>