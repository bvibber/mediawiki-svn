package org.wikimedia.lsearch.util;

import java.util.Arrays;


public class MathFunc {
	
	/** Calculate average value starting from start to end (end excluded) */
	public static double avg(double[] val, int start, int end){
		double s = 0;
		for(int i=start;i<end;i++)
			s+=val[i];
		return s/(end-start);
	}
	
	/** 
	 * Approximate the graph of function with num horizontal lines
	 * (const functions), so to minimize the maximal deviation  
	 * @return list of discontinuities (begin points of horizontal lines)
	 */
	public static int[] partitionList(double[] val, int num){
		//System.out.println("Doing: "+Arrays.toString(val));
		int[] part = new int[num+1]; // point of discontinuity
		//double[] av = new double[num]; // approximate (average over included points)
		if(val.length <= num){
			// no need to partition
			for(int i=0;i<num;i++)
				part[i]=Math.min(i,val.length);
			part[num] = val.length;
			return part;
		}
		// make first num-1 segments have length 1
		for(int i=0;i<num;i++)
			part[i]=i;
		part[num] = val.length; // last point is end of values
		//for(int i=0;i<num;i++)
//			av[i] = avg(val,part[i],part[i+1]);
		// error
		double err = calcErr(part,val,num);
		double err2 = calcErr2(part,val,num);
		// values at next iteration 
		int[] newpart = new int[num+1];
		//double[] newav = new double[num];
		double newerr = 0, newerr2 = 0;
		
		main_loop : while(true){
			if(part[num-1] == part[num] - 1)
				break; // more transformations will get it out of bounds error
			for(int i=0;i<num-1;i++){
				merge(i,part,newpart,val,num);
				newerr = calcErr(newpart,val,num);
				newerr2 = calcErr2(newpart,val,num);
				if(newerr < err || (newerr == err && newerr2 < err2)){
					copy(newpart,part);
					err = newerr;
					err2 = newerr2;
					//MathFuncTest.print(newpart,val);
					continue main_loop;
				}
			}
			// try extending last
			extend(part,newpart,val,num);
			newerr = calcErr(newpart,val,num);
			newerr2 = calcErr2(newpart,val,num);
			if(newerr < err || (newerr == err && newerr2 < err2)){
				copy(newpart,part);
				err = newerr;
				err2 = newerr2;
				//MathFuncTest.print(newpart,val);
				continue main_loop;
			}
			break;
			
		}
		
		
		return part;
	}
	
	private static void extend(int[] part, int[] newpart, double[] val, int num) {
		for(int j=0;j<num;j++)
			newpart[j] = part[j];
		newpart[num-1] = part[num-1]+1;
		newpart[num] = part[num];
		/*for(int j=0;j<num;j++)
			newav[j] = newav[j];
		newav[num-1] = avg(val,newpart[num-1],newpart[num]); */		
	}

	private static void copy(int[] newpart, int[] part) {
		for(int i=0;i<newpart.length;i++)
			part[i] = newpart[i];
		/*for(int i=0;i<newav.length;i++)
			av[i] = newav[i]; */
		
	}

	/** merge i to i+1, create one new part at the end */
	private static void merge(int i, int[] part, int[] newpart, double[] val, int num) {		
		for(int j=0;j<=i;j++)
			newpart[j] = part[j];
		for(int j=i+1;j<num-1;j++)
			newpart[j] = part[j+1];
		newpart[num-1] = part[num-1]+1;
		newpart[num] = part[num];
		// update avg
		/*for(int j=0;j<i;j++)
			newavg[j] = avg[j];
		for(int j=i;j<num;j++)
			newavg[j] = avg(val,newpart[j],newpart[j+1]); */
	}

	private static double calcErr(int[] part, double[] val, int num) {
		double err = 0;
		for(int i=0;i<num;i++){
			// max - min value 
			double v2 = val[part[i]];
			double v1 = val[part[i+1]-1];
			double e = v2 - v1;
			if( e > err )
				err = e;
		}
		return err;
	}	
	
	private static double calcErr2(int[] part, double[] val, int num) {
		double err = 0;
		for(int i=0;i<num;i++){
			// max - min value 
			double v2 = val[part[i]];
			double v1 = val[part[i+1]-1];
			double e = v2 - v1;
			err += e*(part[i+1]-1-part[i]);
		}
		return err;
	}	
}
