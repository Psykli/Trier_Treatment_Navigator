<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" indent="yes" />

<xsl:template match="Questionnaire">
<xsl:for-each select="Language">
<xsl:if test="$language = @lang">
	<main role="main">
		<article>
			<xsl:variable name="table" select="ancestor::Questionnaire/@table" />
			<xsl:variable name="date_column" select="ancestor::Questionnaire/@date" />
			<xsl:variable name="med_column" select="ancestor::Questionnaire/@med" />
			<xsl:variable name="ver_column" select="ancestor::Questionnaire/@ver" />
			<input type="hidden" name="table" value="{$table}" />
			<input type="hidden" name="date_column" value="{$date_column}" />
			<input type="hidden" name="med_column" value="{$med_column}" />
			<input type="hidden" name="ver_column" value="{$ver_column}" />
			<div id="error_container"></div>
			<xsl:if test="count(Headline) &gt; 0">
				<header><h1><xsl:value-of select="Headline" /></h1></header>
			</xsl:if>
			<xsl:if test="count(Info) &gt; 0">
				<section class="description" style="margin-top:.2in;"><p><xsl:copy-of select="Info" /></p>
					<!--<p><strong>Bitte beantworten Sie jede Frage!</strong></p>-->
				</section>
			</xsl:if>
			<xsl:for-each select="Set">
				<section>
					<xsl:if test="count(Caption) &gt; 0 and not(@parentQuestion)"><h3><xsl:value-of select="Caption" /></h3></xsl:if>
					<xsl:if test="count(Description) &gt; 0 and not(@parentQuestion)"><p><xsl:copy-of select="Description" /></p></xsl:if>
					<xsl:apply-templates select="."></xsl:apply-templates></section>
			</xsl:for-each>
		</article>
		<div style="text-align:center;padding:.3in;"><input type="submit" value="Eintragen" name="submit_button_{$file_number}" onclick="return checkAria()"/></div>
	</main>
	<script>
		<xsl:for-each select="Set">
			<xsl:if test="@type='slider' or @type='mm_slider' or @type='mmm_slider'">
				<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)"></xsl:variable>
				<xsl:for-each select="Question">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
						var temp = "_<xsl:value-of select='$file_number' />_<xsl:value-of select='$set_name' />_<xsl:value-of select='$position' />";
						var f = function(event, ui) {
							var temp1 = "_<xsl:value-of select='$file_number' />_<xsl:value-of select='$set_name' />_<xsl:value-of select='$position' />";
							jQuery("#slider_value"+temp1).val(ui.value);
						}
						var s = function(event, ui) {
							var temp1 = "_<xsl:value-of select='$file_number' />_<xsl:value-of select='$set_name' />_<xsl:value-of select='$position' />";
							jQuery('#slider' + temp1 + ' .ui-slider-handle').show();
							jQuery('#slider_value' + temp1 + '-error').hide().removeClass("error");
						}
						$("#slider" + temp).slider({
							/*value: (<xsl:value-of select="number(ancestor::Set/Scale/MaxValue)" /> - <xsl:value-of select="number(ancestor::Set/Scale/MinValue)" />) / 2,*/
							
							slide: f,
							start: s,
							range: "min",
							value: -1,
							min: <xsl:value-of select="ancestor::Set/Scale/MinValue" />,
							max: <xsl:value-of select="ancestor::Set/Scale/MaxValue" />,
						});
						jQuery('#slider'+temp+' .ui-slider-handle').hide();
						//jQuery("#slider_value"+temp).val(jQuery("#slider"+temp).slider("values", 0));
						jQuery("#slider_value" + temp).val(<xsl:value-of select="ancestor::Set/Scale/MinValue" />-1);
				</xsl:for-each>
			</xsl:if>
		</xsl:for-each>	
		
		jQuery(function() {
			jQuery(".radio_buttonset").buttonset();
			jQuery(".an_radio_buttonset").buttonset();
		});
		
		function checkAria() {
			jQuery("label[aria-pressed='true']").each(function(i, e) { jQuery( "#" + e.getAttribute("for")).prop("checked", true) ; });
		}
		
		jQuery.extend(jQuery.validator.messages, {
			required: "Bitte beantworten Sie alle Fragen.",
			max: "Bitte geben Sie eine Zahl kleiner oder gleich {0} ein.",
			min: "Bitte geben Sie eine Zahl größer oder gleich {0} ein."
		});
		
		
		$.validator.setDefaults({
			ignore: [],
		});
		
		jQuery('#questionnaire').validate({
			rules: {
				<xsl:for-each select="Set">
					<xsl:choose>
					<xsl:when test="@type='radio' and not(@parentQuestion)">
						<xsl:for-each select="Question">
							<xsl:if test="not(@optional='true') and not(@parentQuestion)">
								<xsl:value-of select="@column" />: { 
									required: true
								}, 
							</xsl:if>
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="@type='an_radio' and not(@parentQuestion)">
						<xsl:for-each select="Question">
							<xsl:value-of select="@column" />: { required: true },
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="@type='mm_slider' and not(@parentQuestion)">
						<xsl:for-each select="Question">
							<xsl:value-of select="@column" />: { required: true, min: <xsl:value-of select="ancestor::Set/Scale/MinValue" /> },
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="@type='mmm_slider' and not(@parentQuestion)">
						<xsl:for-each select="Question">
							<xsl:value-of select="@column" />: { required: true, min: <xsl:value-of select="ancestor::Set/Scale/MinValue" /> },
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="@type='input' and not(@parentQuestion)">
						<xsl:for-each select="Question" >
							<xsl:if test="not(@optional='true') and not(@parentQuestion)">
								<xsl:value-of select="@column" />: {
								required: true,
								min: <xsl:value-of select="ancestor::Set/Scale/Min" />,
								max: <xsl:value-of select="ancestor::Set/Scale/Max" /> },
							</xsl:if>
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="@type='check_t' and not(@parentQuestion)">
						<xsl:for-each select="Question">
						<xsl:if test="not(@parentQuestion)">
							<xsl:for-each select="ancestor::Set/Scale/Option">
								<xsl:if test="not(@optional='true')">
									<xsl:value-of select="@column" />: { required: true },
								</xsl:if>
							</xsl:for-each>
						</xsl:if>
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="@type='textarea' and not(@parentQuestion)">
						<xsl:for-each select="Question">
						<xsl:if test="not(@optional='true') and not(@parentQuestion)">
							<xsl:value-of select="@column" />: { required: true },
						</xsl:if>
						</xsl:for-each>
					</xsl:when>
					<xsl:when test="@type='radio_head_foot' and not(@parentQuestion)">
						<xsl:for-each select="Question">
						<xsl:if test="not(@optional='true') and not(@parentQuestion)">
							<xsl:value-of select="@column" />: { required: true },
						</xsl:if>
						</xsl:for-each>
					</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			},
			<xsl:if test="Set/@type='radio' or Set/@type='an_radio'">
			errorPlacement: function(error, element) {
				error.insertBefore(element.parent().parent());
			}
			</xsl:if>
		});
	</script>
