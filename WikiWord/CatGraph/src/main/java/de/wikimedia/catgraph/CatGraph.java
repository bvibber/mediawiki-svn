package de.wikimedia.catgraph;

import java.io.File;
import java.io.IOException;
import java.sql.ResultSet;
import java.sql.SQLException;

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

import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.io.IOUtil;

public class CatGraph {
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

	public void load(DatabaseAccess db, String sql) throws SQLException {
		ResultSet rs = db.executeQuery("load graph", sql);
		while (rs.next()) {
			int from = rs.getInt(1);
			int to = rs.getInt(2);
			
			putArc(from ,to);
		}
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
		return putArc( aquireNodeByPageId(from), aquireNodeByPageId(cat) );
	}

	public Relationship putArc(Node from, Node cat) {
		Relationship relationship = cat.createRelationshipTo( from, CategoryRelationships.CONTAINS );
		return relationship;
	}

	public void traverseAndDump(Node startNode) {
		Traverser traverser = startNode.traverse( Traverser.Order.BREADTH_FIRST , StopEvaluator.END_OF_GRAPH, ReturnableEvaluator.ALL, CategoryRelationships.CONTAINS, Direction.OUTGOING   );
		for ( Node node : traverser )
		{
		    System.out.println( node ); 
		}
	}
	
	public static void main(String[] args) throws IOException, SQLException {
		GraphDatabaseService graphDb = new EmbeddedGraphDatabase( args[0] );
		DatabaseConnectionInfo dbInfo = new DatabaseConnectionInfo( new File(args[1]) );
		String sql = IOUtil.slurp(new File(args[2]), "UTF-8");
		
		IndexService indexer = new LuceneIndexService(graphDb); 
		
		DatabaseAccess db = new DatabaseSchema(null, dbInfo, null);  
		
		CatGraph graph = new CatGraph(graphDb, indexer);
		
		Transaction tx = graphDb.beginTx();
		try
		{
			
			graph.load(db, sql);
			
			graph.traverseAndDump(graphDb.getReferenceNode());
			
		   tx.success();
		}
		finally
		{
		   tx.finish();
		}
		
		System.out.println( "done" ); 
	}
}
