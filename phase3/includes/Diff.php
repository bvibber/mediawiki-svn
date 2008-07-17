<?php

/**
 * Class providing primitives for LCS or Diff.
 *
 * @author Guy Van den Broeck
 */
class AbstractDiffAlgorithm {

	/**
	 * Slide down the snake untill reaching an inequality.
	 */
	protected function forward_snake( $k, $y, $maxx, $maxy, $a, $b, $offsetx=0, $offsety=0){
		$x = $y-$k;
		while($x < $maxx && $y < $maxy && $this->equals($a[$offsetx+$x],$b[$offsety+$y])){
			++$x;
			++$y;
		}
		return $y;
	}

	/**
	 * Slide up the snake until reaching an inequality.
	 */
	protected function backward_snake( $k, $y, $maxx, $maxy, $a, $b, $offsetx=0, $offsety=0){
		$x = $y-$k;
		while($x > $maxx && $y > $maxy && $this->equals($a[$offsetx+$x],$b[$offsety+$y])){
			--$x;
			--$y;
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
 * Class implementing the LCS algorithm by backward propagation.
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 *
 * @author Guy Van den Broeck
 */
class BackwardLcs extends AbstractDiffAlgorithm {

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
		if(sizeof($from)>sizeof($to)){
			$temp = $from;
			$from = $to;
			$to = $temp;
		}
		$m = sizeof($from);
		$n = sizeof($to);

		if($n==0){
			return $m;
		}else if($m==0){
			return $n;
		}

		$delta = $n-$m;
		$p = -1;
		$fp = array_fill( 0, $delta+1, $n);

		do{
			++$p;

			$fp[-$p-1] = $n;
			$fp[$delta+$p+1] = $n;

			for($k=$delta+$p;$k>0;$k--){
				$fp[$k] = $this->backward_snake( $k, min( $fp[$k+1]-1, $fp[$k-1]), -1, -1, $from, $to);
			}
			for($k=-$p;$k<0;$k++){
				$fp[$k] = $this->backward_snake( $k, min( $fp[$k+1]-1, $fp[$k-1]), -1, -1, $from, $to);
			}
			$fp[0] = $this->backward_snake( 0, min( $fp[1]-1, $fp[-1]), -1, -1, $from, $to);
		}while($fp[0]!=-1);

		//echo "<br>$delta, $p";
		return $delta+2*$p;
	}

}

/**
 * Class implementing the LCS algorithm by forward propagation.
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 *
 * @author Guy Van den Broeck
 */
class ForwardLcs extends AbstractDiffAlgorithm {

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
		if(sizeof($from)>sizeof($to)){
			$temp = $from;
			$from = $to;
			$to = $temp;
		}
		$m = sizeof($from);
		$n = sizeof($to);

		if($n==0){
			return $m;
		}else if($m==0){
			return $n;
		}

		$delta = $n-$m;
		$p = -1;
		$fp = array_fill( 0, $delta+1, -1);
		do{
			++$p;

			$fp[-$p-1] = -1;
			$fp[$delta+$p+1] = -1;

			for($k=-$p;$k<$delta;$k++){
				$fp[$k] = $this->forward_snake( $k, max( $fp[$k-1]+1, $fp[$k+1]), $m, $n, $from, $to);
			}
			for($k=$delta+$p;$k>$delta;$k--){
				$fp[$k] = $this->forward_snake( $k, max( $fp[$k-1]+1, $fp[$k+1]), $m, $n, $from, $to);
			}
			$fp[$delta] = $this->forward_snake( $delta, max( $fp[$delta-1]+1, $fp[$delta+1]), $m, $n, $from, $to);
		}while($fp[$delta]!=$n);

		return $delta+2*$p;
	}

}

/**
 * Class implementing the LCS algorithm by bidirectional propagation.
 *
 * !!!!!!!!!!!!!!!!!!!!!!
 * BEWARE HERE BE DRAGONS
 * This algorithm is not guaranteed to find the exact size of the LCS.
 * There are border cases where it will be 1 (or more) off.
 * This implmentation is useful because it is very fast.
 * !!!!!!!!!!!!!!!!!!!!!!
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 *
 * @author Guy Van den Broeck
 */
class BidirectionalLcs extends AbstractDiffAlgorithm {

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
		if(sizeof($from)>sizeof($to)){
			$temp = $from;
			$from = $to;
			$to = $temp;
		}
		$m = sizeof($from);
		$n = sizeof($to);

