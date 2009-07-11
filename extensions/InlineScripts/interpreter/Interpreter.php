<?php
/**
 * Interpreter for MediaWiki inline scripts
 * Copyright (C) Victor Vasiliev, Andrew Garrett, 2008-2009.
 * Distributed under GNU GPL v2 or later terms.
 */

require_once( 'Utils.php' );

class InlineScriptInterpreter {
	/**
	 * Used to invalidate AST cache. Increment whenever you change
	 * code parser or $mFunctions/$mOps
	 */
	const ParserVersion = 1;

	var $mVars, $mOut, $mParser, $mFrame, $mCodeParser;

	// length,lcase,ccnorm,rmdoubles,specialratio,rmspecials,norm,count
	static $mFunctions = array(
		'out' => 'funcOut',

		/* String functions */
		'lc' => 'funcLc',
		'uc' => 'funcUc',
		'ucfirst' => 'funcUcFirst',
		'urlencode' => 'funcUrlencode',
		'grammar' => 'funcGrammar',
		'plural' => 'funcPlural',
		'anchorencode' => 'funcAnchorEncode',
		'strlen' => 'funcStrlen',
		'substr' => 'funcSubstr',
		'strreplace' => 'funcStrreplace',
		'split' => 'funcSplit', 

		/* Array functions */
		'join' => 'funcJoin',
		'count' => 'funcCount',

		/* Parser interaction functions */
		'arg' => 'funcArg',
		'args' => 'funcArgs',
		'istranscluded' => 'funcIsTranscluded',
		'parse' => 'funcParse',

		/* Cast functions */
		'string' => 'castString',
		'int' => 'castInt',
		'float' => 'castFloat',
		'bool' => 'castBool',
	);

	// Order is important. The punctuation-matching regex requires that
	//  ** comes before *, etc. They are sorted to make it easy to spot
	//  such errors.
	static $mOps = array(
		'!==', '!=', '!', 	// Inequality
		'+=', '-=',         // Setting 1
		'*=', '/=',         // Setting 2
		'**', '*', 			// Multiplication/exponentiation
		'/', '+', '-', '%', // Other arithmetic
		'&', '|', '^', 		// Logic
		'?', ':', 			// Ternery
		'<=','<', 			// Less than
		'>=', '>', 			// Greater than
		'===', '==', '=', 	// Equality
		',', ';',           // Comma, semicolon
		'(', '[', '{',      // Braces
	);
	static $mKeywords = array(
		'in', 'true', 'false', 'null', 'contains', 'break',
		'if', 'then', 'else', 'foreach', 'do', 'try', 'catch',
		'continue', 'isset', 'unset',
	);

	public function __construct() {
		global $wgInlineScriptsParserParams;
		$this->resetState();
		$this->mCodeParser = new $wgInlineScriptsParserParams['parserClass']( $this );
	}

	public function resetState() {
		$this->mVars = array();
		$this->mOut = '';
	}

	protected function checkRecursionLimit( $rec ) {
		global $wgInlineScriptsParserParams;
		if( $rec > $this->mParser->is_maxDepth )
			$this->mParser->is_maxDepth = $rec;
		return $rec <= $wgInlineScriptsParserParams['limits']['depth'];
	}

	protected function increaseEvaluationsCount() {
		global $wgInlineScriptsParserParams;
		$this->mParser->is_evalsCount++;
		return $this->mParser->is_evalsCount <= $wgInlineScriptsParserParams['limits']['evaluations'];
	}

	public function increaseTokensCount() {
		global $wgInlineScriptsParserParams;
		$this->mParser->is_tokensCount++;
		return $this->mParser->is_tokensCount <= $wgInlineScriptsParserParams['limits']['tokens'];
	}

