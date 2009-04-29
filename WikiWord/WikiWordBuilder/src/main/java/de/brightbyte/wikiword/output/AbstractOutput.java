package de.brightbyte.wikiword.output;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;

public abstract class AbstractOutput implements DataOutput {
	
	//protected int logLevel = LOG_INFO;
	//private Agenda agenda;
	private DatasetIdentifier dataset;
	
	public AbstractOutput(DatasetIdentifier dataset) {
		this.dataset = dataset;
	}

	public Corpus getCorpus() {
		if (!(getDatasetIdentifier() instanceof Corpus)) return null;
		else return (Corpus)getDatasetIdentifier();
	}

	public void prepare() throws PersistenceException {
		//noop
	}

	public void finish() throws PersistenceException {
		flush();
	}

	/*
	public Agenda createAgenda() throws PersistenceException {
		if (agenda==null) agenda = new Agenda(new Agenda.TransientPersistor());
		return agenda;
	}

	public Agenda getAgenda() {
		return agenda;
	}
	*/
	
	/*
	public void setLogLevel(int logLevel) {
		this.logLevel = logLevel;
	}
	*/
	
	public DatasetIdentifier getDatasetIdentifier() {
		return dataset;
	}
	
	public abstract void flush() throws PersistenceException;
	
	public abstract void close() throws PersistenceException;
	
}
