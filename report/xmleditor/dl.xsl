<xsl:stylesheet version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 <xsl:output omit-xml-declaration="yes" indent="yes"/>
 <xsl:strip-space elements="*"/>
 <xsl:template match="/*">
	<h1><xsl:value-of select="name()"/></h1>
	<dl id="tog">
		<xsl:apply-templates/>
		<xsl:apply-templates select="@*"/>
	</dl>
 </xsl:template>

 <xsl:template match="*[*][parent::*]">
	<!--
    <dt class="expandable">
		<button class="expandcollapse" />
		<xsl:value-of select="name()"/>
		<button class="delete" />
	</dt>
	-->

    <xsl:element name="dt">
             <xsl:attribute name="id">
                <xsl:value-of select="@id"/>
             </xsl:attribute>
		<xsl:attribute name="class">expandable</xsl:attribute>
        <!-- <xsl:attribute name="id">dt-<xsl:value-of select="@id"/></xsl:attribute> -->
			<button class="expandcollapse" />
			<xsl:value-of select="name()"/>&#160;
			<xsl:choose>
				<xsl:when test="number">
					<xsl:value-of select="number" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="id" />
				</xsl:otherwise>
			</xsl:choose>
			<button class="delete" />
    </xsl:element>

      <dd>
        <dl class="attributes">
			<xsl:apply-templates select="@*"/>
		</dl>
        <xsl:element name="dl">
           <xsl:attribute name="class">children</xsl:attribute>
			<xsl:apply-templates/>
        </xsl:element>
      </dd>
 </xsl:template>

 <xsl:template match="*[not(*)]">
	<!-- <dt><xsl:value-of select="name()"/></dt> -->

    <xsl:element name="dt">
            <xsl:value-of select="name()"/>
    </xsl:element>

    <dd>
		<xsl:element name="input">
 	       <xsl:attribute name="type">text</xsl:attribute>
			<xsl:attribute name="name">
				<xsl:value-of select="name()"/>
			</xsl:attribute>
	         <xsl:attribute name="value">
				<xsl:value-of select="." />
	         </xsl:attribute>
             <xsl:attribute name="id">
                <xsl:value-of select="@id" />
             </xsl:attribute>
        </xsl:element>
	</dd>
 </xsl:template>

 <xsl:template match="@*[@*][parent::*]">
      <dt><xsl:value-of select="name()"/></dt>
      <dd>
        <dl class="attributes">
          <xsl:apply-templates/>
        </dl>
      </dd>
 </xsl:template>

 <xsl:template match="@*[(contains(., 'autoidTEEX')) and (name() = 'id')]">
 </xsl:template>

 <xsl:template match="@*">
	<!--
	<xsl:if test="contains(., 'TEEX')">
		<h1>Contains!</h1>
	</xsl:if>
	-->
    <dt><xsl:value-of select="name()"/></dt>
    <dd>
        <xsl:element name="input">
           <xsl:attribute name="type">text</xsl:attribute>
            <xsl:attribute name="name">
	            <xsl:value-of select="name()"/>
            </xsl:attribute>
             <xsl:attribute name="value">
                <xsl:value-of select="." />
             </xsl:attribute>
            <xsl:attribute name="id">
				<xsl:value-of select="../@id" />-attr-<xsl:value-of select="name()"/>
			</xsl:attribute>
        </xsl:element>
    </dd>
 </xsl:template>

</xsl:stylesheet>
