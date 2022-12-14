
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- Flower War. Original game © Ice 9 Designs. Designed and implemented by Tug Brice. Designsbyice9@gmail.com
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

CREATE TABLE IF NOT EXISTS `card` (
  `card_id` int(10) NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  `turn` int(4) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `resources` (
    `player_id` int(3) NOT NULL,
    `Az` int(4) NOT NULL,
    `Cath` int(4) NOT NULL,
    `People` int(4) NOT NULL,
    `Time` int(2) NOT NULL,
    `turn` int(4) NOT NULL,
    `charID` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `tokens` (
    `player_id` int(2) NOT NULL,
    `tokenID` int(3) NOT NULL,
    `boardID` int(2) NOT NULL,
    `Quad` int(2) NOT NULL,
    `Space` int(2) NOT NULL,
    `turn` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;