<?php

/**
 * The base model the application is going to use. All other models extend this one
 *
 * @package Boardwalk/Models
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace App\Models;

use Exception;
use InvalidArgumentException;
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
	 * Closes the connection on destruction of the object
	 */
	public function __destruct()
	{
		$this->connection->close();
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
	 * Rekeys a table with a lot of "empty" rows.
	 * DO NOT USE THIS LIGHTLY!
	 *
	 * @return Model
	 */
	public function rekey($idcolumn = 'id')
	{
		$table = $this->secure($this->table);
		$idcolumn = $this->secure($idcolumn);
		$this->connection->query('SET @count = 0');
		$this->connection->query('UPDATE `' . $table . '` SET `' . $table . '`.`' . $idcolumn . '` = @count := @count + 1');
		$this->connection->query('ALTER TABLE `' . $table . '` AUTO_INCREMENT = 1');

		return $this;
	}

	/**
	 * Closes the current connection to the DB
	 *
	 * @return mixed
	 */
	public function close()
	{
		return $this->connection->close();
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
	 * Fetches all rows from a table and returns the mysqli_result
	 *
	 * @param bool $distinct use SELECT DISTINCT
	 * @return mysqli_result
	 */
	private function fetch()
	{
		$query = 'SELECT * FROM `' . $this->table . '`';
		$result = $this->connection->query($query);

		if(!$result)
		{
			throw new Exception($this->connection->error, $this->connection->errno);
		}

		return $result;
	}

	/**
	 * Fetches all rows from a table and returns them
	 *
	 * @see fetch()
	 * @return array
	 */
	public function fetchAll()
	{
		$resultset = $this->fetch();
		$output = array();

		while($row = $resultset->fetch_assoc())
		{
			$output[] = (object)$row;
		}

		return $output;
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

	/**
	 * The update method takes the attributes defined in $attributes and builds an update query with them,
	 * executes it and returns the result
	 *
	 * @param string $whereKey The 1st part of the WHERE clause
	 * @param string $whereOperator The 2nd part of the WHERE clause (the operator)
	 * @param string $whereValue The 3rd part of the WHERE clause
	 * @return mixed
	 */
	public function update($whereKey, $whereOperator, $whereValue, $limitStart = null, $limitEnd = null)
	{
		$query = 'UPDATE `' . $this->table . '` SET ';

		if(count($this->attributes) > 0)
		{
			foreach($this->attributes as $key => $value)
			{
				$key = $this->secure($key);
				$value = $this->secure($value);
				$query .= "`" . $key . "` = '" . $value . "', ";
			}

			$query = rtrim(rtrim($query), ',') . ' WHERE `' . $whereKey . '` ' . $whereOperator . " '" . $whereValue . "' ";

			if(!is_null($limitStart) && is_int($limitStart) && !is_null($limitEnd) && is_int($limitEnd))
			{
				if(is_int($limitStart) && is_int($limitEnd))
				{
					$query .= 'LIMIT ' . $limitStart . ', ' . $limitEnd;
				}
				else
				{
					throw new InvalidArgumentException(__METHOD__ . ' expects integers for limitStart and limitEnd but was given a ' . gettype($limitStart) . ' and a ' . gettype($limitEnd));
				}
			}
			
			if(!is_null($limitStart) && is_null($limitEnd))
			{
				if(is_int($limitStart))
				{
					$query .= 'LIMIT ' . $limitStart;
				}
				else
				{
					throw new InvalidArgumentException(__METHOD__ . ' expects an interger for limitStart but was given a ' . gettype($limitStart));
				}
			}
			
			$result = $this->connection->query($query);

			if(!$result)
			{
				throw new Exception($this->connection->error, $this->connection->errno);
			}

			return $result;
		}
		else
		{
			throw new Exception($this->connection->error, $this->connection->errno);
		}
	}

	/**
	 * This deletes everything from $this->table 
	 *
	 * @param string $whereKey The 1st part of the WHERE clause
	 * @param string $whereOperator The 2nd part of the WHERE clause (the operator)
	 * @param string $whereValue The 3rd part of the WHERE clause
	 * @param int $limit The limit, optional
	 * @throws Exception
	 * @return mysqli_result
	 */
	public function delete($whereKey, $whereOperator, $whereValue, $limit = null)
	{
		$raw = "DELETE FROM `%s` WHERE `%s` %s '%s' ";

		if(!is_null($limit))
		{
			$raw .= 'LIMIT %s';
			$limit = $this->secure($limit);
		}

		$table = $this->secure($this->table);
		$whereKey = $this->secure($whereKey);
		$whereOperator = $this->secure($whereOperator);
		$whereValue = $this->secure($whereValue);
		$query = is_null($limit) ? sprintf($raw, $table, $whereKey, $whereOperator, $whereValue) : sprintf($raw, $table, $whereKey, $whereOperator, $whereValue, $limit);
		$result = $this->connection->query($query);

		if(!$result)
		{
			throw new Exception($this->connection->error, $this->connection->errno);
		}

		return $result;
	}

	/**
	 * Deletes _all_ records from $this->table
	 *
	 * @throws Exception
	 * @return mysqli_result
	 */
	public function deleteAll()
	{
		$query = 'DELETE FROM `' . $this->secure($this->table) . '`';
		$result = $this->connection->query($query);

		if(!$result)
		{
			throw new Exception($this->connection->error, $this->connection->errno);
		}

		return $result;
	}
}