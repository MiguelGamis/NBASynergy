<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <script src="jquery-3.1.1.min.js"></script>
        <script src="playertablefill.js">
        </script>
        <meta charset="UTF-8">
        <title></title>
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
        <select id='mySelect' onchange='onPlayerChange(this)'></select>
        <div id="txtHint"><b>Person info will be listed here...</b></div>
        <canvas id="myCanvas" width="1600" height="400" style="border:1px solid #000000;"/>
        <?php
            include("displaydata.php");
        ?>
    </body>
</html>
