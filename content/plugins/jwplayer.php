<?php 
/**
 * ====================================================================================
 *                           Easy Media Script (c) KBRmedia
 * ----------------------------------------------------------------------------------
 * @copyright This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *  from another site or received it from someone else than me, then you are engaged
 *  in an illegal activity. You must delete this software immediately or buy a proper
 *  license from http://codecanyon.net/user/KBRmedia/portfolio?ref=KBRmedia.
 *
 *  Thank you for your cooperation and don't hesitate to contact me if anything :)
 * ====================================================================================
 */
	function jwplayer_init($config){
		global $jwplayer;
		$jwplayer = $config;
	}

	function jwplayer($media){
		global $jwplayer, $db, $config;
		// check if players is set
		if($jwplayer["mode"] == "cloud"){
				if(empty($jwplayer["url"])) return;
				// Add VideoJS Library
				Main::add($jwplayer["url"],"script",FALSE); // JS
		}else{
			Main::add($config["url"]."/content/plugins/jwplayer.js","script",FALSE); // JS
			Main::add("<script>
						jwplayer.key='enaSseHIwj6O/Zpf3GPStcqpF6Ff2hjP8jRx/3GgYA8NY5k0ysqUuBQSlxRMJ4/p';
					</script>","custom",FALSE); // SWF
		}

		$media = $media["media"];
		$loop = 0;
		$autoplay = 0;
		// Get playlist
		if(isset($_GET["playlist"]) && isset($_GET["index"]) && is_numeric($_GET["index"])){
			$autoplay = "autoplay";
			$playlist = $db->get("playlist", array("uniqueid" => "?"), array("limit" => "1"),array($_GET["playlist"]));
			if($playlist){
				$list = $db->get("toplaylist", array("playlistid" => $playlist->id));
				if(isset($list[$_GET["index"]])) {
					$next = $db->get("media", array("id" => "?"), array("limit" => "1"), array($list[$_GET["index"]]->mediaid));
					if($next) {
						$next = $app->formatMedia($next);
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
				

					// Check if file is not empty
					// Check if source is Youtube
					if(!empty($media->source)){
						$file = $media->source;			
					}		
					// Check if file is not empty
					if(!empty($media->file)){
						$file = $config["url"].'/content/media/'.$media->file;		
					}			
					// Check if link is not empty
					if(!empty($media->link)){
						$file = $media->link;
					}					
					$media->embed = '<div id="video-player"></div><script type="text/javascript">
															var playerInstance = jwplayer("video-player");
															playerInstance.setup({
															file: "'.$file.'",
															width: "100%",
															'.(Main::extension($file) == ".mp3" ? 'image: "'.$media->thumb.'",' : '').'
															abouttext: "'.$media->title.'",
															aboutlink: "'.$media->url.'",		
															stretching: "fill",													
															sharing: {
																link: "'.$media->url.'",	
																code:  encodeURI("'.$media->code.'")
															}
														});
														</script>';					
			}
		}					
		// Remove ad
		//Main::add('<script type="text/javascript">$(document).ready(function(){ var count = '.$config["preroll_timer"].';var countdown = setInterval(function(){$(".ad-preroll p span").html(count);if (count < 1) {clearInterval(countdown);$(".ad-preroll").hide();videojs("video-player").ready(function(){var myPlayer = this;myPlayer.play();});}count--;}, 1000); });</script>',"custom",FALSE);							
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
		          '.(isset($next) ? '.play-screen{display:none;}' : '').'			
		          </style>
		          <script type="text/javascript" src="'.$config["url"].'/static/js/jquery.min.js"></script>
		          ';		          
						  Main::enqueue();
		  echo '</head>
		        <body>';		
		        	// Add Logo					
			        if(!empty($config["logo"])){
			        	echo "<div class='logo'><a href='{$config["url"]}' target='_blank'><img src='{$config["url"]}/content/{$config["logo"]}'></a></div>";
			        }
							if($config["ads"] && $ad = $db->get("ads", array("type" => "preroll", "enabled" => "1"), array("limit" => "1", "order" => "RAND()"))){								
								$db->update("ads", "impression = impression + 1", array("id" => $ad->id));								
					echo "<div class='play-screen'></div>";
					echo "<div class='preroll'>
								   <div class='ad-preroll'>
									   {$ad->code}
									   <p>".e("Please wait")." <span></span> ".e("seconds.")."</p>
								   </div>
								   <div id='player'>{$media->embed}</div>
								</div>";
							}else{
								echo $media->embed;
							}
		  echo '<script>
								$(document).ready(function(){
									$(".play-screen").click(function(e){
										e.preventDefault();
										$(this).hide();
										$(".ad-preroll").show();
										var count = "'.$config["preroll_timer"].'";
										var countdown = setInterval(function(){
											$(".ad-preroll p span").html(count);
											if (count < 1) {
												clearInterval(countdown);
												$(".ad-preroll").hide();	
												playerInstance.play();											
											}
											count--;
											}, 
										1000); 										
									});									
								})
		          </script></body>
		      </html>';	
		exit;
	}

?>