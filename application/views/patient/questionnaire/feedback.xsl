<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="html" indent="yes" />

<xsl:template match="Questionnaire">
<xsl:for-each select="Language">
<xsl:if test="$language = @lang">
<main role="main">
	<article>
		<xsl:if test="count(Headline) &gt; 0">
			<header><h2><xsl:value-of select="Headline" /> 
				<xsl:if test="count(Headline/@alternate) &gt; 0"> <!--später evtl. auch leeren String abfangen--> 
					(<xsl:value-of select="Headline/@alternate" />)
				</xsl:if>
				</h2></header>
		</xsl:if>
		<xsl:if test="count(Info) &gt; 0">
		</xsl:if>
		<xsl:for-each select="Set">
			<section><xsl:apply-templates select="."></xsl:apply-templates></section>
		</xsl:for-each>
	</article>
</main>
<script>
<xsl:for-each select="Set">
	<xsl:choose>
		<xsl:when test="@type='slider' or @type='mm_slider' or @type='mmm_slider'">
			<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)"></xsl:variable>
			<xsl:for-each select="Question">
				<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
				var temp = "_" + <xsl:value-of select="$directory_number" /> + "_" + <xsl:value-of select="$file_number" /> + "_" + <xsl:value-of select="$set_name" /> + "_" + 
							<xsl:value-of select="$position" />;
				/*var f = function(event, ui) {
					var temp1 = "_" + <xsl:value-of select="$directory_number" /> + "_" + <xsl:value-of select="$file_number" /> + "_" + <xsl:value-of select="$set_name" /> + "_" + 
							<xsl:value-of select="$position" />;
					jQuery("#slider_value"+temp1).val(ui.value);
				}*/
				jQuery("#slider" + temp).slider({
					value: jQuery("#slider_value"+temp).val(), 
					disabled: true,
					range: "min",
					min: <xsl:value-of select="ancestor::Set/Scale/MinValue" />,
					max: <xsl:value-of select="ancestor::Set/Scale/MaxValue" />
				});
				/*jQuery("#slider"+temp).val(jQuery("#slider"+temp).slider("values", 0));*/
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise></xsl:otherwise>
	</xsl:choose>
</xsl:for-each>	
</script>
</xsl:if>
</xsl:for-each>
</xsl:template>

