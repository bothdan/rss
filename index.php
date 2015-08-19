<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RSS feed</title>
<style>
	body {
		background: #FEFEFE;
		font-family: arial;
		font-size: 12px;
		color: #333333;
		text-align: center;
	}
	.container {
		width: 940px;
		background: #FFF;
		border: 1px solid #cccccc;
		border-radius: 4px;
		text-align: left;
		padding: 0 10px;
		margin: auto;
	}
	.content {
		text-align: center;
	}
	.ul_json {
		margin: 0px ;
		padding: 0px;
	}
	.ul_json li {
		margin: 10px 0px ;
		padding: 0px 5px;
		list-style: none;
		text-align: left;
		background: #F2F2F2;
		border: 1px solid #cccccc;
	}
	.ul_json li a {
		text-decoration: none;
		color: #000;
	}
	.ul_json li a:hover {
		color: #666666;
	}
	.ul_json li span {
		font-size: 18px;
	}
</style>
</head>

<body>
<p>
	<form name="form" method="post" action="">
		<select name="select">
			  <option value="">-- select RSS</option>
			  <option value="http://rss.cnn.com/rss/cnn_world.rss">CNN</option>
			  <option value="http://feeds.reuters.com/Reuters/worldNews">Reuters</option>
			  <option value="posts.json">Local File</option>
		</select>
		<input type="submit" value="Select">
	<form>
</p>

<?php
$RssClass = new RssClass;
$rssURL = $_POST['select'];

if($rssURL){
	$jfo = $RssClass -> RSS($rssURL);

		// read the title value
		$title = $jfo->channel->title;
		$link = $jfo->channel->link;
		$description = $jfo->channel->description;
		$language = $jfo->channel->language;
		$pubDate = $RssClass -> DateFormat($jfo->channel->pubDate);
		$image = $jfo->channel->image->url;
		// copy the posts array to a php var
		$items = $jfo->channel->item;
?>
	<div class="container">
		<p><img src="<?php echo $image; ?> " width = "100px"></p>
		<p>Language: <?php echo $language; ?></p>
		<p>Date: <?php echo $pubDate; ?></p>
		<p>Title: <?php echo $title; ?></p>
			
		<div class="content">
			<ul class="ul_json">
				<?php
				foreach ($items as $item) {
					$title = $item->title;
					$description =  $RssClass ->trimAds($item->description);
					$excerpt = $RssClass ->trimDescription($description);
					$date = $RssClass -> DateFormat($item->pubDate);
					$link = $item->link;
				?>
					<li>
						<h3><a href="<?php echo  $link; ?>" target="_blank"><?php echo $title; ?></a></h3>
						<p><?php echo $excerpt; ?></p>
						<p><?php if($description){echo $description;}else{echo 'n/a';} ?></p>
						<p><?php echo $date; ?></p>
						<p><a href="<?php echo  $link;?>" target="_blank">[ open link ]</a></p>
					</li>
				<?php
				} // end foreach
				?>
			</ul>
		</div><!-- content -->

	</div><!-- container -->
<?php 
} //end if 

class RssClass{

	function DateFormat($TheDate){	//convert date to difrent format
		$NewDate=date("F d, Y", strtotime($TheDate));  
		return $NewDate;
	}
	
	function trimAds($str){	// replace ' with ASCII code &#39 	
		$str = str_replace("'", '&#39', $str);		
		$str_array = explode("clear=", $str);
		return $str_array[0];
	}
	
	function trimDescription($str){	//trim the description and add 3 dots
		$str_array = str_split($str, 30);
		return $str_array[0].'...';
	}
	
	function RSS($rssURL){	//get the JSON file - if $rssURL start with http will go online if $rssURL  start with post then load the local server file
		if(substr($rssURL, 0, 4) == 'http'){
			$feed = implode(file($rssURL));
			$xml = simplexml_load_string($feed);
			$json = json_encode($xml);
			// convert the string to a json object
			$jfo = json_decode($json);
		}
		if(substr($rssURL, 0, 4) == 'post'){
			$json_file = file_get_contents('posts.json');
			// convert the string to a json object
			$jfo = json_decode($json_file);
		}
		return $jfo;
	}
}
?>
</body>
</html>