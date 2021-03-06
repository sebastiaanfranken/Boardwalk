<?php

/**
 * The base model the application is going to use. All other models extend this one
 *
 * @package Boardwalk/Models
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;
use InvalidArgumentException;
use Boardwalk\Exceptions\NotImplementedException;
use Boardwalk\Exceptions\SQLException;
use Boardwalk\Exceptions\SQLConnectionException;
use mysqli;
use stdClass;
use Boardwalk\Utilities\Text;
use Boardwalk\Utilities\GenericObject;

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
	 * @var int $tableColumnCount Count the number of columns in the table
	 * @access protected
	 */
	protected $tableColumnCount = 0;

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
	 * @throws Boardwalk\Exceptions\SQLConnectionException
	 * @return void
	 */
	public function __construct()
	{
		$dbConfig = require config() . 'db.php';

		/**
		 * @todo Abstract this into multiple drivers so we can use more DBMS's
		 */
		$this->connection = new mysqli(
			$dbConfig['hostname'],
			$dbConfig['username'],
			$dbConfig['password'],
			$dbConfig['database']
		);

		if($this->connection->connect_errno > 0)
		{
			throw new SQLConnectionException($this->connection);
		}

		$calledClass = explode('\\', get_called_class());
		$this->table = strtolower(end($calledClass)) . 's';

		// $columnCountQuery = "SELECT COUNT(COLUMN_NAME) AS `counter` FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = '%s'";
		// $query = sprintf($columnCountQuery, $dbConfig['database'], $this->table);
		// $this->tableColumnCount = $this->connection->query($query)->fetch_object()->counter;

		$columnCountQuery = "SELECT COUNT(`COLUMN_NAME`) AS `counter` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?";
		$query = $this->connection->prepare($columnCountQuery);
		$query->bind_param('ss', $dbConfig['database'], $this->table);
		$query->execute();
		$query = $query->get_result();

		$this->tableColumnCount = $query->fetch_object()->counter;

		$query->close();
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
	 * Closes the connection on destruction of the object
	 *
	 * @todo Check if the connection is still active first
	 */
	public function __destruct()
	{
		// Does nothing for now
	}

	/**
	 * Returns the values in $attributes as a stdClass
	 *
	 * @return mixed
	 */
	public function toObject()
	{
		$object = new GenericObject();

		if(count($this->attributes) > 0)
		{
			$rowCounter = 0;

			foreach($this->attributes as $row)
			{
				$object->{$rowCounter} = (object)$row;
				$rowCounter++;
			}

			return $object;
		}

		return false;
	}

	/**
	 * Returns $attributes if it's set, otherwise it returns false
	 *
	 * @return mixed
	 */
	public function toArray()
	{
		if(count($this->attributes) > 0)
		{
			return $this->attributes;
		}

		return false;
	}

	/**
	 * A wrapper around the native query() method, this has the advantage
	 * of being able to run prepared queries with vsprintf() and it also
	 * runs secure() over all arguments, with array_map()
	 *
	 * @see secure()
	 * @throws Exception
	 * @param string $query The master query
	 * @param mixed $attributes Any other attributes that are defined in the query
	 * @return mysqli_result
	 */
	public function query()
	{
		throw new NotImplementedException(__METHOD__);
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
		$output = Text::secure($input);
		$output = $this->connection->real_escape_string($output);

		return $output;
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
	 * Finds all records that have this ID set
	 *
	 * @param int $id The ID to use
	 * @param string $column The ID column, defaults to "id"
	 * @throws Boardwalk\Exceptions\SQLException
	 * @return mysqli_result
	 */
	public function find($id, $column = 'id')
	{	
		$table = $this->secure($this->table);
		$column = $this->secure($column);
		$id = $this->secure($id);
		$raw = "SELECT * FROM `%s` WHERE `%s` = '%s'";
		$query = sprintf($raw, $table, $column, $id);
		$result = $this->connection->query($query);

		if(!$result)
		{
			throw new SQLException($this->connection, $query);
		}
		else
		{
			$storage = new GenericObject();
			$records = $result->fetch_assoc();

			if(is_array($records) && count($records) > 0)
			{
				foreach($records as $key => $value)
				{
					$storage->{$key} = $value;
				}
			}

			return $storage->all();
		}
	}

	/**
	 * Does the same as find, only with a larger result set
	 *
	 * @param string $column The column to use in the query
	 * @param string $operator The operator
	 * @param string $key The key to use in the query
	 * @return mixed
	 * @throws Boardwalk\Exceptions\SQLException
	 */
	public function get($column, $operator, $key)
	{
		$table = $this->secure($this->table);
		$column = $this->secure($column);
		$operator = $this->secure($operator);
		$key = $this->secure($key);
		
		$raw = "SELECT * FROM `%s` WHERE `%s` %s '%s'";
		$query = sprintf($raw, $table, $column, $operator, $key);
		$result = $this->connection->query($query);
		
		if(!$result)
		{
			throw new SQLException($this->connection, $query);
		}
		else
		{
			$storage = new GenericObject();
			$rowCounter = 0;

			while($row = $result->fetch_assoc())
			{
				$storage->{$rowCounter} = (object)$row;
				$rowCounter++;
			}

			return $storage;
		}
	}

	/**
	 * Fetches all rows from a table and returns the mysqli_result
	 *
	 * @param bool $distinct use SELECT DISTINCT
	 * @throws Boardwalk\Exceptions\SQLException
	 * @return mysqli_result
	 */
	private function fetch()
	{
		$raw = "SELECT * FROM `%s`";
		$table = $this->secure($this->table);
		$query = sprintf($raw, $table);
		$result = $this->connection->query($query);

		if(!$result)
		{
			throw new SQLException($this->connection);
		}

		return $result;
	}

	/**
	 * Fetches all rows from a table and returns them
	 *
	 * @see fetch()
	 * @return stdClass
	 */
	public function all()
	{
		$results = $this->fetch();
		$output = new stdClass;
		$counter = 0;

		while($row = $results->fetch_assoc())
		{
			$output->$counter = new stdClass;

			foreach($row as $key => $value)
			{
				$output->$counter->$key = $value;
			}

			++$counter;
		}

		$results->free();

		return $output;
	}

	/**
	 * Fetches all rows from a table and orders them
	 *
	 * @return stdClass
	 * @throws Boardwalk\Exceptions\SQLException
	 */
	public function fetchOrderdBy($column, $ascending = true)
	{
		$raw = 'SELECT * FROM `%s` ORDER BY `%s` %s';
		$order = $ascending ? 'ASC' : 'DESC';
		$orderField = $this->secure($column);
		$query = sprintf($raw, $this->secure($this->table), $orderField, $order);
		$result = $this->connection->query($query);
		$output = new GenericObject();
		$counter = 0;

		if(!$result)
		{
			throw new SQLException($this->connection);
		}
		else
		{
			while($row = $result->fetch_assoc())
			{
				$output->$counter = new stdClass;

				foreach($row as $key => $value)
				{
					$output->$counter->$key = $value;
				}

				++$counter;
			}

			$result->free();

			return $output->all();
		}
	}

	/**
	 * The create method takes the attributes defined in $attributes and builds a query with them,
	 * executes it and returns the result
	 *
	 * @throws Boardwalk\Exceptions\SQLException
	 * @throws Exception
	 * @return mixed
	 */
	public function create()
	{
		if(count($this->attributes) > 0)
		{
			$raw = "INSERT INTO `%s` SET ";
			$args = array($this->secure($this->table));

			foreach($this->attributes as $key => $value)
			{
				if(is_array($value))
				{
					continue;
				}
				else
				{
					$raw .= "`%s` = '%s', ";
					$args[] = $key;
					$args[] = $value;
				}
			}

			$raw = rtrim(rtrim($raw), ',');
			$query = vsprintf($raw, $args);
			$result = $this->connection->query($query);

			if(!$result)
			{
				throw new SQLException($this->connection);
			}

			return $result;
		}
		else
		{
			throw new Exception(sprintf('No attributes passed to the model at <em>%s</em>', __METHOD__));
		}
	}

	/**
	 * The update method takes the attributes defined in $attributes and builds an update query with them,
	 * executes it and returns the result. It also checks if the result has a 'id', if so it does an update,
	 * if it doesn't it does a create.
	 *
	 * @throws Boardwalk\Exceptions\SQLException
	 * @throws Exception
	 * @return mixed
	 */
	public function update()
	{
		if(count($this->attributes) > 0)
		{
			if(array_key_exists('id', $this->attributes))
			{
				$raw = "UPDATE `%s` SET ";
				$args = array($this->secure($this->table));

				foreach($this->attributes as $key => $value)
				{
					$raw .= "`%s` = '%s', ";
					$args[] = $key;
					$args[] = $value;
				}

				$raw = rtrim(rtrim($raw), ',') . " WHERE `id` = '%s'";
				$args[] = $this->secure($this->attributes['id']);
				$query = vsprintf($raw, $args);
				$result = $this->connection->query($query);

				if(!$result)
				{
					throw new SQLException($this->connection);
				}

				return $result;
			}
			else
			{
				throw new Exception(sprintf('Trying to call <em>%s</em> on a non-existent result.', __METHOD__));
			}
		}
		else
		{
			throw new SQLException($this->connection);
		}
	}

	/**
	 * This deletes everything from $this->table 
	 *
	 * @param string $whereKey The 1st part of the WHERE clause
	 * @param string $whereOperator The 2nd part of the WHERE clause (the operator)
	 * @param string $whereValue The 3rd part of the WHERE clause
	 * @param int $limit The limit, optional
	 * @throws SQLException
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
			throw new SQLException($this->connection);
		}

		return $result;
	}

	/**
	 * Deletes _all_ records from $this->table
	 *
	 * @throws SQLException
	 * @return mysqli_result
	 */
	public function deleteAll()
	{
		$query = 'DELETE FROM `' . $this->secure($this->table) . '`';
		$result = $this->connection->query($query);

		if(!$result)
		{
			throw new SQLException($this->connection);
		}

		return $result;
	}

	/**
	 * Count all rows in a table
	 *
	 * @param string $column The column to count, uses 'id' as standard
	 * @return int
	 * @throws Boardwalk\Exceptions\SQLException
	 */
	public function count($column = 'id')
	{
		$raw = 'SELECT COUNT(`%s`) AS `counter` FROM `%s`';
		$count = $this->secure($column);
		$table = $this->secure($this->table);
		$query = sprintf($raw, $count, $table);
		$result = $this->connection->query($query);

		if(!$result)
		{
			throw new SQLException($this->connection);
		}

		return $result->fetch_object()->counter;
	}

	/**
	 * Count all distinct rows in a table
	 *
	 * @param string $column The column to count
	 * @return int
	 * @throws InvalidArgumentException if there is no $column
	 * @throws Boardwalk\Exceptions\SQLException
	 */
	public function countDistinct($column = null)
	{
		if(is_null($column))
		{
			throw new InvalidArgumentException(__METHOD__ . ' expects a string but was given nothing.');
		}
		else
		{
			$raw = 'SELECT COUNT(DISTINCT `%s`) AS `counter` FROM `%s`';
			$count = $this->secure($column);
			$table = $this->secure($this->table);
			$query = sprintf($raw, $count, $table);
			$result = $this->connection->query($query);

			if(!$result)
			{
				throw new SQLException($this->connection);
			}

			return $result->fetch_object()->counter;
		}
	}
}
