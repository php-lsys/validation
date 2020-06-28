<?php
/**
 * lsys validation
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS; 
use function LSYS\Validation\__;
use LSYS\Validation\Valid;
use LSYS\Exception as VException;
/**
 * Array and variable validation.
 *
 * @package    SL
 * @category   Security
 * @author     SL Team
 * @copyright  (c) 2008-2012 SL Team
 * 
 */
class Validation implements \ArrayAccess {
	/**
	 * Creates a new Validation instance.
	 *
	 * @param   array   $array  array to use for validation
	 * @return  Validation
	 */
	public static function factory(array $array)
	{
		return new Validation($array);
	}
	/**
	 * system default message
	 */
	public static function validMessages($error=NULL){
		static $messsage;
		if(!is_array($messsage)){
			$messsage=array(
				'alpha'         => __(':field must contain only letters'),
				'alpha_dash'    => __(':field must contain only numbers, letters and dashes'),
				'alpha_numeric' => __(':field must contain only letters and numbers'),
				'color'         => __(':field must be a color'),
				'credit_card'   => __(':field must be a credit card number'),
				'date'          => __(':field must be a date'),
				'decimal'       => __(':field must be a decimal with :param2 places'),
				'digit'         => __(':field must be a digit'),
				'email'         => __(':field must be an email address'),
				'email_domain'  => __(':field must contain a valid email domain'),
				'equals'        => __(':field must equal :param2'),
				'exact_length'  => __(':field must be exactly :param2 characters long'),
				'in_array'      => __(':field must be one of the available options'),
				'ip'            => __(':field must be an ip address'),
				'matches'       => __(':field must be the same as :param3'),
				'min_length'    => __(':field must be at least :param2 characters long'),
				'max_length'    => __(':field must not exceed :param2 characters long'),
				'not_empty'     => __(':field must not be empty'),
				'numeric'       => __(':field must be numeric'),
				'phone'         => __(':field must be a phone number'),
				'range'         => __(':field must be within the range of :param2 to :param3'),
				'comp'         => __(':field must be :param3 :param2'),
				'regex'         => __(':field does not match the required format'),
				'url'           => __(':field must be a url'),
			);
		}
		if ($error==Null)RETURN $messsage;
		if(isset($messsage[$error]))return $messsage[$error];
		return NULL;
	}
	// Bound values
	protected $_bound = array();
	// Field rules
	protected $_rules = array();
	// Field labels
	protected $_labels = array();
	// Rules that are executed even when the value is empty
	protected $_empty_rules = array('notEmpty', 'matches');
	// Error list, field => rule
	protected $_errors = array();
	// Message list ,filed => message
	protected $_messages = array();
	// Array to validate
	protected $_data = array();
	/**
	 * Sets the unique "any field" key and creates an ArrayObject from the
	 * passed array.
	 *
	 * @param   array   $array  array to validate
	 * @return  void
	 */
	public function __construct(array $array)
	{
		$this->_data = $array;
	}
	/**
	 * Throws an exception because Validation is read-only.
	 * Implements ArrayAccess method.
	 *
	 * @throws  VException
	 * @param   string   $offset    key to set
	 * @param   mixed    $value     value to set
	 * @return  void
	 */
	public function offsetSet($offset, $value)
	{
		throw new VException(__('Validation objects are read-only.'));
	}
	/**
	 * Checks if key is set in array data.
	 * Implements ArrayAccess method.
	 *
	 * @param   string  $offset key to check
	 * @return  bool    whether the key is set
	 */
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}
	/**
	 * Throws an exception because Validation is read-only.
	 * Implements ArrayAccess method.
	 *
	 * @throws  VException
	 * @param   string  $offset key to unset
	 * @return  void
	 */
	public function offsetUnset($offset)
	{
		throw new VException(__('Validation objects are read-only.'));
	}
	/**
	 * Gets a value from the array data.
	 * Implements ArrayAccess method.
	 *
	 * @param   string  $offset key to return
	 * @return  mixed   value from array
	 */
	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}
	/**
	 * Copies the current rules to a new array.
	 *
	 *     $copy = $array->copy($new_data);
	 *
	 * @param   array   $array  new data set
	 * @return  Validation
	 */
	public function copy(array $array)
	{
		// Create a copy of the current validation set
		$copy = clone $this;
		// Replace the data set
		$copy->_data = $array;
		return $copy;
	}
	
	/**
	 * Returns the array of data to be validated.
	 *
	 * @return  array
	 */
	public function data()
	{
		return $this->_data;
	}
	/**
	 * get key value
	 * @param string $key
	 * @param mixed $default
	 * @return string
	 */
	public function get(string $key,$default=null){
	    if (array_key_exists($key, $this->_data))return $this->_data[$key];
	    return $default;
	}
	/**
	 * get key array
	 * ['key'=>['default','data key']]
	 * ['key'=>[]]
	 * @param array $keys
	 * @param boolean $fill
	 * @return mixed[]
	 */
	public function gets(array $keys,bool $fill=FALSE){
	    $out=array();
	    if($fill){
	        foreach ($keys as $k=>$v){
	            $key=isset($v[1])?$v[1]:$k;
	            $default=isset($v[0])?$v[0]:null;
	            $out[$k]=call_user_func(array($this,'get'),$key,$default);
	        }
	    }else{
	        foreach ($keys as $k=>$v){
	            $key=isset($v[1])?$v[1]:$k;
	            if (array_key_exists($key,$this->_data)){
	                $default=isset($v[0])?$v[0]:null;
	                $out[$k]=call_user_func(array($this,'get'),$key,$default);
	            }
	        }
	    }
	    return $out;
	}
	
	/**
	 * Sets or overwrites the label name for a field.
	 *
	 * @param   string  $field  field name
	 * @param   string  $label  label
	 * @return  $this
	 */
	public function label(string $field, string $label)
	{
		// Set the label for this field
		$this->_labels[$field] = $label;
		return $this;
	}
	/**
	 * Sets labels using an array.
	 *
	 * @param   array   $labels list of field => label names
	 * @return  $this
	 */
	public function labels(array $labels)
	{
		$this->_labels = $labels + $this->_labels;
		return $this;
	}
	/**
	 * Overwrites or appends rules to a field. Each rule will be executed once.
	 * All rules must be string names of functions method names. Parameters must
	 * match the parameters of the callback function exactly
	 *
	 * Aliases you can use in callback parameters:
	 * - :validation - the validation object
	 * - :field - the field name
	 * - :value - the value of the field
	 *
	 *     // The "username" must not be empty and have a minimum length of 4
	 *     $validation->rule('username', 'not_empty')
	 *                ->rule('username', 'min_length', array(':value', 4));
	 *
	 *     // The "password" field must match the "password_repeat" field
	 *     $validation->rule('password', 'matches', array(':validation', 'password', 'password_repeat'));
	 *
	 *     // Using closure (anonymous function)
	 *     $validation->rule('index',
	 *         function(Validation $array, $field, $value)
	 *         {
	 *             if ($value > 6 AND $value < 10)
	 	*             {
	 	*                 $array->error($field, 'custom');
	 	*             }
	 *         }
	 *         , array(':validation', ':field', ':value')
	 *     );
	 *
	 * [!!] Errors must be added manually when using closures!
	 *
	 * @param   string      $field  field name
	 * @param   array|callable    $rule   valid PHP callback or closure
	 * @param   array       $params extra parameters for the rule
	 * @return  $this
	 */
	 public function rule(string $field, $rule, array $params = NULL)
	 {
	 	if ($params === NULL)
	 	{
	 		// Default to array(':value')
	 		$params = array(':value');
	 	}
	 	if ($field !== TRUE AND ! isset($this->_labels[$field]))
	 	{
	 		// Set the field label to the field name
	 		$this->_labels[$field] = $field;
	 	}
	 	// Store the rule and params for this rule
	 	$this->_rules[$field][] = array($rule, $params);
	 	return $this;
	 }
	 /**
	  * Add rules using an array.
	  *
	  * @param   string  $field  field name
	  * @param   array   $rules  list of callbacks dome array('comp',array(':value','>=',1))
	  * @return  $this
	  */
	 public function rules(string $field, array $rules)
	 {
	 	foreach ($rules as $rule)
	 	{
	 		$this->rule($field, $rule[0], isset($rule[1])?$rule[1]:null);
	 	}
	 	return $this;
	 }
	 /**
	  * Bind a value to a parameter definition.
	  *
	  *     // This allows you to use :model in the parameter definition of rules
	  *     $validation->bind(':model', $model)
	  *         ->rule('status', 'valid_status', array(':model'));
	  *
	  * @param   string|array  $key    variable name or an array of variables
	  * @param   mixed   $value  value
	  * @return  $this
	  */
	 public function bind($key, $value = NULL)
	 {
	 	if (is_array($key))
	 	{
	 		foreach ($key as $name => $value)
	 		{
	 			$this->_bound[$name] = $value;
	 		}
	 	}
	 	else
	 	{
	 		$this->_bound[$key] = $value;
	 	}
	 	return $this;
	 }
	 /**
	  * Sets messages
	  *
	  * @param   string  
	  * @param   string 
	  * @return  $this
	  */
	 public function message(string $field,string $message){
	 	// Set the label for this field
	 	$this->_messages[$field] = $message;
	 	return $this;
 	}
	 	
	 /**
	  * Sets messages using an array.
	  *
	  * @param   array   $labels list of field => message names
	  * @return  $this
	  */
	 public function messages(array $messages){
	 	$this->_messages = $messages + $this->_messages;
	 	return $this;
	 }
	 
	 /**
	  * Executes all validation rules. This should
	  * typically be called within an if/else block.
	  *
	  *     if ($validation->check())
	  	*     {
	  	*          // The data is valid, do something here
	  	*     }
	  *
	  * @return  boolean
	  */
	  public function check():bool
	  {
	  	// New data set
	  	$data = $this->_errors = array();
	  	// Store the original data because this class should not modify it post-validation
	  	$original = $this->_data;
	  	// Get a list of the expected fields
	  	$expected=array_keys($original);
	  	$array2=array_keys($this->_labels);
	  	foreach ($array2 as $value)
	  	{
	  		if ( ! in_array($value, $expected, TRUE))
	  		{
	  			$expected[] = $value;
	  		}
	  	}
	  	// Import the rules locally
	  	$rules = $this->_rules;
	  	foreach ($expected as $field)
	  	{
	  		// Use the submitted value or NULL if no data exists
	  		$data[$field] = isset($this[$field])?$this[$field]:null;
	  		if (isset($rules[TRUE]))
	  		{
	  			if ( ! isset($rules[$field]))
	  			{
	  				// Initialize the rules for this field
	  				$rules[$field] = array();
	  			}
	  			// Append the rules
	  			$rules[$field] = array_merge($rules[$field], $rules[TRUE]);
	  		}
	  	}
	  	// Overload the current array with the new one
	  	$this->_data = $data;
	  	// Remove the rules that apply to every field
	  	unset($rules[TRUE]);
	  	// Bind the validation object to :validation
	  	$this->bind(':validation', $this);
	  	// Bind the data to :data
	  	$this->bind(':data', $this->_data);
	  	// Execute the rules
	  	foreach ($rules as $field => $set)
	  	{
	  		// Get the field value
	  		$value = $this[$field];
	  		// Bind the field name and value to :field and :value respectively
	  		$this->bind(array
	  				(
	  						':field' => $field,
	  						':value' => $value,
	  				));
	  		foreach ($set as $array)
	  		{
	  			// Rules are defined as array($rule, $params)
	  			list($rule, $params) = $array;
	  			foreach ($params as $key => $param)
	  			{
	  				if (is_string($param) AND array_key_exists($param, $this->_bound))
	  				{
	  					// Replace with bound value
	  					$params[$key] = $this->_bound[$param];
	  				}
	  			}
	  			// Default the error name to be the rule (except array and lambda rules)
	  			$error_name = $rule;
	  			if (is_array($rule))
	  			{
	  				// Allows rule('field', array(':model', 'some_rule'));
	  				if (is_string($rule[0]) AND array_key_exists($rule[0], $this->_bound))
	  				{
	  					// Replace with bound value
	  					$rule[0] = $this->_bound[$rule[0]];
	  				}
	  				// This is an array callback, the method name is the error name
	  				$error_name = $rule[1];
	  				$passed = call_user_func_array($rule, $params);
	  			}
	  			elseif ( ! is_string($rule))
	  			{
	  				// This is a lambda function, there is no error name (errors must be added manually)
	  				$error_name = FALSE;
	  				$passed = call_user_func_array($rule, $params);
	  			}
	  			
	  			elseif (method_exists('\LSYS\Validation\Valid', $rule))
	  			{
	  				// Use a method in this object
	  				$method = new \ReflectionMethod('\LSYS\Validation\Valid', $rule);
	  				// Call static::$rule($this[$field], $param, ...) with Reflection
	  				$passed = $method->invokeArgs(NULL, $params);
	  			}
	  			elseif (strpos($rule, '::') === FALSE)
	  			{
	  				// Use a function call
	  				$function = new \ReflectionFunction($rule);
	  				// Call $function($this[$field], $param, ...) with Reflection
	  				$passed = $function->invokeArgs($params);
	  			}
	  			else
	  			{
	  				// Split the class and method of the rule
	  				list($class, $method) = explode('::', $rule, 2);
	  				// Use a static method call
	  				$method = new \ReflectionMethod($class, $method);
	  				// Call $Class::$method($this[$field], $param, ...) with Reflection
	  				$passed = $method->invokeArgs(NULL, $params);
	  			}
	  			// Ignore return values from rules when the field is empty
	  			if ( ! in_array($rule, $this->_empty_rules) AND ! Valid::notEmpty($value))
	  				continue;
  				if ($passed === FALSE AND $error_name !== FALSE)
  				{
  					// Add the rule to the errors
  					$this->error($field, $error_name, $params);
  					// This field has an error, stop executing rules
  					break;
  				}
  				elseif (isset($this->_errors[$field]))
  				{
  					// The callback added the error manually, stop checking rules
  					break;
  				}
	  		}
	  	}
	  	// Unbind all the automatic bindings to avoid memory leaks.
	  	unset($this->_bound[':validation']);
	  	unset($this->_bound[':data']);
	  	unset($this->_bound[':field']);
	  	unset($this->_bound[':value']);
	  	// Restore the data to its original form
	  	$this->_data = $original;
	  	return empty($this->_errors);
	  }
	  
	  /**
	   * check and fail throw exception
	   * @param string $translate
	   * @throws Exception
	   * @return $this
	   */
	  public function throwCheck(?string $msg = NULL,bool $translate = TRUE){
	      if($this->check())return $this;
	      throw (new \LSYS\Validation\Exception($msg))->setValidationError($this->errors($translate));
	  }
	  
	  /**
	   * Add an error to a field.
	   *
	   * @param   string  $field  field name
	   * @param   string  $error  error message
	   * @param   array   $params
	   * @return  $this
	   */
	  public function error(string $field, string $error, array $params = NULL)
	  {
	  	$this->_errors[$field] = array($error, $params);
	  	return $this;
	  }
	  /**
	   * Returns the error messages. If no file is specified, the error message
	   * will be the name of the rule that failed. When a file is specified, the
	   * message will be loaded from "field/rule", or if no rule-specific message
	   * exists, "field/default" will be used. If neither is set, the returned
	   * message will be "file/field/rule".
	   *
	   * @return  array
	   */
	  public function errors(bool $translate = TRUE):array
	  {
	  	if ($translate === FALSE)
	  	{
	  		// Return the error list
	  		return $this->_errors;
	  	}
	  	// Create a new message list
	  	$messages = array();
	  	foreach ($this->_errors as $field => $set)
	  	{
	  		list($error, $params) = $set;
	  		// Get the label for this field
	  		$label = $this->_labels[$field];
	  		// Start the translation values list
	  		$values = array(
	  				':field' => $label,
	  				':value' => isset($this[$field])?$this[$field]:null,
	  		);
	  		if (is_array($values[':value']))
	  		{
	  			// All values must be strings
	  			$values[':value'] = implode(', ', self::_flatten($values[':value']));
	  		}
	  		if ($params)
	  		{
	  			foreach ($params as $key => $value)
	  			{
	  				if (is_array($value))
	  				{
	  					// All values must be strings
	  					$value = implode(', ', self::_flatten($value));
	  				}
	  				elseif (is_object($value))
	  				{
	  					// Objects cannot be used in message files
	  					continue;
	  				}
	  				// Check if a label for this parameter exists
	  				if (isset($this->_labels[$value]))
	  				{
	  					// Use the label as the value, eg: related field name for "matches"
	  					$value = $this->_labels[$value];
	  					if ($translate)
	  					{
	  						if (is_string($translate))
	  						{
	  							// Translate the value using the specified language
	  							$value = __($value, NULL, $translate);
	  						}
	  						else
	  						{
	  							// Translate the value
	  							$value = __($value);
	  						}
	  					}
	  				}
	  				// Add each parameter as a numbered value, starting from 1
	  				$values[':param'.($key + 1)] = $value;
	  			}
	  		}
	  		if ($message = (isset($this->_messages["{$field}.{$error}"])?"{$field}.{$error}":NULL) AND is_string($message))
	  		{
	  			// Found a message for this field and error
	  			$message=$this->_messages["{$field}.{$error}"];
	  		}
	  		elseif ($message = self::validMessages($error) AND is_string($message))
	  		{
	  			// Found a default message for this error
	  		}
	  		else
	  		{
	  			// No message exists, display the path expected
	  			$message = "{$field}.{$error}";
	  		}
  			// Do not translate, just replace the values
  			$message = strtr($message, $values);
	  		// Set the message for this field
	  		$messages[$field] = $message;
	  	}
	  	return $messages;
	  }
	  /**
	   * Convert a multi-dimensional array into a single-dimensional array.
	   *
	   *     $array = array('set' => array('one' => 'something'), 'two' => 'other');
	   *
	   *     // Flatten the array
	   *     $array = self::_flatten($array);
	   *
	   *     // The array will now be
	   *     array('one' => 'something', 'two' => 'other');
	   *
	   * [!!] The keys of array values will be discarded.
	   *
	   * @param   array   $array  array to flatten
	   * @return  array
	   */
	  private static function _flatten(array $array):array
	  {
	  	$keys = array_keys($array);
	  	$is_assoc=array_keys($keys) !== $keys;
	  	$flat = array();
	  	foreach ($array as $key => $value)
	  	{
	  		if (is_array($value))
	  		{
	  			$flat = array_merge($flat, self::_flatten($value));
	  		}
	  		else
	  		{
	  			if ($is_assoc)
	  			{
	  				$flat[$key] = $value;
	  			}
	  			else
	  			{
	  				$flat[] = $value;
	  			}
	  		}
	  	}
	  	return $flat;
	  }
} // End Validation
