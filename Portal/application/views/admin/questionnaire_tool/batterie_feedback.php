<div class="container">
	<div class="row">
	
		<div class="col-sm-12">
			<h3>Fragebogen-Tool</h3>
			
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool', 'Dashboard', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/patientenverwaltung' , 'Patientenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/add_questionnaire' , 'Fragebogenverwaltung', array("class" => 'nav-link') ); ?></li>
				<li class="nav-item"><?php echo anchor( 'admin/questionnaire_tool/batterieverwaltung' , 'Fragebogenbatterieverwaltung', array("class" => 'nav-link active') ); ?></li>
			</ul>
		</div>
	</div>

    <br/><br/><br/>
	<div class="row">	
        <div class="col-sm-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">Batterie Feedback</h3>
				</div>
				<div class="card-body">
                    <div class="row">
                    <div class="col-md-8">
                        <ul style="min-height:10px;" class="list-group sortable">
                            <?php if(isset($feedback)):?>
                            <?php foreach($feedback as $f):?>
                                <li class="list-group-item" id="<?php echo 'item_'.$f->id;?>">
                                    <?php switch($f->type){
                                        case 'text':
                                            $output = 'Textelement: '.substr($f->data,0,25);
                                            if(strlen($f->data) > 25)
                                                $output .= '[...]';
                                            echo $output;
                                            break;
                                        case 'process':
                                            $output = 'Verlaufsgrafik(en) für: '.$f->data;
                                            echo $output;
                                            break;
                                        case 'review':
                                            $output = 'Auswertung für: '.$f->data;
                                            echo $output;
                                            break;
                                    }?>
                                    <div class="pull-right">
                                        <a class="btn btn-sm btn-danger" href="<?php echo site_url('admin/questionnaire_tool/feedback_remove_item/'.$f->id.'/'.$bid);?>">
                                        <i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </li>
                            <?php endforeach;?>
                                <?php endif;?>
                        </ul>
                        <button type="button" class="btn btn-outline-secondary" onclick="save_order()">Reihenfolge speichern</button>
                        <div id="save_info" class="alert alert-success" style="display:none;">
                            Reihenfolge gespeichert!
                        </div>
                    </div>

                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary form-control" id="add_text" data-toggle="modal" data-target="#text_modal">Textelement hinzufügen</button>
                        <button class="btn btn-outline-secondary form-control" id="add_process" data-toggle="modal" data-target="#process_modal">Verlauf hinzufügen</button>
                        <button class="btn btn-outline-secondary form-control" id="add_review" data-toggle="modal" data-target="#review_modal">Auswertung hinzufügen</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo form_open( 'admin/questionnaire_tool/feedback_add_text/'.$bid, array( 'role' => 'form' ) ); ?>
    <div class="modal fade" id="text_modal" tabindex="-1" role="dialog" aria-labelledby="text_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="text_modal_label">Textelement hinzufügen</h4>
            </div>
            <div class="modal-body">
                <label for="textelement" class="form-control-label">Inhalt des Textelement:</label>
                <textarea class="form-control" id="textelement" name="textelement"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schließen</button>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    </form>

    <?php echo form_open( 'admin/questionnaire_tool/feedback_add_process/'.$bid, array( 'role' => 'form' ) ); ?>
    <div class="modal fade" id="process_modal" tabindex="-1" role="dialog" aria-labelledby="process_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="process_modal_label">Verlauf hinzufügen</h4>
            </div>
            <div class="modal-body">
                <label for="process" class="form-control-label">Fragebogen des Verlaufs:</label>
                <select class="form-control" id="process" name="process">
                    <?php $done = '';?>
                    <?php foreach($batterie as $quest):?>
                        <?php $process = $process_info[$quest->tablename];
                        foreach($process as $p):
                            if(strpos($done,$p->name) === FALSE):?>
                                <option value="<?php echo $p->name;?>"><?php echo $p->name;?></option>
                                <?php $done .= $p->name;?>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schließen</button>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    </form>

    <?php echo form_open( 'admin/questionnaire_tool/feedback_add_review/'.$bid, array( 'role' => 'form' ) ); ?>
    <div class="modal fade" id="review_modal" tabindex="-1" role="dialog" aria-labelledby="review_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="review_modal_label">Auswertung hinzufügen</h4>
            </div>
            <div class="modal-body">
                <label for="review" class="form-control-label">Fragebogen der Auswertung:</label>
                <select class="form-control" id="review" name="review">
                    <?php foreach($batterie as $quest):?>
                        <option value="<?php echo $quest->tablename;?>"><?php echo $quest->header_name[0];?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Schließen</button>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    </form>

    <script>

        function save_order(){
            var data = $('.sortable').sortable('serialize', {key: 'order'});    

            var csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';

            // POST to server using $.post or $.ajax
            $.ajax({
                data: {order: data, csrf_test_name: csrf_token},
                type: 'POST',
                url: '<?php echo site_url(); ?>/admin/questionnaire_tool/feedback_save_order/<?php echo $bid;?>', 
                success: function() {
                    $('#save_order').removeClass('disabled');
                    $('#save_info').fadeIn(400).delay(1500).fadeOut(400);
                }         
            });
        }

        $(document).ready(function() {     

            $('.sortable').sortable({
                axis: 'y',
            });
        });
    </script>