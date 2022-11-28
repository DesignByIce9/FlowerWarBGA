<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * flowerwar implementation : © Alena Laskavaia <laskava@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * flowerwar game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
          */

$this->token_types = array(
        // --- gen php begin ---
// #this used to generate part of matherial.inc.php using genmat.php
'wcube' => array(
  'type' => 'cube',
  'name' => clienttranslate("Cube"),
),
'card_red' => array(
  'type' => 'card',
  'name' => clienttranslate("Red Spell"),
  'tooltip' => clienttranslate("This is tooltip for red spell"),
  't'=>1,'cn'=>'red','ipos'=>3,
),
'card_blue' => array(
  'type' => 'card',
  'name' => clienttranslate("Blue Spell"),
  'tooltip' => clienttranslate("This is tooltip for blue spell"),
  'tooltip_action' => clienttranslate("Click to cast it"),
  't'=>2,'cn'=>'blue','ipos'=>4,
),
'card_green' => array(
  'type' => 'card',
  'name' => clienttranslate("Green Spell"),
  't'=>3,'cn'=>'green','ipos'=>6,
),
        // --- gen php end ---
);


///////// board info
// Array index = board ID #
// 0 = Quad Number
// 1 = Space Number
// 2 = Az amount (Can be negative)
// 3 = Cath amount (Can be negative)
// 4 = People amount (Always positive)

$this-> board = array (
  0=> array(
    "Quad" => 1,
    "Space" => 1,
    "Az" => 0,
    "Cath" => 1,
    "People" => 1
  ),

  1=> array(
    "Quad" => 1,
    "Space" => 2,
    "Az" => 0,
    "Cath" => 2,
    "People" => 0   
  ),
  
  2=> array(
    "Quad" => 1,
    "Space" => 3,
    "Az" => 4,
    "Cath" => 0,
    "People" => -2    
  ),

  3=> array(
    "Quad" => 1,
    "Space" => 4,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0    
  ),

  4=> array(
    "Quad" => 1,
    "Space" => 5,
    "Az" => 0,
    "Cath" => 3,
    "People" => -1    
  ),

  5=> array(
    "Quad" => 2,
    "Space" => 1,
    "Az" => 0,
    "Cath" => 2,
    "People" => 0
  ),

  6=> array(
    "Quad" => 2,
    "Space" => 2,
    "Az" => 1,
    "Cath" => 0,
    "People" => 1
  ),

  7=> array(
    "Quad" => 2,
    "Space" => 3,
    "Az" => 0,
    "Cath" => 0,
    "People" => 2
  ),

  8=> array(
    "Quad" => 2,
    "Space" => 4,
    "Az" => 3,
    "Cath" => 0,
    "People" => -1
  ),

  9=> array(
    "Quad" => 2,
    "Space" => 5,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0
  ),


  10=> array(
    "Quad" => 3,
    "Space" => 1,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0
  ),

  11=> array(
    "Quad" => 3,
    "Space" => 2,
    "Az" => 0,
    "Cath" => 3,
    "People" => -1    
  ),

  12=> array(
    "Quad" => 3,
    "Space" => 3,
    "Az" => 0,
    "Cath" => 3,
    "People" => -1    
  ),

  13=> array(
    "Quad" => 3,
    "Space" => 4,
    "Az" => 0,
    "Cath" => 1,
    "People" => 1
  ),

  14=> array(
    "Quad" => 3,
    "Space" => 5,
    "Az" => 2,
    "Cath" => 0,
    "People" => 0    
  ),

  15=> array(
    "Quad" => 4,
    "Space" => 1,
    "Az" => 3,
    "Cath" => 0,
    "People" => -2
  ),

  16=> array(
    "Quad" => 4,
    "Space" => 2,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0    
  ),

  17=> array(
    "Quad" => 4,
    "Space" => 3,
    "Az" => 0,
    "Cath" => 4,
    "People" => -2    
  ),

  18=> array(
    "Quad" => 4,
    "Space" => 4,
    "Az" => 2,
    "Cath" => 0,
    "People" => 0    
  ),

  19=> array(
    "Quad" => 4,
    "Space" => 5,
    "Az" => 1,
    "Cath" => 0,
    "People" => 1    
  )
);

