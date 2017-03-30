/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function renderGanttShifts(shifts, divID)
{   
    var playersdiv = document.createElement('div');
    playersdiv.setAttribute("style", "float: left");
    var shiftsdiv = document.createElement('canvas');
    length = 1400;
    shiftsdiv.setAttribute("width", length);
    height = shifts.length * 21;
    shiftsdiv.setAttribute("height", height);
    shiftsdiv.setAttribute("style", "border:1px solid #000000; float: left");
    qlength = length/4;
    var ctx = shiftsdiv.getContext('2d');
    ctx.beginPath();
    ctx.moveTo(qlength,0);
    ctx.lineTo(qlength, height);
    ctx.stroke();
    ctx.moveTo(2*qlength,0);
    ctx.lineTo(2*qlength, height);
    ctx.stroke();
    ctx.moveTo(3*qlength,0);
    ctx.lineTo(3*qlength, height);
    ctx.stroke();
    
    shifts.forEach(
        function(playershift, index){
            var newDiv = document.createElement("div"); 
            var t = document.createTextNode(playershift.player.firstname + ' ' + playershift.player.lastname);
            newDiv.appendChild(t);
            newDiv.setAttribute("class", "child");
            playersdiv.appendChild(newDiv);
            
            playershift.shifts.forEach(
                function(shift){
                    var timestart = (shift.starttime / 2880000) * length;
                    var timelength = ((shift.endtime - shift.starttime) / 2880000) * length;
                    ctx.rect(timestart, 5 + index * 21, timelength, 10);
                    ctx.stroke();
                }
            )
        }
    )
    
    var basediv = document.getElementById(divID);
    basediv.appendChild(playersdiv);
    basediv.appendChild(shiftsdiv);
}