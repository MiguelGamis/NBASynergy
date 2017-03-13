<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$html = file_get_contents('https://watch.nba.com/game/20170216/BOSCHI');

$test = substr_count($html, "End of 1st Quarter");

var_dump($test);