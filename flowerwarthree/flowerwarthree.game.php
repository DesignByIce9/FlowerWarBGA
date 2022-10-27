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
            "blockerSpace" => 29,
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
        
        $sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`) VALUES ";
        $tID = 0;
        $startQuad = 0;
        $startBoard = 0;
        foreach( $players as $player_id => $player ) {
            $startQuad = ($tID+1);
            $startBoard = (($startQuad-1)*5);
            $playerValues[] = "('".$player_id."','".$tID."','".$startBoard."','".$startQuad."',1,2,2,8,1,0)";
            $tID++;
        }
        $sql .= implode( $playerValues, ',' );
        self::DbQuery( $sql );

        $sql = "";
        $values = array();
        
        // adding blockers
        $blockerRoll = bga_rand(1,6);
        $bBoard = 0;
        switch ($blockerRoll) {
            case 6:
                $this->setGameStateValue("blockerSpace", 0);
				$sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (4+$i);
                    $bBoard = (0);
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."',0,0,0,0,0,0)";
                }
                $sql .= implode( $values, ',' );
                self::DbQuery( $sql );
            break;
            default:
            $this->setGameStateValue("blockerSpace", $blockerRoll);
                $sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (4+$i);
                    $bBoard = (((($i)-1)*5)+(($blockerRoll)-1));
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."','".$blockerRoll."',0,0,0,0,0)";
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
        self::setGameStateInitialValue( 'turnCount', 0 );
        self::setGameStateInitialValue( 'roundCount', 0 );
        self::setGameStateInitialValue( 'numPlayers', count($players) );

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
        $this->cards->createCards( $terrain, 'terrain' );
        
        // Event Cards
        $events = array();
        foreach( $this->eventName as $eName => $eValue) {
            $events[] = array( 'type' => $eName, 'type_arg' => $eValue, 'nbr' => 2);
        }
        $this->cards->createCards( $events, 'deck' );

        for($x=0;$x<20;$x++) {
            $this->cards->pickCardForLocation("terrain", "board", $x);
        }


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
            $cardsInHand[] = $this->cards->getCardsInLocation("hand",$player_id);

            $cAz = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cCath = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cPeople = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cTime = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );
            $cCharID = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $player_id ORDER BY `recordID` DESC LIMIT 0,1" );

            $resourceArray[] = array($cAz, $cCath, $cPeople, $cTime, $cCharID);
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
        $sql = "INSERT INTO `resources` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`, `Az`, `Cath`,`People`,`Time`,`charID`) VALUES ";
    
        switch($rID) {
            case 'A':
                $cAz = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;
            case 'C':
                $cCath = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;
            case 'P':
                $cPeople = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;
            case 'T':
                $cTime = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;
            case 'H':
                $ccharID = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;
            case 'Q':
                $cQuad = $rValue;
                $cSpace = 1;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;
            case 'B':
                $cBoard = $rValue;
                $cQuad = ceil(($cBoard+1)/5);
                $cSpace = (($cBoard+1)-(($cQuad-1)*5));
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;
            case 'H':
                $ccharID = $rValue;
                $values = array("( '".$pID."', '".$cToken."', '".$cBoard."', '".$cQuad."', '".$cSpace."', '".$cAz."', '".$cCath."', '".$cPeople."', '".$cTime."', '".$ccharID."' )");
            break;            
            }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );  
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
        switch ($type) {
            case 'A':
                $resource = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
            break;
            case 'C':
                $resource = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        }
        if ($resource==0) {
            throw new BgaUserException ( self::_("You don't have enough Faith to do that"));
        }
        $cQuad = self::getUniqueValueFromDB( "SELECT `Quad` FROM `resources` WHERE `player_id` = $pID ORDER BY `recordID` DESC LIMIT 0,1" );
        if( $cQuad < 4 ) {
        $cQuad++;
        } else if ($cQuad < 4) {
        $cQuad = 1;
        }
        $resource--;
        switch ($type) {
            case 'A':
                $this->updateResources($pID, 'A', $resource);
            break;
            case 'C':
                $this->updateResources($pID, 'C', $resource);
            break;
        }
        $this->updateResources($pID, 'Q', $cQuad);
        switch ($type) {
            case 'A':
                $this->gamestate->nextState("nextQuadA");
            break;
            case 'C':
                $this->gamestate->nextState("nextQuadC");
            break;
        }
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
        $this->gamestate->nextState("resetTime");
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
    
    function argsBoardState() {
        $pID = $this->getActivePlayerId();
        $boardState = array();
        $blocker = self::getGameStateValue('blockerSpace');
        $aFlag = false;
        $cFlag = false;
        $pFlag = false;     
        $availableMoves = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19);
        $removeSpace = array();

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

        if($boardState['Az'] >0) {
            $aFlag = true;
        }
        if($boardState['Cath'] >0) {
            $cFlag = true;
        }
        if($boardState['People'] >1) {
            $pFlag = true;
        }

        $boardState['blocker'] = $blocker;
        $boardState['aFlag'] = $aFlag;
        $boardState['cFlag'] = $cFlag;
        $boardState['pFlag'] = $pFlag;

        if($boardState['Time'] < 4) {
            $testSpace = $boardState['boardID'];
            switch($boardState['Quad']) {
                case 1:
                    for($i=$testSpace;$i>=0;$i--) {
                        array_push($removeSpace, $testSpace);
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
                case 2:
                    for($i=$testSpace;$i>=5;$i--) {
                        array_push($removeSpace, $testSpace);                        
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
                case 3:
                    for($i=$testSpace;$i>=10;$i--) {
                        array_push($removeSpace, $testSpace);                        
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
                case 4:
                    for($i=$testSpace;$i>=15;$i--) {
                        array_push($removeSpace, $testSpace);                        
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
            }         
        } else if($boardState['Time'] = 4) {
            switch($boardState['Quad']) {
                case 1:
                    for($i=4;$i>=0;$i--) {
                        array_push($removeSpace, $i);                        
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
                case 2:
                    for($i=9;$i>=5;$i--) {
                        array_push($removeSpace, $i);                        
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
                case 3:
                    for($i=14;$i>=10;$i--) {
                        array_push($removeSpace, $i);                        
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
                case 4:
                    for($i=19;$i>=15;$i--) {
                        array_push($removeSpace, $i);                        
                    }
                    $availableMoves = array_diff($availableMoves, $removeSpace);
                    $availableMoves = array_values($availableMoves);
                break;
            }
        }
        
        $boardState['possibleMoves'] = $availableMoves;

        return array(
            'boardState' => $boardState
        );
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
        
    }

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
