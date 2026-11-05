<?php
try{
$pdo = new PDO("mysql:host=localhost; dbname=Farumacare","root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
echo "connected";

}
catch(PDOException $e){
    die("Failed: ".$e->getMessage());
}