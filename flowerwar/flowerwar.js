/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * flowerwar implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * flowerwar.js
 *
 * flowerwar user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    g_gamethemeurl + "./modules/boardsetup.js",
],
function (dojo, declare) {
    return declare("bgagame.flowerwar", ebg.core.gamegui, {
        constructor: function() {
            console.log('flowerwar constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

            this.globalTerrain = [];
            this.globalBoard = [];
            this.globalResource = [];
            this.colorArray=[];
            this.cards = [];
            this.blockerArray = [];
            this.blocker = 0;
            this.azTemple = -1;
            this.cathTemple = -1;
            this.apocFlag = 0;
            this.azFlag = false;
            this.cathFlag = false;
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

            // Create Counters
              
            this.azFaithCounter = {};
            this.cathFaithCounter = {};
            this.peopleCounter = {};
            this.timeCounter = {};
            
            // Setting up player boards

            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id]; // boilerplate
                let i=0;
                this.colorArray.push(player.color);
                
                // Set up counters (boilerplate from wiki)
                var player_board_div = $('player_board_'+player_id); 
                dojo.place( this.format_block('jstpl_player_board', player ), player_board_div );

                // Az Counter
                this.azFaithCounter[player_id] = new ebg.counter();
                this.azFaithCounter[player_id].create('azFaithCounter'+player_id);
                this.azFaithCounter[player_id].setValue(gamedatas.resources[i][1]);

                // Cath Counter
                this.cathFaithCounter[player_id] = new ebg.counter();
                this.cathFaithCounter[player_id].create('cathFaithCounter'+player_id);
                this.cathFaithCounter[player_id].setValue(gamedatas.resources[i][2]);

                // People Counter
                this.peopleCounter[player_id] = new ebg.counter();
                this.peopleCounter[player_id].create('peopleCounter'+player_id);
                this.peopleCounter[player_id].setValue(gamedatas.resources[i][3]);

                // Time Counter
                this.timeCounter[player_id] = new ebg.counter();
                this.timeCounter[player_id].create('timeCounter'+player_id);
                this.timeCounter[player_id].setValue(1);
                
                i++;
            }
            
            // TODO: Set up your game interface here, according to "gamedatas"

            // clear global variables
            this.globalTerrain = [];
            this.globalBoard = [];
            this.globalResources = [];
            this.globalTokens = [];
            this.blockerTokens = [];
            this.cards = [];
            this.azTemple = -1;
            this.cathTemple = -1;
            this.apocFlag = 0;
            this.azFlag = false;
            this.cathFlag = false;

            // set global variables
            this.globalTerrain = gamedatas.terrain;
            this.globalBoard = gamedatas.board;
            this.globalResources = gamedatas.resources;
            this.globalTokens = gamedatas.tokens;
            this.blocker = gamedatas.blocker;
            this.azTemple = gamedatas.azTemple;
            this.cathTemple = gamedatas.cathTemple;
            this.apocFlag = gamedatas.apocFlag;
            this.azFlag = gamedatas.azFlag;
            this.cathFlag = gamedatas.cathFlag;
            this.cards = gamedatas.cards;
            this.blockerTokens = gamedatas.blockerTokens;
            

            // set up playing field
            clearBoard();
            createBoard(this.globalTokens, this.blocker, this.blockerTokens, this.colorArray, this.globalTerrain, this.globalBoard);
            createTemple(this.azTemple, this.cathTemple, this.apocFlag, this.azFlag, this.cathFlag);
            
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
                   

                break;
                case 'moveToken':
                    this.updatePageTitle();

                    // button handling
                    let cAz = args.args.boardState.Az;
                    let aButtonFlag = args.args.boardState.aButtonFlag;
                    let cCath = args.args.boardState.Cath;
                    let cButtonFlag = args.args.boardState.cButtonFlag;
                    let cPeople = args.args.boardState.People;
                    let pButtonFlag = args.args.boardState.pButtonFlag;
                    let aCount = args.args.boardState.aCount;
                    
                    // possibleMoves
                    let pID = args.args.boardState.playerID;
                    let cBoard = args.args.boardState.boardID;
                    let cQuad = args.args.boardState.Quad;
                    let oQuad = args.args.boardState.oQuad;
                    let cTime = args.args.boardState.Time;
                    let possibleMoves = args.args.boardState.availableMoves;
                    let blocker = args.args.boardState.blocker;
                    let blockerTokens = args.args.boardState.blockerTokens;


                    
                    if (this.isCurrentPlayerActive() == true) {
                        // handle action buttons
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
                        
                        // highlight spaces
                        this.MakeSpacesClickable(possibleMoves, blocker, blockerTokens, cQuad);
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
            
            switch( stateName )
            {
            
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
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        ajaxCallWrapper: function (action, args) {
                if (!args) {
                    args = {};
                }
                args.lock = true;
                this.ajaxcall('/' + this.game_name + '/' + this.game_name + '/' + action + '.html', args, this,
                    (result) => {}, (is_error) => {});
        },

        createEl: (classList, id, parentEl, prepend = 0) => {
            if (id && document.getElementById(id)) {
              return document.getElementById(id);
            }
            const el = document.createElement('div');
            el.classList.add(...classList.filter(n => n));
            if (id) {
              el.id = id;
            }
            if (typeof parentEl === 'string') {
              parentEl = document.getElementById(parentEl);
            }
            if (parentEl) {
              if (prepend) {
                parentEl.prepend(el);
              } else {
                parentEl.append(el);
              }
            }
            return el;
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

            MakeSpacesClickable: function (moves, blocker, blockerTokens, cQuad) {
                removePossible();
                console.dir(moves);
                
                for (let i = 0; i < moves.length; i++) {
                    highlightSpace = document.getElementById("space_"+moves[i]);
                    highlightSpace.classList.add("possibleMove");
                }
                if (blocker != 6) {
                    for(let i=0;i<blockerTokens.length;i++) {
                        blockedSpace = document.getElementById("space_"+blockerTokens[(cQuad-1)][1]);
                        blockedSpace.classList.add("blockedMove");
                    }
                }
                clickableSpace = document.getElementsByClassName("possibleMove");
                Array.from(clickableSpace).forEach(
                    (elem) => elem.addEventListener("click", () => this.onClickedSpace(elem.id))
                );
                errorSpace = document.getElementsByClassName("blockedMove");
                Array.from(errorSpace).forEach(
                    (elem) => elem.addEventListener("click", () => this.onClickedSpace(elem.id))
                );
            },
        
            onUpdateQuadA: function (evt) {
                pID = this.getActivePlayerId();
                this.azFaithCounter[pID].incValue(-1);
                this.ajaxCallWrapper("updateQuadA",);
                this.updatePageTitle(); 
            },
    
            onUpdateQuadC: function (evt) {
                pID = this.getActivePlayerId();
                this.cathFaithCounter[pID].incValue(-1);
                dojo.stopEvent( evt );
                this.ajaxCallWrapper("updateQuadC",);
                this.updatePageTitle();         
            },

            onResetTime: function (evt) {
                this.peopleCounter[pID].incValue(-1);
                this.timeCounter[pID].toValue(1);
                dojo.stopEvent( evt );
                this.ajaxCallWrapper("resetTime",);
                this.updatePageTitle(); 
            },
    
            onClickedSpace: function (bID) {
                boardID = bID;
                this.ajaxCallWrapper("clickedSpace", {boardID});
            },

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your flowerwar.game.php file.
        
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

            dojo.subscribe( 'otherUpdateQuad', this, "notif_otherUpdateQuad" );
            dojo.subscribe( 'selfUpdateQuad', this, "notif_selfUpdateQuad" );
            this.notifqueue.setIgnoreNotificationCheck( 'otherUpdateQuad', (notif) => (notif.args.player_id == this.player_id) );
            dojo.subscribe( 'otherResetTime', this, "notif_otherResetTime" );
            dojo.subscribe( 'selfResetTime', this, "notif_selfResetTime" );
            this.notifqueue.setIgnoreNotificationCheck( 'otherResetTime', (notif) => (notif.args.player_id == this.player_id) );
            dojo.subscribe( 'BlockedSpace', this, "notif_BlockedSpace" );
            dojo.subscribe( 'moveTokenOther', this, "notif_moveTokenOther" );
            dojo.subscribe( 'moveTokenSelf', this, "notif_moveTokenSelf" );
            this.notifqueue.setIgnoreNotificationCheck( 'moveTokenOther', (notif) => (notif.args.player_id == this.player_id) );
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        notif_selfUpdateQuad: function (notif) {
            MakeSpacesClickable(notif.args.possibleMoves, notif.args.blocker, notif.args.blockerTokens, notif.args.cQuad);
            this.updatePageTitle();
        },

        notif_selfResetTime: function (notif) {
            MakeSpacesClickable(notif.args.possibleMoves, notif.args.blocker, notif.args.blockerTokens, notif.args.cQuad);
            this.updatePageTitle();
        },
        
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

        notif_otherUpdateQuad: function (notif) {
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

              
        notif_BlockedSpace: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_moveTokenOther: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

        notif_moveTokenSelf: function (notif) {
            messageContainer = document.getElementById("messageContainer");
            messageContainer.classList.remove("hidden");
            logText = this.format_string_recursive(notif.log, notif.args);
            document.getElementById("message-text").innerText = logText;
        },

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
