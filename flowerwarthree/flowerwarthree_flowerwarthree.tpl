{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- FlowerWarThree implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    flowerwarthree_flowerwarthree.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->


<div id="fw-container">
    <div id="messageContainer"><p id="message-text"></p></div>
    <div id="boardContainer">
        <div id="temple">
            <div id="apocFlag"></div>
                <div id="templeTracks">
                    <div class="templeTrack" id="azTemple"><p class = "trackHeader" id="azTitle">Aztec track direction: </p>
                        <div class="trackSpace" id="az_6"></div>
                        <div class="trackSpace" id="az_5"></div>
                        <div class="trackSpace" id="az_4"></div>
                        <div class="trackSpace" id="az_3"></div>
                        <div class="trackSpace" id="az_2"></div>
                        <div class="trackSpace" id="az_1"></div>
                        <div class="trackSpace" id="az_0"></div>
                    </div>
                    <div class="templeTrack" id="cathTemple"><p class = "trackHeader" id="cathTitle">Catholic track direction: </p>
                        <div class="trackSpace" id="cath_6"></div>
                        <div class="trackSpace" id="cath_5"></div>
                        <div class="trackSpace" id="cath_4"></div>
                        <div class="trackSpace" id="cath_3"></div>
                        <div class="trackSpace" id="cath_2"></div>
                        <div class="trackSpace" id="cath_1"></div>
                        <div class="trackSpace" id="cath_0"></div>
                    </div>
                </div>
        </div>
    </div>
</div>

<script type="text/javascript">

// Javascript HTML templates

var jstpl_player_board = '<div class="cpboard">\
    <div id="aContainer" class="counterContainer"><div id="aFaithCounter${id}" class="aFaithCounter"></div><div id="panel_icon_a" class="resourceIconA"></div><p class="counter-text">Aztec Faith: <span id="azFaithCounter${id}">0</span></p></div>\
    <div id="cContainer" class="counterContainer"><div id="cFaithCounter${id}" class="cFaithCounter"></div><div id="panel_icon_c" class="resourceIconC"></div><p class="counter-text">Catholic Faith: <span id="cathFaithCounter${id}">0</span></p></div>\
    <div id="pContainer" class="counterContainer"><div id="pCounter${id}" class="pCounter"></div><div id="panel_icon_p" class="resourceIconP"></div><p class="counter-text">People: <span id="peopleCounter${id}">0</span></p></div>\
    <div id="tContainer" class="counterContainer"><div id="tCounter${id}" class="tCounter"></div><div id="panel_icon_t" class="resourceIconT"></div><p class="counter-text">Time: <span id="timeCounter${id}">0</span></p></div>\
</div>';

</script>  

{OVERALL_GAME_FOOTER}
