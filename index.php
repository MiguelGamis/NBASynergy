<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
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
        <div id="myTable"></div>
        <select id='playerSelect' onchange='onChange()'></select>
        <select id='opponentSelect1' onchange='onOpponentChange()'></select>
        <div id="playerData"><b>Player stats will be listed here...</b></div>
        <div id="matchupData" ><b>Matchup stats will be listed here...</b></div>
        <section>
            <div id="away-gantt"></div>
            <div id="home-gantt"></div>
        </section>
        
        <?php
            include("displaydata.php");
        ?>
    </body>
</html>
