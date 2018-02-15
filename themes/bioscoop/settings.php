<?php defined("APP") or die() ?>
<?php
	if(!$this->logged()) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You need to login before you can perform this action.")));
	$site_url = '';
	$site_url = $this->config["url"];
?>
<section class="home mar-bot-30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 col-xs-12 right-border profileLeft">

                    <div class="pro-upload">
                        <img src="<?php echo $this->avatar($user, 160) ?>" alt="<?php echo $profile->name ?>" class="img img-responsive">
                    </div>
                    <div class="pro-name">
                        <h2>Profile Name <?=isset($user->name)?$user->name:'No Name'?></a></h2>
                    </div>


                    <div class="row">
                        <div class="pro-list var-menu">
                        <form action="<?php echo Main::href("user/settings") ?>" method="post" enctype='multipart/form-data'>
                            <ul>
                                <li><label for="name">Profile Name:</label><input type="text" name="name" class="form-control" value="<?=isset($user->name)?$user->name:FALSE?>" placeholder='Type your Name'></li>
                                <li><label for="dob">Date of Birth:</label><input type="text" name="dob" class="form-control" id="datetimepicker" value="<?=isset($user->dob)?$user->dob:FALSE?>" placeholder='Select Date'></li>
                                <li><label for="email">Email:</label><input type="email" name="email" class="form-control" value="<?=isset($user->email)?$user->email:FALSE?>" placeholder='Type your Email'></li>
                                <li><label for="mobile">Mobile:</label><input type="number" name="mobile" class="form-control" value="<?=isset($user->mobile)?$user->mobile:FALSE?>" placeholder="Type Your Mobile No"></li>
                            </ul>
                            <div class="sex-option">
                                    <p>Sex</p>
                                    <input type="radio" name="sex" value="male" <?=($user->sex == 'male'?'checked':FALSE)?>> Male<br>
                                    <input type="radio" name="sex" value="female" <?=($user->sex == 'female'?'checked':FALSE)?>> Female
                            </div>
                            <input type="hidden" name="public" value="1">
                            <?php echo Main::csrf_token(TRUE) ?>
                            <input type="submit" name="submit" value="Submit" class="btn btn-success btn-lg">
                        </form>
                            <div class="save-delete-acc">
                                <ul>
                                    <!-- <li class="save-change-btn"><a href="#">Logout</a></li> -->
                                    <li><a href="#" class="btn btn-danger btn-sm" style="color: #fff">Delete Account</a></li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-sm-8 col-xs-12 pull-right mobile">
                    <div class="user-profile">
                            <div class="bod-bottom"></div>
                            <?php echo $this->profileMedia() ?>
                            
                    </div>
                </div>
            </div>
        </div>
    </section>
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
<style type="text/css">
    .form-group {
            margin-bottom: 0px !important;
        }
</style>