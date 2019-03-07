<?php
/**
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @license    http://kohanaframework.org/license
 */
namespace LSYS\Validation;
use function LSYS\Validation\__;
class Exception extends \LSYS\Exception{
    /**
     * validateion code
     * @var integer
     */
    const VALIDATION_CODE=9527;
    public function __construct($message = NULL, $code =self::VALIDATION_CODE, \Exception $previous = NULL)
    {
        $message=$message?$message:__("data valid fail");
        parent::__construct($message, $code, $previous);
    }
	/**
	 * @var array
	 */
	private $_validation_error=[];
	/**
	 * set validation error
	 * @param array $error
	 * @return $this
	 */
	public function setValidationError(array $error){
		$msg=[];
		foreach ($error as $v){
			if (is_string($v))$msg[]=$v;
		}
		if (count($msg)>0)$this->message.=":".implode(",",$msg);
		$this->_validation_error=$error;
		return $this;
	}
	/**
	 * get validateion message
	 * @return array
	 */
	public function getValidationError(){
		return $this->_validation_error;
	}
}
