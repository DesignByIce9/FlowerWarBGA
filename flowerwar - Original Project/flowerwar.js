/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Flower War. Original game by Ice 9 Games. Designed and developed by Tug Brice. Designsbyice9@gmail.com
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
  "ebg/counter"
],
function (dojo, declare) {
  return declare("bgagame.flowerwar", ebg.core.gamegui, {
      constructor: function(){
          console.log('flowerwar constructor');
            
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
          
          // Setting up player boards
          for( var player_id in gamedatas.players )
          {
            var player = gamedatas.players[player_id];
            var player_board_div = $('player_board_'+player_id);
            dojo.place( this.format_block('jstpl_player_board', player ), player_board_div );
            this.azFaithCounter[player_id] = new ebg.counter();
            this.azFaithCounter[player_id].create('azFaithCounter'+player_id);
            this.cathFaithCounter[player_id] = new ebg.counter();
            this.azFaithCounter[player_id].create('cathFaithCounter'+player_id);
            this.peopleCounter[player_id] = new ebg.counter();
            this.peopleCounter[player_id].create('peopleCounter'+player_id);
            
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
        
            player1= gamedatas.tokens.at(0).at(0);
            player2= gamedatas.tokens.at(1).at(0);
            if (gamedatas.tokens.length>=3) {
                player3= gamedatas.tokens.at(2).at(0);
            }
            if (gamedatas.tokens.length>=4) {
                player4= gamedatas.tokens.at(3).at(0);
            }
          // TODO: Set up your game interface here, according to "gamedatas"
        for(b=0;b<20;b++) {
            for(p=0;p<gamedatas.tokens.length;p++) {
                if(gamedatas.tokens.at(p).at(1) == b) { 
                    let token = document.createElement("div");        
                    token.classList.add("token");
                    token.id="token";
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
                    playerContainer = $(boardID);
                    playerContainer.appendChild(token);
                }
            }
        }
        
        let blockerID="";
        let q1Block = (gamedatas.blocker-1);
        let q2Block = q1Block+5;
        let q3Block = q1Block+10;
        let q4Block = q1Block+15;
        switch(gamedatas.blocker) {
            case 6:
            break;
            default:
                for(let i=0;i<4;i++) {
                    let blocker = document.createElement("div");        
                    blocker.classList.add("blocker");
                    if(i==0) {
                        blockerID = "blocker_"+5;
                        boardID = "space_"+q1Block;
                        console.log("bid: "+boardID);
                        blocker.id=blockerID;
                    }else if(i==1){
                        blockerID = "blocker_"+6;
                        boardID = "space_"+q2Block;
                        console.log("bid: "+boardID);
                        blocker.id=blockerID;
                    }else if(i==2){
                        blockerID = "blocker_"+7;
                        boardID = "space_"+q3Block;
                        console.log("bid: "+boardID);
                        blocker.id=blockerID;
                    }else if(i==3){
                        blockerID = "blocker_"+8;
                        boardID = "space_"+q4Block;
                        console.log("bid: "+boardID);
                        blocker.id=blockerID;
                    }
                    playerContainer = $(boardID);
                    console.dir(boardID);
                    playerContainer.appendChild(blocker);
                }                
            }

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

            case 'pickSpace':


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
/*               
               Example:

               case 'myGameState':
                  
                  // Add 3 action buttons in the action status bar:
                  
                  this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                  this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                  this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                  break;
*/
                case 'pickSpace':
                    addActionButton("advQuad_button", "Move to the next Quadrant", 'onAdvQuad');
                    addActionButton("resetTime_button", "Reset Time", 'onResetTime');
                break;
                    
              }
          }
      },        

      ///////////////////////////////////////////////////
      //// Utility methods
      
      /*
      
          Here, you can defines some utility methods that you can use everywhere in your javascript
          script.
      
      */


      ///////////////////////////////////////////////////
      //// Player's action
      
      /*
      
          Here, you are defining methods to handle player's action (ex: results of mouse click on 
          game objects).
          
          Most of the time, these methods:
          _ check the action is possible at this game state.
          _ make a call to the game server
      
      */
        onAdvQuad: function (evt) {
            this.ajaxcallwrapper('onAdvQuad');
        }
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