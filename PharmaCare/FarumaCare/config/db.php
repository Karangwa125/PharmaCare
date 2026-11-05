<?php
try{
$pdo = new PDO("mysql:host=localhost; dbname=Farumacare","root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


}
catch(PDOException $e){
    die("Failed: ".$e->getMessage());
}