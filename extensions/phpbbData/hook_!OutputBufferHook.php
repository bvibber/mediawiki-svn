<?php

class OutputBufferHook {
	function hookTemplateDisplay(&$hooks, $handle, $include_once = true) {
		ob_start();
	}

	function hookExitHandler(&$hooks) {
		$outputCache = ob_get_contents();
		ob_end_clean();
		
		global $phpbb_hook;
		if ( !empty($phpbb_hook) && $phpbb_hook->call_hook('BeforePageDisplay', $outputCache) ) {
			if ($phpbb_hook->hook_return('BeforePageDisplay')) {
				$hookReturn = $phpbb_hook->hook_return_result('BeforePageDisplay');
				if ($hookReturn) {
					$outputCache =  $hookReturn;
				}
			}
		}

		eval(' ?>' . $outputCache . '<?php ');
	}
}

$phpbb_hook->register(array('template', 'display'), array('OutputBufferHook', 'hookTemplateDisplay'));
$phpbb_hook->register('exit_handler', array('OutputBufferHook', 'hookExitHandler'));
$phpbb_hook->add_hook('BeforePageDisplay');
