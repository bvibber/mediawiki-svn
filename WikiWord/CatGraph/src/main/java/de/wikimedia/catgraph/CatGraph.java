package de.wikimedia.catgraph;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.neo4j.graphdb.Direction;
import org.neo4j.graphdb.GraphDatabaseService;
import org.neo4j.graphdb.Node;
import org.neo4j.graphdb.Relationship;
import org.neo4j.graphdb.RelationshipType;
import org.neo4j.graphdb.ReturnableEvaluator;
import org.neo4j.graphdb.StopEvaluator;
import org.neo4j.graphdb.Transaction;
import org.neo4j.graphdb.Traverser;
import org.neo4j.index.IndexService;
import org.neo4j.index.lucene.LuceneIndexService;
import org.neo4j.kernel.EmbeddedGraphDatabase;

import de.brightbyte.application.Arguments;
import de.brightbyte.application.ConsoleApp;
import de.brightbyte.audit.DebugUtil;
import de.brightbyte.data.Pair;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.io.ChunkingCursor;
import de.brightbyte.io.LineCursor;
import de.brightbyte.io.Output;
import de.brightbyte.job.ChunkedProgressRateTracker;
import de.brightbyte.text.CsvLineChunker;
import de.brightbyte.util.CollectionUtils;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.SystemUtils;

public class CatGraph extends ConsoleApp {
	protected class Descendants implements Command {

		private int start;

		public Descendants(int start) {
			this.start = start;
		}

		public void execute(ConsoleApp app) throws Exception {
			listDescendants(start, out);
		}
	}

	public enum CategoryRelationships implements RelationshipType
	{
	    CONTAINS
	}

	private GraphDatabaseService graphDb;
	private IndexService indexer;
	
	public CatGraph(GraphDatabaseService graphDb, IndexService indexer) {
		this.graphDb = graphDb;
		this.indexer = indexer;
	}

	public void loadArcs(DatabaseAccess db, String sql, int fromCol, int toCol) throws SQLException {
		ResultSet rs = db.executeQuery("load graph", sql);
		while (rs.next()) {
			int from = rs.getInt(fromCol);
			int to = rs.getInt(toCol);
			
			putArc(from ,to);
		}
	}

	public void loadArcs(DataCursor<? extends List<?>> args, int fromCol, int toCol) throws PersistenceException {
		ChunkedProgressRateTracker progressTracker = new ChunkedProgressRateTracker("arcs");
		
		List<?> row ;
		while ((row = args.next()) != null) {
			int from = DatabaseUtil.asInt( row.get(fromCol) );
			int to = DatabaseUtil.asInt( row.get(toCol) );
			
			putArc(from ,to);
			
			progressTracker.step();
			if ( progressTracker.chunkIf(10000, 10) ) {
				out.println(progressTracker);
			}
		}
	}

	public void loadArcs(DataCursor<Pair<Integer, Integer>> args) throws PersistenceException {
		ChunkedProgressRateTracker progressTracker = new ChunkedProgressRateTracker("arcs");
		
		Pair<Integer, Integer> row ;
		while ((row = args.next()) != null) {
			int from = row.getA();
			int to = row.getB();
			
			putArc(from ,to);
			
			progressTracker.step();
			if ( progressTracker.chunkIf(10000, 10) ) {
				out.println(progressTracker);
			}
		}
	}

	public void loadRoots(DatabaseAccess db, String sql) throws SQLException {
		ResultSet rs = db.executeQuery("load graph", sql);
		while (rs.next()) {
			int root = rs.getInt(1);
			
			putRoot(root);
		}
	}
	
	public Node findRootNode(int pageId) {
		Node n = getNodeByPageId(pageId);
		if ( n == null ) throw new IllegalArgumentException("page_id "+pageId+" not found");
		
		return findRootNode(n);
	}

	public Node findRootNode(Node n) {
		Iterable<Relationship> relationships = n.getRelationships(CategoryRelationships.CONTAINS, Direction.INCOMING);
		Iterator<Relationship> it = relationships.iterator();
		
		if (it.hasNext()) return findRootNode(it.next().getStartNode()); 
		else return n;
	}

	public Node getNodeByPageId(int pageId) {
		return indexer.getSingleNode("page_id", pageId);
	}

	public Node aquireNodeByPageId(int pageId) {
		Node n = getNodeByPageId(pageId);
		
		if (n==null) {
			n = graphDb.createNode(); 
			n.setProperty("page_id", pageId);
			indexer.index(n, "page_id", pageId);
		}
		
		return n;
	}
	
	public Relationship putArc(int from, int cat) {
		if ( from == cat ) return null;
		return putArc( aquireNodeByPageId(from), aquireNodeByPageId(cat) );
	}

