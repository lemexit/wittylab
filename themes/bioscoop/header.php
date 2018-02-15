<?php defined("APP") or die() ?>
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
  <head>       
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0" />  
    <meta name="description" content="<?php echo Main::description() ?>" />
    <meta name="keywords" content="<?php echo $this->config["keywords"] ?>" />
    <!-- Open Graph Tags -->
    <?php echo Main::ogp(); ?> 

    <title><?php echo Main::title() ?></title> 
    <link href="<?php echo $this->config["url"] ?>/static/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">        
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/themes/<?php echo $this->config["theme"] ?>/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/themes/<?php echo $this->config["theme"] ?>/responsive.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/static/library/css/components.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/static/frontend/css/jquery-bootstrap-datepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/static/frontend/css/jquery.datetimepicker.min.css">
    
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/frontend/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/frontend/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/frontend/js/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/frontend/js/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/frontend/js/script.js"></script>

    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/notify.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/application.fn.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/is.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/application.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/server.js"></script>    
    <?php Main::enqueue() // Add scripts/stylesheet when needed ?>    
    <script type="text/javascript">
      var appurl = "<?php echo $this->config["url"] ?>";
      var token = "<?php echo $this->config["public_token"] ?>";
    </script>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body<?php echo Main::body_class() ?>>
   <?php echo $this->admin_menu() ?>    
   <?php echo Main::message() ?>   
   <?php
      $site_url = '';
      $site_url = $this->config["url"];
      $actual_link = "$_SERVER[REQUEST_URI]";
   ?>
   <?php if($this->logged() == TRUE): ?>
   <header id="header" style="background: #0A1725;">
      <div class="container">
          <div class="row">
              <div class="col-sm-3 col-xs-3">
                  <div class="logo">
                      <a href="<?=$this->config["url"]?>"><img src="<?php echo  $site_url;?>/static/images/logo.png" alt=""></a>
                  </div>
              </div>
              <div class="col-sm-6 col-xs-6">
                  <div class="head-search">
                      <form action="">
                          <input type="search" name="search" id="search" class="search-field" placeholder="Search">
                          <button type="submit" class="search-btn"><div class="fa fa-search"></div></button>
                      </form>
                  </div>
              </div>
              <div class="col-sm-3 col-xs-3">
                <div class="row">
                  <div class="col-md-9 col-xs-6">
                    <ul>
                    <li class="dropdown dropdown-alt" id="notifications"><a href="#notifications" class="this-action" data-action="notification" ><span class="fa fa-bell<?php echo $notifications ? ' fa-new': '' ?>"></span></a>
                                    <div class="dropdown-holder panel panel-default">
                                      <div class="panel-heading"><?php echo e("Notifications") ?> <a href="<?php echo Main::href("user/account/notifications") ?>" class="pull-right"><small>(<?php echo e("view more") ?>)</small></a></div>
                                      <ul class="panel-body">                      
                                      </ul>                      
                                    </div>
                                  </li>
                    </ul>
                  </div>
                  <div class="col-md-3 col-xs-6">
                    <div class="head-pro">
                        <div class="dropdown">
                            <a href="<?php echo Main::href("user/account") ?>">
                                <div class="user-box">
                                    <img src="<?php echo $this->avatar($this->user) ?>" alt="<?php echo $this->user->username ?>" class="img img-circle img-responsive">
                                </div>
                                <!-- <span class="caret"></span> -->
                            </a>
                            <!-- <ul class="dropdown-menu">
                              <?php
                                if($this->logged()){
                                  ?>

                                <?php
                                  if($this->admin()){
                                    ?>
                                      <li><a href="<?php echo Main::href("user/{$this->user->username}") ?>"><span class="fa fa-user"></span> My Profile</a></li>
                                      <li><a href="<?php echo Main::href("user/account/playlists") ?>"><span class="fa fa-list"></span> <?php echo e("My Playlists") ?></a></li>
                                      <li><a href="<?php echo Main::href("user/account/settings") ?>"><span class="fa fa-gear"></span> <?php echo e("My Settings") ?></a></li>
                                      <li><a href="<?php echo Main::href("user/logout") ?>"><span class="fa fa-sign-out"></span> <?php echo e("Log Out") ?></a></li>
                                    <?php
                                  }
                                  else{
                                    ?>
                                    <li><a href="<?php echo Main::href("user/{$this->user->username}") ?>"><span class="fa fa-eye"></span><?php echo e("My Profile") ?></a></li>
                                    <li><a href="<?php echo Main::href("user/account") ?>"><span class="fa fa-user"></span> <?php echo e("My Account") ?></a></li>
                                    <li><a href="<?php echo Main::href("user/account/playlists") ?>"><span class="fa fa-list"></span> <?php echo e("My Playlists") ?></a></li>
                                    <li><a href="<?php echo Main::href("user/account/settings") ?>"><span class="fa fa-gear"></span> <?php echo e("My Settings") ?></a></li>
                                    <li><a href="<?php echo Main::href("user/logout") ?>"><span class="fa fa-sign-out"></span> <?php echo e("Log Out") ?></a></li>

                                    <?php

                                  }
                                }
                              ?>
                            </ul> -->
                        </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
      </div>
    </header>
    <?php 
      $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      $uri_segments = explode('/', $uri_path);
    ?>
  <section class="menu sticky">
      <div class="hor-menu">
          <ul>
              <li class="<?=empty($uri_segments['3'])?'active':false?>"><a href="<?php echo Main::href()?>"><i class="fa fa-television"></i></a></li>
              <li class="<?=isset($uri_segments['3']) && $uri_segments['3'] =='video'?'active':false?>"><a href="<?php echo Main::href('video')?>"><i class="fa fa-th-large"></i></a></li>
              <li class="<?=isset($uri_segments['3']) && $uri_segments['3'] =='user'?'active':false?>"><a href="<?php echo Main::href("user/{$this->user->username}") ?>"><i class="fa fa-user"></i></a></li>
          </ul>
      </div>
  </section>
  <div style="clear: both"></div>
  <?php endif;?>
   <!-- Here Is the starting point of the Login page -->
  