	public function evaluateForOutput( $code, $parser, $frame, $resetState = true ) {
		wfProfileIn( __METHOD__ );
		if( $resetState )
			$this->resetState();
		$this->mParser = $parser;
		$this->mFrame = $frame;

		$ast = $this->getCodeAST( $code );
		$this->evaluateASTNode( $ast );
		wfProfileOut( __METHOD__ );
		return $this->mOut;
	}

	public function evaluate( $code, $parser, $frame, $resetState = true ) {
		wfProfileIn( __METHOD__ );
		if( $resetState )
			$this->resetState();
		$this->mParser = $parser;
		$this->mFrame = $frame;

		$ast = $this->getCodeAST( $code );
		wfProfileOut( __METHOD__ );
		return $this->evaluateASTNode( $ast )->toString();
	}

	public function getCodeAST( $code ) {
		global $parserMemc;
		static $ASTCache;

		wfProfileIn( __METHOD__ );
		$code = trim( $code );

		$memcKey = 'isparser:ast:' . md5( $code );
		if( isset( $ASTCache[$memcKey] ) ) {
			wfProfileOut( __METHOD__ );
			return $ASTCache[$memcKey];
		}

		$cached = $parserMemc->get( $memcKey );
		if( @$cached instanceof ISParserOutput && !$cached->isOutOfDate() ) {
			$cached->appendTokenCount( $this );
			$ASTCache[$memcKey] = $cached->getAST();
			wfProfileOut( __METHOD__ );
			return $cached->getAST();
		}

		$out = $this->mCodeParser->parse( $code );
		$parserMemc->set( $memcKey, $out );
		$ASTCache[$memcKey] = $out->getAST();
		wfProfileOut( __METHOD__ );
		return $out->getAST();
	}

