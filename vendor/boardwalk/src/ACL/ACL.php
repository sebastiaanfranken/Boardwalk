<?php

/**
 * Main ACL package
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 * @package Boardwalk\ACL
 * @todo Fix this package. Do _not_ use
 */

namespace Boardwalk\ACL;

use Exception;
use Boardwalk\Exceptions\TypeException;

class ACL
{
	/*
	 * Allow constant
	 */
	const ALLOW = 'allow';

	/*
	 * Deny constant
	 */
	const DENY = 'deny';

	/*
	 * Action all constant
	 */
	const ACTION_ALL = 'all';

	/*
	 * Create constant
	 */
	const ACTION_CREATE = 'create';

	/*
	 * Read constant
	 */
	const ACTION_READ = 'read';

	/*
	 * Update constant
	 */
	const ACTION_UPDATE = 'update';

	/*
	 * Delete constant
	 */
	const ACTION_DELETE = 'delete';

	/**
	 * @var array $possibleActions All possible actions
	 * @access protected
	 */
	protected $possibleActions = array(
		self::ACTION_CREATE,
		self::ACTION_READ,
		self::ACTION_UPDATE,
		self::ACTION_DELETE
	);

	/**
	 * @var array $roles The current users' roles
	 * @access protected
	 */
	protected $roles = array();

	/**
	 * @var array $resources A collection of resources
	 * @access protected
	 */
	protected $resources = array();

	/**
	 * @var array $rules The current users' rules
	 * @access protected
	 */
	protected $rules = array();

	/**
	 * The constructor. This calls the flush method, so we _know_ we have a clean start
	 */
	public function __construct()
	{
		$this->flush();
	}

	/**
	 * Resets the main variables ($roles, $resources, $rules) to be an empty array
	 *
	 * @return Boardwalk\ACL\ACL
	 */
	public function flush()
	{
		$this->roles = array();
		$this->resources = array();
		$this->rules = array();

		return $this;
	}

	/**
	 * Checks if a resource is registered
	 *
	 * @param string $resource The resource to check
	 * @return bool
	 * @throws Boardwalk\Exceptions\TypeException if the $resource isn't a string
	 */
	public function hasResource($resource)
	{
		if(is_string($resource))
		{
			return is_array($this->resources) ? in_array($resource, $this->resources) : false;
		}
		else
		{
			throw new TypeException(__METHOD__, 'string', $resource);
		}
	}

	/**
	 * Adds a resource
	 *
	 * @param string $resource The resource to add
	 * @return Boardwalk\ACL\ACL
	 * @throws Boardwalk\Exceptions\TypeException if the $resource isn't a string
	 */
	public function addResource($resource)
	{
		if(is_string($resource))
		{
			if(!$this->hasResource($resource))
			{
				$this->resources[] = $resource;
			}
		}
		else
		{
			throw new TypeException(__METHOD__, 'string', $resource);
		}

		return $this;
	}

	/**
	 * Add multiple resources at once
	 *
	 * @param array $resources The resources to bulk add
	 * @return Boardwalk\ACL\ACL
	 * @throws Boardwalk\Exceptions\TypeException if $resource isn't an array
	 */
	public function addResources($resources = array())
	{
		if(is_array($resources))
		{
			foreach($resources as $resource)
			{
				$this->addResource($resource);
			}
		}
		else
		{
			throw new TypeException(__METHOD__, 'array', $resources);
		}

		return $this;
	}

	/**
	 * Allow access to a resource on all or a specific action
	 *
	 * @param string $resource The resource to use
	 * @param string $action The action to use
	 * @throws Boardwalk\Exceptions\TypeException if $resouce isn't a string
	 * @throws Boardwalk\Exceptions\TypeException if $action isn't a string
	 * @return Boardwalk\ACL\ACL
	 */
	public function allow($resource, $action = self::ACTION_ALL)
	{
		if(is_string($resource) && is_string($action))
		{
			$this->setRule($resource, self::ALLOW, $action);
		}
		else
		{

			/*
			 * $resource isn't a string, $action is
			 */
			if(!is_string($resource) && is_string($action))
			{
				throw new TypeException(__METHOD__, 'string', $resource);
			}

			/*
			 * $action isn't a string, $resource is
			 */
			if(!is_string($action) && is_string($resource))
			{
				throw new TypeException(__METHOD__, 'string', $action);
			}

			/*
			 * $resource isn't a string and neither is $action
			 */
			if(!is_string($resource) && !is_string($action))
			{
				throw new TypeException(__METHOD__, 'string', $resource, new TypeException(__METHOD__, 'string', $action));
			}
		}

		return $this;
	}

