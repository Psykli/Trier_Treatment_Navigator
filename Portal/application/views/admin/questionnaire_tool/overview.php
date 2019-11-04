<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragenbogen-Übungs-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool', 'Dashboard', array("class" => 'nav-link active') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung', array("class" => 'nav-link') ); ?></li>
			</ul>
		</div>
	</div>
	<br/><br/><br/>
	<div class="row">	
		<div class="col-sm-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">Information</h3>
				</div>
				<div class="card-body">
					<p>
						<h4>Bereich 1: Verwaltung der Patienten</h4>
						In diesem Bereich findet die Zuweisung der Übungen und der Fragebögen zu den Patienten statt.
						<ol>
							<li>
								<b><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung' ); ?></b><br />
								In diesem Tool können Sie für einzelnen Patienten Fragebögen freischalten und sehen auch welche Fragebögen für den Patienten freigeschaltet wurden. Zur Ansicht eines Patienten geben Sie den Patientencode vollständig oder teilweise ein. (Beispiel "Patient: 9999P99 oder 9999") Das gleiche Verfahren gilt auch für die Suche nach den Therapeuten, dabei werden dann alle Patienten, die diesem Therapeuten zugeteilt sind angezeigt. 
							</li>
							<br />
							<li>
								<b><?php echo anchor( 'admin/questionnaire_tool/uebungsverwaltung' , 'Übungsverwaltung' ); ?></b><br />
								In der Übungsverwaltung können Sie für Patienten, die keinem Therapeuten zugeordnet sind, Übungen freischalten. 
							</li>
							
						</ol>
						<hr/>
						<h4>Bereich 2: Verwaltung der Fragebögen</h4>
						In diesem Bereich findet die Erstellung von Fragebögen statt sowie die Verwaltung der Fragebogenbatterien
						<ol>
							<li>
								<b><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung' ); ?></b><br />
								In der Fragebogenverwaltung können Fragebögen hinzugefügt werden oder verändert werden
							</li>
							<br/>
							<li>
								<b><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung' ); ?></b><br />
								In der Batterieverwaltung können Fragebogenbatterien verwaltet und und bestehende betrachtet werden.
							</li>
						</ol>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>