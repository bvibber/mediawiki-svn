package de.brightbyte.wikiword.integrator;

public class Association {
		protected FeatureSet sourceItem;
		protected FeatureSet targetItem;
		protected FeatureSet properties;
		
		public Association(FeatureSet sourceItem, FeatureSet targetItem, FeatureSet... properties) {
			if (sourceItem==null) throw new NullPointerException();
			if (targetItem==null) throw new NullPointerException();
			
			this.sourceItem = sourceItem;
			this.targetItem = targetItem;
			this.properties = properties==null ? new DefaultFeatureSet() : FeatureSets.merge(properties);
		}

		public FeatureSet getProperties() {
			return properties;
		}

		public FeatureSet getSourceItem() {
			return sourceItem;
		}

		public FeatureSet getTargetItem() {
			return targetItem;
		}
		
		public String toString() {
			return "[" + sourceItem + " <" + properties + "> " + targetItem + "]"; 
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + ((properties == null) ? 0 : properties.hashCode());
			result = PRIME * result + ((sourceItem == null) ? 0 : sourceItem.hashCode());
			result = PRIME * result + ((targetItem == null) ? 0 : targetItem.hashCode());
			return result;
		}

		@Override
		public boolean equals(Object obj) {
			if (this == obj)
				return true;
			if (obj == null)
				return false;
			if (getClass() != obj.getClass())
				return false;
			final Association other = (Association) obj;
			if (properties == null) {
				if (other.properties != null)
					return false;
			} else if (!properties.equals(other.properties))
				return false;
			if (sourceItem == null) {
				if (other.sourceItem != null)
					return false;
			} else if (!sourceItem.equals(other.sourceItem))
				return false;
			if (targetItem == null) {
				if (other.targetItem != null)
					return false;
			} else if (!targetItem.equals(other.targetItem))
				return false;
			return true;
		}

		public static Association merge(Association a, Association b) {
			FeatureSet src = FeatureSets.merge(a.getSourceItem(), b.getSourceItem());
			FeatureSet tgt = FeatureSets.merge(a.getTargetItem(), b.getTargetItem());
			FeatureSet props = FeatureSets.merge(a.getProperties(), b.getProperties());
			return new Association(src, tgt, props);
		}
		
		
}
