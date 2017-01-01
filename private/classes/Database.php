<?php

use Aura\SqlQuery\QueryFactory;

class Database extends PDO {
	private $sql;

	private $ran;

	public function __construct() {
		try {
			parent::__construct('mysql:dbname=' . getenv('DB_NAME') . ';host=' . getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'));
		} catch(PDOException $ex) {}
	}

	public function newSql($type, $columns) {
		$this->sql = (new QueryFactory('mysql'))->{'new' . ucwords($type)}();

		return $this->sql->cols($columns);
	}

	public function select(array $columns = ['*']) {
		return $this->newSql('select', $columns);
	}

	public function insert(array $columns) {
		return $this->newSql('insert', $columns);
	}

	public function execute() {
		try {
			$query = $this->prepare($this->sql->getStatement());
		} catch(PDOException $ex) {}

		$result = $query->execute($this->sql->getBindValues());

		if($result && substr($query->queryString, 0, 6) === 'SELECT') {
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
		}

		unset($query);

		return $result;
	}

	public function __destruct() {
		unset($this);
	}
}
