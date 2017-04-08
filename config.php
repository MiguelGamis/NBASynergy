<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

try{
    $db = new PDO('mysql:host=localhost;dbname=nbasynergy;charset=utf8', 'root', '', array(PDO::ATTR_PERSISTENT => true));
} catch (Exception $ex) {
    echo $ex->message();
}

if($db){
    //echo "<strong>Successfully connected</strong>";
}
 else {
     die("<strong>Error</strong> Could not connect to the database");
}