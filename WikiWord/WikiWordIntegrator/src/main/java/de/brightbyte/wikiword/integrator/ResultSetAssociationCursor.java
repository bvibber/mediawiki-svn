package de.brightbyte.wikiword.integrator;

import java.sql.ResultSet;

import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseDataSet.Factory;

public class ResultSetAssociationCursor extends DatabaseDataSet.Cursor<Association> {

	public static class FeatureSetFactory implements Factory<FeatureSet> {
		protected String[] fields;
		
		public FeatureSetFactory(String[] fields) {
			super();
			this.fields = fields;
		}

		public FeatureSet newInstance(ResultSet row) throws Exception {
			FeatureSet f = new DefaultFeatureSet();
			
			for (String k: fields) {
				Object v = row.getObject(k);
				f.put(k, v);
			}
			
			return f;
		}

	}

	public static class AssociationFactory implements Factory<Association> {
		protected Factory<FeatureSet> sourceFactory;
		protected Factory<FeatureSet> targetFactory;
		protected Factory<FeatureSet> propsFactory;
		
		public AssociationFactory(Factory<FeatureSet> sourceFactory, Factory<FeatureSet> targetFactory, Factory<FeatureSet> propsFactory) {
			this.sourceFactory = sourceFactory;
			this.targetFactory = targetFactory;
			this.propsFactory = propsFactory;
		}

		public Association newInstance(ResultSet row) throws Exception {
			FeatureSet source = sourceFactory.newInstance(row);
			FeatureSet target = targetFactory.newInstance(row);
			FeatureSet props = propsFactory.newInstance(row);
			
			return new Association(source, target, props);
		}

	}

	public ResultSetAssociationCursor(ResultSet resultSet, String[] sourceFields, String[] targetFields, String[] propertyFields) {
		super(resultSet, new AssociationFactory(new FeatureSetFactory(sourceFields), new FeatureSetFactory(targetFields), new FeatureSetFactory(propertyFields)));
	}

}
