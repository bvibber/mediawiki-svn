package de.brightbyte.wikiword.integrator;

import java.io.IOException;
import java.net.URL;
import java.sql.SQLException;

import org.dbunit.DatabaseUnitException;
import org.dbunit.operation.DatabaseOperation;

import de.brightbyte.db.testing.DatabaseTestBase;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

public abstract class IntegratorAppTestBase<T extends AbstractIntegratorApp> extends DatabaseTestBase {
	
	protected class AppRunner {
		protected String testName;
		protected boolean completed = false;
		
		protected T app;
		
		public AppRunner(String testName) throws IOException {
			this.testName = testName;
		}
		
		public T prepare() throws IOException, PersistenceException, DatabaseUnitException, SQLException {
			TweakSet tweaks = loadTweakSet();
			app = createApp();
			app.setKeepAlive(true);
			app.setTweaks(tweaks);
				
			FeatureSetSourceDescriptor source = loadSourceDescriptor(testName);
			source = app.getAugmentedSourceDescriptor(source);

			app.slaveInit(testDataSource, DatasetIdentifier.forName("TEST", "xx"), tweaks, source, testName);
			
			WikiWordStoreBuilder store = app.getStoreBuilder();
			store.initialize(true, true);
			
			String[] sql = loadSqlScript(getBaseName()+"-"+testName+".create");
			if (sql!=null && sql.length>0) runRawQueries(sql);

			insertDataSetIfExists(getBaseName()+"-"+testName+".initial");
			
			return app;
		}
		
		public void run() throws Exception {
			if (completed) throw new IllegalStateException("app was already run");
			if (app==null) prepare();
			
			//run application
			app.slaveLaunch();
			completed = true;
		}

		public void assertResult() throws Exception {
			if (!completed) throw new IllegalStateException("call run() first");
			
			 //compare query result to expected data from XML file.
			 assertTableData(getBaseName()+"-"+testName, app.getConfiguredDataset().getDbPrefix()+testName);
		}
		
	}
	
	public IntegratorAppTestBase(String name) {
		super(name);
	}
	
    protected DatabaseOperation getSetUpOperation() throws Exception
    {
        return DatabaseOperation.NONE;
    }

	public TweakSet loadTweakSet() throws IOException {
		URL url = requireAuxilliaryFileURL("test-tweaks.properties");
		TweakSet tweaks = new TweakSet();
		tweaks.loadTweaks(url);
		return tweaks;
	}

	public FeatureSetSourceDescriptor loadSourceDescriptor(String testName) throws IOException {
		URL url = requireAuxilliaryFileURL(getBaseName()+"-"+testName+".properties");
		FeatureSetSourceDescriptor descriptor = new FeatureSetSourceDescriptor();
		descriptor.loadTweaks(url);
		return descriptor;
	}

	protected abstract T createApp();
	
	public void runApp(String testName) throws Exception {
		AppRunner runner = new AppRunner(testName);
		runner.run();
		runner.assertResult();
	}
		
}
