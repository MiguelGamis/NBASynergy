<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function show_player_search()
{
    $content = "<form action='#'>";
    $content .= "<label for='player-search'>Player:</label><input type='text' id='player-search' name='player-search'/>";
    $content .= "</form>";
    $content .= "<input id='player-picked' type='hidden'></input>";
    $content .= "<script src='trends/trends.js'></script>";
    
    return $content;
}