<?php defined("APP") or die() ?>
<?php
      $site_url = '';
      $site_url = $this->config["url"];
      $actual_link = "$_SERVER[REQUEST_URI]";
?>
<div class="wrapper" style="background:#0a1725">
  <header id="header">
          <div class="container">
              <div class="row">
                  <div class="col-sm-6 col-xs-12 hidden-xs">
                      <div class="logo">
                          <a href="<?php echo  $site_url;?>"><img src="<?php echo  $site_url;?>/static/images/logo.png" alt=""></a>
                      </div>
                  </div>
                  <div class="col-sm-6 col-xs-12 hidden-xs">
                      <div class="signup">
                        <?php if($this->config["user"] && !$this->config["maintenance"]): ?>
                          <a href="<?php echo Main::href("user/register")?>">Sign up</a>
                          <div>New to wittylab?</div> 
                        <?php endif ?>                      
                      </div>
                  </div>
              </div>
          </div>
      </header>
<section class="login-sec">
        <div class="login-box">
            <div class="login_logo">
                <div class="login_logo_box">
                    <img src="<?=$site_url?>/static/images/logo.png" width="200" />
                </div>
            </div>          
              <form role="form" id="login_form" method="post" action="<?php echo Main::href("user/register")?>">
        <?php if(!$this->config["maintenance"] && $this->config["user"] && ($this->config["fb_connect"] || $this->config["tw_connect"] || $this->config["gl_connect"])):?>
                  <input type="text" name="email" id="email" placeholder="Username" class="trans-in-out">
                  <input type="text" name="email" id="email" placeholder="Enter your valid email." class="trans-in-out">
                  <input type="password" name="password" id="pass" placeholder="Type Your Password" class="trans-in-out pass">
                  <input type="password" name="cpassword" id="cpass" placeholder="Confirm Password" class="trans-in-out cpass">
                  <input type="hidden" name="terms" value="1" data-class="blue">  
                  <?php echo Main::csrf_token(TRUE) ?>
                  <input type="submit" class="login_btn" name="submit" id="submit" value="Submit" class="trans-in-out">
              </form>
              <div class="login-txt">
                  <h2 class="txt-white">Login With</h2>
              </div>
              <div class="social-login hor-menu">
                  <ul>
                    <?php if($this->config["gl_connect"]):?>
                      <li><a href="<?php echo $this->config["url"]?>/user/login/google"><i class="fa fa-google-plus"></i></a></li>
                      <?php endif;?> 
                      <?php if($this->config["fb_connect"]):?>
                      <li><a href="<?php echo $this->config["url"]?>/user/login/facebook"><i class="fa fa-facebook"></i></a></li>
                      <?php endif;?>
                      <?php if($this->config["tw_connect"]):?>
                      <li><a href="<?php echo $this->config["url"]?>/user/login/twitter"><i class="fa fa-twitter"></i></a></li>
                      <?php endif;?>
                    <?php endif;?>
                  </ul>
              </div>
          </div>
      </section>
    </div>
