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
            
            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // TODO: Setting up players boards if needed
            }
            
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

        for(i=0;i<5;i++) {
            let space = document.createElement("div");
            let spaceP = document.createElement("p");
            spaceP.classList.add("spaceText");
            space.classList.add('space');
            let topRow = document.getElementById("topRow");
            let spaceName = "space_"+i;
            let spaceText = "Space "+(i+1);
            space.id=spaceName;
            let spacePtext = document.createTextNode(spaceText);
            spaceP.appendChild(spacePtext);
            space.appendChild(spaceP);
            topRow.appendChild(space);
        }
        for(i=5;i<10;i++) {
            let space = document.createElement("div");
            let spaceP = document.createElement("p");
            spaceP.classList.add("spaceText");
            space.classList.add('space');
            let rightColumn = document.getElementById("rightColumn");
            let spaceName = "space_"+i;
            let spaceText = "Space "+(i+1);
            space.id=spaceName;
            let spacePtext = document.createTextNode(spaceText);
            spaceP.appendChild(spacePtext);
            space.appendChild(spaceP);
            rightColumn.appendChild(space);
        }
        for(i=14;i>9;i--) {
            let space = document.createElement("div");
            let spaceP = document.createElement("p");
            spaceP.classList.add("spaceText");
            space.classList.add('space');
            let bottomRow = document.getElementById("bottomRow");
            let spaceName = "space_"+i;
            let spaceText = "Space "+(i+1);
            space.id=spaceName;
            let spacePtext = document.createTextNode(spaceText);
            spaceP.appendChild(spacePtext);
            space.appendChild(spaceP);
            bottomRow.appendChild(space);
        }
        for(i=19;i>14;i--) {
            let space = document.createElement("div");
            let spaceP = document.createElement("p");
            spaceP.classList.add("spaceText");
            space.classList.add('space');
            let leftColumn = document.getElementById("leftColumn");
            let spaceName = "space_"+i;
            let spaceText = "Space "+(i+1);
            space.id=spaceName;
            let spacePtext = document.createTextNode(spaceText);
            spaceP.appendChild(spacePtext);
            space.appendChild(spaceP);
            leftColumn.appendChild(space);
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
                    boardID = "space_"+b;
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
                    bQuad = Math.ceil(((cBoardID+1)/5));
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

                        if(bQuad != cQuad) {
                            switch(bQuad) {
                                case 1:
                                    if(cQuad == 4) {
                                        aButton = document.getElementById("updateQuadA");
                                        aButton.classList.add("hidden");
                                        cButton = document.getElementById("updateQuadC");
                                        cButton.classList.add("hidden");
                                        pButton = document.getElementById("resetTime");
                                        pButton.classList.add("hidden");
                                    }
                                break;
                                case 2:
                                    if(cQuad == 1) {
                                        aButton = document.getElementById("updateQuadA");
                                        aButton.classList.add("hidden");
                                        cButton = document.getElementById("updateQuadC");
                                        cButton.classList.add("hidden");
                                    }
                                break;
                                case 3:
                                    if(cQuad == 2) {
                                        aButton = document.getElementById("updateQuadA");
                                        aButton.classList.add("hidden");
                                        cButton = document.getElementById("updateQuadC");
                                        cButton.classList.add("hidden");
                                    }
                                break;
                                case 4:
                                    if(cQuad == 3) {
                                        aButton = document.getElementById("updateQuadA");
                                        aButton.classList.add("hidden");
                                        cButton = document.getElementById("updateQuadC");
                                        cButton.classList.add("hidden");
                                    }
                                break;
                            }
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
                    console.dir(args.args.cardState);
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
                this.updatePageTitle(); 
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
                this.updatePageTitle();         
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
                this.updatePageTitle(); 
            },

            onClickedSpace: function (bID) {
                boardID = bID;
                this.ajaxCallWrapper("clickedSpace",{boardID});
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

            
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        

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
