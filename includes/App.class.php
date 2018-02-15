<?php 
/**
 * ====================================================================================
 *
 *
 * @author Emrul (https://lemexit.com)
 * @link https://lemexit.com 
 * @license https://lemexit.com/license
 * @package WittyLab
 * @subpackage API Handler
 */
class App{
	/**
	 * Current Language
	 * @since 1.0
	 **/	
 	public $lang = "";
	/**
	 * Items Per Page
	 * @since 1.0
	 **/
	public $limit = 15;
	/**
	 * Results Count
	 * @var integer
	 */
	public $count = NULL;
	/**
	 * Template Variables
	 * @since 1.0
	 **/
	protected $isHome     = FALSE;
	protected $isList     = FALSE;
	protected $is404      = FALSE;
	protected $isUser     = FALSE;
	protected $isView     = FALSE;
	protected $footerShow = TRUE;
	protected $headerShow = TRUE;	
	protected $admin_menu_html = "";
	/**
	 * Application Variables
	 * @since 1.0
	 **/
	protected $page = 1, $db, $config = array(), $action = "", $do = "", $id = "", $http = "http://", $sandbox = FALSE;
	protected $actions = array(
													"user","page","search","contact","server","upload","rss","embed","v","view",
													"video","videoedit","trending","channel","channels","music","vine","picture","blog", "post", "articles",
													"playlist","api", "staff","category",'cron'
												);	
	/**
	 * User Variables
	 * @since 1.0
	 **/
	protected $logged = FALSE;
	protected $admin  = FALSE, $user = NULL, $userid = "0";		
	/**
	 * Constructor: Checks logged user status
	 * @since 1.0
	 **/
	public function __construct($db,$config){
		$this->config = $config;
		$this->db     = $db;
  	// Clean Request
  	if(isset($_GET)) $_GET = array_map("Main::clean", $_GET);
		if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"]>0) $this->page=Main::clean($_GET["page"]);
		$this->http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://":"http://");		
		$this->check();		
	}
	/**
	 * Bootstrap
	 * @since 1.0
	 **/
	public function run(){
		// Get theme functions file
		if(file_exists(THEME."/functions.php")){
			include($this->t("functions"));
		}		
						
		// Parse URL and execute method
		if(isset($_GET["a"]) && !empty($_GET["a"])){
			// Validate Request
			$var = explode("/", $_GET["a"]);
			if(count($var) > 4) return $this->_404();
			$this->action=Main::clean($var[0],3,TRUE);
			// Set variables to be used
			if(isset($var[1]) && !empty($var[1])) $this->do = Main::clean($var[1],3);
			if(isset($var[2]) && !empty($var[2])) $this->id = Main::clean($var[2],3);			
			// Run Method
			if(in_array($var[0],$this->actions)){
				return $this->{$var[0]}();
			}
			// Show 404 Error Page
			return $this->_404();
		}
		// Run Home Page
		return $this->home();
	}	
	/**
	 * User status: Logged & Admin
	 * @author Emrul
	 * @since  1.0
	 */
	protected function logged(){
		return $this->logged;
	}
	protected function admin(){
		return $this->admin;
	}
	/**
	 * Check user status
	 * @since 1.2.1
	 */
	protected function check(){
		if($info=Main::user()){
			if($user = $this->db->get("user",array("id"=>"?","auth_key"=>"?"),array("limit"=>1),array($info[0],$info[1]))){
				$this->logged = TRUE;		
				$this->user = $user;							
				// Check if Admin				
				if($this->user->admin) $this->admin=TRUE;	
				// Check user profile
				$this->user->profile = json_decode($this->user->profile);
				if(empty($this->user->profile)){
					$this->user->profile = new stdClass;
					$this->user->profile->name = "";
					$this->user->profile->cover = "";
					$this->user->profile->description = "";
				}
				// Unset sensitive information
				unset($this->user->password);
			}
		}
	}

	protected function cron(){
	    $notifications = $this->db->get(array('custom'=>"* FROM ".$this->config['prefix']."notification WHERE notified=0"));
	    foreach ($notifications as $temp){
	        $date = time();
            $media = $this->db->get(array('custom'=>"* FROM ".$this->config['prefix']."Media WHERE id = ".$temp->mediaid))[0];
            $release_date = strtotime($media->release_date);
            if($release_date < $date){
                $user = $this->db->get(array('custom'=>"* FROM ".$this->config['prefix']."user WHERE id = ".$temp->userid))[0];
                $message = "The video you followed was released today, Please click the url to watch the video";
                $message .= $this->config['url'].'view/'.$media->url;
                if(mail($user->email,'Video REleased',$message)){
                    $counter = 1;
                    $count = $counter++;
                }
            }
        }

        if(isset($count) && $count > 0){
	        exit("Email Send to ".$count." Persons");
        }
    }

	protected function category(){
        $catID = $this->do;
        // Plug-in Header
        Main::plug("home_header");
        // Show Template
        $this->header();
        include($this->t("categories"));
        $this->footer();
    }
	/**
	 * Home Page
	 * @since 1.0
	 */
	protected function home(){	
		// Check if under maintenance
		if($this->config["maintenance"] && !$this->admin()) {
			$this->_maintenance();
			return;
		}		
		// Define Homepage
		$this->isHome = TRUE;
		// Get subscription
		$subscription = $this->getSubscriptionMerge();
		// Get featured media
		$featured = $this->listMedia($this->getMedia(array("featured" => "1")));

		Main::cdn("owl");
		Main::add('<script>$(document).ready(function(){$(".media-inline").owlCarousel({items: 6});});</script>', "custom");

		// Plug-in Header
		Main::plug("home_header");
		// Show Template
		$this->header();
		include($this->t("index"));
		$this->footer();
	}

