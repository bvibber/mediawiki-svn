<?

define('DERMIS_VALUE_COMPONENT', 'value');
define('DERMIS_FUNCTION_COMPONENT', 'function');
define('DERMIS_TEMPLATE_COMPONENT', 'template');
define('DERMIS_ARRAY_COMPONENT', 'array');
define('DERMIS_FILE_COMPONENT', 'file');

define('DERMIS_NO_DEFAULT', 'nodefault-23473498494909-nodefault');
define('DERMIS_NO_SUCH_COMPONENT', 'nosuch-23473498494909-nosuch');

class DermisComponent {
	var $type;      //DERMIS_XXX_COMPONENT
	var $data;      //mixed
	var $markup;    //bool
	var $generator; //bool
	var $volatile;  //bool

	function __construct($type, $data, $markup, $generator, $volatile = false) {
		$this->type = $type;
		$this->data = $data;
		$this->markup = $markup;
		$this->generator = $generator;
		$this->volatile = $volatile;
	}
}

class DermisProcessor {
	var $components;

	function __construct() {
		$this->components = array();
		$this->setComponent('errors', DERMIS_ARRAY_COMPONENT, array(), false, false, true);
	}

	protected function error($err, $func = NULL) {
		$this->addValue('errors', $err . ($func?" [$func]":''));
	}

	function component($cmp) {
		if (is_object($cmp)) return $cmp;

		if (!isset($this->components[$cmp])) {
			$this->error("undefined component: " . $cmp, "component");
			return false; //XXX: throw exception?!
		}
		else {
			return $this->components[$cmp];
		}
	}

	function knows($cmp) {
		return isset($this->components[$cmp]);
	}

	function has($cmp) {
		return isset($this->components[$cmp]) && $this->components[$cmp]!==NULL && $this->components[$cmp]!==false && $this->components[$cmp]!=='';
	}

	//--------------------------------------------------------------------------------

	public function escape($t) {
		if (is_array($t) || is_object($t)) throw new Exception("string value expected");
		return htmlspecialchars($t, ENT_QUOTES);
	}

	protected function call($callable, $params = NULL) {
		if (is_string($callable) && preg_match('!^\s*return\s|;$|\(|\$!', $callable)) {
			if (!preg_match('!^\s*return\s!s', $callable)) $callable = 'return '.$callable;
			if (!preg_match('!;\s*$!s', $callable)) $callable = $callable . ';';
			if ($params) extract($params); //FIXME: handle conflicts
			$s = eval($callable);
		}
		else {
			if (!is_callable($callable)) throw new Exception("callable expected");
			$s = call_user_func($callable, $this, $params);
		}

		return $s;
	}

	protected function evaluate($c, $params = NULL, $default = DERMIS_NO_DEFAULT) {
		if ($c===false || $c===NULL) {
			if ($default !== DERMIS_NO_DEFAULT) return $default;
			else return false; //already reported by call to component function
		}

		if ($c->data === '' || $c->data === NULL || $c->data === false) {
			return '';
		}

		if ($c->generator) {
			$this->error("can't evaluate generator", "evaluate");
			throw new Exception("can't evaluate generator");
		}

		if ($c->type == DERMIS_VALUE_COMPONENT) {
			return $c->data;
		}
		else if ($c->type == DERMIS_FUNCTION_COMPONENT) {
			#print "[EVAL F: ".$c->data."]";

			$s = $this->call($c->data, $params);
		}
		else if ($c->type == DERMIS_ARRAY_COMPONENT) {
			$s = '';
			foreach($c->data as $cc) {
				$v = $this->evaluate($cc, $params); //FIXME: separator

				if (!is_scalar($v)) {
					$this->error("found non-scalar value while evaluating array (component: " . $cmp . ")", "evaluate");
					continue;
				}

				$s .= $v;
			}
		}
		else {
			$this->error("type ".$c->type." not known for evaluation (component: " . $cmp . ")", "evaluate");
			throw new Exception("type ".$c->type." not known for evaluation (component: " . $cmp . ")");
		}
		
		if (!$c->volatile && $c->type !== DERMIS_VALUE_COMPONENT) {
			$c->data = $s;
			$c->type = DERMIS_VALUE_COMPONENT;
		}

		return $s;
	}

