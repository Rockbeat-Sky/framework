<?php
namespace Sky\validate;
use Sky\core\BaseClass;
use Sky\core\Language;
use Sky\validate\RuleValidate;

class Validate extends BaseClass{
	use RuleValidate;
	public $scope;
	public $out =[];
	private $error = false;
	protected $data = [];
	protected $label = [];
	public $_config = [
		'error_group'	=> 'error',		// send to group error name message
		'language' 		=> 'App.Validation', // validation language file name
		'flash'			=> false,	// flash data error each field
		'error_break' 	=> false,	// when field have once invalid, will break and continue next field
		'charset'	=> 'UTF8'
	];
	public function __CONSTRUCT($config = []){
		parent::__CONSTRUCT($config);
		$this->__init();
		//$this->CI = & get_instance();
		// Set the character encoding in MB.
		if (function_exists('mb_internal_encoding')){
			mb_internal_encoding($this->getConfig('charset'));
		}
		Language::load($this->getConfig('language'));
	}
	/**
	* scope
	* 
	* add scope for callback
	* 
	* @param class object
	* @return void
	*/
	public function scope($object){
		$this->scope = $object;
		return $this;
	}
	public function setLabel($data){
		$this->label = $data;
		return $this;
	}
	/**
	* Check
	* 
	* validating data and rule
	*
	* @param array
	* @param array
	* @param boolean
	* @return void
	*/
	function check($rule,$data,$base_rule = true){
		$this->error = false;
		$this->data = [];
		// set_data
		$this->_rule_data($rule,$data,$base_rule);
		
		// have data?
		if($this->data){
			$this->_run();
		}
		return !$this->CI->msg->is_error;
	}

	public function set_error($rule,$param,$field,$value,$label){
		$this->error = true;
		$replace = array(ucwords(str_replace('_',' ',$label)),$param,$rule,$value);
		set_msg(strtoupper($rule),$replace,$this->config['error_group']);
	}
	/**
	* 
	* process build to mapping master data, rule, and label before execution validation
	* 
	* @param array
	* @param array
	* @param boolean
	* @return void
	*/
	protected function _rule_data($rules,$data,$base_on_rule = true){
		$this->out = $data;
		if(is_array($rules) and is_array($data)){
			// mapping base on rules
			if($base_on_rule){
				foreach ($rules as $field => $val){
					$value = isset($data[$field])?$data[$field]:'';
					$this->data [$field] = [
						'rule' 	=> $val,
						'value' => $value,
						'field' => $field,
						'label' => null
					];
				}
			}
			// mapping base on data
			else{
				foreach($data as $field => $val){
					if(isset($rules[$field])){
						$this->data[$field] = [
							'rule'  => $rules[$field],
							'value' => $val,
							'field' => $field,
							'label' => null
						];
					}
				}
			}
			// set label
			foreach($this->data as $data){
				if(isset($this->label[$data['field']])){
					$label = $this->label[$data['field']];
				}
				else{
					$label = ucwords(str_replace('_',' ',$data['field']));
				}
				$this->data[$data['field']]['label'] = $label;
			}
		}
		return $this;
	}
	/**
	* process for execution validation
	* @return void
	**/
	private function _run(){
		foreach($this->data as $data){
			$error = false;
			/** analyst field data **/
			foreach(explode('|',$data['rule']) as $role){
				$param = false;
				if (preg_match("/(.*?)\[(.*)\]/",$role, $match)){
					$rule	= $match[1];
					$param	= $match[2];
				}
				else{
					$rule = $role;
				}
				if($rule === ''){
					break;
				}
				/* check callback */
				$callback = false;
				if(substr($rule,0,5)=='call_'){
					$rule = substr($rule,5);
					$callback = 'scope';
				}
				/** no callback **/
				if(!$callback){
					// is method exists?
					if(method_exists($this,$rule)){
						$prep = $this->$rule($data['value'],$param,$data['field']);
						if(is_bool($prep) && $prep === false){
							$this->set_error($rule,$param,$data['field'],$data['value'],$data['label']);
							$error = true;
						}
						else if(!is_bool($prep)){
							$this->out[$data['field']] = $prep;
						}
					}
					// is global function exists?
					elseif(function_exists($rule)){
						$prep = $rule($this->data[$data['field']]['value']);
						// check is validate result?
						if(is_bool($prep)){
							
							if($prep){
								$this->set_error($rule,$param,$data['field'],$data['value'],$data['label']);
								$error = true;
							}
						}
						else{
							$this->out[$data['field']] = $prep;
						}
					}
					else{
						show_error('The rule '.$rule.' not found');
					}
				}
				// using callback scope
				// required adding scope
				elseif($callback){

					if($callback === 'scope' and method_exists($this->scope,$rule)){
						if(!$this->scope->$rule($data['value'],$param)){
							$this->set_error($rule,$param,$data['field'],$data['value'],$data['label']);
							$error = true;
						}
					}
					else{
						show_error('Function '.$rule.' not found');
					}
				}
				if($error && $this->config['error_break']){
					break;
				}
			}
			// if error and flash_data true set flash data
			if($this->config['flash']){
				$this->CI->session->set_flashdata($data['field'],$this->get_error($data['field']));
			}
		}
	}
}