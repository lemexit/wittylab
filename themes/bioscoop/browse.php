<?php defined("APP") or die() ?>
<?php
    $query = "Select * from "
?>
<section class="home mar-bot-30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">

                    <?php
                    $coun = 1;
                    $catDatas = $this->GetCategory('sports', 4);
                    if(count($catDatas) > 0):
                    ?>
                    <div class="video-side-bar">
                        <header>
                          <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?=$catDatas[0]->catid?>]' class="this-action follow video-side-btn" data-content="follow">Follow</a>
                          <h3 class="video-sub-title">Sports</h3>
                        </header>
                        <div class="list-video">
                            <?php 
                                
                            foreach($catDatas as $catData):
                              if($coun == 1):
                            ?>
                            <div class="row">
                            <?php endif ?>  
                                <div class="col-sm-3">
                                    <div class="list-view">
                                        <img src="<?=$this->config["url"]?>/content/thumbs/<?=$catData->thumb?>" alt="<?=$catData->titlee?>" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="<?=$this->config["url"]?>/view/<?=$catData->url?>" class="play-btn"><img src="<?=$this->config["url"]?>/static/images/play-btn.png" width="48" height="48" alt="<?=$catData->title?>"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php if($coun == 4): ?>
                              </div>
                            <?php $coun = 0; endif; ?>
                            <?php
                              $coun++; 
                              endforeach; 
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn_more" href="<?=$this->config["url"]?>/category/sports">More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>

                    <?php
                    $coun = 1;
                    $catDatas = $this->GetCategory('movies', 8);
                    if(count($catDatas) > 0):
                    ?>
                    <div class="video-side-bar">
                        <header>
                            <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?=$catDatas[0]->catid?>]' class="this-action follow video-side-btn" data-content="follow">Follow</a>
                            <h3 class="video-sub-title">Movies</h3>
                        </header>
                        <div class="list-video">
                            <?php 
                             
                            foreach($catDatas as $catData):
                              if($coun == 1):
                            ?>
                            <div class="row">
                            <?php endif ?>  
                                <div class="col-sm-3">
                                    <div class="list-view">
                                        <img src="<?=$this->config["url"]?>/content/thumbs/<?=$catData->thumb?>" alt="<?=$catData->titlee?>" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="<?=$this->config["url"]?>/view/<?=$catData->url?>" class="play-btn"><img src="<?=$this->config["url"]?>/static/images/play-btn.png" width="48" height="48" alt="<?=$catData->title?>"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php if($coun == 4): ?>
                              </div>
                            <?php $coun = 0; endif; ?>
                            <?php
                              $coun++; 
                              endforeach; 
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn_more" href="<?=$this->config["url"]?>/category/movies">More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>

                    <?php
                    $coun = 1;
                    $catDatas = $this->GetCategory('life-events', 8);
                    if(count($catDatas) > 0):
                    ?>
                    <div class="video-side-bar">
                        <header>
                          <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?=$catDatas[0]->catid?>]' class="this-action follow video-side-btn" data-content="follow">Follow</a>
                            <h3 class="video-sub-title">Life Events</h3>
                        </header>
                        <div class="list-video">
                          <?php 
                            
                            foreach($catDatas as $catData):
                              if($coun == 1):
                            ?>
                            <div class="row">
                            <?php endif ?>  
                                <div class="col-sm-3">
                                    <div class="list-view">
                                        <img src="<?=$this->config["url"]?>/content/thumbs/<?=$catData->thumb?>" alt="<?=$catData->titlee?>" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="<?=$this->config["url"]?>/view/<?=$catData->url?>" class="play-btn"><img src="<?=$this->config["url"]?>/static/images/play-btn.png" width="48" height="48" alt="<?=$catData->title?>"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php if($coun == 4): ?>
                              </div>
                            <?php $coun = 0; endif; ?>
                            <?php
                              $coun++; 
                              endforeach; 
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn_more" href="<?=$this->config["url"]?>/category/life-events">More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>

                    <?php
                    $coun = 1;
                    $catDatas = $this->GetCategory('public-events', 8);
                    if(count($catDatas) > 0):
                    ?>
                    <div class="video-side-bar">
                        <header>
                            <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?=$catDatas[0]->catid?>]' class="this-action follow video-side-btn" data-content="follow">Follow</a>
                            <h3 class="video-sub-title">Public Events</h3>
                        </header>
                        <div class="list-video">
                          <?php 
                            
                            foreach($catDatas as $catData):
                              if($coun == 1):
                            ?>
                            <div class="row">
                            <?php endif ?>  
                                <div class="col-sm-3">
                                    <div class="list-view">
                                        <img src="<?=$this->config["url"]?>/content/thumbs/<?=$catData->thumb?>" alt="<?=$catData->titlee?>" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="<?=$this->config["url"]?>/view/<?=$catData->url?>" class="play-btn"><img src="<?=$this->config["url"]?>/static/images/play-btn.png" width="48" height="48" alt="<?=$catData->title?>"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php if($coun == 4): ?>
                              </div>
                            <?php $coun = 0; endif; ?>
                            <?php
                              $coun++; 
                              endforeach; 
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn_more" href="<?=$this->config["url"]?>/category/public-events">More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>

                    <?php
                    $coun = 1;
                    $catDatas = $this->GetCategory('songs', 8);
                    if(count($catData) > 0):
                    ?>
                    <div class="video-side-bar">
                        <header>
                            <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?=$catDatas[0]->catid?>]' class="this-action follow video-side-btn" data-content="follow">Follow</a>
                            <h3 class="video-sub-title">Songs</h3>
                        </header>
                        <div class="list-video">
                          <?php 
                             
                            foreach($catDatas as $catData):
                              if($coun == 1):
                            ?>
                            <div class="row">
                            <?php endif ?>  
                                <div class="col-sm-3">
                                    <div class="list-view">
                                        <img src="<?=$this->config["url"]?>/content/thumbs/<?=$catData->thumb?>" alt="<?=$catData->titlee?>" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="<?=$this->config["url"]?>/view/<?=$catData->url?>" class="play-btn"><img src="<?=$this->config["url"]?>/static/images/play-btn.png" width="48" height="48" alt="<?=$catData->title?>"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php if($coun == 4): ?>
                              </div>
                            <?php $coun = 0; endif; ?>
                            <?php
                              $coun++; 
                              endforeach; 
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn_more" href="<?=$this->config["url"]?>/category/songs">More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>

                    <?php
                    $coun = 1;
                    $catDatas = $this->GetCategory('tv-shows', 8);
                    if(count($catDatas) > 0):
                    ?>
                    <div class="video-side-bar">
                        <header>
                            <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?=$catDatas[0]->catid?>]' class="this-action follow video-side-btn" data-content="follow">Follow</a>
                            <h3 class="video-sub-title">TV Shows</h3>
                        </header>
                        <div class="list-video">
                          <?php

                            foreach($catDatas as $catData):
                              if($coun == 1):
                            ?>
                            <div class="row">
                            <?php endif ?>
                                <div class="col-sm-3">
                                    <div class="list-view">
                                        <img src="<?=$this->config["url"]?>/content/thumbs/<?=$catData->thumb?>" alt="<?=$catData->titlee?>" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="<?=$this->config["url"]?>/view/<?=$catData->url?>" class="play-btn"><img src="<?=$this->config["url"]?>/static/images/play-btn.png" width="48" height="48" alt="<?=$catData->title?>"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php if($coun == 4): ?>
                              </div>
                            <?php $coun = 0; endif; ?>
                            <?php
                              $coun++;
                              endforeach;
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn_more" href="<?=$this->config["url"]?>/category/tv-shows">More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>

                    <?php
                    $coun = 1;
                    $catDatas = $this->GetCategory('others', 8);
                    if(count($catDatas) > 0):
                    ?>
                    <div class="video-side-bar">
                        <header>
                            <a href='#follow' id='this-subscribe' data-action='follow' data-data='["id":<?=$catDatas[0]->catid?>]' class="this-action follow video-side-btn" data-content="follow">Follow</a>
                            <h3 class="video-sub-title">Others</h3>
                        </header>
                        <div class="list-video">
                          <?php

                            foreach($catDatas as $catData):
                              if($coun == 1):
                            ?>
                            <div class="row">
                            <?php endif ?>
                                <div class="col-sm-3">
                                    <div class="list-view">
                                        <img src="<?=$this->config["url"]?>/content/thumbs/<?=$catData->thumb?>" alt="<?=$catData->titlee?>" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="<?=$this->config["url"]?>/view/<?=$catData->url?>" class="play-btn"><img src="<?=$this->config["url"]?>/static/images/play-btn.png" width="48" height="48" alt="<?=$catData->title?>"></a>
                                        </div>
                                    </div>
                                </div>
                            <?php if($coun == 4): ?>
                              </div>
                            <?php $coun = 0; endif; ?>
                            <?php
                              $coun++;
                              endforeach;
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a class="btn_more" href="<?=$this->config["url"]?>/category/others">More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>

                </div>
            </div>
        </div>
    </section>