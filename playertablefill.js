/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function isInt(n) {
   return n % 1 === 0;
}

function onPlayerChange() {
    var playerselection = document.getElementById("playerSelect");
    var playerID = playerselection.value;
    
    if (!isInt(playerID)) {
        document.getElementById("playerData").innerHTML = "error";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("playerData").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getdata.php?playerID="+playerID,true);
        xmlhttp.send();
    }
}

function addOptions(players) {
    var playerSelect = document.getElementById('playerSelect') 

    for (i=0; i<players.length; i++) {
        var option = document.createElement("option")
        option.text = players[i].firstname + ' ' + players[i].lastname
        option.value = players[i].playerID
        playerSelect.add(option)
    }
}

function onChange(){
    onPlayerChange();
    onOpponentChange();
}

function onOpponentChange() {
    var opponentselection = document.getElementById("opponentSelect1");
    var opponentID = opponentselection.value;
    
    var playerselection = document.getElementById("playerSelect");
    var playerID = playerselection.value;
    
    if (!isInt(opponentID) || !isInt(playerID)) {
        document.getElementById("matchupData").innerHTML = "error";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("matchupData").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getmatchupdata.php?playerID="+playerID+"&opponentID="+opponentID,true);
        xmlhttp.send();
    }
}

function addOtherOptions(players) {
    var opponentSelect1 = document.getElementById('opponentSelect1') 

    for (i=0; i<players.length; i++) {
        var option = document.createElement("option")
        option.text = players[i].firstname + ' ' + players[i].lastname
        option.value = players[i].playerID
        opponentSelect1.add(option)
    }
}