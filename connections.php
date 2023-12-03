<?php
$connect = new PDO("mysql:host=localhost; dbname=library_managment_system", "root", "");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
