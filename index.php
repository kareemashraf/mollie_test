	

<?php
$Game = new Game(); //initiate new call with each request
$Game->Intro(); // display the HTML Intro
$Game->createDominoes(); 
$Game->start();

class Game {

	private $Player1 = array(); // an array to contain player1's tiles
	private $Player2 = array(); // an array to contain player2's tiles
	private $Players = array('Alice','Bob'); 
	private $Remains = array(); //the remaining tiles to draw from
	private $board = array(); //the board made by playing rounds
	private $frontTile = array(); //first tile on the board
	private $backTile = array(); //last tile on the board
	Private $nowPlaying;
	private $front; //front tile value
	private $back;  //back tile value
	
	/**
	 * Create the Dominoes 28 tiles as a multidimensional array
	 *
	 */
	public function createDominoes()
		{
			$board = array(0,1,2,3,4,5,6);
			$dominoes = array();
			for ($i = 0; $i <= 6; $i++){
				unset($board[$i - 1]);
				foreach($board as $key => $value){
					$dominoes[] = [$value, $i];
				}
			}

			shuffle($dominoes); //to randomly shuffle all the domioes tiles
			list($this->Player1, $this->Player2) = array_chunk($dominoes, 7); //give each player 7 tiles
			$this->remains = array_slice($dominoes, 14, 27); // the rest of the tiles
		}

	/**
	 * start the game by picking a randome player and a randome tile to start with
	 *
	 */	
	public function start()
		{

			// start with a randome Player

			$players = $this->Players;
			shuffle($players);
			$this->nowPlaying = $players[0];
			$firstTile = $this->remains[0];
			unset($this->remains[0]); //remove the tile from the remaining tiles
			$this->board = [$firstTile];

			echo "Game starting with first tile: " . $this->createTile($firstTile) . "</br>";
			$this->front = $firstTile[0];  
			$this->back = $firstTile[1];  
			$this->frontTile = $firstTile; 
			$this->backTile = $firstTile; 
			$this->playTurn();
		}

	/**
	 * simple function to return the current player's tiles
	 *
	 */		
	public function getPlayerTieles()
		{
			if ($this->nowPlaying == 'Alice') {
				 $playerTieles = $this->Player1;
			}else{
				$playerTieles = $this->Player2;
			}

			return $playerTieles;
		}

	/**
	 * simple function to shift turns between players and control the game round
	 *
	 */
	public function playTurn()
		{

			$playerTieles = $this->getPlayerTieles();

			// loop through the player's tiles to check availability
			foreach($playerTieles as $key => $value) {
				$Turn = $this->matchTiles($value, $this->front, $this->back, $key, $playerTieles);
				if ($Turn){
					break;
				}
			}

			if (!$Turn && !empty($this->remains)){
				$takeTile = current($this->remains);
				$key = array_search($takeTile, $this->remains);
				unset($this->remains[$key]);
				if ($this->nowPlaying == 'Alice'){
					$this->Player1 = array_merge([$takeTile], $this->Player1);
				}else{
					$this->Player2 = array_merge([$takeTile], $this->Player2);
				}

				echo $this->nowPlaying . " Can't play, drawing a tile " . $this->createTile($takeTile) . "</br>";

				// $this->reverse();
				$this->playTurn();
			}

			if ($Turn && !empty($playerTieles)){
				$this->reverse();
				$this->playTurn();
			}

			if ((empty($playerTieles)) || (!$Turn && empty($this->remains))){ 
				//in case the reamining tiles are over, who wins the round is the one with less tiles
				if (count($this->Player1) < count($this->Player2)){ 
					die("Player " . $this->Players[0] . " has won!");  
				}else{
					die("Player " . $this->Players[1] . " has won!");
				}
			}

		}

	/**
	 * Reverse the current player to play a round and the Tiles
	 *
	 */	
	public function reverse()
		{
			$playerTieles = array_search($this->nowPlaying, $this->Players);
			$players = $this->Players;
			if ($playerTieles == 0){
				$playerTieles = $this->Player2;
				unset($players[0]);
				$this->nowPlaying = $players[1];
			}else{
				$playerTieles = $this->Player1;
				unset($players[1]);
				$this->nowPlaying = $players[0];
			}
		}