</xsl:if>
</xsl:for-each>
</xsl:template>

<xsl:template match="Set">
	<p>Fehler: ungültiger Fragentyp</p>
</xsl:template>

<xsl:template match="Set[@type='hidden_instance']" name="i_hidden_instance">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />	
	<xsl:variable name="column" select="@column" />	
	<input type="hidden" name="{$column}" value="{$last_sb_instance}"/>
</xsl:template>

<xsl:template match="Set[@type='radio']" name="i_radio">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />	
	<table class="radio_table">
			<thead>
				<tr>
					<th class="radio_question_header">
					</th>
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
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />
					<xsl:variable name="even_odd" select="$position mod 2" />
					<tr class="qt_{$even_odd} radio_buttonset">
						<td class="radio_question">
							<xsl:if test="ancestor::Set/@enumerate='yes'">
								<xsl:value-of select="format-number(number($position), '00')" />. 
							</xsl:if>
							<xsl:copy-of select="node()" />
						</td>
						<xsl:for-each select="ancestor::Set/Scale/Option">
							<xsl:variable name="val" select="Value" />
							<xsl:variable name="rid" select="1+count(preceding-sibling::Option)" />
							<td class="radio_container">
								<input type="radio" name="{$column}" value="{$val}" id="radio_button_{$file_number}_{$set_name}_{$position}_{$rid}"/>
								<label class="radio_label" for="radio_button_{$file_number}_{$set_name}_{$position}_{$rid}">
									<xsl:value-of select="Value" />
								</label>
							</td>
							
						</xsl:for-each>
					</tr>
				</xsl:for-each>
			</tbody>
			</table>