<xsl:template match="Set">
<xsl:variable name='table' select="/Questionnaire/@table" />
	<xsl:variable name="type" select="@type" />
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />
	<xsl:if test="count(Caption) &gt; 0">
		<h3><xsl:value-of select="Caption" /></h3>
	</xsl:if>
	<xsl:if test="count(Description) &gt; 0">
		<p><xsl:value-of select="Description" /></p>
	</xsl:if>
	<xsl:choose>
		<xsl:when test="@type='radio'">
			<table class="radio_table">
			<thead>
				<tr>
					<th class="radio_question_header"></th>
					<xsl:for-each select="Scale/Option">
						<th class="radio_option_header">
							<xsl:if test="count(Text) &gt; 0">
								<xsl:value-of select="Text" />
							</xsl:if>
						</th>
					</xsl:for-each>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="Question">
				<!--ändern: nur testen, ob enumerate einen sinnvollen Wert enthält-->
				<xsl:if test="not(@noeval='yes') or $eval_everything">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />
					<xsl:variable name="selected" select="php:function('selected', string($table), string($column))" />
					<xsl:variable name="even_odd" select="$position mod 2" />
					<tr class="qt_{$even_odd}">
						<td class="radio_question">
							<!--ändern: nur testen, ob enumerate einen sinnvollen Wert enthält-->
							<xsl:if test="ancestor::Set/@enumerate='yes'">
								<xsl:value-of select="format-number(number($position), '00')" />. 
							</xsl:if>
							<xsl:copy-of select="node()" />
						</td>
						<xsl:for-each select="ancestor::Set/Scale/Option">
							<xsl:variable name="val" select="Value" />
							<xsl:variable name="rid" select="1+count(preceding-sibling::Option)" />
							<xsl:variable name="color" select="@color" />
							<xsl:choose>
								<xsl:when test="Value=$selected">
									<td class="radio_container radio_selected"><strong><xsl:value-of select="Value" /></strong></td>
								</xsl:when>
								<xsl:otherwise>
									<td class="radio_container"><strong><xsl:value-of select="Value" /></strong></td>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</tr>
				</xsl:if>
				</xsl:for-each>
			</tbody>
			</table>
		</xsl:when>
		<xsl:when test="@type='slider'">
			<table class="slider_table">
			<!--<thead>
				<tr>
					<th>Frage</th>
					<th>Antwort</th>
				</tr>
			</thead>-->
			<tbody>
				<xsl:for-each select="Question">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />				
					<tr>
						<td class="slider_question">
							<!--Bitte ändern: es soll nur getestet werden, ob enumerate einen sinnvollen Wert enthält
								danach Tutorial ändern-->
							<xsl:if test="ancestor::Set/@enumerate='yes'">
								<xsl:value-of select="format-number(number($position), '00')" />. 
							</xsl:if>
							<xsl:copy-of select="node()" />
						</td>
						<td>TESTDUMMY</td>
					</tr>
				</xsl:for-each>
			</tbody>
			</table>
		</xsl:when>
		<xsl:when test="@type='mm_slider'">
			<table class="mm_slider_table">
				<xsl:for-each select="Question">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />
					<tr class="mm_slider_question">
						<td>
							<strong><xsl:value-of select="." /></strong>
						</td>
						<td>
							<xsl:value-of select="ancestor::Set/Scale/MinValue" /> - <strong><xsl:value-of select="$selected" /></strong> - 
							<xsl:value-of select="ancestor::Set/Scale/MaxValue" />
						</td>
					</tr>
				</xsl:for-each>
			</table>
		</xsl:when>
		<xsl:when test="@type='check_t'">
			<xsl:for-each select="Question">
				<strong><xsl:value-of select="." /></strong>
				<table class="an_radio_table" border="1 solid">
					<xsl:variable name="cspan" select="count(ancestor::Set/Scale/Option)" />
					<tbody>
							<tr>
								<xsl:for-each select="ancestor::Set/Scale/Option">
									<xsl:variable name="column" select="@column" />
									<xsl:variable name="selected" select="php:function('selected', string($table), string($column))" />
									<xsl:variable name="color" select="@color" />

									<xsl:choose>
										<xsl:when test="$selected=1">
											<td class="an_radio_eval an_radio_selected {@color}"><strong><xsl:value-of select="Text" /></strong></td>
										</xsl:when>
										<xsl:when test="@has_checkbox='false'">
											<td class="an_radio_eval an_radio_selected {@color}"><strong><xsl:value-of select="$selected" /></strong></td>
										</xsl:when>
										<xsl:otherwise>
											<td class="an_radio_eval "><strong><xsl:value-of select="Text" /></strong></td>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:for-each>
							</tr>
					</tbody>
				</table>
			</xsl:for-each>
		</xsl:when>
		<xsl:when test="@type='textarea' and not(@parentQuestion)">
			<xsl:for-each select="Question">
			<xsl:variable name="column" select="@column" />
			<xsl:variable name="selected" select="php:function('selected', string($table), string($column))" />
				<p><xsl:value-of select="." /><xsl:value-of select="$selected" /></p>
			</xsl:for-each>
		</xsl:when>
		<xsl:when test="@type='an_radio'">
			<p><xsl:copy-of select="Question/node()" /></p>
			<table class="an_radio_table" border="1 solid">
				<xsl:variable name="cspan" select="count(ancestor::Set/Scale/Option)" />
				<tbody>
					<xsl:for-each select="Question">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					
					<xsl:variable name="column" select="@column" />
					<xsl:variable name="selected" select="php:function('selected', string($table), string($column))" />
						<tr>
							<xsl:for-each select="ancestor::Set/Scale/Option">
								<xsl:choose>
									<xsl:when test="Value=$selected">
										<td class="an_radio_eval an_radio_selected"><strong><xsl:value-of select="Value" /></strong></td>
									</xsl:when>
									<xsl:otherwise>
										<td class="an_radio_eval "><strong><xsl:value-of select="Value" /></strong></td>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</tr>
						<tr>
							<xsl:for-each select="ancestor::Set/Scale/Option">
								<xsl:choose>
									<xsl:when test="Value=$selected">
										<td class="an_radio_eval an_radio_selected"><strong><xsl:value-of select="Text" /></strong></td>
									</xsl:when>
									<xsl:otherwise>
										<td class="an_radio_eval "><strong><xsl:value-of select="Text" /></strong></td>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</tr>
					</xsl:for-each>
				</tbody>
			</table>
		</xsl:when>
		<xsl:when test="@type='mmm_slider'">
			<table class="mm_slider_table">
				<xsl:for-each select="Question">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />
					<xsl:variable name="selected" select="php:function('selected', string($table), string($column))" />
							<!--<xsl:value-of select="ancestor::Set/Scale/MinValue" /> - <strong><xsl:value-of select="$selected" /></strong> - 
							<xsl:value-of select="ancestor::Set/Scale/MaxValue" />-->
					<tr class="mm_slider_question">
						<td colspan="3">
							<xsl:copy-of select="Text/node()" />
							<input type="hidden" id="slider_value_{$directory_number}_{$file_number}_{$set_name}_{$position}" value="{$selected}" />
						</td>
					</tr>
					<tr>
						<td class="mm_slider_min"><xsl:value-of select="ancestor::Set/Scale/MinValue" /></td>
						<td class="mm_eval_container"><strong><xsl:value-of select="$selected" /></strong></td>
						<td class="mm_slider_max"><xsl:value-of select="ancestor::Set/Scale/MaxValue" /></td>
					</tr>
					<tr>
						<td class="mm_slider_min"><xsl:value-of select="Min" /></td>
						<td class="mm_slider_container"><div id="slider_{$directory_number}_{$file_number}_{$set_name}_{$position}" style="opacity: 1;"></div></td>
						<td class="mm_slider_max"><xsl:value-of select="Max" /></td>
					</tr>
				</xsl:for-each>
			</table>
		</xsl:when>
		<xsl:when test="@type='input'">
			<xsl:variable name="min" select="Scale/Min" />
			<xsl:variable name="max" select="Scale/Max" />
			<table class="text_table">
				<thead><tr><th colspan="2"><xsl:value-of select="Scale/Description" /></th></tr></thead>
				<xsl:for-each select="Question">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />
					<xsl:variable name="size" select="@size" />
					<tr>
						<td><xsl:copy-of select="node()" /></td>
						<td><xsl:value-of select="php:function('selected', string($table), string($column))" /></td>
					</tr>
				</xsl:for-each>
			</table>
		</xsl:when>
		<xsl:otherwise>
			<p>Fehler</p>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>