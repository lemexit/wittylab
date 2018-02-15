<?php defined("APP") or die() ?>
<?php if($this->config["carousel"]): ?>
<section class="promo">  
  <div class="media media-inline">
    <?php echo $this->listMedia($this->getMedia(array("order" => "RAND()", "limit" => 8)), NULL, NULL, "custom"); ?>
  </div>
</section>  
<?php endif; ?>
<section>
  <div class="container">
    <?php echo $this->getSubscriptionMerge() ?>
    <div class="row">
      <div class="col-md-8 content">   
        <?php echo $this->homeMedia() ?>                             
      </div>
      <div class="col-md-4 sidebar">
        <?php $this->sidebar(array("trending" => array("limit" => 5), "blog" => array("limit" => 5), "topusers" => array("limit" => 10))) ?>
      </div>
    </div>
  </div>
</section>