<?php

/**
 * Class providing primitives for LCS or Diff.
 *
 * @author Guy Van den Broeck
 */
class AbstractDiffAlgorithm {

	protected $a;
	protected $m;

	protected $b;
	protected $n;

	/**
	 * The sequences need to be ordered by length.
	 */
	protected function order(array $from, array $to){
		if(sizeof($from)>=sizeof($to)){
			$this->a = $to;
			$this->m = sizeof($to);
			$this->b = $from;
			$this->n = sizeof($from);
		}else{
			$this->a = $from;
			$this->m = sizeof($from);
			$this->b = $to;
			$this->n = sizeof($to);
		}
	}

	/**
	 * Slide down the snake untill reaching an inequality.
	 */
	protected function snake($k,$y){
		$x = $y-$k;
		while($x < $this->m && $y < $this->n && $this->equals($this->a[$x],$this->b[$y])){
			++$x;
			++$y;
		}
		return $y;
	}

	/**
	 * Override this method to compute the LCS with different equality measures.
	 */
	public function equals($left, $right){
		return $left == $right;
	}
}

/**
 * Class implementing the Diff algorithm.
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 */
class LcsAlgorithm extends AbstractDiffAlgorithm {
	
	/*
	 * Computers the length of the longest common subsequence of
	 * the arrays $from and $to.
	 */
	public function lcs (array $from, array $to) {
		return (sizeof($from)+sizeof($to)-$this->ses( $from, $to))/2;
	}
	
	/*
	 * Computers the length of the shortest edit script of
	 * the arrays $from and $to.
	 */
	public function ses (array $from, array $to) {
		$this->order($from, $to);

		$delta = $this->n-$this->m;
		$fp = array_fill_keys( range( -($this->m+1), $this->n+1), -1);
		$p = -1;

		do{
			++$p;
			for($k=-$p;$k<$delta;$k++){
				$fp[$k] = $this->snake( $k, max( $fp[$k-1]+1, $fp[$k+1]));
			}

			for($k=$delta+$p;$k>$delta;$k--){
				$fp[$k] = $this->snake( $k, max( $fp[$k-1]+1, $fp[$k+1]));
			}
				
			$fp[$k] = $this->snake( $delta, max( $fp[$delta-1]+1, $fp[$delta+1]));
		}while($fp[$delta]!=$this->n);

		return $delta+2*$p;
	}
	
}

/**
 * Class implementing the Diff algorithm.
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 */
class DiffAlgorithm extends AbstractDiffAlgorithm {

	private $a_inLcs;

	private $b_inLcs;

	private $lcsLength;

	/*
	 * Diffs two arrays and returns array($from_inLcs, $to_inLcs)
	 * where {from,to}_inLcs is FALSE if the token was {removed,added}
	 * and TRUE if it is in the longest common subsequence.
	 */
	public function diff (array $from, array $to) {
		$this->order($from, $to);

		//There is no need to remove common tokens at the beginning and end, the first snake will catch them.
		//Hashing the tokens is not generally applicable.
		//Removing tokens that are not in both sequences is O(N*M)>=O(N*P). There is little to gain when most edits are small.

		//TODO
	}
}
?>