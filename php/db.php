<?php 
// DATABASE HANDELER
class DBHandler
{
	private $connection = "";
	private $config = "";
	
	function __construct($config)
	{
		try {
			$this->config = $config;
			$this->connection = new PDO('mysql:host='.$config["DB_host"], $config["user_name"], $config["password"]);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->checkDB();
		} catch (Exception $e) {
			throw new Exception( "Sorry, There was an issue with the database: ". $e->getMessage() );
		}
	}

	// CHECK DATABASE AND TABLES. CREAT IF DON'T ALREADY EXIST
	private function checkDB(){
		$this->connection->query("CREATE DATABASE IF NOT EXISTS " . $this->config["DB_name"]);
		$this->connection->query("use ".$this->config["DB_name"]);

		$query = "CREATE TABLE IF NOT EXISTS games (
					 id INT NOT NULL AUTO_INCREMENT , 
					 selected_number INT NOT NULL , 
					 secret_number INT NOT NULL , 
					 status VARCHAR(50) NOT NULL , 
					 session VARCHAR(50) NOT NULL ,
					 php_datetime VARCHAR(50) NOT NULL , 
					 datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
					 PRIMARY KEY (`id`))";

		$this->connection->query($query);

		$query = "CREATE TABLE IF NOT EXISTS trials (
			id INT NOT NULL AUTO_INCREMENT , 
			game_id INT NOT NULL , 
			guessed_number INT NOT NULL , 
			result VARCHAR(50) NOT NULL , 
			php_datetime VARCHAR(50) NOT NULL , 
			datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
			PRIMARY KEY (`id`))";

		$this->connection->query($query);
	}

	// LOG TRIALS
	public function LogPlay($data){
		if($data["result"] == "correct"){
			$this->WinGame();
		}
		$stmt = $this->connection->prepare("INSERT INTO trials (game_id, guessed_number, result, php_datetime) 
								VALUES (:game_id, :guessed_number, :result, '".date('Y-m-d H:i:s')."')");
		$stmt->bindParam(':game_id', $data["game_id"]);
		$stmt->bindParam(':guessed_number', $data["guessed_number"]);
		$stmt->bindParam(':result', $data["result"]);
		$stmt->execute();
	}

	// LOG NEW GAME
	public function LogNewGame($data){
		$stmt = $this->connection->prepare("INSERT INTO games (selected_number, secret_number, status, session, php_datetime) 
								VALUES (:selected_number, :secret_number, 'active', '".session_id()."', '".date('Y-m-d H:i:s')."')");
		$stmt->bindParam(':selected_number', $data["selected_number"]);
		$stmt->bindParam(':secret_number', $data["secret_number"]);
		return $stmt->execute();
	}

	// LOG WIN
	public function WinGame(){
		$this->connection->query("UPDATE games SET status='won', php_datetime = '".date('Y-m-d H:i:s')."' WHERE status='active' AND session = '".session_id()."'");
	}

	// LOG ABANDON
	public function AbandonGame(){
		$this->connection->query("UPDATE games SET status='abandoned', php_datetime = '".date('Y-m-d H:i:s')."' WHERE status='active' AND session = '".session_id()."'");
	}

	//GET ACTIVE GAME DATA
	public function GetActiveGameData(){
		$result = $this->connection->query("SELECT * FROM games WHERE status='active' AND session = '".session_id()."'");
		return $result->fetchObject();
	}

	// GET STATISTICS
	public function GetStats() {
		// GET ALL STATS
		$query = "
		SELECT t.game_id, g.status, g.selected_number, g.secret_number, t.guessed_number, t.result, DATE_FORMAT(t.datetime, '%a %b %d, %H:%i:%S') AS datetime, 
			(SELECT TIMESTAMPDIFF(SECOND, MIN(datetime), MAX(datetime)) AS seconds 
			FROM trials 
			WHERE game_id=t.game_id 
			GROUP BY game_id 
			ORDER BY game_id) AS seconds
		FROM games g LEFT JOIN trials t ON g.id = t.game_id 
		ORDER BY t.datetime
		";
		$result = $this->connection->query($query)->fetchAll();

		return $result;
	}

}