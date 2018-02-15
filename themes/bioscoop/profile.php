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
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 mobile">
                            <div class="side-bar profile-sidebar">
                                <h2 class="title profile-sidebar-title">Most Waited Videos</h2>
                                <header>
                                    <?php
                                    $coun = 1;
                                    $sports = $this->GetCategory('sports', 6);
                                    ?>
<!--                                    <h3 class="sub-title">Sports</h3>-->
                                    <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?= $sports[0]->catid ?>]' class="this-action follow side-btn" data-content="follow">Follow</a>
                                </header>
                                <div class="list-item">
                                    <div>
                                        <div class="carousel-inner1">
                                            <?php
                                            foreach ($sports as $sport):
                                                if ($coun == 1):
                                                    ?>
                                                    <div class="row marginT-row">
                                                    <?php endif ?>
                                                    <div class="col-sm-6">
                                                        <div class="item">
                                                            <img src="<?= $this->config["url"] ?>/content/thumbs/<?= $sport->thumb ?>" alt="<?= $sport->title ?>" class="img-responsive">
                                                            <div class="video-overly">
                                                                <a href='<?= $this->config["url"] ?>/view/<?= $sport->url ?>' alt='<?= $sport->title ?>'><img src='<?= $this->config["url"] ?>/static/images/play-btn.png' alt='<?= $sport->title ?>' /></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if ($coun == 2): ?>
                                                    </div>
                                                    <?php $coun = 0;
                                                endif; ?>
                                                <?php
                                                $coun++;
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 col-xs-12 pull-right">
                    <div class="user-profile">
                        <form action="<?php echo Main::href("upload/media") ?>" method="post" enctype="multipart/form-data">
                            <header class="pro-header">
                                <textarea name="description" id="inputDes" class="yourshow" rows="1" required="required" placeholder="Write something about your upcoming video.. (Maximum 160 characters)"></textarea>
                                <!-- <input type="text" class="yourshow" name="description" id="title" placeholder=""> -->
                            </header>
                            <div class="form-group hidden" id="videoUpForm">
                            <div class="row">
                                <div  class="col-md-6 col-xs-12">
                                <input type="file" id="upload" name="upload" class="form-control-file" id="videoUp" placeholder="Maximum size of 50MB">
                                <small id="upload" name="upload" class="form-text text-muted" style="text-align: left; display: block">Maximum size of 50MB</small>
                                </div>
                                <div  class="col-md-6 col-xs-12">
                                <input type="file" id="thumb" name="thumb" class="form-control-file" id="videoThumb" placeholder="Set your video thumbnail">
                                <small id="thumb" name="thumb" class="form-text text-muted" style="text-align: left; display: block; margin: 0;">Set your video thumbnail</small>
                                </div>
                            </div>
                            </div>
                            <input type="hidden" name="type" value="video">
                            <div class="date-pic-area hidden">
                                <input type='text' name="release_date" class="form-control release_date" placeholder="Select Date and Time" id='datetimepicker4'/>
                            </div>
                            <script type="text/javascript">
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

                            <div class="upload-catagory hidden">
                               <select name="category" id="category-1" data-active="video">
                                <?php $categories = $this->db->get("category",array("type"=>"video", "parentid" => "0")) ?>
                                <?php foreach ($categories as $category): ?>                  
                                  <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                                  <?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
                                  <?php foreach ($child as $ch): ?>
                                    <option value="<?php echo $ch->id ?>">&nbsp;&nbsp;|_&nbsp;<?php echo $ch->name ?></option>
                                  <?php endforeach ?>
                                <?php endforeach ?> 
                                </select> 
                            </div>
                            
                            <div class="urlUpload hidden">
                                <input type='text' name="title" class="form-control profile-url" placeholder="'ex: http://exmple.com' or 'place name' or 'channal name for full video'" id='url'/>
                            </div>
                            <div class="menu" style="margin-top: -5px">
                                <div class="hor-menu">
                                    <ul>
                                        <li><a id="file-upload" href="javascript:void(0)"><i class="fa fa-upload"></i></a></li>
                                        <li><a id="publishedDate" href="javascript:void(0)"><i class="fa fa-calendar"></i></a></li>
                                        <li><a id="videoCategory" href="javascript:void(0)"><i class="fa fa-th-large"></i></a></li>
                                        <li><a id="main_url" href="javascript:void(0)"><i class="fa fa-link"></i></a></li>
                                    </ul>
                                </div>
                                <?php echo Main::csrf_token(TRUE) ?>
                                <button class="yourshowBtn" type="submit">Submit</button>
                            </div>
                            </form>
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