<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="2.0">
    <xsl:template match="/">
        <xsl:element name="modsCollection">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="/records">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="record">
        <xsl:element name="mods">
            <xsl:apply-templates select="title"/>
            <xsl:apply-templates select="photographer"/>
            <xsl:call-template name="src"/>
            <xsl:call-template name="virtual"/>
            <xsl:apply-templates select="subjects"/>
            <xsl:call-template name="access"/>
            <xsl:call-template name="recordInfo"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="title">
        <xsl:element name="titleInfo">
            <xsl:element name="title">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template name="virtual">
        <xsl:element name="relatedItem">
            <!-- see the following for an example http://library.princeton.edu/departments/tsd/metadoc/mods/relateditem.html           -->
            <xsl:attribute name="type">host</xsl:attribute>
            <xsl:element name="typeOfResource">
                <xsl:attribute name="collection">yes</xsl:attribute>still image</xsl:element>
            <xsl:element name="titleInfo">
                <xsl:attribute name="type">uniform</xsl:attribute>
                <xsl:attribute name="authority">?????</xsl:attribute>
                <xsl:value-of select="digital-collection"/>
            </xsl:element>

        </xsl:element>
    </xsl:template>

    <xsl:template name="src">
        <xsl:element name="relatedItem">
            <xsl:attribute name="type">original</xsl:attribute>
            <xsl:element name="originInfo">
                <xsl:apply-templates select="date"/>
            </xsl:element>
            <xsl:apply-templates select="physical-description"/>
            <xsl:element name="location">
                <xsl:element name="physicalLocation">
                    <xsl:attribute name="displayLabel">Original</xsl:attribute>
                    <xsl:value-of select="cite-as"/>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template name="recordInfo">
        <xsl:element name="recordInfo">
            <xsl:element name="recordIdentifer">
                <xsl:value-of select="item-number"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="photographer">
        <xsl:element name="name">
            <xsl:attribute name="type">personal</xsl:attribute>
            <!-- see the following for an example https://www.digitalcommonwealth.org/search/commonwealth-oai:pr76f487p/librarian_view           -->
            <xsl:element name="namePart">
                <xsl:value-of select="."/>
            </xsl:element>
            <xsl:element name="role">
                <xsl:element name="roleTerm">
                    <xsl:attribute name="type">code</xsl:attribute>
                    <xsl:attribute name="authority">marcrelator</xsl:attribute>pht</xsl:element>
                <xsl:element name="roleTerm">
                    <xsl:attribute name="type">text</xsl:attribute>
                    <xsl:attribute name="authority">marcrelator</xsl:attribute>Photographer
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="physical-description">
        <xsl:element name="physicalDescription">
            <xsl:element name="note">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:element>
    </xsl:template>

    <xsl:template match="type">
        <xsl:element name="typeOfResource">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="subjects">
        <xsl:for-each select="tokenize(., '; ')">
            <xsl:element name="subjectAuthority">
                <xsl:element name="topic">
                    <xsl:value-of select="."/>
                </xsl:element>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="date">
        <xsl:element name="dateCreated">
            <xsl:attribute name="encoding">w3cdtf</xsl:attribute>
            <xsl:attribute name="keyDate">yes</xsl:attribute>
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>

    <xsl:template name="access">
        <xsl:element name="accessCondition">
            <xsl:call-template name="restrictions"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="digital-collection"> </xsl:template>

    <xsl:template match="shelving-location"> </xsl:template>

    <xsl:template match="repository"> </xsl:template>

    <xsl:template match="repository-collection-guide"> </xsl:template>

    <xsl:template match="cite-as"> </xsl:template>

    <xsl:template name="restrictions">
        <xsl:value-of select="restrictions"/>
    </xsl:template>

    <xsl:template match="contoct-and-ordering-information"> </xsl:template>

    <xsl:template match="item-number"> </xsl:template>

    <xsl:template match="item-url"> </xsl:template>

    <xsl:template match="collection-url"> </xsl:template>

    <xsl:template match="date-created">
        <xsl:element name="dateCreated">
            <xsl:attribute name="encoding">w3cdtf</xsl:attribute>
            <xsl:attribute name="keyDate">yes</xsl:attribute>
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="date-modified"> </xsl:template>

</xsl:stylesheet>