</xsl:template>

<xsl:template match="Set[@type='radio_head_foot']" name="i_radio_head_foot">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />	
	<table class="radio_table">
			<thead>
				<tr>
					<th>
					</th>
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
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />
					<xsl:variable name="even_odd" select="$position mod 2" />
					<tr class="qt_{$even_odd} radio_buttonset">
						<td class="radio_question" style="text-align: right;">
							<xsl:if test="ancestor::Set/@enumerate='yes'">
								<xsl:value-of select="format-number(number($position), '00')" />. 
							</xsl:if>
							<xsl:copy-of select="Head/node()" />
						</td>
						<xsl:for-each select="ancestor::Set/Scale/Option">
							<xsl:variable name="val" select="Value" />
							<xsl:variable name="rid" select="1+count(preceding-sibling::Option)" />
							<td class="radio_container">
								<input type="radio" name="{$column}" value="{$val}" id="radio_button_{$file_number}_{$set_name}_{$position}_{$rid}"/>
								<label class="radio_label" for="radio_button_{$file_number}_{$set_name}_{$position}_{$rid}">
									<xsl:value-of select="Value" />
								</label>
							</td>
							
						</xsl:for-each>
						<xsl:if test="count(Foot) &gt; 0">
							<td class="radio_question">
							<xsl:copy-of select="Foot/node()" />
							</td>
						</xsl:if>
					</tr>
				</xsl:for-each>
			</tbody>
			</table>
</xsl:template>

<xsl:template match="Set[@type='check_t']" name="i_check_t">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />	
	<table class="radio_table">
			<thead>
				<tr>
					<th class="radio_question_header">
					</th>
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
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="even_odd" select="$position mod 2" />
					<tr class="qt_{$even_odd} an_radio_buttonset">
						<td class="radio_question">
							<xsl:if test="ancestor::Set/@enumerate='yes'">
								<xsl:value-of select="format-number(number($position), '00')" />. 
							</xsl:if>
							<xsl:copy-of select="node()" />
						</td>
						<xsl:for-each select="ancestor::Set/Scale/Option">
							<xsl:variable name="val" select="Value" />
							<xsl:variable name="rid" select="1+count(preceding-sibling::Option)" />
							<xsl:variable name="column" select="@column" />
							<xsl:variable name="has_checkbox" select="@has_checkbox" />
							<xsl:variable name="has_text" select="@has_text" />
							<td class="radio_container">
								<xsl:if test="@has_checkbox = 'true' and not(@has_text = 'true')">
									<input type="hidden" value="-9999" name="{$column}"/>
									<input type="checkbox" name="{$column}" value="1" id="checkbox_button_{$file_number}_{$set_name}_{$position}_{$rid}"/>		
									<label class="check_label" for="checkbox_button_{$file_number}_{$set_name}_{$position}_{$rid}">
									<xsl:value-of select="Value" />
									</label>
								</xsl:if>	
								<xsl:if test="@has_checkbox = 'true' and @has_text = 'true'">
									<input type="checkbox" value="1" id="checkbox_button_{$file_number}_{$set_name}_{$position}_{$rid}"/>	
									<label class="radio_label" for="checkbox_button_{$file_number}_{$set_name}_{$position}_{$rid}">
									<xsl:value-of select="Value" />
								</label>	
								</xsl:if>	

								
								<xsl:if test="@has_text = 'true'">
									<input type="text" name="{$column}"  id="checkbox_text_{$file_number}_{$set_name}_{$position}_{$rid}"/>
								</xsl:if>
							</td>
						</xsl:for-each>
					</tr>
				</xsl:for-each>
			</tbody>
			</table>
</xsl:template>

