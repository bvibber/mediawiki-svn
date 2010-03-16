package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.util.ArrayList;
import java.util.Arrays;

import org.wikimedia.lsearch.util.MathFunc;

public class MathFuncTest {
	public static void print(int[] p, double[] val){
		for(int i=0;i<p.length-1;i++){
			System.out.print(MathFunc.avg(val,p[i],p[i+1])+" -> ");
			for(int j=p[i];j<p[i+1];j++){
				System.out.print(val[j]+" ");
			}			
			System.out.println();
		}
		System.out.println();
	}
	
	public static void main(String[] args) throws Exception{
		double[] val = {39.2,13.45,12.67,10.25,8.84,8.66,8.31,8.19,8.06,7.99,6.39,6.19,6,5.92,5.85};
		String testfile = "./test-data/mathfunc.test";
		int[] p = MathFunc.partitionList(val,3);
		print(p,val);		
		
		System.out.println("From "+testfile);
		BufferedReader r = new BufferedReader(new FileReader(new File(testfile)));
		String line;
		ArrayList<Double> val2a = new ArrayList<Double>();
		while((line = r.readLine()) != null){
			val2a.add(new Double(line));
		}
		double[] val2 = new double[val2a.size()];
		for(int i=0;i<val2.length;i++)
			val2[i] = val2a.get(i);
		print(MathFunc.partitionList(val2,5),val2);
		
		double[] val3 = {1.5192982456140351, 1.222988282514404, 1.053690036900369, 1.053690036900369, 1.003690036900369, 0.5229882825144041};
		print(MathFunc.partitionList(val3,5),val3);
		
		
	}
}