	/**
	 * Revokes a permission
	 *
	 * @param string $resource The resource to use
	 * @param string $action The action to use
	 * @throws Boardwalk\Exceptions\TypeException if $resouce isn't a string
	 * @throws Boardwalk\Exceptions\TypeException if $action isn't a string
	 * @return Boardwalk\ACL\ACL
	 */
	public function deny($resource, $action = self::ACTION_ALL)
	{
		if(is_string($resource) && is_string($action))
		{
			$this->setRule($resource, self::DENY, $action);
		}
		else
		{

			/*
			 * $resource isn't a string, $action is
			 */
			if(!is_string($resource) && is_string($action))
			{
				throw new TypeException(__METHOD__, 'string', $resource);
			}

			/*
			 * $action isn't a string, $resource is
			 */
			if(!is_string($action) && is_string($resource))
			{
				throw new TypeException(__METHOD__, 'string', $action);
			}

			/*
			 * $resource isn't a string and neither is $action
			 */
			if(!is_string($resource) && !is_string($action))
			{
				throw new TypeException(__METHOD__, 'string', $resource, new TypeException(__METHOD__, 'string', $action));
			}
		}

		return $this;
	}

	/**
	 * Forces a DENY
	 *
	 * @param string $resource The resource to use
	 * @param string $action The action to use
	 * @throws Boardwalk\Exceptions\TypeException if $resouce isn't a string
	 * @throws Boardwalk\Exceptions\TypeException if $action isn't a string
	 * @return Boardwalk\ACL\ACL
	 */
	public function forceDeny($resource, $action = self::ACTION_ALL)
	{
		if(is_string($resource) && is_string($action))
		{
			$this->setRule($resource, self::DENY, $action);
		}
		else
		{

			/*
			 * $resource isn't a string, $action is
			 */
			if(!is_string($resource) && is_string($action))
			{
				throw new TypeException(__METHOD__, 'string', $resource);
			}

			/*
			 * $action isn't a string, $resource is
			 */
			if(!is_string($action) && is_string($resource))
			{
				throw new TypeException(__METHOD__, 'string', $action);
			}

			/*
			 * $resource isn't a string and neither is $action
			 */
			if(!is_string($resource) && !is_string($action))
			{
				throw new TypeException(__METHOD__, 'string', $resource, new TypeException(__METHOD__, 'string', $action));
			}
		}

		return $this;
	}

	public function setRole($role)
	{
		if(!$this->hasRole($role))
		{
			$this->roles[] = $role;
		}

		return $this;
	}

	public function setRoles(array $roles)
	{
		foreach($roles as $role)
		{
			$this->setRole($role);
		}

		return $this;
	}

	public function hasRole($role)
	{
		return is_array($this->roles) ? in_array($role, $this->roles) : false;
	}

	public function getRoles()
	{
		return $this->roles;
	}

	public function setRule($resource, $privilege, $action = self::ACTION_ALL)
	{
		if(!$this->hasResource($resource))
		{
			$this->addResource($resource);
		}

		if($action == self::ACTION_ALL)
		{
			foreach($this->possibleActions as $action)
			{
				if(!isset($this->rules[$resource][$action]))
				{
					$this->rules[$resource][$action] = $privilege;
				}
				else
				{
					$this->rules[$resource][$action] = $this->permissionOr($this->rules[$resource][$action], $privilege);
				}
			}
		}
		else
		{
			if(!isset($this->rules[$resource][$action]))
			{
				$this->rules[$resource][$action] = $privilege;
			}
			else
			{
				$this->rules[$resource][$action] = $this->permissionOr($this->rules[$resource][$action], $privilege);
			}
		}
	}

	public function setForceRule($resource, $privilege, $action = self::ACTION_ALL)
	{
		if(!$this->hasResource($resource))
		{
			$this->addResource($resource);
		}

		if($action == self::ACTION_ALL)
		{
			foreach($this->possibleActions as $action)
			{
				$this->rules[$resource][$action] = $privilege;
			}
		}
		else
		{
			$this->rules[$resource][$action] = $privilege;
		}
	}

	public function setRules(array $rules)
	{
		foreach($rules as $rule)
		{
			$this->setRule($rule['resource'], $rule['privilege'], $rule['action']);
		}
	}

	public function setForceRules(array $rules)
	{
		foreach($rules as $rule)
		{
			$this->setForceRule($rule['resource'], $rule['privilege'], $rule['action']);
		}
	}

	public function getRules()
	{
		return $this->rules;
	}

	public function isAllowed($resource, $action = self::ACTION_ALL)
	{
		if($action == self::ACTION_ALL)
		{
			$return = true;

			foreach($this->possibleActions as $action)
			{
				if(!isset($this->rules[$resource][$action]) || (isset($this->rules[$resource][$action]) && $this->rules[$resource][$action] !== self::ALLOW))
				{
					$return = false;
					break;
				}
			}

			return $return;
		}
		else
		{
			return ((isset($this->rules[$resource][$action]) && $this->rules[$resource][$action] === 'allow') ? true : false);
		}
	}

	public function deleteRole($role)
	{
		if($this->hasRole($role))
		{
			$key = array_search($role, $this->roles);
			unset($this->roles[$key]);
		}
	}

	private function permissionOr($a, $b)
	{
		$x = ($a === self::ALLOW) ? 1 : 0;
		$y = ($b === self::ALLOW) ? 1 : 0;

		return (($x | $y) == 1) ? 'allow' : 'deny';
	}
}