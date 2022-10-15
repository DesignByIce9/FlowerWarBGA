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



<div id="flowerwar_container">
    <div vertical-align="top" align="center" id="flowerwar_title"><h1>Flower War</h1></div>
    <div align = "left" id="flowerwar_resourcepanel">
        <h2>Resource Panel</h2>
    </div>
    <div align="center" id="flowerwar_board_container">
    <div vertical-align="top" align="center" id="fw_board_title"><h2>Board</h2></div>
    <div align="center" id="FW_board">
        <div vertical-align="top" align="center" class="fw_row" id="fw_b_top_row">
            <div class="fw_space" id="space_0">Space 0</div>
            <div class="fw_space" id="space_1">Space 1</div>
            <div class="fw_space" id="space_2">Space 2</div>
            <div class="fw_space" id="space_3">Space 3</div>
            <div class="fw_space" id="space_4">Space 4</div>
        </div>
        <div align="RIGHT"  id="fw_b_right_row">
            <div class="fw_space" id="space_5">Space 5</div>
            <div class="fw_space" id="space_6">Space 6</div>
            <div class="fw_space" id="space_7">Space 7</div>
            <div class="fw_space" id="space_8">Space 8</div>
            <div class="fw_space" id="space_9">Space 9</div>
        </div>
        <div align="center" vertical-align="center" id="fw_temple"><h1>Temple</h1></div>
        <div vertical-align="bottom" class="fw_row" align="center" id="fw_b_bottom_row">
            <div class="fw_space" id="space_10">Space 10</div>
            <div class="fw_space" id="space_11">Space 11</div>
            <div class="fw_space" id="space_12">Space 12</div>
            <div class="fw_space" id="space_13">Space 13</div>
            <div class="fw_space" id="space_14">Space 14</div>
        </div>
        <div align="left" class="fw_column" id="fw_b_left_row">
            <div class="fw_space" id="space_15">Space 15</div>
            <div class="fw_space" id="space_16">Space 16</div>
            <div class="fw_space" id="space_17">Space 17</div>
            <div class="fw_space" id="space_18">Space 18</div>
            <div class="fw_space" id="space_19">Space 19</div>
        </div>
    <div id=fw_board_footer><h2>bottom</h2></div>
</div>


<script type="text/javascript">

// Javascript HTML templates

/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';

*/

</script>  

{OVERALL_GAME_FOOTER}
