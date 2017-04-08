/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var types = ['dunk', 'layup', 'hook', 'jump shot', 'fadeaway', '3pt', 'other'];
var makecolors = ["#ff5050","#ffb366","#ffff66","#85e085","#66ffcc","#66b3ff","#b3b3b3"];
var misscolors = ["#ff9999","#ffe5cc","#ffffcc","#d7f4d7","#ccffee","#cce6ff","#e6e6e6"];

$('#matchup-picker-dropdown').click(function(e) {
    e.stopPropagation();
});
$('#team-picked').change(function(){
    var logo = document.getElementById('team-picked-logo');
    var teampicked = document.getElementById('team-picked');
    logo.src = '//i.cdn.turner.com/nba/nba/assets/logos/teams/secondary/web/'+teampicked.value+'.svg';

    $.ajax({
        async: false,
        url: 'getdata.php?action=getplayers&team='+teampicked.value, 
        success: function(result){
        $('#player-picker').empty();
        var players = JSON.parse(result);
        for (i=0; i<players.length; i++) {
            var li = document.createElement('li');
            li.id = players[i].playerID;
            var a = document.createElement('a');
            a.href = '#';
            a.appendChild(document.createTextNode(players[i].firstname+' '+players[i].lastname));
            li.appendChild(a);
            $('#player-picker').append(li);
        }
        $('#player-picker li').on('click', function(){
            $('#player-picked').val(this.id)
                                .trigger('change');
        });
    }});
});

$('#player-picked').change(function(){
    var playerID = $('#player-picked').val();
    var playerli = $('#'+playerID+' a');
    if(playerli)
    {
        $('#player-picked-photo').attr('src', '//ak-static.cms.nba.com/wp-content/uploads/headshots/nba/latest/260x190/'+playerID+'.png');
        var textNode = document.createTextNode(playerli.text());
        $('#player-picked-name').empty();
        $('#player-picked-name').append(textNode);
    }
    else
    {
        $('#player-picked').val(null);
    }
});

//$('#matchup-players-picked').change(function(){
//    var playerstring = $('#matchup-players-picked').val();
//    var players = playerstring.split("~");
//    $('#matchup-players-identifier').empty();
//    players.forEach(function(playerID){
//        var playerprofile = document.createElement('div');
//        playerprofile.style = 'width:20%; float:left';
//        
//        var playername = document.createElement('div');
//        playername.innerHTML = $('#matchup-player-'+playerID).text();
//        playername.style = 'width:100%';
//        
//        var playerimage = document.createElement('img');
//        playerimage.src = '//ak-static.cms.nba.com/wp-content/uploads/headshots/nba/latest/260x190/'+playerID+'.png';
//        playerimage.style = 'width:100%';
//        
//        playerprofile.appendChild(playerimage);
//        playerprofile.appendChild(playername);
//        
//        $('#matchup-players-identifier').append(playerprofile);
//    });
//});

//$('#matchup-players-button').on('click', function(){
//    alert('Go time');
//    var matchupplayers = [];
//    $('.matchup-checkbox:checked').each(function(){
//        matchupplayers.push($(this).val());
//    });
//    var matchupplayersval = matchupplayers.join("~");
//    $('#matchup-players-picked').val(matchupplayersval).trigger('change');
//});

$('#team-picker li').on('click', function(){
    if($('#team-picked').val() !== this.id)
    {
        $('#team-picked').val(this.id)
                    .trigger('change');
    }
});

