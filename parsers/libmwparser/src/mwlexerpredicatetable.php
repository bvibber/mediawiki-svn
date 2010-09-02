<?php
/*
 * Copyright 2010  Andreas Jonsson
 *
 * This file is part of libmwparser.
 *
 * Libmwparser is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * This table describes the properties of each lexer predicate.  Each
 * lexer predicate may have these properties:
 *
 * + name              The name of the predicate.
 * + close             The name of the corresponding close predicate. (optional)
 * + initallyDisabled  A list of "causes" (see below) that may disable the token on
 *                     a reset of the lexer. (optional)
 * + affects           A list of predicates or predicate types that are enabled or disabled
 *                     by the opening token corresponding to this predicate.  The inverse
 *                     of the affect is implied for the close predicate. (optional)
 * + mayNest           Indicates wheter the start token should be immediately disabled after
 *                     it has been accepted by the lexer.  If there is a corresponding close
 *                     predicate, a nesting level will be computed.
 * + haveNestingCount  Indicate if a counter for the nesting level should be kept for this
 *                     predicate.
 * + types             A list of types for this predicate.  If both 'html' and 'block' is set,
 *                     'blockHtml' is implied.
 * + openCalls         A list of method calls that will be executed when accepting an
 *                     opening token corresponding to this predicate. (optional)
 * + closeCalls        A list of method calls that will be executed when accepting a
 *                     closing token corresponding to this predicate. (optional)
 * + scope             A condition where the token will be disabled for the cause 'OPENED'.
 *                     Supported values: eol (optional)
 */