	protected function generate($c, $params = NULL) {
		if ($c===false || $c===NULL) {
			return false; //already reported by call to component function
		}

		if ($c->data === '' || $c->data === NULL || $c->data === false) {
			return true;
		}

		if (!$c->generator) {
			print $this->markup($c, $params);
			return true;
		}
		else if (!$c->markup) {
			$this->error("can't generate from non-markup component", "generate");
			throw new Exception("can't generate from non-markup component", "generate");
		}

		if ($c->type == DERMIS_FUNCTION_COMPONENT) {
			$this->call($c->data, $params);
		}
		else if ($c->type == DERMIS_TEMPLATE_COMPONENT) {
			if ($params) extract($params); //FIXME: handle conflicts
			require($c->data); //FIXME: globals; //FIXME: search path
		}
		else if ($c->type == DERMIS_FILE_COMPONENT) { //FIXME: don't read! resole as local path or URL!
			readfile($c->data); //FIXME: search path
		}
		else if ($c->type == DERMIS_ARRAY_COMPONENT) {
			foreach($c->data as $cc) {
				$this->generate($cc, $params);
			}
		}
		else {
			$this->error("bad component type for generator: " . $c->type, "generate");
			return false;
		}

		return true;
	}

	//--------------------------------------------------------------------------------
	function items($cmp, $params = NULL, $default = DERMIS_NO_DEFAULT) {
		$c = $this->component($cmp);
		
		if ($c===false || $c===NULL) {
			if ($default !== DERMIS_NO_DEFAULT) return $default;
			else return false; //already reported by call to component function
		}

		if ($c->type == DERMIS_ARRAY_COMPONENT) {
			$a = array();

			foreach($c->data as $cc) {
				$v = $this->evaluate($cc, $params);
				$a[] = $v;
			}
		}
		else {
			$v = $this->evaluate($c, $params);
			$a = array( $v );
		}

		return $a;
	}

	function apply($cmp, $list) {
		$c = $this->component($cmp);
		if ($c===false || $c===NULL) return false;

		$a = $this->items($list);
		if (!$a) return false;

		foreach($a as $v) {
			$this->generate($c, array('entry' => $v));
		}
	}

	function url($cmp, $params = NULL, $default = DERMIS_NO_DEFAULT) { //FIXME: find relative to base. Also provide local file support
		$c = $this->component($cmp);

		if ($c && $c->markup) {
			$this->error("can't convert markup to URL (component: " . $cmp . ")", "url");
			return false; //XXX: throw exception?!
		}

		$s = $this->evaluate($c, $params, $default);
		return $s;
	}

	function text($cmp, $params = NULL, $default = DERMIS_NO_DEFAULT) {
		$c = $this->component($cmp);

		if ($c && $c->markup) {
			$this->error("can't convert markup to text (component: " . $cmp . ")", "text");
			return false; //XXX: throw exception?!
		}

		$s = $this->evaluate($c, $params, $default);
		if (!is_scalar($s)) {
			$this->error("can't convert non-scalar value to text (component: " . $cmp . ")", "text");
			return false; //XXX: throw exception?!
		}

		return $s;
	}

	function value($cmp, $params = NULL, $default = DERMIS_NO_DEFAULT) {
		$c = $this->component($cmp);

		if ($c && $c->markup) {
			$this->error("can't convert markup to plain value (component: " . $cmp . ")", "value");
			return false; //XXX: throw exception?!
		}

		$s = $this->evaluate($c, $params, $default);
		return $s;
	}

	function markup($cmp, $params = NULL, $default = DERMIS_NO_DEFAULT) {
		$c = $this->component($cmp);
		$s = $this->evaluate($c, $params, $default);

		if ($c && !$c->markup) $s = $this->escape($s);
		return $s;
	}

	function atext($cmp, $params = NULL, $default = DERMIS_NO_DEFAULT) {
		$c = $this->component($cmp);

		if ($c && $c->markup) {
			$this->error("can't use markup as attribute value (component: " . $cmp . ")", "atext");
			return false; //XXX: throw exception?!
		}

		$s = $this->evaluate($c, $params, $default);

		if ($c && !$c->markup) $s = $this->escape($s);
		return $s;
	}

