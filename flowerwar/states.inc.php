<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * flowerwar implementation : © Tug
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
        "transitions" => array( "" => 2 )
        //"transitions" => array( "" => 201 )
    ),
    
    // Note: ID=2 => your first state
/*
    2 => array(
        "name" => "startTurn",
        "description" => clienttranslate('Doing turn start stuff'),
        "descriptionmyturn" => clienttranslate('${you}, move your token to an available square or choose an option'),
        "type" => "game",
        "action" => "stStartTurn",
        "transitions" => array( "moveToken" => 3 )
),
*/
    2 => array(
        "name" => "moveToken",
        "description" => clienttranslate('${actplayer} is deciding where to move'),
        "descriptionmyturn" => clienttranslate('${you}, move your token to an available square or choose an option'),
        "type" => "activeplayer",
        "args" => "argsBoardState",
        "action" => "stMoveToken",
        "possibleactions" => array( "nextQuadA", "nextQuadC", "resetTime", "boardUpdate" ),
        "transitions" => array( "nextQuadA" => 2, "nextQuadC" => 2,"resetTime" => 2, "boardUpdate" => 4 )
),
    
    4 => array(
        "name" => "boardUpdate",
        "description" => clienttranslate('Updating the board'),
        "descriptionmyturn" => clienttranslate('Updating the board'),
        "type" => "game",
        "args" => "argsBoardState",
        "action" => "stBoardUpdate",
        "possibleactions" => array( "cardHandler" ),
        "transitions" => array( "cardHandler" => 5 )
        //"possibleactions" => array( "cardTestStart" ),
        //"transitions" => array( "cardTestStart" => 202 )
    ),

    5 => array(
        "name" => "cardHandler",
        "description" => clienttranslate('${actplayer} is encountering an event'),
        "descriptionmyturn" => clienttranslate('${you} are encountering an event'),
        "type" => "activeplayer",
        "args" => "argsCardState",
        "action" => "stCardHandler",
        "possibleactions" => array( "resourceLoop" ),
        "transitions" => array( "resourceLoop" => 10)
        //"possibleactions" => array( "cardTestEnd" ),
        //"transitions" => array( "cardTestEnd" => 203)
    ),

    10 => array(
        "name" => "resourceLoop",
        "description" => clienttranslate('${actplayer} is moving to the next quadrant'),
        "descriptionmyturn" => clienttranslate('${you} are moving to the next quadrant'),
        "type" => "activeplayer",
        "args" => "argPlayerState",
        "action" => "stResourceLoop",
        "possibleactions" => array( "moveToken", "endTurn", "winState" ),
        "transitions" => array( "moveToken" => 2, "endTurn" =>50, "winState" => 80)
    ),

    201 => array(
        "name" => "alwaysFirst",
        "description" => clienttranslate('${actplayer} is moving to the next quadrant'),
        "descriptionmyturn" => clienttranslate('${you} are moving to the next quadrant'),
        "type" => "game",
        "action" => "alwaysFirst",
        "possibleactions" => array( "moveToken", ),
        "transitions" => array( "moveToken" => 2 )
    ),
    // /////////////////////////////////////////////////////////
    202 => array(
        "name" => "cardTestStart",
        "description" => clienttranslate('Starting Test'),
        "descriptionmyturn" => clienttranslate('Starting Test'),
        "type" => "game",
        "action" => "stCardTestStart",
        "possibleactions" => array( "cardHandler", ),
        "transitions" => array( "cardHandler" => 5 )
    ),

    203 => array(
        "name" => "cardTestEnd",
        "description" => clienttranslate('Ending Test'),
        "descriptionmyturn" => clienttranslate('Ending Test'),
        "type" => "game",
        "action" => "stCardTestEnd",
        "possibleactions" => array( "cardTestStart", "resourceLoop" ),
        "transitions" => array( "cardTestStart" => 202, "resourceLoop" => 10 )
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