<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

        class Player
        {
            public $playerID;
            public $firstname;
            public $lastname;
            public function player($fname, $lname)
            {
                $this->firstname = $fname;
                $this->lastname = $lname;
            }
        }
        
        class Shot
        {
            public $playerID;
            public $gameID;
            public $time;
            public $type;
            public $success;
            public $isHome;
            public $distance;
            public $shotclock;
        }
        
        class Assist
        {
            public $game;
            public $time;
            public $shot;
            public $playerID;
            public $isHome;
        }
        
        class Rebound
        {
            public $playerID;
            public $gameID;
            public $time;
            public $isHome;
        }
        
        class Foul
        {
            public $foulerID;
            public $fouleeID;
            public $gameID;
            public $time;
            public $type;
            public $isHome;
        }
        
        class Shift
        {
            public $playerID;
            public $gameID;
            public $starttime;
            public $endtime;
            public $isHome;
        }
        
        class Subsitution
        {
            public $subout;
            public $subin;
        }
        
        class Game
        {
            public $gameID;
            public $date;
            public $time;
            public $home;
            public $away;
            public function __construct($id) {
                $this->gameID = $id;
            }
        }
        
        class Team
        {
            public $name;
        }