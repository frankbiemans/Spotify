<?php

function returnSpotifyCodes(){
	$spotify_api = [];
	$spotify_api['baseurl'] = 'https://api.genius.com/';
	$spotify_api['clientid'] = 'f5iQmy5lLqn73riMhKVjEJPnpAvvOHMfT3V1qHOYy_WNeG5HYHawaDXQX37qt9oN';
	$spotify_api['clientsecret'] = '9KrELt6lsJj9VlR7-9BWxZ-Bn8wIz-FqWPffmf_zrCWR2z8YVlTv9ZH9LoyVIs39nAAwcr04joFbOag2Lq4kKQ';
	$spotify_api['accesstoken'] = 'ETyLQxY-I6L1-JrKzMdqBQL-WXMpVacZLHth4cldYOJyHsfAZbcjoNt85YGn6pLS';
	return $spotify_api;
}

function printSpotifyAnnotation( $annotation_id, $format = 'html' ){
	$spotify_api = returnSpotifyCodes();
	$get_url = $spotify_api['baseurl'].'annotations/'. $annotation_id .'?text_format='. $format .'&access_token='. $spotify_api['accesstoken'];
	$json = file_get_contents($get_url);
	$obj = json_decode($json);

	echo '
		<p>
			<span>
				'. nl2br( $obj->response->referent->range->content ) .'
				<dfn>
					"'. strip_tags( $obj->response->annotation->body->html ) .'"
					<span class="footer">
						<span>Annotation by <strong>'. $obj->response->annotation->authors[0]->user->name .'</strong></span> | <a href="'. $obj->response->annotation->url .'" target="_blank">View on Genius</a>
					</span>
				</dfn>
			</span>
		</p>
	';
}

function printWikipediaPageIntro( $pageslug ) {
	$get_url = 'https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='. $pageslug;
	$json = file_get_contents($get_url);
	$obj = json_decode($json);
	$pages = array_values( get_object_vars($obj->query->pages) );
	echo $pages[0]->extract;
}


function getAllMusicSnippets( $id ) {
	$get_url = 'http://www.allmusic.com/album/'. $id .'/samples.json';
	$json = file_get_contents($get_url);
	$obj = json_decode($json);
	return $obj;
}

function artistsToString($artists){

	$return = '';
	$i = 0;

	foreach($artists as $artist){
		$i++;

		$return .= $artist->name;

		if($i < count($artists) && $i == 1){
			$return .= ' ft. ';
		} elseif($i > 1 && $i != count($artists)){
			$return .= ' & ';
		}

	}

	return $return;

}

function millisecondsToMinutes($input){
	$uSec = $input % 1000;
	$input = floor($input / 1000);

	$seconds = $input % 60;
	$input = floor($input / 60);

	$minutes = $input % 60;
	$input = floor($input / 60); 

	$hours = $input % 60;
	$input = floor($input / 60); 

	if($hours > 0){
		return $hours .':'. $minutes .':'. sprintf('%02d', $seconds);
	} else {
		return $minutes .':'. sprintf('%02d', $seconds);
	}
}

function savedAlbumIds($saved_albums){
	$return = [];

	foreach($saved_albums->items as $item){
		array_push($return, $item->album->id);
	}

	return $return;
}

function printAlbumTracklist( $album, $title = 'Tracklist' ){
	?>
	<section id="album-tracklist">
		<h3><?php echo $title; ?></h3>
		<ul class="list-group">
			<?php
			$total_time = 0;
			foreach($album->tracks->items as $track){
				echo '<li class="list-group-item">&nbsp;';
				echo '['. $track->track_number .']&nbsp;';
				echo '<strong>'. $track->name .'</strong>&nbsp;';
				echo '<span class="duration badge badge-default"> '. millisecondsToMinutes( $track->duration_ms ) .'</span>';
				echo '</li>';

				$total_time = $total_time + $track->duration_ms;
			}
			?>
			<li class="list-group-item text-right"><small>
				Full length: <strong><?php echo millisecondsToMinutes( $total_time ); ?></strong>
			</small></li>
		</ul>
	</section>
	<?php
}

function returnAlbumLink($album_id){
	$link = 'index.php?access-token='. $_GET['access-token'] .'&page=album-overview&album-id='. $album_id .'';
	return $link;
}

function printItemTopTrack($item, $classes){
	$artists = artistsToString($item->artists);
	$format = '
		<li class="'. $classes .'">
			<div class="row">
				<div class="col-6">%1$s <span class="seperator">-</span></div>
				<div class="col-6">%2$s</div>

				<div class="album-title">
					<div class="album-title__inner">
					%3$s
					</div>
				</div>
			</div>
		</li>
	';
	echo sprintf($format, $artists, $item->name, $item->album->name);
}

function top_track_listing( $tracks, $title ){
	?>
	<ul class="items-listing">
		<?php
		$albums = [];
		$trackstoadd = '';
		foreach($tracks as $track){
			$trackstoadd .= '|'. $track->id;
			$album_id = $track->album->id;

			if(in_array($album_id, $albums)){
				$key = array_search($album_id, $albums);
			} else {
				$albums[] = $track->album->id;
				end($albums);
				$key = key($albums);
			}

			//echo $key;

			printItemTopTrack($track, 'album album-'. $key);
		}
		?>
	</ul>
	<div class="c2a-holder mt-2">
		<div class="message-holder"></div>
		<a href="requests/create-term-playlist.php?access-token=<?php echo $_GET['access-token']; ?>&playlisttitle=<?php echo $title; ?>&trackstoadd=<?php echo $trackstoadd; ?>" target="_blank" class="btn btn-primary" data-ajax-request>
			Add this to my profile as playlist
		</a>
	</div>
	<?php
}