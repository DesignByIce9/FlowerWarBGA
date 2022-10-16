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
  * flowerwar.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class flowerwar extends Table
{
	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array( 
                "faithThreshold" => 10,
                "peopleThreshold" => 11,
                "faithPenalty" => 12,
                "PeoplePenalty" => 13,
                "faithBonus" => 14,
                "peopleBonus" => 15,
                "faithConversionRate" => 16,
                "peopleConversionRate" => 17,
                "templeMaxHeight" => 18,
                "apocFlag" => 19,
                "azFlag" => 20,
                "cathFlag" => 21,
                "azLevel" => 22,
                "cathLevel" => 23,
                "quadUpdateFlag" => 24,
                "timeUpdateFlag" => 25,
                "turnCount" => 26,
                "roundCount" => 27,
                "numPlayers" => 28,
                "blockerSpace" => 29,
        ) );
            $this->cards = self::getNew( "module.common.deck" );
            $this->cards ->init( "card" );
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "flowerwar";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        $sql = "INSERT INTO `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player ) {
            $values[] = "('".$player_id."',2,2,8,1,0,0)";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );

        $sql = "INSERT INTO `tokens` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`,`turn`) VALUES ";
        $values = array();
        $tID = 1;
        $startQuad = 0;
        $startBoard = 0;
        foreach( $players as $player_id => $player ) {
            $startQuad = ($tID);
            $startBoard = (($startQuad-1)*5);
            $values[] = "('".$player_id."','".$tID."','".$startBoard."','".$startQuad."',1,0)";
            $tID++;
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );

        $sql = "INSERT INTO `tokens` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`,`turn`) VALUES ";
        $values = array();
        $tID = 5;
        $startQuad = 0;
        $startBoard = 0;
        for($i=1;$i<5;$i++){
            $values[] = "(5,5,0,'".$i."',0,0)";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );

        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        self::setGameStateInitialValue( 'faithThreshold', 6 );
        self::setGameStateInitialValue( 'peopleThreshold', 4 );
        self::setGameStateInitialValue( 'faithPenalty', 2 );
        self::setGameStateInitialValue( 'PeoplePenalty', 1 );
        self::setGameStateInitialValue( 'faithBonus', 2 );
        self::setGameStateInitialValue( 'peopleBonus', 1 );
        self::setGameStateInitialValue( 'faithConversionRate', 2 );
        self::setGameStateInitialValue( 'peopleConversionRate', 3 );
        self::setGameStateInitialValue( 'templeMaxHeight', 7 );
        self::setGameStateInitialValue( 'apocFlag', 0 );
        self::setGameStateInitialValue( 'azFlag', false );
        self::setGameStateInitialValue( 'cathFlag', false );
        self::setGameStateInitialValue( 'azLevel', 0 );
        self::setGameStateInitialValue( 'cathLevel', 0 );
        self::setGameStateInitialValue( 'quadUpdateFlag', false );
        self::setGameStateInitialValue( 'timeUpdateFlag', false );
        self::setGameStateInitialValue( 'turnCount', 0 );
        self::setGameStateInitialValue( 'roundCount', 0 );
        self::setGameStateInitialValue( 'numPlayers', count($players) );
        self::setGameStateInitialValue( 'blockerSpace', 0 );
        

        $nPlayers = self::getGameStateValue('numPlayers');

        
        $terrain = array();
        foreach( $this->terrainName as $tName => $tValue) {
            $terrain[] = array( 'type' => $tName, 'type_arg' => $tValue, 'nbr' => 4);
        }
        $this->cards->createCards( $terrain, 'terrain' );
        
        $events = array();
        foreach( $this->eventName as $eName => $eValue) {
            $events[] = array( 'type' => $eName, 'type_arg' => $eValue, 'nbr' => 2);
        }
        $this->cards->createCards( $events, 'deck' );

        for($x=0;$x<20;$x++) {
            $this->cards->pickCardForLocation("terrain", "board", $x);
        }



        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).
  
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

    function getBoardPosition($player_id) {
        $pID = $player_id;
        $tID = self::getCollectionFromDB( "SELECT `tokenID` FROM `tokens` WHERE `player_id` = $pID" );
        $cBoard = self::getCollectionFromDB( "SELECT `boardID` FROM `tokens` WHERE `player_id` = $pID" );
        $cQuad = self::getCollectionFromDB( "SELECT `Quad` FROM `tokens` WHERE `player_id` = $pID" );
        $cSpace = self::getCollectionFromDB( "SELECT `Space` FROM `tokens` WHERE `player_id` = $pID" );
        $blocker = self::getGameStateValue('blockerSpace');
        
        if ($blocker == $cSpace) {
            $blockerFlag = false;
        } else{
            $blockerFlag = true;
        }

        $spaceOccupied = self::getCollectionFromDB("SELECT `tokenID` FROM `tokens` WHERE `boardID` = $cBoard AND `player_id` != $player_id");
        if(count($spaceOccupied) == 0) {
            $oFlag = false;
        } else {
            $oFlag = true;
        }

        $boardPos = array(
            "tokenID" => $tID, "boardID" => $cBoard, "Quad" => $cQuad, "Space" => $cSpace, "Blocked" => $blockerFlag, "Occupied" => $oFlag
        );

        return $boardPos;
    }

    function checkMoves($quad, $qFlag) {
        $pID = self::getActivePlayerId();
        $boardPos = getBoardPosition($pID);
        $pResources =getPlayerResources($pID);
        $cBoardID = $boardPos['boardID'];
        $cQuad = $quad;
        $quadFlag = $qFlag;
        $cTime = $pResources['Time'];
        $lastSpace = 0;
        $blockedSpace = self::getGameStateValue('blockerSpace');
        $possibleMoves = array();
        $blockedMoves = array();
        $allMoves = array();
        
        if ($quadFlag == false) {
            $testSpace = $cBoardID++;    
        } else if ($quadFlag == true) {
            if($cTime <4) {
                if($cQuad <4)
                {
                    $cQuad++;
                } else if($cQuad ==4) {
                    $cQuad = 1;
                }
                $testSpace = (($cQuad-1)*5);    
            } else if ($cTime == 4) {
                if($cQuad <4)
                {
                    $cQuad++;
                } else if($cQuad ==4) {
                    $cQuad = 1;
                }
                $testSpace = (($cQuad-1)*5);    
            }
            $lastSpace = (($cQuad*5)-1);
            for($z=$testSpace;$z<=$lastSpace;$z++) {
                if((($z+1)-($cQuad-1)*5) == $blockedSpace) {
                    array_push($blockedMoves, $z);
                } else {
                    array_push($possibleMoves, $z);
                }
            }
        }
        $allMoves = array(
            "Possible" => $possibleMoves, "Blocked" => $blockedMoves
        );
        
        return $allMoves;
    }

    function updateBoard($player_id, $actualMove) {
        $pID = $player_id;
        $cPos = getBoardPosition();
        $tID = $cPos["tokenID"];
        $newMove = $actualMove;
        $newQuad = $this ->board[$actualMove]["Quad"];
        $newSpace = $this ->board[$actualMove]["Space"];
        $turn = $this ->getGameStateValue("turnCount");

        $sql = "INSERT INTO `tokens` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`,`turn`) VALUES ( '".$pID."', '".$tID."', '".$newMove."', '".$newQuad."', '".$newSpace."', '".$turn."') ";

    }

    function getPlayerResources($player_id) {
        $pID = $player_id;
        $cAz = self::getCollectionFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID" );
        $cCath = self::getCollectionFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID" );
        $cPeople = self::getCollectionFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID" );
        $cTime = self::getCollectionFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $pID" );
        $cID = self::getCollectionFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $pID" );
        
        $pResources = array(
            'Az' => $cAz, 'Cath' => $cCath, 'People' => $cPeople, 'Time' => $cTime, 'cID' => $cID
        );

        return $pResources;

    }

    function updateResources($player_id, $resource, $newTotal) {
        $pID = $player_id;
        $cResources = getPlayerResources($pID);
        $cAz = $cResources['Az'];
        $cCath = $cResources['Cath'];
        $cPeople = $cResources['People'];
        $cTime = $cResources['Time'];
        $cID = $cResources['cID'];

        switch ($resource) {
            case 'A':
                $cAz = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID."') ";
                self::DbQuery( $sql );
            break;
            case 'C':
                $cCath = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID."') ";
                self::DbQuery( $sql );
            break;
            case 'P':
                $cPeople = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID."') ";
                self::DbQuery( $sql );
            break;
            case 'T':
                $cTime = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID."') ";
                self::DbQuery( $sql );
            break;
        }
    }

    function setBlocker() {

    }

    function convertFaith($player_id, $fromWhich) {
        $pID = $player_id;
        $cResources = getPlayerResources($pID);
        $cAz = $cResources['Az'];
        $cCath = $cResources['Cath'];
        $fRate = $this ->getGameStateValue("faithConversionRate");

        switch ($fromWhich) {
            case 'A':
                if ($cAz < $fRate) {
                    throw new BgaUserException( self::_("You don't have enough Aztec Faith.") );
                } else {
                    $cAz -= $fRate;
                    $cCath++;
                    updateResources($pID, 'A', $cAz);
                    updateResources($pID, 'C', $cCath);
                }
            break;
            case 'C':
                if ($cCath < $fRate) {
                    throw new BgaUserException( self::_("You don't have enough Catholic Faith.") );
                } else{
                    $cCath -= $fRate;
                    $cAz++;
                    updateResources($pID, 'A', $cAz);
                    updateResources($pID, 'C', $cCath);
                }
            break;
        }
    }

    function convertPeople($player_id, $fromWhich) {
        $pID = $player_id;
        $cResources = getPlayerResources($pID);
        $cAz = $cResources['Az'];
        $cCath = $cResources['Cath'];
        $cPeople = $cResources['People'];
        $pRate = $this ->getGameStateValue("peopleConversionRate");

        switch ($fromWhich) {
            case 'A':
                if ($cAz < $fRate) {
                    throw new BgaUserException( self::_("You don't have enough Aztec Faith.") );
                } else {
                    $cAz -= $pRate;
                    $cPeople++;
                    updateResources($pID, 'A', $cAz);
                    updateResources($pID, 'P', $cPeople);
                }
            break;
            case 'C':
                if ($cCath < $pRate) {
                    throw new BgaUserException( self::_("You don't have enough Catholic Faith.") );
                } else{
                    $cCath -= $fRate;
                    $cPeople++;
                    updateResources($pID, 'C', $cAz);
                    updateResources($pID, 'P', $cPeople);
                }
            break;
        }
    }
    
    function cardConvert($player_id, $fromWhich) {
        $pID = $player_id;
        $cResources = getPlayerResources($pID);
        $cAz = $cResources['Az'];
        $cCath = $cResources['Cath'];
        $cPeople = $cResources['People'];
        $pRate = $this ->getGameStateValue("peopleConversionRate");
        $aFlag = $this ->getGameStateValue("apocFlag");

        switch ($fromWhich) {
            case 'A':
                if($aFlag != 2) {
                    $cAz += $pRate;
                    $cPeople--;
                    updateResources($pID, 'A', $cAz);
                    updateResources($pID, 'P', $cPeople);
                } else if ($aFlag == 2) {
                    $cAz -= ($pRate-1);
                    $cPeople++;
                    updateResources($pID, 'A', $cAz);
                    updateResources($pID, 'P', $cPeople);
                }
                loseCheck($pID);
            break;
            case 'C':
                if($aFlag != 1) {
                    $cCath += $pRate;
                    $cPeople--;
                    updateResources($pID, 'C', $cAz);
                    updateResources($pID, 'P', $cPeople);
                } else if ($aFlag == 1) {
                    $cCath -= ($pRate-1);
                    $cPeople++;
                    updateResources($pID, 'C', $cAz);
                    updateResources($pID, 'P', $cPeople);
                }
                loseCheck($pID);
            break;
        }
    }

    function calcTempleCost($which) {
        $azFlag = $this ->getGameStateValue("azFlag");
        $cathFlag = $this ->getGameStateValue("cathFlag");
        $tMax = $this ->getGameStateValue("templeMaxHeight");
        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $tCost = 0;

        switch ($which) {
            case 'A':
                if ($azFlag == false) {
                    $tCost = ($aLevel +1);
                } else if ($azFlag == true) {
                    $tCost = (($tMax - $aLevel)+1);
                }
                return $tCost;
            break;
            case 'C':
                if ($cathFlag == false) {
                    $tCost = ($cLevel +1);
                } else if ($cathFlag == true) {
                    $tCost = (($tMax - $cLevel)+1);
                }
                return $tCost;
            break;
        }
    }

    function updateTemple($which, $player_id) {
        $azFlag = $this ->getGameStateValue("azFlag");
        $cathFlag = $this ->getGameStateValue("cathFlag");
        $tMax = $this ->getGameStateValue("templeMaxHeight");
        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $aflag = $this ->getGameStateValue("apocFlag");
        $pID = $player_id;

        switch ($which) {
            case 'A':
                if($azFlag == false) {
                    if ($aLevel < ($tMax-1)) {
                        $aLevel++;
                        $this->setGameStateValue( "azLevel", $aLevel );
                    } else if ($aLevel = ($tMax-1)) {
                        $aLevel++;
                        $this->setGameStateValue( "azLevel", $aLevel );
                        $this->setGameStateValue( "azFlag", true );
                        if ($aFlag == 0) {
                            $this->setGameStateValue( "apocFlag", 1 );
                        }
                    }
                }else if ($azFlag == true) {
                    if ($aLevel >1) {
                        $aLevel--;
                        $this->setGameStateValue( "azLevel", $aLevel );
                    } else if ($aLevel == 1) {
                        $aLevel--;
                        $this->setGameStateValue( "azLevel", $aLevel );
                        winCheck($pID);
                    }
                }
            break;
            case 'C':
                if($cathFlag == false) {
                    if ($cLevel < ($tMax-1)) {
                        $cLevel++;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                    } else if ($cLevel = ($tMax-1)) {
                        $cLevel++;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                        $this->setGameStateValue( "cathFlag", true );
                        if ($aFlag == 0) {
                            $this->setGameStateValue( "apocFlag", 2 );
                        }
                    }
                }else if ($cathFlag == true) {
                    if ($cLevel >1) {
                        $cLevel--;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                    } else if ($cLevel == 1) {
                        $cLevel--;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                        winCheck($pID);
                    }
                }
            break;
        }
    }

    function getCard($player_id) {
        $pID = $player_id;
        $heldCardInfo = array();
        $heldCardType = "";

        $this->cards->pickCardForLocation('deck', 'held', $pID);

        $heldCardInfo = $this->cards->getCardsInLocation('held', $pID);
        $heldCardType = $heldCardInfo["card_type"];

        return $heldCardID;

    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in flowerwar.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    function queryBoard() {

    }
    
    function pickSpace() {

    }
    
    function updateSpace() {

    }
    
    function playersInSpace() {

    }
    
    function drawCardState() {

    }
    
    function resolveCard() {

    }
    
    function ResourceLoop() {

    }

    function endTurn() {

    }
    
    function loseCheck($player_id) {

    }

    function winCheck() {

    }
    
    function gameWon() {

    }
    

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