<xsl:template match="Set[@type='slider']">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />
		<table class="slider_table">
			<thead>
				<tr>
					<th class="slider_question_header"></th>
					<th class="slider_slider_header">
						<span class="slider_scale_min"><xsl:copy-of select="Scale/Min" /></span>
						<span class="slider_scale_max"><xsl:copy-of select="Scale/Max" /></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="Question">
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
					<xsl:variable name="column" select="@column" />				
					<xsl:variable name="even_odd" select="$position mod 2" />					
					<tr class="qt_{$even_odd}">
						<td class="slider_question">
							<input type="hidden" id="slider_value_{$file_number}_{$set_name}_{$position}" name="{$column}" />
							<xsl:if test="ancestor::Set/@enumerate='yes'">
								<xsl:value-of select="format-number(number($position), '00')" />. 
							</xsl:if>
							<xsl:copy-of select="node()" />
						</td>
						<td class="slider_container"><div id="slider_{$file_number}_{$set_name}_{$position}"></div></td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
</xsl:template>

<xsl:template match="Set[@type='an_radio']" name ="i_an_radio">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />
	<xsl:variable name="columntest" select="Question/@column" />
			<!--begin:Extrawurst:ENDTHERAPY-->
			<xsl:if test="(not($columntest = 'ETS011') and not($columntest = 'ETN011')) or $endTherapy = '0'">
			<!--end:Extrawurst:ENDTHERAPY-->
			<table class="an_radio_table" id="set_{$set_name}">
				<xsl:variable name="cspan" select="count(ancestor::Set/Scale/Option)" />
				<tbody>
					<xsl:for-each select="Question">
					<xsl:variable name="column" select="@column" />
					<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
						<!--<tr>
							<td class="an_radio_question" colspan="{$cspan}">
								
							</td>
						</tr>-->
						<p class="an_radio_question"><xsl:copy-of select="node()" /></p>
						<tr class="an_radio_buttonset">
							<xsl:for-each select="ancestor::Set/Scale/Option">
							<xsl:variable name="rid" select="1+count(preceding-sibling::Option)" />
							<xsl:variable name="val" select="Value" />
							<xsl:variable name="even_odd" select="1+count(preceding-sibling::Option) mod 2" />
								<td class="qc_{$even_odd}">
								<!--begin:Extrawurst:ENDTHERAPY-->
									<xsl:choose>
									<xsl:when test="count(Dialog) &gt; 0 and $endTherapy = '0'">
										<xsl:variable name="dialogID" select="Dialog/@id" />
										<xsl:variable name="dialog" select="Dialog" />
										<input type="radio" name="{$column}" id="an_radio_button_{$file_number}_{$set_name}_{$position}_{$rid}" value="{$val}" class="{$dialogID}"
										onclick="showDialogue('{$file_number}_{$set_name}_{$position}_{$rid}', 'set_{$set_name}')" />
										<script>		
											function showDialogue(id, set) {
												var dlID = "dialog_" + id;
												jQuery("#" + dlID).dialog({
													modal:true,
													width: 500,
													buttons: {
														OK: {
														click: function() {
															jQuery(this).dialog("close");
														},
														text: 'OK',
														style: ";margin-right:auto;margin-left:auto;"
														}
													}
												});
											}
										</script>
										<div style="display:none;" id="dialog_{$file_number}_{$set_name}_{$position}_{$rid}"><p><xsl:copy-of select="Dialog/node()"></xsl:copy-of></p></div>
									</xsl:when>
									<xsl:otherwise>
										<input type="radio" name="{$column}" id="an_radio_button_{$file_number}_{$set_name}_{$position}_{$rid}" value="{$val}" />
									</xsl:otherwise>
									<!--end:Extrawurst:ENDTHERAPY-->
									</xsl:choose>
									<label class="an_radio_label" for="an_radio_button_{$file_number}_{$set_name}_{$position}_{$rid}">
										<xsl:value-of select="Text" />
									</label>
								</td>
							</xsl:for-each>
						</tr>
						<!--OK:-->
						<!--<tr>
							<xsl:for-each select="ancestor::Set/Scale/Option">
								<xsl:variable name="even_odd" select="1+count(preceding-sibling::Option) mod 2" />
								<td class="an_radio_option qc_{$even_odd}">
									<xsl:value-of select="." />
								</td>
							</xsl:for-each>
						</tr>
						<tr>
							<xsl:for-each select="ancestor::Set/Scale/Option">
								<xsl:variable name="even_odd" select="1+count(preceding-sibling::Option) mod 2" />
								<td class="an_radio_container qc_{$even_odd}">
									<input type="radio" name="group_{$file_number}_{$set_name}_{$position}" />
								</td>
							</xsl:for-each>
						</tr>
						-->
					</xsl:for-each>
				</tbody>
			</table>
			<!--begin:Extrawurst:ENDTHERAPY-->
			</xsl:if>
			<!--end:Extrawurst:ENDTHERAPY-->
