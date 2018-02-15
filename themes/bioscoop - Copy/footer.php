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
<?php defined("APP") or die() ?>
<?php if($this->logged() == TRUE): ?>
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
<?php endif ?>
  </body>
</html>