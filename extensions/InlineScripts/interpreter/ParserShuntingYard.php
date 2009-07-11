<?php

class ISCodeParserShuntingYard {
	const ExpectingData = 1;
	const ExpectingOperator = 2;

	var $mCode, $mPos, $mCur, $mPrev, $mFunctions, $mInterpreter, $mTokensCount;

	public function __construct( &$interpreter ) {
		$this->resetState();
		$this->mFunctions = array_keys( InlineScriptInterpreter::$mFunctions );
		$this->mInterpreter = $interpreter;
	}

	public function resetState() {
		$this->mCode = '';
		$this->mPos = $this->mTokensCount = 0;
		$this->mCur = $this->mPrev = null;
	}

	public function parse( $code ) {
		$this->resetState();
		$this->mCode = $code;
		$ast = $this->parseToAST();
		return new ISParserOutput( $ast, $this->mTokensCount );
	}

	function nextToken() {
		$tok = '';

		// Spaces
		$matches = array();
		if ( preg_match( '/\s+/uA', $this->mCode, $matches, 0, $this->mPos ) )
			$this->mPos += strlen($matches[0]);		

		if( $this->mPos >= strlen($this->mCode) )
			return array( '', ISToken::TNone, $this->mCode, $this->mPos );

		// Comments
		if ( substr($this->mCode, $this->mPos, 2) == '/*' ) {
			$this->mPos = strpos( $this->mCode, '*/', $this->mPos ) + 2;
			return self::nextToken();
		}

		// Braces
		if( $this->mCode[$this->mPos] == ')'  ) {
			return array( $this->mCode[$this->mPos], ISToken::TBraceClose, $this->mCode, $this->mPos + 1 );
		}
		
		// Square brackets
		if( $this->mCode[$this->mPos] == ']' ) {
			return array( $this->mCode[$this->mPos], ISToken::TSquareClose, $this->mCode, $this->mPos + 1 );
		}

		// Curly brackets
		if( $this->mCode[$this->mPos] == '}' ) {
			return array( $this->mCode[$this->mPos], ISToken::TCurlyClose, $this->mCode, $this->mPos + 1 );
		}

		// Strings
		if( $this->mCode[$this->mPos] == '"' || $this->mCode[$this->mPos] == "'" ) {
			$type = $this->mCode[$this->mPos];
			$this->mPos++;
			$strLen = strlen($this->mCode);
			while( $this->mPos < $strLen ) {
				if( $this->mCode[$this->mPos] == $type ) {
					$this->mPos++;
					return array( $tok, ISToken::TString, $this->mCode, $this->mPos );
				}

				// Performance: Use a PHP function (implemented in C)
				//  to scan ahead.
				$addLength = strcspn( $this->mCode, $type."\\", $this->mPos );
				if ($addLength) {
					$tok .= substr( $this->mCode, $this->mPos, $addLength );
					$this->mPos += $addLength;
				} elseif( $this->mCode[$this->mPos] == '\\' ) {
					switch( $this->mCode[$this->mPos + 1] ) {
						case '\\':
							$tok .= '\\';
							break;
						case $type:
							$tok .= $type;
							break;
						case 'n';
							$tok .= "\n";
							break;
						case 'r':
							$tok .= "\r";
							break;
						case 't':
							$tok .= "\t";
							break;
						case 'x':
							$chr = substr( $this->mCode, $this->mPos + 2, 2 );
							
							if ( preg_match( '/^[0-9A-Fa-f]{2}$/', $chr ) ) {
								$chr = base_convert( $chr, 16, 10 );
								$tok .= chr($chr);
								$this->mPos += 2; # \xXX -- 2 done later
							} else {
								$tok .= 'x';
							}
							break;
						default:
							$tok .= "\\" . $this->mCode[$this->mPos + 1];
					}
					$this->mPos+=2;
				} else {
					$tok .= $this->mCode[$this->mPos];
					$this->mPos++;
				}
			}
			throw new ISUserVisibleException( 'unclosedstring', $this->mPos, array() );;
		}

		// Find operators
		static $operator_regex = null;
		// Match using a regex. Regexes are faster than PHP
		if (!$operator_regex) {
			$quoted_operators = array();

			foreach( InlineScriptInterpreter::$mOps as $op )
				$quoted_operators[] = preg_quote( $op, '/' );
			$operator_regex = '/('.implode('|', $quoted_operators).')/A';
		}

		$matches = array();

		preg_match( $operator_regex, $this->mCode, $matches, 0, $this->mPos );

		if( count( $matches ) ) {
			$tok = $matches[0];
			$this->mPos += strlen( $tok );
			return array( $tok, ISToken::TOp, $this->mCode, $this->mPos );
		}

		// Find bare numbers
		$bases = array( 'b' => 2,
						'x' => 16,
						'o' => 8 );
		$baseChars = array(
						2 => '[01]',
						16 => '[0-9A-Fa-f]',
						8 => '[0-8]',
						10 => '[0-9.]',
						);
		$baseClass = '['.implode('', array_keys($bases)).']';
		$radixRegex = "/([0-9A-Fa-f]+(?:\.\d*)?|\.\d+)($baseClass)?/Au";
		$matches = array();

		if ( preg_match( $radixRegex, $this->mCode, $matches, 0, $this->mPos ) ) {
			$input = $matches[1];
			$baseChar = @$matches[2];
			$num = null;
			// Sometimes the base char gets mixed in with the rest of it because
			//  the regex targets hex, too.
			//  This mostly happens with binary
			if (!$baseChar && !empty( $bases[ substr( $input, -1 ) ] ) ) {
				$baseChar = substr( $input, -1, 1 );
				$input = substr( $input, 0, -1 );
			}

			if( $baseChar )
				$base = $bases[$baseChar];
			else
				$base = 10;

			// Check against the appropriate character class for input validation
			$baseRegex = "/^".$baseChars[$base]."+$/";

			if ( preg_match( $baseRegex, $input ) ) {
				if ($base != 10) {
					$num = base_convert( $input, $base, 10 );
				} else {
					$num = $input;
				}

				$this->mPos += strlen( $matches[0] );

				$float = in_string( '.', $input );

				return array(
					$float
						? doubleval( $num )
						: intval( $num ),
					$float
						? ISToken::TFloat
						: ISToken::TInt,
					$this->mCode,
					$this->mPos
				);
			}
		}

		// The rest are considered IDs

		// Regex match > PHP
		$idSymbolRegex = '/[0-9A-Za-z_]+/A';
		$matches = array();

		if ( preg_match( $idSymbolRegex, $this->mCode, $matches, 0, $this->mPos ) ) {
			$tok = strtolower( $matches[0] );

			$type = in_array( $tok, InlineScriptInterpreter::$mKeywords )
				? ISToken::TKeyword
				: ( in_array( $tok, $this->mFunctions )
				? ISToken::TFunction : ISToken::TID);

			return array( $tok, $type, $this->mCode, $this->mPos + strlen($tok) );
		}

		throw new ISUserVisibleException(
			'unrecognisedtoken', $this->mPos, array( substr( $this->mCode, $this->mPos ) ) );
	}