$predicates =
array(
    array(
          'name'              =>  'wikitextTableOpen',
          'close'             =>  'wikitextTableClose',
          'initiallyDisabled' =>  array(),
          'affects'           =>  array(new TypeEnable('wikitextTable', 'BLOCK_CONTEXT')),
          'mayNest'           =>  true,
          'haveNestingCount'  =>  true,
          'types'             =>  array('block', 'wikitextTable'),
          'pushesBlockContext'=>  true,
          ),
    array(
          'name'              => 'wikitextTableRow',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'types'             =>  array('block', 'wikitextTable'),
          'mayNest'           =>  true,
          ),
    array(
          'name'              => 'wikitextTableCell',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'types'             =>  array('block', 'wikitextTable'),
          'affects'           =>  array(new PredicateEnable('wikitextTableInlineCell', 'OPENED')),
          'mayNest'           =>  true,
          ),
    array(
          'name'              =>  'wikitextTableInlineCell',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT', 'OPENED'),
          'types'             =>  array('block', 'wikitextTable'),
          'mayNest'           =>  true,
          'scope'             =>  new Scope('eol', new PredicateEnable('wikitextTableInlineCell', 'OPENED')),
          ),
    array(
          'name'              =>  'wikitextTableHeading',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'types'             =>  array('block', 'wikitextTable'),
          'affects'           =>  array(new PredicateEnable('wikitextTableInlineHeading', 'OPENED')),
          'mayNest'           =>  true,
          ),
    array(
          'name'              =>  'wikitextTableInlineHeading',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT', 'OPENED'),
          'types'             =>  array('block', 'wikitextTable'),
          'mayNest'           =>  true,
          'scope'             =>  new Scope('eol', new PredicateEnable('wikitextTableInlineHeading', 'OPENED')),
          ),
    array(
          'name'              => 'wikitextListElement',
          'initiallyDisabled' => array(),
          'types'             => array('block'),
          'affects'           => array(new TypeDisable('block', 'WIKITEXT_BLOCK_OR_LINK')),
          'mayNest'           => false,
          'scope'             => new Scope('eol'),
          ),
    array(
          'name'              => 'indentedText',
          'initiallyDisabled' => array(),
          'types'             => array('block'),
          'mayNest'           => false,
          'scope'             => new Scope('eol'),
          ),
    array(
          'name'              => 'wikitextHeadingOpen',
          'close'             => 'wikitextHeadingClose',
          'initiallyDisabled' => array(),
          'affects'           => array(new TypeDisable('block', 'HEADING')),
          'types'             => array('block'),
          'mayNest'           => false,
          ),
    array(
          'name'              => 'tableOfContents',
          'initiallyDisabled' => array(),
          'types'             => array('block'),
          'mayNest'           => false,
          ),
    array(
          'name'              => 'htmlTableOpen',
          'close'             => 'htmlTableClose',
          'initiallyDisabled' =>  array(),
          'affects'           =>  array(new TypeEnable('htmlTable', 'BLOCK_CONTEXT')),
          'mayNest'           =>  true,
          'haveNestingCount'  =>  true,
          'types'             =>  array('block', 'htmlTable', 'html'),
          'pushesBlockContext'=>  true,
          ),
    array(
          'name'              => 'htmlTbodyOpen',
          'close'             => 'htmlTbodyClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'htmlTable', 'html'),
          ),
    array(
          'name'              => 'htmlTrOpen',
          'close'             => 'htmlTrClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'htmlTable', 'html'),
          ),
    array(
          'name'              => 'htmlTdOpen',
          'close'             => 'htmlTdClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'htmlTable', 'html'),
          ),
    array(
          'name'              => 'htmlThOpen',
          'close'             => 'htmlThClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'htmlTable', 'html'),
          ),
    array(
          'name'              => 'htmlCaptionOpen',
          'close'             => 'htmlCaptionClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'htmlTable', 'html'),
          ),
    array(
          'name'              => 'htmlUlOpen',
          'close'             => 'htmlUlClose',
          'initiallyDisabled' =>  array(),
          'mayNest'           =>  true,
          'haveNestingCount'  =>  true,
          'types'             =>  array('block', 'html'),
          'pushesBlockContext'=>  true,
          'affects'           =>  array(new PredicateEnable('htmlUlLiOpen', 'BLOCK_CONTEXT'),
                                        new PredicateEnable('htmlUlLiClose', 'BLOCK_CONTEXT')),
          ),
    array(
          'name'              => 'htmlUlLiOpen',
          'close'             => 'htmlUlLiClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'html'),
          ),
    array(
          'name'              => 'htmlOlOpen',
          'close'             => 'htmlOlClose',
          'initiallyDisabled' =>  array(),
          'mayNest'           =>  true,
          'haveNestingCount'  =>  true,
          'types'             =>  array('block', 'html'),
          'pushesBlockContext'=>  true,
          'affects'           =>  array(new PredicateEnable('htmlOlLiOpen', 'BLOCK_CONTEXT'),
                                        new PredicateEnable('htmlOlLiClose', 'BLOCK_CONTEXT')),
          ),
    array(
          'name'              => 'htmlOlLiOpen',
          'close'             => 'htmlOlLiClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'html'),
          ),
    array(
          'name'              => 'htmlDlOpen',
          'close'             => 'htmlDlClose',
          'initiallyDisabled' =>  array(),
          'mayNest'           =>  true,
          'haveNestingCount'  =>  true,
          'types'             =>  array('block', 'html'),
          'pushesBlockContext'=>  true,
          'affects'           =>  array(new PredicateEnable('htmlDdOpen', 'BLOCK_CONTEXT'),
                                        new PredicateEnable('htmlDdClose', 'BLOCK_CONTEXT'),
                                        new PredicateEnable('htmlDtOpen', 'BLOCK_CONTEXT'),
                                        new PredicateEnable('htmlDtClose', 'BLOCK_CONTEXT')),
          ),
    array(
          'name'              => 'htmlDdOpen',
          'close'             => 'htmlDdClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'html'),
          ),
    array(
          'name'              => 'htmlDtOpen',
          'close'             => 'htmlDtClose',
          'initiallyDisabled' =>  array('BLOCK_CONTEXT'),
          'affects'           =>  array(new TypeDisable('block', 'BLOCK')),
          'mayNest'           =>  true,
          'types'             =>  array('block', 'html'),
          ),
    array(
          'name'              => "htmlBr",
          'initiallyDisabled' => array(),
          'mayNest'           => true,
          'types'             => array('html'),
          ),
    array(
          'name'              => 'htmlPOpen',
          'close'             => 'htmlPClose',
          'initiallyDisabled' => array(),
          'mayNest'           => false,
          'types'             => array('html', 'block'),
          ),
    array(
          'name'              => "htmlBlockquoteOpen",
          'close'             => "htmlBlockquoteClose",
          'initiallyDisabled' => array(),
          'mayNest'           => false,
          'types'             => array('html', 'block'),
          'affects'           => array(new TypeDisable('block', 'BLOCKQUOTE')),
          ),
    array(
          'name'              => "internalLinkOpen",
          'close'             => "internalLinkClose",
          'initiallyDisabled' => array(),
          'mayNest'           => false,
          'types'             => array(),
          'affects'           => array(new PredicateDisable('externalLinkOpen', 'WIKITEXT_BLOCK_OR_LINK')),
          ),
    array(
          'name'              => "externalLinkOpen",
          'close'             => "externalLinkClose",
          'initiallyDisabled' => array(),
          'mayNest'           => false,
          'types'             => array(),
          ),
    array(
          'name'              => "mediaLinkOpen",
          'close'             => "mediaLinkClose",
          'initiallyDisabled' => array(),
          'mayNest'           => true,
          'haveNestingCount'  => true,
          'maxNestingLevel'   => 2,
          'types'             => array(),
          ),
      );

