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
        
        class FreeThrow
        {
            public $freethrowID;
            public $foulID;
            public $playerID;
            public $made;
            public $seq;
            public $total;
            public function __construct($foulID, $playerID, $made, $seq, $total) {
                $foulID
                $playerID
                $made
                $seq
                $total
            }
        }
        
        class Foul
        {
            public $foulID;
            public $gameID;
            public $time;
            public $shotID;
            public $foulerID;
            public $fouleeID;
            public $type;
            public function __construct($gameID, $time, $foulerID, $type, $fouleeID = null, $shotID = null) {
                $this->gameID = $gameID;
                $this->time = $time;
                $this->foulerID = $foulerID;
                $this->type = $type;
                $this->fouleeID = $fouleeID;
                $this->shotID = $shotID;
            }
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
            public $defensive;
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
            public function __construct($gameID, $date) {
                $this->gameID = $gameID;
            }
        }
        
        class Team
        {
            public $name;
        }