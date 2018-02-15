<?php defined("APP") or die() ?>
<?php
if(!$this->logged()) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You need to login before you can perform this action.")));
$site_url = '';
$site_url = $this->config["url"];
?>

<section class="home mar-bot-30">
        <div class="container">
            <div class="row">
                <div class="col-sm-4 col-xs-12 right-border">
                    <div class="pro-upload">
                        <img src="<?php echo $this->avatar($user, 160) ?>" alt="<?php echo $profile->name ?>" class="img img-responsive">
                        <!-- <a href="#">Upload</a> -->
                    </div>
                    <div class="pro-name">
                        <h2>Edit Profile <a href="<?php echo Main::href("user/account/settings") ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a></h2>
                    </div>

                    <div class="row">
                        <div class="pro-list var-menu">
                            <ul>
                                <li><a href="#">Profile Name: <span><?php echo $profile->name ?></span></a></li>
                                <li><a href="#">Username: <span><?=$user->username?></span></a></li>
                                <li><a href="#">Email: <span><?=$user->email?></span></a></li>
                                <li><a href="#">Phone: <span><?=$user->mobile?></span></a></li>
                                <li><a href="#">Connect to Facebook</a></li>
                                <li><a href="#">Connect to Google+</a></li>
                                <li><a href="#">Connect to Twitter</a></li>
                                <li><a href="#">Following Categoris</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <div class="user-profile">
                        <form action="<?php echo Main::href("upload/media") ?>" method="post" enctype="multipart/form-data">
                            <header class="pro-header">
                                <input type="text" class="yourshow" name="title" id="title" placeholder="Write something about your upcoming video.. ">
                            </header>
                            <div class="form-group hidden" id="videoUpForm">
                                <input type="file" id="upload" name="upload" class="form-control" id="videoUp" aria-describedby="emailHelp" placeholder="No file Chosen">
                                <small id="upload" name="upload" class="form-text text-muted" style="text-align: left; display: block">Maximum size of 50MB</small>
                                <input type="file" id="thumb" name="thumb" class="form-control" id="videoThumb" aria-describedby="emailHelp" placeholder="No file Chosen">
                                <small id="thumb" name="thumb" class="form-text text-muted" style="text-align: left; display: block; margin: 0;">Set your video thumbnail</small>
                            </div>
                            <input type="hidden" name="type" value="video">
                            <div class="date-pic-area hidden">
                                <input type='text' name="release_date" class="form-control" placeholder="Select Date and Time" id='datetimepicker4'/>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    $('#datetimepicker4').datetimepicker({
                                        format: 'M d, Y H:i:s'
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
                                <input type='url' class="form-control profile-url" placeholder="'ex: http://exmple.com' or 'place name' or 'channal name for full video'" id='url'/>
                            </div>
                            <div class="menu">
                                <div class="hor-menu">
                                    <ul>
                                        <li><a id="file-upload" href="javascript:void(0)"><i class="fa fa-upload"></i></a></li>
                                        <li><a id="publishedDate" href="javascript:void(0)"><i class="fa fa-calendar"></i></a></li>
                                        <li><a id="videoCategory" href="javascript:void(0)"><i class="fa fa-th-large"></i></a></li>
                                        <li><a id="main_url" href="javascript:void(0)"><i class="fa fa-link"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php echo Main::csrf_token(TRUE) ?>
                            <button class="yourshowBtn" type="submit">Submit</button>
                            </form>
                            <div class="bod-bottom"></div>

                            <div class="profile-details">
                                <div class="pro-title">
                                    <div class="row">
                                        <div class="col-sm-7">
                                            <h2>Time Left : <span id="counter"></span><!--Months, Days , Hours, Min , Sec--></h2>
                                            <div id="release" class="hidden">dec 24, 2017 15:37:25</div>
                                        </div>                    
                                        <div class="col-sm-5">
                                            <a href="" class="btn btn-success txt-white pull-right">&bull;&bull;&bull;</a>
                                            <a class="bell-alert" href="#"><i class="fa fa-bell"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="pro-body">
                                    <div class="row">
                                        <?php echo $this->profileMedia() ?>
                                        <div class="col-sm-5">
                                            <div class="video-palyer">
                                                <video controls="" id="videoPlayer" width="100%">
                                                    <source src="<?php echo $site_url ?>/static/frontend/videos/phire-to-pabona.mp4" type="video/mp4"></source>
                                                    
                                                </video>
                                                <img src="<?php echo $site_url ?>/static/frontend/videos/hridoy.png" alt="Video Title" class="img-responsive" id="video">
                                            </div>
                                            <div class="hor-menu">
                                                <ul>
                                                    <li>
                                                        <a href="">
                                                            <i class="fa fa-clock-o"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="">
                                                            <i class="fa fa-comments"></i>
                                                        </a>
                                                    </li>
                                                    <li><a href=""><i class="fa fa-share-alt"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <artical>
                                                <p>What is Lorem Ipsum? Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the</p>
                                            </artical>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>$(document).ready(function(){counter()})</script>