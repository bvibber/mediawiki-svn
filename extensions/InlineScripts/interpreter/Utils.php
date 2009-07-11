<?php

class ISASTNode {
	const NodeData = 1;
	const NodeOperator = 2;

	var $mChildren, $mType, $mPos;

	public function __construct( $type, $pos ) {
		$this->mChildren = array();
		$this->mType = $type;
		$this->mPos = $pos;
	}

	public function addChild( $child ) {
		array_unshift( $this->mChildren, $child );
	}
	
	public function addChildren( $children ) {
		$this->mChildren = array_merge( $this->mChildren, $children );
	}

	public function getChildren() {
		return $this->mChildren;
	}

	public function getType() {
		return $this->mType;
	}

	public function getPos() {
		return $this->mPos;
	}

	public function isOp( $op ) {
		return $this->mType == self::NodeOperator &&
			$this->getOperator() == $op;
	}

	protected function childrenToString() {
		$s = array();
		foreach( $this->mChildren as $child ) {
			$lines = explode( "\n", $child->__toString() );
			foreach( $lines as $line )
				$s[] = " {$line}";
		}
		return implode( "\n", $s );
	}
}

class ISOperatorNode extends ISASTNode {
	const OFunction = 'FUNCTION';
	const OArrayElement = 'AELEMENT';
	const OArray = 'ARRAY';
	const OArrayElementSingle = 'AELEMENT_S';	// array[]
	const OPositive = 'POSITIVE';	// not to be confused with sum
	const ONegative = 'NEGATIVE';	// not to be confused with sub
	const OIf = 'if';
	const OThen = 'then';
	const OElse = 'else';
	const ODo = 'do';
	const OForeach = 'foreach';
	const OTry = 'try';
	const OCatch = 'catch';
	const OBreak = 'break';
	const OContinue = 'continue';
	const OIsset = 'isset';
	const OUnset = 'unset';
	const OIn = 'in';
	const OInvert = '!';
	const OPow = '**';
	const OMul = '*';
	const ODiv = '/';
	const OMod = '%';
	const OSum = 'SUM';
	const OSub = 'SUB';
	const OEqualsTo = '==';
	const ONotEqualsTo = '!=';
	const OEqualsToStrict = '===';
	const ONotEqualsToStrict = '!==';
	const OGreater = '>';
	const OLess = '<';
	const OGreaterOrEq = '>=';
	const OLessOrEq = '<=';
	const OAnd = '&';
	const OOr = '|';
	const OXor = '^';
	const OTrinary = '?';
	const OColon = ':';
	const OSet = '=';
	const OSetAdd = '+=';
	const OSetSub = '-=';
	const OSetMul = '*=';
	const OSetDiv = '/=';
	const OComma = ',';
	const OStatementSeperator = ';';
	const OLeftBrace = '(';
	const OLeftCurly = '{';

	static $precedence = array(
		self::OFunction => 20, self::OIsset => 20, self::OUnset => 20,
		self::OArrayElement => 19, self::OPositive => 19, self::ONegative => 19,
		self::OIn => 18,
		self::OInvert => 17, self::OPow => 17,
		self::OMul => 16, self::ODiv => 16, self::OMod => 16,
		self::OSum => 15, self::OSub => 15,
		self::OEqualsTo => 14, self::ONotEqualsTo => 14, self::OEqualsToStrict => 14,
		self::ONotEqualsToStrict => 13, self::OGreater => 13, self::OLess => 13,
		self::OGreaterOrEq => 13, self::OLessOrEq => 13,
		self::OAnd => 12, self::OOr => 12, self::OXor => 12,
		self::OColon => 11, self::OTrinary => 10,
		self::OSet => 9, self::OSetAdd => 9, self::OSetSub => 9,
		self::OSetMul => 9, self::OSetDiv => 9,
		self::OIf => 6, self::OThen => 7, self::OElse => 8,
		self::OForeach => 6, self::ODo => 7,
		self::OTry => 6, self::OCatch => 7,
		self::OComma => 8, self::OStatementSeperator => 0,
	);

	static $unaryOperators = array(
		self::OPositive, self::ONegative, self::OFunction, self::OArray, self::OTry,
		self::OIf, self::OForeach, self::OInvert, self::OArrayElementSingle,
		self::OIsset, self::OUnset
	);