	protected function followed_media($keyword = NULL){
        $userid = $this->user->id;
        $categories = $this->db->get(array('custom'=>"* FROM ".$this->config['prefix']."follow WHERE userid = ".$userid));
        foreach($categories as $cats){
            $medias = $this->db->get(array('custom'=>'* FROM '.$this->config['prefix']."media WHERE catid = ".$cats->catid));
            foreach($medias as $med){
                $media[] = $med;
            }
        }
        if(isset($media) && count($media) > 0){
            $html = "";
            $i = 0;
            foreach ($media as $media) {
                    // Format Media
                    $media = $this->formatMedia($media);
                    if(!isset($media->profile)) $media->profile ="";
                    if(!isset($media->author)) $media->author ="";
                    $media->description = Main::truncate($media->description, 200);
                    if(!is_null($keyword)){
                        $media->description = str_ireplace($keyword,"<strong>$keyword</strong>",$media->description);
                    }
                    if($media->type == "post"){
                        if(!empty($media->file)){
                            $media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}'></a>";
                        }else{
                            $media->player = "";
                        }
                    }
                    // Check if logged user has already rated this media and check if nsfw is enabled
                    if($this->logged()){
                        if(!$this->user->nsfw && $media->nsfw){
                            $media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/static/nsfw_big.png' alt=''></a>";
                        }
                        $rating = $this->db->get("rating", array("mediaid" => $media->id, "userid" => $this->user->id),array("limit" => "1"));
                    }else{
                        if($media->nsfw){
                            $media->player = "<img src='{$this->config["url"]}/static/nsfw_big.jpg' alt=''>";
                        }
                        $rating = NULL;
                    }
                    $profile = new stdClass();
                    $profile->player = "<iframe src='".Main::href("embed/{$media->uniqueid}")."' frameborder='0' width='100%' height='250' scrolling='no' allowfullscreen></iframe>";
                    if($i > 0 && $i%5==0)	$html .= $this->ads(728);

                    $sum = $media->likes - $media->dislikes;
                    $sum = $sum < 0 ? 0 : $sum;
                    $points = $sum == "1" ? "<strong> {$sum}</strong>	".e("Point")."" : "<strong> {$sum}</strong>	".e("Points")."";
                    $html .="<div class='profile-details'>
                                <div class='pro-title'>
                                    <div class='row'>
                                        <div class='col-sm-10'>
                                            <h2><span id='relesDate{$media->id}'><script>counter('{$media->id}', '{$media->release_date}','{$media->title}')</script></span></h2>
                                        </div>
                                        <div class='col-sm-2'>
                                            <a href='".Main::href("videoedit/{$media->uniqueid}")."' class='btn btn-success txt-white pull-right'>&bull;&bull;&bull;</a>
                                        </div>
                                    </div>
                                </div>
                                <div class='pro-body'>
                                    <div class='row'>
                                        <div class='col-sm-5'>
                                            <div class='video-palyer' id='published{$media->id}'>
					                            {$profile->player}
					                        </div>
                                        <div class='video-options'>
				                        	<ul class='player-bottom'>
				                            <li><a href=href='#like' id='this-like-{$media->id}' class='this-action".($rating && $rating->rating=="liked" ? " active":"")."' data-content='' data-action='like' data-data='[\"id\":{$media->id}, \"check\":{$media->userid}]'><i class='fa fa-user'></i><div id='waitPlus-{$media->id}'>{$media->likes}</div> Waiting</a></li>
				                            <li><a href='javascript:;' onclick='comment({$media->id})' class='comments-trigger'><i class='fa fa-comments'></i>{$media->comments} Comment</a></li>
				                            <li><a href='#' id='shareBtn'><i class='fa fa-share-alt'></i>Share</a><ul><li>".Main::share($media->url,urlencode($media->title), array("facebook", "twitter", "google"))."</li></ul></li>
				                            </ul>
				                        </div>
                                        </div>
                                        <div class='col-sm-7'>
                                            <artical>
                                                <p>{$media->description}</p>
                                            </artical>
                                        </div>
                                        <div class='commentSection' style='display: none' id='commentSection{$media->id}'><div class='video-author' data-id='{$media->userid}'></div>".
                        $this->comments($media->id, $media->url, $media->comments)."
                                    </div>
                                </div>
                            </div>
                            
                    </div>";
                    $i++;
            }
            return $html;
        }
    }
		/**
		 * Get Hompage Media
		 * @author Emrul
		 * @since  1.0
		 */
	protected function profileMedia(){
			$content = "";
			// Get media
				Main::add("<script>$('.ads-301').sticky({TopMargin: '20px'})</script>","custom", TRUE);
				$content = $this->ads("728");
				$media = $this->getMedia(array("limit" => $this->config["homelimit"], "pagination" => TRUE));
				$content .= "<div class=''>";
				$content .=  $this->ProfileMediaGet($media);
				$content .=  Main::ajax_button(Main::href("?page=%d"), $this->page, $this->count);
				$content .= "</div>";
			return $content;
		}
	protected function ProfileMediaGet(array $list, $keyword = NULL){
		// Loop and format media
		$html = "";
		$i = 0;
		
		foreach ($list as $media) {
			if($media->userid == $this->user->id):
			// Format Media
			$media = $this->formatMedia($media);
			if(!isset($media->profile)) $media->profile ="";
			if(!isset($media->author)) $media->author ="";
			$media->description = Main::truncate($media->description, 200);
			if(!is_null($keyword)){
				$media->description = str_ireplace($keyword,"<strong>$keyword</strong>",$media->description);
			}
			if($media->type == "post"){
				if(!empty($media->file)){
					$media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}'></a>";
				}else{
					$media->player = "";
				}	
			}			
			// Check if logged user has already rated this media and check if nsfw is enabled
			if($this->logged()){
				if(!$this->user->nsfw && $media->nsfw){
					$media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/static/nsfw_big.png' alt=''></a>";
				}
				$rating = $this->db->get("rating", array("mediaid" => $media->id, "userid" => $this->user->id),array("limit" => "1"));
			}else{
				if($media->nsfw){
					$media->player = "<img src='{$this->config["url"]}/static/nsfw_big.jpg' alt=''>";
				}				
				$rating = NULL;
			}
			$profile = new stdClass();
			$profile->player = "<iframe src='".Main::href("embed/{$media->uniqueid}")."' frameborder='0' width='100%' height='250' scrolling='no' allowfullscreen></iframe>";		
			if($i > 0 && $i%5==0)	$html .= $this->ads(728);
			
			$sum = $media->likes - $media->dislikes;
			$sum = $sum < 0 ? 0 : $sum;
			$points = $sum == "1" ? "<strong> {$sum}</strong>	".e("Point")."" : "<strong> {$sum}</strong>	".e("Points")."";
			$html .="<div class='profile-details'>
                                <div class='pro-title'>
                                    <div class='row'>
                                        <div class='col-sm-10'>
                                            <h2><span id='relesDate{$media->id}'><script>counter('{$media->id}', '{$media->release_date}','{$media->title}')</script></span></h2>
                                        </div>
                                        <div class='col-sm-2'>
                                            <a href='".Main::href("videoedit/{$media->uniqueid}")."' class='btn btn-success txt-white pull-right'>&bull;&bull;&bull;</a>
                                        </div>
                                    </div>
                                </div>
                                <div class='pro-body'>
                                    <div class='row'>
                                        <div class='col-sm-5'>
                                            <div class='video-palyer' id='published{$media->id}'>
					                            {$profile->player}
					                        </div>
                                        <div class='video-options'>
				                        	<ul class='player-bottom'>
				                            <li><a href=href='#like' id='this-like-{$media->id}' class='this-action".($rating && $rating->rating=="liked" ? " active":"")."' data-content='' data-action='like' data-data='[\"id\":{$media->id}, \"check\":{$media->userid}]'><i class='fa fa-user'></i><div id='waitPlus-{$media->id}'>{$media->likes}</div> Waiting</a></li>
				                            <li><a href='javascript:;' onclick='comment({$media->id})' class='comments-trigger'><i class='fa fa-comments'></i>{$media->comments} Comment</a></li>
				                            <li><a href='#' id='shareBtn'><i class='fa fa-share-alt'></i>Share</a><ul><li>".Main::share($media->url,urlencode($media->title), array("facebook", "twitter", "google"))."</li></ul></li>
				                            </ul>
				                        </div>
                                        </div>
                                        <div class='col-sm-7'>
                                            <artical>
                                                <p>{$media->description}</p>
                                            </artical>
                                        </div>
                                        <div class='commentSection' style='display: none' id='commentSection{$media->id}'><div class='video-author' data-id='{$media->userid}'></div>".
                        $this->comments($media->id, $media->url, $media->comments)."
                                    </div>
                                </div>
                            </div>
                            
                    </div>";
			$i++;	
			endif;		
		}
		
		return $html;
	}
	protected function homeMedia(){
			$content = "";
			// Get media
			if ($this->config["mode"] == "bioscoop") {
				Main::add("<script>$('.ads-301').sticky({TopMargin: '20px'})</script>","custom", TRUE);
				$content = $this->ads("728");
				$media = $this->getMedia(array("limit" => $this->config["homelimit"], "pagination" => TRUE));
					$content .=  $this->BiosCoop($media);
					$content .=  Main::ajax_button(Main::href("?page=%d"), $this->page, $this->count);
				
			}
			else if($this->config["mode"] == "grid"){
				foreach (types() as $type => $name) {
					$media = $this->listMedia($this->getMedia(array("type" => $type, "limit" => $this->config["homelimit"])));
					if($media){
						$content .= '<div class="panel panel-default type-'.$type.'" >
								            <div class="panel-heading">
								             	<h3><i class="fa fa-'.types_icon($type).'"></i> '.types($type, FALSE, TRUE).'</h3>
								              <a href="'.Main::href($type).'" class="btn btn-primary btn-xs pull-right">'.e("View More").'</a>
								            </div>
								            <div class="media media-latest">
								            	<div class="row">
									              '.$media.'
								              </div>
								            </div>    
								          </div>';
						$content.= $this->ads("728");
					}
				}
			}else if($this->config["mode"] == "uni"){
						$media = $this->listMedia($this->getMedia(array("pagination" => TRUE, "order"=>"date")));
						// Generate Pagination
						$pagination = Main::pagination($this->count, $this->page, Main::href("?page=%d"));
						$content .= '<div class="panel panel-default" >
								            <div class="panel-heading">
								             	<h3><i class="fa fa-youtube-play"></i> '.e("Browse Media").'</h3>
								            </div>
								            <div class="row">
									            <div class="media media-latest">
									              '.$media.'
									            </div>      
								            </div> 
								            '.$pagination.'
								          </div>';		
			}else{
				// Add Sticky Ads
				Main::add("<script>$('.ads-301').sticky({TopMargin: '20px'})</script>","custom", TRUE);
				$content = $this->ads("728");
				$media = $this->getMedia(array("limit" => $this->config["homelimit"], "pagination" => TRUE));
				$content .= "<div class='scroll'>";
					$content .=  $this->rowMedia($media);
					$content .=  Main::ajax_button(Main::href("?page=%d"), $this->page, $this->count);
				$content .= "</div>";
			}
			return $content;
		}
	/**
	 * Trending Videos
	 * @author Emrul
	 * @since  1.0
	 */
	protected function trending(){		
		$this->filter($this->do);
		// Set Page Title
		$title = e("Trending Media");
		// Get latest Videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, "order"=>"trending")));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("trending?page=%d"));
		// Set meta data
		Main::set("title",$title);
		Main::set("description", e("Browse our library of trending media. These media were chosen by our users to be the best so we are sure you will love them."));
		Main::plug("trending_header");
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();
	}
	/**
	 * Trending Videos
	 * @author Emrul
	 * @since  1.0
	 */
	protected function staff(){		
		$this->filter($this->do);
		// Set Page Title
		$title = e("Staff Picked Media");
		// Get latest Videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, "featured" => 1)));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("staff?page=%d"));
		// Set meta data
		Main::set("title", $title);
		Main::set("description", e("Browse our library of trending media. These media were chosen by our users to be the best so we are sure you will love them."));
		Main::plug("trending_header");
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();
	}	
	/**
	 * Browse Videos
	 * @author Emrul
	 * @since  1.0
	 */
	protected function video(){
		$this->filter($this->do);
		if(!$this->config["type"]["video"]){
			return $this->_404();
		}
		// Define Page Title
		$title = e("Browse Videos");
		if(isset($_GET["filter"]) && in_array($_GET["filter"], array("views","date","likes","comments"))) {
			$filter = $_GET["filter"];
		}else{
			$filter = "date";
		}
		// Get latest videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, 'type' => 'video', "order" => $filter)));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("video?page=%d&filter=$filter"));
		// Set meta data
		Main::set("title",$title);
		Main::set("description", e("Browse our library of lastest videos including music videos."));
		Main::plug("video_header");
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();		
	}
	/**
	 * Music Videos
	 * @author Emrul
	 * @since  1.0
	 */
	protected function music(){
		$this->filter($this->do);
		if(!$this->config["type"]["music"]){
			return $this->_404();
		}		
		// Set Page Title		
		$title = types('music', FALSE, TRUE);
		// Define Page Title
		if(isset($_GET["filter"]) && in_array($_GET["filter"], array("views","date","likes","comments"))) {
			$filter = $_GET["filter"];
		}else{
			$filter = "date";
		}		
		// Get latest Videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, "type"=> "music", "order" => $filter)));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("music?page=%d&filter=$filter"));
		// Set alt style
		Main::set("body_class","music-page");
		// Set meta data
		Main::set("title",e("Browse ".$title));
		Main::set("description", e("Browse our library of music videos. We have made sure that these videos are the best!"));
		Main::set("url","{$this->config["url"]}/{$this->id}");
		Main::plug("music_header");		
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();
	}
	/**
	 * Vines
	 * @author Emrul
	 * @since  1.0
	 */
	protected function vine(){
		$this->filter($this->do);
		if(!$this->config["type"]["vine"]){
			return $this->_404();
		}
		// Set Page Title		
		$title = types('vine', FALSE, TRUE);
		if(isset($_GET["filter"]) && in_array($_GET["filter"], array("views","date","likes","comments"))) {
			$filter = $_GET["filter"];
		}else{
			$filter = "date";
		}				
		// Get latest Videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, "type"=> "vine","order" => $filter)));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("vine?page=%d&filter=$filter"));
		// Set alt style
		Main::set("body_class","vine-page");
		// Set meta data
		Main::set("title",$title);
		Main::set("description", e("Browse our library of vines. We have made sure that these vines are the best!"));
		Main::plug("vine_header");
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();
	}
	/**
	 * Pictures
	 * @author Emrul
	 * @since  1.0
	 */
	protected function picture(){
		$this->filter($this->do);
		if(!$this->config["type"]["picture"]){
			return $this->_404();
		}
		// Set Page Title		
		$title = types('picture', FALSE, TRUE);
		if(isset($_GET["filter"]) && in_array($_GET["filter"], array("views","date","likes","comments"))) {
			$filter = $_GET["filter"];
		}else{
			$filter = "date";
		}				
		// Get latest Videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, "type"=> "picture","order" => $filter)));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("picture?page=%d&filter=$filter"));
		// Set alt style
		Main::set("body_class","picture-page");
		// Set meta data
		Main::set("title",$title);
		Main::set("description", e("Browse our library of pictures. We have made sure that these pictures are the best!"));
		Main::plug("picture_header");
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();
	}
	/**
	 * Posts
	 * @author Emrul
	 * @since  1.5
	 */
	protected function articles(){
		return Main::redirect("post");
	}		
	protected function post(){
		$this->filter($this->do);
		if(!$this->config["type"]["post"]){
			return $this->_404();
		}
		// Set Page Title		
		$title = types('post', FALSE, TRUE);
		if(isset($_GET["filter"]) && in_array($_GET["filter"], array("views","date","likes","comments"))) {
			$filter = $_GET["filter"];
		}else{
			$filter = "date";
		}				
		// Get latest Videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, "type"=> "post","order" => $filter)));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("post?page=%d&filter=$filter"));
		// Set alt style
		Main::set("body_class","post-page");
		// Set meta data
		Main::set("title", $title);
		Main::set("description", e("Browse our library of posts. We have made sure that these posts are the best!"));
		Main::plug("post_header");
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();
	}	
	/**
	 * Channel Videos
	 * @author Emrul
	 * @since  1.6
	 */
	public function GetCategory($catname, $limit){
		// Get Category
		$category = $this->db->get("category",["slug" => $catname]);
		if($category){
		    $category = $this->db->get("media",["catid" => $category[0]->id],array("limit" => $limit));
		    return $category;
        }else{
		    return array();
        }
	}
	protected function channel(){
		// Get Category
		if(!$category = $this->db->get("category", array("slug" => "?", "type" => "?"),array("limit" => 1),array($this->id,$this->do))){
			return $this->_404();
		}
		// Define Page Title
		$title = e($category->name);		

		if(!empty($category->description)){
			$description = $category->description;
		}else{
			$description = e("Browse our library of lastest videos including music videos.");
		}
		if(isset($_GET["filter"]) && in_array($_GET["filter"], array("views","date","likes","comments"))) {
			$filter = $_GET["filter"];
		}else{
			$filter = "date";
		}		
		// Get latest videos
		$videos = $this->listMedia($this->getMedia(array("pagination" => TRUE, "category" => $category->id,"order" => $filter)));
		// Generate Pagination
		$pagination = Main::pagination($this->count, $this->page, Main::href("channel/{$category->type}/{$category->slug}?page=%d&filter=$filter"));
		// Set meta data
		Main::set("title",$title);
		Main::set("description", $description);
		Main::plug("channel_header", array("id" => $category->id));
		// Show Template
		$this->header();
		include($this->t("browse"));
		$this->footer();		
	}
	/**
	 * Channels Video
	 * @author Emrul
	 * @since  1.0
	 */
	protected function channels(){
		// Set meta data
		Main::set("title", e("Channels"));
		Main::set("description", e("Browse our channels containing the best videos in each category."));
		Main::plug("channels_header");		
		// Show Template
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}	
			/**
			 * Get Channels
			 * @author Emrul
			 * @since  1.0
			 */
	private function getChannels(){
				$content = "";
				foreach (types() as $type => $name) {
					$has_Media = 0;
					if(!$this->config["type"][$type]) continue;
						$categories = $this->db->get("category",array("type" => $type, "parentid" => "0"),array("order" => "name", "asc" => 1));
						if($categories){
							$content .= '<div class="row category_holder" id="category-'.$type.'">
														<div class="row">
															<div class="col-sm-8">
																<i class="fa fa-'.types_icon($type).'"></i>	'.types($type, FALSE, TRUE).'
															</div>
															<div class="col-sm-4">
																<a href="'.Main::href($type).'" class="btn btn-primary btn-xs pull-right">'.e("View More").'</a>
															</div>
														</div>
													</div>';

							$content .= '<div class="panel panel-default type-'.$type.'" >';									           
														foreach ($categories as $category) {
															$media = $this->listMedia($this->getMedia(array("category" => $category->id, "limit" => 4)));
								$content .= "<div class='row'><div class='col-md-12'>
															<h3 class='header-in' id='{$category->type}-{$category->id}'>
							                  {$category->name}
							                  <a href='".Main::href("channel/{$category->type}/{$category->slug}")."' class='btn btn-xs btn-primary pull-right'>".e("View More")."</a>
							                </h3>
							                <div class='row'>
							                	<div class='media'>
							                  	{$media}
						                  	</div>
							                </div>";   
							    					if($child = $this->db->get("category",array("parentid" => $category->id),array("order" => "name", "asc" => 1))){
															$content .="<div class='channel-sub'>";
															foreach ($child as $c) {
																$media = $this->listMedia($this->getMedia(array("category" => $c->id, "limit" => 4)));
											$content .= "<h3 class='header-in' id='{$c->type}-{$c->id}'>
									                  {$c->name} ".types($c->type, FALSE, TRUE)."
									                  <a href='".Main::href("channel/{$c->type}/{$c->slug}")."' class='btn btn-xs btn-primary pull-right'>".e("View More")."</a>
									                </h3>
									                <div class='row'>
									                	<div class='media'>
									                  	{$media}
								                  	</div>
									                </div>";	
							    						}			
							    						$content .='</div>';			    						
							    					}            
	                $content .="</div></div><hr>";
														}
			          $content .='</div>';
						}
				}	
				return $content;
			}
	/**
	 * Display Blog
	 * @author Emrul
	 * @since  1.0
	 */
	protected function blog(){
		$this->filter($this->id);
		// Check if Blog is enabled
		if(!$this->config["type"]["blog"]) return $this->_404();
		// Check if a post is requested
		if(!empty($this->do)) return $this->blog_post();
		// Show the blog posts
		$posts = $this->db->get("blog",array("publish" => "1"), array("order" => "date", "count" => TRUE, "limit" => (($this->page-1)*2).", 2"));
		// Pagination
   	if(($this->db->rowCount%2)<>0) {
      $max = floor($this->db->rowCount/2)+1;
    } else {
      $max = floor($this->db->rowCount/2);
    } 		
		$pagination = Main::pagination($max, $this->page, Main::href("blog?page=%d"));
		// Error 
		if($this->db->rowCount > 0 && $this->page > $max) return $this->_404();
		// Plugin Blog
		Main::plug("blog_header");
		Main::set("title",e("Blog"));
		Main::set("description", e("Check out our blog for latest articles and news."));
		// Plug Admin control links
		$this->admin_menu_html = build_menu(array(
				array("href" => Main::ahref("blog/add"), "text" => "Add Blog Post", "icon" => "plus"),
			), TRUE);
		// Generate Template
		$this->header();
		include($this->t("blog"));
		$this->footer();
	}
			/**
			 * Display Blog Post
			 * @author Emrul
			 * @since  1.0
			 */	
			private function blog_post(){
				// Blog Post
				if(!$page = $this->db->get("blog", array("slug" => "?", "publish" => "1"), array("limit" => 1), array($this->do))){
					return $this->_404();
				}
				if(empty($page->meta_title)){
					$page->meta_title = $page->name;
				}
				if(empty($page->meta_description)){
					$page->meta_description = Main::truncate($page->name, 200, TRUE);
				}		
				// Plug Admin control links
				$this->admin_menu_html = build_menu(array(
						array("href" => Main::ahref("blog/edit/{$page->id}"), "text" => "Edit Post", "icon" => "edit"),
					), TRUE);		
				// Set meta data
				Main::set("title", $page->meta_title);
				Main::set("description", Main::truncate($page->meta_description,200, TRUE));
				
				// Plug-in Header
				Main::plug("post_header", array("post" => $page));
				$page->content = str_replace("<!--more-->","", $page->content);
				$page->content = str_replace("&lt;!--more--&gt;","", $page->content);
				$page->content = $page->content."<hr><div class='social-media'>".Main::share(Main::href("blog/{$page->slug}"),urlencode($page->name))."</div> ";
				// Add Comments
				if($this->config["comments"]){
					Main::hook("after_content" , array("App", "blog_comment"));
				}
				// Plug Before Content
				Main::hook("before_content", array("App", "blog_before_date"));
				// Show Template
				$this->header();
				include($this->t("page"));
				$this->footer();							
			}
			/**
			 * Return Excerpt
			 * @author Emrul
			 * @since  1.0
			 */
			private function blog_excerpt($post){
				$content = explode("<!--more-->", $post->content);
				$content = explode("&lt;!--more--&gt;", $content[0]);
				return $content[0]."<a href='".Main::href("blog/{$post->slug}")."' class='btn btn-xs btn-primary'>".e("Read more")."</a></p>";
			}
			/**
			 * Blog Comment
			 * @author Emrul
			 * @since  1.0
			 */
			public static function blog_comment($data){
				echo '<div class="panel panel-default">
								<div class="panel-heading">'.e("Comments").'</div>
								<div class="panel-body">
									<p>&nbsp;</p>
									<div id="fb-root"></div>
									<div class="fb-comments" data-href="'.$data["url"].'" data-num-posts="10" data-colorscheme="light" data-width="100%"></div>
								</div>
							</div>
				      <script>(function(d, s, id) {
				      var js, fjs = d.getElementsByTagName(s)[0];
				      if (d.getElementById(id)) return;
				      js = d.createElement(s); js.id = id;
				      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
				      fjs.parentNode.insertBefore(js, fjs);
				      }(document, \'script\', \'facebook-jssdk\'));</script>
						';						
			}	
			/**
			 * Add Date
			 * @author Emrul
			 * @since  1.0
			 */
			public static function blog_before_date($data){
				echo "<p><small>".e("Published")." <strong>".Main::timeago($data["data"]->date)."</strong></small></p>";
			}
	/**
	 * User Leaderboard
	 * @author Emrul
	 * @since  1.1
	 */
	protected function leaderboard(){
		Main::set("title", e("Top Users"));
		Main::set("description", "We have a global community of awesome users. They contribute in many ways.");
		Main::plug("search_header");		
		// Show Template
		$this->header();
		include($this->t(__FUNCTION__));		
		$this->footer();
	}
	/**
	 * Contact Us
	 * @author Emrul
	 * @since  1.5.1
	 */
	protected function contact(){
		if(isset($_POST["token"])){
			// Kill the bot
			if(Main::bot()) return $this->_404();
			// Validate Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect("contact",array("danger",e("Something went wrong, please try again.")));
			}		
			if(empty($_POST["email"]) || !Main::email($_POST["email"]) || empty($_POST["message"]) || strlen($_POST["message"]) < 10){
				return Main::redirect("contact",array("danger",e("Please fill everything")."!"));			
			}
			// Check Captcha
			if($this->config["captcha"]){
				$captcha=Main::check_captcha($_POST);
				if($captcha!='ok'){
					Main::redirect("contact",array("danger",$captcha));
					return;					
				}
			}	
			$email=Main::clean($_POST["email"],3,TRUE);
			$name=Main::clean($_POST["name"],3,TRUE);			
			$mail["to"]=$this->config["email"];
			$mail["subject"]="[{$this->config["title"]}] You have been contacted!";
			$mail["message"]="From: $name ($email)<br><br>".Main::clean($_POST["message"],3,TRUE);
			Main::send($mail);
			return Main::redirect("contact",array("success",e("Your message has been sent. We will reply you as soon as possible.")));	
		}
		Main::set("title",e("Contact Us"));
		Main::set("description",e("If you have any questions, feel free to contact us on this page."));
		Main::set("url","{$this->config["url"]}/contact");

		Main::plug("contact_header");		
		// Show Template
		$this->header();
		 echo '<section class="promo">
					  <div class="container text-center">
					    <h1>'.e("Contact us").'</h1>
							<p>'.e("If you have any questions, feel free to contact us on this page.").'</p>					    
					  </div>
					</section>
					<section id="login">
					  <div class="container">    
					    <div class="centered form panel panel-body">      
					      <form role="form" class="live_form" method="post" action="'.Main::href("contact").'">					      
					        <div class="form-group">
					          <label>'.e("Name").'</label>
					          <input type="text" class="form-control" name="name" value="">	            
					        </div>
					        <div class="form-group">
					          <label>'.e("Email").' ('.e("Required").')</label>
					          <input type="email" class="form-control" name="email" value="" required>		            
					        </div>  
					        <div class="form-group">
					          <label>'.e("Message").' ('.e("Required").')</label>
					          <textarea name="message" class="form-control" rows="10" required></textarea>	            
					        </div>          
									<div id="captcha" class="display">';
										Main::captcha();
						echo '</div><br>	        
					        '.Main::csrf_token(TRUE).'
					        <button type="submit" class="btn btn-primary">'.e("Send").'</button>        
					      </form>        
							</div>
						</div>
					</section>';
		$this->footer();
	}	
	/**
	 * Search Media
	 * @author Emrul
	 * @since  1.0
	 */
	protected function search(){
		$this->filter($this->id);
		if(isset($this->do)){
			$q = $this->do;
		}
		if(isset($_GET["q"])){
			$q = Main::clean($_GET["q"], 3, TRUE);
			return Main::redirect("search/{$q}");
		}
		if(empty($q) || strlen($q) <= 2){
			return Main::redirect("",array("danger", e("Sorry the keyword did not results any relevant media. Please try again.")));
		}
		if(isset($_GET["filter"]) && in_array($_GET["filter"], array("views","date","likes","comments"))) {
			$filter = $_GET["filter"];
		}else{
			$filter = "date";
		}

		// Get Videos
		$videos = $this->listMedia($this->db->search(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"),array(array("approved","1"),"tags" => ":t","title" => ":t", "description" => ":t"),array("limit" => (($this->page-1)*$this->limit).", {$this->limit}", "count" => TRUE, "order" => $filter),array(":t" => "%$q%")), $q, NULL, 1);
		if(!$videos){
			$videos = "<div class='media-item col-md-12'>
									<p><strong>".e("No media found")."</strong></p>
									<p>".e("Sorry the keyword did not results any relevant media. Please try again.")."</p>
								</div>";
		}
    if(($this->db->rowCount%$this->limit)<>0) {
      $max = floor($this->db->rowCount/$this->limit)+1;
    } else {
      $max = floor($this->db->rowCount/$this->limit);
    } 		
    $pagination = Main::pagination($max, $this->page, Main::href("search/{$q}?page=%d"));
		// Set meta data
		$title = e("Search results for ").$q;
		Main::set("title", $title);
		Main::set("description", "Check our most popular videos for $q.");
		Main::plug("search_header");		
		// Show Template
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();				
	}
	/**
	 * Upload Media [Public]
	 * @author Emrul
	 * @since  1.0
	 */
	protected function upload(){
		$this->filter($this->id);
		// Check if logged in
		if(!$this->logged()) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You need to login before you can perform this action.")));
		if($this->logged() && $this->config["submission"] != "2") return $this->_404();
		if(empty($this->user->username)) return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Please choose a username before continuing.")));
		// Upload media
		if(!empty($this->do)){
			if(in_array($this->do, array("url", "media"))){
				$fn = "upload_{$this->do}";
				return $this->$fn();			
			}else{
				return $this->_404();
			}
		}
		// Set meta data
		Main::set("title",e("Upload or Submit Media"));
		Main::set("description", e("Upload or submit media and started generating views and clicks."));
		Main::add("{$this->config["url"]}/static/js/tagsinput.min.js","script", FALSE);
		Main::add("<script>$('.tags').tagsInput({'width':'100%', 'height': '64px','minChars' : 3});</script>","custom", TRUE);
		Main::plug("upload_header");		

		// Show Template
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();
	}	
			/**
			 * Upload URL
			 * @author Emrul
			 * @since  1.6
			 */
			private function upload_url(){
				// Validate CSRF Token
				if(!Main::validate_csrf_token($_POST["token"])){
					return Main::redirect(Main::href("user".$this->user->username,"",FALSE),array("danger",e("Invalid token. Please try again.")));
				}
				// Get Media
				include(ROOT."/includes/Media.class.php");
      	$media = new Media(
                      array(
                          "yt_api" => $this->config["yt_api"],
                          "vm_api" => $this->config["vm_api"]
                          )
                      );				
				$data = $media->import($_POST["url"]);
				if(!isset($data->src)) return Main::redirect(Main::href("user".$this->user->username,"",FALSE),array("danger", e("For some reason this video cannot be imported.")));				

				if(!empty($_POST["title"]) || strlen($_POST["title"]) > 3){
					$data->title = Main::clean($_POST["title"], 3, TRUE);
				}
				if(!empty($_POST["description"]) || strlen($_POST["description"]) > 3){
					$data->desc = Main::clean($_POST["description"], 3, TRUE);
				}
				if(!empty($_POST["tags"]) || strlen($_POST["tags"]) > 3){
					$data->tag = Main::clean($_POST["tags"], 3, TRUE);
				}
				// Check to make sure title is set
				if(empty($data->title)){
					return Main::redirect(Main::href("upload?url={$_POST["url"]}","",FALSE),array("danger", e("Please add a title")));				
				}
				// Check if video is in database
				if($this->db->get("media","title=? OR url=?","",array($data->title,Main::slug(Main::clean($data->title,3))))){
					return Main::redirect(Main::href("user".$this->user->username,"",FALSE),array("danger",e("This video already exists in the database.")));
				}				
				$unique = $this->uniqueid();
        // Prepare array of data  
        $values = array(
          ':type' => types($_POST["type"]) ? $_POST["type"] : 'video', 
          ':catid'=> is_numeric($_POST["category"]) ? $_POST["category"] : "1", 
          ':uniqueid'=> $unique, 
          ':title'=> Main::clean($data->title,3), 
          ':url'=> Main::slug(Main::clean($data->title,3)),
          ':description'=> Main::clean($data->desc,3,TRUE), 
          ':source' => Main::clean($_POST["url"], 3, TRUE),
          ':embed'=> $data->code, 
          ':userid'=> $this->user->id,
          ':nsfw' => (in_array($_POST["nsfw"], array("0", "1")) ? $_POST["nsfw"] : "0"),
          ':tags'=> $data->tag,
          ':approved' => ($this->config["autoapprove"]) ? "1" : "0"
				);
        if($_POST["type"] == "picture" && $this->config["local_thumbs"] && in_array(Main::extension($_POST["url"], FALSE), array("jpg","gif","png"))){
          // Copy Image
          $file = md5($unique).".".Main::extension($_POST["url"], FALSE);
          copy($_POST["url"], MEDIA."/$file");
          $values[":file"] = $file;
          $thumb = md5($unique.rand(0,999)).".".Main::extension($_POST["url"], FALSE);
          Main::generatethumb(MEDIA."/".$file,THUMBS."/".$thumb,450);
          $values[":thumb"] = $thumb;
        }else{				
					// Thumbnail
	        if(!$this->config["local_thumbs"]){
	          $values[":ext_thumb"] = $data->thumb;
	        }else{
	        	copy($data->thumb, THUMBS."/$unique.jpg");
						$values[":thumb"] = "$unique.jpg";
	        }		
        }		
	      if($this->config["s3"]=="1"){
					include(ROOT."/includes/Upload.class.php");
	      	$s3 = new Upload($this->config["s3_region"], $this->config["s3_public"], $this->config["s3_private"], $this->config["s3_bucket"]);	      	
	      	 if(isset($values[":file"])){
		         $values[":link"] = $s3->save($values[":file"],MEDIA."/$file");
		         unlink(MEDIA."/$file");	
		         unset($values[":file"]);
	      	 }
	      	 if(isset($values[":thumb"])){
		         $values[":ext_thumb"] = $s3->save($values[":thumb"],THUMBS."/".$thumb);
		         unlink(THUMBS."/".$thumb);
		         unset($values[":thumb"]);
	      	 }
	      }	              
        // Insert data to database
        if($this->db->insert("media", $values)){      
        	$this->db->update("setting","value = value + 1",array("config"=>"?"),array("count_media"));
        	if($this->config["autoapprove"]){
			      // Add Points
			      if($this->config["points"]){
			        // Check if user has already been awarded points for this media
			        $this->db->insert("point", array(":action" => "submit", ":userid" => $this->user->id, ":actionid" => $this->db->lastID(), ":point" => $this->config["amount_points"]["submit"]));
			        $this->db->update("user", "points = points+{$this->config["amount_points"]["submit"]}", array("id" =>  $this->user->id));
			      }          		
        	}
       		return Main::redirect("user".$this->user->username, array("success", e("Thank you for submitting a media. It should be displayed soon.")));
        }
			}
			/**
			 * Upload Media
			 * @author Emrul
			 * @since  1.6
			 */
			private function upload_media(){
				if(!$this->config["upload"]) return Main::redirect(Main::href("user".$this->user->username,"",FALSE),array("danger",e("An unexpected error has occured.")));
				// Validate CSRF Token
				if(!Main::validate_csrf_token($_POST["token"])){
					return Main::redirect(Main::href("user".$this->user->username,"",FALSE),array("danger",e("Invalid token. Please try again.")));
				}
				$error = NULL;

				if(empty($_POST["title"]) || strlen($_POST["title"]) < 3 ){
					$error .= "<span>".e("Title must contain at least 3 characters.")."</span>"; 
				}

				// Check if video is in database
				if($this->db->get("media","title=? OR url=?","",array($_POST["title"],Main::slug($_POST["title"])))){
					return Main::redirect(Main::href("user".$this->user->username,"",FALSE),array("danger",e("This media seems to already exist in the database.")));
				}			
				$unique = $this->uniqueid();
        $formats = formats();				
				// Validate file
				if(isset($_FILES["thumb"]) && !empty($_FILES["thumb"]["tmp_name"])){
          if(isset($_FILES["thumb"]) && empty($_FILES["thumb"])) $error.="<p>".e("You forgot to select a thumbnail to upload").".</p>";
          elseif($_FILES["thumb"]["size"] > 500*1024) $error .= "<p>".e("The thumbnail size must not exceed")." 500 KB</p>";
				}else{
					if($_POST["type"] !== "picture"){
						$error .= "<span>".e("Please select a thumbnail to upload.")."</span>";
					}
				}

				if(isset($_FILES["upload"]) && !empty($_FILES["upload"]["tmp_name"])){
					if(!$formats[$_FILES["upload"]["type"]]) $error .= "<span>".e("This media format is not currently supported.")."</span>";
					if($_FILES["upload"]["size"] > $this->config["max_size"]*1024*1024) $error .= "<span>".e("The media size must not exceed")." {$this->config["max_size"]} MB</span>";	
					$filename = md5($unique.rand(0,20000)).".".$formats[$_FILES["upload"]["type"]];
				}else{
					$error .= "<span>".e("Please select a media file to upload.")."</span>";
				}		

				if(!is_null($error)) {
					return Main::redirect(Main::href("user".$this->user->username,"",FALSE),array("danger",$error));
				}
        // Prepare array of data  
        $values = array(
          ':type' => types($_POST["type"]) ? $_POST["type"] : 'video', 
          ':catid'=> is_numeric($_POST["category"]) ? $_POST["category"] : "1", 
          ':uniqueid'=> $unique, 
          ':title'=> Main::clean($_POST["title"],3), 
          ':url'=> Main::slug(Main::clean($_POST["title"],3)),
          ':description'=> Main::clean($_POST["description"],3,TRUE),
          ':file' => $filename,
          ':userid'=> $this->user->id,
          ':nsfw' => (in_array($_POST["nsfw"], array("0", "1")) ? $_POST["nsfw"] : "0"),
          ':tags'=> $_POST["tags"],
          ':release_date'=> $_POST["release_date"],
          ':approved' => ($this->config["autoapprove"]) ? "1" : "0"
				);
				$thumb = md5($unique.rand(0,999)).".".(!empty($_FILE["thumb"]["tmp_name"]) ? $formats[$_FILES["thumb"]["type"]] : $formats[$_FILES["upload"]["type"]]);
				$values[":thumb"] = $thumb;

				if(!empty($_FILES["upload"]["tmp_name"])){
					move_uploaded_file($_FILES["upload"]['tmp_name'], MEDIA."/$filename");
				}
				// Thumbnail					
        if($_POST["type"] == "picture" && $this->config["local_thumbs"] && $formats[$_FILES["upload"]["type"]] && empty($_FILES["thumb"]["tmp_name"])){
          // Generate THUMBS	          
          Main::generatethumb(MEDIA."/".$filename,THUMBS."/".$thumb,560);	          
        }else{
        	move_uploaded_file($_FILES["thumb"]['tmp_name'], THUMBS."/".$thumb);
        }	

	      if($this->config["s3"]=="1"){
					include(ROOT."/includes/Upload.class.php");
	      	$s3 = new Upload($this->config["s3_region"], $this->config["s3_public"], $this->config["s3_private"], $this->config["s3_bucket"]);	      	
	      	 if(isset($values[":file"])){
		         $values[":link"] = $s3->save($values[":file"],MEDIA."/$filename");
		         unlink(MEDIA."/$filename");	
		         unset($values[":file"]);
	      	 }
	      	 if(isset($values[":thumb"])){
		         $values[":ext_thumb"] = $s3->save($values[":thumb"],THUMBS."/".$thumb);
		         unlink(THUMBS."/".$thumb);
		         unset($values[":thumb"]);
	      	 }
	      }	 
		
        if($this->db->insert("media", $values)){
        	$this->db->update("setting","value = value + 1",array("config"=>"?"),array("count_media"));
       		if($this->config["autoapprove"]){
			      // Add Points
			      if($this->config["points"]){
			        // Check if user has already been awarded points for this media
			        $this->db->insert("point", array(":action" => "submit", ":userid" => $this->user->id, ":actionid" => $this->db->lastID(), ":point" => $this->config["amount_points"]["submit"]));
			        $this->db->update("user", "points = points+{$this->config["amount_points"]["submit"]}", array("id" =>  $this->user->id));
			      }          		
        	}					
					return Main::redirect(Main::href("user/".$this->user->username,"",FALSE),array("success",e("This media has been successfully uploaded.")));
        }	

			}
	/**
	 * User Page
	 * @since 1.0
	 */
	protected function user(){
		// Define User Scope
		$this->isUser = TRUE;
		// include User Library
		include(ROOT."/includes/User.class.php");		
		return new User($this->config, $this->db, array($this->do, $this->id));
	}
	/**
	 * Playlist
	 * @since 1.5.1
	 */
	protected function playlist(){
		// Filter ID & Do
		$this->filter($this->id);	
		if(empty($this->do)) return $this->_404();
		// Get List
		$playlist = $this->db->get("playlist",array("uniqueid" => "?"), array("limit" => 1), array($this->do));
		// Check if public
		if(!$playlist->public && !$this->admin()){
			if(!$this->logged() || ($this->logged() && $this->user->id !== $playlist->userid)) return $this->_404();
		}
		// Get Media List
		$media = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}toplaylist.mediaid as mediaid,{$this->config["prefix"]}toplaylist.date as pdate FROM `{$this->config["prefix"]}toplaylist` INNER JOIN `{$this->config["prefix"]}media` ON {$this->config["prefix"]}media.id = mediaid"), array("playlistid" => $playlist->id),array("limit" => 50, "order" => "pdate"));

		$videos = $this->listMedia($media, NULL, array("playlist" => "?playlist={$playlist->uniqueid}"));	
		
		// Meta Tags
		$title = $playlist->name;
		$pagination = NULL;
		if(isset($media[0])){
			$nofilter = "<a href='{$media[0]->url}?playlist={$playlist->uniqueid}&index=1' class='btn btn-primary'>".e("Play All")."</a>";
		}
		Main::set("title", $playlist->name);
		Main::set("description", $playlist->description);

		$this->header(); 
		include($this->t("browse"));
		$this->footer();				
	}
	protected function videoedit(){
		if($_POST){
			$data = array(
				":title"=> Main::clean($_POST["title"],3,FALSE),
				":description"=> Main::clean($_POST["description"],4),
				":release_date"=> Main::clean($_POST["release_date"],4),
			);
			if($this->db->update("media","",array("id"=> $this->do), $data)) {
				return Main::redirect(Main::href("user/" . $this->user->username, "", FALSE), array("success", e("This media has been successupdated.")));
			}

		}
		else {
			if (empty($this->id)) {
				if ($media = $this->db->get("media", array("uniqueid" => "?"), array("limit" => 1), array($this->do))) {

//				return Main::redirect("videoedit/{$media->uniqueid}","",301);
				}
			}
			// Define ViewPage
			$this->isView = TRUE;
			// Show Template
			$this->header();
			include($this->t(__FUNCTION__));
			$this->footer();
		}

	}
	/**
	 * View Media
	 * @since 1.5
	 */
	protected function view(){		
		if(empty($this->id)){
			if($media = $this->db->get("media",array("url" => "?"),array("limit" => 1), array($this->do))){
				return Main::redirect("view/{$media->url}/{$media->uniqueid}","",301);
			}
		}
		// Define ViewPage
		$this->isView = TRUE;		
		// Get Media
		if($this->admin()){
			$media = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"),array("uniqueid" => "?"),array("limit" => 1), array($this->id));
			if($media && !$media->approved){
				Main::add("<script>$('.video-player').before('<div class=\'alert alert-info\'><strong>Admin Preview</strong> This media is currently not approved! This is just a preview of what it would look like if you approve it.</div>')</script>", "custom", TRUE);
			}
		}else{
			$media = $this->getMedia(array("url"=>$this->do,"limit"=>1));
		}
		// Show 404
		if(!$media || empty($this->do)){ 
			return $this->_404("media");
		}
		// Update Counter if is human
		if($this->view_update($media->id)) $media->views++; 
		// Format Media
		$media = $this->formatMedia($media, FALSE);
		// Check if NSFW and user is allowed to see
		if($media->nsfw){
			if(!$this->logged()) return Main::redirect(Main::href("user/login", "", FALSE), array("danger", e("This media might contain explicit content. You will need to login first.")));
			if($this->logged() && !$this->user->nsfw) return Main::redirect(Main::href("user/settings", "", FALSE), array("danger", e("Please enable NSFW option in the privacy settings.")));
		}
		// Get Author's info
		$author = $this->db->get(array("count" => "username,profile,avatar,email,subscribers", "table" => "user"), array("id" => $media->userid), array("limit" => 1));
		$author->avatar = $this->avatar($author);
		$author->profile = json_decode($author->profile);
		if(empty($author->profile)) $author->profile = new stdClass();
		if(!isset($author->profile->name) || empty($author->profile->name)) $author->profile->name = ucfirst($author->username);
		// Category
		$category = $this->db->get("category", array("id" => $media->catid), array("limit" => 1));

		if($category->parentid > 0){
			$parent = $this->db->get("category", array("id" => $category->parentid), array("limit" => 1));
		}
		// Add Like/Dislike Status + Favorite + Sub
		if($this->logged()){
			if($rating = $this->db->get("rating", array("userid" => $this->user->id, "mediaid" => $media->id), array("limit" => 1))){
				if($rating->rating == "liked"){
					Main::add("<script>$('#this-like-{$media->id}').addClass('active').attr('data-content','".e("Unlike")."');</script>","custom",TRUE);
				}
				if($rating->rating == 'disliked'){
					Main::add("<script>$('#this-dislike-{$media->id}').addClass('active').attr('data-content','".e("Undislike")."');</script>","custom",TRUE);
				}
			}
			if($this->db->get("favorite", array("userid" => $this->user->id, "mediaid" => $media->id), array("limit" => 1))){
				Main::add("<script>$('#this-addtofav').addClass('active').attr('data-content','".e("Remove from favorites")."').text('".e("Remove from favorites")."');$('#this-addto').addClass('active');</script>","custom",TRUE);
			}
			if($this->db->get("subscription", array("userid" => $this->user->id, "authorid" => $media->userid))){
				Main::add("<script>$('#this-subscribe').addClass('active').attr('data-content','".e("Unsubscribe")."');</script>","custom",TRUE);
			}		
			$playlists = $this->db->get("playlist", array("userid" => $this->user->id));
			$this->user->playlists = "";
			foreach ($playlists as $playlist) {
				if($this->db->get("toplaylist", array("mediaid" => $media->id, "playlistid" => $playlist->id))){
					$this->user->playlists .= "<li id='playlist-{$playlist->id}'><a href='#' class='active this-action' data-action='addto_playlist' data-data='[\"id\":{$media->id},\"check\":{$playlist->id}]'>{$playlist->name} ({$playlist->num})</a></li>";
				}else{
					$this->user->playlists .= "<li id='playlist-{$playlist->id}'><a href='#' class='this-action' data-action='addto_playlist' data-data='[\"id\":{$media->id},\"check\":{$playlist->id}]'>{$playlist->name} ({$playlist->num})</a></li>";
				}
			}
		}
		// Plug Admin control links
		$this->admin_menu_html = build_menu(array(
				array("href" => Main::ahref("media/edit/{$media->id}"), "text" => "Edit Media", "icon" => "edit"),
				array("href" => Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}"), "text" => "Delete", "icon" => "times-circle")
			), TRUE);

		// Get next and previous media
		if($next = $this->db->get("media", array("id" => $media->id + 1, "approved" => "1"), array("limit" => 1))){
			$next = $this->formatMedia($next);
			Main::add('<a href="'.$next->url.'" class="arrow-next"><span class="fa fa-chevron-right"></span></a>',"custom", TRUE);
		}			
		if($prev = $this->db->get("media", array("id" => $media->id - 1, "approved" => "1"), array("limit" => 1))){
			$prev = $this->formatMedia($prev);
			Main::add('<a href="'.$prev->url.'" class="arrow-prev"><span class="fa fa-chevron-left"></span></a>',"custom", TRUE);
		}	
		// Update Embed Code to take into account the playlist
		if(isset($_GET["playlist"]) && isset($_GET["index"]) && is_numeric($_GET["index"])){
			preg_match_all("~src='(.*)' frameborder~", $media->player, $src);
			if(isset($src[1][0])) $media->player = str_replace($src[1][0], "{$src[1][0]}?playlist=".Main::clean($_GET["playlist"],3,TRUE)."&index=".Main::clean($_GET["index"],3,TRUE), $media->player);
		}
		// Set meta data
		Main::set("title",$media->title);
		Main::set("description", Main::truncate(Main::clean($media->description, 3, TRUE),200));
		Main::set("image", $media->thumb);
		Main::set("url", $media->url);
		Main::set("type", $media->type);
		// Plug-in Header
		Main::plug("view_header", array("id" => $media->id, "type" => $media->type));		
		// Fix Description
		$media->description = nl2br(str_replace('"',"'",$media->description));		

		// Show Template
		$this->header();

		if($media->type == "post"){
			include($this->t("post"));
		}else{
			include($this->t(__FUNCTION__));
		}

		$this->footer();			
	}
		/**
		 * Update view counter
		 * @author Emrul
		 * @since  1.0
		 */
		private function view_update($id, $embed = FALSE){
			// Prevents Bots
			if(Main::bot()) return FALSE;
			// Check user visited recently
			if(Main::cookie("media_{$id}")) return FALSE;
			// Update clicks
			if($this->db->update("media",array("views"=>"views+1"),array("id"=>":a"),array(":a"=>$id))){
				// Plug-in
				Main::plug("update_media_stats", array("id" => $id));
				// Set cookie to prevent flooding valid for XX minutes
				Main::cookie("media_{$id}",rand(10000,99999), 10);
			}
			return TRUE;
		}
		/**
		 * Get Comments
		 * @author Emrul
		 * @since  1.6
		 */
		private function comments($id, $url, $count, $type = "media", $limit = 20){
			if(!$this->config["comments"]) return FALSE;
			// Plugin Comments
			$plug = Main::plug("comments", array("url" => $url, "count" => $count));
			if($plug) return $plug;
			// Merge Comments
			if($this->config["merge_comments"]){
					$html = "<div class='panel panel-default' id='comments'>";
						$html .="<div class='panel-heading'><h3><i class='fa fa-comments'></i> ".e('Comments')."</h3></div>";
							$html .= '<div class="panel-body">
													<div class="row">												
													  <div class="col-sm-4 tabs">
													    <a href="#comments_system" class="btn btn-block btn-primary">'.e('Comments').'</a>
													  </div>
													  <div class="col-sm-4 tabs">
													    <a href="#comments_facebook" class="btn btn-block btn-facebook">Facebook</a>
													  </div>
													  <div class="col-sm-4 tabs">
													    <a href="#comments_disqus" class="btn btn-block btn-success">Disqus</a>
													  </div>
												  </div>
												</div>';
						$html .= $this->comments_system($id, $url, $count, $type = "media", $limit = 20, $tabbed = TRUE);
						$html .= $this->comments_facebook($url, $tabbed = TRUE);
						$html .= $this->comments_disqus($url, $tabbed = TRUE);
					$html .= "</div>";
				return $html;
			}
			$html = "<div class='panel panel-default' id='comments'>";
				$html .="<div class='panel-heading'>".e('Comments')."</div>";						
			// Check and switch system
			if($this->config["comment_sys"]!="system" && in_array($this->config["comment_sys"], array("disqus","facebook"))) {
				$fn = "comments_{$this->config["comment_sys"]}";
				$html .=$this->$fn($url);
			}else{
				// Return System
				$html .= $this->comments_system($id, $url, $count, $type = "media", $limit = 20);
			}
			$html .= "</div>";
			return $html;
		}
					/**
					 * System Comments
					 * @since  1.5
					 */
	private function comments_system($id, $url, $count, $type = "media", $limit = 20, $tabbed = FALSE){
						// Get Comments from database
						$comments = $this->db->get(array("custom" => "{$this->config["prefix"]}comment.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.avatar, {$this->config["prefix"]}user.email as email FROM `{$this->config["prefix"]}comment` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}comment.userid"),array("mediaid" => $id, "parentid" => 0, "type" => $type),array("limit"=> ($this->page - 1)*$limit.", $limit", "count" => TRUE, "order"=>"date"));
				    if(($this->db->rowCount%$limit)<>0) {
				      $max = floor($this->db->rowCount/$limit)+1;
				    } else {
				      $max = floor($this->db->rowCount/$limit);
				    }   			
						// Form HTML
						// $html ="<div class='panel panel-default".($tabbed ? " tabbed" : "")."' id='comments_system'>
						//     			{$this->comments_form($id)}						    			
						//           <div class='panel-body'>
						//           <h2><span>{$count} ".(($count == 0 || $count > 1) ? e('Comments') : e("Comment"))."</span></h2>";	
						$html ="<div class='panel panel-default".($tabbed ? " tabbed" : "")."' id='comments_system'>
						    			{$this->comments_form($id)}						    			
						          <div class='panel-body'>
						         ";						          
								$html.='<ul class="media-list comments">';
								foreach ($comments as $comment) {
									if(empty($comment->username)) continue;
									// Get Child Comments
									$childs = $this->db->get(array("custom" => "{$this->config["prefix"]}comment.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.avatar, {$this->config["prefix"]}user.email as email FROM `{$this->config["prefix"]}comment` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}comment.userid"),array("mediaid" => $id, "parentid" => $comment->id, "type" => "media"),array("limit"=> "5", "order"=>"date"));
									// Generate @user tag
									$comment->body = $this->at($comment->body, Main::href("user/"));
									$comment->body = Main::hash($comment->body, Main::href("search/"));
									$comment->body = Main::filter($comment->body, $this->config["comment_blacklist"]);

									$html.="<li class='media' id='comment-{$comment->id}'>
						                <a class='pull-left' href='".Main::href("user/{$comment->username}")."'>
						                  <img class='media-object' src='{$this->avatar($comment)}' width='50' alt=''>
						                </a>
						                <div class='media-body'>
						                  <h4 class='media-heading'>
						                    <a href='".Main::href("user/{$comment->username}")."' class='author'>".ucfirst($comment->username)."</a>
						                    <span>".Main::timeago($comment->date)."</span>
																".($this->admin() ? "<a href='".Main::ahref("comments/delete/{$comment->id}").Main::nonce("delete-comment-{$comment->id}")."' target='_blank' class='pull-right delete' title='".e("Delete")."'><span class='fa fa-remove'></span></a>" : "")."			                    
						                    ".($this->logged() ? "<a href='#flag' class='this-action pull-right' data-action='report' data-data='[\"id\":{$comment->id},\"check\":\"comment\"]' title='".e("Flag")."'><span class='fa fa-flag'></span></a>                    
						                    											<a href='#reply' class='reply pull-right' data-parent='{$comment->id}' data-user='".ucfirst($comment->username)."' title='".e("Reply")."'><span class='fa fa-reply'></span></a>" : "")."
						                    ".($this->admin() ? "<a href='".Main::ahref("comments/edit/{$comment->id}")."' target='_blank' class='pull-right' title='".e("Edit")."'><span class='fa fa-edit'></span></a>" : "")."			                    
						                  </h4>
						                  {$comment->body}";
										if($childs){
											foreach ($childs as $child) {			
												if(empty($child->username)) continue;
												$child->body = $this->at($child->body, Main::href("user/"));
												$child->body = Main::hash($child->body, Main::href("search/"));
												$child->body = Main::filter($child->body, $this->config["comment_blacklist"]);

												$html.="<div class='media' id='comment-{$child->id}'>
									                <a class='pull-left' href='".Main::href("user/{$child->username}")."'>
									                  <img class='media-object' src='{$this->avatar($child)}' width='35' alt=''>
									                </a>
									                <div class='media-body'>
									                  <h4 class='media-heading'>
									                    <a href='".Main::href("user/{$child->username}")."' class='author'>".ucfirst($child->username)."</a>
									                    <span>".Main::timeago($child->date)."</span>
																			".($this->admin() ? "<a href='".Main::ahref("comments/delete/{$child->id}").Main::nonce("delete-comment-{$child->id}")."' target='_blank' class='pull-right delete' title='".e("Delete")."'><span class='fa fa-remove'></span></a>" : "")."						                    
									                    <a href='#flag' class='this-action pull-right' data-action='report' data-data='[\"id\":{$child->id},\"check\":\"comment\"]' title='".e("Flag")."'><span class='fa fa-flag'></span></a>
									                    ".($this->admin() ? "<a href='".Main::ahref("comments/edit/{$child->id}")."' target='_blank' class='pull-right' title='".e("Edit")."'><span class='fa fa-edit'></span></a>" : "")."              
									                  </h4>
									                  {$child->body}
									                </div>
								                </div>";									
											}					
										}
						      $html .="</div></li>";	
								}						
								$html.='</ul><!--/.comment-list -->';
							if($max > 1){
								$html.= Main::pagination($max, $this->page, $url."?page=%d");
							}
						$html.='</div></div>';
						return $html;						
					}
					/**
					 * Comments Form
					 * @author Emrul
					 * @since  1.5
					 */
					private function comments_form($id){
						// No system
						if(!$this->logged()) return FALSE;
						// Return HTML
						return '<div class="panel-body">											
											<form id="comment-form" method="post" action="'.Main::href("server").'">
												<div class="return-data"></div>
												<div class="row">
													<div class="col-sm-1 hidden-xs hidden-sm">
														<img src="'.$this->avatar($this->user).'" alt="">
													</div>
													<div class="col-sm-11 col-xs-12">
														<textarea name="comment" class="form-control" placeholder="'.e("Leave a comment here...").'"></textarea>
														<input type="hidden" name="action" value="comment">
														<input type="hidden" name="token" value="'.$this->config["public_token"].'">
														<input type="hidden" name="media" value="'.$id.'">
														<input type="hidden" name="parentid" value="0" id="comment-parentid">
														<span class="replyto"></span>
														<button type="submit" class="btn btn-primary pull-right">'.e("Submit").'</button>
													</div>
												</div>
									   </form><!--/.comment-form-->						
										</div>';
					}
					/**
					 * Generate Disqus
					 * @author Emrul
					 * @since  1.0
					 */
					public function comments_disqus($url='', $tabbed = FALSE){
						if(empty($this->config["disqus_username"])) return FALSE;
						// Return Disqus
						return "<div class='panel-body".($tabbed ? " tabbed" : "")."' id='comments_disqus'><div id=\"disqus_thread\"></div></div>
				      <script type=\"text/javascript\">
				          var disqus_shortname = '{$this->config["disqus_username"]}';
				          var disqus_url = '$url';
				          (function() {
				              var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
				              dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
				              (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
				          })();
				      </script>";	
				  }
				  /**
				   * Generate Facebook Comments
				   * @author Emrul
				   * @since  1.0
				   */
					public function comments_facebook($url='', $tabbed = FALSE){
						$html = '<div class="panel-body'.($tabbed ? " tabbed" : "").'" id="comments_facebook"><div id="fb-root"></div><div class="fb-comments" data-href="'.$url.'" data-num-posts="10" data-colorscheme="light" data-width="100%"></div></div>
				      <script>(function(d, s, id) {
				      var js, fjs = d.getElementsByTagName(s)[0];
				      if (d.getElementById(id)) return;
				      js = d.createElement(s); js.id = id;
				      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
				      fjs.parentNode.insertBefore(js, fjs);
				      }(document, \'script\', \'facebook-jssdk\'));</script>';
			      return $html;
					}
	/**
	 * Redirect Shortlink
	 * @author Emrul
	 * @since  1.0
	 */
	private function v(){
		$this->filter($this->id);
		if(empty($this->do) || !$media = $this->db->get("media",array("uniqueid" => "?"),array("limit" => 1), array($this->do))){
			return $this->_404();
		}
		return Main::redirect("view/{$media->url}","","403");
	}
	/**
	 * Embed Page
	 * @since 1.6
	 */
	protected function embed(){
		$this->filter($this->id);
		// Get Media
		if(!$media = $this->db->get('media',array("uniqueid"=> "?"),array("limit"=>1), array($this->do))){
			echo "This videos has been deleted or is not available. Please visit us at <a href='{$this->config["url"]}'>{$this->config["url"]}</a> for more videos.";
			exit;
		}
		$loop = FALSE;
		$autoplay = FALSE;
		if(isset($_GET["loop"]) && $_GET["loop"] == "1"){
			$loop = "loop";
		}
		// Format Media
		$media = $this->formatMedia($media);	

		// Check if subscibe is required
		if($media->subscribe){
			if(!$this->logged() || !$this->db->get("subscription", array("userid" => $this->user->id, "authorid" => $media->userid))){				
				return $this->embed_subscribe($media);
			}
		}	
		// Plug-in Header
		Main::plug("embed_header", array("media" => $media));	

		// Get playlist
		if(isset($_GET["playlist"]) && isset($_GET["index"]) && is_numeric($_GET["index"])){
			$autoplay = "autoplay";
			$playlist = $this->db->get("playlist", array("uniqueid" => "?"), array("limit" => "1"),array($_GET["playlist"]));
			if($playlist){
				$list = $this->db->get("toplaylist", array("playlistid" => $playlist->id), array("order" => "date"));
				if(isset($list[$_GET["index"]])) {
					$next = $this->db->get("media", array("id" => "?"), array("limit" => "1"), array($list[$_GET["index"]]->mediaid));
					if($next) {
						$next = $this->formatMedia($next);
						$next->url = $next->url."?playlist={$playlist->uniqueid}&index=".($_GET["index"] + 1);
					}
				}
			}
		}		

		// Generate code
		if(empty($media->file) && empty($media->link) && empty($media->source)){
			$media->embed = $media->embed;
		}else{
			// Format Videos
			if($media->type == "video" || $media->type == "music"){ // Use code for only video and music

				if($this->config["player"] == "videojs"){ // Videos JS
					// Add VideoJS Library
					Main::add("{$this->config["url"]}/static/player/video-js.css","style",FALSE); // CSS
					Main::add("{$this->config["url"]}/static/player/video.js","script",FALSE); // JS
					Main::add("{$this->config["url"]}/static/player/videojs.loopbutton.css","style",FALSE); // CSS					
					Main::add("{$this->config["url"]}/static/player/videojs.loopbutton.min.js","script",FALSE); // JS
					Main::add("<script>
											videojs.options.flash.swf = '{$this->config["url"]}/static/player/video-js.swf';
										</script>","custom",FALSE); // SWF

					// Check if source is Youtube
					if(!empty($media->source)){
						// If Youtube
						$domain = Main::domain($media->source, FALSE, FALSE);
						// VideoJS Youtube Player + Library
						if($domain == "youtube"){
							Main::add("{$this->config["url"]}/static/player/video-js.youtube.js","script",FALSE);
							$media->embed = '<video id="video-player" src="" class="video-js vjs-default-skin vjs-big-play-centered" '.$loop.' '.$autoplay.' controls preload="auto" width="640" height="360" data-setup=\'{ "techOrder": ["youtube"], "src": "'.$media->source.'" }\'></video>';				
						}				
					}		
					// Check if file is not empty
					if(!empty($media->file)){
						if(Main::extension($media->file) == ".mp3"){
							// Videos JS
							$media->embed = '<audio id="video-player" class="video-js vjs-default-skin vjs-big-play-centered" '.$loop.' '.$autoplay.' controls preload="auto" width="640" height="360" poster="'.$media->thumb.'" data-setup="{}">
														 		<source src="'.$this->config["url"].'/content/media/'.$media->file.'" type="audio/mp3" />
															</audio>';
						}else{
							// Videos JS
							$media->embed = '<video id="video-player" class="video-js vjs-default-skin vjs-big-play-centered" '.$loop.' '.$autoplay.' controls preload="auto" width="640" height="360" poster="'.$media->thumb.'" data-setup="{}">
														 		<source src="'.$this->config["url"].'/content/media/'.$media->file.'" type="video/mp4" />
															</video>';
						}
					}			
					// Check if link is not empty
					if(!empty($media->link)){
						if(Main::extension($media->file) == ".mp3"){
							// Videos JS
							$media->embed = '<audio id="video-player" class="video-js vjs-default-skin vjs-big-play-centered" '.$loop.' '.$autoplay.' controls preload="auto" width="640" height="360" poster="'.$media->thumb.'" data-setup="{}">
														 		<source src="'.$media->link.'" type="audio/mp3" />
															</audio>';
						}else{						
							$check = parse_url($media->link);
							if($check["scheme"] == "rtmp"){
								// Videos JS
								$media->embed = '<video id="video-player" class="video-js vjs-default-skin vjs-big-play-centered" '.$loop.' '.$autoplay.' controls preload="auto" width="640" height="360" poster="'.$media->thumb.'" data-setup="{}">
															 		<source src="'.$media->link.'" type="rtmp/mp4" />
																</video>';
							}else{
								// Videos JS
								$media->embed = '<video id="video-player" class="video-js vjs-default-skin vjs-big-play-centered" '.$loop.' '.$autoplay.' controls preload="auto" width="640" height="360" poster="'.$media->thumb.'" data-setup="{}">
															 		<source src="'.$media->link.'" type="video/mp4" />
																</video>';
							}
						}
					}
				}elseif($this->config["player"] == "flowplayer"){ // FlowPlayer
					// Add FlowPlayer Library
					Main::add("{$this->config["url"]}/static/player/flowplayer/skin/minimalist.css","style",FALSE); // CSS
					Main::add("{$this->config["url"]}/static/player/flowplayer/flowplayer.min.js","script",FALSE); // JS
					Main::add("<script>flowplayer.conf.embed = false;</script>","custom",FALSE); // Disable Embed
					// Check if file is not empty
					if(!empty($media->file)){			
							$media->embed = '<div class="flowplayer" data-swf="'.$this->config["url"].'/static/player/flowplayer/flowplayer.swf">
													      <video '.$loop.' '.$autoplay.' controls preload="auto" poster="'.$media->thumb.'">
													         <source type="video/mp4" src="'.$this->config["url"].'/content/media/'.$media->file.'">
													      </video>
													  	</div>';																			
					}			
					// Check if link is not empty
					if(!empty($media->link)){
							$media->embed = '<div class="flowplayer" data-swf="'.$this->config["url"].'/static/player/flowplayer/flowplayer.swf">
												      <video '.$loop.' '.$autoplay.' controls preload="auto" poster="'.$media->thumb.'">
												         <source type="video/mp4" src="'.$media->link.'">
												      </video>
												  	</div>';																		
					}					
				}
			}
		}					
		// Remove ad
		//Main::add('<script type="text/javascript">$(document).ready(function(){ var count = '.$this->config["preroll_timer"].';var countdown = setInterval(function(){$(".ad-preroll p span").html(count);if (count < 1) {clearInterval(countdown);$(".ad-preroll").hide();videojs("video-player").ready(function(){var myPlayer = this;myPlayer.play();});}count--;}, 1000); });</script>',"custom",FALSE);							
    echo '<html>
		        <head>
		        	<title>'.$media->title.'</title>
		          <style>
		          body{ margin: 0; padding: 0; font-family: "Helvetica", Arial sans-serif;}
		          video,iframe,embed,#video-player,.flowplayer{width: 100% !important; height: 100% !important;}
		          .flowplayer{background:#000};
		          .preroll{position: relative}
		          .ad-preroll{color: #fff; background: #000; background: rgba(0,0,0,0.5);width: 100% !important; height: 100% !important;border-radius: 2px; text-align:center;padding-top: 5%;position: absolute; z-index: 9999999; display: none;}
		          .play-screen{position: absolute; top: 0; left: 0; width: 100% !important; height: 100% !important; z-index: 9999999;cursor:pointer;}
		          .logo{position: absolute; top: 5px; right: 5px;  z-index: 9999999999; opacity: 0.8;}
		          .logo img{max-width: 50px; max-height:50px;}
		          .logo:hover{opacity: 1}
							a.skipad {text-decoration: none;color: #fff;background: #000;padding: 5px;background-color: rgba(0,0,0,0.65);border: 1px solid #fff;display: none;position: absolute;right: 10px;bottom: 50px;font-size: 11px;}
		          '.(isset($next) ? '.play-screen{display:none;}' : '').'			
		          '.(!empty($this->config["color"]) ? '.vjs-default-skin .vjs-play-progress, .vjs-default-skin .vjs-volume-level{background-color: #d8232a !important}':'').'
		          </style>
		          <script type="text/javascript" src="'.$this->config["url"].'/static/js/jquery.min.js"></script>
		          <script>
		          	$(document).ready(function(){
									if($("#video-player").length > 0){
										videojs("video-player").ready(function(){
											var player = this;
											'.(isset($next) ? 'player.on("ended", function(){ top.location.href = \''.$next->url.'\';});' : '').'	
											$(".skipad").click(function(e){
												e.preventDefault();
													$(".ad-preroll").hide();
													player.play();													
											});
											$(".play-screen").click(function(e){
												e.preventDefault();
												$(this).hide();
												$(".ad-preroll").show();
												var count = "'.$this->config["preroll_timer"].'";
												var countdown = setInterval(function(){
													$(".ad-preroll p span").html(count);
													if (count < 1) {
														clearInterval(countdown);
														$(".ad-preroll p").hide();
														$(".skipad").show();
													}
													count--;
													}, 
												1000); 										
											});
											player.loopbutton();													
										});											
									}else{
										$(".skipad").click(function(e){
											e.preventDefault();
												$(".ad-preroll").hide();											
										});										
										$(".play-screen").click(function(e){
												e.preventDefault();
												$(this).hide();
												$(".ad-preroll").show();
													var count = "'.$this->config["preroll_timer"].'";
													var countdown = setInterval(function(){
													$(".ad-preroll p span").html(count);
													if (count < 1) {
														clearInterval(countdown);
														$(".ad-preroll p").hide();												
														$(".skipad").show();		
													}
													count--;
													}, 
												1000); 										
											});
									}       		
		          	});
		          </script>
		          ';		          
						  Main::enqueue();
		  echo '</head>
		        <body>';		
						// Check if social lock is enabled
						if($media->social){
							echo $this->locker($media);
						}else{
		        	// Add Logo					
			        if(!empty($this->config["logo"])){
			        	echo "<div class='logo'><a href='{$this->config["url"]}' target='_blank'><img src='{$this->config["url"]}/content/{$this->config["logo"]}'></a></div>";
			        }
							if($this->config["ads"] && $ad = $this->db->get("ads", array("type" => "preroll", "enabled" => "1"), array("limit" => "1", "order" => "RAND()"))){								
								$this->db->update("ads", "impression = impression + 1", array("id" => $ad->id));								
					echo "<div class='play-screen'></div>";
					echo "<div class='preroll'>
								   <div class='ad-preroll'>
									   {$ad->code}
									   <p>".e("Please wait")." <span>{$this->config["preroll_timer"]}</span> ".e("seconds.")."</p><br><br>
									   <a href='#' class='skipad'>".e("Skip Ad")."</a>
								   </div>
								   <div id='player'>{$media->embed}</div>
								</div>";
							}else{
								echo $media->embed;
							}
					}
		  echo '</body>
		      </html>';		
	}
		/**
		 * FB Locker
		 */
		protected function locker($media){
			$html = "<div class='locker' data-lock-id='{$media->uniqueid}' style='width:100%;height:100%;position:absolute;z-index:9999999999999;background:#fff;background-color: rgba(0,0,0,0.5); background-image: url({$media->thumb}); background-size: cover; background-position: center; text-align:center;'> 
			<div style='width:100%;height:100%;position:absolute;z-index:9999999999999;background:#fff;background-color: rgba(0,0,0,0.8);z-index:1;'></div>
									<a class='facebook' href='#' style='margin-top:20%; display:inline-block; background: #3b5998;color:#fff; text-decoration:none;padding: 10px;border-radius:2px;z-index:2;position:relative;'>".e("Share to watch video")."</a>
									<div id='fb-root'></div>
									<script src='{$this->config["url"]}/static/js/total.js'></script>
									<script>
									(function(d, s, id) {
									  var js, fjs = d.getElementsByTagName(s)[0];
									  if (d.getElementById(id)) return;
									  js = d.createElement(s); js.id = id;
									  js.src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&appId={$this->config["facebook_app_id"]}&version=v2.0';
									  fjs.parentNode.insertBefore(js, fjs);
									}(document, 'script', 'facebook-jssdk'));
									$(document).ready(function(){
										$('.facebook').click(function(a) {
												a.preventDefault();
												var id = $('.locker').data('lock-id');
										    if($.totalStorage(id) == 1){
													$('.locker').hide();
										    }else{
													var e = {
											        method: 'feed',
											        link: '{$media->url}',
											        picture: '{$media->thumb}',
											        name: '{$media->title}',
											        caption: '{$media->type}'
											    };
											    FB.ui(e, function(t) {
											        if (t['post_id']) {
	                              $('.locker').show();									          
	                              $.totalStorage(id, 1);
											        }
											    })										    	
										    }
										});
									});
									</script>
							 </div>";
			return $html;
		}
		/**
		 * Embed Subscribe
		 * @since  1.3
		 */
		protected function embed_subscribe($media){
			$user = $this->db->get("user", array("id" => $media->userid), array("limit" => 1));
			 echo '<html>
		        <head>
			        <link href="'.$this->config["url"].'/static/css/bootstrap.min.css" rel="stylesheet">     
			        <link rel="stylesheet" type="text/css" href="'.$this->config["url"].'/themes/'.$this->config["theme"].'/style.css">
		          <style>
		          body{ margin: 0; padding: 10% 5%; font-family: "Helvetica", Arial sans-serif; background: #151720; color: #fff}
		          </style>
		          <script type="text/javascript" src="'.$this->config["url"].'/static/js/jquery.min.js"></script>
		          <script type="text/javascript" src="'.$this->config["url"].'/static/application.fn.js"></script>
		          <script type="text/javascript" src="'.$this->config["url"].'/static/server.js"></script>
		          <script>
		          	$(document).ready(function(){
									//var html = $(".login-form").html();
								  //$(document).modal({title: "Login to your account", content: html, link: ""});
								  $(".subscribe").click(function(){
										location.reload();
								  });
		          	});
								var appurl = "'.$this->config["url"].'";
					      var token = "'.$this->config["public_token"].'";
		          </script>
		          ';		          
		  echo '</head>
		        <body>
			        <section class="embed_subscribe">
			        	<div class="row">
			        		<div class="col-xs-4">
				        		<a href="'.$media->url.'" target="_blank"><img src="'.$media->thumb.'" width="100%"></a>
			        		</div>
			        		<div class="col-xs-8 hidden-xs">
			        			<h3><a href="'.$media->url.'" target="_blank">'.$media->title.'</a></h3>
			        			<p>'.Main::truncate($media->description, 250).'</p>
			        		</div>
			        	</div>
			        	<div class="row text-center embed_promo hidden-xs">
				        	<br>
			        		<div class="col-xs-4">
			        			'.$media->views.'
			        			<span>'.e("Views").'</span>
			        		</div>
			        		<div class="col-xs-4">
				        		'.$media->likes.'
				        		<span>'.e("Likes").'</span>
			        		</div>
			        		<div class="col-xs-4">
				        		'.$media->comments.'
				        		<span>'.e("Comments").'</span>
			        		</div>
			        	</div>
			        	<br>
			        	<div class="row">
				        	<div class="col-sm-12">
				        		'.(!$this->logged() ? 
											'<a class="btn btn-danger btn-block" href="'.Main::href("user/login").'" target="_blank">'.e("Login and Subscribe to Unlock").'</a>'
				        			:
				        				'<a class="btn btn-primary btn-block this-action subscribe" id="this-subscribe" data-action="subscribe" data-data=\'["id":'.$media->userid.']\'  href="#">'.e("Subscribe to Unlock").'</a>'
		        				).'
				        	</div>
			        	</div>
			        </section>
		        </body>
		      </html>';	
		}
	/**
	 * Custom Page
	 */
	protected function page(){
		$this->filter($this->id);
		// Get published page
		$data = array("slug" => "?");
		// If not admin show only published page else show all pages
		if(!$this->admin()){
			$data["publish"] = "1";
		}
		// Get Media
		if(!$page = $this->db->get("page", $data, array("limit" => 1), array($this->do))){
			return $this->_404();
		}
		if(empty($page->meta_title)){
			$page->meta_title = $page->name;
		}
		if(empty($page->meta_description)){
			$page->meta_description = Main::truncate($page->name, 200, TRUE);
		}		
		// Plug Admin control links
		$this->admin_menu_html = build_menu(array(
				array("href" => Main::ahref("pages/edit/{$page->id}"), "text" => "Edit Page", "icon" => "edit"),
			), TRUE);		
		// Set meta data
		Main::set("title", $page->meta_title);
		Main::set("description", Main::truncate($page->meta_description,200, TRUE));
		
		// Plug-in Header
		Main::plug("page_header", array("page" => $page));
		// Show Template
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();			
	}
	/**
	 * RSS Feed
	 * @since  1.1.1
	 */
	private function rss(){
		$this->filter($this->do);
		// Get Media
		$media = $this->db->get("media", array("approved" => "1"), array("order" => "date","limit" => $this->config["rsslimit"]));
		$medium = array(
				"video" => "video",
				"picture" => "image",
				"music" => "video",
				"vine" => "video"
			);
		// Set Header
		header('content-type: application/xml; charset=utf-8');
 
    $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
 
    $xml .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:dcterms="http://purl.org/dc/terms/">' . "\n";
 
    // channel required properties
    $xml .= '<channel>' . "\n";
    $xml .= '<title>' . $this->config["title"] . '</title>' . "\n";
    $xml .= '<link>' . $this->config["url"] . '</link>' . "\n";
    $xml .= '<description>' . $this->config["description"] . '</description>' . "\n";
 		
 
    foreach($media as $item) {
    	$item = $this->formatMedia($item);
      $xml .= '<item>' . "\n";
      $xml .= '<title>' . Main::clean($item->title,3,TRUE) . '</title>' . "\n";
      $xml .= '<link>' . $item->url . '</link>' . "\n";
      $xml .= '<description>' . Main::clean($item->description,3,TRUE) . '</description>' . "\n";
      $xml .= '<pubDate>'.date("D, d M Y H:i:s T", strtotime($item->date)).'</pubDate>' . "\n";
      $xml .= '<category>'.types($item->type).'</category>' . "\n";
      $xml .= '<media:content medium="'.$medium[$item->type].'" height="350" width="650">' . "\n";
      	$xml .= '<media:player url="'.Main::href("embed/{$item->uniqueid}").'" height="200" width="400" />'. "\n";
				$xml .= '<media:title>' . Main::clean($item->title,3,TRUE) . '</media:title>'. "\n";
				$xml .= '<media:description>' . Main::clean($item->description,3,TRUE) . '</media:description>'. "\n";
				$xml .= '<media:thumbnail url="'.$item->thumb.'"/>'. "\n";
			$xml .= '</media:content>'. "\n";
      $xml .= '</item>' . "\n";
    }
 
    $xml .= '</channel>';
 
    $xml .= '</rss>';
    echo $xml;
	}
	/**
	 * Header
	 * @since 1.4
	 **/
	protected function header(){
		if($this->sandbox==TRUE) {
			// Developement Stylesheets
			Main::add("<link rel='stylesheet/less' type='text/css' href='{$this->config["url"]}/themes/{$this->config["theme"]}/style.less'>","custom",false);
			Main::cdn("less");
		}
		// Use CDN for better performance
		if($this->config["cdn"]){
			Main::cdn("chosen");
			Main::cdn("icheck");
		}else{
			Main::add($this->config["url"]."/static/js/chosen.min.js","script",0);
		}
		if(!empty($this->config["font"])) {
			Main::add("http://fonts.googleapis.com/css?family=".str_replace(' ', '+', ucwords($this->config["font"])),"style",FALSE);
			Main::add("<style type='text/css'>body{font-family: {$this->config["font"]} !important}</style>","custom",FALSE);
		}	
		if(!empty($this->config["ga"])){
			Main::add("<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{$this->config["ga"]}', 'auto');ga('send', 'pageview');</script>","custom",FALSE);	
		}
		Main::cdn("pace");		
		// Get nofications
		$notifications = FALSE;
		if($this->logged()){
			$notifications = $this->db->get("temp", array("type" => "notification","filter" => $this->user->id, "viewed" => "0"), array("limit" => 1));
		}
		include($this->t(__FUNCTION__));
	}
	/**
	 * Footer
	 * @since 1.0
	 **/
	protected function footer(){
		$pages = $this->db->get("page",array("menu"=>1 , "publish" => 1),array("limit" => 10));
		include($this->t(__FUNCTION__));
	}
	/**
	 * Main Menu
	 * @since  1.6
	 */
	protected function menu(){
		// Generate main menu
		echo "<nav id='main-menu'>";
			echo "<div class='container'>";
				echo "<ul class='nav navbar-nav'>";
					$default = array();
					if($this->config["type"]["video"]){
						$default[] = array(
								"href" => Main::href("video"),
								"text" => e("Video"),
								"icon" => types_icon("video")
							);						
					}
					if($this->config["type"]["music"]){
						$default[] = array(
								"href" => Main::href("music"),
								"text" => e("Music"),
								"icon" => types_icon("music")
							);												
					}
					if($this->config["type"]["vine"]){
						$default[] = array(
								"href" => Main::href("vine"),
								"text" => e("Vine"),
								"icon" => types_icon("vine")
							);							
					}
					if($this->config["type"]["picture"]){
						$default[] = array(
								"href" => Main::href("picture"),
								"text" => e("Picture"),
								"icon" => types_icon("picture")
							);							
					}
					if($this->config["type"]["post"]){
						$default[] = array(
								"href" => Main::href("post"),
								"text" => e("Articles"),
								"icon" => types_icon("post")
							);							
					}					
					$default[] = array(
									"href" => Main::href("channels"),
									"text" => e("Channels"),
									"icon" => "list"
								);					
					if($this->config["type"]["blog"]){
						$default[] = array(
								"href" => Main::href("blog"),
								"text" => e("Blog"),
								"icon" => "newspaper-o"
							);							
					} 
					echo build_menu($default);
					// Custom Menu
					echo Main::plug("main_menu");
				echo " </ul>";
			echo "</div>";
		echo "</nav>";
	}
	/**
	 * Admin
	 * @author Emrul
	 * @since  1.0
	 */
	protected function admin_menu(){
		// Make admin is logged
    if(!$this->logged() || !$this->admin()) return FALSE;
      echo "<div class='admin-bar hidden-xs'>
			        <ul class='admin-bar-menu'>
			          <li><strong>Welcome Admin</strong>!</li>
			          <li><a href='".Main::ahref('?bar')."'' class='admin-alt'><span class='fa fa-dashboard'></span> Admin Dashboard</a></li>
			          <li><a href='".Main::ahref('media/add')."''><span class='fa fa-plus'></span> Add Media</a></li>
			          <li><a href='".Main::ahref('media/import')."''><span class='fa fa-download'></span> Import Media</a></li>
			          <li><a href='".Main::ahref('media/youtube')."''><span class='fa fa-cloud-download'></span> Import from Youtube</a></li>
			          <li><a href='".Main::ahref('media/')."''><span class='fa fa-film'></span> Manage Media</a></li>
			          {$this->admin_menu_html}
			          <li class='pull-right'><a href='".Main::ahref('settings/')."''><span class='fa fa-gear'></span> Configuration</a></li>
			        </ul>
			      </div>";
	}
	/**
	 * Sidebar
	 * @since 1.0
	 */
	protected function sidebar($array = array()){
		// Return Media Sidebar
		if($this->isView) return $this->sidebar_media($array);
		// Ads
		if($this->config["ads"]) echo "{$this->ads(300)}";
		// Plug-in function in sidebar
		Main::plug("sidebar");
		// Show social stuff
		echo $this->sidebar_social();
		// Show Featured Media
		if(!isset($array["categories"])){
			echo $this->sidebar_categories();
		}		
		// Show Featured Media
		if(isset($array["featured"])){
			echo $this->sidebar_featured($array["featured"]);
		}
		// Show Trending
		if(isset($array["trending"])){
			echo $this->sidebar_trending($array["trending"]);
		}		
		// Show Top Users
		if(isset($array["topusers"])){
			echo $this->sidebar_topusers($array["topusers"]);
		}			
		// Show Blog Posts
		if(isset($array["blog"])){
			echo $this->sidebar_blog($array["blog"]);
		}		
	}
		/**
		 * View Page Sidebar
		 * @since 1.5.1
		 */
		public function sidebar_media($array = array()){
			// Playlist
			if(isset($_GET["playlist"])){
				$playlist = $this->db->get("playlist",array("uniqueid" => "?"), array("limit" => 1, "order" => "date"), array($_GET["playlist"]));
				// Get Media List
				$media = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}toplaylist.mediaid as mediaid,{$this->config["prefix"]}toplaylist.date as pdate FROM `{$this->config["prefix"]}toplaylist` INNER JOIN `{$this->config["prefix"]}media` ON {$this->config["prefix"]}media.id = mediaid"), array("playlistid" => $playlist->id),array("limit" => 50, "order" => "pdate"));
				
				$count = count($media);
				if($playlist){
					if(isset($_GET["index"]) && is_numeric($_GET["index"]) && $_GET["index"] <= $count) {
						$index = $_GET["index"];
						$order = "<span class='pull-right'>{$index}/{$count}</span>";
					}else{
						$index = "";
						$order = "";
					}

					echo "<div class='sidebar-playlist panel panel-default'>
				          <div class='panel-heading'><a href='".Main::href("playlist/{$playlist->uniqueid}")."'>{$playlist->name}</a> $order</div>";
                    echo "<ul class='media media-sidebar media-playlist'>";
                    echo $this->listMedia($media, NULL, array("playlist" => "?playlist={$playlist->uniqueid}","current" => $index), 1);
                    echo "</ul>";
					echo "</div>";					
				}
			}			
			if($this->config["ads"]) echo "{$this->ads(300)}";
			// Plug-in function in media sidebar
			Main::plug("media_sidebar");
			// Get data
			$media = $array["related"];					
			// Next Video
			if($next = $this->db->get("media", array("id" => $media->id+1, "approved" => "1"), array("limit" => 2))){
				echo "<div class='panel panel-default'>";			
					echo "<div class='panel-heading'><span>".e("Next Media")."</span></div>";
						echo "<div class='media media-sidebar'>";
							echo "<div class='row'>";
								echo $this->listMedia($next, NULL, array(), 1);
							echo "</div>";						
						echo "</div>";						
				echo "</div>";				
			}	
			// Get Related Videos			
			if(empty($media->tags)){
				$related = $this->listMedia($this->db->search("media","`approved`='1' AND `id` != '{$media->id}' AND (`catid` = :c)",array("order" => "RAND()", "limit" => 5),array(":c" => $media->catid)), NULL, array(), 1);
			}else{
				$tags = explode(",", $media->tags);
			  $c = count($tags);
			  $i = 1;
				$q = "";
				foreach ($tags as $tag) {			
					$tag = str_replace("'"," ",$tag);		
          if($i>=$c){
            $q .= "`tags` LIKE '%$tag%'";
          }else{
            $q .= "`tags` LIKE '%$tag%' OR ";
          }  					
          $i++;
				}
				$related = $this->listMedia($this->db->search("media","`approved`='1' AND `id` != '{$media->id}' AND ($q)",array("order" => "views", "limit" => 5)), NULL, array(), 1);
			}
			if(!$related) $related = $this->listMedia($this->getMedia(array("order" => "RAND", "limit" => 5)), NULL, array(), 1);			
				echo "<div class='panel panel-default'>";			
					echo "<div class='panel-heading'><span>".e("Related Media")."</span></div>";
					echo "<div class='media media-sidebar'>";
						echo "<div class='row'>";
							echo $related;
						echo "</div>";						
					echo "</div>";
				echo "</div>";
			// Get Media from same User
			$user_media = $this->listMedia($this->getMedia(array("userid" => $media->userid, "limit" => 5)), NULL, array(), 1);
			if($user_media){
				echo "<div class='panel panel-default'>";			
					echo "<div class='panel-heading'><span>".e("More from this user")."</span></div>";
					echo "<div class='media media-sidebar'>";
						echo "<div class='row'>";
							echo $user_media;
						echo "</div>";						
					echo "</div>";
				echo "</div>";
			}
			// Show Featured Media
			if(isset($array["featured"])){
				echo $this->sidebar_featured($array["featured"]);
			}
			// Show Social Stuff
			if(isset($array["social"])){
				echo $this->sidebar_social($array["social"]);
			}			
			return;
		}
		/**
		 * Show featured videos in sidebar
		 * @author Emrul
		 * @since  1.0
		 */
		public function sidebar_featured($array = array()){
			if(!isset($array["limit"])) $array["limit"] = 5;
			// Get media
			$media = $this->listMedia($this->getMedia(array("limit" => $array["limit"], "featured" => "1", "order" => "RAND()")), NULL, array(), 1);
			if(!$media) $media = $this->listMedia($this->getMedia(array("limit" => $array["limit"], "order" => "RAND()")), NULL, array(), 1);
			echo "<div class='panel panel-default' id='".__FUNCTION__."'>
							<div class='panel-heading'><span>".e("Featured")."</span></div>
							<div class='media media-sidebar-featured'>
								<div class='row'>
									$media
								</div>
							</div>
						</div>";
		}
		/**
		 * Show trending videos in sidebar
		 * @author Emrul
		 * @since  1.0
		 */
		public function sidebar_trending($array = array()){
			if(!isset($array["limit"])) $array["limit"] = 5;
			// Get media
			$media = $this->listMedia($this->getMedia(array("limit" => $array["limit"], "order"=>"trending")), NULL, array(), 1);
			if(!$media) $media = $this->listMedia($this->getMedia(array("limit" => $array["limit"], "order" => "RAND()")), NULL, array(), 1);
			echo "<div class='panel panel-default' id='".__FUNCTION__."'>
							<div class='panel-heading'><span>".e("Trending")."</span></div>
							<div class='media media-sidebar-trending'>
								<div class='row'>
									$media
								</div>
							</div>
						</div>";
		}		
		/**
		 * Social Sidebar
		 * @author Emrul
		 * @since  1.5
		 */
		public function sidebar_social(){
			if(empty($this->config["twitter"]) && empty($this->config["facebook"]) && empty($this->config["google"])) return FALSE;
			echo "<div class='panel panel-default' id='".__FUNCTION__."'>
							<div class='panel-heading'><span>".e("Follow Us")."</span></div>";
								if(!empty($this->config["facebook"])){
									echo '<div id="fb-root"></div>
												<script>(function(d, s, id) {
												  var js, fjs = d.getElementsByTagName(s)[0];
												  if (d.getElementById(id)) return;
												  js = d.createElement(s); js.id = id;
												  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8'.(!empty($this->config["facebook_app_id"]) ? "&appId={$this->config["facebook_app_id"]}": "").'";
												  fjs.parentNode.insertBefore(js, fjs);
												}(document, \'script\', \'facebook-jssdk\'));</script>
												<div class="fb-page" data-href="'.urldecode($this->config["facebook"]).'" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"><blockquote cite="'.urldecode($this->config["facebook"]).'" class="fb-xfbml-parse-ignore"><a href="'.urldecode($this->config["facebook"]).'">Facebook</a></blockquote></div>';
								}			
						echo '<div class="panel-body" style="padding: 10px;">';
								if(!empty($this->config["twitter"])){
									echo '<div class="pull-left"><a href="'.$this->config["twitter"].'" class="twitter-follow-button" data-size="large" data-show-count="false">Follow @structuly</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script></div>';
								}			
								if(!empty($this->config["google"])){
									echo '<div class="pull-right" style="margin-top:3px"><script src="https://apis.google.com/js/platform.js" async defer></script><div class="g-follow" data-annotation="none" data-height="24" data-href="'.$this->config["google"].'" data-rel="publisher"></div></div>';
								}
						echo "</div>";				
			echo "</div>";				
		}
		/**
		 * Blog Sidebar
		 * @author Emrul
		 * @since  1.0		 
		 */
		public function sidebar_blog(){
			if(!$this->config["type"]["blog"]) return FALSE;
			// Get posts
			$posts = $this->db->get("blog", array("publish" => "1"), array("limit" => "5", "order" => "date"));			
			if(!$posts) return FALSE;
			echo "<div class='panel panel-default' id='".__FUNCTION__."'>
							<div class='panel-heading'><span>".e("From the Blog")."</span></div>
							<ul class='blog-posts'>";
				foreach ($posts as $post) {
					echo "<li>
									<h3><a href='".Main::href("blog/{$post->slug}")."'>{$post->name}</a></h3>
									".Main::truncate(Main::clean($post->content, 3), 64, "...<p><a href='".Main::href("blog/{$post->slug}")."' class='btn btn-primary btn-xs'>".e("Read More")."</a></p>")."
								</li>";
				}							
			echo "	</ul>
						</div>";			
		}
		/**
		 * Top Users Sidebar
		 * @author Emrul
		 * @since  1.2.1
		 */
		public function sidebar_topusers($array = array()){
			if(!$this->config["points"]) return FALSE;
			if(!isset($array["limit"])) $array["limit"] = 10;

			$users = $this->db->get("user", "active = 1 AND username != ''", array("order" => "points", "limit" => $array["limit"]));
			echo "<div class='panel panel-default' id='".__FUNCTION__."'>
							<div class='panel-heading'><span>".e("Top Users")."</span></div>
							<ul class='top-users'>";
				$i = 0;
				foreach ($users as $user) {
					if($i == 0){
						echo "<li><a href='".Main::href("user/{$user->username}")."'><i class='glyphicon glyphicon-sunglasses'></i> ".ucfirst($user->username)." <strong>{$user->points} ".e("points")."</strong></a></li>";					
					}else{
						echo "<li><a href='".Main::href("user/{$user->username}")."'><i class='glyphicon glyphicon-user'></i> ".ucfirst($user->username)." <strong>{$user->points} ".e("points")."</strong></a></li>";					
					}					
					$i++;
				}							
			echo "	</ul>
						</div>";	
		}
		/**
		 * Categories
		 * @since  1.3
		 */
		public function sidebar_categories($array = array()){
			// Get Categories
				$parent = $this->db->get("category", array("parentid" => "0"), array("order" => "name", "asc" => 1));			
				echo "<div class='panel panel-default' id='".__FUNCTION__."'>
								<div class='panel-heading'><span>".e("Categories")."</span></div>
								<ul class='categories'>";
					foreach ($parent as $single) {
						echo "<li><i class='fa fa-chevron-right'></i> <a href='".Main::href("channel/{$single->type}/{$single->slug}")."'>{$single->name}</a>";
							if($child = $this->db->get("category", array("parentid" => $single->id), array("order" => "type", "asc" => 1))){
								echo "<ul>";
								foreach ($child as $single) {
									echo "<li><i class='fa fa-chevron-right'></i> <a href='".Main::href("channel/{$single->type}/{$single->slug}")."'>{$single->name}</a></li>";
								}
								echo "</ul>";
							}
						echo "</li>";
					}							
				echo "	</ul>
							</div>";
		}
	/**
	 * 404 Page
	 * @author Emrul
	 * @since  1.0
	 */
	protected function _404($type = NULL){
		// Plug-in 404 Page - Redirects allowed
		$custom404 = Main::plug("404", array("type" => $type));
		// 404 Header
		header('HTTP/1.0 404 Not Found');		
		// Show Template		
		if($custom404){
			$this->header();
			// Show custom 404 page if any
			echo $custom404;
			$this->footer();
			return;
		}
		// Show built-in 404
		if($type == "media"){
			Main::set("title",e("Media not found"));
			Main::set("description", "The media you are looking for is not available or has been deleted.");
			// General 404
			$content = "<section class='page404 media404'>
									  <div class='container text-center'>
									    <h1>404 <span>Media Not Found</span></h1>    
									    <br>
											<div class='panel panel-default text-left'>
												<div class='panel-heading'>".e("Suggested Media")."</div>
									  		<ul class='media media-wide'>
										  		{$this->listMedia($this->getMedia(array("order"=>"RAND()" , "limit" => "10")))}
									  		</ul>
										  </div>									    
									  </div>									  
									</section>";
		}else{
			Main::set("title",e("Page not found"));				
			Main::set("description", "The page you are looking for is not available or has been deleted.");
			// General 404
			$content = "<section class='page404'>
									  <div class='container text-center'>
									    <h1>404 <span>Page Not Found</span></h1>    
									  </div>
									</section>";
		}
		$this->header();	
		echo $content;	
		$this->footer();
		return;
	}
	/**
	 * Maintenance Page
	 * @author Emrul
	 * @since  1.0
	 */
	protected function _maintenance(){
		Main::set("title",e("Under Maintenance"));				
		Main::set("description", "We are currently under maintenance. Please check us back later.
			");
		// General 404
		$content = "<section class='page404'>
								  <div class='container text-center'>
								    {$this->config["offline_message"]}    
								  </div>
								</section>";
		$this->header();	
		echo $content;	
		$this->footer();
	}
	/**
	 * Format single media data
	 * @author Emrul
	 * @since  1.2
	 * @param  object $media Media data
	 * @return object
	 */
	public function formatMedia($media, $options = array()){
		// Format URLs
		$media->url = Main::href("view/{$media->url}/{$media->uniqueid}");
		// Get correct thumbnail path
		if($this->config["local_thumbs"] || empty($media->ext_thumb)){
			if(empty($media->thumb)){
				$media->thumb = $media->ext_thumb;
			}else{
				$media->thumb = "{$this->config["url"]}/content/thumbs/{$media->thumb}";
			}
		}else{
			$media->thumb = $media->ext_thumb;
		}
		if(Main::extension($media->file) == ".gif"){
			if(file_exists(THUMBS."/static_{$media->thumb}"))	{
				$media->thumb_big = "{$this->config["url"]}/content/thumbs/static_{$media->thumb}";
			}else{
				$media->thumb_big = $media->thumb;
			}
		}
		unset($media->ext_thumb);
		// Get Media Player
		if($media->type == "picture"){
			if(!empty($media->file)){
				if(Main::extension($media->file) == ".gif"){
					$media->player = "<a href='#play' class='play-gif' data-static='{$media->thumb_big}' data-gif='{$this->config["url"]}/content/media/{$media->file}'><img src='{$media->thumb_big}' alt='{$media->title}'/><span class='fa fa-play-circle'></span></a>";
					$media->code = "<a href='{$media->url}' alt='{$media->title}'><img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}' /></a>";		
				}else{
					$media->player = "<img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}'/>";
					$media->code = "<a href='{$media->url}' alt='{$media->title}'><img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}' /></a>";		
				}						
			}elseif(!empty($media->embed)){
				$media->player = $media->embed;
				$media->code = $media->embed;					
			}else{
				$media->player = "<img src='{$media->link}' alt='{$media->title}'/>";
				$media->code = "<a href='{$media->url}' alt='{$media->title}'><img src='{$media->link}' alt='{$media->title}' /></a>";				
			}			
		}else{
			$media->player = "<iframe src='".Main::href("embed/{$media->uniqueid}")."' frameborder='0' width='100%' height='450' scrolling='no' allowfullscreen></iframe>";
			
			$media->code = "<iframe src='".Main::href("embed/{$media->uniqueid}")."' frameborder='0' width='550' height='350' scrolling='no' allowfullscreen></iframe><p><a style='color: #000; text-decoration: none;' href='{$media->url}' title='{$media->title}'>{$media->title}</a></p>";
		}		
		// Format numbers
		$media->rating = $media->votes > 0 ? round($media->likes / $media->votes,1)*5 : 0;
		$media->views = number_format($media->views, 0, ".", ",");
		$media->votes = number_format($media->votes, 0, ".", ",");
		$media->likes = number_format($media->likes, 0, ".", ",");
		$media->dislikes = number_format($media->dislikes, 0, ".", ",");
		$media->date = Main::timeago($media->date);
		$media->tags_html = "";
		// Format Tags
		if(!empty($media->tags)){
			$tags = explode(",", $media->tags);
			$media->tags_html = "<p>
							          <strong>".e("Tags")."</strong>";
			foreach ($tags as $tag) {
				if(empty($tag)) continue;
				$media->tags_html .="<a href='".Main::href("search/".urlencode($tag))."' class='btn btn-info video-tag'>$tag</a> ";
			}			 			
    }
    if(isset($media->username)){
    	$media->author = ucfirst($media->username);
    }
		if(isset($media->name)){
			// Format Author Data
			$profile = json_decode($media->name);
			if(is_object($profile) && !empty($profile->name)){
				$media->author = ucfirst($profile->name);		
			}
			$media->profile = Main::href("user/{$media->username}");	
		}
		return $media;
	}
	/**
	 * Generated Media list from object
	 * @author Emrul
	 * @since  1.6
	 * @param  array $list Data to format and generate html
	 * @param  string $custom Custom html
	 * @return string       Formatted list
	 */
	public function listMedia(array $list, $keyword = NULL, $options = array(), $count = NULL){
		if(is_null($count)){
			$count = $this->config["perrow"];
		}
		// Loop and format media
		$html = "";
		foreach ($list as $i => $media) {
			// Format Media
			$media = $this->formatMedia($media, $options);
			// ReFormat URL
			if(isset($options["playlist"])){
				$media->url = $media->url.$options["playlist"]."&index=".($i+1);
			}
			if(!isset($media->profile)) $media->profile ="";
			if(!isset($media->author)) $media->author ="";
			
			$media->description = Main::truncate($media->description, 200);
			
			if(!is_null($keyword)){
				$media->description = str_ireplace($keyword,"<strong>$keyword</strong>",$media->description);
			}

			if($count == 4){
				$class = 'col-md-3';
			}elseif ($count == "custom") {
				$class = 'col-md-custom';
			}elseif ($count == 6) {
				$class = 'col-md-2';
			}elseif ($count == 2) {
				$class = 'col-md-6';				
			}elseif ($count == 1) {
				$class = 'col-md-12';
			}else{
				$class = 'col-md-4';
			}

			$html .= "<div class='$class media-item media-item-{$count} media-{$media->type} {$media->type}-{$media->catid}  ".($media->featured ? "media-item-featured": "" )."".(isset($options["current"]) && $options["current"] == ($i+1) ? " playlist-current": "")."' id='media-{$media->id}'>
                <div class='mediathumb'>
                  <a href='{$media->url}' title='".htmlentities($media->title)."' class='mediacontainer'>
                    <span class='mediabg' style='background-image:url({$media->thumb})'>{$media->title}</span>
                    ".($media->nsfw ? "<span class='mediansfw'>NSFW</span>": "" )."                   
                    <span class='mediatype'><i class='fa fa-".types_icon($media->type)."'></i> <span class='mediadate'>".$media->date."</span></span>
                    <small>".(in_array($media->type, array("video","music","vine")) && $media->duration > 0 ? totime($media->duration) : $media->date)."</small>
                  </a>                  	                  
                </div> 
                <div class='mediainfo'>
	                <h4>
	                	<a href='{$media->url}' title='".htmlentities($media->title)."' class='medialink'>".Main::truncate($media->title, 60)."</a>
		                <span class='mediavotes'><span class='fa fa-thumbs-up'></span> {$media->likes}&nbsp;&nbsp;&nbsp;</span>	
	                  <span class='mediacomments'><span class='fa fa-comment'></span> {$media->comments}</span>	                	
	                </h4>
	                <p class='mediadescription'>".Main::truncate(Main::clean($media->description, 3 , TRUE), 200)."</p>	              
	                ".($media->author ? "<a class='mediaauthor' href='{$media->profile}' title='{$media->author}'>  
	                	<span class='mediaauthorname'><i class='glyphicon glyphicon-user'></i> <strong>".Main::truncate(ucfirst($media->author), 15)."</strong></span>
	                  <span class='mediaviews'>{$media->views} ".e("views")."</span> 
	                </a>" : " ")."                                
                </div>
              </div>";				
		}
		return $html;
	}
/**
	 * Generated Media Rows from object
	 * @author Emrul
	 * @since  1.4
	 * @param  array $list Data to format and generate html
	 * @return string       Formatted list
	 */
		protected function BiosCoop(array $list, $keyword = NULL){
		// Loop and format media
		$html = "";
		$i = 0;
		foreach ($list as $media) {
			// Format Media
			$media = $this->formatMedia($media);
			if(!isset($media->profile)) $media->profile ="";
			if(!isset($media->author)) $media->author ="";
			$media->description = Main::truncate($media->description, 200);
			if(!is_null($keyword)){
				$media->description = str_ireplace($keyword,"<strong>$keyword</strong>",$media->description);
			}
			if($media->type == "post"){
				if(!empty($media->file)){
					$media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}'></a>";
				}else{
					$media->player = "";
				}	
			}			
			// Check if logged user has already rated this media and check if nsfw is enabled
			if($this->logged()){
				if(!$this->user->nsfw && $media->nsfw){
					$media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/static/nsfw_big.png' alt=''></a>";
				}
				$rating = $this->db->get("rating", array("mediaid" => $media->id, "userid" => $this->user->id),array("limit" => "1"));
			}else{
				if($media->nsfw){
					$media->player = "<img src='{$this->config["url"]}/static/nsfw_big.jpg' alt=''>";
				}				
				$rating = NULL;
			}
			$bioscoop = new stdClass();
			$bioscoop->player = "<iframe src='".Main::href("embed/{$media->uniqueid}")."' frameborder='0' width='100%' height='250' scrolling='no' allowfullscreen></iframe>";
			if($i > 0 && $i%5==0)	$html .= $this->ads(728);
			
			$sum = $media->likes - $media->dislikes;
			$sum = $sum < 0 ? 0 : $sum;
			$points = $sum == "1" ? "<strong> {$sum}</strong>	".e("Point")."" : "<strong> {$sum}</strong>	".e("Points")."";
		    // $html .= "<div class='video-box'>
      //                   <div class='video-details'>
      //                       <h2 class='title'><a href='{$media->url}' title='{$media->title}'>".Main::truncate($media->title,100)."</a></h2>
      //                       <p class='video-info'><b><span id='relesDate{$media->id}'><script>counter('{$media->id}', '{$media->release_date}')</script></span></b> <a href='' id='this-report' class='this-action' data-toggle='modal' data-content='Report' title='Report This Video' data-action='report' data-data='[\"id\":{$media->id}, \"check\":\"media\"]'><span class='pull-right'>&#8226;&#8226;&#8226;</span></a> <a href='#follow' id='this-subscribe' data-action='follow' data-data='[\"id\":{$media->catid}]' class='this-action follow' data-content='follow'><span class='pull-right fa fa-bell'></span></a></p>
      //                       <p style='display:none' id='description{$media->id}'>".$media->description."</p>
      //                   </div>
      //                   <div class='video-palyer' id='published{$media->id}'>
      //                       {$bioscoop->player}
      //                   </div>
      //                   <div class='video-options' id='comment{$media->id}'>
      //                   	<ul class='player-bottom'>
      //                       <li><a href=href='#like' id='this-like-{$media->id}' class='this-action".($rating && $rating->rating=="liked" ? " active":"")."' data-content='' data-action='like' data-data='[\"id\":{$media->id}, \"check\":{$media->userid}]'><i class='fa fa-user'></i><div id='waitPlus-{$media->id}'>{$media->likes}</div> Waiting</a></li>
      //                       <li><a href='javascript:;' onclick='comment({$media->id})' class='comments-trigger'><i class='fa fa-comments'></i>{$media->comments} Comment</a></li>
      //                       <li><a href='#' id='shareBtn'><i class='fa fa-share-alt'></i>Share</a><ul><li>".Main::share($media->url,urlencode($media->title), array("facebook", "twitter", "google"))."</li></ul></li></ul>
      //                   </div>
      //               </div><div class='commentSection' style='display: none' id='commentSection{$media->id}'><div class='video-author' data-id='{$media->userid}'></div>".
      //                   $this->comments($media->id, $media->url, $media->comments)."
      //               </div>";
			$html .="<div class='profile-details'>
                                <div class='pro-title'>
                                    <div class='row'>
                                        <div class='col-sm-10'>
                                            <h2><span id='relesDate{$media->id}'><script>counter('{$media->id}', '{$media->release_date}','{$media->title}')</script></span></h2>
                                        </div>
                                        <div class='col-sm-2'>
                                            <a href=''id='this-report' class='this-action btn btn-success txt-white pull-right' data-toggle='modal' data-content='Report' title='Report This Video' data-action='report' data-data='[\"id\":{$media->id}, \"check\":\"media\"]'>&bull;&bull;&bull;</a>
                                            <a href='#getnotify' id='this-subscribe' data-action='getnotify' data-data='[\"id\":{$media->id}]' class='this-action getnotify bell-alert' data-content='getnotify'><i class='fa fa-bell'></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class='pro-body'>
                                    <div class='row'>
                                        <div class='col-sm-7'>
                                            <div class='video-palyer' id='published{$media->id}'>
					                            {$bioscoop->player}
					                        </div>
                                        <div class='video-options'>
				                        	<ul class='player-bottom'>
				                            <li><a href=href='#like' id='this-like-{$media->id}' class='this-action".($rating && $rating->rating=="liked" ? " active":"")."' data-content='' data-action='like' data-data='[\"id\":{$media->id}, \"check\":{$media->userid}]'><i class='fa fa-user'></i><div id='waitPlus-{$media->id}'>{$media->likes}</div> Waiting</a></li>
				                            <li><a href='javascript::void' onclick='comment({$media->id})' class='comments-trigger'><i class='fa fa-comments'></i>{$media->comments} Comment</a></li>
				                            <li><a href='javascript::void' id='shareBtn'><i class='fa fa-share-alt'></i>Share</a><ul><li>".Main::share($media->url,urlencode($media->title), array("facebook", "twitter", "google"))."</li></ul></li>
				                            </ul>
				                        </div>
                                        </div>
                                        <div class='col-sm-5'>
                                            <artical>
                                                <p>{$media->description}</p>
                                            </artical>
                                        </div>
                                        <div class='row'>
										<div class='col-md-12'>
                                        <div class='commentSection' style='display: none' id='commentSection{$media->id}'>
                                        <div class='video-author' data-id='{$media->userid}'></div>".
                        					$this->comments($media->id, $media->url, $media->comments)."
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            
                    </div>";
			$i++;			
		}
		return $html;
	}
	protected function rowMedia(array $list, $keyword = NULL){
		// Loop and format media
		$html = "";
		$i = 0;
		foreach ($list as $media) {
			// Format Media
			$media = $this->formatMedia($media);
			if(!isset($media->profile)) $media->profile ="";
			if(!isset($media->author)) $media->author ="";
			$media->description = Main::truncate($media->description, 200);
			if(!is_null($keyword)){
				$media->description = str_ireplace($keyword,"<strong>$keyword</strong>",$media->description);
			}
			if($media->type == "post"){
				if(!empty($media->file)){
					$media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}'></a>";
				}else{
					$media->player = "";
				}	
			}			
			// Check if logged user has already rated this media and check if nsfw is enabled
			if($this->logged()){
				if(!$this->user->nsfw && $media->nsfw){
					$media->player = "<a href='".$media->url."'><img src='{$this->config["url"]}/static/nsfw_big.png' alt=''></a>";
				}
				$rating = $this->db->get("rating", array("mediaid" => $media->id, "userid" => $this->user->id),array("limit" => "1"));
			}else{
				if($media->nsfw){
					$media->player = "<img src='{$this->config["url"]}/static/nsfw_big.jpg' alt=''>";
				}				
				$rating = NULL;
			}		
			if($i > 0 && $i%5==0)	$html .= $this->ads(728);
			
			$sum = $media->likes - $media->dislikes;
			$sum = $sum < 0 ? 0 : $sum;
			$points = $sum == "1" ? "<strong> {$sum}</strong>	".e("Point")."" : "<strong> {$sum}</strong>	".e("Points")."";
			$html .= "<div class='media-row'>
               		<div class='media-player'>{$media->player}</div>
									<div class='panel panel-default'>
										<div class='panel-heading'>
											<div class='row'>
												<div class='col-sm-12 col-md-10'>
													<a href='{$media->profile}' class='media-avatar'>
														<img src='{$this->avatar($media)}' alt='{$media->username}'>
													</a>
													<a href='{$media->url}' title='{$media->title}'>
													".Main::truncate($media->title,100)."
													".($media->featured ? "<span class='featured'>".e("Featured")."</span>":"")."										
													".($media->nsfw ? "<span class='nsfw'>".e("NSFW")."</span>":"")."
													</a>												
												</div>
												<div class='col-md-2 text-right media-stats hidden-xs'>							
													{$points}
												</div>												
											</div>										
										</div>
	                  <div class='media-embed panel-body'>
											<div class='social-media'>
					              <a href='#like' id='this-like-{$media->id}' class='this-action this-tooltip".($rating && $rating->rating=="liked" ? " active":"")."' data-content='".e('Like')."' data-action='like' data-data='[\"id\":{$media->id}, \"check\":{$media->userid}]'><span class='fa fa-chevron-up'></span></a>
					              <a href='#dislike' id='this-dislike-{$media->id}' class='this-action this-tooltip".($rating && $rating->rating=="disliked" ? " active":"")."' data-content='".e('Dislike')."' data-action='dislike' data-data='[\"id\":{$media->id}, \"check\":{$media->userid}]'><span class='fa fa-chevron-down'></span></a>
		                    <a href='{$media->url}#comments' target='_blank' class='comments-trigger'><span class='fa fa-comment'></span> {$media->comments} ".e("Comments")."</a>
		                    ".Main::share($media->url,urlencode($media->title), array("facebook", "twitter", "google"))."
		                  </div>                   		
	                  </div>                            
									</div>
								</div>";	
			$i++;			
		}
		return $html;
	}	
	/**
	 * Get Media from database
	 * @author Emrul
	 * @since  1.0
	 * @param  array  $options Options to filter media
	 * @return object          Media
	 */
	public function getMedia($options = array()){
		// Default Configuration
		$config = array(
								"pagination" => FALSE,
								"type" => NULL,
								"category" => NULL,
								"featured" => 0,
								"order" => "date",
								"limit" => ($this->config["pagelimit"] ? $this->config["pagelimit"] : $this->limit ),
								"userid" => NULL
							);
		// Filter and replace default options
		if(isset($options["order"]) && in_array($options["order"], array("date","id","views","title", "RAND()", "likes", "comments"))) $config["order"] = $options["order"];
		if(isset($options["category"]) && is_numeric($options["category"])) $config["category"] = $options["category"];
		if(isset($options["userid"]) && is_numeric($options["userid"])) $config["userid"] = $options["userid"];
		if(isset($options["featured"]) && $options["featured"] > 0) $config["featured"] = "1";
		if(isset($options["limit"]) && is_numeric($options["limit"])) $config["limit"] = $options["limit"];
		if(isset($options["type"]) && array_key_exists($options["type"], types())) $config["type"] = $options["type"];
		if(isset($options["pagination"]) && $options["pagination"] === TRUE) $config["pagination"] = $options["pagination"];
		
		// Preset data type
		$sort = array();
		$where = array();
		$where["approved"] = "1";

		// Where: Filter Data
		if(!is_null($config["type"])) $where["type"] = $config["type"];
		if(isset($options["url"]) && !is_null($options["url"])) $where["url"] = $options["url"];
		if(isset($options["uniqueid"]) && is_numeric($options["uniqueid"])) $where["uniqueid"] = $options["uniqueid"];
		if(!is_null($config["category"])) $where["catid"] = $config["category"];
		if(!is_null($config["userid"])) $where["userid"] = $config["userid"];
		if($config["featured"]) $where["featured"] = $config["featured"];		

		// Sort: Order & Limit 
		$sort["order"] = $config["order"];
		if($config["pagination"]){
			$sort["limit"] = (($this->page-1)*$config["limit"]).", {$config["limit"]}";
			$sort["count"] = TRUE;
		}else{
			$sort["limit"] = $config["limit"];
		}

		// Fetch Data
		if(isset($options["order"]) && $options["order"] == "trending"){
			$date = date("Y-m-d", strtotime("-1 month"));			
			$media = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.email as email, {$this->config["prefix"]}user.avatar as avatar, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"), "{$this->config["prefix"]}media.approved = '1' AND {$this->config["prefix"]}media.date BETWEEN NOW() AND $date", array("order" => "likes", "limit" => $sort["limit"]));			
			if(!$media){
				$media = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.email as email, {$this->config["prefix"]}user.avatar as avatar, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"), array("approved" => '1'), array("order" => "likes", "limit" => $sort["limit"]));
			}
		}else{
			$media = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.email as email, {$this->config["prefix"]}user.avatar as avatar, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"), $where, $sort);
		}			
		// Count pages
		if($config["pagination"]){
	    if(($this->db->rowCount%$config["limit"])<>0) {
	      $max = floor($this->db->rowCount/$config["limit"])+1;
	    } else {
	      $max = floor($this->db->rowCount/$config["limit"]);
	    }   
	    $this->count = $max;
		}
		return $media;
	}
	/**
	 * Get Subscription of logged user
	 * @author Emrul
	 * @since  1.0
	 */
	public function getSubscription($limit = 2, $userid = NULL){
		if($userid == NULL) $userid = $this->user->id;
		$html ="";
		$lists = $this->db->get(array("custom" => "{$this->config["prefix"]}subscription.*, {$this->config["prefix"]}user.username as author, {$this->config["prefix"]}user.email as email, {$this->config["prefix"]}user.avatar as avatar FROM `{$this->config["prefix"]}subscription` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}subscription.authorid"), array("userid" => $userid), array("limit" => (($this->page-1)*10).", 10"));
    if(($this->db->rowCount%10)<>0) {
      $max = floor($this->db->rowCount/10)+1;
    } else {
      $max = floor($this->db->rowCount/10);
    } 			
		foreach ($lists as $list) {
			$subscription = $this->listMedia($this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"), array("userid" => $list->authorid, "approved" => "1"), array("limit" => 4, "order" => "date")));
			if(!$subscription) continue;
			$html .="<div class='panel panel-default'>
				        <div class='panel-heading'><img src='{$this->avatar($list, 20)}' width='20' alt=''> &nbsp; <a href='".Main::href("user/{$list->author}")."'>".ucfirst($list->author)."</a>
				        	<a href='".Main::href("user/{$list->author}")."' class='btn btn-primary btn-xs pull-right'>".e("View More")."</a>
				        </div>
								<div class='media media-row'>
				           <div class='row'>
					           $subscription
				           </div>
				        </div>  				            
				      </div>";
		}
		$html .= Main::pagination($max, $this->page, Main::href("user/subscriptions?page=%d"));
		return $html;
	}
	/**
	 * Get subscription and merge them into 1 list
	 * @author Emrul
	 * @since  1.0
	 */
	public function getSubscriptionMerge(){
		if(!$this->logged()) return FALSE;
		if(!$list = $this->db->get("subscription", array("userid" => $this->user->id), array("limit" => 5))){
			return;
		}
		$media = array();
		foreach ($list as $data) {			
			$media = array_merge($media, $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"), array("userid" => $data->authorid, "approved" => "1"), array("limit" => 2, "order" => "date")));
		}  		
		if(!$media) return FALSE;
		$html ="<div class='panel panel-default'>
			        <div class='panel-heading'>
			        	<h3><i class='fa fa-indent'></i>".e("Subscriptions")."</h3>
			        	<a href='".Main::href("user/account")."' class='btn btn-primary btn-xs pull-right'>".e("View More")."</a>
			        </div>
			        <div class='media media-wide'>
		           <div class='row'>
			           {$this->listMedia($media, NULL, array(), 4)}
		           </div>
			        </div>          
			      </div>";
		return $html;
	}	
	/**
	 * Generated Acitity list from object
	 * @author Emrul
	 * @since  1.0
	 * @param  array $list Data to format and generate html
	 * @param  string $custom Custom html
	 * @return string       Formatted list
	 */
	protected function listActivity(array $list, $username = NULL){		
		// Loop and format media
		$html = "";
		foreach ($list as $data) {
			$media = $this->db->get("media", array("id" => $data->id));
			if($data->type == "media"){
				$text = e("submitted");
			}elseif($data->type == "fav"){
				$text = e("added to favorites");
			}elseif($data->type == "rating"){
				$text = e("".$data->num);
			}	
			if($data->num != "disliked"){
				$single = $media[0];
				$html .= "<div class='panel panel-default activities'>
										<div class='panel-heading'><strong>{$username}</strong> $text <strong><a href='".Main::href("view/{$single->url}")."'>{$single->title}</a></strong> <span class='pull-right'>".Main::timeago($data->thisdate)."</span></div>
										<div class='panel-body'>
											<div class='row'>
												<div class='media media-grid'>
													{$this->listMedia($media)}
				              	</div>	
											</div>									
										</div>
	              	</div>";
			}				
		}
		return $html;		
	}	
	/**
	 * Server Requests
	 * @since 1.0
	 **/
	protected function server(){

		// Make sure that the request is valid!
		if(!isset($_POST["action"]) || !isset($_POST["token"]) || $_POST["token"] !== $this->config["public_token"]) return $this->server_die();		
		// Generate server request
		$server = Main::clean($_POST["action"],3,TRUE);

		// Shorten URL
		if($server == "shorten"){
			return $this->server_shorten();
		}
		// Live Search
		if($server == "livesearch"){
			return $this->server_livesearch();
		}
		// Switch requests
		$system = array("like","dislike","addtofav","subscribe","follow","getnotify","report","videoedit","comment","submit","notification","playlist_settings","playlist_remove","playlist_add","addto_playlist","clear_notifications");
		$fn = "server_$server";

		// Make sure that user is logged to access protected server requests
		if(!$this->logged()){
			echo "<script>
							login_modal();
							$.notify('".e("You need to login before you can perform this action.")."', 'error');
						</script>";			
			return;
		}			
		if(in_array($server, $system) && method_exists("App",$fn)){
			return $this->$fn();
		}
		return $this->server_die();		
	}	
		/**
		 * Server Error
		 * @since 1.0
		 **/
		private function server_die(){
			return die(header('HTTP/1.1 400 Bad Request', true, 400));
		}	
		/**
		 * Like Media
		 * @since 1.1
		 **/
		private function server_like(){
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			if(!isset($data->check) || !is_numeric($data->check)) $this->server_die();
			// Check if media exists
			if(!$this->db->get("media",array("id" => "?", "approved" => "1", "userid" => "?"), array("limit" => 1), array($data->id, $data->check))){
				echo "<script>
									$.notify('".e("Something went wrong.")."', 'error');
							</script>";
			}
			// Check if already voted once
			if($rated = $this->db->get("rating", array("userid" => $this->user->id, "mediaid" => $data->id), array("limit" => 1))){
				if($rated->rating == "disliked"){
					// Add rating
					$this->db->update("rating",array("rating" => "liked"),array("userid" => $this->user->id, "mediaid" => $data->id));
					// Update Data
					$this->db->update("media", "dislikes = dislikes-1,likes = likes+1", array("id" => $data->id));
					// Add Points
					if($this->config["points"] && !$this->db->get("point",array("action" => "like", "userid" => $this->user->id, "actionid" => $data->id))){
						// Check if user has already been awarded points for this media
						$this->db->insert("point", array(":action" => "like", ":userid" => $this->user->id, ":actionid" => $data->id, ":point" => $this->config["amount_points"]["like"]));
						$this->db->update("user", "points = points+{$this->config["amount_points"]["like"]}", array("id" =>  $this->user->id));
						echo "<script>						
										$.notify('".e("Thank you for Waiting.")."', 'success');
										$.notify('".e("You have earned")." {$this->config["amount_points"]["like"]} ".e("points")."', 'info');
										$('#this-dislike-{$data->id}').removeClass('active');
										$('#this-like-{$data->id}').addClass('active').attr('data-content','".e("Unlike")."');
										$('.share_content').fadeIn();
										$('#share').addClass('active');
								</script>";
					}else{
						echo "<script>
											$.notify('".e("Thank you for Waiting.")."', 'success');
											$('#this-dislike-{$data->id}').removeClass('active');
											$('#this-like-{$data->id}').addClass('active').attr('data-content','".e("Unlike")."');
											$('.share_content').fadeIn();
											$('#share').addClass('active');
									</script>";
					}						
					echo "<script>
									$.notify('".e("Thank you for Waiting.")."', 'success');
									$('#this-dislike-{$data->id}').removeClass('active');
									$('#this-like-{$data->id}').addClass('active').attr('data-content','".e("Unlike")."');
									$('.share_content').fadeIn();
									$('#share').addClass('active');
							</script>";
				}else{
					//	Unlike
					$this->db->delete("rating", array("userid" => $this->user->id, "mediaid" => $data->id, "rating" => "liked"));
					$this->db->update("media", "likes = likes-1,votes = votes-1", array("id" => $data->id));	
					$this->db->delete("temp", array("type" => "notification", "data" => json_encode(array("type" => "liked","user" => $this->user->id,"media" => $data->id)), "filter" => $data->check));				
					// Remove points
					if($this->config["points"] && $this->db->get("point",array("action" => "like", "userid" => $this->user->id, "actionid" => $data->id))){
						// Check if user has already been awarded points for this media
						$this->db->delete("point", array("action" => "like", "userid" => $this->user->id, "actionid" => $data->id));
						$this->db->update("user", "points = points-{$this->config["amount_points"]["like"]}", array("id" =>  $this->user->id));
					}
					echo "<script>
										$('#this-like-{$data->id}').removeClass('active').attr('data-content','".e("Like")."');
										$.notify('".e("Waiting has been removed.")."', 'success');
										var wait = parseInt($('#waitPlus-{$data->id}').text());
										if(wait != 0) console.log($('#waitPlus-{$data->id}').text(wait-1));
								</script>";
				}
			}else{
				// Vote & update media + Add notification
				$this->db->insert("rating", array(":userid" => $this->user->id, ":mediaid" => $data->id, ":rating" => "liked"));
				$this->db->update("media", "likes = likes+1,votes = votes+1", array("id" => $data->id));
				// Notification
				$this->db->insert("temp", array(":type" => "notification", ":data" => json_encode(array("type" => "liked","user" => $this->user->id,"media" => $data->id)), ":filter" => $data->check));				
				// Add Points
				if($this->config["points"] && !$this->db->get("point",array("action" => "like", "userid" => $this->user->id, "actionid" => $data->id))){
					// Check if user has already been awarded points for this media
					$this->db->insert("point", array(":action" => "like", ":userid" => $this->user->id, ":actionid" => $data->id, ":point" => $this->config["amount_points"]["like"]));
					$this->db->update("user", "points = points+{$this->config["amount_points"]["like"]}", array("id" =>  $this->user->id));
					echo "<script>						
									$.notify('".e("Thank you for Waiting.")."', 'success');
									$.notify('".e("You have earned")." {$this->config["amount_points"]["like"]} ".e("points")."', 'info');
									$('#this-dislike-{$data->id}').removeClass('active');
									$('#this-like-{$data->id}').addClass('active').attr('data-content','".e("Unlike")."');
									$('.share_content').fadeIn();
									$('#share').addClass('active');
							</script>";
				}else{
					echo "<script>
										$.notify('".e("Thank you for Waiting.")."', 'success');
										$('#this-dislike-{$data->id}').removeClass('active');
										$('#this-like-{$data->id}').addClass('active').attr('data-content','".e("Unlike")."');
										$('.share_content').fadeIn();
										$('#share').addClass('active');
										var wait = $('#waitPlus-{$data->id}').text()
										console.log($('#waitPlus-{$data->id}').text(parseInt(wait)+1));
								</script>";
				}				
			}
			return;
		}	
		/**
		 * DisLike Media
		 * @since 1.1
		 **/
		private function server_dislike(){
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			if(!isset($data->check) || !is_numeric($data->check)) $this->server_die();
			// Check if media exists
			if(!$this->db->get("media",array("id" => "?", "approved" => "1", "userid" => "?"), array("limit" => 1), array($data->id, $data->check))){
				echo "<script>
									$.notify('".e("Something went wrong.")."', 'error');
							</script>";
			}
			// Check if already voted once
			if($rated = $this->db->get("rating", array("userid" => $this->user->id, "mediaid" => $data->id), array("limit" => 1))){
				if($rated->rating == "liked"){
					// Remove points
					if($this->config["points"] && $this->db->get("point",array("action" => "like", "userid" => $this->user->id, "actionid" => $data->id))){
						// Check if user has already been awarded points for this media
						$this->db->delete("point", array("action" => "like", "userid" => $this->user->id, "actionid" => $data->id));
						$this->db->update("user", "points = points-{$this->config["amount_points"]["like"]}", array("id" =>  $this->user->id));
					}					
					$this->db->update("rating",array("rating" => "disliked"),array("userid" => $this->user->id, "mediaid" => $data->id));
					$this->db->update("media", "dislikes = dislikes+1,likes = likes-1", array("id" => $data->id));
					echo "<script>
									$.notify('".e("Thank you for Waiting.")."', 'success');
									$('#this-dislike-{$data->id}').addClass('active').attr('data-content','".e("Undislike")."');
									$('#this-like-{$data->id}').removeClass('active');
									alert(6);									
							</script>";
					return;					
				}else{
					//	UnDislike
					$this->db->delete("rating", array("userid" => $this->user->id, "mediaid" => $data->id, "rating" => "disliked"));
					$this->db->update("media", "dislikes = dislikes-1,votes = votes-1", array("id" => $data->id));	
					echo "<script>
										$('#this-dislike-{$data->id}').removeClass('active').attr('data-content','".e("Dislike")."');
										$.notify('".e("Waiting has been removed.")."', 'success');
								</script>";
				}
			}else{
				// Vote & update media
				$this->db->insert("rating", array(":userid" => $this->user->id, ":mediaid" => $data->id, ":rating" => "disliked"));
				$this->db->update("media", "dislikes = dislikes+1,votes = votes+1", array("id" => $data->id));
				// Notification
				$this->db->insert("temp", array(":type" => "notification", ":data" => json_encode(array("type" => "disliked","user" => $this->user->id,"media" => $data->id)), ":filter" => $data->check));								
				echo "<script>
								$.notify('".e("Thank you for Waiting.")."', 'success');
								$('#this-dislike-{$data->id}').addClass('active').attr('data-content','".e("Undislike")."');
								$('#this-like-{$data->id}').removeClass('active');	
								alert(7);							
						</script>";
				return;
			}
		}	
		/**
		 * Add to favorite
		 * @since 1.0
		 **/
		private function server_addtofav(){
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			if(!isset($data->check) || !is_numeric($data->check)) $this->server_die();
			// Check if media exists
			if(!$this->db->get("media",array("id" => "?", "approved" => "1", "userid" => "?"), array("limit" => 1), array($data->id, $data->check))){
				echo "<script>
									$.notify('".e("Something went wrong.")."', 'error');
							</script>";
			}
			if($this->db->get("favorite", array("userid" => $this->user->id, "mediaid" => $data->id))){
				$this->db->delete("favorite", array("userid" => $this->user->id, "mediaid" => $data->id));
				echo "<script>
									$('#this-addtofav').removeClass('active').attr('data-content','".e("Add to favorites")."');
									$.notify('".e("Removed from favorites.")."', 'success');
							</script>";
			}else{
				$this->db->insert("favorite", array(":userid" => $this->user->id, ":mediaid" => $data->id));
				// Notification
				$this->db->insert("temp", array(":type" => "notification", ":data" => json_encode(array("type" => "faved","user" => $this->user->id,"media" => $data->id)), ":filter" => $data->check));								
				echo "<script>
									$('#this-addtofav').addClass('active').attr('data-content','".e("Remove from favorites")."');
									$.notify('".e("Added to favorites.")."', 'success');
							</script>";		
			}	
			return;
		}
		/**
		 * Subscribe to video
		 * @since 1.1
		 **/
		private function server_subscribe(){
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			// Check if media exists
			if(!$this->db->get("subscription",array("authorid" => "?", "userid" => "?"), array("limit" => 1), array($data->id, $this->user->id))){
				if($this->user->id == $data->id){
					echo "<script>
									$.notify('".e("You just subscribed to yourself.")."', 'info');
								</script>";						
				}				
				// Subscribe
				$this->db->update("user", "subscribers = subscribers+1", array("id" => $data->id));
				$this->db->insert("subscription", array(":userid" => $this->user->id, ":authorid" => $data->id));
				// Notification
				$this->db->insert("temp", array(":type" => "notification", ":data" => json_encode(array("type" => "subbed","user" => $this->user->id)), ":filter" => $data->id));								
				if($this->config["points"] && !$this->db->get("point",array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id))){
					// Check if user has already been awarded points for this action
					$this->db->insert("point", array(":action" => "subscribe", ":userid" => $this->user->id, ":actionid" => $data->id, ":point" => $this->config["amount_points"]["subscribe"]));
					$this->db->update("user", "points = points+{$this->config["amount_points"]["subscribe"]}", array("id" =>  $this->user->id));					
					echo "<script>
										$('#this-subscribe').addClass('active').attr('data-content','".e("Unsubscribe")."');							
										$.notify('".e("Subscribed to this user.")."', 'success');
										$.notify('".e("You have earned")." {$this->config["amount_points"]["subscribe"]} ".e("points")."', 'info');													
								</script>";						
				}else{
					echo "<script>
										$('#this-subscribe').addClass('active').attr('data-content','".e("Unsubscribe")."');
										$.notify('".e("Subscribed to this user.")."', 'success');
								</script>";						
				}
			}else{
				// Unsubscribe
				$this->db->delete("subscription", array("userid" => $this->user->id, "authorid" => $data->id));
				$this->db->delete("temp", array("type" => "notification", "data" => json_encode(array("type" => "subbed","user" => $this->user->id)), "filter" => $data->id));
				$this->db->update("user", "subscribers = subscribers-1", array("id" => $data->id));
				// Remove points
				if($this->config["points"] && $this->db->get("point",array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id))){
					// Check if user has already been awarded points for this media
					$this->db->delete("point", array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id));
					$this->db->update("user", "points = points-{$this->config["amount_points"]["subscribe"]}", array("id" =>  $this->user->id));
				}						
				echo "<script>
									$('#this-subscribe').removeClass('active').attr('data-content','".e("Subscribe")."');
									$.notify('".e("Unsubscribed from this user.")."', 'success');
							</script>";		
			}
			return;
		}
		private function server_follow(){
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			// Check if media exists
			if(!$this->db->get("follow",array("catid" => "?", "userid" => "?"), array("limit" => 1), array($data->id, $this->user->id))){
				if($this->user->id == $data->id){
					echo "<script>
									$.notify('".e("You just Follow to yourself.")."', 'info');
								</script>";						
				}				
				// Subscribe
				$this->db->update("user", "subscribers = subscribers+1", array("id" => $data->id));
				$this->db->insert("follow", array(":userid" => $this->user->id, ":catid" => $data->id));
				// Notification
				$this->db->insert("temp", array(":type" => "notification", ":data" => json_encode(array("type" => "subbed","user" => $this->user->id)), ":filter" => $data->id));								
				if($this->config["points"] && !$this->db->get("point",array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id))){
					// Check if user has already been awarded points for this action
					$this->db->insert("point", array(":action" => "subscribe", ":userid" => $this->user->id, ":actionid" => $data->id, ":point" => $this->config["amount_points"]["subscribe"]));
					$this->db->update("user", "points = points+{$this->config["amount_points"]["subscribe"]}", array("id" =>  $this->user->id));					
					echo "<script>
										$('#this-subscribe').addClass('active').attr('data-content','".e("Unsubscribe")."');							
										$.notify('".e("Subscribed to this user.")."', 'success');
										$.notify('".e("You have earned")." {$this->config["amount_points"]["subscribe"]} ".e("points")."', 'info');													
								</script>";						
				}else{
					echo "<script>
										$('#this-subscribe').addClass('active').attr('data-content','".e("Unsubscribe")."');
										$.notify('Thanks for Fllowing', 'success');
								</script>";						
				}
			}else{
				// Unsubscribe
				$this->db->delete("follow", array("userid" => $this->user->id, "catid" => $data->id));
				$this->db->delete("temp", array("type" => "notification", "data" => json_encode(array("type" => "subbed","user" => $this->user->id)), "filter" => $data->id));
				$this->db->update("user", "subscribers = subscribers-1", array("id" => $data->id));
				// Remove points
				if($this->config["points"] && $this->db->get("point",array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id))){
					// Check if user has already been awarded points for this media
					$this->db->delete("point", array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id));
					$this->db->update("user", "points = points-{$this->config["amount_points"]["subscribe"]}", array("id" =>  $this->user->id));
				}						
				echo "<script>
									$('#this-subscribe').removeClass('active').attr('data-content','".e("Subscribe")."');
									$.notify('You Just Unfollow', 'success');
							</script>";		
			}
			return;
		}
	private function server_getnotify(){
		$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));
		// Validate data
		if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
		// Check if media exists
		if(!$this->db->get("notification",array("mediaid" => "?", "userid" => "?"), array("limit" => 1), array($data->id, $this->user->id))){
			if($this->user->id == $data->id){
				echo "<script>
									$.notify('".e("Notification On.")."', 'info');
								</script>";
			}
			// Subscribe
			//$this->db->update("user", "subscribers = subscribers+1", array("id" => $data->id));
			$this->db->insert("notification", array(":userid" => $this->user->id, ":mediaid" => $data->id));
			// Notification
			$this->db->insert("temp", array(":type" => "notification", ":data" => json_encode(array("type" => "subbed","user" => $this->user->id)), ":filter" => $data->id));
			if($this->config["points"] && !$this->db->get("point",array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id))){
				// Check if user has already been awarded points for this action
				$this->db->insert("point", array(":action" => "subscribe", ":userid" => $this->user->id, ":actionid" => $data->id, ":point" => $this->config["amount_points"]["subscribe"]));
				$this->db->update("user", "points = points+{$this->config["amount_points"]["subscribe"]}", array("id" =>  $this->user->id));
				echo "<script>
										$('#this-subscribe').addClass('active').attr('data-content','".e("Unsubscribe")."');
										$.notify('".e("Subscribed to this user.")."', 'success');
										$.notify('".e("You have earned")." {$this->config["amount_points"]["subscribe"]} ".e("points")."', 'info');
								</script>";
			}else{
				echo "<script>
										$('#this-subscribe').addClass('active').attr('data-content','".e("Unsubscribe")."');
										$.notify('Thanks for Notification On', 'success');
								</script>";
			}
		}else{
			// Notification Off
			$this->db->delete("notification", array("userid" => $this->user->id, "mediaid" => $data->id));
			$this->db->delete("temp", array("type" => "notification", "data" => json_encode(array("type" => "subbed","user" => $this->user->id)), "filter" => $data->id));
			//$this->db->update("user", "subscribers = subscribers-1", array("id" => $data->id));
			// Remove points
			if($this->config["points"] && $this->db->get("point",array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id))){
				// Check if user has already been awarded points for this media
				$this->db->delete("point", array("action" => "subscribe", "userid" => $this->user->id, "actionid" => $data->id));
				$this->db->update("user", "points = points-{$this->config["amount_points"]["subscribe"]}", array("id" =>  $this->user->id));
			}
			echo "<script>
									$('#this-subscribe').removeClass('active').attr('data-content','".e("Subscribe")."');
									$.notify('Notification Off', 'success');
							</script>";
		}
		return;
	}
		/**
		 * Add comment
		 * @author Emrul
		 * @since  1.2.1
		 */
		private function server_comment(){
			// Check to make user is logged in
			if(!$this->logged()){
				echo "<script type='text/javascript'>	
								$('#comment-form .return-data').html('<div class=\'alert alert-danger\'>".e("You need to login to perform this action.")."</div>');
								$.notify('".e("Something went wrong.")."', 'error');
							</script>";
				return;							
			}
			if(empty($this->user->username)){
				echo "<script type='text/javascript'>	
								window.location = '".Main::href('user/settings')."';
							</script>";
				return;						
			}
			// Clean comment
			$_POST["comment"] = Main::clean($_POST["comment"], 3, TRUE);
			if(!is_numeric($_POST["media"]) || !is_numeric($_POST["parentid"]) || !is_numeric($_POST["user"])){
				return $this->server_die();
			}
			$_POST["media"] = Main::clean($_POST["media"], 3, TRUE);			
			$_POST["parentid"] = Main::clean($_POST["parentid"], 3, TRUE);			
			// Check comment
			if(empty($_POST["comment"]) || strlen($_POST["comment"]) < 3 || strlen($_POST["comment"] > 200)) {
				echo "<script type='text/javascript'>	
								$('#comment-form .return-data').html('<div class=\'alert alert-danger\'>".e("Comment cannot be empty, less than 3 characters or more than 200 characters.")."</div>');
								$.notify('".e("Something went wrong.")."', 'error');
							</script>";		
				return;		
			}
			// Check comment flood + Duplicate
			$date = date("Y-m-d H:i:s",time()-30);
			// if($this->db->get("comment","mediaid = ? AND userid =? AND date > ?",array("limit" => 1),array($_POST["media"],$this->user->id, $date))){
			// 	echo "<script type='text/javascript'>	
			// 					$('#comment-form .return-data').html('<div class=\'alert alert-danger\'>".e("Please wait 30 seconds before commenting again.")."</div>');
			// 					$.notify('".e("Something went wrong.")."', 'error');
			// 				</script>";
			// 	return;															
			// }
			if($again = $this->db->get("comment","mediaid = ? AND userid =? AND body = ?",array("limit" => 1),array($_POST["media"],$this->user->id, $_POST["comment"]))){
				echo "<script type='text/javascript'>	
								$('#comment-form .return-data').html('<div class=\'alert alert-danger\'>".e("You seem to have already posted this comment ").Main::timeago($again->date).".</div>');
								$.notify('".e("Something went wrong.")."', 'error');
							</script>";
				return;															
			}			
			// Prepare Data
			$data = array(
					":mediaid" => $_POST["media"],
					":userid" => $this->user->id,
					":parentid" => $_POST["parentid"],
					":body" => $_POST["comment"]
				);
			if($this->db->insert("comment",$data)){
				$this->db->update("media", "comments = comments+1", array("id" => $_POST["media"]));
				// Notification
				$this->db->insert("temp", array(":type" => "notification", ":data" => json_encode(array("type" => "commented","user" => $this->user->id,"media" => $_POST["media"])), ":filter" => $_POST["user"]));				
				$_POST["comment"] = $this->at($_POST["comment"], Main::href("user/"));
				$_POST["comment"] = Main::hash($_POST["comment"], Main::href("search/"));
				if($_POST["parentid"]){
					$html="<li class='media'><a class='pull-left' href='".Main::href("user/{$this->user->username}")."'><img class='media-object' src='{$this->avatar($this->user)}' width='35' alt=''></a><div class='media-body'><h4 class='media-heading'><a href='".Main::href("user/{$this->user->username}")."' class='author'>".ucfirst($this->user->username)."</a><span>".Main::timeago(date("Y-m-d H:i:s"))."</span></h4>{$_POST["comment"]}</div></li>";			
				}else{
					$html="<li class='media'><a class='pull-left' href='".Main::href("user/{$this->user->username}")."'><img class='media-object' src='{$this->avatar($this->user)}' width='50' alt=''></a><div class='media-body'><h4 class='media-heading'><a href='".Main::href("user/{$this->user->username}")."' class='author'>".ucfirst($this->user->username)."</a><span>".Main::timeago(date("Y-m-d H:i:s"))."</span></h4>{$_POST["comment"]}</div></li>";			
				}		
				
				// Add Points
				if($this->config["points"] && !$this->db->get("point",array("action" => "comment", "userid" => $this->user->id, "actionid" => $_POST["media"]))){
					// Check if user has already been awarded points for this action
					$this->db->insert("point", array(":action" => "comment", ":userid" => $this->user->id, ":actionid" => $_POST["media"], ":point" => $this->config["amount_points"]["comment"]));
					$this->db->update("user", "points = points+{$this->config["amount_points"]["comment"]}", array("id" =>  $this->user->id));					
					echo "<script type='text/javascript'>					
								$('#comment-form textarea').val('');
								".($_POST["parentid"] ? "$('#comments_system .media-list li#comment-{$_POST["parentid"]}' > .media-body').append(\"$html\");" : "$('#comments_system .media-list').prepend(\"$html\");")."								
								$.notify('".e("Your comment has been successfully added.")."', 'success');
								".(empty($this->user->name) ? "$.notify('".e("Please choose a username before continuing.")."', 'error');" : "")."
								$.notify('".e("You have earned")." {$this->config["amount_points"]["comment"]} ".e("points")."', 'info');
							</script>";															
				}else{
					echo "<script type='text/javascript'>					
								$('#comment-form textarea').val('');
								".(empty($this->user->name) ? "$.notify('".e("Please choose a username before continuing.")."', 'error');" : ($_POST["parentid"] ? "$('#comments_system .media-list li#comment-{$_POST["parentid"]} > .media-body').append(\"$html\");" : "$('#comments_system .media-list').prepend(\"$html\");"))."
								
								$.notify('".e("Your comment has been successfully added.")."', 'success');
							</script>";					
				}
				return;							
			}		
		}
	private function server_videoedit(){
		// Report
		$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));
		if(isset($_POST["report"]) || $data->check == "comment"){
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			if(!isset($data->check) || !in_array($data->check, array("media","comment","user"))) $this->server_die();
			// Check if this user has already report this video
			$report = array(
				"abuse" => "Abusive Content",
				"copy" => "Copyright Breach",
				"violent" => "Violent Content",
				"spam" => "Spam"
			);
			$values = json_encode(array(
				"type" => $data->check,
				"id" => $data->id,
				"user" => $this->user->id,
				"comment" => isset($_POST["report"]) && isset($report[$_POST["report"]]) ? $report[$_POST["report"]] : 'To be reviewed'
			));
			if(!$this->db->get("temp", array("type" => "{$data->check}_report", "data" => $values))){
				$data = array(
					":type" => "{$data->check}_report",
					":filter" => $this->user->id,
					":data" => $values
				);
				$this->db->insert("temp",$data);
				echo "<script type='text/javascript'>
									$(document).modal({close: 1});
									$.notify('".e("You have successfully report this. We will moderate it soon. Thanks!")."', 'success');
								</script>";
			}else{
				echo "<script type='text/javascript'>
									$(document).modal({close: 1});
									$.notify('".e("You have already reported this once.")."', 'error');
								</script>";
			}
			return;
		}else{
			// Return HTML
			$html = "<form action='".Main::href("upload/media")."' enctype='multipart/form-data' method='post'><textarea name='description' id='inputDes' class='yourshow' rows='1' required='required' placeholder='Write something about your upcoming video.. (Maximum 160 characters)'></textarea><input type='hidden' name='type' value='video'><div class='date-pic-area hidden'><input type='text' name='release_date' class='form-control release_date' placeholder='Select Date and Time' id='datetimepickeredit'/></div><button type='submit' name='report_token' class='btn btn-primary'>".e("Submit")."</button></form>";
			echo '<script>$("#modal-alert #modal-content").html("'.$html.'"); $("#report-form input[type=hidden]").val($("#this-report").data("data"));$(document).ready(function(){$("#datetimepickeredit").datetimepicker({format: "M d, Y H:i:s"});});</script>';
			return;
		}
	}
		/**
		 * Report media, user or comment
		 * @author Emrul
		 * @since  1.0
		 */
	private function server_report(){
			// Report
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));			
			if(isset($_POST["report"]) || $data->check == "comment"){				
				// Validate data
				if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
				if(!isset($data->check) || !in_array($data->check, array("media","comment","user"))) $this->server_die();
				// Check if this user has already report this video
				$report = array(
						"abuse" => "Abusive Content",
						"copy" => "Copyright Breach",
						"violent" => "Violent Content",
						"spam" => "Spam"
					);
				$values = json_encode(array(
						"type" => $data->check,
						"id" => $data->id,
						"user" => $this->user->id,
						"comment" => isset($_POST["report"]) && isset($report[$_POST["report"]]) ? $report[$_POST["report"]] : 'To be reviewed'
					));
				if(!$this->db->get("temp", array("type" => "{$data->check}_report", "data" => $values))){
					$data = array(
							":type" => "{$data->check}_report",
							":filter" => $this->user->id,
							":data" => $values							
						);
					$this->db->insert("temp",$data);		
					echo "<script type='text/javascript'>
									$(document).modal({close: 1});
									$.notify('".e("You have successfully report this. We will moderate it soon. Thanks!")."', 'success');
								</script>";					
				}else{
					echo "<script type='text/javascript'>			
									$(document).modal({close: 1});		
									$.notify('".e("You have already reported this once.")."', 'error');
								</script>";
				}	
				return;			
			}else{
				// Return HTML
				$html = "<form action='#report' id='report-form' method='post'><p>".e("Please choose the most appropriate reason why you want flag this page. Note that we will investigate this and if it indeed breaches our terms of service we will remove it.")."</p><div class='form-group'><label for='reason' class='control-label'>".e('Reason')."</label><select name='reason' class='form-control' id='reason'><option value='abuse'>".e("Abusive Content")."</option><option value='violent'>".e("Violent Content")."</option><option value='spam'>".e("Spam")."</option><option value='copy'>".e("Copyright Breach")."</option></select></div><input type='hidden' name='data'><button type='submit' name='report_token' class='btn btn-primary'>".e("Submit")."</button></form>";
				echo '<script>$("#modal-alert #modal-content").html("'.$html.'"); $("#report-form input[type=hidden]").val($("#this-report").data("data"));</script>';
				return;
			}
		}
		/**
		 * Add Playlist
		 * @author Emrul
		 * @since  1.4
		 */
		private function server_playlist_add(){
			// Return HTML
			$html = "<form action='".Main::href("user/account/playlists")."' method='post'><div class='form-group'><label class='control-label'>".e('Name')."</label><input type='text' name='name' value='' class='form-control'></div><div class='form-group'><label class='control-label'>".e('Description')."</label><input type='text' name='description' value = '' class='form-control'></div><div class='form-group'><label for='public' class='control-label'>".e('Privacy')."</label><select name='public' class='form-control' id='public'><option value='1'>".e("Public")."</option><option value='0'>".e("Private")."</option></select></div>".Main::csrf_token(TRUE)."<button type='submit' name='playlist_add_token' class='btn btn-primary'>".e("Submit")."</button></form>";
			echo '<script>$("#modal-alert #modal-content").html("'.$html.'");</script>';
			return;
		}		
		/**
		 * Edit Playlist
		 * @author Emrul
		 * @since  1.4
		 */
		private function server_playlist_settings(){
			// Report
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));		
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			if(!$playlist = $this->db->get("playlist", array("id" => "?", "userid" => "?"), array("limit" => "1"), array($data->id, $this->user->id))) $this->server_die();			
			// Return HTML
			$html = "<form action='".Main::href("user/account/playlists?id={$playlist->uniqueid}")."' method='post'><div class='form-group'><label class='control-label'>".e('Name')."</label><input type='text' name='name' value='{$playlist->name}' class='form-control'></div><div class='form-group'><label class='control-label'>".e('Description')."</label><input type='text' name='description' value = '{$playlist->description}' class='form-control'></div><div class='form-group'><label for='public' class='control-label'>".e('Privacy')."</label><select name='public' class='form-control' id='public'><option value='1' ".($playlist->public ? "selected": "").">".e("Public")."</option><option value='0' ".(!$playlist->public ? "selected": "").">".e("Private")."</option></select></div>".Main::csrf_token(TRUE)."<button type='submit' name='playlist_token' class='btn btn-primary'>".e("Submit")."</button><a href='".Main::href("user/account/playlists".Main::nonce("delete_playlist-{$playlist->uniqueid}")."&id={$playlist->uniqueid}")."' class='btn btn-danger pull-right'>".e("Delete")."</a></form>";
			echo '<script>$("#modal-alert #modal-content").html("'.$html.'");</script>';
			return;
		}			
		/**
		 * Remove Playlist
		 * @author Emrul
		 * @since  1.4
		 */
		private function server_playlist_remove(){
			// Report
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));		
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			if(!isset($data->check) || !is_numeric($data->check)) $this->server_die();
			$last = $this->db->get("toplaylist",array("playlistid" => $data->check), array("limit" => 1));
			$this->db->delete("toplaylist", array("mediaid" => $data->id, "playlistid" => $data->check));
			$this->db->update("playlist", "num = num-1", array("id" =>  $data->check));	
			$this->db->update("playlist", array("lastid" => $last->mediaid), array("id" =>  $data->check));	
			echo "<script>$('#media-{$data->id}').fadeOut();</script>";
			return;
		}
		/**
		 * Addto Playlist
		 * @author Emrul
		 * @since  1.5.1
		 */		
		private function server_addto_playlist(){
			// Report
			$data = json_decode(str_replace("[","{", str_replace("]","}", Main::clean($_POST["data"]))));		
			// Validate data
			if(!isset($data->id) || !is_numeric($data->id)) $this->server_die();
			if(!isset($data->check) || !is_numeric($data->check)) $this->server_die();

			if($this->db->get("toplaylist", array("mediaid" => $data->id, "playlistid" => $data->check), array("limit" => 1))){
				$this->db->update("playlist", "num = num-1,lastid = {$data->id}", array("id" =>  $data->check));	
				$this->db->delete("toplaylist", array("mediaid" => $data->id, "playlistid" => $data->check));
				echo "<script>$('#playlist-{$data->check} a').removeClass('active');</script>";
			}else{
				// Count
				$check_limit = $this->db->get("toplaylist", array("playlistid" => $data->check));
				if($this->db->rowCountAll > 50){
					echo "<script type='text/javascript'>			
									$.notify('".e("You have reach your maximum limit of 50 media per playlist.")."', 'error');
								</script>";		
					return;	
				}				
				$this->db->update("playlist", "num = num+1,lastid = {$data->id}", array("id" => $data->check));	
				$this->db->insert("toplaylist", array(":mediaid" => $data->id, ":playlistid" => $data->check));
				echo "<script>$('#playlist-{$data->check} a').addClass('active');</script>";
			}
		}				
		/**
		 * Submit URL
		 * @author Emrul
		 * @since  1.0
		 */
		private function server_submit(){
			$url = Main::clean($_POST["url"],3,true);
			if(!Main::is_url($url)) {
				echo "<script type='text/javascript'>			
								$('#submit-url .return-data').html('<div class=\'alert alert-danger\'>".e("Invalid URL or provider not currently supported.")."</div>');		
								$.notify('".e("Something went wrong.")."', 'error');
							</script>";		
				return;		
			}
			// Include Importer
			include(ROOT."/includes/Media.class.php");
      $media = new Media(
                      array(
                          "yt_api" => $this->config["yt_api"],
                          "vm_api" => $this->config["vm_api"]
                          )
                      );
			$data = $media->import($url, "100%");			
			if(!isset($data->error)){
				$data->desc = urlencode($data->desc);
				$data->title = urlencode($data->title);
				// Generate Upload form
				echo "<script type='text/javascript'>			
								var form = $('#submit-url');
										form.find('#title').val(decodeURIComponent((\"{$data->title}\"+'').replace(/\+/g, '%20')));
										form.find('#description').val(decodeURIComponent((\"{$data->desc}\"+'').replace(/\+/g, '%20')));
										form.find('#tags').val(\"{$data->tag}\");
										form.find('#preview').html(\"{$data->code}<hr>\");
										form.find('.this-hide').fadeIn('slow');
								$.notify('".e("Media has been imported. Please review the information.")."', 'success');
							</script>";
				return;        	
			}else{
				// Return Error
				echo "<script type='text/javascript'>	
								$('#submit-url .return-data').html('<div class=\'alert alert-danger\'>"._($data->error)."</div>');		
								$.notify('"._($data->error)."', 'error');
							</script>";
				return;				
			}
		}
		/**
		 * Get Notifications
		 * @author Emrul
		 * @since  1.0
		 */
		private function server_notification(){
			$notifications = $this->db->get("temp", array("type" => "notification","filter" => $this->user->id), array("order" => "date","limit" => 20));
			$html = "";
			foreach ($notifications as $notification) {
				$data = json_decode($notification->data);
				if($data->type == "liked"){
					$media = $this->db->get("media", array("id" => "?"),array("limit" => 1), array($data->media));
					$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
					if($_user && $media){
						$_user->username = ucfirst($_user->username);
						$html .="<li ".($notification->viewed == 0 ? "class='notification_new'":"")."><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a> ".e("liked")." <a href='".Main::href("view/{$media->url}")."'><strong>".htmlentities($media->title)."</strong></a></li>";
					}
				}elseif($data->type == "subbed"){
					$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
					if($_user){
						$_user->username = ucfirst($_user->username);
						$html .="<li ".($notification->viewed == 0 ? "class='notification_new'":"")."><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a> ".e("subscribed to you")."</li>";
					}
				}elseif($data->type == "commented"){
					$media = $this->db->get("media", array("id" => "?"),array("limit" => 1), array($data->media));
					$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
					if($media && $_user){
						$_user->username = ucfirst($_user->username);
						$html .="<li ".($notification->viewed == 0 ? "class='notification_new'":"")."><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a> ".e("commented on")." <a href='".Main::href("view/{$media->url}")."'><strong>".htmlentities($media->title)."</strong></a></li>";
					}
				}
			}
			$this->db->update("temp",array("viewed" => "1"), array("type" => "notification","filter" => $this->user->id, "viewed" => "0"));
			echo "<script>
							$('#notifications .dropdown-holder ul').html(\"$html\");
							$('#notifications .dropdown-holder').toggle();
							$('.fa-bell').removeClass('fa-new');
						</script>";
		}
		/**
		 * Clear Notifications
		 * @since  1.5
		 */
		private function server_clear_notifications(){
			$this->db->delete("temp", array("filter" => $this->user->id));
			echo "<script>							
							$('.table tbody').html('');
						</script>";					
		}
		/**
		 * Generate Short URL
		 * @author Emrul
		 * @since  1.0
		 */
		private function server_shorten(){
			if(!isset($_POST["url"])) return FALSE;
			// Run adfly
			if($this->config["shorturl"] == "adfly"){
				include(PLUGINS."/adfly.php");
				echo shorten_url($_POST["url"]);
				return;
			}elseif($this->config["shorturl"] == "google"){
				include(PLUGINS."/google.php");
				echo shorten_url($_POST["url"]);				

			}elseif($this->config["shorturl"] == "custom"){
				$data = file_get_contents(str_replace("@URL@",urlencode($_POST["url"]),$this->config["custom_shorturl"]));
				echo $data;
			}
		}	
		/**
		 * Live Search Media
		 * @author Emrul
		 * @since  1.0
		 */
		private function server_livesearch(){
			if(!isset($_POST["value"])) return FALSE;			
			$q = Main::clean($_POST["value"], 3, TRUE);
			$media = $this->db->search("media",array("title" => "?"), array("limit" => 10), array("%$q%"));
			if(!$media) {echo " "; return;}

			echo "<div class='panel panel-default' id='live-search'>";
				echo "<ul>";
					foreach ($media as $data) {
						$data = $this->formatMedia($data);
						$data->title = str_ireplace($q, "<strong>$q</strong>", $data->title);
						echo "<li><a href='{$data->url}?ref=livesearch'>{$data->title}</a> ".($data->featured ? "<span class='featured'>".e("Featured")."</span>":"")."	".($data->nsfw ? "<span class='nsfw'>".e("NSFW")."</span>":"")."</li>";
					}
				echo "</ul>";
			echo "</div>";
		}	
	/**
	 * API 
	 * @since  1.5
	 */
	protected function api(){
		include(ROOT."/includes/API.class.php");
		return new API($this->config, $this->db, array($this->do, $this->id));
	}		
	/**
	 * Avatar
	 */
	public function avatar($user, $size = 48){
		if(empty($user->avatar)){
			return $this->http."gravatar.com/avatar/".md5($user->email)."?s={$size}&r=pg&d=mm";
		}else{
			return $this->config["url"]."/content/user/{$user->avatar}";
		}
	}
	/**
	 * Display advertisement - Note: The class name is the size+1 to trick some adblockers
	 * @since 1.1	 	 
	 */	
	public function ads($size,$text=FALSE, $breadcrumb=""){		
		if(in_array($size, array("728","468","300","resp","preroll"))){			
			// Get Ads
			if($this->config["ads"]){
				if(mobile()) {
					$ad = $this->db->get("ads", array("type" => "resp", "enabled" => "1"), array("limit" => "1", "order" => "RAND()"));
					if(!$ad) return FALSE;
					$this->db->update("ads", "impression = impression + 1", array("id" => $ad->id));
					return "<div class='ads ads-rep clearfix'>{$ad->code}</div>";	
				}
				$ad = $this->db->get("ads", array("type" => $size, "enabled" => "1"), array("limit" => "1", "order" => "RAND()"));
				if(!$ad) return FALSE;
				$this->db->update("ads", "impression = impression + 1", array("id" => $ad->id));
				return "<div class='ads ads-".($size+1)." clearfix'>{$ad->code}</div>";				
			}
		}
		return;		
	}  		
	/**
	 * Get Template
	 * @since 1.4
	 **/
	protected function t($template){
    if(!file_exists(THEME."/$template.php")) die("<p class='alert alert-danger' style='margin: 10px;'><strong>System Error!</strong> File ($template.php) is missing from the <strong>themes/{$this->config["theme"]}/</strong> folder. Please make sure to upload it!</p>");
    return THEME."/$template.php";
 	}	
  /**
   *  Generate & Validate Unique ID
   *  @param none
   *  @return valid id
   *  @since 1.0
   */ 
  protected function uniqueid(){
    $l=5; 
    $i=0; 
    while(1) {
      if($i >='100') { $i=0; $l=$l+1; };
      $unique=Main::strrand($l);
      if(!$this->db->get("media",array("uniqueid"=>$unique))) {
        return $unique;
        break;
      }
      $i++;
    }   
  }    	
	/**
	 * Filter
	 * @since 1.0 
	 **/
	protected function filter($filter=null){
		if(is_null($filter)){
			if(!empty($this->do) || !empty($this->id)) die($this->_404());
		}else{
			if(!empty($filter)) die($this->_404());
		}
	}  
/**
 * Languages
 * @since 1.0
 **/	
  private function lang($form=TRUE){
		if($form){
			$lang="<option value='en'".(($this->lang=="" || $this->lang=="en")?"selected":"").">English</option>";
		}else{
			$lang="<a href='?lang=en'>English</a>";
		}
    foreach (new RecursiveDirectoryIterator(ROOT."/includes/languages/") as $path){
      if(!$path->isDir() && $path->getFilename()!=="." && $path->getFilename()!==".." && $path->getFilename()!=="lang_sample.php" && $path->getFilename()!=="index.php" && Main::extension($path->getFilename())==".php"){  
          $data=token_get_all(file_get_contents($path));
          $data=$data[1][1];
          if(preg_match("~Language:\s(.*)~", $data,$name)){
            $name="".strip_tags(trim($name[1]))."";
          }                  
        $code=str_replace(".php", "" , $path->getFilename());
        if($form){
					$lang.="<option value='".$code."'".($this->lang==$code?"selected":"").">$name</option>";
        }else{
					$lang.="<a href='?lang=$code'>$name</a>";	
        }
      }
    }  
    return $lang;	
  }	 	
  /**
   * Extract @user
   * @author Emrul
   * @since  1.1
   */
  protected function at($text, $url = NULL){
    preg_match_all("/(@\w+)/", $text, $at);
    if(!isset($at[0][0])) return $text;
    if(!is_null($url)){
      foreach ($at[0] as $ats) {
      	if(!$this->db->get("user", array("username" => str_replace("@","",strtolower($ats))), array("limit" => 1))) continue;
        $text = str_replace($ats, "<a href='$url".str_replace("@","",strtolower($ats))."' class='user-at'>$ats</a>", $text);
      }
      return $text;
    }
    return $at;
  } 
  // End of File 
}