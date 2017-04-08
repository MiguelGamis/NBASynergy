/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 function processAjaxData(response, urlPath){
     document.getElementById("content").innerHTML = response.html;
     document.title = response.pageTitle;
     window.history.pushState({"html":response.html,"pageTitle":response.pageTitle},"", urlPath);
 }

function onTeamChange() {
    var teamselection = document.getElementById("teamselect");
    var teamabbrev = teamselection.value;
    
    if (teamabbrev === null) {
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
                if(this.responseText)
                {
                    $("#playerselect").empty();
                    var playerselect = document.getElementById("playerselect");
                    var players = JSON.parse(this.responseText);
                    for (i=0; i<players.length; i++) {
                        var option = document.createElement("option");
                        option.text = players[i].firstname + ' ' + players[i].lastname;
                        option.value = players[i].playerID;
                        playerselect.add(option);
                    }
                }
            }
        };
        xmlhttp.open("GET","getdata.php?action=getplayers&team="+teamabbrev,true);
        xmlhttp.send();
    }
}

function onPlayerChange() {
    var playerselection = document.getElementById("playerselect");
    var playerID = playerselection.value;
    
    if (playerID == null) {
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
                document.getElementById("playerdata").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getdata.php?action=getplayerdata&playerID="+playerID,true);
        xmlhttp.send();
    }
}

function onOtherTeamChange() {
    var teamselection = document.getElementById("otherteamselect");
    var teamabbrev = teamselection.value;
    
    if (teamabbrev === null) {
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
                if(this.responseText)
                {
                    $("#otherplayerscheckboxesdiv").empty();
                    var playerselectedID = document.getElementById("playerselect").value;
                    var checkboxesdiv = document.getElementById("otherplayerscheckboxes");
                    var players = JSON.parse(this.responseText);
                    for (i=0; i<players.length; i++) {
                        if(players[i].playerID == playerselectedID)
                        {
                            continue;
                        }
                        var cb = document.createElement("input");
                        cb.setAttribute("type", "checkbox"); 
                        cb.setAttribute("id", "checkbox"+players[i].playerID);
                        cb.setAttribute("class", "checkboxotherplayers");
                        cb.setAttribute("value", players[i].playerID);
                        var cblabel = document.createElement("label");
                        cblabel.setAttribute("for", "checkbox"+players[i].playerID);
                        cblabel.innerHTML = players[i].firstname + ' ' + players[i].lastname;
                        $('#otherplayerscheckboxesdiv').append(cb);
                        $('#otherplayerscheckboxesdiv').append(cblabel);
                    }
                }
            }
        };
        xmlhttp.open("GET","getdata.php?action=getplayers&team="+teamabbrev,true);
        xmlhttp.send();
    }
}

function script(playerID = null)
{
    if($('#teamselect').val())
    {
        $.ajax({url: "getdata.php?action=getplayers&team="+$('#teamselect').val(), success: function(result){
            var players = JSON.parse(result);
            for (i=0; i<players.length; i++) {
                var option = document.createElement("option");
                option.text = players[i].firstname + ' ' + players[i].lastname;
                option.value = players[i].playerID;
                $("#playerselect").append(option);
            }
            $("#playerselect").val(playerID);
        }});
    }
}