<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li><?php echo anchor( 'admin/questionnaire_tool', 'Startseite' ); ?></li>
				<li class="active"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung' ); ?></li>
				<li><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung' ); ?></li>
                <li><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung' ); ?></li>
				
			</ul>
		</div>
	</div>
    <br/><br/><br/>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Fragebogen</h3>
			</div>
			<div class="panel-body">
			<?php 
					$_GET['tables'] = $tables;
					function selected($table, $item) {
						foreach($_GET['tables'] as $key => $value) {
							if(strcmp($key, $item) == 0)
								return $value;
						}
						return 'Fehler'; //besserer Umgang mit Fehlern?
					}
				?>
				<?php $directory_number = 0; ?>
				<?php 
					if(isset($xml_directories['xml'])){
						$file = $xml_directories['xml'];
						$name = $xml_directories['name']; 
						++$directory_number;
						//$content = simplexml_load_file($file);
						$reader = new XMLReader;
						$reader->open($file);
						while($reader->read() && strcmp($reader->name, "Questionnaire") != 0);
						$table = $reader->getAttribute('table'); //(string) $content['table'];
						$max_instance = intval($instances[$table][0]->INSTANCE);	
						while($reader->read() && strcmp($reader->name, "Headline") != 0);
						$alternate = $reader->getAttribute('alternate'); //$content->Headline['alternate'];
						
						if(strlen($name) > 0) { echo '<div style="text-align: center"><h1>' . $name. '</h1></div>'; }
						$xml = new DOMDocument();
						$xml->load($file);
						$xsl = new DOMDocument();
						$xsl->load($evaluationXSL);
						$proc = new XSLTProcessor();
						$proc->registerPHPFunctions();
						$proc->importStyleSheet($xsl);
						$proc->setParameter('', 'eval_everything', true);
						$proc->setParameter('', 'directory_number', $directory_number);
						$proc->setParameter('', 'file_number', $directory_number);
						$proc->setParameter('', 'exists', 1);
						$proc->setParameter('', 'language', 'de');
						echo $proc->transformToXML($xml);
						echo "<hr />";
						
					}

					?>
			</div>
		</div><!-- /.panel panel-default -->
	</div><!-- /.col-md-6 -->