foreach(array('B', 'Del', 'I', 'Ins', 'U', 'Font', 'Big', 'Small', 'Sub', 'Sup', 'Cite',
              'Code', 'Em', 'S', 'Strike', 'Strong', 'Tt', 'Var', 'Span', 'Abbr')
        as $element) {
    array_push($predicates,
               array(
                     'name'              => "html${element}Open",
                     'close'             => "html${element}Close",
                     'initiallyDisabled' => array(),
                     'mayNest'           => false,
                     'types'             => array('html'),
                     )
               );
}

foreach(array('Center', 'Div') as $element) {
    array_push($predicates,
               array(
                     'name'              => "html${element}Open",
                     'close'             => "html${element}Close",
                     'initiallyDisabled' => array(),
                     'mayNest'           => true,
                     'types'             => array('html', 'block'),
                     'haveNestingCount'  => true,
                     'pushesBlockContext'=> true,
                     )
               );
}

foreach(array('H1', 'H2', 'H3', 'H4', 'H5', 'H6') as $hX) {
    array_push($predicates, 
               array(
                     'name'              => "html${hX}Open",
                     'close'             => "html${hX}Close",
                     'initiallyDisabled' => array(),
                     'mayNest'           => false,
                     'affects'           => array(new TypeDisable('block', 'HEADING')),
                     'types'             => array('html', 'block'),
                     ));
}

/*
 * Modifying a php array seems unreliable.  So, lets make a copy instead.
 */
$predicates_temp = array();

foreach($predicates as $pred) {
    if (in_array('block', $pred['types']) && in_array('html', $pred['types'])) {
        array_push($pred['types'], 'blockHtml');
    }
    array_push($predicates_temp, $pred);
}

$predicates = $predicates_temp;

/**
 * A "cause" encodes a reason for a predicate being disabled.  There
 * may be several independent causes for a predicate being disabled.
 * For instance, a HTML <td> token may be disabled because html tables
 * are disabled, or because block elements are disabled.
 */
$disabledCauses = array(
    'BLOCK',
    'HTML',
    'OPENED',
    'HEADING',
    'BLOCK_CONTEXT',
    'BLOCKQUOTE',
    'NESTING_LIMIT',
    'WIKITEXT_BLOCK_OR_LINK'  // It should be OK for these two causes
                              // to share the same bit, since they are never applied
                              // to the same predicate.
 );

define('CX', 'context');

function pred_method_name($name) {
    return 'on' . ucfirst($name);
}

function disabled_predicate_name($name) {
    return "${name}Disabled";
}

function get_predicate($name) {
    global $predicates;
    foreach($predicates as $pred) {
        if ($pred['name'] == $name) {
            return $name;
        }
    }
}

function nesting_level_name($pred) {
    $name = $pred['name'];
    return "${name}NestingLevel";
}

function to_underscore_separated_uc($name) {
    return implode('_', array_map(function($_) { return strtoupper($_); }, preg_split('/(?=[A-Z])/', $name)));
}

function block_context_item_name($pred) {
    $name = $pred['name'];
    return 'BCI_' . to_underscore_separated_uc($name);
}

function dc_mask_name($name) {
    return DISABLED_CAUSES_PREFIX . strtoupper($name);
}

function pred_token_name($name) {
    return to_underscore_separated_uc($name);
}

class MethodCall {
    private $name;
    private $params;

    function __construct($name, $params = array()) {
        $this->name = $name;
        $this->params = $params;
    }

    function getCode($indent) {
        return $indent . CX . '->' . $this->name . '(' . CX . $this->serialize_params() . ");\n";
    }

    function serialize_params() {
        $s = "";
        foreach ($this->params as $param) {
            $s .= ", $param";
        }
        return $s;
    }
}

abstract class Affect
{
    protected $name;
    protected $cause;

    public function __construct($predicateOrTypeName, $cause) {
        $this->name = $predicateOrTypeName;
        $this->cause = $cause;
    }

