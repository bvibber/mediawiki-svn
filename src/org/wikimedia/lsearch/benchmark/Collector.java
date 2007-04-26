package org.wikimedia.lsearch.benchmark;

import java.sql.ResultSet;
import java.text.MessageFormat;
import java.util.ArrayList;

/** Collects and reports results from benchmark threads */
public class Collector {
	class ReportSet {
		public int results;
		public long time;
		public ReportSet(int results, long time) {
			this.results = results;
			this.time = time;
		}
	}
	
	protected ArrayList<ReportSet> reports = new ArrayList<ReportSet>();
	protected long startTime;
	protected int reportInc; // after how many reports to print out results
	protected int curInc; // current increment
	protected int total;
	
	Collector(int reportInc, int total){
		startTime = System.currentTimeMillis();
		this.reportInc = reportInc;
		curInc = 0;
		this.total = total;
	}
	
	synchronized public void add(int results, long time){
		ReportSet rs = new ReportSet(results,time);
		reports.add(rs);
		curInc++;
		if(curInc >= reportInc){
			report();
			curInc = 0;
		}
	}

	synchronized private void report() {
		long results=0, time=0;
		for(ReportSet rs : reports){
			results += rs.results;
			time += rs.time;
		}
		long now = System.currentTimeMillis();
		int sec = (int) ((now-startTime)/1000);
		int min = 0;
		if(sec>=60){
			min = sec/60;
			sec = sec%60;
		}
		double pers = (double)(now-startTime)/reports.size();
		//double avgtime = (double)time/reports.size();
		System.out.format("[%d:%02d %d/%d] %2.1fms : %d results / search\n", min, sec, reports.size(), total, pers, results/reports.size());
		System.out.flush();
	}
}
