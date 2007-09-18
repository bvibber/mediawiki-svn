package org.wikimedia.lsearch.analyzers;

/**
 * Generate field names for the index. 
 * 
 * @author rainman
 *
 */
public class FieldNameFactory {
	public static final boolean EXACT_CASE = true;
	protected boolean exactCase;

	public FieldNameFactory(){
		this.exactCase = false;
	}
	
	public FieldNameFactory(boolean exactCase){
		this.exactCase = exactCase;
	}

	public String contents(){
		if(exactCase)
			return "contents_exact";
		else
			return "contents"; 
	}
	
	public String title(){
		if(exactCase)
			return "title_exact";
		else
			return "title"; 
	}
	
	public String stemtitle(){
		if(exactCase)
			return "stemtitle_exact";
		else
			return "stemtitle"; 
	}
	
	public String alttitle(){
		if(exactCase)
			return "alttitle_exact";
		else
			return "alttitle"; 
	}
	
	public String keyword(){
		if(exactCase)
			return "keyword_exact";
		else
			return "keyword";
	}
	
	public String related(){
		if(exactCase)
			return "related_exact";
		else
			return "related";
	}
	
	public String anchor(){
		if(exactCase)
			return "anchor_exact";
		else
			return "anchor";
	}
	
	public String wholetitle(){
		if(exactCase)
			return "wholetitle_exact";
		else
			return "wholetitle";
	}


	public boolean isExactCase() {
		return exactCase;
	}	
}
