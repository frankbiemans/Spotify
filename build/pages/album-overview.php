
<?php
$api->setAccessToken($_GET['access-token']);
$album = $api->getAlbum($_GET['album-id']);
$saved_albums = savedAlbumIds($api->getMySavedAlbums());
$album_popularity = $album->popularity;

if ($album_popularity > 0 && $album_popularity < 25){
	$progress_color = 'danger';
}

if ($album_popularity > 25 && $album_popularity < 50){
	$progress_color = 'warning';
}

if ($album_popularity > 50 && $album_popularity < 75){
	$progress_color = 'info';
}

if ($album_popularity > 75 && $album_popularity < 101){
	$progress_color = 'success';
}

include('parts/header.php'); 

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-6">
			<section id="album-profile">
				<h3>
					<span class="album-title"><?php echo $album->name; ?></span> 
					<small>
						by <span class="album-artist"><?php echo artistsToString($album->artists); ?></span>
					</small>
				</h3>
				<p>
					<mark><?php echo $album->release_date; ?></mark>
				</p>
				<p>
					<small>
					<?php
						if($album->copyrights[0]->type == 'C')
							echo '&copy;&nbsp;';

						echo $album->copyrights[0]->text;
					?>
					</small>
				</p>

				<div class="progress">
					<div class="progress-bar bg-<?php echo $progress_color; ?> progress-bar-striped active progress-bar-animated" role="progressbar" style="width: <?php echo $album->popularity; ?>%">
						Popularity
					</div>
				</div>
				<hr />

			</section>
			<section id="album-actions">

				<p>
					<a class="btn btn-primary" href="<?php echo $album->external_urls->spotify; ?>" target="_blank">
						View in spotify
					</a>
					<a class="btn btn-info" href="<?php echo $album->href; ?>" target="_blank">
						View JSON
					</a>
				</p>

				<p>
					<?php if( in_array( $album->id, $saved_albums ) ) { ?>
						<a href="requests/delete-album.php?access-token=<?php echo $_GET['access-token']; ?>&album-id=<?php echo $album->id; ?>" target="_blank" class="btn btn-danger" data-ajax-request>
							Delete this from my saved albums
						</a>
						<?php } else { ?>
							<a href="requests/add-album.php?access-token=<?php echo $_GET['access-token']; ?>&album-id=<?php echo $album->id; ?>" target="_blank" class="btn btn-success" data-ajax-request>
								Add this to my saved albums
							</a>
							<?php } ?>
						</p>
						<hr />
					</section>
					<?php printAlbumTracklist( $album ); ?>
				</div>

				<div class="col-sm-6">
					<section id="album-image">
						<figure class="thumbnail pull-right">
							<img src="<?php echo $album->images[0]->url; ?>" width="<?php echo $album->images[0]->width; ?>" height="<?php echo $album->images[0]->height; ?>" />
						</figure>
					</section>
				</div>
			</div>
		</div>

		<?php include('parts/footer.php'); ?>