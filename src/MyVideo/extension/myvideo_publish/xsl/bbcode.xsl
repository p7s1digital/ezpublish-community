<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:php="http://php.net/xsl"
                version="1.0">

    <xsl:output method="text" indent="no" encoding="UTF-8" omit-xml-declaration="yes" />
    <xsl:preserve-space elements="paragraph"/>
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="paragraph">
        <xsl:text>[div class="bbcode--paragraph"]</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>[/div]</xsl:text>
    </xsl:template>

    <xsl:template match="line">
        <xsl:apply-templates/>
        <xsl:text>&#xa;</xsl:text>
    </xsl:template>

    <xsl:template match="table">
        <xsl:text>[table class="bbcode--table"]</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>[/table]</xsl:text>
    </xsl:template>

    <xsl:template match="tr">
        <xsl:text>[tr class="bbcode--table-row"]</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>[/tr]</xsl:text>
    </xsl:template>

    <xsl:template match="td">
        <xsl:text>[td class="bbcode--table-cell"]</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>[/td]</xsl:text>
    </xsl:template>

    <xsl:template match="ul">
        <xsl:text>[list]</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>[/list]</xsl:text>
    </xsl:template>

    <xsl:template match="ol">
        <xsl:text>[olist]</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>[/olist]</xsl:text>
    </xsl:template>

    <xsl:template match="li">
        <xsl:text>[li]</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>[/li]</xsl:text>
    </xsl:template>

    <xsl:template match="link">
        <xsl:text>[url="</xsl:text>
        <xsl:value-of select="php:function('mxdXMLTransform::getUrlFromId',string(@url_id))"/>
        <xsl:text>"]</xsl:text>
        <xsl:apply-templates />
        <xsl:text>[/url]</xsl:text>
    </xsl:template>

    <xsl:template match="strong">
        <xsl:text>[b]</xsl:text>
        <xsl:apply-templates />
        <xsl:text>[/b]</xsl:text>
    </xsl:template>

    <xsl:template match="emphasize">
        <xsl:text>[i]</xsl:text>
        <xsl:apply-templates />
        <xsl:text>[/i]</xsl:text>
    </xsl:template>

    <xsl:template match="custom[@name='underline']">
        <xsl:text>[u]</xsl:text>
        <xsl:apply-templates />
        <xsl:text>[/u]</xsl:text>
    </xsl:template>

    <xsl:template match="ezauthor/authors/author">
        <xsl:text>[email=</xsl:text>
        <xsl:value-of select="@email"/>
        <xsl:text>]</xsl:text>
        <xsl:value-of select="@name"/>
        <xsl:text>[/email]</xsl:text>
    </xsl:template>

</xsl:stylesheet>
