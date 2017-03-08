/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function isInt(n) {
   return n % 1 === 0;
}

function onPlayerChange(selection) {
    var playerID = selection.value
    if (!isInt(playerID)) {
        document.getElementById("txtHint").innerHTML = "";
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
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getdata.php?playerID="+playerID,true);
        xmlhttp.send();
    }
}

function addOptions(players) {
    var myTable = document.getElementById('myTable')

    var selection = document.createElement("select")

    var mySelect = document.getElementById('mySelect') 

    for (i=0; i<players.length; i++) {
        var option = document.createElement("option")
        option.text = players[i].firstname + ' ' + players[i].lastname
        option.value = players[i].playerID
        selection.add(option, selection[i])
        mySelect.add(option)
    }
    
    var option = document.createElement("option")
    option.text = 'Get a rope'
    mySelect.options[0] = option
    
    selection.onchange = onPlayerChange();
    myTable.appendChild(selection);
}