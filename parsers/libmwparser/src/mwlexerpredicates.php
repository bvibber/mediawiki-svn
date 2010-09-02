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

#ifndef MWLEXERPREDICATES_H_
#define MWLEXERPREDICATES_H_

/*
 * This file is generated from mwlexerpredicates.php.  Don't edit directly!
 */
<?php
/*
 * This file is mwlexerpredicates.php.  Ignore the above message.
 */

require 'mwlexerpredicatetable.php';

$prototypes = array();
$methods = array();

function reset_expr($varName) {
    return INDENT . CX . '->' . disabled_predicate_name($varName) . " = 0;\n";
}

function scope_method_name($scope) {
    return 'on' . ucfirst($scope);
}

function nesting_level_ref($pred) {
    return CX . '->' . nesting_level_name($pred);
}

function grep_predicates($type) {
    global $predicates;
    return array_filter($predicates, function($_) use($type) { return in_array($type, $_['types']); });
}

function block_context_id($pred) {
    return '&' . CX . '->' . disabled_predicate_name($pred['name']);
}

function deactivate_block_context_name($pred) {
    return 'deactivate' . ucfirst($pred['name']);
}

function activate_block_context_name($pred) {
    return 'activate' . ucfirst($pred['name']);
}

function method_head($name, $returns = "void", $params = array()) {
    global $prototypes;
    $paramstring = "";
    if (isset($params)) {
        foreach($params as $param) {
            $paramstring .= ", $param";
        }
    }
    $head = "static inline ${returns} ${name}(struct MWLEXERCONTEXT_struct *context${paramstring})"; 
    array_push($prototypes, "$head;\n");
    return $head;
}

function enable_disable_method($type, $enable) {
    global $methods, $predicates;
    $name = ucfirst($type);
    $class = $enable ? 'PredicateEnable' : 'PredicateDisable';
    $head = method_head(($enable ? "enable" : "disable") . "${name}", "void", array("enum DISABLED_CAUSES_MASK cause"));

    $typePredicates = grep_predicates($type);
    $method = "$head {\n";
    foreach ($typePredicates as $tok) {
        $d = new $class($tok['name'], 'cause');
        $method .= $d->getCode(INDENT, true);
        if (isset($tok['close'])) {
            $d = new $class($tok['close'], 'cause');
            $method .= $d->getCode(INDENT, true);
        }
    }
    $method .= "}\n";
    array_push($methods, $method);
}

function method_calls($methodCalls) {
    $calls = "";
    foreach($methodCalls as $mc) {
        $calls .= $mc->getCode(INDENT);
    }
    return $calls;
}

function activate_deactivate_method($pred, $activate) {
    global $methods;
    $function_name = ($activate ? 'activate' : 'deactivate') . '_block_context_name';
    $affects_function = $activate ? 'open_affects' : 'close_affects';
    if (isset($pred['pushesBlockContext']) && $pred['pushesBlockContext']) {
        $head = method_head($function_name($pred));
        $method = "$head {\n";
        $method .= $affects_function($pred, INDENT);
        $t = new PredicateEnable($pred['close'], 'OPENED');
        if ($activate) {
            $method .= $t->getCode(INDENT);
        } else {
            $method .= $t->getInverse()->getCode(INDENT);
        }
        $method .= "}\n";
        array_push($methods, $method);
    }
}

function pred_method($pred) {
    global $methods;
    $head = method_head(pred_method_name($pred['name']));
    $method = "$head {\n";
    if (!$pred['mayNest']) {
        $d = new PredicateDisable($pred['name'], 'OPENED');
        $method .= $d->getCode(INDENT);
    } else if (isset($pred['haveNestingCount']) && $pred['haveNestingCount']) {
        if (isset($pred['maxNestingLevel'])) {
            $max = $pred['maxNestingLevel'];
        } else {
            $max = MAX_NESTING_LEVEL;
        }
        $method .= INDENT . nesting_level_ref($pred). "++;\n";
        $method .= INDENT . 'if (' . nesting_level_ref($pred) . " >= $max) {\n";
        $d = new PredicateDisable($pred['name'], 'NESTING_LIMIT');
        $method .= $d->getCode(INDENT . INDENT);
        $method .= INDENT . "}\n";
    }
    $method .= open_affects($pred, INDENT);
    if (isset($pred['close'])) {
        $t = new PredicateEnable($pred['close'], 'OPENED');
        $method .= $t->getCode(INDENT);
    }
    if (isset($pred['pushesBlockContext']) && $pred['pushesBlockContext']) {
        $method .= INDENT . 'pushBlockContext(' . CX . ', ' . block_context_id($pred) . ");\n";
    }
    if (isset($pred['openCalls'])) {
        $method .= method_calls($pred['openCalls']);
    }
    $method .= "}\n";
    array_push($methods, $method);
}

