<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("queries.php");

function show_team_select($selectedteam = null){
    $teams = DataManager::getTeams();

    $content = "<label for='teamselect'>Team select:</label>";
    
    $content .= "<select id='teamselect' onchange='onTeamChange()'>";
    foreach($teams as $team)
    {
        $selected = $selectedteam == $team->abbrev ? "selected" : "";
        $content .= "<option value='$team->abbrev' $selected>$team->city $team->teamname</option>";
    }
    $content .= "</select>";

    $content .="<select id='playerselect' onchange='onPlayerChange()'></select>";
    
    return $content;
}

function show_others_team_select(){
    $teams = DataManager::getTeams();

    $content = "<label for='otherteamselect'>Team select:</label>";
    
    $content .= "<select id='otherteamselect' onchange='onOtherTeamChange()'>";
    foreach($teams as $team)
    {
        $content .= "<option value='$team->abbrev'>$team->city $team->teamname</option>";
    }
    $content .= "</select>";

    $content .= "<div id='otherplayerscheckboxesdiv'>
                </div>";
    
    $content .= "<button onclick='synergize()'>Synergy</button>";
    
    return $content;
}

?>