	public function evaluateASTNode( $ast, $rec = 0 ) {
		if( $ast instanceof ISDataNode ) {
			return $this->getDataNodeValue( $ast );
		}

		if( !$this->checkRecursionLimit( $rec ) )
			throw new ISUserVisibleException( 'recoverflow', $ast->getPos() );
		if( !$this->increaseEvaluationsCount() )
			throw new ISUserVisibleException( 'toomanyevals', $ast->getPos() );

		@list( $l, $r ) = $ast->getChildren();
		$op = $ast->getOperator();
		switch( $op ) {
			/* Math */
			case ISOperatorNode::OMul:
			case ISOperatorNode::ODiv:
			case ISOperatorNode::OMod:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				$rdata = $this->evaluateASTNode( $r, $rec + 1 );
				return ISData::mulRel( $ldata, $rdata, $op, $ast->getPos() );
			case ISOperatorNode::OSum:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				$rdata = $this->evaluateASTNode( $r, $rec + 1 );
				return ISData::sum( $ldata, $rdata );
			case ISOperatorNode::OSub:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				$rdata = $this->evaluateASTNode( $r, $rec + 1 );
				return ISData::sub( $ldata, $rdata );
			case ISOperatorNode::OPow:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				$rdata = $this->evaluateASTNode( $r, $rec + 1 );
				return ISData::pow( $ldata, $rdata );
			case ISOperatorNode::OPositive:
				return $this->evaluateASTNode( $l, $rec + 1 );
			case ISOperatorNode::ONegative:
				$data = $this->evaluateASTNode( $l, $rec + 1 );
				return ISData::unaryMinus( $data );

			/* Statement seperator */
			case ISOperatorNode::OStatementSeperator:
				// Linearize tree for ";" to allow code with many statements
				$statements = array();
				if( $r )
					array_unshift( $statements, $r );
				while( $l->isOp( ';' ) ) {
					list( $l, $r ) = $l->getChildren();
					array_unshift( $statements, $r );
				}
				array_unshift( $statements, $l );

				foreach( $statements as $node )
					$result = $this->evaluateASTNode( $node, $rec + 1 );
				return $result;

			/* Logic */
			case ISOperatorNode::OInvert:
				$data = $this->evaluateASTNode( $l, $rec + 1 );
				return ISData::boolInvert( $data );
			case ISOperatorNode::OAnd:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				if( $ldata->toBool() ) {
					return ISData::castTypes( $this->evaluateASTNode( $r, $rec + 1 ), ISData::DBool );
				} else {
					return new ISData( ISData::DBool, false );
				}
			case ISOperatorNode::OOr:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				if( !$ldata->toBool() ) {
					return ISData::castTypes( $this->evaluateASTNode( $r, $rec + 1 ), ISData::DBool );
				} else {
					return new ISData( ISData::DBool, true );
				}
			case ISOperatorNode::OXor:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				$rdata = $this->evaluateASTNode( $r, $rec + 1 );
				return new ISData( ISData::DBool, $ldata->toBool() xor $rdata->toBool() );

			/* Comparsions */
			case ISOperatorNode::OEqualsTo:
			case ISOperatorNode::ONotEqualsTo:
			case ISOperatorNode::OEqualsToStrict:
			case ISOperatorNode::ONotEqualsToStrict:
			case ISOperatorNode::OGreater:
			case ISOperatorNode::OGreaterOrEq:
			case ISOperatorNode::OLess:
			case ISOperatorNode::OLessOrEq:
				$ldata = $this->evaluateASTNode( $l, $rec + 1 );
				$rdata = $this->evaluateASTNode( $r, $rec + 1 );
				return ISData::compareOp( $ldata, $rdata, $op );

			/* Variable assignment */
			case ISOperatorNode::OSet:
			case ISOperatorNode::OSetAdd:
			case ISOperatorNode::OSetSub:
			case ISOperatorNode::OSetMul:
			case ISOperatorNode::OSetDiv:
				if( $l->isOp( ISOperatorNode::OArrayElement ) || $l->isOp( ISOperatorNode::OArrayElementSingle ) ) {
					$datanode = $r;
					$keys = array();
					while( $l->isOp( ISOperatorNode::OArrayElement ) || $l->isOp( ISOperatorNode::OArrayElementSingle ) ) {
						@list( $l, $r ) = $l->getChildren();
						array_unshift( $keys, $r ? $r : null );
					}
					if( $l->getType() != ISASTNode::NodeData || $l->getType() == ISDataNode::DNData )
						throw new ISUserVisibleException( 'cantchangeconst', $pos );
					$array = $this->getDataNodeValue( new ISDataNode( $l->getVar(), 0 ) );
					foreach( $keys as &$key )
						if( $key )
							$key = $this->evaluateASTNode( $key, $rec + 1 );
					$val = $this->evaluateASTNode( $datanode, $rec + 1 );
					$array->setValueByIndices( $val, $keys );
					$this->mVars[$l->getVar()] = $array;
					return $val;
				} else {
					if( $l->getType() != ISASTNode::NodeData || $l->getType() == ISDataNode::DNData )
						throw new ISUserVisibleException( 'cantchangeconst', $pos );
					$val = $this->getValueForSetting( @$this->mVars[$l->getVar()], 
						$this->evaluateASTNode( $r, $rec + 1 ), $op );
					return $this->mVars[$l->getVar()] = $val;
				}

			/* Arrays */
			case ISOperatorNode::OArray:
				$array = array();
				while( $l->isOp( ',' ) ) {
					list( $l, $r )  = $l->getChildren();
					$array[] = $r;
				}
				$array[] = $l;
				$array = array_reverse( $array );
				foreach( $array as &$element )
					$element = $this->evaluateASTNode( $element, $rec + 1 );
				return new ISData( ISData::DList, $array );
			case ISOperatorNode::OArrayElement:
				$array = $this->evaluateASTNode( $l, $rec + 1 );
				$index = $this->evaluateASTNode( $r, $rec + 1 )->toInt();
				if( $array->type != ISData::DList ) 
					throw new ISUserVisibleException( 'notanarray', $ast->getPos(), array( $array->type ) );
				if( count( $array->data ) <= $index )
					throw new ISUserVisibleException( 'outofbounds', $ast->getPos(), array( count( $array->data ), $index ) );
				return $array->data[$index];

			/* Flow control (if, foreach, etc) */
			case ISOperatorNode::OTrinary:
				if( !$r->isOp( ':' ) )
					throw new ISUserVisibleException( 'expectednotfound', $pos, array( ':' ) );
				$cond = $this->evaluateASTNode( $l, $rec + 1 );
				list( $onTrue, $onFalse ) = $r->getChildren();
				if( $cond->toBool() )
					return $this->evaluateASTNode( $onTrue, $rec + 1 );
				else
					return $this->evaluateASTNode( $onFalse, $rec + 1 );
			case ISOperatorNode::OIf:
				if( !$l->isOp( ISOperatorNode::OThen ) )
					throw new ISUserVisibleException( 'exceptednotfound', $ast->getPos(), array( 'then' ) );
				list( $l, $r ) = $l->getChildren();
				if( $r->isOp( ISOperatorNode::OElse ) ) {
					list( $onTrue, $onFalse ) = $r->getChildren();
					if( $this->evaluateASTNode( $l, $rec + 1 )->toBool() )
						$this->evaluateASTNode( $onTrue, $rec + 1 );
					else
						$this->evaluateASTNode( $onFalse, $rec + 1 );
				} else {
					if( $this->evaluateASTNode( $l, $rec + 1 )->toBool() )
						$this->evaluateASTNode( $r, $rec + 1 );
				}
				return new ISData();
			case ISOperatorNode::OForeach:
				if( !$l->isOp( ISOperatorNode::ODo ) )
					throw new ISUserVisibleException( 'exceptednotfound', $ast->getPos(), array( 'do' ) );
				list( $l, $r ) = $l->getChildren();
				$array = $this->evaluateASTNode( $l, $rec + 1 );
				if( $array->type != ISData::DList )
					throw new ISUserVisibleException( 'invalidforeach', $ast->getPos(), array( $array->type ) );
				foreach( $array->data as $element ) {
					try {
						$this->mVars[$ast->getData()] = $element;
						$this->evaluateASTNode( $r, $rec + 1 );
					} catch( ISUserVisibleException $e ) {
						if( $e->getExceptionID() == 'break' )
							break;
						elseif( $e->getExceptionID() == 'continue' )
							continue;
						else
							throw $e;
					}
				}
				return new ISData();
			case ISOperatorNode::OTry:
				if( $l->isOp( ISOperatorNode::OCatch ) ) {
					list( $code, $errorHandler ) = $l->getChildren();
					try {
						$val = $this->evaluateASTNode( $code, $rec + 1 );
					} catch( ISUserVisibleException $e ) {
						if( in_array( $e->getExceptionID(), array( 'break', 'continue' ) ) )
							throw $e;
						$varname = $l->getData();
						$old = wfSetVar( $this->mVars[$varname], 
							new ISData( ISData::DString, $e->getExceptionID() ) );
						$val = $this->evaluateASTNode( $errorHandler, $rec + 1 );
						$this->mVars[$varname] = $old;
					}
					return $val;
				} else {
					try {
						return $this->evaluateASTNode( $l, $rec + 1 );
					} catch( ISUserVisibleException $e ) {
						return new ISData();
					}
				}

			/* break/continue */
			case ISOperatorNode::OBreak:
				throw new ISUserVisibleException( 'break', $ast->getPos() );
			case ISOperatorNode::OContinue:
				throw new ISUserVisibleException( 'continue', $ast->getPos() );

			/* isset/unset */
			case ISOperatorNode::OUnset:
				if( $l->getType() == ISASTNode::NodeData && $l->getDataType() == ISDataNode::DNVariable ) {
					if( isset( $this->mVars[$l->getVar()] ) )
						unset( $this->mVars[$l->getVar()] );
					break;
				} else {
					throw new ISUserVisibleException( 'cantchangeconst', $ast->getPos() );
				}
			case ISOperatorNode::OIsset:
				if( $l->getType() == ISASTNode::NodeData && $l->getDataType() == ISDataNode::DNVariable ) {
					return new ISData( ISData::DBool, isset( $this->mVars[$l->getVar()] ) );
				} elseif( $l->isOp( ISOperatorNode::OArrayElement ) ) {
					$indices = array();
					while( $l->isOp( ISOperatorNode::OArrayElement ) ) {
						list( $l, $r ) = $l->getChildren();
						array_unshift( $indices, $r );
					}
					if( !($l->getType() == ISASTNode::NodeData && $l->getDataType() == ISDataNode::DNVariable) )
						throw new ISUserVisibleException( 'cantchangeconst', $ast->getPos() );
					foreach( $indices as &$idx )
						$idx = $this->evaluateASTNode( $idx )->toInt();
					$var = $l->getVar();

					if( !isset( $this->mVars[$var] ) )
						return new ISData( ISData::DBool, false );
					return new ISData( ISData::DBool, $this->mVars[$var]->checkIssetByIndices( $indices ) );
				} else {
					throw new ISUserVisibleException( 'cantchangeconst', $ast->getPos() );
				}

			/* Functions */
			case ISOperatorNode::OFunction:
				$args = array();
				if( $l ) {
					while( $l->isOp( ',' ) ) {
						@list( $l, $r ) = $l->getChildren();
						array_unshift( $args, $r );
					}
					array_unshift( $args, $l );
				}
				foreach( $args as &$arg )
					$arg = $this->evaluateASTNode( $arg, $rec + 1 );
				$funcName = self::$mFunctions[$ast->getData()];
				$result = $this->$funcName( $args, $ast->getPos() );
				return $result;
			default:
				throw new ISUserVisibleException( 'unexceptedop', $ast->getPos(), array( $op ) );
		}
	}

