package de.brightbyte.wikiword.store.builder;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
import java.net.URL;
import java.net.URLConnection;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;

import de.brightbyte.data.ChunkyBitSet;
import de.brightbyte.data.IntList;
import de.brightbyte.data.IntRelation;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.io.LogOutput;
import de.brightbyte.text.DurationFormat;
import de.brightbyte.util.LogLevels;
import de.brightbyte.util.PersistenceException;

public class CycleFinder {
	protected IntRelation graph;

	private boolean breakCycles;
	
	private int cycles = 0;
	private int visits = 0;
	
	protected LeveledOutput out;
	private int maxDepth = 1024;
	private int levelWarningThreshold = 32;
	private int degreeThreshold = 1024;

	private ChunkyBitSet visited = new ChunkyBitSet();
	private int[] roots;

	private int usedNodeCount = -1;
	private int usedRootCount = -1;
	
	public CycleFinder(IntRelation graph, boolean breakCycles) {
		super();
		this.graph = graph;
		this.breakCycles = breakCycles;
	}

	public int getUsedNodeCount() {
		return usedNodeCount;
	}

	public int getUsedRootCount() {
		return usedRootCount;
	}

	public LeveledOutput getOut() {
		return out;
	}

	public void setOut(LeveledOutput out) {
		this.out = out;
	}

	public boolean isBreakCycles() {
		return breakCycles;
	}

	public void setBreakCycles(boolean breakCycles) {
		this.breakCycles = breakCycles;
	}

	public int getRootCount() {
		return getRoots().length;
	}

	public int[] getRoots() {
		if (roots==null) {
			if (graph.size()==0) return null;
			roots = graph.getSources().toIntArray();
			//FIXME: check that all nodes are reachable from these roots!
			
			if (roots==null || roots.length==0) { //FIXME: instead featch all part-graphs that are not reachable from the roots
				roots = new int[] { graph.keys().iterator().next() }; //FIXME: instead, pick one from each part-graph!
			}
		}
		return roots;
	}

	public int getNodeCount() {
		return graph.size();
	}

	public int getCycleCount() {
		return cycles;
	}

	public int getVisitCount() {
		return visits;
	}
	
	public void pruneAll() {
		graph.pruneSinks();
		roots = null;
	}

	public void findCycles() throws PersistenceException  {
		pruneAll();
		
		usedNodeCount = graph.size();
		
		if (usedNodeCount==0) {
			usedRootCount = 0;
			return;
		}
		
		usedRootCount = getRoots().length;
		
		log("finding cycles in "+usedNodeCount+" nodes using "+usedRootCount+" roots");
		
		walk(-1, new IntList(maxDepth));
		
		//prunePending(); //XXXXX
	}
	
	protected void walk(int root, IntList path) throws PersistenceException {
		if (path==null && root<0) path = new IntList();
		int level = path.size();
		
		if (root>=0 && !visited.add(root)) {
			int idx = path.indexOf(root); 
			if (root>=0 && idx>=0) {
				onCycle(path, root, idx);
				return;
			}

			return;
		}
		
		if (level>maxDepth) { 
			error("exceeded maximum hierarchy depth", "path: "+path);
			return;
		}
		else if (level>0 && (level % levelWarningThreshold)==0) {
			warning("great hierarchy depth", "path("+level+"): "+path);
		}

		visits++;
		
		if ((visits % 1000000) == 0) {
			if (out!=null) debug("-- VISITS: "+visits/1000000+"M");
		}

		int[] nodes;
		
		if (root<0) {
			nodes = getRoots();
		}
		else {
			nodes = graph.get(root);
		}
		
		if (nodes==null || nodes.length==0) return;

		if (nodes.length>degreeThreshold ) {
			warning("very large node", "#"+root+" has "+nodes.length+" children");				
		}
		
		if (root>=0) path.add(root);

		for (int n: nodes) {
			if (n==root) {
				//NOTE: shouldn't happen, should have been resolved by deleteLoops
				onCycle(path, root, level);
				continue;
			}
			
			walk(n, path);
		}
		
		if (root>=0) path.remove(path.size()-1);
	}
	
	public int getDegreeWarningThreshold() {
		return degreeThreshold;
	}

	public void setDegreeWarningThreshold(int degreeThreshold) {
		this.degreeThreshold = degreeThreshold;
	}

	public int getMaxDepth() {
		return maxDepth;
	}

	public void setMaxDepth(int maxDepth) {
		this.maxDepth = maxDepth;
	}
	
	public int getLevelWarningThreshold() {
		return levelWarningThreshold;
	}

	public void setLevelWarningThreshold(int levelWarningThreshold) {
		this.levelWarningThreshold = levelWarningThreshold;
	}	

	public int getVisits() {
		return visits;
	}

	protected void onCycle(List<Integer> path, int id, int backlinkIndex) throws PersistenceException {
		List<Integer> cycle = path.subList(backlinkIndex, path.size());
		
		if (out!=null) log("cycle found at "+id+" via "+cycle);
		
		cycles++;
		
		int parent = path.get(path.size()-1);
		if (breakCycles) {
			graph.remove(parent, id);
		}
	}

