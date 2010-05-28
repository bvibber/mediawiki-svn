<?php

class ActiveStrategyPF {
	static function activityTag( $str, $args, $parser ) {
		return ActiveStrategy::getOutput();
	}
	
	static function setup( $parser ) {
		$parser->setHook( 'activity', array( 'ActiveStrategyPF', 'activityTag' ) );
		
		return true;
	}
}
