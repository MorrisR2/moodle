<?xml version='1.0'?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
      <xsl:output method="html" indent="yes" media-type="text/html"/>

      <xsl:template match="/">

            <xsl:element name="table">
                  <xsl:apply-templates select="/*/*/*" />
            </xsl:element>

      </xsl:template>

      <xsl:template match="*">

            <xsl:element name="tr">
                  <xsl:element name="td">
                        <xsl:value-of select="name(.)" />
                  </xsl:element>
                  <xsl:element name="td">
                        <xsl:element name="input">
                              <xsl:attribute name="type">text</xsl:attribute>
                              <xsl:attribute name="name">
                                    <xsl:value-of select="name(.)" />
                              </xsl:attribute>
                              <xsl:attribute name="value">
                                    <xsl:value-of select="." />
                              </xsl:attribute>
                        </xsl:element>
                  </xsl:element>
            </xsl:element>

      </xsl:template>

</xsl:stylesheet>
