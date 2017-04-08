/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function() {
    $( '#player-search' ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: 'trends/playersearch.php',
                dataType: 'json',
                data: {
                    q: request.term
                },
                success: function( data ) {
                    response( data );
                }
            });
        },
        select: function(e, ui) {
            e.preventDefault(); // <--- Prevent the value from being inserted.
            $("#player-search").val(ui.item.value);
            $(this).val(ui.item.label).trigger('change');
            $('#player-picked').val(ui.item.id).trigger('change');
        }
    });

});

var playersearch= document.getElementById('player-search');

$('#player-picked').change(function(){
    var playerID = $('#player-picked').val();
    var playerli = $('#'+playerID+' a');
    alert(playerli);
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
    
    $.ajax({url: 'getdata.php?action=getplayerdata&playerID='+playerID, 
        success: function(result){
        alert(result);
    });
});