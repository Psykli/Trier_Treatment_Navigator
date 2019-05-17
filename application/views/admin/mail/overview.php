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
        <li role="presentation" class="active"><a href="<?php echo site_url().'/admin/mail/index'?>">Neue Mail</a></li>
        <li role="presentation"><a href="<?php echo site_url().'/admin/mail/message_management'?>">Vorlagen verwalten</a></li>
        <li role="presentation"><a href="<?php echo site_url().'/admin/patient/messages'?>">Interne Nachrichten</a></li>
    </ul>
    <br/>
</div>

    <div class="row">
        <?php if(isset($mail_sent)):?>
            <?php if($mail_sent): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success alert-dismissable fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        Nachricht wurde versandt</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissable fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        Nachricht konnte nicht versandt werden</div>
                    </div>
                </div>
        <?php endif; endif; ?>
    </div>

    <div class="row">
        <div class="panel panel-default">
        
            <div class="panel-heading">
                <h4>Neue Mail verfassen</h4>
            </div>
            <div class="panel-body">
            <?php echo form_open('admin/mail/index/send_mail', array('role'=>'form', 'method' => 'post')); ?>
                <div class="form-group">
                    <label for="sender"> Absender </label>
                    <input class="form-control" id="sender" name="sender" type="email" value=""/>
                    <label for="receiver">Empfänger</label>
                    <br/>
                    <select class="contact_select form-control" multiple="multiple" id="receiver" name="receiver[]">
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user->email; ?>"><?php echo $user->FIRST_NAME.', '.$user->LAST_NAME.', '.$user->INITIALS.', '.$user->email; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="cc">Cc</label>
                    <select class="contact_select form-control" multiple="multiple" id="cc" name="cc[]">
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user->email; ?>"><?php echo $user->FIRST_NAME.', '.$user->LAST_NAME.', '.$user->INITIALS.', '.$user->email; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="bcc">Bcc</label>
                    <select class="contact_select form-control" multiple="multiple" id="bcc" name="bcc[]">
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user->email; ?>"><?php echo $user->FIRST_NAME.', '.$user->LAST_NAME.', '.$user->INITIALS.', '.$user->email; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <!--
                    <input type="text" class="form-control" id="receiver" name="receiver" placeholder="An">
                    <input type="text" class="form-control" id="cc" name="cc" placeholder="Cc">
                    <input type="text" class="form-control" id="bcc" name="bcc" placeholder="Bcc">
                    -->
                </div>
                <hr/>
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="templates" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Vorlagen
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="templates">
                        <?php foreach ($messages as $message): ?>
                            <li><a onclick='fillMessage(<?php echo json_encode($message->subject);?>,<?php echo json_encode($message->message);?>);'><?php echo $message->subject;?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <br/>

                <div class="form-group">
                    <label for="subject">Betreff</label>
                    <input type="text" class="form-control" id="subject" name="subject">
                </div>
                
                <div class="form-group">	
                    <label for="message">Nachricht</label>						
                    <textarea class="form-control" id="message" name="message" rows="15"></textarea>
                </div>
                <?php echo form_submit(array('class' => 'btn btn-primary'), 'Senden') ?>
            </div>
                
        </form>
        </div>
    </div>
</div>

<script>
    function fillMessage(subject,message){
       var input = $('#subject');
       input[0].value = subject;

       var textarea = $('#message');
       textarea[0].value = message;
    }

    $(document).ready(function() {
        $(".contact_select").select2({
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });
</script>