		if($n==0){
			return $m;
		}else if($m==0){
			return $n;
		}

		$delta = $n-$m;
		$p = -1;
		$fpf = array_fill( 0, $delta+1, -1);
		$fpb = array_fill( 0, $delta+1, $n);

		$p = -1;

		while(1){
			++$p;

			//forward
			$fpf[-$p-1] = -1;
			$fpf[$delta+$p+1] = -1;

			for($k=-$p;$k<$delta;$k++){
				$fpf[$k] = $this->forward_snake( $k, max( $fpf[$k-1]+1, $fpf[$k+1]), $m, $n, $from, $to);
				if($fpf[$k]>$fpb[$k]+1){
					return $delta+4*$p-4;
				}
			}
			for($k=$delta+$p;$k>$delta;$k--){
				$fpf[$k] = $this->forward_snake( $k, max( $fpf[$k-1]+1, $fpf[$k+1]), $m, $n, $from, $to);
				if($fpf[$k]>$fpb[$k]+1){
					return $delta+4*$p-4;
				}
			}
			$fpf[$delta] = $this->forward_snake( $delta, max( $fpf[$delta-1]+1, $fpf[$delta+1]), $m, $n, $from, $to);

			if($fpf[$delta]>$fpb[$delta]+1){
				return $delta+4*$p-4;
			}

			//backward
			$fpb[-$p-1] = $n;
			$fpb[$delta+$p+1] = $n;

			for($k=$delta+$p;$k>0;$k--){
				$fpb[$k] = $this->backward_snake( $k, min( $fpb[$k+1]-1, $fpb[$k-1]), -1, -1, $from, $to);
				if($fpf[$k]>$fpb[$k]+1){
					return $delta+4*$p;
				}
			}
			for($k=-$p;$k<0;$k++){
				$fpb[$k] = $this->backward_snake( $k, min( $fpb[$k+1]-1, $fpb[$k-1]), -1, -1, $from, $to);
				if($fpf[$k]>$fpb[$k]+1){
					return $delta+4*$p;
				}
			}
			$fpb[0] = $this->backward_snake( 0, min( $fpb[1]-1, $fpb[-1]), -1, -1, $from, $to);

			if($fpf[0]>$fpb[0]+1){
				return $delta+4*$p;
			}

		}
	}

}

/**
 * Class implementing the Diff algorithm.
 *
 * !!!!!!!!!!!!!!!!!!!!!!
 * BEWARE HERE BE DRAGONS
 * This algorithm is not guaranteed to find the exact size of the LCS.
 * There are border cases  for n>5 where it will be 1 (or more) off recursively.
 * This can add up to a difference from the optimal LCS of 5%-10%.
 * The common subsequence will be correct though.
 * This implementation is useful because it is very fast for huge input sizes.
 * !!!!!!!!!!!!!!!!!!!!!!
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 *
 * @author Guy Van den Broeck
 */
class FastDiffAlgorithm extends AbstractDiffAlgorithm {

	private $a_inLcs;

	private $b_inLcs;

