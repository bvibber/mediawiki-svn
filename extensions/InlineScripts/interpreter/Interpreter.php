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

	var $mVars, $mOut, $mParser, $mFrame, $mCodeParser, $mLimits;

	// length,lcase,ccnorm,rmdoubles,specialratio,rmspecials,norm,count
	static $mFunctions = array(
		'lc' => 'funcLc',
		'out' => 'funcOut',
		'arg' => 'funcArg',
		'args' => 'funcArgs',
		'istranscluded' => 'funcIsTranscluded',
	);

	// Order is important. The punctuation-matching regex requires that
	//  ** comes before *, etc. They are sorted to make it easy to spot
	//  such errors.
	static $mOps = array(
		'!==', '!=', '!', 	// Inequality
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
		'in', 'true', 'false', 'null', 'contains', 'matches',
		'if', 'then', 'else', 'foreach', 'do',
	);

	public function __construct( $params ) {
		$this->resetState();
		$this->mCodeParser = new $params['parserClass']( $this );
		$this->mLimits = $params['limits'];
	}

	public function resetState() {
		$this->mVars = array();
		$this->mCode = '';
		$this->mOut = '';
	}

	protected function checkRecursionLimit( $rec ) {
		if( $rec > $this->mParser->is_maxDepth )
			$this->mParser->is_maxDepth = $rec;
		return $rec <= $this->mLimits['depth'];
	}

	protected function increaseEvaluationsCount() {
		$this->mParser->is_evalsCount++;
		return $this->mParser->is_evalsCount <= $this->mLimits['evaluations'];
	}

	public function increaseTokensCount() {
		$this->mParser->is_tokensCount++;
		return $this->mParser->is_tokensCount <= $this->mLimits['tokens'];
	}

	public function evaluateForOutput( $code, $parser, $frame ) {
		wfProfileIn( __METHOD__ );
		$this->resetState();
		$this->mParser = $parser;
		$this->mFrame = $frame;

		$ast = $this->getCodeAST( $code );
		$this->evaluateASTNode( $ast );
		wfProfileOut( __METHOD__ );
		return $this->mOut;
	}

	public function evaluate( $code, $parser, $frame ) {
		wfProfileIn( __METHOD__ );
		$this->resetState();
		$this->mParser = $parser;
		$this->mFrame = $frame;

		$ast = $this->getCodeAST( $code );
		wfProfileOut( __METHOD__ );
		return $this->evaluateASTNode( $ast )->toString();
	}

	public function getCodeAST( $code ) {
		global $wgMemc;
		$code = trim( $code );

		$memcKey = 'isparser:ast:' . md5( $code );
		$cached = $wgMemc->get( $memcKey );
		if( $cached instanceof ISParserOutput && !$cached->isOutOfDate() ) {
			$cached->appendTokenCount( $this );
			return $cached->getAST();
		}

		$out = $this->mCodeParser->parse( $code );
		$wgMemc->set( $memcKey, $out );
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
				$data = $this->evaluateASTNode( $r, $rec + 1 );
				if( $l->getType() != ISASTNode::NodeData || $l->getType() == ISDataNode::DNData )
					throw new ISUserVisibleException( 'cantchangeconst', $pos );
				switch( $l->getDataType() ) {
					case ISDataNode::DNVariable:
						$this->mVars[$l->getVar()] = $data;
						break;
				}
				return $data;

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
					throw new ISUserVisibleException( 'outofbounds', $ast->getPos(), array( count( $array->data, $index ) ) );
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
					if( $this->evaluateASTNode( $l )->toBool() )
						$this->evaluateASTNode( $onTrue );
					else
						$this->evaluateASTNode( $onFalse );
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
					$this->mVars[$ast->getData()] = $element;
					$this->evaluateASTNode( $r, $rec + 1 );
				}
				return new ISData();
			
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
					$arg = $this->evaluateASTNode( $arg );
				$funcName = self::$mFunctions[$ast->getData()];
				$result = $this->$funcName( $args, $ast->getPos() );
				return $result;
			default:
				throw new ISUserVisibleException( 'unexceptedop', $ast->getPos(), array( $op ) );
		}
	}

	function getDataNodeValue( $node ) {
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

	/** Functions */
	function funcLc( $args, $pos ) {
		global $wgContLang;
		if( !$args )
			throw new ISUserVisibleException( 'notenoughargs', $pos );

		return new ISData( ISData::DString, $wgContLang->lc( $args[0]->toString() ) );
	}

	function funcOut( $args, $pos ) {
		if( !$args )
			throw new ISUserVisibleException( 'notenoughargs', $pos );

		for( $i = 0; $i < count( $args ); $i++ )
			$args[$i] = $args[$i]->toString();
		$str = implode( "\n", $args );
		$this->mOut .= $str;
		return new ISData();
	}

	function funcArg( $args, $pos ) {
		if( !$args )
			throw new ISUserVisibleException( 'notenoughargs', $pos );

		$argName = $args[0]->toString();
		return new ISData( ISData::DString, $this->mFrame->getArgument( $argName ) );
	}

	function funcArgs( $args, $pos ) {
		return ISData::newFromPHPVar( $this->mFrame->getNumberedArguments() );
	}

	function funcIsTranscluded( $args, $pos ) {
		return new ISData( ISData::DBool, $this->mFrame->isTemplate() );
	}
}
