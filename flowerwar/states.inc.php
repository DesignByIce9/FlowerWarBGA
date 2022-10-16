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
 * states.inc.php
 *
 * flowerwar game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

 
$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 3 )
    ),
    
    // Note: ID=2 => your first state


    3 => array(
        "name" => "queryBoard",
        "description" => clienttranslate('Generating possible moves for ${actplayer}'),
        "descriptionmyturn" => clienttranslate('Generating possible moves for ${you}'),
        "type" => "activeplayer",
        "action" => "queryBoard",
        "possibleactions" => array( "pickSpace"),
        "transitions" => array( "pickSpace" => 5 )
    ),

    5 => array(
        "name" => "pickSpace",
        "description" => clienttranslate('${actplayer}, pick an available space to move your token to'),
        "descriptionmyturn" => clienttranslate('Pick an available space to move your token to, ${you}'),
        "type" => "activeplayer",
        "action" => "displayPossibleMoves",
        "args" => "possibleMoves",
        "possibleactions" => array( "updateBoard"),
        "transitions" => array( "updateBoard" => 9 )
    ),

    9 => array(
        "name" => "updateSpace",
        "description" => clienttranslate('${actplayer}, moving your token to space ${actualMove}'),
    	"descriptionmyturn" => clienttranslate('Moving your token to space ${actualMove}, ${you}'),
        "type" => "activeplayer",
        "action" => "updateSpace",
        "args" => "actualMove",
        "possibleactions" => array( "playersInSpace", "drawCardState"),
        "transitions" => array( "playersInSpace" => 11, "drawCardState" => 30 )  
    ),

    11 => array(
        "name" => "playersInSpace",
        "description" => clienttranslate('${actplayer} has encountered players ${encounteredPlayers}. Waiting for action'),
    	"descriptionmyturn" => clienttranslate('${you} have encountered players ${encounteredPlayers}. Choose an action'),
        "type" => "activeplayer",
        "action" => "playersInSpace",
        "args" => "encounteredPlayers",
        "possibleactions" => array( "drawCardState"),
        "transitions" => array( "drawCardState" => 30)
    ),

    30 => array(
        "name" => "drawCardState",
        "description" => clienttranslate('${actplayer} is drawing a card'),
    	"descriptionmyturn" => clienttranslate('${you} are drawing a card'),
        "type" => "activeplayer",
        "action" => "drawCardState",
        "possibleactions" => array( "resolveCard"),
        "transitions" => array( "resolveCard" => 35)
    ),

    35 => array(
        "name" => "resolveCard",
        "description" => clienttranslate('Resolving Card'),
    	"descriptionmyturn" => clienttranslate('Resolving Card'),
        "type" => "game",
        "action" => "resolveCard",
        "args" => "actualCard",
        "possibleactions" => array( "resourceLoop", "loseCheck", "winCheck"),
        "transitions" => array( "resourceLoop" => 40, "loseCheck" => 60, "winCheck" => 70)
    ),

    40 => array(
        "name" => "ResourceLoop",
        "description" => clienttranslate('${actplayer} is spending their resources'),
    	"descriptionmyturn" => clienttranslate('${you}, you may now spend your resources. Choose an action'),
        "type" => "activeplayer",
        "action" => "resolveCard",
        "args" => "actualCard",
        "possibleactions" => array( "endTurn"),
        "transitions" => array( "endTurn" => 50)
    ),

    50 => array(
        "name" => "endTurn",
        "description" => clienttranslate('${actplayer} has ended their turn'),
        "descriptionmyturn" => clienttranslate('${you} have ended your turn'),
        "type" => "activeplayer",
        "action" => "endTurn",
        "possibleactions" => array( "queryBoard"),
        "transitions" => array( "queryBoard" => 2)
    ),

    60 => array(
        "name" => "loseCheck",
        "description" => clienttranslate('Checking'),
        "descriptionmyturn" => clienttranslate('Checking'),
        "type" => "game",
        "action" => "loseCheck",
        "args" => "playersToCheck", "lastState",
        "possibleactions" => array( "drawCardState", "ResourceLoop"),
        "transitions" => array( "drawCardState"  => 30, "ResourceLoop" => 40)
    ),

    70 => array(
        "name" => "winCheck",
        "description" => clienttranslate('Checking'),
        "descriptionmyturn" => clienttranslate('Checking'),
        "type" => "game",
        "action" => "winCheck",
        "args" => "activeplayer",
        "possibleactions" => array( "ResourceLoop", "gameWon"),
        "transitions" => array( "ResourceLoop" => 40, "gameWon"  => 75)
    ),

    75 => array(
        "name" => "gameWon",
        "description" => clienttranslate('${actplayer} has won the game!'),
        "descriptionmyturn" => clienttranslate('${you} have won the game!'),
        "type" => "game",
        "action" => "gameWon",
        "possibleactions" => array( "gameEnd"),
        "transitions" => array( "gameWon" => 99)
    ),
   
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