	function attribute($name, $cmp) {
		if (!$this->has($cmp)) return false;
		$t = $this->atext($cmp);
		return ' ' . $name . '="' . $t . '" ';
	}

	function comment($txt) {
		return '';
	}

	function execute($cmp, $params = NULL) {
		$c = $this->component($cmp);
		if (!$c) return;

		print $this->comment("BEGIN: $cmp");
		$ok = $this->generate($c, $params);
		print $this->comment("END: $cmp");
		return $ok;
	}

	//--------------------------------------------------------------------------------

	function copyComponent($name, $name2) {
		if ($this->components[$name2]) $this->components[$name] = $this->components[$name2];
	}

	function setComponent($name, $type, $data, $markup, $generator, $volatile) {
		$this->components[$name] = new DermisComponent($type, $data, $markup, $generator, $volatile);
	}

	function addComponent($name, $type, $data, $markup, $generator, $volatile) {
		$c = new DermisComponent($type, $data, $markup, $generator, $volatile);

		if (isset($this->components[$name])) {
			$cc = $this->components[$name];
			if ($cc->type == DERMIS_ARRAY_COMPONENT) {
				$cc->data[] = $c;
			}
			else {
				$oc = $cc;
				$cc = new DermisComponent(DERMIS_ARRAY_COMPONENT, array($oc, $c), $oc->markup, $oc->generator, $oc->volatile);
				$this->components[$name] = $cc;
			}

			$cc->markup = ($cc->markup || $c->markup);
			$cc->generator = ($cc->generator || $c->generator);
			$cc->volatile = ($cc->volatile || $c->volatile);
		}
		else {
			$this->components[$name] = $c;
		}
	}

	function setFunction($name, $func, $markup = false, $generator = false, $volatile = false) {
		if ($func === NULL || $func === false || $func === '') unset($this->components[$name]);
		else $this->setComponent($name, DERMIS_FUNCTION_COMPONENT, $func, $markup, $generator, $volatile);
	}

	function setGenerator($name, $func, $markup = true, $generator = true, $volatile = false) {
		if ($func === NULL || $func === false || $func === '') unset($this->components[$name]);
		else $this->setComponent($name, DERMIS_FUNCTION_COMPONENT, $func, $markup, $generator, $volatile);
	}

	function setTemplate($name, $tpl) {
		if ($tpl === NULL || $tpl === false || $tpl === '') unset($this->components[$name]);
		else $this->setComponent($name, DERMIS_TEMPLATE_COMPONENT, $tpl, true, true, false);
	}

	function setFile($name, $f) {
		if ($f === NULL || $f === false || $f === '') unset($this->components[$name]);
		else $this->setComponent($name, DERMIS_FILE_COMPONENT, $f, true, false, false);
	}

	function setValue($name, $v, $markup=false) {
		$this->setComponent($name, DERMIS_VALUE_COMPONENT, $v, $markup, false, false);
	}

	function addFunction($name, $func, $markup = false, $generator = false, $volatile = false) {
		if ($func === NULL || $func === false || $func === '') return;
		else $this->addComponent($name, DERMIS_FUNCTION_COMPONENT, $func, $markup, $generator, $volatile);
	}

	function addGenerator($name, $func, $markup = true, $generator = true, $volatile = false) {
		if ($func === NULL || $func === false || $func === '') return;
		else $this->addComponent($name, DERMIS_FUNCTION_COMPONENT, $func, $markup, $generator, $volatile);
	}

	function addTemplate($name, $tpl) {
		if ($tpl === NULL || $tpl === false || $tpl === '') return;
		else $this->addComponent($name, DERMIS_TEMPLATE_COMPONENT, $tpl, true, true, false);
	}

	function addFile($name, $f) {
		if ($f === NULL || $f === false || $f === '') return;
		else $this->addComponent($name, DERMIS_FILE_COMPONENT, $f, true, false, false);
	}

	function addValue($name, $v, $markup=false) {
		$this->addComponent($name, DERMIS_VALUE_COMPONENT, $v, $markup, false, false);
	}

}

?>