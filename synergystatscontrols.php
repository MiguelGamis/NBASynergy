<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("queries.php");

function show_team_select(){
    $teams = DataManager::getTeams();
    $teamcols = array_chunk($teams, 10);

    $content = "
    <div class='dropdown'>
      <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>
         Choose a team
         <span class='caret'></span>
      </button>
      <ul id='team-picker' class='dropdown-menu multi-column columns-3' role='menu' aria-labelledby='menu1'>";
    $content .= "<div class='row' style='width: 900px;'>";
    foreach($teamcols as $teams)
    {
        $content .= "<div class='col-sm-4'><ul class='multi-column-dropdown teams'>";
        foreach($teams as $team)
        {
           $content.= "<li id='$team->abbrev'><a href='#'><img class='team-pick' src='//i.cdn.turner.com/nba/nba/assets/logos/teams/secondary/web/$team->abbrev.svg'/>$team->city $team->teamname</a></li>";
        }
        $content .= "</ul></div>";
    }
    $content.= "</div></ul></div>";
    
    return $content;
}

function prepare_player_select(){
    $content = "<div class='dropdown'>
      <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>
         Choose player
         <span class='caret'></span>
      </button>
      <ul id='player-picker' class='dropdown-menu' role='menu' aria-labelledby='menu1'></ul>";
    $content .= "</div>";
    return $content;
}

//function show_matchup_team_select(){
//    $teams = DataManager::getTeams();
//
//    $content = "
//    <div class='dropdown'>
//      <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>
//         Choose a team
//         <span class='caret'></span>
//      </button>
//      <ul id='matchup-team-picker' class='dropdown-menu' role='menu' aria-labelledby='menu1'>";
//    foreach($teams as $team)
//    {
//       $content.= "<li id='$team->abbrev'><a href='#'><img class='team-pick' src='Images/teamlogos/$team->abbrev.svg'/>$team->city $team->teamname</a></li>";
//    }
//    $content.= "</ul></div>";
//    
//    return $content;
//}

function show_matchup_team_select(){
    $teams = DataManager::getTeams();
    $teamcols = array_chunk($teams, 10);

    $content = "
    <div class='dropdown'>
      <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>
         Choose a team
         <span class='caret'></span>
      </button>
      <ul id='matchup-team-picker' class='dropdown-menu dropdown-menu-right multi-column columns-3' role='menu' aria-labelledby='menu1'>";
    $content .= "<div class='row' style='width: 850px;'>";
    foreach($teamcols as $teams)
    {
        $content .= "<div class='col-sm-4'><ul class='multi-column-dropdown teams'>";
        foreach($teams as $team)
        {
           $content.= "<li id='$team->abbrev'><a href='#'><img class='team-pick' src='//i.cdn.turner.com/nba/nba/assets/logos/teams/secondary/web/$team->abbrev.svg'/>$team->city $team->teamname</a></li>";
        }
        $content .= "</ul></div>";
    }
    $content.= "</div></ul></div>";
    
    return $content;
}

function prepare_multi_player_checkboxes(){
    $content = "
    <div class='dropdown'>
        <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>
           Choose 1 - 5 players
           <span class='caret'></span>
        </button>
        <ul id='matchup-picker-dropdown' class='dropdown-menu multi-column columns-3' role='menu' aria-labelledby='menu1'>
            <div id='matchup-picker-grid' class='row' style='width: 900px;'>
                <div class='col-sm-4'><ul id='matchup-picker-col-1' class='multi-column-dropdown'></ul></div>
                <div class='col-sm-4'><ul id='matchup-picker-col-2' class='multi-column-dropdown'></ul></div>
                <div class='col-sm-4'><ul id='matchup-picker-col-3' class='multi-column-dropdown'></ul></div>
            </div>
            <div><button id='matchup-players-button'>Go</button></div>
        </ul>
    </div>";
    
    return $content;
}

?>