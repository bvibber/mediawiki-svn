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

	protected $swapped;

	/**
	 * The sequences need to be ordered by length.
	 */
	protected function order(array $from, array $to){
		if(sizeof($from)>=sizeof($to)){
			$this->a = $to;
			$this->m = sizeof($to);
			$this->b = $from;
			$this->n = sizeof($from);
			$this->swapped = TRUE;
		}else{
			$this->a = $from;
			$this->m = sizeof($from);
			$this->b = $to;
			$this->n = sizeof($to);
			$this->swapped = FALSE;
		}
	}

	/**
	 * Slide down the snake untill reaching an inequality.
	 */
	protected function forward_snake( $k, $y, $maxx, $maxy, $offsetx=0, $offsety=0, $swapped=FALSE){
		if($swapped){
			return $this->forward_snake( $k, $y-$k, $maxy, $maxx, $offsety, $offsetx, $swapped );
		}else{
			$x = $y-$k;
			while($x < $maxx && $y < $maxy && $this->equals($this->a[$offsetx+$x],$this->b[$offsety+$y])){
				++$x;
				++$y;
			}

			return $y;
		}
	}

	/**
	 * Slide up the snake until reaching an inequality.
	 */
	protected function backward_snake( $k, $y, $maxx, $maxy, $offsetx=0, $offsety=0, $swapped=FALSE){
		if($swapped){
			return $this->backward_snake( $k, $y-$k, $maxy, $maxx, $offsety, $offsetx, $swapped );
		}else{
			$x = $y-$k;
			while($x > $maxx && $y > $maxy && $this->equals($this->a[$offsetx+$x],$this->b[$offsety+$y])){
				--$x;
				--$y;
			}
			return $y;
		}
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
		$this->order($from, $to);

		if($this->n==0){
			return $this->m;
		}else if($this->m==0){
			return $this->n;
		}

		$delta = $this->n-$this->m;
		$fp = array_fill_keys( range( -($this->m+1), $this->n+1), $this->n);
		$p = -1;

		do{
			++$p;
			for($k=$delta+$p;$k>0;$k--){
				$fp[$k] = $this->backward_snake( $k, min( $fp[$k+1]-1, $fp[$k-1]), -1, -1);
			}
			for($k=-$p;$k<0;$k++){
				$fp[$k] = $this->backward_snake( $k, min( $fp[$k+1]-1, $fp[$k-1]), -1, -1);
			}
			$fp[0] = $this->backward_snake( 0, min( $fp[1]-1, $fp[-1]), -1, -1);
		}while($fp[0]!=-1);

		echo "<br>$delta, $p";
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
		$this->order($from, $to);

		if($this->n==0){
			return $this->m;
		}else if($this->m==0){
			return $this->n;
		}

		$delta = $this->n-$this->m;
		$fp = array_fill_keys( range( -($this->m+1), $this->n+1), -1);
		$p = -1;

		do{
			++$p;
			for($k=-$p;$k<$delta;$k++){
				$fp[$k] = $this->forward_snake( $k, max( $fp[$k-1]+1, $fp[$k+1]), $this->m, $this->n);
			}
			for($k=$delta+$p;$k>$delta;$k--){
				$fp[$k] = $this->forward_snake( $k, max( $fp[$k-1]+1, $fp[$k+1]), $this->m, $this->n);
			}
			$fp[$delta] = $this->forward_snake( $delta, max( $fp[$delta-1]+1, $fp[$delta+1]), $this->m, $this->n);
		}while($fp[$delta]!=$this->n);

		echo "<br>$delta, $p";
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
		$this->order($from, $to);

		if($this->n==0){
			return $this->m;
		}else if($this->m==0){
			return $this->n;
		}

		$delta = $this->n-$this->m;

		$fpkeys = range( -($this->m+1), $this->n+1);
		$fpf = array_fill_keys( $fpkeys, -1);
		$fpb = array_fill_keys( $fpkeys, $this->n);
		$p = -1;

		while(1){
			++$p;

			//forward
			for($k=-$p;$k<$delta;$k++){
				$fpf[$k] = $this->forward_snake( $k, max( $fpf[$k-1]+1, $fpf[$k+1]), $this->m, $this->n);
				if($fpf[$k]>$fpb[$k]+1){
					echo "<br>0, $delta, $p";
					return $delta+4*$p-4;
				}
			}
			for($k=$delta+$p;$k>$delta;$k--){
				$fpf[$k] = $this->forward_snake( $k, max( $fpf[$k-1]+1, $fpf[$k+1]), $this->m, $this->n);
				if($fpf[$k]>$fpb[$k]+1){
					echo "<br>0, $delta, $p";
					return $delta+4*$p-4;
				}
			}
			$fpf[$delta] = $this->forward_snake( $delta, max( $fpf[$delta-1]+1, $fpf[$delta+1]), $this->m, $this->n);

			if($fpf[$delta]>$fpb[$delta]+1){
				echo "<br>0, $delta, $p";
				return $delta+4*$p-4;
			}

			//backward
			for($k=$delta+$p;$k>0;$k--){
				$fpb[$k] = $this->backward_snake( $k, min( $fpb[$k+1]-1, $fpb[$k-1]), -1, -1);
				if($fpf[$k]>$fpb[$k]+1){
					echo "<br>1, $delta, $p";
					return $delta+4*$p;
				}
			}
			for($k=-$p;$k<0;$k++){
				$fpb[$k] = $this->backward_snake( $k, min( $fpb[$k+1]-1, $fpb[$k-1]), -1, -1);
				if($fpf[$k]>$fpb[$k]+1){
					echo "<br>1, $delta, $p";
					return $delta+4*$p;
				}
			}
			$fpb[0] = $this->backward_snake( 0, min( $fpb[1]-1, $fpb[-1]), -1, -1);

			if($fpf[0]>$fpb[0]+1){
				echo "<br>1, $delta, $p";
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
 * There are border cases where it will be 1 (or more) off.
 * This implmentation is useful because it is very fast.
 * !!!!!!!!!!!!!!!!!!!!!!
 *
 * This is the O(NP) variant as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 *
 * @author Guy Van den Broeck
 */
class FastDiffAlgorithm extends AbstractDiffAlgorithm {

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
		$this->a_inLcs = array_fill(0,$this->m,FALSE);
		$this->b_inLcs = array_fill(0,$this->n,FALSE);

		//There is no need to remove common tokens at the beginning and end, the first snake will catch them.
		//Hashing the tokens is not generally applicable.
		//Removing tokens that are not in both sequences is O(N*M)>=O(N*P). There is little to gain when most edits are small.

		$this->diff_recursive(0,$this->m,0,$this->n);

		if($this->swapped){
			return array($this->b_inLcs,$this->a_inLcs);
		}else{
			return array($this->a_inLcs,$this->b_inLcs);
		}
	}

	/**
	 * Calculate the diff with a divide and conquer method.
	 *
	 * Ends are exclusive
	 */
	protected function diff_recursive($x_start, $x_end, $y_start, $y_end){
		echo "<br>$x_start-$x_end , $y_start-$y_end";
		if($x_end>$x_start && $y_end>$y_start){

			if($x_end-$x_start>$y_end-$y_start){
				$temp = $x_end;
				$x_end = $y_end;
				$y_end = $temp;
				$temp = $x_start;
				$x_start = $y_start;
				$y_start = $temp;
				$swapped = TRUE;
			}else{
				$swapped = FALSE;
			}

			$m = $x_end-$x_start;
			$n = $y_end-$y_start;

			$delta = $n-$m;

			$fpkeys = range( -($m+1), $n+1);
			$fpf = array_fill_keys( $fpkeys, -1);
			$fpb = array_fill_keys( $fpkeys, $n);
			$p = -1;

			while(1){
				++$p;

				echo "$p\n";
				echo "forward:\n";
				print_r($fpf);
				echo "backward:\n";
				print_r($fpb);
				
				//forward
				for($k=-$p;$k<$delta;$k++){
					$fpf[$k] = $this->forward_snake( $k, max( $fpf[$k-1]+1, $fpf[$k+1]), $m, $n, $x_start, $y_start, $swapped);
					if($fpf[$k]>$fpb[$k]+1){
						$this->foundSnake($x_start, $x_end, $y_start, $y_end, $x_start+$fpb[$k]-$k+1, $x_start+$fpf[$k]-$k, $y_start+$fpb[$k]+1, $y_start+$fpf[$k], $swapped);
						return;
					}
				}
				echo "forward:\n";
				print_r($fpf);
				echo "backward:\n";
				print_r($fpb);
				for($k=$delta+$p;$k>$delta;$k--){
					$fpf[$k] = $this->forward_snake( $k, max( $fpf[$k-1]+1, $fpf[$k+1]), $m, $n, $x_start, $y_start, $swapped);
					if($fpf[$k]>$fpb[$k]+1){
						$this->foundSnake($x_start, $x_end, $y_start, $y_end, $x_start+$fpb[$k]-$k+1, $x_start+$fpf[$k]-$k, $y_start+$fpb[$k]+1, $y_start+$fpf[$k], $swapped);
						return;
					}
				}
				echo "forward:\n";
				print_r($fpf);
				echo "backward:\n";
				print_r($fpb);
				$fpf[$delta] = $this->forward_snake( $delta, max( $fpf[$delta-1]+1, $fpf[$delta+1]), $m, $n, $x_start, $y_start, $swapped);
				if($fpf[$delta]>$fpb[$delta]+1){
					$this->foundSnake($x_start, $x_end, $y_start, $y_end, $x_start+$fpb[$delta]-$delta+1, $x_start+$fpf[$delta]-$k, $y_start+$fpb[$delta]+1, $y_start+$fpf[$delta], $swapped);
					return;
				}
				echo "forward:\n";
				print_r($fpf);
				echo "backward:\n";
				print_r($fpb);

				//backward
				for($k=$delta+$p;$k>0;$k--){
					$fpb[$k] = $this->backward_snake( $k, min( $fpb[$k+1]-1, $fpb[$k-1]), -1, -1, $x_start, $y_start, $swapped);
					if($fpf[$k]>$fpb[$k]+1){
						$this->foundSnake($x_start, $x_end, $y_start, $y_end, $x_start+$fpb[$k]-$k+1, $x_start+$fpf[$k]-$k, $y_start+$fpb[$k]+1, $y_start+$fpf[$k], $swapped);
						return;
					}
				}
				echo "forward:\n";
				print_r($fpf);
				echo "backward:\n";
				print_r($fpb);
				for($k=-$p;$k<0;$k++){
					$fpb[$k] = $this->backward_snake( $k, min( $fpb[$k+1]-1, $fpb[$k-1]), -1, -1, $x_start, $y_start, $swapped);
					if($fpf[$k]>$fpb[$k]+1){
						$this->foundSnake($x_start, $x_end, $y_start, $y_end, $x_start+$fpb[$k]-$k+1, $x_start+$fpf[$k]-$k, $y_start+$fpb[$k]+1, $y_start+$fpf[$k], $swapped);
						return;
					}
				}
				echo "forward:\n";
				print_r($fpf);
				echo "backward:\n";
				print_r($fpb);
				$fpb[0] = $this->backward_snake( 0, min( $fpb[1]-1, $fpb[-1]), -1, -1, $x_start, $y_start, $swapped);
				if($fpf[0]>$fpb[0]+1){
					$this->foundSnake($x_start, $x_end, $y_start, $y_end, $x_start+$fpb[0]+1, $x_start+$fpf[0], $y_start+$fpb[0]+1, $y_start+$fpf[0], $swapped);
					return;
				}
				echo "forward:\n";
				print_r($fpf);
				echo "backward:\n";
				print_r($fpb);

			}
		}
	}

	/**
	 * Mark tokens $start until $end (exclusive) as in the LCS
	 * Diff the part before and after the snake.
	 */
	protected function foundSnake($x_start, $x_end, $y_start, $y_end, $x_snakestart, $x_snakeend, $y_snakestart, $y_snakeend, $swapped){
		echo "<br>$x_start, $x_end, $y_start, $y_end, $x_snakestart, $x_snakeend, $y_snakestart, $y_snakeend, $swapped";
		if($swapped){
			$temp = $x_snakestart;
			$x_snakestart = $y_snakestart;
			$y_snakestart = $temp;
			$temp = $x_snakeend;
			$x_snakeend = $y_snakeend;
			$y_snakeend = $temp;

			$temp = $x_start;
			$x_start = $y_start;
			$y_start = $temp;
			$temp = $x_end;
			$x_end = $y_end;
			$y_end = $temp;
		}
		for($x=$x_snakestart;$x<$x_snakeend;$x++){
			$this->a_inLcs[$x] = TRUE;
		}
		for($y=$y_snakestart;$y<$y_snakeend;$y++){
			$this->b_inLcs[$y] = TRUE;
		}
		echo "<br>left:";
		$this->diff_recursive($x_start, $x_snakestart, $y_start, $y_snakestart);
		echo "<br>right:";
		$this->diff_recursive($x_snakeend, $x_end, $y_snakeend, $y_end);
	}
}
?>