$('#matchup-team-picked').change(function(){
    var logo = document.getElementById('matchup-team-picked-logo');
    var matchupteampicked = document.getElementById('matchup-team-picked');
    logo.src ='//i.cdn.turner.com/nba/nba/assets/logos/teams/secondary/web/'+matchupteampicked.value+'.svg';

    $('#matchup-players-identifier').empty();

    $.ajax({async: false, url: 'getdata.php?action=getplayers&team='+matchupteampicked.value, success: function(result){
        $('#matchup-picker-col-1').empty();
        $('#matchup-picker-col-2').empty();
        $('#matchup-picker-col-3').empty();
        var players = JSON.parse(result);
        for (i=0; i<players.length; i++) {
            var label = document.createElement('label');
            label.setAttribute('for', "cb-"+players[i].playerID);
            
            var li = document.createElement('li');
            li.id = players[i].playerID;
            
            var cb = document.createElement("input");
            cb.setAttribute("type", "checkbox");
            cb.setAttribute("class", "matchup-checkbox"); 
            cb.setAttribute("value", players[i].playerID);
            cb.setAttribute("id", "cb-"+players[i].playerID);
            cb.setAttribute('style', 'float:left');
            
            var profilepic = document.createElement("img");
            profilepic.setAttribute('src', '//ak-static.cms.nba.com/wp-content/uploads/headshots/nba/latest/260x190/'+players[i].playerID+'.png');
            profilepic.setAttribute('style', 'width:40%;');
            
            var name = document.createTextNode(players[i].firstname+' '+players[i].lastname);
            
            var div = document.createElement('div');
            div.id = 'matchup-player-'+players[i].playerID;
            div.style.cursor = 'pointer';
            div.appendChild(profilepic);
            div.appendChild(cb);
            div.appendChild(name);
            
            label.appendChild(div);
            li.appendChild(label);
            var col = i%3;
            $('#matchup-picker-col-'+(col+1)).append(li);
        }
        $('.matchup-checkbox').on('change', function() {
            if($('.matchup-checkbox:checked').length == 5) {
                $('.matchup-checkbox').attr('disabled', 'disabled');
                $('.matchup-checkbox:checked').removeAttr('disabled');
            }else{
                $('.matchup-checkbox').removeAttr('disabled');
            }
            
            if(this.checked)
            {
                var playerprofile = document.createElement('div');
                playerprofile.id = 'player-profile-'+this.value;

                var playername = document.createElement('div');
                playername.innerHTML = $('#matchup-player-'+this.value).text();
                playername.style = 'width:100%';

                var playerimage = document.createElement('img');
                playerimage.src = '//ak-static.cms.nba.com/wp-content/uploads/headshots/nba/latest/260x190/'+this.value+'.png';
                playerimage.style = 'width:100%';
                
                playerprofile.appendChild(playerimage);
                playerprofile.appendChild(playername);
                $('#matchup-players-identifier').append(playerprofile);
                $('#player-profile-'+this.value).attr('style','width:20%;float:left');
            }
            else
            {              
                $('#player-profile-'+this.value).remove();
            }
         });
    }});
});

$('#matchup-team-picker li').on('click', function(){
    $('#matchup-team-picked').val(this.id)
                .trigger('change');
});

$('#compute-button').on('click', function(){
    synergize();
});


function synergize(){
    var teamID = $('#team-picked').val();
    var matchupteamID = $('#matchup-team-picked').val();
    
    var playerID = $('#player-picked').val();
    var matchupplayers = [];
    $('.matchup-checkbox:checked').each(function(){
        matchupplayers.push($(this).val());
    });
    if(playerID == null || matchupplayers.length == 0 || matchupteamID == null || teamID == null)
    {
        document.location.hash = '';
        return;
    }
    document.location.hash = '?teamID='+teamID+'&playerID='+playerID+'&matchupteamID='+matchupteamID+'&matchupplayers='+matchupplayers.join(",");

    var matchupargs = "";
    matchupplayers.forEach(function(playerID, index){ matchupargs += '&matchup'+(index+1)+'='+playerID; });

    alert('getdata.php?action=getplayerdata&playerID='+playerID+matchupargs);

    $.ajax({url:'getdata.php?action=getplayerdata&playerID='+playerID+matchupargs, success: function(result){
        var json = JSON.parse(result);
        $('#customdata').html(json[0]);

        
        var shottypechartsdiv = document.createElement("div");
        shottypechartsdiv.id = 'shottype-charts';
        
        var shottypechartwithoutdiv = document.createElement("div");
        shottypechartwithoutdiv.style = "float:left";
        var withoutlabel = document.createElement("p");
        withoutlabel.innerHTML = 'Without';
        shottypechartwithoutdiv.id = 'shottype-chart-without';
        var shottypecanvaswithout = document.createElement("canvas");
        shottypecanvaswithout.id = "shottype-without";
        shottypechartwithoutdiv.appendChild(withoutlabel);
        shottypechartwithoutdiv.appendChild(shottypecanvaswithout);
        
        var shottypechartwithdiv = document.createElement("div");
        shottypechartwithdiv.style = "float:left";
        var withlabel = document.createElement("p");
        withlabel.innerHTML = 'With';
        shottypechartwithdiv.id = 'shottype-chart-with';
        var shottypecanvaswith = document.createElement("canvas");
        shottypecanvaswith.id = "shottype-with";
        shottypechartwithdiv.appendChild(withlabel);
        shottypechartwithdiv.appendChild(shottypecanvaswith);
        
        shottypechartsdiv.appendChild(shottypechartwithoutdiv);
        shottypechartsdiv.appendChild(shottypechartwithdiv);
        $('#customdata').append(shottypechartsdiv);

        plotShotTypeData(json[2], 'shottype-without');
        plotShotTypeData(json[1], 'shottype-with');
    }});
}

