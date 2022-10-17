{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Flower War. Original game by Ice 9 Games. Designed and developed by Tug Brice. Designsbyice9@gmail.com
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    flowerwar_flowerwar.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->



<script type="text/javascript">

// Javascript HTML templates

var jstpl_player_board = '<div class="cpboard">\
    <div id="aFaithCounter${id}" class="aFaithCounter"></div>Aztec Faith: <span id="azFaithCounter${id}">0</span>\
    <div id="cFaithCounter${id}" class="cFaithCounter"></div>Catholic Faith: <span id="cathFaithCounter${id}">0</span>\
    <div id="pCounter${id}" class="pCounter"></div>People: <span id="peopleCounter${id}">0</span>\
</div>';

</script>  

{OVERALL_GAME_FOOTER}