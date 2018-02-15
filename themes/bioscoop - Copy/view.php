<?php defined("APP") or die() ?>
<?php 
if($this->logged() != TRUE)
      header("Location: ".$this->config['url']."/user/login");
?>
<?php if($this->logged() == TRUE): ?>
<?php if($this->config["carousel"]): ?>
<section class="promo">  
  <div class="media media-inline">
    <?php echo $this->listMedia($this->getMedia(array("order" => "RAND()", "limit" => 8)), NULL, NULL, "custom"); ?>
  </div>
</section>  
<?php endif; ?>
<section class="home">
  <div class="container-fluid">
    <?php echo $this->getSubscriptionMerge(); ?>
    <div class="row">
                <div class="col-sm-3 col-xs-12">
                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Sports</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel1" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner1">
                                    <?php 
                                        $coun = 1;
                                        $sports = $this->GetCategory('sports'); 
                                        foreach($sports as $sport):
                                            if($coun == 1):
                                    ?>
                                        <div class="row marginT-row">
                                        <?php endif ?>
                                            <div class="col-sm-6">
                                                <div class="item">
                                                    <img src="<?=$this->config["url"]?>/content/thumbs/<?=$sport->thumb?>" alt="Natok" class="img-responsive">
                                                    <div class="video-overly">
                                                    <a href='<?=$this->config["url"]?>/view/<?=$sport->url?>' alt='<?=$sport->title?>'><img src='<?=$this->config["url"]?>/static/images/play-btn.png' alt='<?=$sport->title?>' /></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php if($coun == 2): ?>
                                        </div>
                                        <?php $coun = 0; endif; ?>
                                    <?php
                                        $coun++; 
                                        endforeach; 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Life events</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel2" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner1">
                                    <?php 
                                        $coun = 1;
                                        $lifeevents = $this->GetCategory('life-events'); 
                                        foreach($lifeevents as $lifeevent):
                                            if($coun == 1):
                                    ?>
                                        <div class="row marginT-row">
                                        <?php endif ?>
                                            <div class="col-sm-6">
                                                <div class="item">
                                                    <img src="<?=$this->config["url"]?>/content/thumbs/<?=$lifeevent->thumb?>" alt="Natok" class="img-responsive">
                                                    <div class="video-overly">
                                                    <a href='<?=$this->config["url"]?>/view/<?=$lifeevent->url?>' alt='<?=$lifeevent->title?>'><img src='<?=$this->config["url"]?>/static/images/play-btn.png' alt='<?=$lifeevent->title?>' /></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php if($coun == 2): ?>
                                        </div>
                                        <?php $coun = 0; endif; ?>
                                    <?php
                                        $coun++; 
                                        endforeach; 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                  <div class='video-box'>
                        <div class='video-details'>
                            <h2 class='title'><a href='<?=$media->url?>' title='<?=$media->title?>'><?=Main::truncate($media->title,100)?></a></h2>
                            <p class='video-info'><b><span id='relesDate<?=$media->id?>'><script>counter('<?=$media->id?>', '<?=$media->release_date?>')</script></span></b> <a href='#'><span class='pull-right'>&#8226;&#8226;&#8226;</span></a> <a href='#like' id='this-like-<?=$media->id?>' class='this-action this-tooltip' data-content='<?=e('Like')?>' data-action='like' data-data='["id":<?=$media->id?>, "check":<?=$media->userid?>]'><span class='pull-right fa fa-bell'></span></a></p>
                            <!-- <p><?=$media->description?></p> -->
                        </div>
                        <div class='video-palyer' id="published<?=$media->id?>">
                           <?php echo $media->player ?>
                        </div>
                        <div class='video-options' id="comment<?=$media->id?>">
                            <a href=href='#like' id='this-like-{$media->id}' class='this-action this-tooltip".($rating && $rating->rating=="liked" ? " active":"")."' data-content='".e('Like')."' data-action='like' data-data='[\"id\":{$media->id}, \"check\":{$media->userid}]'><i class='fa fa-user'></i>Waiting</a>
                            <a href='{$media->url}#comments' class='comments-trigger'><i class='fa fa-comments'></i>Comment</a>
                            <a href='#'><i class='fa fa-share-alt'></i>Share</a>
                        </div>
                    </div>
                    <div class="commentSection" id="commentSection">
                        <div class="video-author" data-id="<?php echo $media->userid ?>">
                        <?php echo $this->comments($media->id, $media->url, $media->comments) ?>
                    </div>
                    
                     <?php //echo $this->homeMedia() ?>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Public Events</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel3" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner1">
                                    <?php 
                                        $coun = 1;
                                        $publicevents = $this->GetCategory('public-events'); 
                                        foreach($publicevents as $publicevent):
                                            if($coun == 1):
                                    ?>
                                        <div class="row marginT-row">
                                        <?php endif ?>
                                            <div class="col-sm-6">
                                                <div class="item">
                                                    <img src="<?=$this->config["url"]?>/content/thumbs/<?=$publicevent->thumb?>" alt="Natok" class="img-responsive">
                                                    <div class="video-overly">
                                                    <a href='<?=$this->config["url"]?>/view/<?=$publicevent->url?>' alt='<?=$publicevent->title?>'><img src='<?=$this->config["url"]?>/static/images/play-btn.png' alt='<?=$publicevent->title?>' /></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php if($coun == 2): ?>
                                        </div>
                                        <?php $coun = 0; endif; ?>
                                    <?php
                                        $coun++; 
                                        endforeach; 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Movie</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel4" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner1">
                                    <?php 
                                        $coun = 1;
                                        $movies = $this->GetCategory('movies'); 
                                        foreach($movies as $movie):
                                            if($coun == 1):
                                    ?>
                                        <div class="row marginT-row">
                                        <?php endif ?>
                                            <div class="col-sm-6">
                                                <div class="item">
                                                    <img src="<?=$this->config["url"]?>/content/thumbs/<?=$movie->thumb?>" alt="Natok" class="img-responsive">
                                                    <div class="video-overly">
                                                    <a href='<?=$this->config["url"]?>/view/<?=$movie->url?>' alt='<?=$movie->title?>'><img src='<?=$this->config["url"]?>/static/images/play-btn.png' alt='<?=$movie->title?>' /></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php if($coun == 2): ?>
                                        </div>
                                        <?php $coun = 0; endif; ?>
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
</section>
<?php endif; ?>