	static function parseOperator( $op, $expecting, $pos ) {
		if( $expecting == ISCodeParserShuntingYard::ExpectingData ) {
			$ops = array( '(', '{', 'if', '!', 'try', 'break', 'continue',
				'isset', 'unset' );
			if( $op == '+' ) return new self( self::OPositive, $pos );
			if( $op == '-' ) return new self( self::ONegative, $pos );
			if( $op == '[' ) return new self( self::OArray, $pos );
			if( in_array( $op, $ops ) )
					return new self( $op, $pos );
			return null;
		} else {
			if( $op == '+' ) return new self( self::OSum, $pos );
			if( $op == '-' ) return new self( self::OSub, $pos );
			if( $op == '[' ) return new self( self::OArrayElement, $pos );
			return new self( $op, $pos );
		}
	}

	var $mOperator, $mData, $mNumArgs;

	public function __construct( $op, $pos, $data = '' ) {
		parent::__construct( ISASTNode::NodeOperator, $pos );
		$this->mOperator = $op;
		$this->mData = $data;
		$this->mNumArgs = null;
	}

	public function getOperator() {
		return $this->mOperator;
	}

	public function getData() {
		return $this->mData;
	}

	public function getPrecedence() {
		return isset( self::$precedence[$this->mOperator] ) ?
			self::$precedence[$this->mOperator] : -1;
	}

	public function getArgsNumber() {
		if( $this->mNumArgs !== null )
			return $this->mNumArgs;
		if( in_array( $this->mOperator, self::$unaryOperators ) )
			return 1;
		elseif( $this->mOperator == self::OBreak ||
			$this->mOperator == self::OContinue )
			return 0;
		else
			return 2;
	}

	public function setArgsNumber( $num ) {
		$this->mNumArgs = $num;
	}

	public function setData( $data ) {
		$this->mData = $data;
	}

	public function isRightAssociative() {
		return $this->mOperator == self::OPow ||
			$this->mOperator == self::OSet;
	}

	public function isLeftSquare() {
		return $this->mOperator == self::OArrayElement ||
			$this->mOperator == self::OArray;
	}

	public function __toString() {
		return "<operator value=\"{$this->mOperator}\" data=\"{$this->mData}\">\n" .
			$this->childrenToString() . "\n</operator>";
	}
}

class ISDataNode extends ISASTNode {
	const DNData = 0;
	const DNVariable = 1;

	var $mDataType, $mData, $mVar;

	public function __construct( $dataOrVar, $pos ) {
		parent::__construct( ISASTNode::NodeData, $pos );
		if( $dataOrVar instanceof ISData ) {
			$this->mDataType = self::DNData;
			$this->mData = $dataOrVar;
		}
		if( is_string( $dataOrVar ) ) {
			$this->mDataType = self::DNVariable;
			$this->mVar = $dataOrVar;
		}
		// Some whining here?
	}

	public function getDataType() {
		return $this->mDataType;
	}

	public function getData() {
		return $this->mData;
	}

	public function getVar() {
		return $this->mVar;
	}

	public function __toString() {
		$type = '';
		switch( $this->mDataType ) {
			case self::DNData:
				return "<datanode nodetype=\"data\" type=\"{$this->mData->type}\" value=\"{$this->mData->data}\" />";
			case self::DNVariable:
				return "<datanode nodetype=\"var\" name=\"{$this->mVar}\" />";
				break;
		}
	}
}

class ISData {
	// Data types
	const DInt    = 'int';
	const DString = 'string';
	const DNull   = 'null';
	const DBool   = 'bool';
	const DFloat  = 'float';
	const DList   = 'list';

	var $type;
	var $data;

	public function __construct( $type = self::DNull, $val = null ) {
		$this->type = $type;
		$this->data = $val;
	}

	public static function newFromPHPVar( $var ) {
		if( is_string( $var ) )
			return new ISData( self::DString, $var );
		elseif( is_int( $var ) )
			return new ISData( self::DInt, $var );
		elseif( is_float( $var ) )
			return new ISData( self::DFloat, $var );
		elseif( is_bool( $var ) )
			return new ISData( self::DBool, $var );
		elseif( is_array( $var ) ) {
			$result = array();
			foreach( $var as $item )
				$result[] = self::newFromPHPVar( $item );
			return new ISData( self::DList, $result );
		}
		elseif( is_null( $var ) )
			return new ISData();
		else
			throw new ISException(
				"Data type " . gettype( $var ) . " is not supported by InlineScrtips" );
	}
	
