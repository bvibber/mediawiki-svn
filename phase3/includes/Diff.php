<?php
/**
 * Class implementing the Diff/LCS algorithm.
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 *
 * @author Guy Van den Broeck
 */

class Diff {

	private $bigSequence;
	private $n;
	private $bigSequence_inLcs;

	private $smallSequence;
	private $m;
	private $smallSequence_inLcs;

	private $lcsLength;

	/*
	 * Diffs two arrays and returns array($from_inLcs, $to_inLcs)
	 * where {from,to}_inLcs is 0 if the token was {removed,added}
	 * and 1 if it is in the longest common subsequence.
	 */
	public function diff (array $from, array $to) {

		if(sizeof($from)>=sizeof($to)){
			$bigSequence = $from;
			$n = sizeof($from);
			
			$smallSequence = $to;
			$m = sizeof($to);
		}else{
			$bigSequence = $to;
			$n = sizeof($to);
			
			$smallSequence = $from;
			$m = sizeof($from);
		}

		//There is no need to remove common tokens at the beginning and end, the first snake will catch them.
		//Hashing the tokens is not generally applicable.
		//Removing tokens that are not in both sequences is O(N*M)>=O(NP). There is little to gain when most edits are small.
	
	}

	/*
	 * Computers the length of the longest common subsequence of 
	 * the arrays $from and $to.
	 */
	public function lcs (array $from, array $to) {
		$n = sizeof($from);
		$m = sizeof($to);
		//TODO
	}

	public function equals($left, $right){
		return $left == $right;
	}
}
?>