<?php
/**
 * Created by Emrul.
 * User: Wittylab
 * Date: 24-Jan-18
 * Time: 2:31 AM
 */
defined("APP") or die();
if(!$this->logged()) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You need to login before you can perform this action.")));
//echo "<pre>";
//print_r($media);
//echo "</pre>";
//die();
?><div class="col-md-6">
	<?php echo $this->profileMedia() ?>
</div>
    <div class="col-md-6">
		<form action="<?php echo Main::href("videoedit/{$media->id}") ?>" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="inputDes">Update your Video Description:</label>
		    	<textarea name="description" id="inputDes" rows="5" class="yourshow form-control" required="required"><?=$media->description?></textarea>
		    </div>
		    <div class="form-group">
		    	<label for="title">Place:</label>
				<input type="text" class="form-control" name="title" id="title" value="<?=$media->title?>">
			</div>
            <input type="hidden" name="type" value="video">
            <div class='form-group date-pic-area'>
            	<label for="datetimepicker4">Release Date:</label>
                <input type='text' name='release_date' class='form-control release_date' placeholder='Select Date and Time' value="<?=$media->release_date?>" id='datetimepicker4'/>
            </div>
            <script type='text/javascript'>
                $(document).ready(function () {
                    $('#datetimepicker4').datetimepicker({
                        format: 'M d, Y H:i:s',
                        // format:'DD.MM.YYYY h:mm a',
                        // formatTime:'H:i:s',
                        // formatDate:'M d, Y',
                        // minuteIntervel: '5'                                   
                    });
                });
            </script>
		    
		    <div class="form-group">
		    	<input type="submit" class="btn btn-lg btn-block btn-success" value="Submit" name="submit">
			</div>
		   
		</form>
	</div>
	
<script type="text/javascript">
    function clearDesc() {
        // if(this.value != "Write something about your upcoming video.. ") {
        this.value = '';
        return false;
        // }
    }
    $("textarea").keyup(function(e) {
        while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
            $(this).height($(this).height()+1);
        };
    });
</script>