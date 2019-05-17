<div class="media bottom_spacer place_headline">
	<a class="pull-left" href="#">
		<img class="media-object" src="<?php echo base_url(); ?>/img/48x48/patient.png" data-src="holder.js/32x32">
	</a>
	<div class="media-body">
		<h1 class="media-heading">Admin Mail</h1> 	
	</div>
</div>

<ul class="breadcrumb">
	<li><a href="../dashboard">Dashboard</a></li>
	<li class="active">Admin Mail</li>
</ul>
<div class="col-md-12">

<div class="row">
    <ul class="nav nav-tabs">
        <li role="presentation"><a href="<?php echo site_url().'/admin/mail/index'?>">Neue Mail</a></li>
        <li role="presentation" class="active"><a href="<?php echo site_url().'/admin/mail/message_management'?>">Vorlagen verwalten</a></li>
        <li role="presentation"><a href="<?php echo site_url().'/user/patient/messages'?>">Interne Nachrichten</a></li>
    </ul>
    <br/>
</div>

    

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Angelegte Vorlagen</h4>
            </div>
            <div class="panel-body">
                <div class="col-md-6">
                <ul class="list-group">
                    <?php foreach ($messages as $message): ?>
                        <li class="list-group-item clearfix"><b><?php echo $message->subject; ?></b>
                        <div class="pull-right">
                            <button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#updateMessage<?php echo $message->id;?>"><span class="glyphicon glyphicon-pencil"></span></button>
                            <a class="btn btn-xs btn-danger" href="<?php echo site_url()?>/admin/mail/delete_message/<?php echo $message->id;?>"><span class="glyphicon glyphicon-remove"></span></a>
                        </div>
                        </li>
                        <div class="modal fade" id="updateMessage<?php echo $message->id;?>" tabindex="-1" role="dialog" aria-labelledby="updateMessageLabel<?php echo $message->id;?>">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="updateMessageLabel<?php echo $message->id;?>">Vorlage bearbeiten</h4>
                                </div>
                                <div class="modal-body">
                                    <?php echo form_open('admin/mail/update_message/'.$message->id, array('role'=>'form', 'method' => 'post')); ?>
                                        <div class="form-group">
                                            <label for="uMSubject">Betreff</label>
                                            <input type="text" class="form-control" id="uMSubject<?php echo $message->id;?>" name="subject" placeholder="Betreff" value="<?php echo $message->subject;?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="uMMessage">Nachricht</label>
                                            <textarea class="form-control" id="uMMessage<?php echo $message->id;?>" name="message" rows="15" placeholder="Nachricht"><?php echo $message->message;?></textarea>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                                    <button type="submit" class="btn btn-primary">Speichern</button>
                                </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </ul>
                </div>      
            </div>
            <div class="panel-footer">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newMessage"> Neue Vorlage anlegen </button>
            </div>
        </div>
    </div>

        
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="newMessage" tabindex="-1" role="dialog" aria-labelledby="newMessageLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="newMessageLabel">Neue Vorlage anlegen</h4>
      </div>
      <div class="modal-body">
        <?php echo form_open('admin/mail/save_message', array('role'=>'form', 'method' => 'post')); ?>
            <div class="form-group">
                <label for="nMSubject">Betreff</label>
                <input type="text" class="form-control" id="nMSubject" name="subject" placeholder="Betreff">
            </div>
            <div class="form-group">
                <label for="nMMessage">Nachricht</label>
                <textarea class="form-control" id="nMMessage" name="message" rows="15" placeholder="Nachricht"></textarea>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
        <button type="submit" class="btn btn-primary">Speichern</button>
      </div>
      </form>
    </div>
  </div>
</div>