/*
 * The below content is generated from mwlexerpredicatedefs.php. Don't edit directly!
 */
<?php
/*
 * This file is mwlexerpredicatedefs.php.  Ignore the above message.
 */

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

include 'mwlexerpredicatetable.php';

define('PRED_TYPE', 'ANTLR3_UINT8');
define('PRED_SIZE_BITS', 8);
define('COUNT_TYPE', 'unsigned int');

foreach ($predicates as $pred) {
    echo(PRED_TYPE . ' ' . disabled_predicate_name($pred['name']) . ";\n");
    if (isset($pred['close'])) {
        echo(PRED_TYPE . ' ' . disabled_predicate_name($pred['close']) . ";\n");
    }
}

foreach ($predicates as $pred) {
    if ($pred['mayNest'] && isset($pred['haveNestingCount']) && $pred['haveNestingCount']) {
        echo(COUNT_TYPE . ' ' . nesting_level_name($pred) . ";\n");
    }
}

echo('#define PRED_SIZE_BITS ' . PRED_SIZE_BITS . "\n");
echo('#define ' . MAX_NESTING_LEVEL . " 20\n");
?>
int           lookahead;
bool          inEmptyHtmlTag;
ANTLR3_UINT32 emptyHtmlTagType;
