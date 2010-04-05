package de.brightbyte.wikiword;


/**
 * Enumeration of resource types; each resource type represents a kind of page that may occurr 
 * in a wiki. Resource types represents functionally different kinds of pages, and they generally
 * determine if and how a page will be further analyzed. 
 * Each type is associated with a code (for internal use) and a URI (for external use).
 * The URI is constructed based on {@link RdfEntities.resourceTypeBase}.
 */
public enum ResourceType {

	/** Unknown type, SHOULD not occurr in final data. MAY be used for
	 * resources that are referenced but where not available for analysis,
	 * or have not yet been analyzed. 
	 **/
	UNKNOWN(0),
	
	/**
	 * A "real" page, describing a concept.
	 */
	ARTICLE(10),
	
	/**
	 * This page is a supplemental part of an article, typically a transcluded
	 * subpage or simmilar.   
	 */
	SUPPLEMENT(15),
	
	
	/**
	 * A page solely defining a redirect/alias for another page
	 */
	REDIRECT(20),

	/**
	 * A disambuguation page, listing different meanings for the page title, 
	 * each linking to a article page.
	 */
	DISAMBIG(30),
	
	/**
	 * A page that contains a list of concepts that share some common property or quality,
	 * usually each linking to a page describing that concept.
	 */
	LIST(40),

	/**
	 * A page acting as a portal for a specific topic, often including meta-information
	 * about maintenance activity.
	 */
	PORTAL(45),
	
	/**
	 * A category page.
	 */
	CATEGORY(50),
	
	/**
	 * This page does not contain relevant information for WikiWord
	 */
	OTHER(99),
	
	/**
	 * A page that is broken in some way, or was marked as bad or disputed. Such pages
	 * SHOULD generally be treated as if theys didn't exist.
	 */
	BAD(100),
	
	/**
	 * A resource that is not a page by itself, but merely a section of a page. Sections
	 * SHOULD always be part of a page of type ARTICLE, and are expected to descibe
	 * a narrower concept than the "parent" page.
	 */
	SECTION(200);
	
	private int code;
	
	private ResourceType(int code) {
		this.code = code;
	}
	
	public int getCode() {
		return code;
	}

	public static ResourceType getType(int code) {
		for (ResourceType v : values()) {
			if (code == v.getCode()) return v;
		}
		
		throw new IllegalArgumentException("unknown code "+code+" for enumeration "+ResourceType.class.getName());
	}
}