	public function dup() {
		return new ISData( $this->type, $this->data );
	}
	
	public static function castTypes( $orig, $target ) {
		if( $orig->type == $target )
			return $orig->dup();
		if( $target == self::DNull ) {
			return new ISData();
		}

		if( $orig->type == self::DList ) {
			if( $target == self::DBool )
				return new ISData( self::DBool, (bool)count( $orig->data ) );
			if( $target == self::DFloat ) {
				return new ISData( self::DFloat, doubleval( count( $orig->data  ) ) );
			}
			if( $target == self::DInt ) {
				return new ISData( self::DInt, intval( count( $orig->data ) ) );
			}
			if( $target == self::DString ) {
				$s = '';
				foreach( $orig->data as $item )
					$s .= $item->toString()."\n";
				return new ISData( self::DString, $s );
			}
		}

		if( $target == self::DBool ) {
			return new ISData( self::DBool, (bool)$orig->data );
		}
		if( $target == self::DFloat ) {
			return new ISData( self::DFloat, doubleval( $orig->data ) );
		}
		if( $target == self::DInt ) {
			return new ISData( self::DInt, intval( $orig->data ) );
		}
		if( $target == self::DString ) {
			return new ISData( self::DString, strval( $orig->data ) );
		}
		if( $target == self::DList ) {
			return new ISData( self::DList, array( $orig ) );
		}
	}
	
	public static function boolInvert( $value ) {
		return new ISData( self::DBool, !$value->toBool() );
	}
	
	public static function pow( $base, $exponent ) {
		if( $base->type == self::DInt && $exponent->type == self::DInt )
			return new ISData( self::DInt, pow( $base->toInt(), $exponent->toInt() ) );
		else
			return new ISData( self::DFloat, pow( $base->toFloat(), $exponent->toFloat() ) );
	}
	
	public static function keywordIn( $a, $b ) {
		$a = $a->toString();
		$b = $b->toString();
		
		if ($a == '' || $b == '') {
			return new ISData( self::DBool, false );
		}
		
		return new ISData( self::DBool, in_string( $a, $b ) );
	}
	
	public static function keywordContains( $a, $b ) {
		$a = $a->toString();
		$b = $b->toString();
		
		if ($a == '' || $b == '') {
			return new ISData( self::DBool, false );
		}
		
		return new ISData( self::DBool, in_string( $b, $a ) );
	}

	public static function listContains( $value, $list ) {
		// Should use built-in PHP function somehow
		foreach( $list->data as $item ) {
			if( self::equals( $value, $item ) )
				return true;
		}
		return false;
	}
	
	public static function equals( $d1, $d2 ) {
		return $d1->type != self::DList && $d2->type != self::DList &&
			$d1->data == $d2->data;
	}

	public static function unaryMinus( $data ) {
		if ($data->type == self::DInt) {
			return new ISData( $data->type, -$data->toInt() );
		} else {
			return new ISData( $data->type, -$data->toFloat() );
		}
	}
	
	public static function compareOp( $a, $b, $op ) {
		if( $op == '==' )
			return new ISData( self::DBool, self::equals( $a, $b ) );
		if( $op == '!=' )
			return new ISData( self::DBool, !self::equals( $a, $b ) );
		if( $op == '===' )
			return new ISData( self::DBool, $a->type == $b->type && self::equals( $a, $b ) );
		if( $op == '!==' )
			return new ISData( self::DBool, $a->type != $b->type || !self::equals( $a, $b ) );
		$a = $a->toString();
		$b = $b->toString();
		if( $op == '>' )
			return new ISData( self::DBool, $a > $b );
		if( $op == '<' )
			return new ISData( self::DBool, $a < $b );
		if( $op == '>=' )
			return new ISData( self::DBool, $a >= $b );
		if( $op == '<=' )
			return new ISData( self::DBool, $a <= $b );
		throw new ISException( "Invalid comparison operation: {$op}" ); // Should never happen
	}
	
