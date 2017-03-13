<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <script src="jquery-3.1.1.min.js"></script>
        <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
        <script src="playertablefill.js"></script>
        <script src="gantt-shifts.js"></script>
        </script>
        <meta charset="UTF-8">
        <title></title>
        <style>
            #gantt-display {
                text-align: center;
            }
            #gantt-players {
                padding:1px;
            }
            #inner {
                overflow: hidden;
                width: 1800px;
            }
            .child {
                background:#ccc;
                height:20px;
                margin:1px;
            }
        </style>
    </head>
    <body>
<!--        <header>
            <h1>JSON and AJAX</h1>
            <button id="btn">Fetch Info for 3 New Animals</button>
        </header>
        
        <div id="animal-info"></div>
        
        <script src="main.js"></script>-->
        <h1>Testing...</h1>
        <div id="myTable"/>
        <select id='playerSelect' onchange='onChange()'></select>
        <select id='opponentSelect1' onchange='onOpponentChange()'></select>
        <div id="playerData"><b>Player stats will be listed here...</b></div>
        <div id="matchupData" ><b>Matchup stats will be listed here...</b></div>
        <div class="gantt-display">
            <div id="inner">
                <div id="gantt-players" style="float: left"></div>
                <canvas id="gantt-shifts" width="1600" height="400" style="border:1px solid #000000; float: left"/>
            </div>
        </div>
        
        <?php
            include("displaydata.php");
        ?>
    </body>
</html>
