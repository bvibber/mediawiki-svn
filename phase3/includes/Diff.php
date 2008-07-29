<?php
/* Copyright (C) 2008 Guy Van den Broeck <guy@guyvdb.eu>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 * Implementation of the Diff algorithm.
 *
 * The algorithm is based on the O(NP) LCS algorithm as descibed by Wu et al. in "An O(NP) Sequence Comparison Algorithm"
 * and extended with my own ideas.
 *
 * @return array($from_removed, $to_added)
 * 		   | TRUE if the token was removed or added.
 *
 * @author Guy Van den Broeck
 */
function wikidiff3_diff(array $from, array $to, $boundRunningTime=FALSE, $max_NP_before_bound = 800000){
	wfProfileIn( __METHOD__ );

	$m = sizeof($from);
	$n = sizeof($to);

	$result_from =  array_fill(0,$m,TRUE);
	$result_to =  array_fill(0,$n,TRUE);

	//reduce the complexity for the next step (intentionally done twice)
	//remove common tokens at the start
	$i=0;
	while($i<$m && $i<$n && $from[$i]===$to[$i]){
		$result_from[$i] = FALSE;
		$result_to[$i] = FALSE;
		unset($from[$i],$to[$i]);
		++$i;
	}

	//remove common tokens at the end
	$j=1;
	while($i+$j<=$m && $i+$j<=$n && $from[$m-$j]===$to[$n-$j]){
		$result_from[$m-$j] = FALSE;
		$result_to[$n-$j] = FALSE;
		unset($from[$m-$j],$to[$n-$j]);
		++$j;
	}

	$newFrom = array();
	$newFromIndex = array();
	$newTo = array();
	$newToIndex = array();

	//remove tokens not in both sequences
	$shared = array_fill_keys($from,FALSE);
	foreach($to as $index => $el){
		if(array_key_exists($el,$shared)){
			//keep it
			$shared[$el] = TRUE;
			$newTo[] = $el;
			$newToIndex[] = $index;
		}
	}
	foreach($from as $index => $el){
		if($shared[$el]){
			//keep it
			$newFrom[] = $el;
			$newFromIndex[] = $index;
		}
	}

	unset($from, $to);

	$m = sizeof($newFrom);
	$n = sizeof($newTo);
	$offsetx = 0;
	$offsety = 0;
	$from_inLcs = new InLcs($m);
	$to_inLcs = new InLcs($n);

	wikidiff3_diffPart($newFrom, $newTo, $from_inLcs, $to_inLcs, $m, $n, $offsetx, $offsety, $m, $boundRunningTime, $max_NP_before_bound);

	foreach($from_inLcs->inLcs as $key => $in){
		if($in){
			$result_from[$newFromIndex[$key]] = FALSE;
		}
	}
	foreach($to_inLcs->inLcs as $key => $in){
		if($in){
			$result_to[$newToIndex[$key]] = FALSE;
		}
	}
	wfProfileOut( __METHOD__ );
	return array($result_from, $result_to);
}

