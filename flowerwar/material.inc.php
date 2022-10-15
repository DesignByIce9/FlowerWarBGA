<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Flower War. Original game by Ice 9 Games. Designed and developed by Tug Brice. Designsbyice9@gmail.com
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


$this-> board = array (
  0=> array(
    "boardID" => 0,
    "Quad" => 1,
    "Space" => 1,
    "Az" => 0,
    "Cath" => 1,
    "People" => 1,
    "Terrain" => null
  ),

  1=> array(
    "boardID" => 1,
    "Quad" => 1,
    "Space" => 2,
    "Az" => 0,
    "Cath" => 2,
    "People" => 0,
    "Terrain" => null
  ),
  
  2=> array(
    "boardID" => 2,
    "Quad" => 1,
    "Space" => 3,
    "Az" => 4,
    "Cath" => 0,
    "People" => -2,
    "Terrain" => null
  ),

  3=> array(
    "boardID" => 3,
    "Quad" => 1,
    "Space" => 4,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0,
    "Terrain" => null
  ),

  4=> array(
    "boardID" => 4,
    "Quad" => 1,
    "Space" => 5,
    "Az" => 0,
    "Cath" => 3,
    "People" => -1,
    "Terrain" => null
  ),

  5=> array(
    "boardID" => 5,
    "Quad" => 2,
    "Space" => 1,
    "Az" => 0,
    "Cath" => 2,
    "People" => 0,
    "Terrain" => null
  ),

  6=> array(
    "boardID" => 6,
    "Quad" => 2,
    "Space" => 2,
    "Az" => 1,
    "Cath" => 0,
    "People" => 1,
    "Terrain" => null

  ),

  7=> array(
    "boardID" => 7,
    "Quad" => 2,
    "Space" => 3,
    "Az" => 0,
    "Cath" => 0,
    "People" => 2,
    "Terrain" => null
  ),

  8=> array(
    "boardID" => 8,
    "Quad" => 2,
    "Space" => 4,
    "Az" => 3,
    "Cath" => 0,
    "People" => -1,
    "Terrain" => null
  ),

  9=> array(
    "boardID" => 9,
    "Quad" => 2,
    "Space" => 5,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0,
    "Terrain" => null
  ),


  10=> array(
    "boardID" => 10,
    "Quad" => 3,
    "Space" => 1,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0,
    "Terrain" => null
  ),

  11=> array(
    "boardID" => 11,
    "Quad" => 3,
    "Space" => 2,
    "Az" => 0,
    "Cath" => 3,
    "People" => -1,
    "Terrain" => null
  ),

  12=> array(
    "boardID" => 12,
    "Quad" => 3,
    "Space" => 3,
    "Az" => 0,
    "Cath" => 3,
    "People" => -1,
    "Terrain" => null
  ),

  13=> array(
    "boardID" => 13,
    "Quad" => 3,
    "Space" => 4,
    "Az" => 0,
    "Cath" => 1,
    "People" => 1,
    "Terrain" => null
  ),

  14=> array(
    "boardID" => 14,
    "Quad" => 3,
    "Space" => 5,
    "Az" => 2,
    "Cath" => 0,
    "People" => 0,
    "Terrain" => null
  ),

  15=> array(
    "boardID" => 15,
    "Quad" => 4,
    "Space" => 1,
    "Az" => 3,
    "Cath" => 0,
    "People" => -2,
    "Terrain" => null
  ),

  16=> array(
    "boardID" => 16,
    "Quad" => 4,
    "Space" => 2,
    "Az" => 1,
    "Cath" => 1,
    "People" => 0,
    "Terrain" => null
  ),

  17=> array(
    "boardID" => 17,
    "Quad" => 4,
    "Space" => 3,
    "Az" => 0,
    "Cath" => 4,
    "People" => -2,
    "Terrain" => null
  ),

  18=> array(
    "boardID" => 18,
    "Quad" => 4,
    "Space" => 4,
    "Az" => 2,
    "Cath" => 0,
    "People" => 0,
    "Terrain" => null
  ),

  19=> array(
    "boardID" => 19,
    "Quad" => 4,
    "Space" => 5,
    "Az" => 1,
    "Cath" => 0,
    "People" => 1,
    "Terrain" => null
  )
);

$this-> terrain = array (
  0=> array( 
    'terrainID' => 0,
    'terrainType' => 1,
    'terrainName' => 'Plains',
    'timeMod' => 0,
    'tradeMod' => 1,
    'battleMod' => -1
  ),
  1=> array( 
    'terrainID' => 1,
    'terrainType' => 2,
    'terrainName' => 'Road',
    'timeMod' => 1,
    'tradeMod' => 0,
    'battleMod' => -1
  ),
  2=> array( 
    'terrainID' => 2,
    'terrainType' => 3,
    'terrainName' => 'Hills',
    'timeMod' => 0,
    'tradeMod' => -1,
    'battleMod' => 1
  ),
  3=> array( 
    'terrainID' => 3,
    'terrainType' => 4,
    'terrainName' => 'River',
    'timeMod' => -1,
    'tradeMod' => 1,
    'battleMod' => 0
  ),
  4=> array( 
    'terrainID' => 4,
    'terrainType' => 5,
    'terrainName' => 'Jungle',
    'timeMod' => -1,
    'tradeMod' => 0,
    'battleMod' => 1
  )
);

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




