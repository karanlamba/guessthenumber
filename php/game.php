<?php 
// GAME CLASS
class Game
{
	private $max_number;
	private $secret_number;
	private $db;
	private $game_id;

	function __construct($db)
	{
		$this->db = $db;
		// CHECK ACTIVE GAME SESSION
		$activegame = $this->db->GetActiveGameData();
		if ($activegame){
			$this->game_id = $activegame->id;
			$this->max_number = $activegame->selected_number;
			$this->secret_number = $activegame->secret_number;
		}
	}

	// START A NEW GAME
	public function Start ($max_number) {
		if ($this->game_id){
			die("there is an active game with id: " . $this->game_id);
		}

		if (is_int($max_number) && $max_number > 1){
			$this->max_number = $max_number;
			$this->secret_number = rand(1, $max_number);

			//Log start of game
			return $this->db->LogNewGame(["selected_number"=>$max_number, "secret_number"=>$this->secret_number]);

		} else {
			throw new Exception( "Please enter a number greater then 1" );
		}
	}

	// GUESS THE NUMBER
	public function Guess ($guess_number) {
		if (is_int($guess_number)){
			if ($guess_number > $this->secret_number) {
				$result = "over";
			}else if ($guess_number < $this->secret_number) {
				$result = "under";
			} else {
				$result = "correct";
			}

			$this->db->LogPlay(["game_id"=>$this->game_id, "guessed_number"=>$guess_number, "result"=>$result]);

		} else {
			throw new Exception( "Please enter a number" );
		}

		return $result;
	}

	// ABANDON
	public function End () {
		$this->db->AbandonGame();
	}

	// GET ACTIVE GAME DATA
	public function getGameData(){
		return $this->max_number;
	}

	// GET STATISTICS
	public function getGameStats(){
		return $this->db->GetStats();
	}

}