</xsl:template>

<xsl:template match="Set[@type='mm_slider']" name="i_mm_slider">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />
		<table class="mm_slider_table">
			<xsl:for-each select="Question">
				<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
				<xsl:variable name="column" select="@column" />
				<tr class="mm_slider_question">
					<td colspan="3">
						<xsl:copy-of select="node()" />
					</td>
				</tr>
				<tr>
					<td class="mm_slider_min"><xsl:copy-of select="ancestor::Set/Scale/Min" />
						<input type="hidden" id="slider_value_{$file_number}_{$set_name}_{$position}" name="{$column}" /></td>
					<td class="mm_slider_container"><div id="slider_{$file_number}_{$set_name}_{$position}"></div></td>
					<td class="mm_slider_max"><xsl:copy-of select="ancestor::Set/Scale/Max" /></td>
				</tr>
			</xsl:for-each>
		</table>
</xsl:template>

<xsl:template match="Set[@type='mmm_slider']" name="i_mmm_slider">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />
	<table class="mm_slider_table">
		<xsl:for-each select="Question">
			<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
			<xsl:variable name="column" select="@column" />
			<tr class="mm_slider_question">
				<td colspan="3">
					<xsl:copy-of select="Text/node()" />
				</td>
			</tr>
			<tr>
				<td class="mm_slider_min"><xsl:copy-of select="Min" />
					<input type="hidden" id="slider_value_{$file_number}_{$set_name}_{$position}" name="{$column}" /></td>
				<td class="mm_slider_container"><div id="slider_{$file_number}_{$set_name}_{$position}"></div></td>
				<td class="mm_slider_max"><xsl:copy-of select="Max" /></td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>