	protected void warning(String problem, String details) {
		if (out!=null) out.warn(problem+": "+details);
	}

	protected void error(String problem, String details) {
		if (out!=null) out.error(problem+": "+details, null);
	}

	protected void log(String msg) {
		if (out!=null) out.info(msg);
	}

	protected void debug(String msg) {
		if (out!=null) out.debug(msg);
	}

	public static CycleFinder load(URL u) throws IOException {
		URLConnection conn = u.openConnection();
		InputStream in = conn.getInputStream();
		CycleFinder cf = load(in, conn.getContentEncoding());
		in.close();
		return cf;
	}
	
	public static CycleFinder load(File f, String enc) throws IOException {
		InputStream in = new BufferedInputStream(new FileInputStream(f));
		CycleFinder cf = load(in, enc);
		in.close();
		return cf;
	}
	
	public static CycleFinder load(InputStream in, String enc) throws IOException {
		Reader rd = enc == null ? new InputStreamReader(in) : new InputStreamReader(in, enc);
		CycleFinder cf = load(rd);
		rd.close();
		return cf;
	}
	
	public static CycleFinder load(Reader rd) throws IOException {
		IntList broad  = new IntList();
		IntList narrow = new IntList();
		
		BufferedReader in = rd instanceof BufferedReader ? (BufferedReader)rd : new BufferedReader(rd);
		String s;
		
		boolean head = true;
		int i = 0;
		while ((s=in.readLine()) != null) {
			i++;
			s = s.trim();
			if (s.length()==0) continue;
			
			String[] nn = s.split("[,;:/|#&+$\\s]");
			if (nn.length!=2) {
				if (head); //ignore
				else throw new IOException("bad number of tokens on line "+i+" found "+nn.length+": "+s);
			}
			
			try {
				int a = Integer.parseInt(nn[0]);
				int b = Integer.parseInt(nn[1]);
				
				broad.add(a);
				narrow.add(b);
				
				head = false;
			}
			catch (NumberFormatException ex) {
				if (head); //ignore
				else throw new IOException("malformed token on line "+i+": "+s);
			}
		}
		
		System.out.println("loaded. sorting...");
		IntRelation rel = new IntRelation(broad, narrow);
		
		System.out.println("done.");
		
		return new CycleFinder(rel, true);
	}
	
	public Map<Integer, Integer> getDegreeDistribution() {
		TreeMap<Integer, Integer> distrib = new TreeMap<Integer, Integer>();
		
		for (Integer k: graph.keys()) {
			int c = graph.get(k).length;
			if (c>0) {
				Integer v = distrib.get(c);
				int n = 1;
				if (v!=null) n += v;
				distrib.put(c, n);
			}
		}
		
		return distrib;
	}
	

	private void dumpDistributionStats() {
		Map<Integer, Integer> distrib = getDegreeDistribution();
		
		int total = 0;
		int number = 0;
		for (Map.Entry<Integer, Integer> e: distrib.entrySet()) {
			int d = e.getKey();
			int n = e.getValue();
			total += d*n;
			number += n;
		}

		int counter = 0;
		int median = 0;
		for (Map.Entry<Integer, Integer> e: distrib.entrySet()) {
			counter += e.getValue();
			if (counter >= number/2) {
				median = e.getKey();
				break;
			}
		}
		
		int mean = total / number;
		
		log("distribution: "+distrib);
		log("mean degree: "+mean);
		log("median degree: "+median);
	}

	public static void main(String[] args) throws IOException, PersistenceException {
		LogOutput out = new LogOutput(ConsoleIO.output);
		out.setLogLevel(LogLevels.LOG_FINE);

		out.println("loading...");
		CycleFinder cf = load(new File(args[0]), null);
		
		cf.setOut(out);
		long t, d;
		
		//cf.dumpDistributionStats();
		/*t = System.currentTimeMillis();
		out.println("pruning leafs from "+cf.getNodeCount()+" ("+cf.getRootCount()+" roots)...");
		cf.prune();
		d = System.currentTimeMillis() - t;
		out.println("remaining: "+cf.getNodeCount()+" inner nodes, took "+DurationFormat.instance.format(d)+".");
		
		cf.dumpDistributionStats();
		*/
		int nodes = cf.getNodeCount();
		int roots = cf.getRootCount();
		
		out.println("finding cycles in "+nodes+" nodes with "+roots+" roots...");
		t = System.currentTimeMillis();
		cf.findCycles();
		d = System.currentTimeMillis() - t;
		
		out.println("found "+cf.getCycleCount()+" cycles using "+cf.getVisitCount()+" visits in "+DurationFormat.instance.format(d)+"");
		out.println("originally "+nodes+" nodes with "+roots+" roots.");
		out.println("used "+cf.getUsedNodeCount()+" nodes with "+cf.getUsedRootCount()+" roots.");
		out.println("throughput: "+(cf.getVisitCount()*1000.0/d)+" visits/sec");
		out.println("stats: "+cf.visits/1000000+"M; nodes: "+cf.visited.size());	
	}

}