function wikidiff3_diffPart(array $a, array $b, InLcs $a_inLcs, InLcs $b_inLcs, $m, $n, $offsetx, $offsety, $bestKnownLcs, $boundRunningTime=FALSE, $max_NP_before_bound = 800000){
	if($bestKnownLcs==0 || $m==0 || $n==0){
		return;
	}
	if($m>$n){
		return wikidiff3_diffPart($b, $a, $b_inLcs, $a_inLcs, $n, $m, $offsety, $offsetx, $bestKnownLcs, $boundRunningTime, $max_NP_before_bound);
	}

	wfProfileIn( __METHOD__ );

	$a_inLcs_sym = &$a_inLcs->inLcs;
	$b_inLcs_sym = &$b_inLcs->inLcs;

	//remove common tokens at the start
	while($m>0 && $n>0 && $a[0]===$b[0]){
		$a_inLcs_sym[$offsetx] = TRUE;
		$b_inLcs_sym[$offsety] = TRUE;
		++$offsetx;	++$offsety;
		$m--; $n--;
		$bestKnownLcs--;
		array_shift($a);
		array_shift($b);
	}

	//remove common tokens at the end
	while($m>0 && $n>0 && $a[$m-1]===$b[$n-1]){
		$a_inLcs_sym[$offsetx+$m-1] = TRUE;
		$b_inLcs_sym[$offsety+$n-1] = TRUE;
		--$m; --$n;
		$bestKnownLcs--;
		array_pop($a);
		array_pop($b);
	}

	$delta = $n-$m;
	$delta_plus_1 = $delta+1;
	$delta_min_1 = $delta-1;

	$fpForw = array_fill( 0, $delta_plus_1, -1);
	$lcsSizeForw = array_fill( 0, $delta_plus_1, 0);
	$snakeBeginForw = array_fill( 0, $delta_plus_1, -1);
	$snakeEndForw = array_fill( 0, $delta_plus_1, -1);
	$snakekForw = array_fill( 0, $delta_plus_1, 0);

	$fpBack = array_fill( 0, $delta_plus_1, $n);
	$lcsSizeBack = array_fill( 0, $delta_plus_1, 0);
	$snakeBeginBack = array_fill( 0, $delta_plus_1, $n);
	$snakeEndBack = array_fill( 0, $delta_plus_1, $n);
	$snakekBack = array_fill( 0, $delta_plus_1, 0);

	$overlap = $delta>1 ? array_fill( 1, $delta_min_1, FALSE) : array();

	$bestKnownLcs = $bestKnownLcs;

	$bestLcsLength = -1;
	$bestLcsLengthTop = -1;
	$bestLcsLengthBottom = -1;

	if($boundRunningTime){
		$maxp_before_bound = max($max_NP_before_bound/$n,10);
		if($maxp_before_bound>=$m){
			$boundRunningTime = false;
			unset($maxp_before_bound);
		}
	}

	$p=-1;
	$m_min_1 = $m-1;
	$maxp=$m_min_1-$bestLcsLength;

	while($p<$maxp){

		if($boundRunningTime && $p>$maxp_before_bound){
			// bound the running time by stopping early
			if($bestLcsLength>0){
				break;
			}else{
				$bestLcsProgressForw=0;
				$bestkForw = 0;
				foreach($lcsSizeForw as $k => $localLcsProgress){
					if($localLcsProgress>$bestLcsProgressForw){
						$bestLcsProgressForw = $localLcsProgress;
						$bestkForw = $k;
					}
				}
				$bestLcsProgressBack=0;
				$bestkBack = 0;
				foreach($lcsSizeBack as $k => $localLcsProgress){
					if($localLcsProgress-max(0,-$k,$k-$delta)>$bestLcsProgressBack){
						$bestLcsProgressBack = $localLcsProgress-max(0,-$k,$k-$delta);
						$bestkBack = $k;
					}
				}
				if($lcsSizeForw[$bestkForw]>0 || $lcsSizeForw[$bestkBack]>0){
					if($fpForw[$bestkForw]>$fpBack[$bestkBack] || $fpForw[$bestkForw]-$bestkForw>$fpBack[$bestkBack]-$bestkBack){
						// This is hard, maybe try some more? Can this even happen?
					}else{
						$topSnakeStart = $snakeBeginForw[$bestkForw];
						$topSnakeEnd = $snakeEndForw[$bestkForw];
						$topSnakek = $snakekForw[$bestkForw];
						$bottomSnakeStart = $snakeEndBack[$bestkBack]+1;
						$bottomSnakeEnd = $snakeBeginBack[$bestkBack]+1;
						$bottomSnakek = $snakekBack[$bestkBack];
						$bestLcsLengthTop = $lcsSizeForw[$bestkBack] + $topSnakeStart - $topSnakeEnd;
						$bestLcsLengthBottom =  $lcsSizeBack[$bestkBack] + $bottomSnakeStart - $bottomSnakeEnd;

						// also diff the middle now
						if($bottomSnakeEnd>($fpForw[$bestkForw]>>1)){
							$m_t = ($bottomSnakeStart-$bottomSnakek)-($topSnakeEnd-$topSnakek);
							$n_t = $bottomSnakeStart-$topSnakeEnd;
							$a_t = array_slice($a, $topSnakeEnd-$topSnakek, $m_t);
							$b_t = array_slice($b, $topSnakeEnd, $n_t);
							$offsetx_t = $offsetx+($topSnakeEnd-$topSnakek);
							$offsety_t = $offsety+$topSnakeEnd;
						}else{
							$m_t = ($fpBack[$bestkBack]+1-$bestkBack)-($fpForw[$bestkForw]-$bestkForw);
							$n_t = $fpBack[$bestkBack]+1-$fpForw[$bestkForw];
							$a_t = array_slice($a, $fpForw[$bestkForw]-$bestkForw, $m_t);
							$b_t = array_slice($b, $fpForw[$bestkForw], $n_t);
							$offsetx_t = $offsetx+($fpForw[$bestkForw]-$bestkForw);
							$offsety_t = $offsety+$fpForw[$bestkForw];
						}
						wikidiff3_diffPart($a_t, $b_t, $a_inLcs, $b_inLcs, $m_t, $n_t, $offsetx_t, $offsety_t, $m_t, $boundRunningTime, $max_NP_before_bound);
						break;
					}
				}
				//OOPS, problem
				//Maybe try a little more?
			}
		}
		++$p;
		$overlap[-$p] = FALSE;
		$overlap[$delta+$p] = FALSE;

		$min_p_min_1 = -$p-1;
		$delta_plus_1_plus_p = $delta_plus_1+$p;

		// forward
		$fpForw[$min_p_min_1] = -1;
		$lcsSizeForw[$min_p_min_1] = 0;
		$snakeBeginForw[$min_p_min_1]=-1;
		$snakeEndForw[$min_p_min_1]=-1;
		$snakekForw[$min_p_min_1]=-1;

		$fpForw[$delta_plus_1_plus_p] = -1;
		$lcsSizeForw[$delta_plus_1_plus_p] = 0;
		$snakeBeginForw[$delta_plus_1_plus_p]=-1;
		$snakeEndForw[$delta_plus_1_plus_p]=-1;
		$snakekForw[$delta_plus_1_plus_p]=-1;

		$k=-$p;
		do {
			$k_plus_1 = $k+1;
			$k_min_1 = $k-1;

			$fpBelow = $fpForw[$k_min_1]+1;
			$fpAbove = $fpForw[$k_plus_1];
			$y = &$fpForw[$k];
			if($fpBelow>$fpAbove){
				$y = $fpBelow;
				$lcsSizeForw[$k] = $lcsSizeForw[$k_min_1];
				$snakeBeginForw[$k] = $snakeBeginForw[$k_min_1];
				$snakeEndForw[$k] = $snakeEndForw[$k_min_1];
				$snakekForw[$k] = $snakekForw[$k_min_1];
			}else{
				$y = $fpAbove;
				$lcsSizeForw[$k] = $lcsSizeForw[$k_plus_1];
				$snakeBeginForw[$k] = $snakeBeginForw[$k_plus_1];
				$snakeEndForw[$k] = $snakeEndForw[$k_plus_1];
				$snakekForw[$k] = $snakekForw[$k_plus_1];
			}
			$oldy = $y;
			$x = $y-$k;
			while($x < $m && $y < $n && $a[$x]===$b[$y]){
				++$x;
				++$y;
			}
			if($y-$oldy>0){
				$lcsSizeForw[$k] += $y-$oldy;
				$snakeBeginForw[$k] = $oldy;
				$snakeEndForw[$k]= $y;
				$snakekForw[$k] = $k;
			}
			if($y>=$fpBack[$k] && !$overlap[$k]){
				// there is overlap
				$overlap[$k]=TRUE;
				$lcsLength = $lcsSizeForw[$k]+$lcsSizeBack[$k];
				if($y>$fpBack[$k]+1){
					$snakeoverlap = $y-$fpBack[$k]-1;
					$lcsLength -= $snakeoverlap;
				}
				if($lcsLength>$bestLcsLength){
					// a better sequence found!
					$bestLcsLength = $lcsLength;

					$topSnakeStart = $snakeBeginForw[$k];
					$topSnakeEnd = $snakeEndForw[$k];
					$topSnakek = $snakekForw[$k];

					// aligned snakes bite (\   )
					//                     ( \ \)
					$bottomSnakeStart = max($snakeEndBack[$k]+1, $topSnakeEnd+max(0,$snakekBack[$k]-$snakekForw[$k]));
					$bottomSnakeEnd = max($snakeBeginBack[$k]+1, $bottomSnakeStart);
					$bottomSnakek = $snakekBack[$k];

					if($bottomSnakeEnd<$y){
						$bottomSnakeStart = $y;
						$bottomSnakeEnd = $y;
						$bottomSnakek = $k;
					}

					$bestLcsLengthTop = $lcsSizeForw[$k]-($snakeEndForw[$k]-$snakeBeginForw[$k]);
					$bestLcsLengthBottom = $lcsSizeBack[$k]-($snakeBeginBack[$k]-$snakeEndBack[$k]);
					if($bestKnownLcs==$lcsLength){
						$fpForw[$k]=$y;
						break 2;
					}
					$maxp=$m_min_1-$bestLcsLength;
				}
			}
			if($k<$delta_min_1){
				++$k;
			}else if($k>$delta){
				--$k;
			}else if($k==$delta_min_1){
				$k = $delta+$p;
			}else{
				break;
			}
		} while(TRUE);

		// backward
		$fpBack[$min_p_min_1] = $n;
		$lcsSizeBack[$min_p_min_1] = 0;
		$snakeBeginBack[$min_p_min_1]=$n;
		$snakeEndBack[$min_p_min_1]=$n;
		$snakekBack[$min_p_min_1]=$n;

		$fpBack[$delta_plus_1_plus_p] = $n;
		$lcsSizeBack[$delta_plus_1_plus_p] = 0;
		$snakeBeginBack[$delta_plus_1_plus_p]=$n;
		$snakeEndBack[$delta_plus_1_plus_p]=$n;
		$snakekBack[$delta_plus_1_plus_p]=$n;

		$k=$delta+$p;
		do {
			$k_plus_1 = $k+1;
			$k_min_1 = $k-1;

			$fpBelow = $fpBack[$k_min_1];
			$fpAbove = $fpBack[$k_plus_1]-1;
			$y = &$fpBack[$k];
			if($fpBelow<$fpAbove){
				$y = $fpBelow;
				$lcsSizeBack[$k] = $lcsSizeBack[$k_min_1];
				$snakeBeginBack[$k] = $snakeBeginBack[$k_min_1];
				$snakeEndBack[$k] = $snakeEndBack[$k_min_1];
				$snakekBack[$k] = $snakekBack[$k_min_1];
			}else{
				$y = $fpAbove;
				$lcsSizeBack[$k] = $lcsSizeBack[$k_plus_1];
				$snakeBeginBack[$k] = $snakeBeginBack[$k_plus_1];
				$snakeEndBack[$k] = $snakeEndBack[$k_plus_1];
				$snakekBack[$k] = $snakekBack[$k_plus_1];
			}
			$oldy = $y;
			$x = $y-$k;
			while($x > -1 && $y > -1 && $a[$x]===$b[$y]){
				--$x;
				--$y;
			}
			if($oldy-$y>0){
				$lcsSizeBack[$k] += $oldy-$y;
				$snakeBeginBack[$k] = $oldy;
				$snakeEndBack[$k] = $y;
				$snakekBack[$k] = $k;
			}
			if($fpForw[$k]>=$y && !$overlap[$k]){
				// there is overlap
				$overlap[$k]=TRUE;
				$lcsLength = $lcsSizeForw[$k]+$lcsSizeBack[$k];
				if($fpForw[$k]>$y+1){
					$snakeoverlap = $fpForw[$k]-$y-1;
					$lcsLength -= $snakeoverlap;
				}
				if($lcsLength>$bestLcsLength){
					// a better sequence found!
					$bestLcsLength = $lcsLength;

					$topSnakeStart = $snakeBeginForw[$k];
					$topSnakeEnd = $snakeEndForw[$k];
					$topSnakek = $snakekForw[$k];

					// aligned snakes bite (\   )
					//                     ( \ \)
					$bottomSnakeStart = max($snakeEndBack[$k]+1, $topSnakeEnd+max(0,$snakekBack[$k]-$snakekForw[$k]));
					$bottomSnakeEnd = max($snakeBeginBack[$k]+1, $bottomSnakeStart);
					$bottomSnakek = $snakekBack[$k];

					if($bottomSnakeEnd<$fpForw[$k]){
						$bottomSnakeStart = $fpForw[$k];
						$bottomSnakeEnd = $fpForw[$k];
						$bottomSnakek = $k;
					}

					$bestLcsLengthTop = $lcsSizeForw[$k]-($snakeEndForw[$k]-$snakeBeginForw[$k]);
					$bestLcsLengthBottom = $lcsSizeBack[$k]-($snakeBeginBack[$k]-$snakeEndBack[$k]);
					if($bestKnownLcs==$lcsLength){
						$fpBack[$k] = $y;
						break 2;
					}
					$maxp=$m_min_1-$bestLcsLength;
				}
			}
			if($k>1){
				--$k;
			}else if($k<0){
				++$k;
			}else if($k==1){
				$k = -$p;
			}else{
				break;
			}
		} while(TRUE);
	}

	unset($fpForw, $fpBack, $lcsSizeForw, $lcsSizeBack);
	unset($snakeBeginForw, $snakeBeginBack, $snakeEndForw, $snakeEndBack, $snakekForw, $snakekBack);
	unset($overlap);

	// Mark snakes as in LCS
	$maxi = $offsetx+$topSnakeEnd-$topSnakek;
	for($i=$offsetx+$topSnakeStart-$topSnakek;$i<$maxi;++$i){
		$a_inLcs_sym[$i] = TRUE;
	}
	$maxi = $offsety+$topSnakeEnd;
	for($i=$offsety+$topSnakeStart;$i<$maxi;++$i){
		$b_inLcs_sym[$i] = TRUE;
	}
	$maxi = $offsetx+$bottomSnakeEnd-$bottomSnakek;
	for($i=$offsetx+$bottomSnakeStart-$bottomSnakek;$i<$maxi;++$i){
		$a_inLcs_sym[$i] = TRUE;
	}
	$maxi = $offsety+$bottomSnakeEnd;
	for($i=$offsety+$bottomSnakeStart;$i<$maxi;++$i){
		$b_inLcs_sym[$i] = TRUE;
	}

	$m_t = $topSnakeStart-$topSnakek;
	$a_t = array_slice($a, 0, $m_t);
	$b_t = array_slice($b, 0, $topSnakeStart);

	$m_b = $m-($bottomSnakeEnd-$bottomSnakek);
	$n_b = $n-$bottomSnakeEnd;
	$a_b = array_slice($a, $bottomSnakeEnd-$bottomSnakek, $m_b);
	$b_b = array_slice($b, $bottomSnakeEnd, $n_b);

	wikidiff3_diffPart($a_t, $b_t, $a_inLcs, $b_inLcs, $m_t, $topSnakeStart, $offsetx, $offsety, $bestLcsLengthTop, $boundRunningTime, $max_NP_before_bound);

	wikidiff3_diffPart($a_b, $b_b, $a_inLcs, $b_inLcs, $m_b, $n_b, $offsetx+($bottomSnakeEnd-$bottomSnakek), $offsety+$bottomSnakeEnd, $bestLcsLengthBottom, $boundRunningTime, $max_NP_before_bound);

	wfProfileOut( __METHOD__ );
}

class InLcs {

	public $inLcs;

	function __construct($length){
		$this->inLcs = $length>0 ? array_fill(0,$length,FALSE): array();
	}

	/**
	 * Get the length of the Longest Common Subsequence (computed)
	 */
	public function getLcsLength(){
		return array_sum($this->inLcs);
	}

}

/**
 * DISCLAIMER: The next classes are made obsolete by the previous function.
 * They implement Wu's O(NP) algorithm in a literal way and only compute the LCS.
 */

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
?>