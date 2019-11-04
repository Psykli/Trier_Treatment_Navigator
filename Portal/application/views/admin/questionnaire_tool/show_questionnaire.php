<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool', 'Dashboard', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung', array("class" => 'nav-link active') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung', array("class" => 'nav-link') ); ?></li>
			</ul>
		</div>
	</div>
    <br/><br/><br/>
<div class="row">
	<div class="col-md-12">
		<div class="card ">
			<div class="card-header">
				<h3 class="card-title">Fragebogen</h3>
			</div>
			<div class="card-body">
			<?php 
					function selected($table, $item) {
						foreach($tables as $key => $value) {
							if($key === $item)
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
						while($reader->read() && $reader->name !== "Questionnaire");
						$table = $reader->getAttribute('table'); //(string) $content['table'];
						$max_instance = intval($instances[$table][0]->INSTANCE);	
						while($reader->read() && $reader->name !== "Headline");
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
		</div><!-- /.card  -->
	</div><!-- /.col-md-6 -->