///////// Terrain info
// Array index = Terrain ID #
// 0 = terrainType - found as type_arg in card
// 1 = terrainName - found as type in card
// 2 = timeMod - modifies time needed to move through terrain (not implemented yet)
// 3 = tradeMod - modifies amount of resources received in basic trade (not implemented yet)
// 4 = battleMod - modifies roll for combat. Only applies to attacker (not implemented yet)

$this-> terrain = array (
  0=> array( 
    'terrainType' => 1,
    'terrainName' => 'Plains',
    'timeMod' => 0,
    'tradeMod' => 1,
    'battleMod' => -1
  ),
  1=> array( 
    'terrainType' => 2,
    'terrainName' => 'Road',
    'timeMod' => 1,
    'tradeMod' => 0,
    'battleMod' => -1
  ),
  2=> array( 
    'terrainType' => 3,
    'terrainName' => 'Hills',
    'timeMod' => 0,
    'tradeMod' => -1,
    'battleMod' => 1
  ),
  3=> array( 
    'terrainType' => 4,
    'terrainName' => 'River',
    'timeMod' => -1,
    'tradeMod' => 1,
    'battleMod' => 0
  ),
  4=> array( 
    'terrainType' => 5,
    'terrainName' => 'Jungle',
    'timeMod' => -1,
    'tradeMod' => 0,
    'battleMod' => 1
  )
);

///////// Event info
// Array index = nothing
// 0 = Event ID #
// 1 = cardTest - found as type in card

$this-> events = array (
  0=> array( 
    'eventID' => 1,
    'cardTest' => 'aPenalty',
  ),
  1=> array( 
    'eventID' =>2,
    'cardTest' => 'cPenalty',
  ),
  2=> array( 
    'eventID' =>3,
    'cardTest' => 'gPenalty',
  ),
  3=> array( 
    'eventID' =>4,
    'cardTest' => 'pPenalty',
  ),
  4=> array( 
    'eventID' =>5,
    'cardTest' => 'aCheck',
  ),
  5=> array( 
    'eventID' =>6,
    'cardTest' => 'cCheck',
  ),
  6=> array( 
    'eventID' =>7,
    'cardTest' => 'gCheck',
  ),
  7=> array( 
    'eventID' =>8,
    'cardTest' => 'pCheck',
  ),
  8=> array( 
    'eventID' =>9,
    'cardTest' => 'aConvert',
  ),
  9=> array( 
    'eventID' =>10,
    'cardTest' => 'cConvert',
  ),
  10=> array( 
    'eventID' =>11,
    'cardTest' => 'aCull',
  ),
  11=> array( 
    'eventID' =>12,
    'cardTest' => 'cCull',
  ),
  12=> array( 
    'eventID' =>13,
    'cardTest' => 'catchUp',
  ),
  13=> array( 
    'eventID' =>14,
    'cardTest' => 'aBonus',
  ),
  14=> array( 
    'eventID' =>15,
    'cardTest' => 'cBonus',
  ),
  15=> array( 
    'eventID' =>16,
    'cardTest' => 'gBonus',
  ),
  16=> array( 
    'eventID' =>17,
    'cardTest' => 'pBonus',
  ),
  17=> array( 
    'eventID' =>18,
    'cardTest' => 'uFigure',
  ),
  18=> array( 
    'eventID' =>19,
    'cardTest' => 'dFigure',
  ),
  19=> array( 
    'eventID' =>20,
    'cardTest' => 'iBlock',
  ),
  20=> array( 
    'eventID' =>21,
    'cardTest' => 'rBlock',
  ),
  21=> array( 
    'eventID' =>22,
    'cardTest' => 'nQuad',
  ),
  22=> array( 
    'eventID' =>23,
    'cardTest' => 'aSpace',
  ),
  23=> array( 
    'eventID' =>24,
    'cardTest' => 'rTime',
  )
);

