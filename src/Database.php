<?php

class Database {
	private $databaseConnection;
	private $queryResult;
	private $numberOfResultsTotal, $numberOfResultsLeft;
	
	public function __construct() {
		global $databaseConfiguration;
		
		$this->databaseConnection = new mysqli($databaseConfiguration['host'],
		                                       $databaseConfiguration['user'],
		                                       $databaseConfiguration['password'],
		                                       $databaseConfiguration['database']);
		
		if(mysqli_connect_errno()) {
			throw new Exception('Cannot connect to database');
		}
	}
	
	public function query($query) {
		$this->queryResult = $this->databaseConnection->query($query);
		echo $this->databaseConnection->error;
		
		if(isset($this->queryResult->num_rows)) {
			$this->numberOfResultsLeft = $this->numberOfResultsTotal = $this->queryResult->num_rows;
		}
	}
	
	public function getTotalNumberOfResults() {
		return $this->numberOfResultsTotal;
	}
	
	public function getTotalNumberOfResultsLeft() {
		return $this->numberOfResultsLeft;
	}
	
	public function isResultAvailable() {
		return $this->numberOfResultsLeft > 0;
	}
	
	public function fetchRow() {
		$this->numberOfResultsLeft--;
		return $this->queryResult->fetch_assoc();
	}
	
	public function __destruct() {
		$this->databaseConnection->close();
	}
}