function close_pred_method($pred) {
    global $methods;
    if (!isset($pred['close'])) {
        return;
    }
    $head = method_head(pred_method_name($pred['close']));
    $method = "$head {\n";
    if (!$pred['mayNest']) {
        $d = new PredicateEnable($pred['name'], 'OPENED');
        $method .= $d->getCode(INDENT);
        $d = new PredicateDisable($pred['close'], 'OPENED');
        $method .= $d->getCode(INDENT);
        $method .= close_affects($pred, INDENT);
    } else if (isset($pred['haveNestingCount']) && $pred['haveNestingCount']) {
        $nl = nesting_level_ref($pred);
        $method .= INDENT . "${nl}--;\n";
        $e = new PredicateEnable($pred['name'], 'NESTING_LIMIT');
        $method .= $e->getCode(INDENT);
        $method .= INDENT . "if ($nl == 0) {\n";
        $d = new PredicateDisable($pred['close'], 'OPENED');
        $method .= $d->getCode(INDENT . INDENT);
        $method .= close_affects($pred, INDENT . INDENT);
        $method .= INDENT . "}\n";
    }
    if (isset($pred['pushesBlockContext']) && $pred['pushesBlockContext']) {
        $method .= INDENT . 'popBlockContext(' . CX . ', ' . block_context_id($pred) . ");\n";
    }
    if (isset($pred['closeCalls'])) {
            $method .= method_calls($pred['closeCalls']);
    }
    $method .= "}\n";
    array_push($methods, $method);
}

function scope_method($scope)
{
    global $predicates, $methods;
    $head = method_head(scope_method_name($scope));
    $method = "$head {\n";
    foreach($predicates as $pred) {
        if (isset($pred['scope'])  && $pred['scope']->getName() == $scope) {
            $method .= $pred['scope']->getCode($pred, INDENT);
        }
    }
    $method .= "}\n";
    array_push($methods, $method);
}

function reset_method() {
    global $predicates, $methods;
    $head = method_head('mwlexerpredicatesReset');
    $method = "$head {\n";
    
    foreach($predicates as $pred) {
        $method .= reset_expr($pred['name']);
        if (isset($pred['initiallyDisabled'])) {
            foreach($pred['initiallyDisabled'] as $cause) {
                $d = new PredicateDisable($pred['name'], $cause);
                $method .= $d->getCode(INDENT);
            }
        }
        if (isset($pred['close'])) {
            $method .= reset_expr($pred['close']);
            $d = new PredicateDisable($pred['close'], 'OPENED');
            $method .= $d->getCode(INDENT);
            if ($pred['mayNest'] && isset($pred['haveNestingCount']) && $pred['haveNestingCount']) {
                $method .= INDENT . nesting_level_ref($pred) . " = 0;\n";
            }
        }
    }
    $method .= INDENT . "context->emptyHtmlTagType = ANTLR3_TOKEN_INVALID;\n";
    $method .= INDENT . "context->inEmptyHtmlTag = false;\n";
    $method .= INDENT . "context->lookahead = 0;\n";
    $method .= "}\n";
    array_push($methods, $method);
}

function newline_pred_method() {
    global $predicates, $methods;
    $head = method_head('onNewlinePred');
    $method = "$head {\n";
    
    foreach(array_filter($predicates, 
                         function($_) {
                             return $_['scope'] == 'eol';
                         })
            as $tok) {
        $method .= disable_expr($tok['name'], 'OPENED');
        if (isset($tok['close'])) {
                $method .= disable_expr($tok['close'], 'OPENED');
        }
    }
    $method .= "}\n";
    array_push($methods, $method);
}

