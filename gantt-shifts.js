/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function renderGanttShifts(homeshifts)
{
    var homeplayersdiv = document.getElementById('gantt-players');
    
    var homeshiftsdiv = document.getElementById('gantt-shifts');
    homeshiftsdiv.setAttribute("height", homeshifts.length * 21);
    var ctx = homeshiftsdiv.getContext('2d');
    ctx.beginPath();
    ctx.moveTo(400,0);
    ctx.lineTo(400,400);
    ctx.stroke();
    ctx.moveTo(800,0);
    ctx.lineTo(800,400);
    ctx.stroke();
    ctx.moveTo(1200,0);
    ctx.lineTo(1200,400);
    ctx.stroke();
    
    homeshifts.forEach(
        function(playershift, index){
            var newDiv = document.createElement("div"); 
            var t = document.createTextNode(playershift.player.firstname + ' ' + playershift.player.lastname);
            newDiv.appendChild(t);
            newDiv.className = "child";
            homeplayersdiv.appendChild(newDiv);
            
            playershift.shifts.forEach(
                function(shift){
                    var timestart = (shift.starttime / 2880000) * 1600;
                    var timelength = ((shift.endtime - shift.starttime) / 2880000) * 1600;
                    ctx.rect(timestart, 5 + index * 21, timelength, 10);
                    ctx.stroke();
                }
            )
        }
    )
}