///////// Character info
// Array index = nothing
// 0 = Character ID #
// 1 = terrain Mod - gives bonus for type of terrain (not implemented yet)
// 2 = encounter Mod - gives bonus for type of encounter (not implemented yet)
// 3 = faith Mod - gives bonus for particular faith (not implemented yet)

$this-> character = array (
  0=> array(
    'charID' => 1,
    'boardMod' => '',
    'encounterMod' => '',
    'faithMod' => '',
  ),
  1=> array(
    'charID' => 2,
    'boardMod' => '',
    'encounterMod' => '',
    'faithMod' => '',
  ),
  2=> array(
    'charID' => 3,
    'boardMod' => '',
    'encounterMod' => '',
    'faithMod' => '',
  ),
  3=> array(
    'charID' => 4,
    'boardMod' => '',
    'encounterMod' => '',
    'faithMod' => '',
  ),
  4=> array(
    'charID' => 5,
    'boardMod' => '',
    'encounterMod' => '',
    'faithMod' => '',
  ),
  5=> array(
    'charID' => 6,
    'boardMod' => '',
    'encounterMod' => '',
    'faithMod' => '',
  )
);

///////// Temple Cost - 
///// Currently hardcoded to max of 7 (base+6).
// Array index = overall number of moves
// 1 = level - current temple level
// 2 = cost - cost to move to that level
// 3 = type - F for faith, P for people.

$this -> templeCost = array(
  0=> array(
    'level' => 0,
    'cost' => 0,
    'type' => 'F'
  ),
  1=> array(
    'level' => 1,
    'cost' => 2,
    'type' => 'F'
  ),
  2=> array(
    'level' => 2,
    'cost' => 3,
    'type' => 'F'
  ),
  3=> array(
    'level' => 3,
    'cost' => 4,
    'type' => 'F'
  ),
  4=> array(
    'level' => 4,
    'cost' => 5,
    'type' => 'F'
  ),
  5=> array(
    'level' => 5,
    'cost' => 6,
    'type' => 'F'
  ),
  6=> array(
    'level' => 6,
    'cost' => 7,
    'type' => 'F'
  ),
  7=> array(
    'level' => 5,
    'cost' => 2,
    'type' => 'P'
  ),
  8=> array(
    'level' => 4,
    'cost' => 3,
    'type' => 'P'
  ),
  9=> array(
    'level' => 3,
    'cost' => 4,
    'type' => 'P'
  ),
  10=> array(
    'level' => 2,
    'cost' => 5,
    'type' => 'P'
  ),
  11=> array(
    'level' => 1,
    'cost' => 6,
    'type' => 'P'
  ),
  12=> array(
    'level' => 0,
    'cost' => 7,
    'type' => 'P'
  )
);

// card initialization lists

$this -> terrainName = array(
  'Plains' => 1,
  'River' => 2,
  'Hills' => 3,
  'Road' => 4,
  'Jungle' => 5
);

$this -> eventName = array(
'aPenalty' => 1,
'cPenalty' => 2,
'gPenalty' => 3,
'pPenalty' => 4,
'aCheck' => 5,
'gCheck' => 6,
'pCheck' => 7,
'aConvert' => 8,
'cConvert' => 9,
'aCull' => 10,
'cCull' => 11,
'catchUp' => 12,
'aBonus' => 13,
'cBonus' => 14,
'gBonus' => 15,
'pBonus' => 16,
'uFigure' => 17,
'dFigure' => 18,
'iBlock' => 19,
'rBlock' => 21,
'nQuad' => 22,
'aSpace' => 23,
'rTime' => 24
);