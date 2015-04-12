<?php

/**
 * The base model the application is going to use. All other models extend this one
 *
 * @package Boardwalk/Models
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace App\Models;

use Exception;
use mysqli;

abstract class Model
{

	/**
	 * @var mysqli $connection The connection to the DB
	 * @access protected
	 */
	protected $connection;

	/**
	 * @var string $table The table to use
	 * @access protected
	 */
	protected $table;

	/**
	 * @var array $attributes Stores attributes set after initialization through __set 
	 * @access protected
	 */
	protected $attributes = array();

	/**
	 * The constructor creates a new Database instance and tries to set the
	 * table variable depending on the called class name (plural).
	 *
	 * Alternatively you can set the table name with setTable()
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$dbConfig = require(config() . 'db.php');

		$this->connection = new mysqli(
			$dbConfig['hostname'],
			$dbConfig['username'],
			$dbConfig['password'],
			$dbConfig['database']
		);

		if($this->connection->connect_errno > 0)
		{
			throw new Exception($this->connection->connect_error, $this->connection->connect_errno);
		}

		$calledClass = explode('\\', get_called_class());
		$this->table = strtolower(end($calledClass)) . 's';
	}

	/**
	 * Runs a few security related functions over input
	 *
	 * @param string $input The base query to parse
	 * @return string
	 * @access private
	 */
	private function secure($input)
	{
		$output = strip_tags($input);
		$output = addslashes($output);
		$output = $this->connection->real_escape_string($output);

		return $output;
	}

	/**
	 * A setter for class variables that aren't defined (magic vars)
	 *
	 * @param string $key The key
	 * @param string $value The value
	 * @return Model
	 */
	public function __set($key, $value)
	{
		$this->attributes[$key] = $value;
		return $this;
	}

	/**
	 * A getter for class variables (magic vars)
	 *
	 * @param string $key The variable to get
	 * @return string|Model
	 */
	public function __get($key)
	{
		if(array_key_exists($key, $this->attributes))
		{
			return $this->attributes[$key];
		}
		
		return $this;
	}

	/**
	 * A getter for the table variable
	 *
	 * @return string
	 */
	public function getTable()
	{
		return (string)$this->table;
	}

	/**
	 * A setter for the table variable
	 *
	 * @param string $table The (new) table value to set
	 * @return Model
	 */
	public function setTable($table)
	{
		$this->table = (string)$table;
		return $this;
	}

	/**
	 * The create method takes the attributes defined in $attributes and builds a query with them,
	 * executes it and returns the result
	 *
	 * @return mixed
	 */
	public function create()
	{
		$query = 'INSERT INTO `' . $this->table . '` SET ';

		if(count($this->attributes) > 0)
		{
			foreach($this->attributes as $key => $value)
			{
				$key = $this->secure($key);
				$value = $this->secure($value);
				$query .= "`" . $key . "` = '" . $value . "', ";
			}

			$query = rtrim(rtrim($query), ',');
			$result = $this->connection->query($query);

			if(!$result)
			{
				throw new Exception($this->connection->error, $this->connection->errno);
			}

			return $result;
		}
		else
		{
			throw new Exception('No attributes passed to the model');
		}
	}
}