	public Relationship putRoot(int root) {
		return putRoot( aquireNodeByPageId(root) );
	}

	public Relationship putArc(Node from, Node cat) {
		if ( from.getId() == cat.getId() ) return null;
		Relationship relationship = cat.createRelationshipTo( from, CategoryRelationships.CONTAINS );
		return relationship;
	}

	public Relationship putRoot(Node root) {
		Node ref = graphDb.getReferenceNode();
		if (ref.getId() == root.getId()) return null;
		
		Relationship relationship = ref.createRelationshipTo( root, CategoryRelationships.CONTAINS );
		return relationship;
	}

	public Collection<Integer> getDescendants(int start) {
		Node n = getNodeByPageId(start);
		if ( n == null ) throw new IllegalArgumentException("page_id "+start+" not found");
		
		return getDescendants(n);
	}
	
	public Collection<Integer> getDescendants(Node startNode) {
		List<Integer> descendants = new ArrayList<Integer>();
		
		Traverser traverser = startNode.traverse( Traverser.Order.BREADTH_FIRST , StopEvaluator.END_OF_GRAPH, ReturnableEvaluator.ALL, CategoryRelationships.CONTAINS, Direction.OUTGOING   );
		for ( Node node : traverser )
		{
			if ( node.hasProperty("page_id") ) 
				descendants.add((Integer)node.getProperty("page_id"));
		}
		
		return descendants;
	}

	public void traverseAndDump(Node startNode) {
		Traverser traverser = startNode.traverse( Traverser.Order.BREADTH_FIRST , StopEvaluator.END_OF_GRAPH, ReturnableEvaluator.ALL, CategoryRelationships.CONTAINS, Direction.OUTGOING   );
		for ( Node node : traverser )
		{
			if ( node.hasProperty("page_id") ) 
					System.out.println( "page #" + node.getProperty("page_id") );
			else
				System.out.println( node.toString() );
		}
	}
	
	/*
	protected static final String catlinksSql = "select cl_from, page_id from categorylinks join page on cl_to = page_title and page_namespace = 14";
	protected static final String rootCatsSql = "select page_id from page left join categorylinks on cl_from = page_id where cl_from is null and page_namespace = 14";
	*/
	
	public void listDescendants(int start, Output  out) {
		out.println("finding descendants of "+start+"....");
		long t = System.currentTimeMillis();
		Collection<Integer> descendants = getDescendants(start);
		out.println("finding descendants of "+start+" took "+(System.currentTimeMillis() - t)+"ms.");

		out.println("-----------------------------");
		DebugUtil.dump("", descendants, out);
		out.println("-----------------------------");
	}

	protected Command newCommand(String cmd, List<Object> args) {
		Command command = super.newCommand(cmd, args);
		if (command!=null) return command;
		
		if (cmd.equals("descendants") || cmd.equals("desc") || cmd.equals("d")) return new Descendants(DatabaseUtil.asInt(args.get(0)));
		else return null;
	}
	
	public static void main(String[] argv) throws IOException, SQLException, PersistenceException {
		Arguments args = new Arguments(); 
		args.parse(argv);
		
		Map<String,String> configuration = null;
		
		if ( args.isSet("config") ) {
			configuration = EmbeddedGraphDatabase.loadConfigurations( args.getOption("config", (String)null) );
		} else {
			URL u = CatGraph.class.getResource("neo4j.properties");
			configuration = CollectionUtils.asMap( SystemUtils.loadProperties(u, null) );
		}
		
		GraphDatabaseService graphDb = new EmbeddedGraphDatabase( args.getParameter(0), configuration );
		File tsv = new File(args.getParameter(1));

		IndexService indexer = new LuceneIndexService(graphDb); 

		/*
		DatabaseAccess db = new DatabaseSchema(null, dbInfo, null);
		db.open();
		
		db.executeUpdate("", "use "+database+";");
		*/
		CatGraph graph = new CatGraph(graphDb, indexer);
		
		InputStreamReader rd = new InputStreamReader(new FileInputStream(tsv));
		ChunkingCursor cursor =  new ChunkingCursor(new LineCursor(rd), CsvLineChunker.tsv);
		
		cursor.next(); //skip header in first line
		
		Transaction tx = graphDb.beginTx();
		try
		{
			System.out.println("loading arcs....");
			long t = System.currentTimeMillis();
			graph.loadArcs(cursor, 0, 1);
			System.out.println("loading arcs took "+(System.currentTimeMillis() - t)+"ms.");
		
			graph.run();
		}
		finally
		{
		   tx.finish();
		   graphDb.shutdown();
		}
		
		System.out.println( "done" ); 
	}
}
