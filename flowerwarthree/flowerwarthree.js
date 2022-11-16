/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * FlowerWarThree implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * flowerwarthree.js
 *
 * FlowerWarThree user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */


let blockerSpace = 0; 
let boardArray = [];
let terrainArray = [];

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.flowerwarthree", ebg.core.gamegui, {
        constructor: function(){
            console.log('flowerwarthree constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
    
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );

            this.azFaithCounter = {};
            this.cathFaithCounter = {};
            this.peopleCounter = {};
            this.timeCounter = {};
            
            // Setting up player boards
            i=0;
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];                        
                var player_board_div = $('player_board_'+player_id);
                dojo.place( this.format_block('jstpl_player_board', player ), player_board_div );
                this.azFaithCounter[player_id] = new ebg.counter();
                this.azFaithCounter[player_id].create('azFaithCounter'+player_id);
                this.azFaithCounter[player_id].setValue(gamedatas.resources[i][0]);
                this.cathFaithCounter[player_id] = new ebg.counter();
                this.cathFaithCounter[player_id].create('cathFaithCounter'+player_id);
                this.cathFaithCounter[player_id].setValue(gamedatas.resources[i][1]);
                this.peopleCounter[player_id] = new ebg.counter();
                this.peopleCounter[player_id].create('peopleCounter'+player_id);
                this.peopleCounter[player_id].setValue(gamedatas.resources[i][2]);
                this.timeCounter[player_id] = new ebg.counter();
                this.timeCounter[player_id].create('timeCounter'+player_id);
                this.timeCounter[player_id].setValue(1);
                i++;         
            }
            terrainArray = gamedatas.terrain;
            boardArray = gamedatas.board;

            for(i=0;i<19;i++) {
                let boardContainer = document.getElementById("boardContainer");
                boardSpace = this.createSpace(i);
                boardContainer.appendChild(boardSpace);
            }   
                player1= gamedatas.tokens[0][0];
                player2= gamedatas.tokens[1][0];
                if (gamedatas.tokens.length>=3) {
                    player3= gamedatas.tokens[2][0];
                }
                if (gamedatas.tokens.length>=4) {
                    player4= gamedatas.tokens[3][0];
                }
            // TODO: Set up your game interface here, according to "gamedatas"
            for(b=0;b<20;b++) {
                for(p=0;p<gamedatas.tokens.length;p++) {
                    if(gamedatas.tokens.at(p).at(1) == b) { 
                        let token = document.createElement("div");        
                        token.classList.add("token");
                        token.id="token_"+p;
                        boardID = "tokenHolder_"+b;
                        playerContainer = document.getElementById(boardID);
                        switch (p) {
                            case 0:
                                color = gamedatas.players[player1].color;
                                break;
                            case 1:
                                color = gamedatas.players[player2].color
                                break;
                            case 2:
                                color = gamedatas.players[player3].color
                                break;
                            case 3:
                                color = gamedatas.players[player4].color
                                break;
                        }
                        color = "filter_" +color;
                        token.classList.add(color);                                       
                        playerContainer = document.getElementById(boardID);
                        playerContainer.appendChild(token);
                    }
                }
            }
        
            blockerSpace = gamedatas.blocker;
            let q1Block = (gamedatas.blocker-1);
            let q2Block = q1Block+5;
            let q3Block = q1Block+10;
            let q4Block = q1Block+15;
            switch(blockerSpace) {
                case "0":
                break;
                default:
                    for(let i=0;i<4;i++) {
                        let blocker = document.createElement("div");        
                        blocker.classList.add("blocker");
                        blocker.classList.add("filter_000000");
                        if(i==0) {
                            blockerID = "blocker_"+5;
                            boardID = "space_"+q1Block;
                            blocker.id=blockerID;
                        }else if(i==1){
                            blockerID = "blocker_"+6;
                            boardID = "space_"+q2Block;
                            blocker.id=blockerID;
                        }else if(i==2){
                            blockerID = "blocker_"+7;
                            boardID = "space_"+q3Block;
                            blocker.id=blockerID;
                        }else if(i==3){
                            blockerID = "blocker_"+8;
                            boardID = "space_"+q4Block;
                            blocker.id=blockerID;
                            
                        }
                        playerContainer = document.getElementById(boardID);
                        playerContainer.appendChild(blocker);
                    }
                    break;             
            }

            for(let i=0; i<7; i++) {
                if(gamedatas.azTemple == i) {
                    templeToken = document.createElement("div");
                    templeToken.id= "azTempleToken";
                    templeToken.classList.add("templeToken");
                    azTempleLevel = document.getElementById("az_"+i);
                    azTempleLevel.appendChild(templeToken);
                }
                if(gamedatas.cathTemple == i) {
                    templeToken = document.createElement("div");
                    templeToken.id= "cathTempleToken";
                    templeToken.classList.add("templeToken");
                    cathTempleLevel = document.getElementById("cath_"+i);
                    cathTempleLevel.appendChild(templeToken);
                }
            }
            
            if(gamedatas.azFlag == false) {
                templeFlag = document.createElement("div");
                templeFlag.id= "azFlag";
                templeFlag.classList.add("upArrow");
                azTempleFlag = document.getElementById("azTitle");
                azTempleFlag.appendChild(templeFlag);
            } else if(gamedatas.azFlag == true) {
                templeFlag = document.createElement("div");
                templeFlag.id= "azFlag";
                templeFlag.classList.add("downArrow");
                azTempleLevel = document.getElementById("azTitle");
                azTempleLevel.appendChild(templeFlag);
            }
            if(gamedatas.azFlag == false) {
                templeFlag = document.createElement("div");
                templeFlag.id= "cathFlag";
                templeFlag.classList.add("upArrow");
                azTempleFlag = document.getElementById("cathTitle");
                azTempleFlag.appendChild(templeFlag);
            } else if(gamedatas.azFlag == true) {
                templeFlag = document.createElement("div");
                templeFlag.id= "cathFlag";
                templeFlag.classList.add("downArrow");
                azTempleLevel = document.getElementById("cathTitle");
                azTempleLevel.appendChild(templeFlag);
            }

            if(gamedatas.apocFlag == 0) {
                apocFlag = document.getElementById("apocFlag");
                apocTitle = document.createElement("p");
                apocTitle = document.createTextNode("Apocalypse Status: No Apocalypse");
                apocFlag.appendChild(apocTitle);
            } else if(gamedatas.apocFlag == 1) {
                apocFlag = document.getElementById("apocFlag");
                apocTitle = document.createElement("p");
                apocTitle = document.createTextNode("Apocalypse Status: Aztec Apocalypse");
                apocFlag.appendChild(apocTitle);
            } else if(gamedatas.apocFlag == 2) {
                apocFlag = document.getElementById("apocFlag");
                apocTitle = document.createElement("p");
                apocTitle = document.createTextNode("Apocalypse Status: Catholic Apocalypse");
                apocFlag.appendChild(apocTitle);
            }

            dojo.place("<div id='customActions' style='display:inline-block'></div>", $('generalactions'), 'after');

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
        },
        

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
            messagecontainer = document.getElementById("messageContainer");
            messagecontainer.classList.add("hidden");   
        
            switch( stateName )
            {
                case 'startTurn':
                   

                break
                case 'moveToken':
                    // handle action buttons
                    this.updatePageTitle();

                    pID = args.args.boardState.playerID;
                    aButtonFlag = args.args.boardState.aButtonFlag;
                    cButtonFlag = args.args.boardState.cButtonFlag;
                    pButtonFlag = args.args.boardState.pButtonFlag;
                    cTime = args.args.boardState.Time;
                    cQuad = args.args.boardState.Quad;
                    cBoardID = args.args.boardState.boardID;
                    aCount = args.args.boardState.aCount;
                    availableMoves = args.args.boardState.possibleMoves;

                    if (this.isCurrentPlayerActive() == true) {
                        if(aButtonFlag == false) {
                            aButton = document.getElementById("updateQuadA");
                            aButton.classList.add("hidden");
                        }
                        if(cButtonFlag == false) {
                            cButton = document.getElementById("updateQuadC");
                            cButton.classList.add("hidden");
                        }
                        if(pButtonFlag == false) {
                            pButton = document.getElementById("resetTime");
                            pButton.classList.add("hidden");
                        }

                        if(aCount==3) {
                            aButton = document.getElementById("updateQuadA");
                            aButton.classList.add("hidden");
                            cButton = document.getElementById("updateQuadC");
                            cButton.classList.add("hidden");
                        }
                        
                        if (cTime == 1) {
                            pButton = document.getElementById("resetTime");
                            pButton.classList.add("hidden");
                        }


                        // get active spaces 
                        let blockedQuad = (((cQuad-1)*5)+(blockerSpace-1));
                        let openSpace =[];
                        if(cTime <4) {
                            openSpace = this.possibleMoves(availableMoves, cQuad);
                        } else if(cTime == 4) {
                            cQuad++;
                            openSpace = this.possibleMoves(availableMoves, cQuad);
                        }

                        for(let p=0; p<openSpace.length; p++) {
                            highlightSpace = document.getElementById("space_"+openSpace[p]);
                            highlightSpace.classList.add("possibleMove");
                        }
                        if(blockerSpace > 0) {
                            highlightSpace = document.getElementById("space_"+blockedQuad);
                            highlightSpace.classList.add("blockedMove");
                        }
    
                        clickableSpace = document.getElementsByClassName("possibleMove");
                        Array.from(clickableSpace).forEach(
                            (elem) => elem.addEventListener("click", () => this.onClickedSpace(elem.id))
                        );
                    }
                break;

                case 'boardUpdate':
                    this.updatePageTitle();
                    pID = args.args.boardState.playerID;
                    cQuad = args.args.boardState.Quad;
                    cBoardID = args.args.boardState.boardID;
                    tokenID = args.args.boardState.tokenID;
                    color = args.args.boardState.pColor;
                    cAz = args.args.boardState.Az;
                    cCath = args.args.boardState.Cath;
                    cPeople = args.args.boardState.People;
                    cTime = args.args.boardState.Time

                    this.azFaithCounter[pID].toValue(cAz);
                    this.cathFaithCounter[pID].toValue(cCath);
                    this.peopleCounter[pID].toValue(cPeople);
                    this.timeCounter[pID].toValue(cTime);
                    
                    color = "filter_" +color;
                    tokenID = "token_"+tokenID;
                    
                    currentBoard = document.getElementById(tokenID);
                    currentBoard.remove();

                    let token = document.createElement("div");        
                    token.classList.add("token");
                    token.id=tokenID
                    playerContainer = document.getElementById(boardID);
                    token.classList.add(color);                                       
                    playerContainer.appendChild(token);
                    for(let i=0;i<20; i++) {
                        removeTags = document.getElementById("space_"+i);
                        removeTags.classList.remove("possibleMove");
                        removeTags.classList.remove("blockedMove");
                    }
                    
                break;

                case 'cardHandler':
                    cardType = args.args.cardState.cardType;
                    cardFaithFlag = args.args.cardState.faithChoiceFlag;
                    cardTempleFlag = args.args.cardState.faithChoiceFlag;
                    moveAD = args.args.cardState.moveAD;
                    moveAU = args.args.cardState.moveAU;
                    moveCD = args.args.cardState.moveCD;
                    moveCU = args.args.cardState.moveCU;
                    cLevel = args.args.cardState.cathLevel;
                    maxHeight = args.args.cardState.maxHeight;

                    const faiths = ["Aztec", "Catholic"];

                    switch(cardType) {
                        case 'gPenalty':
                        case 'gCheck':
                            if(cardFaithFlag == true) {
                                this.multipleChoiceDialog(_("Which Faith would you like to spend to pay the penalty?"), faiths, (choice) => {
                                    var faithChoice = faiths[choice];
                                    this.ajaxcallwrapper("faithCardChoice", { string: faithChoice });
                                  });
                                  return;
                            }
                        break;
                        case 'catchUp':
                        case 'gBonus':
                            if(cardFaithFlag == true) {
                                this.multipleChoiceDialog(_("Which Faith would you like to gain?"), faiths, (choice) => {
                                    var faithChoice = faiths[choice];
                                    this.ajaxcallwrapper("faithCardChoice", { string: faithChoice });
                                  });
                                  return;
                            }
                        break;
                        case "uFigure":
                            if(cardTempleFlag == true) {
                                actualChoices = [];
                                if(MoveAU == true) {
                                    actualChoices.push("Aztec");
                                }
                                if(MoveCU == true) {
                                    actualChoices.push("Catholiic");
                                }
                                this.multipleChoiceDialog(_("Choose a figure to move up a step"), actualChoices, (choice) => {
                                    var templeChoice = actualChoices[choice];
                                    this.ajaxcallwrapper("templeCardChoice", { string: templeChoice });
                                  });
                                  return;
                            }
                        break;
                        case "dFigure":
                            if(cardTempleFlag == true) {
                                actualChoices = [];
                                if(MoveAD == true) {
                                    actualChoices.push("Aztec");
                                }
                                if(MoveCD == true) {
                                    actualChoices.push("Catholiic");
                                }
                                this.multipleChoiceDialog(_("Choose a figure to move down a step"), actualChoices, (choice) => {
                                    var templeChoice = actualChoices[choice];
                                    this.ajaxcallwrapper("templeCardChoice", { string: templeChoice });
                                  });
                                  return;
                            }
                        break;
                        case "nQuad":
                            
                        break;
                    }

                break;
           
           
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName ) {

                case'moveToken':
                                        
                break;
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
            testID = this.isCurrentPlayerActive();
                      
            if( this.isCurrentPlayerActive() )
            {
                
                switch( stateName )
                {

                    case 'moveToken':
                        console.log( 'state: '+stateName );
                            this.addActionButton( 'updateQuadA', _('1 Aztec Faith: Move to the next Quadrant'), 'onUpdateQuadA' ); 
                            this.addActionButton( 'updateQuadC', _('1 Catholic Faith: Move to the next Quadrant'), 'onUpdateQuadC' ); 
                            this.addActionButton( 'resetTime', _("1 People: Reset your Time"), 'onResetTime' );
                    break;

                    /*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */

            createSpace: function (boardID) {
                let space = document.createElement("div");
                space.id="space_"+boardID;
                let spaceResource = document.createElement("div");
                spaceResource.id = "resourceContainer_space_"+boardID;
                spaceResource.classList.add("resourceContainer");
                let resourceA = document.createElement("div");
                resourceA.id = "resources_a_space_"+boardID;
                resourceA.classList.add("resource");
                let resourceC = document.createElement("div");
                resourceC.id = "resources_c_space_"+boardID;
                resourceC.classList.add("resource");
                let resourceP = document.createElement("div");
                resourceP.id = "resources_p_space_"+boardID;
                resourceP.classList.add("resource");
                let iconA = document.createElement("div");
                iconA.id = "resources_a_icon_space_"+boardID;
                iconA.classList.add("resourceIconA");
                let iconC = document.createElement("div");
                iconC.id = "resources_c_icon_space_"+boardID;
                iconC.classList.add("resourceIconC");
                let iconP = document.createElement("div");
                iconP.id = "resources_p_icon_space_"+boardID;
                iconP.classList.add("resourceIconP");
                let resourceAText = document.createElement("p");
                resourceAText.id = "resources_a_text_space_"+boardID;
                resourceAText.classList.add("resourceText");
                let resourceCText = document.createElement("p");
                resourceCText.id = "resources_c_text_space_"+boardID;
                resourceCText.classList.add("resourceText");
                let resourcePText = document.createElement("p");
                resourcePText.id = "resources_p_text_space_"+boardID;
                resourcePText.classList.add("resourceText");
                let tokenHolder = document.createElement("div");
                tokenHolder.id = "tokenHolder_"+boardID;
                tokenHolder.classList.add("tokenHolder");
                let spaceP = document.createElement("p");
                spaceP.id = "text_space_"+boardID;

                let Atext = document.createTextNode(boardArray[boardID]['Az']);
                let Ctext = document.createTextNode(boardArray[boardID]['Cath']);
                let Ptext = document.createTextNode(boardArray[boardID]['People']);

                resourceAText.appendChild(Atext);
                resourceCText.appendChild(Ctext);
                resourcePText.appendChild(Ptext);
                
                resourceA.appendChild(resourceAText);
                resourceA.appendChild(iconA);
                resourceC.appendChild(resourceCText);
                resourceC.appendChild(iconC);
                resourceP.appendChild(resourcePText);
                resourceP.appendChild(iconP);
                
                spaceResource.appendChild(resourceA);
                spaceResource.appendChild(resourceC);
                spaceResource.appendChild(resourceP);
                
                spaceP.classList.add("spaceText");
                space.classList.add('space');
                space.classList.add(terrainArray[boardID]);

                let spacePtext = document.createTextNode("Space "+(boardID+1));
                spaceP.appendChild(spacePtext);
                space.appendChild(spaceP);
                space.appendChild(spaceResource);
                space.appendChild(tokenHolder);
                
		        return space
            },

            ajaxCallWrapper: function (action, args) {
                if (!args) {
                    args = {};
                }
                args.lock = true;
                this.ajaxcall('/' + this.game_name + '/' + this.game_name + '/' + action + '.html', args, this,
                    (result) => {}, (is_error) => {});
            },


            possibleMoves: function (availableMoves, Quad) {
                let Moves = availableMoves;
                let aQuad = Quad;
                let testSpace = 0;
                let lastSpace = 0;
                let openSpaces = [];
                let blocker = 0;
                
                testSpace = ((aQuad-1)*5);
                lastSpace = ((aQuad*5)-1);
                blocker = (((aQuad-1)*5)+(blockerSpace-1));
                for (i=testSpace;i<=lastSpace;i++) {
                    if(Moves.includes(i) == true) {
                        if (i != blocker){
                            openSpaces.push(i);  
                        }
                    }
                }
                return openSpaces;
            },

           
        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
            onUpdateQuadA: function (evt) {
                pID = this.getActivePlayerId();
                this.azFaithCounter[pID].incValue(-1);
                for(let i=0;i<20; i++) {
                    removeTags = document.getElementById("space_"+i);
                    removeTags.classList.remove("possibleMove");
                    removeTags.classList.remove("blockedMove");
                }
                this.ajaxCallWrapper("updateQuadA",);
                //this.updatePageTitle(); 
            },

            onUpdateQuadC: function (evt) {
                this.cathFaithCounter[pID].incValue(-1);
                for(let i=0;i<20; i++) {
                    removeTags = document.getElementById("space_"+i);
                    removeTags.classList.remove("possibleMove");
                    removeTags.classList.remove("blockedMove");
                }
                dojo.stopEvent( evt );
                this.ajaxCallWrapper("updateQuadC",);
                //this.updatePageTitle();         
            },
            onResetTime: function (evt) {
                this.peopleCounter[pID].incValue(-1);
                this.timeCounter[pID].toValue(1);
                for(let i=0;i<20; i++) {
                    removeTags = document.getElementById("space_"+i);
                    removeTags.classList.remove("possibleMove");
                    removeTags.classList.remove("blockedMove");
                }
                dojo.stopEvent( evt );
                this.ajaxCallWrapper("resetTime",);
                //this.updatePageTitle(); 
            },

            onClickedSpace: function (bID) {
                boardID = bID;
                this.ajaxCallWrapper("clickedSpace", {boardID});
            },
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/flowerwarthree/flowerwarthree/myAction.html", { 
                                                                    lock: true, 
                                                                    myArgument1: arg1, 
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
        },        
        
        */

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your flowerwarthree.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 

            dojo.subscribe( 'otherDrawnCard', this, "notif_otherDrawnCard" );
            dojo.subscribe( 'selfDrawnCard', this, "notif_selfDrawnCard" );

            dojo.subscribe( 'otherUpdateQuadA', this, "notif_otherUpdateQuadA" );
            dojo.subscribe( 'selfUpdateQuadA', this, "notif_selfUpdateQuadA" );
            this.notifqueue.setIgnoreNotificationCheck( 'otherUpdateQuadA', (notif) => (notif.args.player_id == this.player_id) );
            dojo.subscribe( 'otherUpdateQuadC', this, "notif_otherUpdateQuadC" );
            dojo.subscribe( 'selfUpdateQuadC', this, "notif_selfUpdateQuadC" );
            this.notifqueue.setIgnoreNotificationCheck( 'otherUpdateQuadC', (notif) => (notif.args.player_id == this.player_id) );
            dojo.subscribe( 'otherResetTime', this, "notif_otherResetTime" );
            dojo.subscribe( 'selfResetTime', this, "notif_selfResetTime" );
            this.notifqueue.setIgnoreNotificationCheck( 'otherResetTime', (notif) => (notif.args.player_id == this.player_id) );
            
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        notif_otherDrawnCard: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_selfDrawnCard: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_otherUpdateQuadA: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_otherUpdateQuadC: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_selfUpdateQuadA: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_selfUpdateQuadC: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_otherResetTime: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_selfResetTime: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        }

        /*
        Example:
        
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */
   });             
});
