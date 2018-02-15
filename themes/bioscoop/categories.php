<?php defined("APP") or die(); ?>
<?php
$cat = $this->do;
$query = "Select * from "
?>
<section class="home mar-bot-30">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">

                <?php
                $coun = 1;
                $catDatas = $this->GetCategory($cat, 1000);
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
                        </div>
                    </div>
                <?php else:?>
                <h1 style="margin-top:50px;">No Video Found In This Category</h1>
                <?php endif;?>

            </div>
        </div>
    </div>
</section>