function html_match_end_token_method() {
    global $predicates, $methods;
    $preds = grep_predicates('html');
    $head = method_head("getEmptyHtmlEndToken", "ANTLR3_UINT32");
    $method = "${head} {\n";
    $method .= INDENT . 'switch (' . CX . "->emptyHtmlTagType) {\n";
    $i2 = INDENT . INDENT;
    $i3 = INDENT . INDENT . INDENT;
    foreach($preds as $pred) {
        if (isset($pred['close'])) {
            $method .= $i2 . 'case ' . pred_token_name($pred['name']) . ":\n";
            $method .= $i3 . 'return ' . pred_token_name($pred['close']) . ";\n";
        }
    }
    $method .= "${i2}default:\n";
    $method .= "${i3}return HTML_CLOSE_TAG;\n";
    $method .= INDENT . "}\n}\n";
    array_push($methods, $method);
}

function activate_deactivate_block_context_method($activate) {
    global $predicates, $methods;
    $function_name = ($activate ? 'activate' : 'deactivate');
    $head = method_head($function_name .'BlockContext', 'void', array('void *blockContextId'));
    $function_name .= '_block_context_name';
    $method = "$head {\n";
    $first = true;
    foreach ($predicates as $pred) {
        if (isset($pred['pushesBlockContext']) && $pred['pushesBlockContext']) {
            if ($first) {
                $first = false;
                $method .= INDENT;
            } else {
                $method .= ' else ';
            }
            $method .= 'if (blockContextId == ' . block_context_id($pred) . ") {\n";
            $method .= INDENT . INDENT . $function_name($pred) . '('. CX . ");\n";
            $method .= INDENT . '}';
        }
    }
    $method .= "\n}\n";
    array_push($methods, $method);
}

function close_block_context_method() {
    global $predicates, $methods;
    $head = method_head('closeBlockContext', 'void', array('void *blockContextId'));
    $method = "$head {\n";
    $first = true;
    foreach ($predicates as $pred) {
        if (isset($pred['pushesBlockContext']) && $pred['pushesBlockContext']) {
            if ($first) {
                $first = false;
                $method .= INDENT;
            } else {
                $method .= ' else ';
            }
            $method .= 'if (blockContextId == ' . block_context_id($pred) . ") {\n";
            $method .= INDENT . INDENT . pred_method_name($pred['close']) . '('. CX . ");\n";
            $method .= INDENT . '}';
        }
    }
    $method .= "\n}\n";
    array_push($methods, $method);
}



function push_block_context_method() {
    global $predicates, $methods;
    $head = method_head('pushBlockContext', 'void', array('void *blockContextId'));
    $method = "$head {\n";
    $method .= INDENT . 'pANTLR3_STACK s = ' . CX . "->blockContextStack;\n";
    $method .= INDENT . "void *cur = s->peek(s);\n";
    $method .= INDENT . "if (cur != NULL) {\n";
    $method .= INDENT . INDENT . 'deactivateBlockContext(' . CX .", cur);\n";
    $method .= INDENT .  "}\n";
    $method .= INDENT . 'activateBlockContext(' . CX . ", blockContextId);\n";
    $method .= INDENT . "s->push(s, blockContextId, NULL);\n";
    $method .= "}\n";
    array_push($methods, $method);
}

function pop_block_context_method() {
    global $predicates, $methods;
    $head = method_head('popBlockContext', 'void', array('void *blockContextId'));
    $method = "$head {\n";
    $method .= INDENT . 'pANTLR3_STACK s = ' . CX . "->blockContextStack;\n";
    $method .= INDENT . "void *cur = s->peek(s);\n";
    $method .= INDENT . "deactivateBlockContext(" . CX . ", cur);\n";
    $method .= INDENT . "while (cur != blockContextId) {\n";
    $method .= INDENT . INDENT . "closeBlockContext(" . CX . ", cur);\n";
    $method .= INDENT . INDENT . "cur = s->peek(s);\n";
    $method .= INDENT . "}\n";
    $method .= INDENT . "s->pop(s);\n";
    $method .= INDENT . "cur = s->peek(s);\n";
    $method .= INDENT . "if (cur != NULL) {\n";
    $method .= INDENT . INDENT . "activateBlockContext(" . CX . ", cur);\n";
    $method .= INDENT . "}\n";
    $method .= "}\n";
    array_push($methods, $method);
}