<xsl:template match="Set[@type='input']" name="i_input">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />
	<xsl:variable name="min" select="Scale/Min" />
	<xsl:variable name="max" select="Scale/Max" />
	<table class="text_table">
		<thead>
			<tr>
				<th colspan="2" class="text_scale_description"><xsl:copy-of select="Scale/Description/node()" />
					<xsl:if test="not(@popover = 'false')">
						<span class="glyphicon glyphicon-info-sign" rel="popover" data-popover-content="#myPopover"></span>
						<script>
							$(function(){
								$('[rel="popover"]').popover({
									container: 'body',
									html: true,
									content: function () {
										var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
										return clone;
									}
								}).click(function(e) {
									e.preventDefault();
								});
							});
						</script>
						<div id="myPopover" class="hide" style="				
						position: absolute;
						top: 0;
						left: 0;
						z-index: 1010;
						width: 600px;
						max-width: 1000px;
						padding: 1px;
						text-align: left;
						white-space: normal;
						background-color: #ffffff;
						border: 1px solid #ccc;
						border: 1px solid rgba(0, 0, 0, 0.2);
						-webkit-border-radius: 6px;
							-moz-border-radius: 6px;
								border-radius: 6px;
						-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
							-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
								box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
						-webkit-background-clip: padding-box;
							-moz-background-clip: padding;
								background-clip: padding-box;
						">
							<b>Code (Benutzen Sie auch entsprechende Zwischenwerte, z. B: 45, 68, 72)</b>
							<ul>
								<li><b>100-91</b> Hervorragende Leistungsfähigkeit in einem breiten Spektrum von Aktivitäten;
								Schwierigkeiten im Leben scheinen nie außer Kontrolle zu geraten; keine
								Symptome.</li>

								<li><b>90-81</b> Keine oder nur minimale Symptome (z.B. leicht Angst vor einer Prüfung), gute
								Leistungsfähigkeit in allen Gebieten, interessiert und eingebunden in ein
								breites Spektrum von Aktivitäten, sozial effektiv im Verhalten, im allgemein
								zufrieden mit dem Leben, übliche Alltagsprobleme oder -sorgen (z.B. nur
								gelegentlicher Streit mit einem Familienmitglied).</li>

								<li><b>80-71</b> Wenn Symptome vorliegen, sind dies vorübergehende oder zu erwartende
								Reaktionen auf psychosoziale Belastungsfaktoren (z.B.
								Konzentrationsschwierigkeiten nach einem Familienstreit); höchstens leichte
								Beeinträchtigung der sozialen beruflichen und schulischen Leistungsfähigkeit
								(z. B. zeitweises Zurückbleiben in der Schule).</li>

								<li><b>70-61</b> Einige leichte Symptome (z.B. depressive Stimmung oder leichte
								Schlaflosigkeit ODER einige leichte Schwierigkeiten hinsichtlich der sozialen,
								beruflichen oder schulischen Leistungsfähigkeit (z. B. gelegentliches
								Schuleschwänzen oder Diebstahl im Haushalt), aber im allgemeinen relativ
								gute Leistungsfähigkeit, hat einige wichtige zwischenmenschliche
								Beziehungen.</li>

								<li><b>60-51</b> Mäßig ausgeprägte Symptome (z.B. Affektverflachung, weitschweifige
								Sprache, gelegentliche Panikattacken) ODER mäßig ausgeprägte
								Schwierigkeiten bezüglich der sozialen, beruflichen oder schulischen
								Leistungsfähigkeit (z. B wenige Freunde, Konflikte mit Arbeitskollegen,
								Schulkameraden oder Bezugspersonen).</li>

								<li><b>50-41</b> Ernste Symptome (z. B Suizidgedanken, schwere Zwangsrituale, häufige
								Ladendiebstähle) ODER eine Beeinträchtigung der sozialen, beruflichen und
								schulischen Leistungsfähigkeit (z. B. keine Freunde, Unfähigkeit, eine
								Arbeitsstelle zu behalten).</li>

								<li><b>40-31</b> Einige Beeinträchtigungen in der Realitätskontrolle oder der Kommunikation
								(z. B. Sprache zeitweise unlogisch, unverständlich oder belanglos) ODER
								starke Beeinträchtigung in mehreren Bereichen, z B. Arbeit oder Schule,
								familiäre Beziehungen, Urteilsvermögen, Denken oder Stimmung (z. B. ein
								Mann mit einer Depression vermeidet Freunde, vernachlässigt seine Familie
								und ist unfähig zu arbeiten; eine Kind schlägt häufig jüngere Kinder, ist zu
								Hause trotzig und versagt in der Schule).</li>

								<li><b>30-21</b> Das Verhalten ist ernsthaft durch Wahnphänomene oder Halluzinationen
								beeinflusst ODER ernsthafte Beeinträchtigung der Kommunikation und des
								Urteilsvermögens (z.B. manchmal inkohärent, handelt grob inadäquat, starkes
								Eingenommensein von Selbstmordgedanken ODER Leistungsunfähigkeit in
								fast alles Bereichen (z. B. bleibt den ganzen Tag im Bett, hat keine Arbeit,
								Kein Zuhause und keine Freunde).</li>

								<li><b>20-11</b> Selbst- und Fremdgefährdung (z. B. Selbstmordversuche ohne eindeutige
								Todesabsicht, häufig gewalttätig, manische Erregung) ODER ist gelegentlich
								nicht in der Lage, die geringste Hygiene aufrechtzuerhalten (z. B. schmiert mit
								Kot) ODER grobe Beeinträchtigung der Kommunikation (größtenteils
								inkohärent oder stumm).</li>

								<li><b>10-1</b> Ständige Gefahr, sich oder andere schwer zu verletzen (z. B. wiederholte
								Gewaltanwendung) ODER anhaltende Unfähigkeit, die minimale persönliche
								Hygiene aufrechtzuerhalten ODER ernsthafter Selbstmordversuch mit
								eindeutiger Todesabsicht.</li>

								<li><b>0</b> Unzureichende Informationen</li>
							</ul>
						</div>
					</xsl:if>
				</th>
			</tr>
		</thead>
		<xsl:for-each select="Question">
			<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
			<xsl:variable name="column" select="@column" />
			<xsl:variable name="size" select="@size" />
			<xsl:variable name="maxlength" select="@maxlength" />
			<tr>
				<td class="text_question"><xsl:copy-of select="node()" /></td>
				<td><input type="text" id="text_{$file_number}_[$set_name]_{$position}" name="{$column}" size="{$size}" maxlength="{$maxlength}" /></td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>

<xsl:template match="Set[@type='textarea']" name="i_textarea">
	<xsl:variable name="set_name" select="1+count(preceding-sibling::Set)" />
	<xsl:variable name="rows" select="Scale/Rows" />
	<xsl:variable name="cols" select="Scale/Cols" />
	<table class="text_table">
		<thead>
			<tr>
				<th colspan="2" class="text_scale_description"><xsl:copy-of select="Scale/Description/node()" /></th>
			</tr>
		</thead>
		<xsl:for-each select="Question">
			<xsl:variable name="position" select="1+count(preceding-sibling::Question)" />
			<xsl:variable name="column" select="@column" />
			<xsl:variable name="size" select="@size" />
			<xsl:variable name="maxlength" select="@maxlength" />
			<tr>
				<td class="text_question"><xsl:copy-of select="node()" /></td>
				<td><textarea type="text" id="text_{$file_number}_[$set_name]_{$position}" name="{$column}" rows="{$rows}" cols="{$cols}" ></textarea></td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>

<xsl:template match="Set[@parentQuestion]">
	<xsl:variable name="parentQuestion" select="@parentQuestion"></xsl:variable>
	<xsl:variable name="hidden_number" select="1+count(preceding-sibling::Set[@parentQuestion])"></xsl:variable>
	<script>	
		$('input[name=<xsl:value-of select="$parentQuestion"></xsl:value-of>]').change(function () {
			if ( 
			<xsl:for-each select="Trigger">
			<xsl:variable name="triggervalue" select="." />
				$(this).val() == '<xsl:value-of select="$triggervalue" />' ||
			</xsl:for-each>
				false
			) {
				$("#hidden<xsl:value-of select='$hidden_number' />").show();
			} else {
				$("#hidden<xsl:value-of select='$hidden_number' /> input:radio").prop('checked', false);
				$("#hidden<xsl:value-of select='$hidden_number' /> input:checkbox").prop('checked', false);
				jQuery("#hidden<xsl:value-of select='$hidden_number' /> .radio_buttonset").buttonset("refresh");
				$("#hidden<xsl:value-of select='$hidden_number' />").hide();
			}
		});
	</script>
	<div id="hidden{$hidden_number}" style="display:none" class="hideThis">
		<xsl:if test="count(Caption) &gt; 0"><h3><xsl:value-of select="Caption" /></h3></xsl:if>
		<xsl:if test="count(Description) &gt; 0"><p><xsl:copy-of select="Description" /></p></xsl:if>
		<xsl:choose>
			<xsl:when test="@type='radio'"><xsl:call-template select="." name="i_radio"></xsl:call-template></xsl:when>
			<xsl:when test="@type='input'"><xsl:call-template select="." name="i_input"></xsl:call-template></xsl:when>
			<xsl:when test="@type='mmm_slider'"><xsl:call-template select="." name="i_mmm_slider"></xsl:call-template></xsl:when>
			<xsl:when test="@type='mm_slider'"><xsl:call-template select="." name="i_mm_slider"></xsl:call-template></xsl:when>
			<xsl:when test="@type='an_radio'"><xsl:call-template select="." name="i_an_radio"></xsl:call-template></xsl:when>
			<xsl:when test="@type='check_t'"><xsl:call-template select="." name="i_check_t"></xsl:call-template></xsl:when>
			<xsl:when test="@type='radio_head_foot'"><xsl:call-template select="." name="i_radio_head_foot"></xsl:call-template></xsl:when>
			<xsl:when test="@type='textarea'"><xsl:call-template select="." name="i_textarea"></xsl:call-template></xsl:when>
		</xsl:choose>
	</div>
</xsl:template>
</xsl:stylesheet>

