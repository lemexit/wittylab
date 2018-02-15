<?php defined("APP") or die() ?>
<section id="page">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content">
      	<?php foreach ($posts as $post): ?>
          <div class="panel panel-default">
            <div class="panel-heading"><a href="<?php echo Main::href("blog/{$post->slug}") ?>" title="<?php echo $post->name ?>"><?php echo $post->name ?></a></div>
            <p>
              <i class="fa fa-clock-o"></i> <strong><?php echo Main::timeago($post->date) ?></strong> /
              <i class="glyphicon glyphicon-user"></i> <a href="<?php echo Main::href() ?>"><strong>Admin</strong></a>
            </p>
            <div class="panel-body">
              <?php echo $this->blog_excerpt($post) ?>
              <hr>
              <div class='social-media'>                
                <?php echo Main::share(Main::href("blog/{$post->slug}"),urlencode($post->name)) ?>
              </div>               
            </div>
          </div>          
        <?php endforeach ?>
        <?php echo $pagination ?>
      </div>
      <div class="col-md-4 sidebar">
        <?php $this->sidebar(array("featured" => array("limit" => 5),"topusers" => array("limit" => 10))) ?>
      </div>
    </div>
  </div>
</section>