function save_context_method() {
    global $predicates, $methods;
    $head = method_head('saveContext', 'void', array('MWLEXERCONTEXT_BACKUP *back'));
    $method = "$head {\n";
    foreach ($predicates as $pred) {
        $name = disabled_predicate_name($pred['name']);
        $method .= INDENT . 'back->' . $name . ' = ' . CX . '->' . $name . ";\n";
        if (isset($pred['close'])) {
            $name = disabled_predicate_name($pred['close']);
            $method .= INDENT . 'back->' . $name . ' = ' . CX . '->' . $name .";\n";
        }
        if ($pred['mayNest'] && isset($pred['haveNestingCount']) && $pred['haveNestingCount']) {
            $name = nesting_level_name($pred);
            $method .= INDENT . 'back->' . $name . ' = ' . CX . '->' . $name. ";\n";
        }
    }
    $method .= INDENT . "pANTLR3_STACK s = " . CX . "->blockContextStack;\n";
    $method .= INDENT . "pANTLR3_VECTOR v = back->blockContextStack;\n";
    $method .= INDENT . "v->count = 0;\n";
    $method .= INDENT . "while (s->size(s) > 0) {\n";
    $method .= INDENT . INDENT . "v->add(v, s->peek(s), NULL);\n";
    $method .= INDENT . INDENT . "s->pop(s);\n";
    $method .= INDENT . "}\n";
    $method .= INDENT . "int i;\n";
    $method .= INDENT . "for (i = v->count - 1 ; i >= 0; i--) {\n";
    $method .= INDENT . INDENT . "s->push(s, v->get(v, i), NULL);\n";
    $method .= INDENT . "}\n";
    $method .= INDENT . CX . "->emptyHtmlTagType = back->emptyHtmlTagType;\n";
    $method .= INDENT . CX . "->inEmptyHtmlTag   = back->inEmptyHtmlTag;\n"; 
    $method .= "}\n";
    array_push($methods, $method);
}

function restore_context_method() {
    global $predicates, $methods;
    $head = method_head('restoreContext', 'void', array('MWLEXERCONTEXT_BACKUP *back'));
    $method = "$head {\n";
    foreach ($predicates as $pred) {
        $name = disabled_predicate_name($pred['name']);
        $method .= INDENT . CX . '->' . $name . ' = back->' . $name . ";\n";
        if (isset($pred['close'])) {
            $name = disabled_predicate_name($pred['close']);
            $method .= INDENT . CX . '->' . $name . ' = back->' . $name .";\n";
        }
        if ($pred['mayNest'] && isset($pred['haveNestingCount']) && $pred['haveNestingCount']) {
            $name = nesting_level_name($pred);
            $method .= INDENT . CX . '->' . $name . ' = back->' . $name. ";\n";
        }
    }
    $method .= INDENT . "pANTLR3_STACK s = context->blockContextStack;\n";
    $method .= INDENT . "pANTLR3_VECTOR v = back->blockContextStack;\n";
    $method .= INDENT . "int i;\n";
    $method .= INDENT . "for (i = v->count - 1 ; i >= 0; i--) {\n";
    $method .= INDENT . INDENT . "s->push(s, v->get(v, i), NULL);\n";
    $method .= INDENT . "}\n";
    $method .= INDENT . "back->emptyHtmlTagType = " . CX . "->emptyHtmlTagType;\n";
    $method .= INDENT . "back->inEmptyHtmlTag   = " . CX . "->inEmptyHtmlTag;\n"; 
    $method .= "}\n";
    array_push($methods, $method);
}


reset_method();

foreach (array("eol") as $scope) {
    scope_method($scope);
}

activate_deactivate_block_context_method(true);
activate_deactivate_block_context_method(false);
push_block_context_method();
pop_block_context_method();
close_block_context_method();
save_context_method();
restore_context_method();

foreach (array("block", "html", "wikitextTable", "htmlTable", "blockHtml") as $type) {
    enable_disable_method($type, true);
    enable_disable_method($type, false);
}

foreach ($predicates as $pred) {
    pred_method($pred);
    close_pred_method($pred);
    activate_deactivate_method($pred, true);
    activate_deactivate_method($pred, false);
}

html_match_end_token_method()

?>


/**
 * Masks used for specifying different flags representing causes for a
 * token being disabled.  If any such flag is set, the token is
 * disabled.
 */
enum DISABLED_CAUSES_MASK {
<?php
    $i = 0;
    foreach($disabledCauses as $cause) {
        echo INDENT . DISABLED_CAUSES_PREFIX . "${cause} = 1 << $i,\n";
        $i++;
    }
echo ("#if ($i > PRED_SIZE_BITS)\n#error To many causes for predicate type!\n#endif\n");

?>
};


<?php
foreach($prototypes as $proto) {
    echo "$proto";
}
?>


<?php
foreach($methods as $method) {
    echo($method);
    echo("\n");
}
?>

#endif