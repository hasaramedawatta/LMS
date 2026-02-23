<?php
include("../config/db.php");

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}
?>