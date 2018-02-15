<?php defined("APP") or die() ?>
<?php if($this->config["carousel"]): ?>
<?php
  $site_url = '';
  $site_url = $this->config["url"];
?>
<?php echo $this->getSubscriptionMerge() ?>
                    <?php echo $this->homeMedia(); ?>
<section class="home">
        <div class="container">
            <div class="row">
                <div class="col-sm-3 col-xs-12">
                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Natok</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel1" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="item active">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok1.jpg" alt="Natok 1" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rony" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok2.jpg" alt="Natok 2" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rana" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Natok</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel2" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="item active">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok3.jpg" alt="Natok 1" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rony" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok4.jpg" alt="Natok 2" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rana" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <?php echo $this->getSubscriptionMerge() ?>
                    <?php echo $this->homeMedia(); ?>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Natok</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel3" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="item active">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok1.jpg" alt="Natok 1" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rony" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok2.jpg" alt="Natok 2" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rana" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="side-bar">
                        <h2 class="title">Suggested Category</h2>
                        <header>
                            <h3 class="sub-title">Natok</h3>
                            <a href="#" class="side-btn"><i class="fa fa-share"></i> Follow</a>
                        </header>
                        <div class="list-item">
                            <div id="VideoCarousel4" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="item active">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok3.jpg" alt="Natok 1" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rony" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <img src="<?php echo  $site_url;?>/static/frontend/images/natok4.jpg" alt="Natok 2" class="img-responsive">
                                        <div class="video-overly">
                                            <a href="#rana" class="play-btn"><img src="<?php echo  $site_url;?>/static/frontend/images/play-btn.png" width="48" height="48" alt="Play"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>