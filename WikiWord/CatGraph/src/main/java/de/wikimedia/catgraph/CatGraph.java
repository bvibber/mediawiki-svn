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
import de.brightbyte.db.DatabaseDataSet.Cursor;
import de.brightbyte.io.ChunkingCursor;
import de.brightbyte.io.LineCursor;
import de.brightbyte.io.Output;
import de.brightbyte.job.ChunkedProgressRateTracker;
import de.brightbyte.text.CsvLineChunker;
import de.brightbyte.util.CollectionUtils;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.SystemUtils;

public class CatGraph extends ConsoleApp {
	public class ListElementPairCursor implements
			DataCursor<Pair<Integer, Integer>> {

		private DataCursor<? extends List<?>> cursor;
		private int aField;
		private int bField;

		public ListElementPairCursor(DataCursor<? extends List<?>> cursor, int aField, int bField) {
			this.cursor = cursor;
			this.aField = aField;
			this.bField = bField;
		}

		public void close() {
			cursor.close();
		}

		public Pair<Integer, Integer> next() throws PersistenceException {
			List<?> row = cursor.next();
			if ( row == null ) return null;
			
			int a =  DatabaseUtil.asInt( row.get(aField) );
			int b =  DatabaseUtil.asInt( row.get(bField) );
			
			return new Pair<Integer, Integer>(a, b);
		}

	}

	public class ResultSetPairCursor implements
			DataCursor<Pair<Integer, Integer>> {

		private ResultSet cursor;
		private int aField;
		private int bField;

		public ResultSetPairCursor(ResultSet cursor, int aField, int bField) {
			this.cursor = cursor;
			this.aField = aField;
			this.bField = bField;
		}

		public void close() {
			try {
				cursor.close();
			} catch (SQLException e) {
				//ignore silently
			}
		}

		public Pair<Integer, Integer> next() throws PersistenceException {
			try {
				if (!cursor.next()) return null;
				
				int a = DatabaseUtil.asInt( cursor.getObject(aField) );
				int b = DatabaseUtil.asInt( cursor.getObject(bField) );

				return new Pair<Integer, Integer>(  a, b );
			} catch (SQLException e) {
				throw new PersistenceException();
			}
		}
	}

	protected class Descendants implements Command {

		private int start;

		public Descendants(int start) {
			this.start = start;
		}

		public void execute(ConsoleApp app) throws Exception {
		    Transaction tx = graphDb.beginTx(); // real transaction
		    try {
		    		listDescendants(start, out);
		    } finally {
		    		tx.finish();
		    }
		}
	}

	public enum CategoryRelationships implements RelationshipType
	{
	    CONTAINS
	}

	private GraphDatabaseService graphDb;
	private IndexService indexer;
	private long chunkSize = 100000;
	
	public CatGraph(GraphDatabaseService graphDb, IndexService indexer) {
		this.graphDb = graphDb;
		this.indexer = indexer;
	}

