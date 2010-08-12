<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:c="http://exslt.org/common"
                xmlns:fn="http://www.w3.org/2005/xpath-functions"
                xmlns:set="http://exslt.org/sets">
  <xsl:param name="userinfo"/>

  <xsl:output
      method="text"
      encoding="UTF-8" />

  <xsl:template match="/cruisecontrol/build[@error]">
Something broke: <xsl:value-of select="string(@error)"/>
    <xsl:call-template name="get-last-modified"/>
  </xsl:template>

  <xsl:template name="get-last-modified"><xsl:text>
Possible culprits: </xsl:text>
    <xsl:for-each select="set:distinct(c:node-set(
                                       /cruisecontrol/modifications/modification/user |
                                       /cruisecontrol/modifications/modification/revision))">
      <xsl:if test="name() = 'user'">
        <xsl:call-template name="irc-nick-lookup">
          <xsl:with-param name="id">
            <xsl:value-of select="."/>
          </xsl:with-param>
        </xsl:call-template>
      </xsl:if>
      <xsl:if test="name() = 'revision'">/r<xsl:value-of select="."/>
      <xsl:text> </xsl:text>
      </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="irc-nick-lookup">
    <xsl:param name="id"/>
    <xsl:variable name="nick"
                  select="document($userinfo)/userinfo/committer[@id=$id]/irc"/>
    <xsl:choose>
      <xsl:when test="$nick">
        <xsl:value-of select="$nick"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$id"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="text()"/>

</xsl:stylesheet>