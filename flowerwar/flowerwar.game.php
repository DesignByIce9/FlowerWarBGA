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
            $this->cards->autoreshuffle = true;
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

        $sql = "";
        $values = array();
        $blockerRoll = bga_rand(1,6);
        $turn = $this ->getGameStateValue("turnCount");
        $bBoard = 0;
        switch ($blockerRoll) {
            case 6:
                $this->setGameStateValue("blockerSpace", 0);
                $sql = "INSERT INTO `tokens` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`,`turn`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (4+$i);
                    $bBoard = (0);
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."',0,'".$turn."')";
                }
                $sql .= implode( $values, ',' );
                self::DbQuery( $sql );
            break;
            default:
            $this->setGameStateValue("blockerSpace", $blockerRoll);
                $sql = "INSERT INTO `tokens` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`,`turn`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (4+$i);
                    $bBoard = (((($i)-1)*5)+(($blockerRoll)-1));
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."','".$blockerRoll."','".$turn."')";
                }
                $sql .= implode( $values, ',' );
                self::DbQuery( $sql );            
        }


        
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
        
        $blocker = self::getGameStateValue('blockerSpace');
        $cardsInHand = array();
        $tokenArray = array();

        $players = $this->loadPlayersBasicInfos();
        foreach( $players as $player_id => $player ) {
            $cBoard = self::getUniqueValueFromDB( "SELECT `boardID` FROM `tokens` WHERE `player_id` = $player_id ORDER BY `turn` DESC LIMIT 0,1" );

            $tokenArray[] = array($player_id, $cBoard);

            $cardsInHand[] = $this->cards->getCardsInLocation("hand",$player_id);
        }

        $result['tokens'] = $tokenArray;
        $resunt['cards'] = $cardsInHand;
        $result['blocker'] = $blocker;
  
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
        In this space, you can put any utility methods useful for your game logic
    */

    function getBoardPosition($player_id) {
        $pID = $player_id;
        $tID = self::getUniqueValueFromDB( "SELECT `tokenID` FROM `tokens` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1"  );
        $cBoard = self::getUniqueValueFromDB( "SELECT `boardID` FROM `tokens` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
        $cQuad = self::getUniqueValueFromDB( "SELECT `Quad` FROM `tokens` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
        $cSpace = self::getUniqueValueFromDB( "SELECT `Space` FROM `tokens` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
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
        $boardPos = self::getBoardPosition($pID);
        $pResources = $this->getPlayerResources($pID);
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
                if((($z+1)-($cQuad-1)*5) != $blockedSpace) {
                    array_push($possibleMoves, $z);
                }
            }
        }
        
        return $possibleMoves;
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
        $cAz = self::getUniqueValueFromDB( "SELECT `Az` FROM `resources` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
        $cCath = self::getUniqueValueFromDB( "SELECT `Cath` FROM `resources` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
        $cPeople = self::getUniqueValueFromDB( "SELECT `People` FROM `resources` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
        $cTime = self::getUniqueValueFromDB( "SELECT `Time` FROM `resources` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
        $cID = self::getUniqueValueFromDB( "SELECT `charID` FROM `resources` WHERE `player_id` = $pID ORDER BY `turn` DESC LIMIT 0,1" );
        
        $pResources = array(
            'Az' => $cAz, 'Cath' => $cCath, 'People' => $cPeople, 'Time' => $cTime, 'cID' => $cID
        );

        return $pResources;

    }

    function getSpecificResources($player_id, $resource) {
        $pID = $player_id;
        $cResources = getPlayerResources($pID);
        $cAz = $cResources['Az'];
        $cCath = $cResources['Cath'];
        $cPeople = $cResources['People'];
        $cTime = $cResources['Time'];
        $cID = $cResources['cID'];
        
        switch($resource) {
            case 'A':
                return $cAz;
            break;
            case 'C':
                return $cCath;
            break;
            case 'P':
                return $cPeople;
            break;
            case 'T':
                return $cTime;
            break;
            case 'H':
                return $cID;    
            break;
        }

    }

    function updateResources($player_id, $resource, $newTotal) {
        $pID = $player_id;
        $cResources = getPlayerResources($pID);
        $cAz = $cResources['Az'];
        $cCath = $cResources['Cath'];
        $cPeople = $cResources['People'];
        $cTime = $cResources['Time'];
        $cID = $cResources['cID'];
        $turn = $this ->getGameStateValue("turnCount");

        switch ($resource) {
            case 'A':
                $cAz = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID.", '".$turn."') ";
                self::DbQuery( $sql );
            break;
            case 'C':
                $cCath = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID."', '".$turn."') ";
                self::DbQuery( $sql );
            break;
            case 'P':
                $cPeople = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID."', '".$turn."') ";
                self::DbQuery( $sql );
            break;
            case 'T':
                $cTime = $newTotal;
                $sql = "insert into `resources` (`player_id`, `Az`, `Cath`,`People`,`Time`,`CharID`,`turn`) values ('".$pID."', '".$cAz."','".$cCath."', '".$cPeople."','".$cTime."', '".$cID."', '".$turn."') ";
                self::DbQuery( $sql );
            break;
        }
    }

    function setBlocker() {
        $blockerRoll = bga_rand(1,6);
        $turn = $this ->getGameStateValue("turnCount");
        $bBoard = 0;
        switch ($blockerRoll) {
            case 6:
                $this->setGameStateValue("blockerSpace", 0);
                $sql = "INSERT INTO `tokens` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`,`turn`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (0);
                    $bBoard = (0);
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."',0,'".$turn."')";
                }
                $sql .= implode( $values, ',' );
                self::DbQuery( $sql );
            break;
            default:
            $this->setGameStateValue("blockerSpace", 0);
                $sql = "INSERT INTO `tokens` (`player_id`, `tokenID`, `boardID`, `Quad`,`Space`,`turn`) VALUES ";
                for($i=1;$i<5;$i++){
                    $blockerID = (4+$i);
                    $blockerID = (4+$i);
                    $bBoard = (($i-1)*5);
                    $values[] = "(5,'".$blockerID."','".$bBoard."','".$i."','".$blockerRoll."','".$turn."')";
                }
                $sql .= implode( $values, ',' );
                self::DbQuery( $sql );            
        }

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
                    $results = array('Az' => $cAz, 'C' => $cCath);
                    self::notifyAllPlayers( "AzToCath", clienttranslate( '${player_name} has converted Aztec faith to Catholic Faith' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $results;
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
                    $results = array('Az' => $cAz, 'C' => $cCath);
                    self::notifyAllPlayers( "CathToAz", clienttranslate( '${player_name} has converted Catholic faith to Aztec faith' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $results;
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
                    $results = array('Az' => $cAz, 'P' => $cPeople);
                    self::notifyAllPlayers( "AzToPeople", clienttranslate( '${player_name} has converted Aztec faith to People' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $results;
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
                    $results = array('Cath' => $cCath, 'P' => $cPeople);
                    self::notifyAllPlayers( "CathToPeople", clienttranslate( '${player_name} has converted Catholic faith to People' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $results;
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
                    $results = array('Az' => $cAz, 'P' => $cPeople);
                    self::notifyAllPlayers( "PeopleToAz", clienttranslate( '${player_name} has been forced to convert People to Aztec faith' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $results;
                } else if ($aFlag == 2) {
                    $cAz -= ($pRate-1);
                    $cPeople++;
                    updateResources($pID, 'A', $cAz);
                    updateResources($pID, 'P', $cPeople);
                    $results = array('Cath' => $cCath, 'P' => $cPeople);
                    self::notifyAllPlayers( "PeopleToAz", clienttranslate( '${player_name} has been forced to convert People to Aztec faith' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $results;
                }
                st_loseCheck($pID);
            break;
            case 'C':
                if($aFlag != 1) {
                    $cCath += $pRate;
                    $cPeople--;
                    updateResources($pID, 'C', $cAz);
                    updateResources($pID, 'P', $cPeople);
                    self::notifyAllPlayers( "PeopleToCath", clienttranslate( '${player_name} has been forced to convert People to Catholic faith' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $cCath;
                } else if ($aFlag == 1) {
                    $cCath -= ($pRate-1);
                    $cPeople++;
                    updateResources($pID, 'C', $cAz);
                    updateResources($pID, 'P', $cPeople);
                    self::notifyAllPlayers( "PeopleToCath", clienttranslate( '${player_name} has been forced to convert People to Catholic faith' ),
                     array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        ) );
                    return $cCath;
                }
                st_loseCheck($pID);
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
        $results = array();

        switch ($which) {
            case 'A':
                if ($azFlag == false) {
                    $tCost = ($aLevel +1);
                    $results = array('Type' => 'A', 'Amount' => $tCost);
                } else if ($azFlag == true) {
                    $tCost = (($tMax - $aLevel)+1);
                    $results = array('Type' => 'P', 'Amount' => $tCost);
                }
                return $results;
            break;
            case 'C':
                if ($cathFlag == false) {
                    $tCost = ($cLevel +1);
                    $results = array('Type' => 'P', 'Amount' => $tCost);
                } else if ($cathFlag == true) {
                    $tCost = (($tMax - $cLevel)+1);
                    $results = array('Type' => 'P', 'Amount' => $tCost);
                }
                return $results;
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
                        self::notifyAllPlayers( "AzTempleUp", clienttranslate( '${player_name} has moved Juan Diego up a level' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        return $aLevel;
                    } else if ($aLevel = ($tMax-1)) {
                        $aLevel++;
                        $this->setGameStateValue( "azLevel", $aLevel );
                        $this->setGameStateValue( "azFlag", true );
                        self::notifyAllPlayers( "AzTempleTop", clienttranslate( '${player_name} has moved Shield Flower to the top of the hill' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        if ($aFlag == 0) {
                            $this->setGameStateValue( "apocFlag", 1 );
                            self::notifyAllPlayers( "AzApoc", clienttranslate( 'The Aztec Apocalypse has started! A storm gather, rain pours down and a massive flood begins.' ));
                        }
                        
                        return $aLevel;
                    }
                }else if ($azFlag == true) {
                    if ($aLevel >1) {
                        $aLevel--;
                        $this->setGameStateValue( "azLevel", $aLevel );
                        self::notifyAllPlayers( "AzTempleDown", clienttranslate( '${player_name} has moved Shield Flower down a level' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        return $aLevel;
                    } else if ($aLevel == 1) {
                        $aLevel--;
                        $this->setGameStateValue( "azLevel", $aLevel );
                        self::notifyAllPlayers( "AzTempleBottom", clienttranslate( '${player_name} has moved Shield Flower to the bottom level' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        winCheck($pID);
                    }
                }
            break;
            case 'C':
                if($cathFlag == false) {
                    if ($cLevel < ($tMax-1)) {
                        $cLevel++;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                        self::notifyAllPlayers( "CathTempleUp", clienttranslate( '${player_name} has moved Juan Diego up a level' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        return $cLevel;
                    } else if ($cLevel = ($tMax-1)) {
                        $cLevel++;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                        $this->setGameStateValue( "cathFlag", true );
                        $this->setGameStateValue( "cathLevel", $cLevel );
                        self::notifyAllPlayers( "CathTempleTop", clienttranslate( '${player_name} has moved Juan Diego to the top of the hill' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        return $cLevel;
                        if ($aFlag == 0) {
                            $this->setGameStateValue( "apocFlag", 2 );
                            self::notifyAllPlayers( "CathApoc", clienttranslate( 'The Catholic Apocalypse has started! Cortez and his conquistadors have started the assault on Tenochtitlan' ),);
                        }
                    }
                }else if ($cathFlag == true) {
                    if ($cLevel >1) {
                        $cLevel--;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                        self::notifyAllPlayers( "CathTempleDown", clienttranslate( '${player_name} has moved Juan Diego down a level' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
                        return $cLevel;
                    } else if ($cLevel == 1) {
                        $cLevel--;
                        $this->setGameStateValue( "cathLevel", $cLevel );
                        self::notifyAllPlayers( "CathTempleDown", clienttranslate( '${player_name} has moved Juan Diego to the bottom of the hill' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );
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
        self::notifyAllPlayers( "DrawCard", clienttranslate( '${player_name} has drawn the ${heldCard}' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'heldcard' => $heldCardInfo,
                        ) );
        return $heldCardType;

    }

    function cardHandler ($player_id, $card_type) {
        $pID = $player_id;
        $cResources = getPlayerResources($pID);
        $cAz = $cResources['Az'];
        $cCath = $cResources['Cath'];
        $cPeople = $cResources['People'];
        $fPen = $this ->getGameStateValue("faithPenalty" );
        $pPen = $this ->getGameStateValue("PeoplePenalty" );
        $fBon = $this ->getGameStateValue("faithBonus" );
        $pBon = $this ->getGameStateValue("peopleBonus" );
        $fThresh = $this ->getGameStateValue("faithThreshold" );
        $pThresh = $this ->getGameStateValue("peopleThreshold" );
        $pRate = $this ->getGameStateValue("peopleConversionRate");
        $fRate = $this ->getGameStateValue("faithConversionRate");
        $aFlag = $this ->getGameStateValue("apocFlag");
        $azLevel = $this ->getGameStateValue("azLevel");
        $cathLevel = $this ->getGameStateValue("cathLevel");
        $templeMax = $this ->getGameStateValue("templeMax");
        $highest = 0;
        $convert = array();

        if($cAz>$cCath) {
            $highest = 1;
        } else if ($cAz<$cCath) {
            $highest = 2;
        } else if ($cAz==$cCath) {
            $highest= 3;
        }

        switch ($card_type) {
            case 'aPenalty':
                if($cAz>=$fPen) {
                    $cAz-=$fPen;
                    updateResources($pID, 'A', $cAz);
                } else {
                    do{
                        $convert = cardConvert($pID, 'A');
                        $cAz = $convert['Az'];
                    } while($cAz<$fPen);
                    $cAz-=$fPen;
                    updateResources($pID, 'A', $cAz);
                }
                self::notifyAllPlayers( "aPenalty", clienttranslate( '${player_name} has lost ${fPen} Aztec faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'fPen' => $fPen,
                        ) );
            break;
            case'cPenalty':
                if($cCath>=$fPen) {
                    $cCath-=$fPen;
                    updateResources($pID, 'C', $cCath);
                } else {
                    do{
                        $convert = cardConvert($pID, 'C');
                        $cCath = $convert['Cath'];
                    } while($cCath<$fPen);
                    $cCath-=$fPen;
                    updateResources($pID, 'C', $cCath);
                }
                self::notifyAllPlayers( "cPenalty", clienttranslate( '${player_name} has lost ${fPen} Catholic faith' ),
                array(
                    'player_id' => $player_id,
                    'player_name' => self::getActivePlayerName(),
                    'fPen' => $fPen,
                ) );
            break;
            case 'gPenalty':
                if($cAz<$cCath) {
                    if($cCath>=$fPen) {
                        $cCath-=$fPen;
                        updateResources($pID, 'C', $cCath);
                    } else {
                        do{
                            $convert = cardConvert($pID, 'C');
                            $cCath = $convert['Cath'];
                        } while($cCath<$fPen);
                        $cCath-=$fPen;
                        updateResources($pID, 'C', $cCath);
                    }
                } else if ($cAz>$cCath) {
                    if($cAz>=$fPen) {
                        $cAz-=$fPen;
                        updateResources($pID, 'A', $cAz);
                    } else {
                        do{
                            $convert = cardConvert($pID, 'A');
                            $cAz = $convert['Az'];
                        } while($cAz<$fPen);
                        $cAz-=$fPen;
                        updateResources($pID, 'A', $cAz);
                    }
                } else if ($cAz==$cCath) {
                    // player input
                }
                self::notifyAllPlayers( "gPenalty", clienttranslate( '${player_name} has lost ${fPen} of their highest faith' ),
                array(
                    'player_id' => $player_id,
                    'player_name' => self::getActivePlayerName(),
                    'fPen' => $fPen,
                ) );
            break;
            case 'pPenalty':
                if($cPeople>=$pPen) {
                    $cPeople-=$pPen;
                    updateResources($pID, 'P', $cPeople);
                } else {
                        // player input
                        do{
                            $convert = convertPeople($pID, 'A');
                            $cPeople = $convert['A'];
                        } while($cPeople>=$pPen);
                        
                        $cPeople-=$pPen;
                        updateResources($pID, 'P', $cPeople);
                }
                self::notifyAllPlayers( "pPenalty", clienttranslate( '${player_name} has lost ${fPen} People' ),
                array(
                    'player_id' => $player_id,
                    'player_name' => self::getActivePlayerName(),
                    'pPen' => $pPen,
                ) );
            break;
            case 'aCheck':
                if($cAz<$cCath) {
                    if($cAz>=$fPen) {
                        $cAz-=$fPen;
                        updateResources($pID, 'A', $cAz);
                    } else {
                        do{
                            $convert = cardConvert($pID, 'A');
                            $cAz = $convert['Az'];
                        } while($cAz<$fPen);
                        $cAz-=$fPen;
                        updateResources($pID, 'A', $cAz);
                    }
                    self::notifyAllPlayers( "aCheckFail", clienttranslate( '${player_name}\'s Aztec faith is lower than their Catholic faith, and so has lost ${fPen} Aztec faith' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fPen' => $fPen,
                            ) );
                } else {
                    self::notifyAllPlayers( "aCheckPass", clienttranslate( '${player_name}\'s Aztec faith is higher than their Catholic faith and so retains their believers' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                            ) );
                }
            break;
            case 'cCheck':
                if($cCath<$cAz) {
                    if($cCath>=$fPen) {
                        $cCath-=$fPen;
                        updateResources($pID, 'C', $cCath);
                    } else {
                        do{
                            $convert = cardConvert($pID, 'C');
                            $cCath = $convert['Cath'];
                        } while($cCath<$fPen);
                        $cCath-=$fPen;
                        updateResources($pID, 'C', $cCath);
                    }
                    self::notifyAllPlayers( "cCheckFail", clienttranslate( '${player_name}\'s Aztec faith is higher than their Catholic faith and so has lost ${fPen} Catholic faith' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fPen' => $fPen,
                            ) );
                } else {
                    self::notifyAllPlayers( "cCheckPass", clienttranslate( '${player_name}\'s Aztec faith is higher than their Catholic faith and so retains their believers' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                            ) );
                }
            break;
            case 'gCheck':
                if (($cAz && $cCath)  > $fThresh) {
                    self::notifyAllPlayers( "gCheckPass", clienttranslate( '${player_name} has both faiths above ${fThresh} faith and so retains all of their believers' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                            ) );
                } else if ($highest == 1) {
                    if($cAz>=$fPen) {
                        $cAz-=$fPen;
                        updateResources($pID, 'A', $cAz);
                    } else {
                        do{
                            $convert = cardConvert($pID, 'A');
                            $cAz = $convert['Az'];
                        } while($cAz<$fPen);
                        $cAz-=$fPen;
                        updateResources($pID, 'A', $cAz);
                    }
                    self::notifyAllPlayers( "gCheckFail", clienttranslate( '${player_name} doesn\`t have both faiths above ${fThresh} faith and so loses ${fPen} faith from their highest faith' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                                'fPen' => $fPen,
                            ) );
                } else if ($highest == 2) {
                    if($cCath>=$fPen) {
                        $cCath-=$fPen;
                        updateResources($pID, 'C', $cCath);
                    } else {
                        do{
                            $convert = cardConvert($pID, 'C');
                            $cCath = $convert['Cath'];
                        } while($cCath<$fPen);
                        $cCath-=$fPen;
                        updateResources($pID, 'C', $cCath);
                    }
                    self::notifyAllPlayers( "gCheckFail", clienttranslate( '${player_name} doesn\`t have both faiths above ${fThresh} faith and so loses ${fPen} faith from their highest faith' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                                'fPen' => $fPen,
                            ) );
                } else if ($highest == 3) {
                    // player input

                    self::notifyAllPlayers( "gCheckFail", clienttranslate( '${player_name} doesn\`t have both faiths above ${fThresh} faith and so loses ${fPen} faith from their highest faith' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fThresh' => $fThresh,
                                'fPen' => $fPen,
                            ) );
                }                
            break;
            case 'pCheck':
                if($cPeople < $pThresh) {
                    if($cPeople>=$pPen) {
                        $cPeople-=$pPen;
                        updateResources($pID, 'P', $cPeople);
                    } else {
                            // player input
                            do{
                                $convert = convertPeople($pID, 'A');
                                $cPeople = $convert['A'];
                            } while($cPeople>=$pPen);
                            
                            $cPeople-=$pPen;
                            updateResources($pID, 'P', $cPeople);
                    }
                    self::notifyAllPlayers( "pCheckFail", clienttranslate( '${player_name} is below ${pThresh} People faith and so loses ${pPen} People' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'pThresh' => $pThresh,
                                'pPen' => $pPen,
                            ) );        
                } else {
                    self::notifyAllPlayers( "pCheckPass", clienttranslate( '${player_name} is above ${pThresh} People faith and so retains their People' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'pThresh' => $pThresh,
                            ) );
                }
            break;
            case 'aConvert':
                if($cAz>=$fPen) {
                    $cAz-=$fPen;
                    updateResources($pID, 'A', $cAz);
                } else {
                    do{
                        $convert = cardConvert($pID, 'A');
                        $cAz = $convert['Az'];
                    } while($cAz<$fPen);
                    $cAz-=$fPen;
                    updateResources($pID, 'A', $cAz);
                }
                $cCath += (2*$fPen);
                updateResources($pID, 'C', $cCath);
                self::notifyAllPlayers( "aConvert", clienttranslate( '${player_name} is forced to convert ${fPen} Aztec faith to twice that amount of Catholic faith' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fPen' => $fPen,
                            ) );                
            break;
            case 'cConvert':
                if($cCath>=$fPen) {
                    $cCath-=$fPen;
                    updateResources($pID, 'C', $cCath);
                } else {
                    do{
                        $convert = cardConvert($pID, 'C');
                        $cCath = $convert['Cath'];
                    } while($cCath<$fPen);
                    $cCath-=$fPen;
                    updateResources($pID, 'C', $cCath);
                }
                $cAz += (2*$fPen);
                updateResources($pID, 'A', $cAz);
                self::notifyAllPlayers( "cConvert", clienttranslate( '${player_name} is forced to convert ${fPen} Catholic faith to twice that amount of Aztec faith' ),
                            array(
                                'player_id' => $player_id,
                                'player_name' => self::getActivePlayerName(),
                                'fPen' => $fPen,
                            ) );   
            break;
            case 'aCull':
                $cPeople -=$pPen;
                $cAz += $pRate;
                updateResources($pID, 'P', $cPeople);
                updateResources($pID, 'A', $cAz);
                self::notifyAllPlayers( "aCull", clienttranslate( '${player_name} is forced to convert ${pPen} People faith to ${pRate) Aztec faith' ),
                array(
                    'player_id' => $player_id,
                    'player_name' => self::getActivePlayerName(),
                    'pPen' => $pPen,
                    'pRate' => $pRate,
                ) );   
                st_loseCheck();
            break;
            case 'cCull':
                $cPeople -=$pPen;
                $cCath += $pRate;
                updateResources($pID, 'P', $cPeople);
                updateResources($pID, 'C', $cCath);
                self::notifyAllPlayers( "cCull", clienttranslate( '${player_name} is forced to convert ${pPen} People faith to ${pRate) Aztec faith' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        'pPen' => $pPen,
                        'pRate' => $pRate,
                    ) );   
                    st_loseCheck();
            break;
            case 'catchUp':
                if($cPeople<$pThresh) {
                    $cPeople++;
                    updateResources($pID, 'P', $cPeople);
                    self::notifyAllPlayers( "catchUpP", clienttranslate( '${player_name}\'s tribe has grown! People up by 1' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );  
                } else if ($highest == 1) {
                    $cCath = $cCath +2;
                    updateResources($pID, 'C', $cCath); 
                    self::notifyAllPlayers( "catchUpF", clienttranslate( '${player_name} has gained 2 to their lowest Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );    
                } else if ($highest == 2) {
                    $cAz = $cAz +2;
                    updateResources($pID, 'A', $cAz); 
                    self::notifyAllPlayers( "catchUpF", clienttranslate( '${player_name} has gained 2 to their lowest Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );    
                } else if ($highest == 3) {
                    // Player input
                    
                    
                    self::notifyAllPlayers( "catchUpF", clienttranslate( '${player_name} has gained 2 to their lowest Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );    
                }
            break;
            case 'aBonus':
                $cAz += $fBon;
                    updateResources($pID, 'A', $cAz); 
                    self::notifyAllPlayers( "aBonus", clienttranslate( '${player_name} has gained ${fBon} Aztec Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) ); 
            break;
            case 'cBonus':
                $cCath += $fBon;
                    updateResources($pID, 'C', $cCath); 
                    self::notifyAllPlayers( "cBonus", clienttranslate( '${player_name} has gained ${fBon} Aztec Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) ); 
            break;
            case 'gBonus':
                if ($highest == 1) {
                    $cCath += $fBon;
                    updateResources($pID, 'C', $cCath); 
                    self::notifyAllPlayers( "gBonus", clienttranslate( '${player_name} has gained ${fBon} to their lowest Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) ); 
                } else if ($highest == 2) {
                    $cAz += $fBon;
                    updateResources($pID, 'A', $cAz); 
                    self::notifyAllPlayers( "gBonus", clienttranslate( '${player_name} has gained ${fBon} to their lowest Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) ); 
                } else if ($highest == 3) {
                    // player input

                    self::notifyAllPlayers( "gBonus", clienttranslate( '${player_name} has gained ${fBon} to their lowest Faith' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'fBon' => $fBon,
                        ) ); 
                }
            break;
            case 'pBonus':
                $cPeople += $pBon;
                updateResources($pID, 'P', $cPeople);
                self::notifyAllPlayers( "cBonus", clienttranslate( '${player_name} has gained ${pBon} People' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                        'pBon' => $pBon,
                    ) );  
            break;
            case 'uFigure':
                $inputFlag = false;
                $moveTemple = '';
                
                do {
                    // player input
                    if(($playerInput == 'A') && ($azLevel != $templeMax)) {
                        $moveTemple = 'A';
                        $inputFlag = true;
                    }
                    if(($playerInput == 'C') && ($cathLevel != $templeMax)) {
                        $moveTemple = 'C';
                        $inputFlag = true;
                    }
                } while ($inputFlag == false);
                switch ($moveTemple) {
                    case 'A':
                        $azLevel++;
                        $this->setGameStateValue("azLevel", $azLevel);
                        self::notifyAllPlayers( "uFigureA", clienttranslate( '${player_name} has moved Shield Flower up a level' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                    ) );  
                    break;
                    case 'C':
                        $cathLevel++;
                        $this->setGameStateValue("cathLevel", $cathLevel);
                        self::notifyAllPlayers( "uFigureA", clienttranslate( '${player_name} has moved Juan Diego up a level' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );  
                    break;
                }
            break;
            case 'dFigure':
                $inputFlag = false;
                $moveTemple = '';
                
                do {
                    // player input
                    if(($playerInput == 'A') && ($azLevel != $templeMax)) {
                        $moveTemple = 'A';
                        $inputFlag = true;
                    }
                    if(($playerInput == 'C') && ($cathLevel != $templeMax)) {
                        $moveTemple = 'C';
                        $inputFlag = true;
                    }
                } while ($inputFlag == false);
                switch ($moveTemple) {
                    case 'A':
                        $azLevel--;
                        $this->setGameStateValue("azLevel", $azLevel);
                        self::notifyAllPlayers( "dFigureA", clienttranslate( '${player_name} has moved Shield Flower down a level' ),
                    array(
                        'player_id' => $player_id,
                        'player_name' => self::getActivePlayerName(),
                    ) );  
                    break;
                    case 'C':
                        $cathLevel--;
                        $this->setGameStateValue("cathLevel", $cathLevel);
                        self::notifyAllPlayers( "dFigureC", clienttranslate( '${player_name} has moved Juan Diego down a level' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );  
                    break;
                }
            break;
            case 'iBlock':
                $this->deck->pickCards(1,'held',$pID);
                self::notifyAllPlayers( "iBlock", clienttranslate( '${player_name} has drawn an Ignore Block card. It has been placed in their hand' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );  
            break;
            case 'rBlock':
                $this->deck->pickCards(1,'held',$pID);
                self::notifyAllPlayers( "rBlock", clienttranslate( '${player_name} has drawn a Reroll Block card' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                        ) );  
            break;
            case 'nQuad':
                $cPos = getBoardPosition($pID);
                $cQuad = $cPos['Quad'];
                $actualMove = 0;
                if($cQuad != 4){
                    $cQuad++;
                    $actualMove = (($cQuad-1)*5);
                    updateBoard($pID, $actualMove);
                } else if ($cQuad ==4) {
                    $cQuad = 1;
                    $actualMove = 0;
                    updateBoard($pID, $actualMove);
                }
                self::notifyAllPlayers( "nQuad", clienttranslate( '${player_name} has been moved to the first space in quadrant ${cQuad}' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'cQuad' => $cQuad,
                        ) );
            break;  
            case 'aSpace':
                // player input
                $actualMove = $playerInput;
                $spaceIndex = ($actualMove+1);
                updateBoard($pID, $actualMove);
                self::notifyAllPlayers( "aSpace", clienttranslate( '${player_name} has been moved to space ${spaceIndex}' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName(),
                            'spaceIndex' => $spaceIndex,
                        ) );
            break;
            case 'rTime':
                updateResources($pID, 'T', 1);
                self::notifyAllPlayers( "rTime", clienttranslate( '${player_name} has had their Time reset to 1' ),
                        array(
                            'player_id' => $player_id,
                            'player_name' => self::getActivePlayerName()
                        ) );
            break;
        }
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

    function argspickSpace() {
        
        return array(
            $possibleMoves => self::checkMoves()
        );
    }

    function argsupdateSpace() {
        //actualMove
    }

    function argsplayersInSpace() {
        //encounteredPlayers
    }

    function argsresolveCard() {
        //actualCard
    }

    function argsloseCheck() {
        //playersToCheck
        //lastState
    }

    function argswinCheck() {
        //playersToCheck
        //lastState
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
    
    function st_queryBoard() {
        

    }
    
    function st_pickSpace($spaceList, $blocker) {
        
    }
    
    function st_updateSpace() {

    }
    
    function st_playersInSpace() {

    }
    
    function st_drawCardState() {

    }
    
    function st_resolveCard() {

    }
    
    function st_ResourceLoop() {

    }

    function st_endTurn() {

    }
    
    function st_loseCheck($player_id) {
        $pID = $player_id;
        $peopleCheck = getSpecificResources($pID, 'P');
        if($peopleCheck<=0) {
            $this->activeNextPlayer();
            self::notifyAllPlayers( "lossNotify", clienttranslate( '${player_name} has run out of People and been eliminated from the game' ),
            array(
                'player_id' => $pID,
            ) );
            self::eliminatePlayer( $pID );
        }
    }

    function st_winCheck() {
        

    }
    
    function st_gameWon() {

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
