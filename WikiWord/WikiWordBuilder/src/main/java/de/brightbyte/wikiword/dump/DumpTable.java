package de.brightbyte.wikiword.dump;

import de.brightbyte.util.PersistenceException;

public class DumpTable extends SQLDumperApp {

	protected String table;
	
	public DumpTable() {
		super(true, true);
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();
		
		args.declare("fields", null, false, Boolean.class, "Database fields to dump, as a comma-separated list. Supports SQL syntax, like \"AS\".");
	}

	@Override
	protected String getQuerySQL() {
		   String fields = args.getOption("fields", "*"); //TODO: split, sanitize and quote to avoid injection!
		
			String t = conceptStoreDB.getSQLTableName(table, true);
			String sql = "SELECT "+fields+" FROM " + t;
			return sql;
	}

	protected void open(int paramOffset) throws PersistenceException {
		this.table = args.getParameter(paramOffset);
		
		sink = openSink(paramOffset+1);
	}

	
	public static void main(String[] argv) throws Exception {
		DumpTable app = new DumpTable();
		app.launch(argv);
	}
}