	/**
	 * Reverse the current player to play a round and the Tiles
	 *
	 */	
	public function matchTiles($value, $front, $back, $key, $playerTieles)
		{
			if ($value[0] == $front){
				$this->front = $value[1];
				array_unshift($this->board, $value); //move the tile to the front of the board
				if ($this->nowPlaying == 'Alice') {
					unset($this->Player1[$key]); //remove the tile from the player1
				} else {
					unset($this->Player2[$key]); //remove the tile from the player2
				}

				echo $this->nowPlaying . " Plays " . $this->createTile($value) . " to connect to tile " . $this->createTile($this->frontTile) . " on the board.</br>";
				echo "board is now : " . $this->createboardTile($this->board) . "</br>";
				$this->frontTile = $value;
				return true;

			}elseif ($value[0] == $back) {
				$this->back = $value[1];
				array_push($this->board, $value); //move the tile to the end of the board
				if ($this->nowPlaying == 'Alice') {
					unset($this->Player1[$key]); 
				}else {
					unset($this->Player2[$key]); 
				}

				echo $this->nowPlaying . " Plays " . $this->createTile($value) . " to connect to tile " . $this->createTile($this->backTile) . " on the board.</br>";
				echo "board is now : " . $this->createboardTile($this->board) . "</br>";
				$this->backTile = $value;
				return true;
				
			} elseif ($value[1] == $front){
				$this->front = $value[0];
				array_unshift($this->board, $value);
				if ($this->nowPlaying == 'Alice'){
					unset($this->Player1[$key]); 
				}else {
					unset($this->Player2[$key]); 
				}

				echo $this->nowPlaying . " Plays " . $this->createTile($value) . " to connect to tile " . $this->createTile($this->frontTile) . " on the board.</br>";
				echo "board is now : " . $this->createboardTile($this->board) . "</br>";
				$this->frontTile = $value;
				return true;
				}
			elseif ($value[1] == $back){
				$this->back = $value[0];
				array_push($this->board, $value);
				if ($this->nowPlaying == 'Alice'){
					unset($this->Player1[$key]); 
				}else{
					unset($this->Player2[$key]); 
				}

				echo $this->nowPlaying . " Plays " . $this->createTile($value) . " to connect to tile " . $this->createTile($this->backTile) . " on the board.</br>";
				echo "board is now : " . $this->createboardTile($this->board) . "</br>";
				$this->backTile = $value;
				return true;
			}else {
				return false; // could not be matched (the player has to draw)
			}
		}

	/**
	 * Simple function to create a tile based on front and back
	 *
	 */
	public function createTile($tile)
		{
			return "<" . $tile[0] . ":" . $tile[1] . ">";
		}

	/**
	 *  create the board tiles based on front and back values to always connect it by similar values from the 2d array
	 *
	 */
	public function createboardTile($board)
		{

			foreach($board as $key => $val) {
				if ($key == 0) {
					$next = $board[1];
					if ($next[0] == $val[0]) {
						$b = "<" . $val[1] . ":" . $val[0] . "> ";
						$board[$key] = [$val[1], $val[0]];
					}else{
						$b = "<" . $val[0] . ":" . $val[1] . "> ";
						$board[$key] = [$val[0], $val[1]];
					}
				}else{
					$last = $board[$key - 1];
					if ($last[0] == $val[0]){
						$b.= "<" . $val[0] . ":" . $val[1] . "> ";
					}elseif ($last[0] == $val[1]){
						$b.= "<" . $val[1] . ":" . $val[0] . "> ";
					}elseif ($last[1] == $val[1]){
						$b.= "<" . $val[1] . ":" . $val[0] . "> ";
					}else{
						$b.= "<" . $val[0] . ":" . $val[1] . "> ";
					}
				}
			}

			return $b;
		}

	public function Intro()
		{
			echo "<center><h1>Test Assignment @ Mollie</h1> <a href='http://ec2-34-252-123-254.eu-west-1.compute.amazonaws.com/'> <button>refresh</button></a></br> 
			<h3>Dominoes programming exercise</h3>
			<span>Kindly find the source code in the following GitHub Repository <a target='_blank' href='https://github.com/kareemashraf/mollie_test'> https://github.com/kareemashraf/mollie_test </a></span>
			<hr></center>";
		}

}

?>