	/*
	 * Diffs two arrays and returns array($from_inLcs, $to_inLcs)
	 * where {from,to}_inLcs is FALSE if the token was {removed,added}
	 * and TRUE if it is in the longest common subsequence.
	 */
	public function diff (array $from, array $to) {
		$this->a_inLcs = sizeof($from)>0 ? array_fill(0,sizeof($from),FALSE): array();
		$this->b_inLcs = sizeof($to)>0 ? array_fill(0,sizeof($to),FALSE): array();

		//There is no need to remove common tokens at the beginning and end, the first snake will catch them.
		//Hashing the tokens is not generally applicable.
		//Removing tokens that are not in both sequences is O(N*M)>=O(N*P). There is little to gain when most edits are small.

		$this->diff_recursive(0,sizeof($from),0,sizeof($to), $from, $to, FALSE);

		return array($this->a_inLcs,$this->b_inLcs);
	}

	/**
	 * Calculate the diff with a divide and conquer method.
	 *
	 * Ends are exclusive
	 */
	protected function diff_recursive($x_start, $x_end, $y_start, $y_end, $a, $b, $swapped){
		//echo "<br>$x_start-$x_end , $y_start-$y_end";
		if($x_end>$x_start && $y_end>$y_start){
			//echo "-pass";

			//echo "<br>";

			if($x_end-$x_start>$y_end-$y_start){
				$temp = $x_end;
				$x_end = $y_end;
				$y_end = $temp;
				$temp = $x_start;
				$x_start = $y_start;
				$y_start = $temp;
				$temp = $a;
				$a = $b;
				$b = $temp;
				$swapped = ! $swapped;
				//echo "<br>swapped=$swapped";
			}

			$m = $x_end-$x_start;
			$n = $y_end-$y_start;

			$delta = $n-$m;

			$fpf_start = array_fill( 0, $delta+1, -1);
			$fpb_start = array_fill( 0, $delta+1, $n);
			$fpf_end = array_fill( 0, $delta+1, -1);
			$fpb_end = array_fill( 0, $delta+1, $n);

			$no_middle_found = TRUE;
			$endgame = FALSE;
			$middle_found = array_fill( 0, $delta+1, FALSE);
			$best_middle_score = -$n-3;
			$best_middle_start;
			$best_middle_end;
			$best_middle_k;

			$p = -1;

			while(1){
				++$p;

				$middle_found[-$p-1] = FALSE;
				$middle_found[$delta+$p+1] = FALSE;

				// forward
				$fpf_start[-$p-1] = -1;
				$fpf_start[$delta+$p+1] = -1;
				$fpf_end[-$p-1] = -1;
				$fpf_end[$delta+$p+1] = -1;
					
				for($k=-$p;$k<$delta;$k++){
					$fpf_start[$k] = max( $fpf_end[$k-1]+1, $fpf_end[$k+1]);
					//echo "<br>p=$p, fpf_start[$k]=$fpf_start[$k]";
					if($fpf_start[$k]>$fpb_end[$k] && $fpb_end[$k]-$fpb_start[$k]!=0 && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpb_start[$k]-$fpb_end[$k]>$best_middle_score){
							//echo " and best! ($fpb_start[$k]-$fpb_end[$k]>$best_middle_score)";
							$best_middle_start = $fpb_end[$k]+1;
							$best_middle_end = $fpb_start[$k]+1;
							$best_middle_k = $k;
							$best_middle_score = $best_middle_end-$best_middle_start;
						}
					}
					$fpf_end[$k] = $this->forward_snake( $k, $fpf_start[$k], $m, $n, $a, $b, $x_start, $y_start);
					//echo ", fpf_end[$k]=$fpf_end[$k]";
					if($fpf_end[$k]>$fpb_end[$k] && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpb_end[$k]-$fpf_end[$k]>$best_middle_score){
							//echo " and best ($fpb_end[$k]-$fpf_end[$k]>$best_middle_score)!";
							$best_middle_start = $fpf_start[$k];
							$best_middle_end = $fpf_end[$k];
							$best_middle_k = $k;
							$best_middle_score = $fpb_end[$k]-$fpf_end[$k];
						}
					}

				}

				for($k=$delta+$p;$k>$delta;$k--){
					$fpf_start[$k] = max( $fpf_end[$k-1]+1, $fpf_end[$k+1]);
					//echo "<br>p=$p, fpf_start[$k]=$fpf_start[$k]";
					if($fpf_start[$k]>$fpb_end[$k] && $fpb_end[$k]-$fpb_start[$k]!=0 && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpb_start[$k]-$fpb_end[$k]>$best_middle_score){
							//echo " and best! ($fpb_start[$k]-$fpb_end[$k]>$best_middle_score)";
							$best_middle_start = $fpb_end[$k]+1;
							$best_middle_end = $fpb_start[$k]+1;
							$best_middle_k = $k;
							$best_middle_score = $best_middle_end-$best_middle_start;
						}
					}
					$fpf_end[$k] = $this->forward_snake( $k, $fpf_start[$k], $m, $n, $a, $b, $x_start, $y_start);
					//echo ", fpf_end[$k]=$fpf_end[$k]";
					if($fpf_end[$k]>$fpb_end[$k] && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpb_end[$k]-$fpf_end[$k]>$best_middle_score){
							//echo " and best! ($fpb_end[$k]-$fpf_end[$k]>$best_middle_score)";
							$best_middle_start = $fpf_start[$k];
							$best_middle_end = $fpf_end[$k];
							$best_middle_k = $k;
							$best_middle_score = $fpb_end[$k]-$fpf_end[$k];
						}
					}
				}

				// delta
				$fpf_start[$delta] = max( $fpf_end[$delta-1]+1, $fpf_end[$delta+1]);
				//echo "<br>p=$p, fpf_start[$delta]=$fpf_start[$delta]";
				if($fpf_start[$delta]>$fpb_end[$delta] && $fpb_end[$delta]-$fpb_start[$delta]!=0 && !$middle_found[$delta]){
					if($fpb_start[$delta]+1==$n){
						return $this->foundMiddleSnake($x_start, $x_end, $y_start, $y_end, $fpb_end[$delta]+1,$fpb_start[$delta]+1, $delta, $a, $b, $swapped);
					}
					$no_middle_found = FALSE;
					$middle_found[$delta] = TRUE;
					//echo "<br>middle found";
					if($fpb_start[$delta]-$fpb_end[$delta]>$best_middle_score){
						//echo " and best! ($fpb_start[$delta]-$fpb_end[$delta]>$best_middle_score)";
						$best_middle_start = $fpb_end[$delta]+1;
						$best_middle_end = $fpb_start[$delta]+1;
						$best_middle_k = $delta;
						$best_middle_score = $best_middle_end-$best_middle_start;
					}
				}
				$fpf_end[$delta] = $this->forward_snake( $delta, $fpf_start[$delta], $m, $n, $a, $b, $x_start, $y_start);
				//echo ", fpf_end[$delta]=$fpf_end[$delta]";
				if($fpf_end[$delta]==$n){
					if($fpf_start[$delta]==$fpf_end[$delta]){
						if($fpf_start[$delta]==$fpf_end[$delta-1]+1){
							//last edge was horizontal
							return $this->reachedEndHorizontal($x_start, $x_end, $y_start, $y_end, $a, $b, $swapped);
						}else{
							//last edge was vertical
							return $this->reachedEndVertical($x_start, $x_end, $y_start, $y_end, $a, $b, $swapped);
						}
					}else{
						return $this->foundMiddleSnake($x_start, $x_end, $y_start, $y_end, $fpf_start[$delta],$fpf_end[$delta], $delta, $a, $b, $swapped);
					}
				}
				if($fpf_end[$delta]>$fpb_end[$delta] && !$middle_found[$delta]){
					$no_middle_found = FALSE;
					$middle_found[$delta] = TRUE;
					//echo "<br>middle found";
					if($fpb_end[$delta]-$fpf_end[$delta]>$best_middle_score){
						//echo " and best! ($fpb_end[$delta]-$fpf_end[$delta]>$best_middle_score)";
						$best_middle_start = $fpf_start[$delta];
						$best_middle_end = $fpf_end[$delta];
						$best_middle_k = $delta;
						$best_middle_score = $fpb_end[$delta]-$fpf_end[$delta];
					}
				}

				// backward
				$fpb_start[-$p-1] = $n;
				$fpb_start[$delta+$p+1] = $n;
				$fpb_end[-$p-1] = $n;
				$fpb_end[$delta+$p+1] = $n;

				for($k=-$p;$k<0;$k++){
					$fpb_start[$k] = min( $fpb_end[$k+1]-1, $fpb_end[$k-1]);
					//echo "<br>p=$p, fpb_start[$k]=$fpb_start[$k]";
					if($fpf_end[$k]>$fpb_start[$k] && $fpf_end[$k]-$fpf_start[$k]!=0 && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpf_end[$k]-$fpf_start[$k]>$best_middle_score){
							//echo " and best! ($fpf_end[$k]-$fpf_start[$k]>$best_middle_score)";
							$best_middle_start = $fpf_start[$k];
							$best_middle_end = $fpf_end[$k];
							$best_middle_k = $k;
							$best_middle_score = $best_middle_end-$best_middle_start;
						}
					}
					$fpb_end[$k] = $this->backward_snake( $k, $fpb_start[$k], -1, -1, $a, $b, $x_start, $y_start);
					//echo ", fpb_end[$k]=$fpb_end[$k]";
					if($fpf_end[$k]>$fpb_end[$k] && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpb_end[$k]-$fpf_end[$k]>$best_middle_score){
							//echo " and best! ($fpb_end[$k]-$fpf_end[$k]>$best_middle_score)";
							$best_middle_start = $fpb_end[$k]+1;
							$best_middle_end = $fpb_start[$k]+1;
							$best_middle_k = $k;
							$best_middle_score = $fpb_end[$k]-$fpf_end[$k];
						}
							
					}
				}
				for($k=$delta+$p;$k>0;$k--){
					$fpb_start[$k] = min( $fpb_end[$k+1]-1, $fpb_end[$k-1]);
					//echo "<br>p=$p, fpb_start[$k]=$fpb_start[$k]";
					if($fpf_end[$k]>$fpb_start[$k] && $fpf_end[$k]-$fpf_start[$k]!=0 && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpf_end[$k]-$fpf_start[$k]>$best_middle_score){
							//echo " and best! ($fpf_end[$k]-$fpf_start[$k]>$best_middle_score)";
							$best_middle_start = $fpf_start[$k];
							$best_middle_end = $fpf_end[$k];
							$best_middle_k = $k;
							$best_middle_score = $best_middle_end-$best_middle_start;
						}
					}
					$fpb_end[$k] = $this->backward_snake( $k, $fpb_start[$k], -1, -1, $a, $b, $x_start, $y_start);
					//echo ", fpb_end[$k]=$fpb_end[$k]";
					if($fpf_end[$k]>$fpb_end[$k] && !$middle_found[$k]){
						$no_middle_found = FALSE;
						$middle_found[$k] = TRUE;
						//echo "<br>middle found";
						if($fpb_end[$k]-$fpf_end[$k]>$best_middle_score){
							//echo " and best! ($fpb_end[$k]-$fpf_end[$k]>$best_middle_score)";
							$best_middle_start = $fpb_end[$k]+1;
							$best_middle_end = $fpb_start[$k]+1;
							$best_middle_k = $k;
							$best_middle_score = $fpb_end[$k]-$fpf_end[$k];
						}
					}
				}


				// 0
				$fpb_start[0] = min( $fpb_end[1]-1, $fpb_end[-1]);
				//echo "<br>p=$p, fpb_start[0]=$fpb_start[0]";
				if($fpf_end[0]>$fpb_start[0] && $fpf_end[0]-$fpf_start[0]!=0 && !$middle_found[0]){
					$no_middle_found = FALSE;
					$middle_found[0] = TRUE;
					//echo "<br>middle found";
					if($fpf_end[0]-$fpf_start[0]>$best_middle_score){
						//echo " and best! ($fpf_end[0]-$fpf_start[0]>$best_middle_score)";
						$best_middle_start = $fpf_start[0];
						$best_middle_end = $fpf_end[0];
						$best_middle_k = 0;
						$best_middle_score = $best_middle_end-$best_middle_start;
					}
				}
				$fpb_end[0] = $this->backward_snake( 0, $fpb_start[0], -1, -1, $a, $b, $x_start, $y_start);
				//echo ", fpb_end[0]=$fpb_end[0]";
				if($fpf_end[0]>$fpb_end[0] && !$middle_found[0]){
					$no_middle_found = FALSE;
					$middle_found[$k] = TRUE;
					//echo "<br>middle found";
					if($fpb_end[0]-$fpf_end[0]>$best_middle_score){
						//echo " and best! ($fpb_end[0]-$fpf_end[0]>$best_middle_score)";
						$best_middle_start = $fpb_end[0]+1;
						$best_middle_end = $fpb_start[0]+1;
						$best_middle_k = 0;
						$best_middle_score = $fpb_end[0]-$fpf_end[0];
					}
				}

				if(!$no_middle_found){
					if($endgame || $p == $m){
						//echo "<br>endgame: $best_middle_start->$best_middle_end, k=$best_middle_k";
						return $this->foundMiddleSnake($x_start, $x_end, $y_start, $y_end, $best_middle_start, $best_middle_end, $best_middle_k, $a, $b, $swapped);
					}
					$endgame = TRUE;
				}
			}
		}
	}

	/**
	 * Mark tokens as in the LCS if it is a snake.
	 * Diff the part before and after the middle.
	 */
	protected function foundMiddleSnake($x_start, $x_end, $y_start, $y_end, $fp_start, $fp_end, $k, $a, $b, $swapped){
		//echo "<br><b>foundmiddle= $x_start, $x_end, $y_start, $y_end, $fp_start->$fp_end, $k</b>";

		$x_snakestart = $x_start+$fp_start-$k;
		$x_snakeend = $x_start+$fp_end-$k;
		$y_snakestart = $y_start+$fp_start;
		$y_snakeend = $y_start+$fp_end;

		if($swapped){
			for($x=$x_snakestart;$x<$x_snakeend;$x++){
				$this->b_inLcs[$x] = TRUE;
			}
			for($y=$y_snakestart;$y<$y_snakeend;$y++){
				$this->a_inLcs[$y] = TRUE;
			}
		}else{
			for($x=$x_snakestart;$x<$x_snakeend;$x++){
				$this->a_inLcs[$x] = TRUE;
			}
			for($y=$y_snakestart;$y<$y_snakeend;$y++){
				$this->b_inLcs[$y] = TRUE;
			}
		}

		//echo "<br>left:";
		$this->diff_recursive($x_start, $x_snakestart, $y_start, $y_snakestart, $a, $b, $swapped);
		//echo "<br>right:";
		$this->diff_recursive($x_snakeend, $x_end, $y_snakeend, $y_end, $a, $b, $swapped);
	}

	protected function reachedEndHorizontal($x_start, $x_end, $y_start, $y_end, $a, $b, $swapped){
		//echo "<br><b>reached end horizontally! $x_start, $x_end, $y_start, $y_end</b>";
		$y_end = $y_end-1;
		$this->diff_recursive($x_start, $x_end, $y_start, $y_end, $a, $b, $swapped);
	}

	protected function reachedEndVertical($x_start, $x_end, $y_start, $y_end, $a, $b, $swapped){
		//echo "<br><b>reached end vertically! $x_start, $x_end, $y_start, $y_end</b>";
		$x_end = $x_end-1;
		$this->diff_recursive($x_start, $x_end, $y_start, $y_end, $a, $b, $swapped);
	}
}

?>