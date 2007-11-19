<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/">
		<martif type="TBX" xml:lang="en">
		    <martifHeader>
		        <fileDesc>
		            <titleStmt>
		                <title>Wikidata Terms</title>
		            </titleStmt>
		            <sourceDesc>
		                <p>from an OmegaWiki termbase</p>
		            </sourceDesc>
		        </fileDesc>
		        <encodingDesc>
		            <p type="DCSName">SYSTEM "TBXDCSv05b.xml"</p>
		        </encodingDesc>
		    </martifHeader>
		    <text>
		    	<body>
		    		<xsl:apply-templates/>
		  		</body>
		  	</text>
		</martif>
	</xsl:template>
	
	<xsl:template match="/wikidata/body/defined-meaning">
		<termEntry>
			<xsl:attribute name="id">
				<xsl:value-of select="@defined-meaning-id" />
			</xsl:attribute>
			<xsl:for-each select="definition/translated-text-list/translated-text">
				<xsl:param name="language" select="@language"/>
				<langSet>
					<xsl:attribute name="xml:lang">
						<xsl:value-of select="$language" />
					</xsl:attribute>
					<descripGrp>
						<descrip type="definition"><xsl:value-of select="." /></descrip>
					</descripGrp>
					<tig>
						<xsl:for-each select="../../../synonyms-translations-list/synonyms-translations/expression[@language=$language]">
							<termGrp>
								<term><xsl:value-of select="." /></term>
							</termGrp>
						</xsl:for-each>
					</tig>
				</langSet>
			</xsl:for-each>
		</termEntry>
	</xsl:template>
</xsl:stylesheet>