</div>
<div class="quest_container container">
	<div class="row">
		<div class="col-sm-12">
		<?php
			$language = "de";
			$filenumber = 0;
			libxml_use_internal_errors(true);
			//$action = count($all_questionnaires == 1) ? base_url() . "/index.php/de/patient/patient/index/questionnaire" : "";
			$action = base_url() . "index.php/".$language."/patient/questionnaire/send_questionnaire/" . $entry->id . "/" . $entry->instance;
			echo form_open($action,array('id' => 'questionnaire', 'method' => 'post'));

			$xml = new DOMDocument();
			$doc = 'application/views/patient/questionnaire/bows/'.$questionnaire[0]->filename;
			$parsed = $xml->load($doc);
			
			if(!$parsed){
				$errors = libxml_get_errors();
				var_dump($errors);
			}
			
			$xsl = new DOMDocument();
			$xsl->load($xsl_file);

			$proc = new XSLTProcessor();
			$proc->importStyleSheet($xsl);
			$proc->setParameter('', 'file_number', $filenumber);
			$proc->setParameter('', 'last_sb_instance', $last_sb_instance);
			$proc->setParameter('', 'language', $language);

			if ($endTherapy <= 0) {				
				$proc->setParameter('', 'endTherapy', 0);
			} else {
				$proc->setParameter('', 'endTherapy', 1);
			}

			echo $proc->transformToXML($xml);
			echo "</form>";
			//break;
		?>
        </div>
	</div>
</div>