	protected function move() {
		wfProfileIn( __METHOD__ );
		list( $val, $type, $code, $offset ) =
			$this->nextToken();

		if( $type != ISToken::TNone ) {
			$this->mTokensCount++;
			if( !$this->mInterpreter->increaseTokensCount() )
				throw new ISUserVisibleException( 'toomanytokens', $this->mPos );
		}

		$token = new ISToken( $type, $val, $this->mPos );
		$this->mPos = $offset;
		wfProfileOut( __METHOD__ );
		$this->mPrev = $this->mCur;
		return $this->mCur = $token;
	}

	protected function parseToAST() {
		$outputQueue = array();
		$opStack = array();
		$expecting = self::ExpectingData;

		while( $this->move()->type != ISToken::TNone ) {
			/* Handling of all constants */
			if( $this->mCur->isDataToken() ) {
				if( $expecting != self::ExpectingData )
					throw new ISUserVisibleException( 'expectingdata', $this->mPos );
				switch( $this->mCur->type ) {
					case ISToken::TID:
						$outputQueue[] = new ISDataNode( $this->mCur->value, $this->mPos );
						break;
					case ISToken::TKeyword:
						switch( $this->mCur->value ) {
							case 'true':
								$outputQueue[] = new ISDataNode( new ISData( ISData::DBool, true ), $this->mPos );
								break;
							case 'false':
								$outputQueue[] = new ISDataNode( new ISData( ISData::DBool, false ), $this->mPos );
								break;
							case 'null':
								$outputQueue[] = new ISDataNode( new ISData(), $this->mPos );
								break;
						}
						break;
					default:
						$outputQueue[] = new ISDataNode( ISData::newFromPHPVar( $this->mCur->value ), $this->mPos );
				}
				$expecting = self::ExpectingOperator;
			}

			/* Foreach handling */
			elseif( $this->mCur->isOp( 'foreach' ) ) {
				if( $expecting != self::ExpectingData )
					throw new ISUserVisibleException( 'expectingdata', $this->mPos );
				$this->move();
				if( $this->mCur->type != ISToken::TID )
					throw new ISUserVisibleException( 'cantchangeconst', $pos );
				$varname = $this->mCur->value;
				$this->move();
				if( !$this->mCur->isOp( 'in' ) )
					throw new ISUserVisibleException( 'expectednotfound', $pos, array( 'in' ) );
				array_push( $opStack, new ISOperatorNode( ISOperatorNode::OForeach, $this->mPos, $varname ) );
			}

			/* Handling of other operators */
			elseif( $this->mCur->type == ISToken::TOp || $this->mCur->type == ISToken::TKeyword ) {
				$op1 = ISOperatorNode::parseOperator( $this->mCur->value, $expecting, $this->mPos );
				if( !$op1 )
					throw new ISUserVisibleException( 'expectingoperator', $this->mPos );
				if( $expecting == self::ExpectingOperator ) {
					while( !empty( $opStack ) ) {
						$op2 = end( $opStack );
						if( $op1->isRightAssociative() ?
							($op1->getPrecedence() < $op2->getPrecedence()) :
							($op1->getPrecedence() <= $op2->getPrecedence()) )
							$this->pushOp( $outputQueue, $opStack );
						else
							break;
					}
				}

				if( $this->mCur->isOp( 'catch' ) ) {
					$this->move();
					if( $this->mCur->type != ISToken::TID )
						throw new ISUserVisibleException( 'cantchangeconst', $pos );
					$op1->setData( $this->mCur->value );
				}

				if( $op1->getArgsNumber() ) {
					array_push( $opStack, $op1 );
					$expecting = self::ExpectingData;
				} else {
					$outputQueue[] = $op1;
					$expecting = self::ExpectingOperator;
				}
			}

			/* Functions */
			elseif( $this->mCur->type == ISToken::TFunction ) {
				if( $expecting != self::ExpectingData )
					throw new ISUserVisibleException( 'expectingdata', $this->mPos );
				array_push( $opStack, new ISOperatorNode( ISOperatorNode::OFunction, $this->mPos,
					$this->mCur->value ) );
			}

			/* Different right parenthesis */
			elseif( $this->mCur->type == ISToken::TSquareClose ) {
				if( $this->mPrev->isOp( '[' ) ) {
					$topToken = array_pop( $opStack );
					if( $topToken->getOperator() == ISOperatorNode::OArrayElement ) {
						array_push( $opStack, new ISOperatorNode( ISOperatorNode::OArrayElementSingle, $this->mPos ) );
						$this->pushOp( $outputQueue, $opStack );
					} else {
						$outputQueue[] = new ISDataNode(
							new ISData( ISData::DList, array() ), $this->mPos );
					}
					$expecting = self::ExpectingOperator;
					continue;
				}
				if( $expecting != self::ExpectingOperator )
					throw new ISUserVisibleException( 'expectingoperator', $this->mPos );
				for(;;) {
					if( empty( $opStack ) )
						throw new ISUserVisibleException( 'unbalancedbraces', $this->mPos );;
					$op = end( $opStack );
					$this->pushOp( $outputQueue, $opStack, true );
					if( $op->isLeftSquare() )
						break;
				}
			} elseif( $this->mCur->type == ISToken::TCurlyClose ) {
				if( $this->mPrev->isOp( ';' ) )
					array_pop( $opStack );
				elseif( $expecting != self::ExpectingOperator )
					throw new ISUserVisibleException( 'expectingoperator', $this->mPos );
				for(;;) {
					if( empty( $opStack ) )
						throw new ISUserVisibleException( 'unbalancedbraces', $this->mPos );;
					$op = end( $opStack );
					if( $op->getOperator() == '{' ) {
						array_pop( $opStack );
						break;
					} else {
						$this->pushOp( $outputQueue, $opStack );
					}
				}
				$expecting = self::ExpectingOperator;
			} elseif( $this->mCur->type == ISToken::TBraceClose ) {
				// Handle no-argument function
				if( $this->mPrev->isOp( '(' ) && count( $opStack ) >= 2 && $opStack[count($opStack) - 2]->isOp( ISOperatorNode::OFunction ) ) {
					array_pop( $opStack );
					$outputQueue[] = array_pop( $opStack );
					$expecting = self::ExpectingOperator;
					continue;
				}

				if( $expecting != self::ExpectingOperator )
					throw new ISUserVisibleException( 'expectingoperator', $this->mPos );
				for(;;) {
					if( empty( $opStack ) )
						throw new ISUserVisibleException( 'unbalancedbraces', $this->mPos );;
					$op = end( $opStack );
					if( $op->getOperator() == '(' ) {
						array_pop( $opStack );
						$topToken = end( $opStack );
						if( $topToken && $topToken->getOperator() == ISOperatorNode::OFunction ) {
							$this->pushOp( $outputQueue, $opStack );
						}
						break;
					} else {
						$this->pushOp( $outputQueue, $opStack );
					}
				}
			}
		}

		while( !empty( $opStack ) ) {
			$this->pushOp( $outputQueue, $opStack );
		}
		if( count( $outputQueue ) != 1 ) 
			throw new ISException( 'Invalid size of output queue' );	// Should never happen
		return $outputQueue[0];
	}