	protected function getDataNodeValue( $node ) {
		switch( $node->getDataType() ) {
			case ISDataNode::DNData:
				return $node->getData();
			case ISDataNode::DNVariable:
				$varname = $node->getVar();
				if( isset( $this->mVars[$varname] ) )
					return $this->mVars[$varname];
				else
					return new ISData();
		}
	}

	protected function getValueForSetting( $old, $new, $set ) {
		switch( $set ) {
			case ISOperatorNode::OSetAdd:
				return ISData::sum( $old, $new );
			case ISOperatorNode::OSetSub:
				return ISData::sub( $old, $new );
			case ISOperatorNode::OSetMul:
				return ISData::mulRel( $old, $new, '*', 0 );
			case ISOperatorNode::OSetDiv:
				return ISData::mulRel( $old, $new, '/', 0 );
			default:
				return $new;
		}
	}

	protected function checkParamsCount( $args, $pos, $count ) {
		if( count( $args ) < $count )
			throw new ISUserVisibleException( 'notenoughargs', $pos );
	}

	/** Functions */
	protected function funcOut( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );

		for( $i = 0; $i < count( $args ); $i++ )
			$args[$i] = $args[$i]->toString();
		$str = implode( "\n", $args );
		$this->mOut .= $str;
		return new ISData();
	}

	protected function funcArg( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );

		$argName = $args[0]->toString();
		$default = isset( $args[1] ) ? $args[1] : new ISData();
		if( $this->mFrame->getArgument( $argName ) === false )
			return $default;
		else
			return new ISData( ISData::DString, $this->mFrame->getArgument( $argName ) );
	}

	protected function funcArgs( $args, $pos ) {
		return ISData::newFromPHPVar( $this->mFrame->getNumberedArguments() );
	}

	protected function funcIsTranscluded( $args, $pos ) {
		return new ISData( ISData::DBool, $this->mFrame->isTemplate() );
	}

	protected function funcParse( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );

		$text = $args[0]->toString();
		$oldOT = $this->mParser->mOutputType;
		$this->mParser->setOutputType( Parser::OT_PREPROCESS );
		$parsed = $this->mParser->replaceVariables( $text, $this->mFrame );
		$parsed = $this->mParser->mStripState->unstripBoth( $parsed );
		$this->mParser->setOutputType( $oldOT );
		return new ISData( ISData::DString, $parsed );
	}
	
	protected function funcLc( $args, $pos ) {
		global $wgContLang;
		$this->checkParamsCount( $args, $pos, 1 );
		return new ISData( ISData::DString, $wgContLang->lc( $args[0]->toString() ) );
	}

	protected function funcUc( $args, $pos ) {
		global $wgContLang;
		$this->checkParamsCount( $args, $pos, 1 );
		return new ISData( ISData::DString, $wgContLang->uc( $args[0]->toString() ) );
	}

	protected function funcUcFirst( $args, $pos ) {
		global $wgContLang;
		$this->checkParamsCount( $args, $pos, 1 );
		return new ISData( ISData::DString, $wgContLang->ucfirst( $args[0]->toString() ) );
	}

	protected function funcUrlencode( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );
		return new ISData( ISData::DString, urlencode( $args[0]->toString() ) );
	}

	protected function funcAnchorEncode( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );

		$s = urlencode( $args[0]->toString() );
		$s = strtr( $s, array( '%' => '.', '+' => '_' ) );
		$s = str_replace( '.3A', ':', $s );

		return new ISData( ISData::DString, $s );
	}

	protected function funcGrammar( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 2 );
		list( $case, $word ) = $args;
		$res = $this->mParser->getFunctionLang()->convertGrammar(
			$word->toString(), $case->toString() );
		return new ISData( ISData::DString, $res );
	}

	protected function funcPlural( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 2 );
		$num = $args[0]->toInt();
		for( $i = 1; $i < count( $args ); $i++ )
			$forms[] = $args[$i]->toString();
		$res = $this->mParser->getFunctionLang()->convertPlural( $num, $forms );
		return new ISData( ISData::DString, $res );
	}

	protected function funcStrlen( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );
		return new ISData( ISData::DInt, mb_strlen( $args[0]->toString() ) );
	}

	protected function funcSubstr( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 3 );
		$s = $args[0]->toString();
		$start = $args[1]->toInt();
		$end = $args[2]->toInt();
		return new ISData( ISData::DString, mb_substr( $s, $start, $end ) );
	}

	protected function funcStrreplace( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 3 );
		$s = $args[0]->toString();
		$old = $args[1]->toString();
		$new = $args[2]->toString();
		return new ISData( ISData::DString, str_replace( $old, $new, $s ) );
	}

	protected function funcSplit( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 2 );
		$list = explode( $args[0]->toString(), $args[1]->toString() );
		return ISData::newFromPHPVar( $list );
	}

	protected function funcJoin( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 2 );
		$seperator = $args[0]->toString();
		if( $args[1]->type == ISData::DList ) {
			$bits = $args[1]->data;
		} else {
			$bits = array_slice( $args, 1 );
		}
		foreach( $bits as &$bit )
			$bit = $bit->toString();
		return new ISData( ISData::DString, implode( $seperator, $bits ) );
	}

	protected function funcCount( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );
		return new ISData( ISData::DInt, count( $args[0]->toList()->data ) );
	}

	protected function castString( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );
		return ISData::castTypes( $args[0], ISData::DString );
	}
	
	protected function castInt( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );
		return ISData::castTypes( $args[0], ISData::DInt );
	}

	protected function castFloat( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );
		return ISData::castTypes( $args[0], ISData::DFloat );
	}
	
	protected function castBool( $args, $pos ) {
		$this->checkParamsCount( $args, $pos, 1 );
		return ISData::castTypes( $args[0], ISData::DBool );
	}
}
