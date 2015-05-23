<?php
namespace Boardwalk;

use Exception;
use Traversable;

class Router
{
	protected $routes = array();
	protected $namedRoutes = array();
	protected $basePath = '';
	protected $matchTypes = array(
		'i'   => '[0-9]++',
		'a'   => '[0-9A-Za-z]++',
		'h'   => '[0-9A-Fa-f]++',
		'*'   => '.+?',
		'**'  => '.++',
		''    => '[^/\.]++'
	);
	
	public function __construct(array $routes = array(), $basePath = '', $matchTypes = array())
	{
		$this->addRoutes($routes);
		$this->setBasePath($basePath);
		$this->addMatchTypes($matchTypes);
	}
	
	public function getRoutes()
	{
		return $this->routes;
	}
	
	public function addRoutes($routes)
	{
		if(!is_array($routes) && !$routes instanceof Traversable)
		{
			$message = '<em>%s</em> should be an array or an instance of Traversable.';
			throw new Exception(sprintf($message, __CLASS__));
		}
		
		foreach($routes as $route)
		{
			call_user_func_array(array($this, 'map'), $route);
		}
	}
	
	public function setBasePath($path)
	{
		$this->basePath = (string)$path;
		return $this;
	}
	
	public function addMatchTypes($types)
	{
		$this->matchTypes = array_merge($this->matchTypes, $types);
	}
	
	public function map($method, $route, $target, $name = null)
	{
		$this->routes[] = array($method, $route, $target, $name);
		
		if(!is_null($name))
		{
			if(isset($this->namedRoutes[$name]))
			{
				$message = 'Cannot redeclare route <em>%s</em>';
				throw new Exception(sprintf($message, $name));
			}
			else
			{
				$this->namedRoutes[$name] = $route;
			}
		}
		
		return;
	}
	
	public function generate($routeName, array $params = array())
	{
		if(!isset($this->namedRoutes[$routeName]))
		{
			$message = 'Route <em>%s</em> does not exist.';
			throw new Exception(sprintf($message, $routeName));
		}
		
		$routes = $this->namedRoutes[$routeName];
		$url = $this->basePath . $route;
		
		if(preg_match('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER))
		{
			foreach($matches as $match)
			{
				list($block, $pre, $type, $param, $optional) = $match;
				
				if($pre)
				{
					$block = substr($block, 1);
				}
				
				if(isset($params[$param]))
				{
					$url = str_replace($block, $params[$param], $url);
				}
				elseif($optional)
				{
					$url = str_replace($pre . $block, '', $url);
				}
			}
		}
		
		return $url;
	}
	
	public function match($requestUrl = null, $requestMethod = null)
	{
		$params = array();
		$match = false;
		
		if(is_null($requestUrl))
		{
			$requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		}
		
		$requestUrl = substr($requestUrl, strlen($this->basePath));
		
		if(($strpos = strpos($requestUrl, '?')) !== false)
		{
			$requestUrl = substr($requestUrl, 0, $strpos);
		}
		
		if(is_null($requestMethod))
		{
			$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		}
		
		$_REQUEST = array_merge($_GET, $_POST);
		
		foreach($this->routes as $handler)
		{
			list($method, $_route, $target, $name) = $handler;
			$methods = explode('|', $method);
			$methodMatch = false;
			
			foreach($methods as $method)
			{
				if(strcasecmp($requestMethod, $method) === 0)
				{
					$methodMatch = true;
					break;
				}
			}
			
			if(!$methodMatch)
			{
				continue;
			}
			
			if($_route === '*')
			{
				$match = true;
			}
			elseif(isset($_route[0]) && $_route[0] === '@')
			{
				$pattern = '`' . substr($_route, 1) . '`u';
				$match = preg_match($pattern, $requestUrl, $params);
			}
			else
			{
				$route = null;
				$regex = false;
				$j = 0;
				$n = isset($_route[0]) ? $_route[0] : null;
				$i = 0;
				
				while(true)
				{
					if(!isset($_route[$i]))
					{
						break;
					}
					elseif(false === $regex)
					{
						$c = $n;
						$regex = $c === '[' || $c === '(' || $c === '.';
						
						if(false === $regex && false !== isset($_route[$i + 1]))
						{
							$n = $_route[$i + 1];
							$regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
						}
						
						if(false === $regex && $c === '/' && (!isset($requestUrl[$j]) || $c !== $requestUrl[$j]))
						{
							continue 2;
						}
						
						$j++;
					}
					
					$route .= $_route[$i++];
				}
				
				$regex = $this->compileRoute($route);
				$match = preg_match($regex, $requestUrl, $params);
			}
			
			if(($match == true || $match > 0))
			{
				if((is_array($params) && count($params) > 0) || ($params instanceof Traversable && count($params) > 0))
				{
					foreach($params as $key => $value)
					{
						if(is_numeric($key))
						{
							unset($params[$key]);
						}
					}
				}
				
				return array(
					'target' => $target,
					'params' => $params,
					'name' => $name
				);
			}
		}
		
		return false;
	}
	
	private function compileRoute($route)
	{
		if(preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER))
		{
			$matchTypes = $this->matchTypes;
			
			foreach($matchTypes as $match)
			{
				list($block, $pre, $type, $param, $optional) = $match;
				
				if(isset($matchTypes[$type]))
				{
					$type = $matchTypes[$type];
				}
				
				if($pre === '.')
				{
					$pre = '\.';
				}
				
				$pattern =  '(?:' .
							(strlen($pre) > 0 ? $pre : null) . 
							'(' .
							(strlen($param) > 0 ? '?P<' . $param . '>' : null) .
							$type . 
							'))' . 
							(strlen($optional) > 0 ? '?' : null);
							
				$route = str_replace($block, $pattern, $route);
			}
		}
		
		return '`^' . $route . '$`u';
	}
	
	public function handle(array $input)
	{
		if(array_key_exists('target', $input))
		{
			$methodParts = explode('@', $input['target']);

			if(count($methodParts) == 2)
			{
				$className = 'App\\Controllers\\' . $methodParts[0];
				$classMethod = $methodParts[1];
				$classInstance = new $className();

				if(method_exists($classInstance, 'before'))
				{
					$classInstance->before();
				}

				$result = call_user_func_array(
					array($classInstance, $classMethod),
					$input['params']
				);

				if(method_exists($classInstance, 'after'))
				{
					$classInstance->after();
				}

				if(is_string($result))
				{
					return $result;
				}
				else
				{
					$exception = 'The method <em>%s</em> does not return a string.';
					throw new Exception(sprintf($exception, $classMethod));
				}
			}
			else
			{
				$exception = 'Invalid method defined: <em>%s</em>';
				throw new Exception(sprintf($exception, $input['target']));
			}
		}
		else
		{
			$exception = 'Maldefined route passed to <em>%s</em>: %s';
			$method = implode(', ', $input);
			throw new Exception(sprintf($exception, __METHOD__, $method));
		}
	}
}
