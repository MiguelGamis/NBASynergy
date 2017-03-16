<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$html = file_get_contents('http://stats.nba.com/game/#!/0021600992/playbyplay/');
$f = fopen("test.txt", "w");
fwrite($f, $html);