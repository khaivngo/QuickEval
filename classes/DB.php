<?php
/**
 * Database wrapper class, that uses the singelton pattern.
 */
class DB
{
	private static $instance = null; # holds a instance of this class
	private $pdo;
	private $query;
	private $error = false;
	private $results; # holds the result from the last query run
	private $count = 0; # number of results from the last query
	private $last_inserted_id; # id of the last query run

	/**
	 * Establish the database connection when this object is instantiated.
	 */
	private function __construct()
	{
		try {
			$this->pdo = new PDO("mysql:host=127.0.0.1;dbname=mariusp", "root", "");
		} catch(PDOException $e) {
			exit($e->getMessage());
		}
	}

	/**
	 * The first time this is called it creates an instance of this object and assign it to a static propery of this class.
	 * Which means we do not create a new instance the next time we use this class, we use the same.
	 * That way the __constructor only runs once and we do not have to connect to the database multiple times.
	 */
	public static function instance()
	{
		if (! self::$instance) {
			self::$instance = new DB();
		}
		return self::$instance;
	}

	/**
	 * This runs our SQL queries on the database.
	 * It binds the paramters we give it, if any, and runs the query and fetch the results
	 * as an array of objects into this class's results property.
	 *
	 * @param: $sql, database query.
	 * @param: $params, optional values to bind.
	 */
	public function run_query($sql, $params = [])
	{
		$this->error = false; # reset the error before each query run

		if ($this->query = $this->pdo->prepare($sql)) {
			# bind the parameters, if we have any
			if (count($params)) {
				$i = 1; # we cannot use $key when we bind the values because it starts on 0
				foreach ($params as $param) {
					$this->query->bindValue($i, $param);
					$i++;
				}
			}

			if ($this->query->execute()) {
				$this->last_inserted_id = $this->pdo->lastInsertId();
				# update the results property with the stuff we get back from the database
				$this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
				# and update the count property with the number of results
				$this->count = $this->query->rowCount();
			} else {
				$this->error = true;
			}
		}

		# return this object so we can chain on methods
		return $this;
	}

	# Setters and getters.
	public function get_results() {
		return $this->results;
	}

	public function get_count() {
		return $this->count;
	}

	public function get_last_inserted_id() {
		return $this->last_inserted_id;
	}

	public function get_error() {
		return $this->error;
	}

} # End of Database class

?>
