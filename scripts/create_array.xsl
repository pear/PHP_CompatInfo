<?xml version='1.0'?>
<!--
/**
 * $RCSfile: create_array.xsl,v $
 * Copyright: David Shafik and Synaptic Media
 * Began: 
 * Desc: 
 * Version number: 1.0
 * E-Mail: davey@synapticmedia.net
 * URL: www.synapticmedia.net
 * Build: $Revision: 1.1 $
 * Modified: $Date: 2004-03-09 18:59:34 $
 */
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="text" version="1.0" omit-xml-declaration="yes" indent="yes" /> 

<xsl:variable name="versions" select="document('versions.xml')" />
<xsl:variable name="eol">
<xsl:text>
</xsl:text>
</xsl:variable>

<xsl:template match="/">
	<xsl:for-each select="/PHPFunc-List/PHPFunc">
		<xsl:text>$funcs['</xsl:text><xsl:value-of select="t" /><xsl:text>']['extension'] = '</xsl:text><xsl:value-of select="mn" /><xsl:text>';
		</xsl:text>		
		<xsl:text>$funcs['</xsl:text><xsl:value-of select="t" /><xsl:text>']['version_init'] = '</xsl:text>
		<xsl:call-template name="version">
			<xsl:with-param name="version">
				<xsl:value-of select="substring-before(ver,',')" />
			</xsl:with-param>
		</xsl:call-template>
		<xsl:text>';
		</xsl:text>
	</xsl:for-each>
</xsl:template> 

<xsl:template name="version">
	<xsl:param name="version" />
	<xsl:value-of select="normalize-space($versions/PHPVersion-list/PHPVersion/No[text()=$version]/following-sibling::Caption/text())" />
</xsl:template>
</xsl:stylesheet>