    abstract public function getCode($indent, $flag = false);
    abstract public function getInverse();
    abstract public function getCondition();

    protected function enable_expr($varName, $cause) {
        return CX . "->$varName &= ~ $cause;\n";
    }

    protected function disable_expr($varName, $cause) {
        return CX . "->$varName |= $cause;\n";
    }

    public function getName() {
        return $this->name;
    }

    public function getCause() {
        return $this->cause;
    }
}

class PredicateEnable extends Affect
{
    public function getCode($indent, $noMask = false) {
        $cause = $noMask ? $this->cause : dc_mask_name($this->cause);
        return $indent . $this->enable_expr(disabled_predicate_name($this->name), $cause);
    }

    public function getCondition() {
        return '((' . CX . '->' . disabled_predicate_name($this->name) . ' & ' . dc_mask_name($this->cause) . ') == 0 )';
    }

    public function getInverse() {
        return new PredicateDisable($this->name, $this->cause);
    }
}

class PredicateDisable extends Affect
{
    public function getCode($indent, $noMask = false) {
        $cause = $noMask ? $this->cause : dc_mask_name($this->cause);
        return $indent . $this->disable_expr(disabled_predicate_name($this->name), $cause);
    }

    public function getCondition() {
        return '((' .CX . '->' . disabled_predicate_name($this->name) . ' & ' . dc_mask_name($this->cause) . ') != 0 )';
    }

    public function getInverse() {
        return new PredicateEnable($this->name, $this->cause);
    }
}

class TypeEnable extends Affect
{
    public function getCode($indent, $noMask = false) {
        $cause = $noMask ? $this->cause : dc_mask_name($this->cause);
        return $indent . 'enable' . ucfirst($this->name) . "(context, $cause);\n";
    }

    public function getInverse() {
        return new TypeDisable($this->name, $this->cause);
    }

    public function getCondition() {
        return 'true'; // TODO
    }
}

class TypeDisable extends Affect
{
    public function getCode($indent, $noMask = false) {
        $cause = $noMask ? $this->cause : dc_mask_name($this->cause);
        return $indent . 'disable' . ucfirst($this->name) . "(context, $cause);\n";
    }

    public function getInverse() {
        return new TypeEnable($this->name, $this->cause);
    }

    public function getCondition() {
        return 'true'; // TODO
    }
}

class Scope {

    private $scope;
    private $affect;

    public function __construct($scopeName, $affect = 0) {
        $this->scope = $scopeName;
        $this->affect = $affect;
    }

    public function getName() {
        return $this->scope;
    }

    public function getCode($pred, $indent) {
        $code = '';
        if ($this->affect instanceof Affect) {
            $a = $this->affect;
        } else {
            $a = new PredicateDisable($pred['name'], 'OPENED');
        }
        $code .= $indent . 'if (' . $a->getCondition() . ") {\n";
        $code .= $a->getInverse()->getCode($indent . $indent);
        $code .= close_affects($pred, $indent . $indent);
        $code .= $indent . "}\n";
        return $code;
    }
}


function open_affects($pred, $indent) {
    $code = '';
    if (isset($pred['affects'])) {
        foreach($pred['affects'] as $affect) {
            $code .= $affect->getCode($indent);
            if ($affect instanceof TypeDisable && 
                in_array($affect->getName(), $pred['types']) &&
                isset($pred['close'])) {
                /*
                 * Since the corresponding token disables a type where
                 * the end token is a member, we need to explicitly
                 * reenable the end token.
                 */
                $e = new PredicateEnable($pred['close'], $affect->getCause());
                $code .= $e->getCode($indent);
            }
        }
    }
    return $code;
}

function close_affects($pred, $indent) {
    $code = '';
    if (isset($pred['affects'])) {
        foreach(array_reverse($pred['affects']) as $affect) {
            $code .= $affect->getInverse()->getCode($indent);
            if ($affect instanceof TypeEnable && in_array($affect->getName(), $pred['types'])) {
                /*
                 * Since the end token will disable a type where the
                 * start token is a member, we will explicitly
                 * reenable the start token.
                 */
                $e = new PredicateEnable($pred['name'], $affect->getCause());
                $code .= $e->getCode($indent);
            }
        }
    }
    return $code;
}


define('DISABLED_CAUSES_PREFIX', 'DC_');
define('INDENT', '    ');
define('MAX_NESTING_LEVEL', 'MAX_NESTING_LEVEL')



?>