<?php
$api->setAccessToken($_GET['access-token']);
$user['profile'] = $api->me();

$playlist = $api->getUserPlaylist($_GET['playlist-user-id'], $_GET['playlist-id']);
$playlist_user = $api->getUser($_GET['playlist-user-id']);

$playlist_tracks_collection = array();
$playlist_tracks_limit = 60;
$playlists_to_load = ceil( $playlist->tracks->total / $playlist_tracks_limit );

$playlist_tracks = $api->getUserPlaylistTracks(
	$_GET['playlist-user-id'], 
	$_GET['playlist-id'],
	[ 'limit' => $playlist_tracks_limit ]
);

foreach($playlist_tracks->items as $item){
	array_push( $playlist_tracks_collection, $item->track);
}

$i = 1;
while($i < $playlists_to_load){
	$playlist_tracks = $api->getUserPlaylistTracks(
		$_GET['playlist-user-id'], 
		$_GET['playlist-id'],
		[ 'limit' => $playlist_tracks_limit, 'offset' => ( $i * $playlist_tracks_limit ) ]
	);
	foreach($playlist_tracks->items as $item){
		array_push( $playlist_tracks_collection, $item->track);
	}
	$i++;
}

include('./parts/header.php'); 

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-6">
			<section id="playlist-profile">
				<p>
					<mark><?php echo $playlist->followers->total ?> followers</mark>
					<mark><?php echo $playlist->tracks->total ?> tracks</mark>
				</p>
				<h3>
					<span class="album-title"><?php echo $playlist->name; ?></span> 
					<small>
						by <span class="album-artist"><?php echo $playlist_user->display_name; ?></span>
					</small>
				</h3>

				<?php if(!empty($playlist->description)){ ?>
					<p class="alert alert-info">
						<?php echo $playlist->description; ?>
					</p>
				<?php } ?>
				<hr />
			</section>
			<section id="playlist-actions">
				<p>
					<a class="btn btn-primary" href="<?php echo $playlist->external_urls->spotify; ?>" target="_blank">
						View in Spotify
					</a>
				</p>
				<hr />
			</section>

			<section id="playlist-tracklist">
				<h3>Tracklist</h3>
				<ul class="list-group">
					<?php
					$total_time = 0;
					$c = 0;
					foreach($playlist_tracks_collection as $track){
						$c++;
						echo '<li class="list-group-item">';
						echo '['. $c .']&nbsp;';
						echo '<strong>'. $track->name .'</strong>&nbsp;';
						echo '<span class="artist-name">by&nbsp;<em>'. artistsToString( $track->artists ) .'</em></span>&nbsp;';
						echo '<span class="duration badge badge-default">'. millisecondsToMinutes( $track->duration_ms ) .'</span>';
						echo '</li>';

						$total_time = $total_time + $track->duration_ms;
					}

					?>
					<li class="list-group-item disabled text-right"><small>
						Full length: <strong><?php echo millisecondsToMinutes( $total_time ); ?></strong>
					</small></li>
				</ul>
			</section>
		</div>

		<div class="col-sm-6">
			<?php if(!empty($playlist->images)) { ?>
				<section id="album-image">
					<figure class="thumbnail pull-right <?php if(count($playlist->images) == 1){ echo 'uploaded-image'; } ?>">
						<img src="<?php echo $playlist->images[0]->url; ?>" width="<?php echo $playlist->images[0]->width; ?>" height="<?php echo $playlist->images[0]->height; ?>" />
					</figure>
				</section>
			<?php } ?>
		</div>
	</div>
</div>

<?php include('./parts/footer.php'); ?>
