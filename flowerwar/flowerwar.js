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
            this.blocker = 0;
            this.azTemple = -1;
            this.cathTemple = -1;
            this.apocFlag = 0;
            this.azFlag = false;
            this.cathFlag = false;
            this.colorArray=[];
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
                this.azFaithCounter[player_id].setValue(gamedatas.resources[i][0]);

                // Cath Counter
                this.cathFaithCounter[player_id] = new ebg.counter();
                this.cathFaithCounter[player_id].create('cathFaithCounter'+player_id);
                this.cathFaithCounter[player_id].setValue(gamedatas.resources[i][1]);

                // People Counter
                this.peopleCounter[player_id] = new ebg.counter();
                this.peopleCounter[player_id].create('peopleCounter'+player_id);
                this.peopleCounter[player_id].setValue(gamedatas.resources[i][2]);

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
            this.blocker = 0;
            this.blockerTokens = [];
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
            this.cathFlag = gamedatas.cathFlag;;

            switch(this.blocker) {
                case 6:
                break;
                default:
                    let b1 = this.blocker -1;
                    let b2 = this.blocker - (-4);
                    let b3 = this.blocker - (-9);
                    let b4 = this.blocker - (-14);
                    this.blockerTokens = [[5,b1],[6,b2],[7,b3],[8,b4]];
            }
            

            // set up board spaces
            clearBoard();
            createBoard(this.globalTokens, this.blocker, this.blockerTokens, this.colorArray, this.globalTerrain, this.globalBoard);

            // set up Temple
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
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
           
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
        
        ajaxCallWrapper: function (action, args) {
                if (!args) {
                    args = {};
                }
                args.lock = true;
                this.ajaxcall('/' + this.game_name + '/' + this.game_name + '/' + action + '.html', args, this,
                    (result) => {}, (is_error) => {});
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
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/flowerwar/flowerwar/myAction.html", { 
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
