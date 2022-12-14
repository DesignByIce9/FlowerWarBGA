<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * FlowerWarThree implementation : © <Your name here> <Your email address here>
  *
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  *
  * flowerwarthree.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class FlowerWarThree extends Table
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
            "cardChoiceFaith" => 29,
            "cardChoiceTemple" => 30,
            "blockerSpace" => 31,
            "cardTestProgress" => 32,
            "advanceCount" => 33,
    ) );

    // setting up Deck
        $this->cards = self::getNew( "module.common.deck" );
        $this->cards ->init( "card" );
        $this->cards->autoreshuffle = true;
	}

    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "flowerwarthree";
    }

    /*
        setupNewGame:

        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {
        self::setGameStateInitialValue( 'turnCount', 0 );
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

        // *****************************
        // Populate non-state tables

        // DB Setup

        $sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`, `turn`) VALUES ";
        $tID = 0;
        $startQuad = 0;
        $startBoard = 0;
        $turn = $this -> getGameStateValue("turnCount");
        foreach( $players as $player_id => $player ) {
            $startQuad = ($tID+1);
            $startBoard = (($startQuad-1)*5);
            $playerValues[] = "('".$player_id."','".$tID."','".$startBoard."','".$startQuad."',1,2,2,8,1,0, '".$turn."')";
            $tID++;
        }
        $sql .= implode( $playerValues, ',' );
        self::DbQuery( $sql );

        $sql = "";
        $values = array();

        // adding blockers
        $blockerRoll = bga_rand(1,6);
        $bBoard = 0;
        $turn = $this -> getGameStateValue("turnCount");
        switch ($blockerRoll) {
            case 6:
                $this->setGameStateValue("blockerSpace", 0);
				$sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`, `turn`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (4+$i);
                    $bBoard = (0);
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."',0,0,0,0,0,0,'".$turn."')";
                }
                $sql .= implode( $values, ',' );
                self::DbQuery( $sql );
            break;
            default:
            $this->setGameStateValue("blockerSpace", $blockerRoll);
                $sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`, `turn`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (4+$i);
                    $bBoard = (((($i)-1)*5)+(($blockerRoll)-1));
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."','".$blockerRoll."',0,0,0,0,0,'".$turn."')";
                }
                $sql .= implode( $values, ',' );
                self::DbQuery( $sql );
        }


        /************ Start the game initialization *****/

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
        self::setGameStateInitialValue( 'roundCount', 0 );
        self::setGameStateInitialValue( 'numPlayers', count($players) );
        self::setGameStateInitialValue( 'cardChoiceFaith', "" );
        self::setGameStateInitialValue( 'cardChoiceTemple', "" );
        self::setGameStateInitialValue( 'cardTestProgress', 0 );
        self::setGameStateInitialValue( 'advanceCount', 0 );

        // setting up state tables

        // Global state table
        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $apocflag = $this ->getGameStateValue("apocFlag");
        $aflag = $this ->getGameStateValue("azFlag");
        $cflag = $this ->getGameStateValue("cathFlag");
        $cBlock = $this ->getGameStateValue("blockerSpace");

        // Dealing Cards
        $nPlayers = self::getGameStateValue('numPlayers');

        // Terrain Cards
        $terrain = array();
        foreach( $this->terrainName as $tName => $tValue) {
            $terrain[] = array( 'type' => $tName, 'type_arg' => $tValue, 'nbr' => 4);
        }
        $this->cards->createCards( $terrain, 'board' );
        $this->cards->shuffle( 'board' );

        // Event Cards
        $events = array();
        foreach( $this->eventName as $eName => $eValue) {
            $events[] = array( 'type' => $eName, 'type_arg' => $eValue, 'nbr' => 2);
        }
        $this->cards->createCards( $events, 'deck' );
        $this->cards->shuffle( 'deck' );


        // TODO: setup the initial game situation here


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

        $blocker = self::getGameStateValue('blockerSpace');
        $cardsInHand = array();
        $tokenArray = array();
        $resourceArray = array();

        $players = $this->loadPlayersBasicInfos();
        foreach( $players as $player_id => $player ) {
            $cBoard = self::getUniqueValueFromDB( "SELECT `boardID` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );

            $tokenArray[] = array($player_id, $cBoard);
            //$cardsInHand[] = $this->cards->getCardsInLocation("hand",$player_id);

            $cAz = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cCath = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cPeople = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cTime = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cCharID = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );

            $resourceArray[] = array($player_id, $cAz, $cCath, $cPeople, $cTime, $cCharID);
        }

        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $apocflag = $this ->getGameStateValue("apocFlag");
        $aflag = $this ->getGameStateValue("azFlag");
        $cflag = $this ->getGameStateValue("cathFlag");

        $result['tokens'] = $tokenArray;
        $result['cards'] = $cardsInHand;
        $result['resources'] = $resourceArray;
        $result['blocker'] = $blocker;
        $result['azTemple'] = $aLevel;
        $result['cathTemple'] = $cLevel;
        $result['apocFlag'] = $apocflag;
        $result['azFlag'] = $aflag;
        $result['cathFlag'] = $cflag;
        $result['board'] = $this->board;

        for($i=0;$i<20;$i++) {
            $bTerrain = $this->cards->getCardsInLocation('board', $i);
            $terrainArray[$i] = array_values($bTerrain)[0]['type'];
        }

        $result['terrain'] = $terrainArray;


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

        $tMax = $this ->getGameStateValue("templeMaxHeight");
        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $aflag = $this ->getGameStateValue("apocFlag");
        $spacesRemaining = (($tMax*2)-1);
        $progress = 0;

        if($aflag != 0) {
            if($aLevel<=$cLevel) {
                $progress = ceil(((($spacesRemaining-$aLevel)/$spacesRemaining)*100));
            } else if ($aLevel>$cLevel) {
                $progress = ceil(((($spacesRemaining-$cLevel)/$spacesRemaining)*100));
            }
        } else if ($aflag == 0) {
            if ($aLevel<=$cLevel) {
                $progress = ceil(($aLevel/$spacesRemaining)*100);
            } else {
                $progress = ceil(($cLevel/$spacesRemaining)*100);
            }

        }

        return $progress;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////

    /*
    function alwaysFirst() {
        $this->createNextPlayerTable([2373342, 2373343], true);
        $this->gamestate->changeActivePlayer(2373342);
        $this->gamestate->nextState("moveToken");
    }
    */

    function cardChoice($cardChoice, $type) {
        $pID = $this->getActivePlayerId();
        $cChoice = $cardChoice;

        $this->setGameStateValue("cardChoiceFaith", "");
        $this->setGameStateValue("cardChoiceTemple", "");

        switch($type) {
            case "F":
                $this->setGameStateValue("cardChoiceFaith", $cChoice);
                $this->notifyPlayer($pID, 'playerLog', clienttranslate('You chose ${cChoice}'), array('cChoice' =>$cChoice));
            break;
            case "T":
                $this->setGameStateValue("cardChoiceTemple", $cChoice);
                $this->notifyPlayer($pID, 'playerLog', clienttranslate('You chose ${cChoice}'), array('cChoice' =>$cChoice));
            break;
        }

    }

    function possibleMoves($playerID, $currentBoard, $currentQuad, $currentTime) {
        $pID = $playerID;
        $bID = $currentBoard;
        $bQuad = $this->board[$bID]['Quad'];
        $cQuad = $currentQuad;
        $testSpace = 0;
        $time = $currentTime;
        $blocker = $this->getGameStateValue('blockerSpace');
        $aCount = $this->getGameStateValue('advanceCount');
        $availableMoves = array();

        $testBoard = $this->resourceQuery($pID, "B"); // test BoardID vs db
        if ($bID != $testBoard) {
            throw new BgaUserException ( self::_("Player BoardID does not match given BoardID"));
        }
        $testTime = $this->resourceQuery($pID, "T"); // test Time vs db
        if ($time != $testTime) {
            throw new BgaUserException ( self::_("Player Time does not match given Time"));
        }
        $testQuad = $this->resourceQuery($pID, "Q"); // test current Quad vs db
        if ($cQuad != $testQuad) {
            throw new BgaUserException ( self::_("Player Quad does not match given Quad"));
        }

        $testQuad = 0; // reset testQuad for advanced quad test

        if ($bQuad != $cQuad) { // test for advanced Quad
            $aTest = $aCount; 
            $aQuad = $cQuad;
            while ($aCount >0) {
                $aQuad --;
                if ($aQuad == 0) {
                    $aQuad = 4;
                }
                $aTest--;
            }
            if ($aQuad != $bQuad) {
                throw new BgaUserException ( self::_("Quad Test Error"));
            }
        } else if ($time >=4) { // if Time is >=4, move to the next Quad
            $testQuad = $cQuad +1;
            $testSpace = (($cQuad-1)*5); // start at the first Space
        } else if ($time <4) { // if Time is <4, stay in the same quad
            $testQuad = $cQuad;
            $testSpace = $bID+1; // But move to the next space.
        }
        switch ($testQuad) { // test to see which space is blocked
            case 1:
                $blocker = $blocker-1;
            break;
            case 2:
                $blocker = $blocker+4;
            break;
            case 3:
                $blocker = $blocker+9;
            break;
            case 4:
                $blocker = $blocker+14;
            break;
        }

        $lastSpace = (($testQuad*5)-1); // Set the last available space in the quad

        for ($i=$testSpace;$i<=$lastSpace;$i++) { // Increment through ever space
            if ($i != $blocker) { // If the space isn't blocked
                array_push($availableMoves, ($i)); // push it to the array
            }
        }
        
        return $availableMoves;
    }

    function boardQuery($bID) {
        $boardID = $bID;
        $bAz = 0;
        $bCath = 0;
        $bPeople = 0;
        $terrainArray = array();
        $bTerrain = "";
        $boardArray = array();

        $bAz = $this->board[$boardID]["Az"];
        $bCath = $this->board[$boardID]["Cath"];
        $bPeople = $this->board[$boardID]["People"];

        $terrainArray = $this->cards->getCardsInLocation('board', $boardID);
        $bTerrain = array_values($terrainArray)[0]['type'];

        $boardArray = array ($bAz, $bCath, $bPeople, $bTerrain);

        return $boardArray;
    }

    function cardConvert($player_id, $fromWhich) {
        $pID = $player_id;
        $cAz = $this->resourceQuery($pID, 'A');
        $cCath = $this->resourceQuery($pID, 'C');
        $cPeople = $this->resourceQuery($pID, 'P');
        $pRate = $this ->getGameStateValue("peopleConversionRate");
        $apocFlag = $this ->getGameStateValue("apocFlag");

        switch ($fromWhich) {
            case 'A':
                if($apocFlag != 2) {
                    $cAz += $pRate;
                    $cPeople--;
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has been forced to convert People to Aztec faith' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    $this-> updateResources($pID, 'A', $cAz);
                    $this-> updateResources($pID, 'P', $cPeople);
                } else if($apocFlag == 2) {
                    $cAz += ($pRate -1);
                    $cPeople--;
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has been forced to convert People to Aztec faith' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    $this-> updateResources($pID, 'A', $cAz);
                    $this-> updateResources($pID, 'P', $cPeople);
                    $this-> loseCheck($pID);
                }
            break;
            case 'C':
                if($apocFlag != 1) {
                    $cCath += $pRate;
                    $cPeople--;
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has been forced to convert People to Catholic faith' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    $this-> updateResources($pID, 'C', $cCath);
                    $this-> updateResources($pID, 'P', $cPeople);
                } else if($apocFlag == 1) {
                    $cCath += ($pRate -1);
                    $cPeople--;
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has been forced to convert People to Catholic faith' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    $this-> updateResources($pID, 'C', $cCath);
                    $this-> updateResources($pID, 'P', $cPeople);
                    $this-> loseCheck($pID);
                }
            break;
        }

    }

    function resourceQuery($player_id, $resource) {
        $pID = $player_id;
        $rID = $resource;
        $value = 0;

        switch($rID) {
            case 'A':
                $value = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'C':
                $value = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'P':
                $value = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'T':
                $value = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'H':
                $value = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'B':
                $value = self::getUniqueValueFromDB( "SELECT `boardID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'Q':
                $value = self::getUniqueValueFromDB( "SELECT `Quad` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'S':
                $value = self::getUniqueValueFromDB( "SELECT `Space` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;

        }
        return $value;
    }

    function updateResources($player_ID, $resource, $new_value) {
        $pID = $player_ID;
        $rID = $resource;
        $rValue = $new_value;
        $values = array();
        $cToken = self::getUniqueValueFromDB( "SELECT `tokenID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cBoard = self::getUniqueValueFromDB( "SELECT `boardID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cQuad = self::getUniqueValueFromDB( "SELECT `Quad` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cSpace = self::getUniqueValueFromDB( "SELECT `Space` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cAz = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cCath= self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cPeople = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cTime = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $ccharID = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $turn = $this ->getGameStateValue("turnCount");
        $sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`, `turn`) VALUES ";

        switch($rID) {
            case 'A':
                $cAz = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            case 'C':
                $cCath = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            case 'P':
                $cPeople = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            case 'T':
                $cTime = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            case 'H':
                $ccharID = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            case 'Q':
                $cQuad = $rValue;
                $cSpace = 1;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            case 'B':
                $cBoard = $rValue;
                $cQuad = ceil(($cBoard+1)/5);
                $cSpace = (($cBoard+1)-(($cQuad-1)*5));
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            case 'H':
                $ccharID = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."', '".$turn."')");
            break;
            }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
    }

    function checkTemple($which) {
        $pID = $this->getActivePlayerId();
        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $apocFlag = $this ->getGameStateValue("apocFlag");
        $aFlag = $this ->getGameStateValue("azFlag");
        $cFlag = $this ->getGameStateValue("cathFlag");
        $maxHeight = $this ->getGameStateValue("templeMaxHeight");

        switch ($which) {
            case 'A':
                if($aFlag == false) {
                    if($aLevel == $maxHeight) {
                        $this->setGameStateValue("azFlag", true);
                        if($apocFlag == 0) {
                            $this->setGameStateValue("apocFlag", 1);
                        }
                    }
                } else if ($aFlag == true) {
                    if($aLevel == 0) {
                        $this->winCheck($pID);
                    }
                }
            break;
            case 'C':
                if($cFlag == false) {
                    if($cLevel == $maxHeight) {
                        $this->setGameStateValue("cathFlag", true);
                        if($apocFlag == 0) {
                            $this->setGameStateValue("apocFlag", 2);
                        }
                    }
                } else if ($aFlag == true) {
                    if($cLevel == 0) {
                        $this->winCheck($pID);
                    }
                }
            break;
        }
    }

    function moveTemple($which, $dir, $card) {
        $pID = $this->getActivePlayerId();
        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $apocFlag = $this ->getGameStateValue("apocFlag");
        $aFlag = $this ->getGameStateValue("azFlag");
        $cFlag = $this ->getGameStateValue("cathFlag");
        $maxLevel = $this ->getGameStateValue("templeMaxHeight");
        $override = $card;

        switch($which) {
            case 'A':
                if($dir == 'D') {
                    if($aLevel >0) {
                        if ($aFlag || $override) {
                            $aLevel--;
                            $this->setGameStateValue("azLevel", $aLevel);
                            $this->checkTemple('A');
                            self::notifyAllPlayers( "message", clienttranslate( '${player_name} has moved Shield Flower down a level' ),
                            array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        }
                    } else {
                        throw new BgaUserException ( self::_("You can't move that figure down"));
                    }
                } else if($dir == 'U') {
                    if($aLevel < $maxLevel) {
                        $aLevel++;
                        $this->setGameStateValue("azLevel", $aLevel);
                        $this->checkTemple('A');
                        self::notifyAllPlayers( "message", clienttranslate( '${player_name} has moved Shield Flower up a level' ),
                        array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                    ) );
                    } else {
                        throw new BgaUserException ( self::_("You can't move that figure up"));
                    }
                }
            break;
            case 'C':
                if($dir == 'D') {
                    if($cLevel >0) {
                        if ($cFlag || $override) {
                            $cLevel--;
                            $this->setGameStateValue("cathFlag", $cLevel);
                            $this->checkTemple('C');
                            self::notifyAllPlayers( "message", clienttranslate( '${player_name} has moved Juan Diego down a level' ),
                            array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        }
                    } else {
                        throw new BgaUserException ( self::_("You can't move that figure down"));
                    }
                } else if($dir == 'U') {
                    if($cLevel < $maxLevel) {
                        $cLevel++;
                        $this->setGameStateValue("cathFlag", $cLevel);
                        $this->checkTemple('C');
                        self::notifyAllPlayers( "message", clienttranslate( '${player_name} has moved Juan Diego up a level' ),
                        array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                    ) );
                    } else {
                        throw new BgaUserException ( self::_("You can't move that figure up"));
                    }
                }
            break;
        }
    }

    function loseCheck($player_id) {
        $pID = $player_id;
        $cPeople = resourceQuery($pID, "P");
        if($cPeople <=0) {
            self::eliminatePlayer( $pID );
        }
    }

    function winCheck($player_id) {
        $pID = $player_id;
        $aLevel = $this ->getGameStateValue("azLevel");
        $cLevel = $this ->getGameStateValue("cathLevel");
        $aFlag = $this ->getGameStateValue("azFlag");
        $cFlag = $this ->getGameStateValue("cathFlag");
        $apocFlag = $this ->getGameStateValue("apocFlag");
        $cPeople = 0;

        $levelCheck = false;
        $peopleCheck = false;
        $apocCheck = false;
        $templeCheck = false;

        if(($aLevel == 0) || ($cLevel == 0)) {
            $levelCheck = true;
        }

        $cPeople = $this->resourceQuery($pID, 'P');
        if ($cPeople >0) {
            $peopleCheck = true;
        }

        if($apocFlag != 0) {
            $apocCheck = true;
        }

        if(($aFlag == true) || ($cFlag == true)) {
            $templeCheck = true;
        }

        $finalCheck = array($levelCheck, $peopleCheck, $apocCheck, $templeCheck);

        if (!in_array(false, $finalCheck)) {
            $this->gamestate->nextState("winState");
        }
    }




//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
////////////

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in flowerwarthree.action.php)
    */

    function quadUpdate($type) {
        $pID = $this->getActivePlayerId();
        $cQuad = $this->resourceQuery($pID, "Q");
        $resource = $this->resourceQuery($pID, $type);
        $aCount = $this->getGameStateValue("advanceCount");
        $rText = "";
        // check if action is valid
        if ($resource <1) {
            throw new BgaUserException( self::_("You don't have enough Faith to perform this action") );
        }
        if ($aCount>=3) {
            throw new BgaUserException( self::_("Can't advance more than three quadrants") );
        } else if ($this->checkAction('nextQuadA', false) || $this->checkAction('nextQuadC', false)) {
            // check and update quad value
            if ($cQuad < 4) {
                $cQuad++;
            } else if ($cQuad >= 4) {
                $cQuad = 1;
            }
            // update resource
            $resource--;
            $this->updateResources($pID, $type, $resource);
            $this->updateResources($pID, "Q", $cQuad);
            
            // send notification
            if ($type == 'A') {
                $rText = "Aztec";
            } else if ($type == 'C') {
                $rText = "Catholic";
            }

            $this->notifyAllPlayers("otherUpdateQuad", clienttranslate('${player_name} has spent 1 ${rText} Faith to move to the next quadrant'), [
                'player_id' => $pID,
                'player_name' => $this->getActivePlayerName(),
                'rText' => $rText,
            ]);
            $this->notifyPlayer($pID, "selfUpdateQuad", clienttranslate('You have spent 1 ${rText} Faith to move to the next quadrant'), [
                'rText' => $rText,
            ]);
        }
        // update advance count
        $aCount++;
        $this->setGameStateValue("advanceCount", $aCount);
    }

    function updateTime() {
        $pID = $this->getActivePlayerId();
        $cPeople = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        if ($cPeople<2) {
            throw new BgaUserException ( self::_("You don't have enough People to do that"));
        }
        $Time = 1;
        $this->updateResources($pID, 'P', $cPeople);
        $this->updateResources($pID, 'T', $Time);
        $this->notifyAllPlayers("otherResetTime", clienttranslate('${player_name} has spent 1 People to reset their Time'), [
            'player_id' => $pID,
            'player_name' => $this->getActivePlayerName()
        ]);

        $this->notifyPlayer($pID, "selfResetTime", clienttranslate('You have spent 1 People to reset your Time'), [
        ]);
        $this->gamestate->nextState("resetTime");
    }

    function clickedSpace($bID) {
        $pID = $this->getActivePlayerId(); // set player
        $boardstring = $bID; // get clicked space ID
        $selectedSpace = str_replace("space_", "", $boardstring); // translate clicked ID to space #
        $oldSpace = $this->resourceQuery($pID, 'B'); // get old boardID from DB
        $oldQuad = $this->board[$oldSpace]['Quad']; // get old boardID Quad from Materials
        $aCount = $this->getGameStateValue("advanceCount"); // get advanceCount
        $advancedQuad = $this->resourceQuery($pID, 'Q'); // get current Quad from DB
        $aTest = $aCount; // set up quad test
        $testQuad = $advancedQuad; 
        $selectedQuad = $this->board[$selectedSpace]['Quad']; // get clicked boardID Quad
        
        $this->checkAction("boardUpdate"); // check if action is valid
        
        while ($aTest>0) { // count backwards based on # of times advanceQuad buttons were clicked
            $testQuad--; // decrement test quad
            if($testQuad == 0) { // Wrap quad #
                $testQuad = 4;
            }
            $aTest--;
        }

        if ($testQuad != $oldQuad) { // Throw exception if test and old quad don't match
            throw new BgaSystemException ( "Test and Old Quads are not the same");
        } else if($advancedQuad != $selectedQuad) { // throw exception if current and clicked quad don't match
            throw new BgaSystemException ( "Selected and Advanced Quads are not the same");
        } else { // if everything is good
            $this->updateResources($pID, "B", $selectedSpace); // update BoardID in DB
        }

        self::notifyAllPlayers("moveTokenOther", clienttranslate( '${player_name} has moved to space ${board_ID}' ),
            array(
                'player_name' => self::getActivePlayerName(),
                'board_ID' => $selectedSpace,
            ) );
        self::notifyPlayer($pID,"moveTokenSelf", clienttranslate( 'You have moved to space ${board_ID}' ),
        array(
            'player_id' => $pID,
            'board_ID' => $selectedSpace,
        ) );

        $this->gamestate->nextState("boardUpdate"); // move to next state 
    }

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

    function getActivePlayerColor() {
        $player_id = self::getActivePlayerId();
        $players = self::loadPlayersBasicInfos();
        if (isset($players[$player_id]))
            return $players[$player_id]['player_color'];
        else
            return null;
    }

    function argsBoardState() {
        $pID = $this->getActivePlayerId();
        $boardState = array();
        $blocker = self::getGameStateValue('blockerSpace');
        $aButtonFlag = false;
        $cButtonFlag = false;
        $pButtonFlag = false;

        $boardState['tokenID'] = self::getUniqueValueFromDB( "SELECT `tokenID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['boardID'] = self::getUniqueValueFromDB( "SELECT `boardID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Quad'] = self::getUniqueValueFromDB( "SELECT `Quad` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Space'] = self::getUniqueValueFromDB( "SELECT `Space` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Az'] = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Cath'] = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['People'] = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Time'] = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['charID'] = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $color = $this->getActivePlayerColor();
        $bID = $boardState['boardID'];
        $cQuad = $boardState['Quad'];
        $cTime = $boardState['Time'];

        $boardState['availableMoves'] = $this->possibleMoves($pID, $bID, $cQuad, $cTime);

        if($boardState['Az'] >0) {
            $aButtonFlag = true;
        }
        if($boardState['Cath'] >0) {
            $cButtonFlag = true;
        }
        if($boardState['People'] >1) {
            $pButtonFlag = true;
        }

        $boardState['blocker'] = $blocker;
        $boardState['aButtonFlag'] = $aButtonFlag;
        $boardState['cButtonFlag'] = $cButtonFlag;
        $boardState['pButtonFlag'] = $pButtonFlag;
        $boardState['pColor'] = $color;
        $boardState['turn'] = $this ->getGameStateValue("turnCount");
        $boardState['aCount'] = $this ->getGameStateValue("advanceCount");

        return array(
            'boardState' => $boardState
        );
    }

    function argsCardState() {
        $pID = $this->getActivePlayerId();
        $cardState = array();
        $cardState['playerID'] = $pID;
        $cardState['Az'] = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cardState['Cath'] = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cardState['People'] = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cardState['Time'] = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cardState['charID'] = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $cardState['aLevel'] = $this ->getGameStateValue("azLevel");
        $cardState['cLevel'] = $this ->getGameStateValue("cathLevel");
        $cardState['apocflag'] = $this ->getGameStateValue("apocFlag");
        $cardState['aflag'] = $this ->getGameStateValue("azFlag");
        $cardState['cflag'] = $this ->getGameStateValue("cathFlag");
        $cardState['maxHeight'] = $this ->getGameStateValue("templeMaxHeight");
        $cardState['faithChoiceFlag'] = false;
        $cardState['templeChoiceFlag'] = false;
        $cardState['moveAU'] = false;
        $cardState['moveAD'] = false;
        $cardState['moveCU'] = false;
        $cardState['moveCD'] = false;
        $cardState['turn'] = $this ->getGameStateValue("turnCount");

        // get event card type
        $currentCard = array();
        $currentCard = $this->cards->getCardsInLocation('held', $pID);
        $currentCardType = array_values($currentCard)[0]["type"];
        $cardState['cardType'] = $currentCardType;

        $cardChoiceFaith = array("gPenalty","gCheck","catchUp","gBonus");
        $cardChoiceTemple = array("uFigure","dFigure");

        if((in_array($cardState['cardType'], $cardChoiceFaith)) && ($cardState['Az']==$cardState['Cath'])) {
            $cardState['faithChoiceFlag'] = true;
        }
        if(in_array($cardState['cardType'], $cardChoiceTemple)) {
            $cardState['templeChoiceFlag'] = true;
        }

        if ($cardState['aLevel'] > 0) {
            $cardState['moveAD'] = true;
        }
        if ($cardState['aLevel'] < $cardState['maxHeight']) {
            $cardState['moveAU'] = true;
        }
        if ($cardState['cLevel'] > 0) {
            $cardState['moveCD'] = true;
        }
        if ($cardState['cLevel'] < $cardState['maxHeight']) {
            $cardState['moveCU'] = true;
        }

        return array(
            'cardState' => $cardState
        );

    }

    function argPlayerState() {
        $pID = $this->getActivePlayerId();
        $boardState = array();
        $blocker = self::getGameStateValue('blockerSpace');
        $aButtonFlag = false;
        $cButtonFlag = false;
        $pButtonFlag = false;

        $boardState['playerID'] = $pID;
        $boardState['tokenID'] = self::getUniqueValueFromDB( "SELECT `tokenID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['boardID'] = self::getUniqueValueFromDB( "SELECT `boardID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Quad'] = self::getUniqueValueFromDB( "SELECT `Quad` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Space'] = self::getUniqueValueFromDB( "SELECT `Space` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Az'] = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Cath'] = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['People'] = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['Time'] = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $boardState['charID'] = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        $color = $this->getActivePlayerColor();

        if($boardState['Az'] >0) {
            $aButtonFlag = true;
        }
        if($boardState['Cath'] >0) {
            $cButtonFlag = true;
        }
        if($boardState['People'] >1) {
            $pButtonFlag = true;
        }

        $boardState['blocker'] = $blocker;
        $boardState['aButtonFlag'] = $aButtonFlag;
        $boardState['cButtonFlag'] = $cButtonFlag;
        $boardState['pButtonFlag'] = $pButtonFlag;
        $boardState['pColor'] = $color;
        $boardState['turn'] = $this ->getGameStateValue("turnCount");

        return $boardState;
    }

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

    function stMoveToken() {
        $count = $this -> getGameStateValue("turnCount");
        $count++;
        $this -> setGameStateValue("turnCount", $count);
    }

    function stBoardUpdate() {
        $pID = $this->getActivePlayerId(); // get player ID
        $bID = $this->resourceQuery($pID, "B"); // get current player board location
        $pA = $this->resourceQuery($pID, "A"); // get current player resources
        $pC = $this->resourceQuery($pID, "C");
        $pP = $this->resourceQuery($pID, "P");
        $time = $this->resourceQuery($pID, "T");
        $bA = $this->board[$bID]['Az']; // get board resources
        $bC = $this->board[$bID]['Cath'];
        $bP = $this->board[$bID]['People'];
        $rText = "";

        $pA += $bA; // update player resources
        $pC += $bC;
        $pP += $bP;
        $time++;
        $this->updateResources($pID, 'A', $pA);
        $this->updateResources($pID, 'C', $pC);
        $this->updateResources($pID, 'P', $pP);
        $this->updateResources($pID, 'T', $time);        

        // Notification for Catholic Faith
        $rText = "Aztec";
        if ($bA > 0) {
            self::notifyAllPlayers("message", clienttranslate( '${player_name} has gained ${bA} ${rText} Faith' ),
            array(
                'player_name' => self::getActivePlayerName(),
                'bA' => $bA,
                'rText' => $rText,
            ) );
            self::notifyPlayer($pID,"message", clienttranslate( 'You have gained ${bA} ${rText} Faith' ),
            array(
                'player_id' => $pID,
                'bA' => $bA,
                'rText' => $rText,
            ) );
        } 

        // Notification for Catholic Faith
        $rText = "Catholic";
        if ($bC > 0) {
            self::notifyAllPlayers("message", clienttranslate( '${player_name} has gained ${bC} ${rText} Faith' ),
            array(
                'player_name' => self::getActivePlayerName(),
                'bC' => $bC,
                'rText' => $rText,
            ) );
            self::notifyPlayer($pID,"message", clienttranslate( 'You have gained ${bC} ${rText} Faith' ),
            array(
                'player_id' => $pID,
                'bC' => $bC,
                'rText' => $rText,
            ) );
        } 

        // Notification for People
        $rText = "People";
        if ($bP > 0) {
            self::notifyAllPlayers("message", clienttranslate( '${player_name} has gained ${bP} ${rText}' ),
            array(
                'player_name' => self::getActivePlayerName(),
                'bP' => $bP,
                'rText' => $rText,
            ) );
            self::notifyPlayer($pID,"message", clienttranslate( 'You have gained ${bP} ${rText}' ),
            array(
                'player_id' => $pID,
                'bP' => $bP,
                'rText' => $rText,
            ) );
        } else if ($bP < 0) {
            self::notifyAllPlayers("message", clienttranslate( '${player_name} has lost ${bP} ${rText}' ),
            array(
                'player_name' => self::getActivePlayerName(),
                'bP' => $bP,
                'rText' => $rText,
            ) );
            self::notifyPlayer($pID,"message", clienttranslate( 'You have lost ${bP} ${rText}' ),
            array(
                'player_id' => $pID,
                'bP' => $bP,
                'rText' => $rText,
            ) );
        }

        $this -> setGameStateValue("advanceCount", 0); // reset advanceCount
        $this->cards->pickCardForLocation( "deck", "held", $pID ); // draw a card
        $this->gamestate->nextState("cardHandler"); // move to next state
    }

    function stCardHandler() {
        $pID = $this->getActivePlayerId();
        $cAz = $this->resourceQuery($pID, 'A');
        $cCath = $this->resourceQuery($pID, 'C');
        $cPeople = $this->resourceQuery($pID, 'P');
        $fThresh = $this ->getGameStateValue("faithThreshold");
        $pThresh = $this ->getGameStateValue("peopleThreshold");
        $fPen = $this ->getGameStateValue("faithPenalty");
        $pPen = $this ->getGameStateValue("PeoplePenalty");
        $fBon = $this ->getGameStateValue("faithBonus");
        $pBon = $this ->getGameStateValue("peopleBonus");
        $pRate = $this ->getGameStateValue("peopleConversionRate");

        $currentCard = array();
        $currentCardType = "";
        $currentCard = $this->cards->getCardsInLocation('held', $pID);
        $currentCardType = array_values($currentCard)[0]["type"];
        self::notifyAllPlayers( "otherDrawnCard", clienttranslate( '${player_name} has drawn the ${currentCardType} card' ),
        array(
            'player_id' => $pID,
            'player_name' => self::getActivePlayerName(),
            'currentCardType' => $currentCardType,
        ) );
        self::notifyPlayer($pID,"selfDrawnCard", clienttranslate( 'You have drawn the ${currentCardType} card' ),
        array(
            'player_id' => $pID,
            'currentCardType' => $currentCardType,
        ) );

        $highest = 0;
        $highestValue = 0;
        $cardChoiceF = $this->getGameStateValue("cardChoiceFaith");
        $cardChoiceT = $this->getGameStateValue("cardChoiceTemple");

        if($cAz == $cCath) {
            $highest = 0;
            $highestValue = $cAz;
        } else if($cAz > $cCath) {
            $highest = 1;
            $highestValue = $cAz;
        }else if($cAz < $cCath) {
            $highest = 2;
            $highestValue = $cCath;
        }

        switch($currentCardType) {
            case 'aPenalty':
                if($cAz >=$fPen)
                {
                    $cAz -= $fPen;
                    $this->updateResources($pID, 'A', $cAz);
                } else {
                    do {
                        $this->cardConvert($pID, 'A');
                        $cAz = $this->resourceQuery($pID, 'A');
                    } while($cAz <$fPen);
                    $cAz -= $fPen;
                    $this->updateResources($pID, 'A', $cAz);
                }
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} has lost ${fPen} Aztec faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'fPen' => $fPen,
                    ) );
                
            break;
            case 'cPenalty':
                if($cCath >=$fPen)
                {
                    $cCath -= $fPen;
                    $this->updateResources($pID, 'C', $cCath);
                } else {
                    do {
                        $this->cardConvert($pID, 'C');
                        $cCath = $this->resourceQuery($pID, 'C');
                    } while($cCath <$fPen);
                    $cCath -= $fPen;
                    $this->updateResources($pID, 'C', $cCath);
                }
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} has lost ${fPen} Catholic faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'fPen' => $fPen,
                    ) );
            break;
            case 'gPenalty':
                if (($highest == 1) || ($cardChoiceF == "Aztec")){
                    if($cAz >=$fPen)
                    {
                        $cAz -= $fPen;
                        $this->updateResources($pID, 'A', $cAz);
                    } else {
                        do {
                            $this->cardConvert($pID, 'A');
                            $cAz = $this->resourceQuery($pID, 'A');
                        } while($cAz <$fPen);
                        $cAz -= $fPen;
                        $this->updateResources($pID, 'A', $cAz);
                    }
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has lost ${fPen} Aztec faith' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                            'fPen' => $fPen,
                        ) );
                } else if(($highest == 2) || ($cardChoiceF == "Catholic")) {
                    if($cCath >=$fPen)
                    {
                        $cCath -= $fPen;
                        $this->updateResources($pID, 'C', $cCath);
                    } else {
                        do {
                            $this->cardConvert($pID, 'C');
                            $cCath = $this->resourceQuery($pID, 'C');
                        } while($cCath <$fPen);
                        $cCath -= $fPen;
                        $this->updateResources($pID, 'C', $cCath);
                    }
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has lost ${fPen} Catholic faith' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                            'fPen' => $fPen,
                        ) );
                }
            break;
            case 'pPenalty':
                $cPeople -= $pPen;
                $this->updateResources($pID, 'P', $cPeople);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} has lost ${pPen} People' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'pPen' => $pPen,
                    ) );
                $this-> loseCheck($pID);
            break;
            case 'aCheck':
                if($highest != 1) {
                    if($cAz >=$fPen)
                    {
                        $cAz -= $fPen;
                        $this->updateResources($pID, 'A', $cAz);
                    } else {
                        do {
                            $this->cardConvert($pID, 'A');
                            $cAz = $this->resourceQuery($pID, 'A');
                        } while($cAz <$fPen);
                        $cAz -= $fPen;
                        $this->updateResources($pID, 'A', $cAz);
                    }
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name}\'s Aztec faith is lower than their Catholic faith, and so has lost ${fPen} Aztec faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'fPen' => $fPen,
                    ) );
                } else {
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name}\'s Aztec faith is higher than their Catholic faith and so retains their Aztec believers' ),
                            array(
                                'player_id' => $pID,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                            ) );
                }
            break;
            case 'cCheck':
                if($highest != 1) {
                    if($cCath >=$fPen)
                    {
                        $cCath -= $fPen;
                        $this->updateResources($pID, 'C', $cCath);
                    } else {
                        do {
                            $this->cardConvert($pID, 'A');
                            $cCath = $this->resourceQuery($pID, 'C');
                        } while($cCath <$fPen);
                        $cCath -= $fPen;
                        $this->updateResources($pID, 'C', $cCath);
                    }
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name}\'s Catholic faith is lower than their Aztec faith, and so has lost ${fPen} Catholic faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'fPen' => $fPen,
                    ) );
                } else {
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name}\'s Catholic faith is higher than their Aztec faith and so retains their Catholic believers' ),
                            array(
                                'player_id' => $pID,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                            ) );
                }
            break;
            case 'gCheck':
                if ($highestValue < $fThresh) {
                    if (($highest == 1) || ($cardChoiceF == "Aztec")) {
                        if($cAz >=$fPen)
                        {
                            $cAz -= $fPen;
                            $this->updateResources($pID, 'A', $cAz);
                        } else {
                            do {
                                $this->cardConvert($pID, 'A');
                                $cAz = $this->resourceQuery($pID, 'A');
                            } while($cAz <$fPen);
                            $cAz -= $fPen;
                            $this->updateResources($pID, 'A', $cAz);
                        }
                        self::notifyAllPlayers( "message", clienttranslate( '${player_name} doesn\`t have both faiths above ${fThresh} faith and so loses ${fPen} faith from their highest faith' ),
                            array(
                                'player_id' => $pID,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                                'fPen' => $fPen,
                            ) );
                    } else if(($highest == 2) || ($cardChoiceF == "Catholic")) {
                        if($cCath >=$fPen)
                        {
                            $cCath -= $fPen;
                            $this->updateResources($pID, 'C', $cCath);
                        } else {
                            do {
                                $this->cardConvert($pID, 'C');
                                $cCath = $this->resourceQuery($pID, 'C');
                            } while($cCath <$fPen);
                            $cCath -= $fPen;
                            $this->updateResources($pID, 'C', $cCath);
                        }
                        self::notifyAllPlayers( "message", clienttranslate( '${player_name} doesn\`t have both faiths above ${fThresh} faith and so loses ${fPen} faith from their highest faith' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                            'fThresh' => $fThresh,
                            'fPen' => $fPen,
                        ) );
                    }
                } else {
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has both faiths above ${fThresh} faith and so retains all of their believers' ),
                            array(
                                'player_id' => $pID,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                            ) );
                }
            break;
            case 'pCheck':
                if ($cPeople < $pThresh) {
                    $cPeople -= $pPen;
                    $this->updateResources($pID, 'P', $cPeople);
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name}\'s People is below ${pThresh} and so has lost ${pPen} People' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                            'pPen' => $pPen,
                            'pThresh' => $pThresh
                        ) );
                    $this-> loseCheck($pID);
                }
            break;
            case 'aConvert':
                if($cAz >=$fPen)
                {
                    $cAz -= $fPen;
                    $this->updateResources($pID, 'A', $cAz);
                } else {
                    do {
                        $this->cardConvert($pID, 'A');
                        $cAz = $this->resourceQuery($pID, 'A');
                    } while($cAz <$fPen);
                    $cAz -= $fPen;
                    $this->updateResources($pID, 'A', $cAz);
                }
                $cCath += (2*$fPen);
                $gain = ($fPen*2);
                $this->updateResources($pID, 'C', $cCath);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} is forced to convert ${fPen} Aztec faith to ${gain} Catholic faith' ),
                            array(
                                'player_id' => $pID,
                                'player_name' => self::getActivePlayerName(),
                                'fPen' => $fPen,
                                'gain' => $gain
                            ) );
            break;
            case 'cConvert':
                if($cCath >=$fPen)
                {
                    $cCath -= $fPen;
                    $this->updateResources($pID, 'C', $cCath);
                } else {
                    do {
                        $this->cardConvert($pID, 'C');
                        $cCath = $this->resourceQuery($pID, 'C');
                    } while($cCath <$fPen);
                    $cCath -= $fPen;
                    $this->updateResources($pID, 'C', $cCath);
                }
                $cAz += (2*$fPen);
                $gain = ($fPen*2);
                $this->updateResources($pID, 'A', $cAz);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} is forced to convert ${fPen} Catholic faith to ${gain} Aztec faith' ),
                            array(
                                'player_id' => $pID,
                                'player_name' => self::getActivePlayerName(),
                                'fPen' => $fPen,
                                'gain' => $gain
                            ) );
            break;
            case 'aCull':
                $cPeople -=$pPen;
                $cAz += $pRate;
                $this->updateResources($pID, 'P', $cPeople);
                $this->updateResources($pID, 'A', $cAz);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} is forced to convert ${pPen} People faith to ${pRate) Aztec faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'pPen' => $pPen,
                        'pRate' => $pRate,
                    ) );
                    $this-> loseCheck($pID);
            break;
            case 'cCull':
                $cPeople -=$pPen;
                $cCath += $pRate;
                $this->updateResources($pID, 'P', $cPeople);
                $this->updateResources($pID, 'C', $cCath);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} is forced to convert ${pPen} People faith to ${pRate) Catholic faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'pPen' => $pPen,
                        'pRate' => $pRate,
                    ) );
                    $this-> loseCheck($pID);
            break;
            case 'catchUp':
                if($cPeople<$pThresh) {
                    $cPeople++;
                    $this->updateResources($pID, 'P', $cPeople);
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name}\'s tribe has grown! People up by 1' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                    ) );
                } else if(($highest == 1) || ($cardChoiceF == "Catholic")) {
                    $cCath = $cCath +2;
                    $this->updateResources($pID, 'C', $cCath);
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has gained 2 to their lowest Faith' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                } else if(($highest == 2) || ($cardChoiceF == "Aztec")) {
                    $cAz = $cCath +2;
                    $this->updateResources($pID, 'A', $cAz);
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has gained 2 to their lowest Faith' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                }
            break;
            case 'aBonus':
                $cAz += $fBon;
                $this->updateResources($pID, 'A', $cAz);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} has gained ${fBon} Aztec Faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'fBon' => $fBon,
                    ) );
            break;
            case 'cBonus':
                $cCath += $fBon;
                $this->updateResources($pID, 'C', $cCath);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} has gained ${fBon} Catholic Faith' ),
                    array(
                        'player_id' => $pID,
                        'player_name' => self::getActivePlayerName(),
                        'fBon' => $fBon,
                    ) );
            break;
            case 'gBonus':
                if(($highest == 1) || ($cardChoiceF == "Catholic")) {
                    $cCath += $fBon;
                    $this->updateResources($pID, 'C', $cCath);
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has gained ${fBon} Catholic Faith' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) );
                } else if(($highest == 2) || ($cardChoiceF == "Aztec")) {
                    $cAz += $fBon;
                    $this->updateResources($pID, 'A', $cAz);
                    self::notifyAllPlayers( "message", clienttranslate( '${player_name} has gained ${fBon} Aztec Faith' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) );
                }
            break;
            case 'pBonus':
                $cPeople += $pBon;
                $this->updateResources($pID, 'P', $cPeople);
                self::notifyAllPlayers( "message", clienttranslate( '${player_name} has gained ${pBon} People' ),
                        array(
                            'player_id' => $pID,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) );
            break;
            case 'uFigure':
                switch ($cardChoiceT) {
                    case "Aztec":
                        $this->moveTemple('A','U',true);
                    break;
                    case "Catholic":
                        $this->moveTemple('C', 'U', true);
                    break;
                }
            break;
            case 'dFigure':
                switch ($cardChoiceT) {
                    case "Aztec":
                        $this->moveTemple('A', 'D', true);
                    break;
                    case "Catholic":
                        $this->moveTemple('C', 'D', true);
                    break;
                }
            break;
            case 'iBlock':
                //placeholder
            break;
            case 'rBlock':
                //placeholder
            break;
            case 'nQuad':
                $cQuad = $this->resourceQuery($pID,'Q');
                $cQuad++;
                $this->updateResources($pID,'Q',$cQuad);
            break;
            case 'aSpace':
                //placeholder
            break;
            case 'rTime':
            break;
        }
        $this->cards->moveAllCardsInLocation('held','discard');
        $this->gamestate->nextState("resourceLoop");
        //$this->gamestate->nextState("cardTestEnd");
    }

    function stResourceLoop() {
        $pID = $this->getActivePlayerId();

        $this->gamestate->nextState("moveToken");
    }

    /* 
    function stCardTestStart() {
        $pID = $this->getActivePlayerId();
        $cardID = $this ->getGameStateValue("cardTestProgress");
        $cardTestType = $this->events[$cardID]["cardTest"];
        $cardArray = array();
        
        $cardArray = $this->cards->getCardsofType($cardTestType);
        $cardTypeID = array_values($cardArray)[0]["id"];
        $this->cards->moveCard($cardTypeID, "held", $pID);
        $cardTypeID = array_values($cardArray)[1]["id"];
        $this->cards->moveCard($cardTypeID, "discard");

        $this->gamestate->nextState("cardHandler");
    }

    function stCardTestEnd() {
        $cardID = $this ->getGameStateValue("cardTestProgress");
        $this->gamestate->nextState("resourceLoop");
        /*if ($cardID<24) {
            $cardID++;
            $this->setGameStateValue("cardTestProgress", $cardID);
            $this->gamestate->nextState("cardTestStart");
        } else {
            $this->gamestate->nextState("resourceLoop");
        }
    }
    */


    /*

    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...

        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }
    */

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