	public static function mulRel( $a, $b, $op, $pos ) {	
		// Figure out the type.
		if( ( $a->type == self::DFloat || $b->type == self::DFloat ) &&
			$op != '/' ) {
			$type = self::DInt;
			$a = $a->toInt();
			$b = $b->toInt();
		} else {
			$type = self::DFloat;
			$a = $a->toFloat();
			$b = $b->toFloat();
		}

		if ($op != '*' && $b == 0) {
			throw new ISUserVisibleException( 'dividebyzero', $pos, array($a) );
		}

		$data = null;
		if( $op == '*' )
			$data = $a * $b;
		elseif( $op == '/' )
			$data = $a / $b;
		elseif( $op == '%' )
			$data = $a % $b;
		else
			throw new ISException( "Invalid multiplication-related operation: {$op}" ); // Should never happen
			
		if ($type == self::DInt)
			$data = intval($data);
		else
			$data = doubleval($data);
		
		return new ISData( $type, $data );
	}
	
	public static function sum( $a, $b ) {
		if( $a->type == self::DString || $b->type == self::DString )
			return new ISData( self::DString, $a->toString() . $b->toString() );
		elseif( $a->type == self::DList && $b->type == self::DList )
			return new ISData( self::DList, array_merge( $a->toList(), $b->toList() ) );
		elseif( $a->type == self::DInt && $b->type == self::DInt )
			return new ISData( self::DInt, $a->toInt() + $b->toInt() );
		else
			return new ISData( self::DFloat, $a->toFloat() + $b->toFloat() );
	}
	
	public static function sub( $a, $b ) {
		if( $a->type == self::DInt && $b->type == self::DInt )
			return new ISData( self::DInt, $a->toInt() - $b->toInt() );
		else
			return new ISData( self::DFloat, $a->toFloat() - $b->toFloat() );
	}
	
	public function setValueByIndices( $val, $indices ) {
		if( $this->type == self::DNull && $indices[0] === null ) {
			$this->type = self::DList;
			$this->value = array();
			$this->setValueByIndices( $val, $indices );
		} elseif( $this->type == self::DList ) {
			if( $indices[0] === null ) {
				$this->data[] = $val;
			} else {
				$idx = $indices[0]->toInt();
				if( $idx < 0 || $idx >= count( $this->data ) )
					throw new ISUserVisibleException( 'outofbounds', 0, array( count( $this->data ), $index ) );
				if( count( $indices ) > 1 )
					$this->data[$idx]->setValueByIndices( $val, array_slice( $indices, 1 ) );
				else
					$this->data[$idx] = $val;
			}
		}
	}

	public function checkIssetByIndices( $indices ) {
		if( $indices ) {
			$idx = array_shift( $indices );
			if( $this->type != self::DList || $idx >= count( $this->data ) )
				return false;
			return $this->checkIssetByIndices( $indices );
		} else {
			return true;
		}
	}

	/** Convert shorteners */
	public function toBool() {
		return self::castTypes( $this, self::DBool )->data;
	}
	
	public function toString() {
		return self::castTypes( $this, self::DString )->data;
	}
	
	public function toFloat() {
		return self::castTypes( $this, self::DFloat )->data;
	}
	
	public function toInt() {
		return self::castTypes( $this, self::DInt )->data;
	}

	public function toList() {
		return self::castTypes( $this, self::DList )->data;
	}
}

class ISParserOutput {
	var $mAST, $mTokensCount, $mVersion;

	public function __construct( $ast, $tokens ) {
		$this->mAST = $ast;
		$this->mTokensCount = $tokens;
		$this->mVersion = InlineScriptInterpreter::ParserVersion;
	}

	public function getAST() {
		return $this->mAST;
	}

	public function isOutOfDate() {
		return InlineScriptInterpreter::ParserVersion > $this->mVersion;
	}

	public function appendTokenCount( &$interpr ) {
		global $wgInlineScriptsParserParams;
		$interpr->mParser->is_tokensCount += $this->mTokensCount;
		if( $interpr->mParser->is_tokensCount > $wgInlineScriptsParserParams['limits']['tokens'] )
			throw new ISUserVisibleException( 'toomanytokens', 0 );
	}
}

class ISException extends MWException {}

// Exceptions that we might conceivably want to report to ordinary users
// (i.e. exceptions that don't represent bugs in the extension itself)
class ISUserVisibleException extends ISException {
	function __construct( $exception_id, $position, $params = array() ) {
		$msg = wfMsgExt( 'inlinescripts-exception-' . $exception_id, array(), array_merge( array($position), $params ) );
		parent::__construct( $msg );

		$this->mExceptionID = $exception_id;
		$this->mPosition = $position;
		$this->mParams = $params;
	}

	public function getExceptionID() {
		return $this->mExceptionID;
	}
}
