<?php defined("APP") or die() ?>
 <style type="text/css">
  #footer-sub{
    background-color: #f3f7f8;
    border-top: 1px solid #dbdbdb;
}

#footer-main{
    background-color: #012b72;
}

#footer-sub h5{
    color:#565656;
    margin-top: 25px;
}

#footer-sub ul{
    list-style: none;
    margin-top: 20px;
}

#footer-sub hr{
    margin: 5px;

}

#footer-sub ul li{
margin-left: -38px;
}

#footer-sub a:link {
    text-decoration: none;
    color:#565656;
    font-size: 12px;
}

#footer-sub a:visited {
    text-decoration: none;
    color:#565656;
}


#footer-sub a:hover {
    text-decoration: none;
    color: blue;
}


#footer-sub a:active {
    text-decoration: none;
    color:#565656;
}

.vertical-line{
    border-right: 1px solid #dbdbdb;
    margin: 8px;
    padding: 0px;
}

.glyphicon {
    font-size: 35px;
     color:#6d6c6c;
}

#sub-two{
    margin: 0px;
    padding: 0px;
}

#sub-two .vertical-line h4{
    color:#6d6c6c;
}


#footer-main ul{
    list-style: none;
}

#footer-main ul li{
    float:left;
    text-decoration: none;
    padding-left: 15px;
    margin-top: 17px;
}

#footer-main a:link {
    color:white;
    font-size: 12px;
}

#footer-main a:visited {
    color:white;
}


#footer-main a:hover {
    text-decoration: none;
    color: #00b9f5;
}


#footer-main a:active {
    color:white;
}

.glyphicon-search{
    font-size: 20px;
}

#social-menu{
    float: right;
   margin-right: 60px;
}

#side-padding{
    padding: 0px;
    margin: 0px;
}
</style>
  <footer>
  <div style="min-height: 50px;" id="footer-main">

  <ul>
      <li>
        <a href="<?php echo $this->config["url"] ?>"><?php echo e("Home") ?></a>
      </li>
      <?php foreach ($pages as $page):?>
      <li><a href="<?php echo Main::href("page/{$page->slug}") ?>" title='<?php echo _($page->name)?>'><b><?php echo _($page->name)?></b></a></li>
      <?php endforeach; ?>
      <li>
        <a href="<?php echo Main::href("contact") ?>"><?php echo e("Contact Us") ?></a>
      </li> 
  </ul>
  
  <div id="social-menu">
    <ul>
      <li><a href="#">&copy; <?php echo date("Y") ?> <?php echo e("All Rights Reserved.") ?></a></li>
    </ul>
  </div>

</div>
  </footer>
  <div class="login-form hidden">
    <?php if(!$this->config["maintenance"] && $this->config["user"] && ($this->config["fb_connect"] || $this->config["tw_connect"] || $this->config["gl_connect"])):?>
      <?php if($this->config["fb_connect"]):?>
        <a href="<?php echo Main::href("user/login/facebook") ?>" class="btn btn-facebook btn-block"><?php echo e("Login with Facebook") ?></a>      
      <?php endif;?>
      <?php if($this->config["tw_connect"]):?>        
        <a href="<?php echo Main::href("user/login/twitter") ?>" class="btn btn-twitter btn-block"><?php echo e("Login with Twitter") ?></a>
      <?php endif;?>
      <?php if($this->config["gl_connect"]):?>        
        <a href="<?php echo Main::href("user/login/google") ?>" class="btn btn-google btn-block"><?php echo e("Login with Google") ?></a>
      <?php endif;?>
    <?php endif; ?>
    <hr>
    <form action="<?php echo Main::href("user/login") ?>" method="post">
      <div class="form-group">
        <label for="user" class="control-label"><?php echo e("Username or Email") ?></label>
        <input type="text" id="user" name="email" value="" placeholder="<?php echo e("Username or Email") ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="password" class="control-label"><?php echo e("Password") ?> </label>
        <input type="password" id="password" name="password" value="" placeholder="<?php echo e("Password") ?>" class="form-control">
      </div>  
      <input type="hidden" name="next" value="<?php echo Main::url() ?>">
      <?php echo Main::csrf_token(TRUE) ?>    
      <button type="submit" class="btn btn-primary"><?php echo e("Login") ?></button>
      <?php if($this->config["user"] && !$this->config["maintenance"]): ?>
        <a href="<?php echo Main::href("user/register") ?>" class="btn btn-primary"><?php echo e("Register") ?></a>
      <?php endif ?>
    </form>
  </div>
  <div class="this-return-data"></div>
  <?php echo Main::enqueue('footer') ?>
  </body>
  <script type="text/javascript">
    function comment(id) {
            $("#commentSection" + id).show();
        }
    // $(document).ready(function(){counter()});
    $(window).scroll(function(){
        var sticky = $('.sticky'),
            scroll = $(window).scrollTop();

        if (scroll >= 100) sticky.addClass('fixed');
        else sticky.removeClass('fixed');

        var sticky = $('.profileLeft'),
            scroll = $(window).scrollTop();

        if (scroll >= 100) sticky.addClass('profileFixed');
        else sticky.removeClass('profileFixed');
      });
  </script>
</html>