	function pushOp( &$queue, &$stack, $allowSquare = false ) {
		$node = array_pop( $stack );
		if( $node->isLeftSquare() && !$allowSquare ) {
			throw new ISUserVisibleException( 'unbalancedbraces', $this->mPos );
		}
		if( $node->getOperator() == ISOperatorNode::OLeftBrace ||
			$node->getOperator() == ISOperatorNode::OLeftCurly ) {
			throw new ISUserVisibleException( 'unbalancedbraces', $this->mPos );
		}
		for( $i = 0; $i < $node->getArgsNumber(); $i++ ) {
			if( $queue ) {
				$node->addChild( array_pop( $queue ) );
			} else {
				if( !($node->getOperator() == ';' && $i == 1) )
					throw new ISUserVisibleException( 'notenoughopargs', $this->mPos );
			}
		}
		$queue[] = $node;
	}
}

class ISToken {
	//Types of token
	const TNone = 'T_NONE';
	const TID = 'T_ID';
	const TFunction = 'T_FUNCTION';
	const TKeyword = 'T_KEYWORD';
	const TString = 'T_STRING';
	const TInt = 'T_INT';
	const TFloat = 'T_FLOAT';
	const TOp = 'T_OP';
	const TBraceClose = 'T_BRACE_CLOSE';
	const TSquareClose = 'T_SQUARE_CURLY';
	const TCurlyClose = 'T_CURLY_CLOSE';

	var $type;
	var $value;
	var $pos;
	
	public function __construct( $type = self::TNone, $value = null, $pos = 0 ) {
		$this->type = $type;
		$this->value = $value;
		$this->pos = $pos;
	}
	
	public function isOp( $op ) {
		return ( $this->type == self::TOp || $this->type == self::TKeyword )
			&& $this->value == $op;
	}
	
	public function isDataToken() {
		$types = array( self::TID, self::TString, self::TInt, self::TFloat );
		$keywords = array( 'true', 'false', 'null' );
		return in_array( $this->type, $types ) ||
			( $this->type == self::TKeyword && in_array( $this->value, $keywords ) );
	}
	
	function __toString() {
		return "{$this->value}";
	}
}
