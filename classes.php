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
            public $team;
            public function __construct($firstname, $lastname, $playerID = null, $team = null)
            {
                $this->firstname = $firstname;
                $this->lastname = $lastname;
                $this->playerID = $playerID;
                $this->team = $team;
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
            public function __construct($playerID, $gameID, $time, $type, $success, $isHome) {
                $this->playerID = $playerID;
                $this->gameID = $gameID;
                $this->time = $time;
                $this->type = $type;
                $this->success = $success;
                $this->isHome = $isHome;
            }
        }
        
        class FreeThrow extends Shot
        {
            public $foultype;
            public $seq;
            public $total;
            public function __construct($playerID, $gameID, $time, $success, $isHome, $foultype, $seq, $total) {
                parent::__construct($playerID, $gameID, $time, "Free Throw", $success, $isHome, NULL, NULL);
                $this->seq = $seq;
                $this->total = $total;
                $this->foultype = $foultype;
            }
        }
        
        class Foul
        {
            public $foulID;
            public $gameID;
            public $time;
            public $isHome;
            public $foulerID;
            public $type;
            public $referee;
            public function __construct($gameID, $time, $isHome, $foulerID, $type, $referee = null) {
                $this->gameID = $gameID;
                $this->time = $time;
                $this->isHome = $isHome;
                $this->foulerID = $foulerID;
                $this->type = $type;
                $this->referee = $referee;
            }
        }
        
        class Assist
        {
            public $playerID;
            public $shotID;
            public function __construct($playerID, $shotID) {
                $this->playerID = $playerID;
                $this->shotID = $shotID;
            }
        }
        
        class Rebound
        {
            public $playerID;
            public $gameID;
            public $time;
            public $isHome;
            public $offensive;
            public function __construct($playerID, $gameID, $time, $isHome, $offensive) {
                $this->playerID = $playerID;
                $this->gameID = $gameID;
                $this->time = $time;
                $this->isHome = $isHome;
                $this->offensive = $offensive;
            }
        }
        
        class Turnover
        {
            public $turnoverID;
            public $playerID;
            public $gameID;
            public $time;
            public $isHome;
            public $type;
            public function __construct($playerID, $gameID, $time, $isHome, $type = null) {
                $this->playerID = $playerID;
                $this->gameID = $gameID;
                $this->time = $time;
                $this->isHome = $isHome;
                $this->type = $type;
            }
        }
        
        class Steal
        {
            public $stealID;
            public $playerID;
            public $turnoverID;
            public function __construct($playerID, $turnoverID) {
                $this->playerID = $playerID;
                $this->turnoverID = $turnoverID;
            }
        }
        
        class Block
        {
            public $blockID;
            public $playerID;
            public $shotID;
            public function __construct($playerID, $shotID) {
                $this->playerID = $playerID;
                $this->shotID = $shotID;
            }
        }
        
        class Shift
        {
            public $playerID;
            public $gameID;
            public $starttime;
            public $endtime;
            public $isHome;
            public function __construct($playerID, $gameID, $starttime, $isHome, $endtime = 0) {
                $this->playerID = $playerID;
                $this->gameID = $gameID;
                $this->starttime = $starttime;
                $this->isHome = $isHome;
                $this->endtime = $endtime;
            }
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
            public $home;
            public $away;
            public function __construct($gameID, $date, $home, $away) {
                $this->gameID = $gameID;
                $this->date = $date;
                $this->home = $home;
                $this->away = $away;
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