	public void loadArcs(DatabaseAccess db, String sql, int fromCol, int toCol) throws PersistenceException {
		try {
			ResultSet rs = db.executeQuery("load graph", sql);
			loadArcs( new ResultSetPairCursor(rs, fromCol, toCol) );
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public void loadArcs(DataCursor<? extends List<?>> args, int fromCol, int toCol) throws PersistenceException {
		loadArcs( new ListElementPairCursor(args, fromCol, toCol) );
	}

	public void loadArcs(DataCursor<Pair<Integer, Integer>> args) throws PersistenceException {
		ChunkedProgressRateTracker progressTracker = new ChunkedProgressRateTracker("arcs");
		
		Transaction tx = null;
		boolean done = false;
		try {
			Pair<Integer, Integer> row ;
			while ((row = args.next()) != null) {
				int from = row.getA();
				int to = row.getB();
				
				if (tx==null) tx = graphDb.beginTx();
				
				putArc(from ,to);
				
				progressTracker.step();
				//out.println("adding "+from+" -> "+to+" (#"+progressTracker.getCurrentChunkSize()+")");
				
				if ( progressTracker.chunkIf(chunkSize , 10) ) {
					if (tx!=null) {
						long t = System.currentTimeMillis();
						out.println("committing...");
						tx.success();
						tx.finish();
						tx = null;
						out.println("commit took "+(System.currentTimeMillis() - t)+"ms.");
					}
					
					out.println(progressTracker);
				}
			}
			
			done = true;
		} finally {
			if ( tx != null) {
				if (done) tx.success();
				else tx.failure();
				
				tx.finish();
			}
		}
		
	}

	/*
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

	public Relationship putRoot(int root) {
		return putRoot( aquireNodeByPageId(root) );
	}

	public Relationship putRoot(Node root) {
		Node ref = graphDb.getReferenceNode();
		if (ref.getId() == root.getId()) return null;
		
		Relationship relationship = ref.createRelationshipTo( root, CategoryRelationships.CONTAINS );
		return relationship;
	}
*/
	
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

	public Relationship putArc(Node from, Node cat) {
		if ( from.getId() == cat.getId() ) return null;
		Relationship relationship = cat.createRelationshipTo( from, CategoryRelationships.CONTAINS );
		return relationship;
	}

	public Collection<Integer> getDescendants(int start) {
		Node n = getNodeByPageId(start);
		if ( n == null ) throw new IllegalArgumentException("page_id "+start+" not found");
		
		return getDescendants(n);
	}
	
	public Collection<Integer> getDescendants(Node startNode) {
		List<Integer> descendants = new ArrayList<Integer>();
		
		Transaction tx = graphDb.beginTx();	
		
		try {
			Traverser traverser = startNode.traverse( Traverser.Order.BREADTH_FIRST , StopEvaluator.END_OF_GRAPH, ReturnableEvaluator.ALL, CategoryRelationships.CONTAINS, Direction.OUTGOING   );
			for ( Node node : traverser )
			{
				if ( node.hasProperty("page_id") ) 
					descendants.add((Integer)node.getProperty("page_id"));
			}
		} finally {
			tx.finish();
		}
		
		return descendants;
	}

	public void traverseAndDump(Node startNode) {
		Transaction tx = graphDb.beginTx();	
		
		try {
			Traverser traverser = startNode.traverse( Traverser.Order.BREADTH_FIRST , StopEvaluator.END_OF_GRAPH, ReturnableEvaluator.ALL, CategoryRelationships.CONTAINS, Direction.OUTGOING   );
			for ( Node node : traverser )
			{
				if ( node.hasProperty("page_id") ) 
						System.out.println( "page #" + node.getProperty("page_id") );
				else
					System.out.println( node.toString() );
			}
		} finally {
			tx.finish();
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
		out.println("finding "+descendants.size()+" descendants of "+start+" took "+(System.currentTimeMillis() - t)+"ms.");

		/*
		out.println("-----------------------------");
		DebugUtil.dump("", descendants, out);
		out.println("-----------------------------");
		*/
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
		
		DataCursor<List<String>> cursor = null; 
		GraphDatabaseService graphDb = null;
		IndexService indexer = null;

		try {
			graphDb = new EmbeddedGraphDatabase( args.getParameter(0), configuration );
			indexer = new LuceneIndexService(graphDb); 
	
			CatGraph graph = new CatGraph(graphDb, indexer);
			
			if (args.getParameterCount()>1) {
				File tsv = new File(args.getParameter(1));
				InputStreamReader rd = new InputStreamReader(new FileInputStream(tsv));
				cursor =  new ChunkingCursor(new LineCursor(rd), CsvLineChunker.tsv);
			
				cursor.next(); //skip header in first line
	
				System.out.println("loading arcs....");
				long t = System.currentTimeMillis();
				graph.loadArcs(cursor, 0, 1);
				System.out.println("loading arcs took "+(System.currentTimeMillis() - t)+"ms.");
			}
			
			graph.run();
			
			System.out.println( "done" ); 
		}
		finally
		{
			if (indexer!=null) indexer.shutdown();
		   if (graphDb!=null) graphDb.shutdown();
		   System.exit(0);
		}
	}
}
