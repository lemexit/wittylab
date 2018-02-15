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
                         <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-camera"></i></a>
                    </div>
                    <div class="pro-name">
                        <h2>Edit Profile <a href="<?php echo Main::href("user/account/settings") ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a></h2>
                    </div>
                    <div class="row">

                        <div class="pro-list var-menu">
                            <ul>
                                <li><a href="#">Profile Name: <span><?php echo $user->name ?></span></a></li>
                                <li><a href="#">Date of Birth: <span><?=$user->dob?></span></a></li>
                                <li><a href="#">Email: <span><?=$user->email?></span></a></li>
                                <li><a href="#">Phone: <span><?=$user->mobile?></span></a></li>
                                <!-- <li><a href="#">Connect to Facebook <span><i class="fa fa-facebook" style="padding: 7px 10px;"></i></span></a></li>
                                <li><a href="#">Connect to Google+ <span><i class="fa fa-google-plus" style="padding: 7px 5px; background-color: #D7473C"></i></span></a></li>
                                <li><a href="#">Connect to Twitter <span><i class="fa fa-twitter" style="padding: 7px 8px; background-color: #61A6D5;"></i></span></a></li>
                                <li><a href="#">Following Categoris</a></li> -->
                            </ul>
                            <div class="sex-option">
                                <form action="">
                                    <p>Sex</p>
                                    <input type="radio" name="gender" value="male" <?=($user->sex == 'male'?'checked':FALSE)?>> Male<br>
                                    <input type="radio" name="gender" value="female" <?=($user->sex == 'female'?'checked':FALSE)?>> Female
                                </form>
                            </div>
                            <div class="save-delete-acc">
                                <ul>
                                    <li class="save-change-btn"><a href="<?php echo Main::href("user/logout") ?>">Logout</a></li>
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

<!-- Modal -->
       <!-- Bootstrap -->
                          <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet"> 
                         <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/static/library/plugins/imgupload/assets/css/demo.html5imageupload.css?v1.3">
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-body">
                                <form enctype="multipart/form-data" action="<?php echo Main::href("user/settings") ?>" method="post" role="form">
                                <!-- <div id="modaldialog" class="html5imageupload" data-resize="true" data-width="960" data-height="540" data-url="canvas.php" style="width: 100%;">
                                  <input type="file" name="thumb" />
                                </div> -->
                                <div id="modaldialog" class="html5imageupload" class="dropzone" data-width="400" data-resize="true" data-ajax="false" data-height="400" style="width: 100%;">
                                  <input type="file" name="thumb" required="required" />
                                </div>
                                <input type="hidden" name="public" value="1">
                                <?php echo Main::csrf_token(TRUE) ?>
                                <button type="submit" class="btn btn-default">Submit</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div> 
                              <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
                          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
                          <!-- Include all compiled plugins (below), or include individual files as needed -->
                          <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
                           <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/library/plugins/imgupload/assets/js/html5imageupload.js?v1.4.3"></script>
                           <script>
    
                              $('#retrievingfilename').html5imageupload({
                                onAfterProcessImage: function() {
                                  $('#filename').val($(this.element).data('name'));
                                },
                                onAfterCancel: function() {
                                  $('#filename').val('');
                                }
                                
                              });
                              
                              $('#save').html5imageupload({
                                onSave: function(data) {
                                  console.log(data);
                                },
                                
                              });
                              
                              $('.dropzone').html5imageupload();
                              
                              $( "#myModal" ).on('shown.bs.modal', function(){
                                $('#modaldialog').html5imageupload();
                              });
                              /*
                              $('#form').html5imageupload({
                                onAfterProcessImage: function() {
                                  $(this.element).closest('form').submit();
                                }
                              });
                              
                              $('form button.btn').unbind('click').click(function(e) {
                                  e.preventDefault()
                                  $(this).closest('form').find('#form').data('html5imageupload').imageCrop()
                              });*/

                              
                              </script>
