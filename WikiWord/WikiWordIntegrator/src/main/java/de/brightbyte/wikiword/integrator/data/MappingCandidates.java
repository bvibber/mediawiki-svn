package de.brightbyte.wikiword.integrator.data;

import java.util.Arrays;
import java.util.Collection;

import de.brightbyte.util.CollectionUtils;

public class MappingCandidates {
		protected ForeignEntityRecord subject;
		protected Collection<ConceptEntityRecord> candidates;
		
		public MappingCandidates(ForeignEntityRecord subject, ConceptEntityRecord... candidates) {
			this(subject, Arrays.asList(candidates));
		}
		
		public MappingCandidates(ForeignEntityRecord subject, Collection<ConceptEntityRecord> candidates) {
			this.subject = subject;
			this.candidates = candidates;
		}

		public Collection<ConceptEntityRecord> getCandidates() {
			return candidates;
		}

		public ForeignEntityRecord getSubject() {
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
			} else if (!CollectionUtils.contentEquals(candidates, other.candidates))
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
