<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * FlowerWarThree implementation : © Tug
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * flowerwarthree.action.php
 *
 * FlowerWarThree main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/flowerwarthree/flowerwarthree/myAction.html", ...)
 *
 */
  
  
  class action_flowerwarthree extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "flowerwarthree_flowerwarthree";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
  	
  	// TODO: defines your action entry points there

    public function updateQuadA () {
      self::setAjaxMode();
      $this->game->quadUpdate('A');
      self::ajaxResponse();
    }

    public function updateQuadC () {
      self::setAjaxMode();
      $this->game->quadUpdate('C');
      self::ajaxResponse( );      
    }

    public function resetTime () {
      self::setAjaxMode();
      $this->game->updateTime();
      self::ajaxResponse( );    
    }

    public function clickedSpace () {
      self::setAjaxMode();
      $boardID =self::getArg("boardID", AT_alphanum, true);
      $this->game->clickedSpace($boardID);
      self::ajaxResponse( );
    }

    public function faithCardChoice() {
      self::setAjaxMode();
      $faithChoice = self::getArg("faithCardChoice", AT_alphanum, true);
      $this->game->cardChoice($faithChoice, "F");
      self::ajaxResponse( );
    }

    public function templeCardChoice() {
      self::setAjaxMode();
      $templeChoice = self::getArg("templeCardChoice", AT_alphanum, true);
      $this->game->cardChoice($templeChoice, "T");
      self::ajaxResponse( );
    }
    
    /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */

  }
  

