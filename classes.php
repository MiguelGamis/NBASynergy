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
            public $shotID;
            public $playerID;
            public $gameID;
            public $time;
            public $type;
            public $success;
            public $isHome;
            public $distance;
            public $shotclock;
            public function __construct($playerID, $gameID, $time, $type, $success, $isHome, $distance, $shotclock) {
                $this->playerID = $playerID;
                $this->gameID = $gameID;
                $this->time = $time;
                $this->type = $type;
                $this->success = $success;
                $this->isHome = $isHome;
                $this->distance = $distance;
                $this->shotclock = $shotclock;
            }
        }
        
        class FreeThrow extends Shot
        {
            public $foulID;
            public $seq;
            public $total;
            public function __construct($playerID, $gameID, $time, $success, $isHome, $foulID, $seq, $total) {
                parent::__construct($playerID, $gameID, $time, "Free Throw", $success, $isHome, NULL, NULL);
                $this->foulID = $foulID;
                $this->seq = $seq;
                $this->total = $total;
            }
        }
        
        class Foul
        {
            public $foulID;
            public $gameID;
            public $time;
            public $shotID;
            public $foulerID;
            public $type;
            public $referee;
            public function __construct($gameID, $time, $foulerID, $type, $referee = null, $shotID = null) {
                $this->gameID = $gameID;
                $this->time = $time;
                $this->foulerID = $foulerID;
                $this->type = $type;
                $this->shotID = $shotID;
                $this->referee = $referee;
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
            public function __construct($date, $home, $away, $time = 0) {
                $this->date = $date;
                $this->home = $home;
                $this->away = $away;
                $this->time = $time;
            }
        }
        
        class Team
        {
            public $abbrev;
            public $city;
            public $teamname;
            public function __construct($abbrev, $city, $teamname) {
                $this->abbrev = $abbrev;
                $this->city = $city;
                $this->teamname = $teamname;
            }
        }