var kvp = window.location.hash.substr(2).split('&');
var teampicked = null;
var playerpicked = null;
var machupteam = null;
var matchupplayers = null;
for(var i = 0; i<kvp.length; i++)
{
    x = kvp[i].split('=');
    if(x[0]=='teamID')
    {
       teampicked = x[1];
    }
    else if(x[0]=='playerID')
    {
        playerpicked = x[1];
    }
    else if(x[0] == 'matchupteamID')
    {
        matchupteam = x[1];
    }
    else if(x[0] == 'matchupplayers')
    {
        matchupplayers = x[1];
    }
}
if(teampicked)
{
    $('#team-picked').val(teampicked).trigger('change');
}
if(playerpicked)
{
    $('#player-picked').val(playerpicked).trigger('change');
}
if(matchupteam)
{
    $('#matchup-team-picked').val(matchupteam).trigger('change');
}
if(matchupplayers)
{
    //$('#matchup-players-picked').val(matchupplayers).trigger('change');
    matchupplayers.split(',').forEach(function(playerID){
        $('#cb-'+playerID).attr('checked', true).trigger('change');
    });
}

function plotShotTypeData(data, canvasid) {
    var canvas;
    var ctx;
    var lastend = 0;
    var myTotal = 0;

    var typemakes = Array.apply(null, Array(types.length)).map(Number.prototype.valueOf,0);
    var typecounts = Array.apply(null, Array(types.length)).map(Number.prototype.valueOf,0);

    //run through shots
    for (var key in data) {
        if (data.hasOwnProperty(key)) {
            var shottypeshots = data[key];
            for(var i = 0; i < shottypeshots.length; i++)
            {
                var shot = shottypeshots[i];
                var typefound = false;
                for(var t = 0; t < types.length - 1; t++)
                {
                    if(shot.type.toLowerCase().indexOf(types[t]) >= 0)
                    {
                        typecounts[t]++;
                        if(shot.made)
                        {
                            typemakes[t]++;
                        }
                        typefound = true;
                        break;
                    }
                }
                if(!typefound)
                {

                    typecounts[types.length - 1]++;
                    if(shot.made)
                    {
                        typemakes[types.length - 1]++;
                    }
                }
                myTotal++;
            }
        }
    }

    //render pie charts
    canvas = document.getElementById(canvasid);
    var x = (canvas.width)/2;
    var y = (canvas.height)/2;
    var radius = Math.min(x,y) * 0.9;
    ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, x, y);

    for (var i = 0; i < typecounts.length; i++) {
        ctx.beginPath();
        ctx.moveTo(x,y);
        ctx.fillStyle = misscolors[i];
        ctx.arc(x,y,radius,lastend,lastend+
          (Math.PI*2*(typecounts[i]/myTotal)),false);
        ctx.lineTo(x,y);
        ctx.fill();

        ctx.beginPath();
        ctx.moveTo(x,y);
        ctx.fillStyle = makecolors[i];
        ctx.arc(x,y,(typemakes[i]/typecounts[i])*radius,lastend,lastend+
          (Math.PI*2*(typecounts[i]/myTotal)),false);
        ctx.lineTo(x,y);
        ctx.fill();
        lastend += Math.PI*2*(typecounts[i]/myTotal);
    }
}