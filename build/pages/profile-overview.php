<?php
$api->setAccessToken($_GET['access-token']);

$user['profile'] = $api->me();
$user['playlists'] = $api->getMyPlaylists( ['limit' => 10 ] );
$user['albums'] = $api->getMySavedAlbums( ['limit' => 50 ] );
$user['tracks']['short_term'] = $api->getMyTop('tracks', [ 'limit' => 30, 'time_range' => 'short_term' ]);
$user['tracks']['medium_term'] = $api->getMyTop('tracks', [ 'limit' => 30, 'time_range' => 'medium_term' ]);
$user['tracks']['long_term'] = $api->getMyTop('tracks', [ 'limit' => 30, 'time_range' => 'long_term' ]);
$user['artists']['following'] = $api->getUserFollowedArtists( [ 'limit' => 50 ]);

include('./parts/header.php'); 

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-6">
			<section id="user-profile">
				<div class="row">
					<div class="col-6">
						<h3><?php echo $user['profile']->display_name; ?></h3>
						<p><?php echo $user['profile']->id; ?></p>
						<p><a href="mailto:<?php echo $user['profile']->email; ?>" target="_blank"><?php echo $user['profile']->email; ?></a></p>
						<p><strong>Followers:</strong> <?php echo $user['profile']->followers->total; ?></p>
					</div>
					<div class="col-6">
						<?php if(isset($user['profile']->images[0]->url)){ ?>
							<figure class="media-right pull-right thumbnail">
								<img src="<?php echo $user['profile']->images[0]->url; ?>" alt="Profile Picture" />
							</figure>
						<?php } ?>
					</div>
				</div>
			</section>
			<section id="top-tracks" class="mt-3">
				<h3>Top Tracks</h3>
				<div class="tabs">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active nav-item">
							<a href="#short_term" aria-controls="short_term" role="tab" data-toggle="tab" class="nav-link">Short Term<small>± 4 weeks</small></a>
						</li>
						<li role="presentation" class="nav-item">
							<a href="#medium_term" aria-controls="medium_term" role="tab" data-toggle="tab" class="nav-link">Medium Term<small>± 6 months</small></a>
						</li>
						<li role="presentation" class="nav-item">
							<a href="#long_term" aria-controls="long_term" role="tab" data-toggle="tab" class="nav-link">Long Term<small>± several years</small></a>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="short_term">
							<?php top_track_listing($user['tracks']['short_term']->items, 'Short%20Term'); ?>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="medium_term">
							<?php top_track_listing($user['tracks']['medium_term']->items, 'Medium%20Term'); ?>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="long_term">
							<?php top_track_listing($user['tracks']['long_term']->items, 'Long%20Term'); ?>
						</div>
					</div>
				</div>
			</section>
		</div>

		<div class="col-sm-6">
			<section id="user-albums">
				<h3>Saved Albums (<?php echo count($user['albums']->items); ?>)</h3>
				<ul class="items-listing">
					<?php
					foreach($user['albums']->items as $item){
						$artists = artistsToString($item->album->artists);
						$href = returnAlbumLink( $item->album->id );

						echo '
						<li>
							<a href="'. $href .'">'.
								sprintf('<strong class="album-title">%1$s</strong> by <em class="album-artist">%2$s</em>', $item->album->name, $artists)
								.'
							</a>
						</li>
						';
					}
					?>
				</ul>
			</section>
			<section id="user-playlists" class="mt-3">
				<h3>Your Playlists (<?php echo count($user['playlists']->items); ?>)</h3>

				<ul class="items-listing">
					<?php
					$first  = '';
					$second  = '';

					foreach($user['playlists']->items as $item){
						$this_user = $api->getUser($item->owner->id);
						$this_user_name = $this_user->display_name;

						if(trim($this_user_name) == '')
							$this_user_name = $item->owner->id;

						if($user['profile']->id == $item->owner->id){
							$first .= '
							<li>
								<a href="index.php?access-token='. $_GET['access-token'] .'&page=tracks-overview-filter&playlist-id='. $item->id .'&playlist-user-id='. $user['profile']->id .'">'.
									sprintf('<strong class="album-title">%1$s</strong> <small>(%2$s tracks by %3$s)</small>', $item->name, $item->tracks->total, $this_user_name)
									.'
								</a>
							</li>
							';
						} else {
							$second .= '<li><span>' . sprintf('<strong class="album-title">%1$s</strong> <small>(%2$s tracks by %3$s)</small>', $item->name, $item->tracks->total, $this_user_name) .'</span></li>';
						}
					}

					echo $first;
					echo $second;
					?>
				</ul>
			</section>
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-sm-12">
			<h3>Artists You Follow (<?php echo count($user['artists']['following']->artists->items); ?>)</h3>
		</div>
		<div class="col-sm-12">
			<section id="following-artists" class="cards-section">
				<?php foreach($user['artists']['following']->artists->items as $item){ ?>
				<div class="col-sm-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 card-col-wrapper">
					<div class="card">
						<figure>
							<?php if(isset($item->images[0]->url)){ ?>
								<img class="card-img-top rounded" src="<?php echo $item->images[0]->url; ?>" alt="<?php echo $item->name; ?>">
							<?php } ?>
						</figure>
						<div class="card-block">
							<h4 class="card-title"><?php echo $item->name; ?></h4>

							<?php if(!empty($item->followers->total)) { ?>
							<div class="row">
								<div class="col-sm-6"><strong>Followers:</strong></div>
								<div class="col-sm-6"><?php echo $item->followers->total; ?></div>
							</div>
							<?php } ?>

							<?php if(count($item->genres) > 0 ){ ?>
							<div class="gernres-wrapper">
								<?php foreach($item->genres as $genre){ ?>
								<span class="badge badge-pill"><?php echo $genre; ?></span>
								<?php } ?>
							</div>
							<?php } ?>

							<a href="<?php echo $item->external_urls->spotify; ?>" target="_blank" class="btn btn-info">Open in Spotify</a>
						</div>
					</div>
				</div>
				<?php } ?>
			</section>
		</div>
	</div>
</div>

<?php include('./parts/footer.php'); ?>