package de.brightbyte.wikiword.integrator;

import java.util.Arrays;
import java.util.Collection;

public class MappingCandidates {
		protected FeatureSet subject;
		protected Collection<FeatureSet> candidates;
		
		public MappingCandidates(FeatureSet subject, FeatureSet... candidates) {
			this(subject, Arrays.asList(candidates));
		}
		
		public MappingCandidates(FeatureSet subject, Collection<FeatureSet> candidates) {
			this.subject = subject;
			this.candidates = candidates;
		}

		public Collection<FeatureSet> getCandidates() {
			return candidates;
		}

		public FeatureSet getSubject() {
			return subject;
		}

		@Override
		public int hashCode() {
			final int PRIME = 31;
			int result = 1;
			result = PRIME * result + ((candidates == null) ? 0 : candidates.hashCode());
			result = PRIME * result + ((subject == null) ? 0 : subject.hashCode());
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
			final MappingCandidates other = (MappingCandidates) obj;
			if (candidates == null) {
				if (other.candidates != null)
					return false;
			} else if (!candidates.equals(other.candidates))
				return false;
			if (subject == null) {
				if (other.subject != null)
					return false;
			} else if (!subject.equals(other.subject))
				return false;
			return true;
		}
		
		public String toString() {
			return subject + " <-> " + candidates; 
		}
		
}
