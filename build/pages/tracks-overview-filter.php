<?php
$api->setAccessToken($_GET['access-token']);
$user['profile'] = $api->me();

$playlist = $api->getUserPlaylist($_GET['playlist-user-id'], $_GET['playlist-id']);
$playlist_user = $api->getUser($_GET['playlist-user-id']);

$playlist_tracks_collection = [];
$playlist_tracks_limit = 60;
$max_audio_features = 50;
$all_track_ids = [];
$tracks_info_arr = [];
$artists_info_arr = [];
$playlists_to_load = ceil( $playlist->tracks->total / $playlist_tracks_limit );
$artist_ids = [];
$artists_info = [];
$tracks_info = [];
$all_genres_unfiltered = [];
$genres_filtered = [];
$genres_filtered['total'] = 0;

$playlist_tracks = $api->getUserPlaylistTracks(
	$_GET['playlist-user-id'], 
	$_GET['playlist-id'],
	[ 'limit' => $playlist_tracks_limit ]
);

foreach($playlist_tracks->items as $item){
	array_push( $artist_ids, $item->track->album->artists[0]->id);
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

$i = 1;
foreach($playlist_tracks_collection as $track){
	$all_track_ids[] = $track->id;
}

while($i <= ceil(count($all_track_ids)/$max_audio_features)){
	$start = ($max_audio_features * ($i-1));
	$tracks_info_arr[] = $api->getAudioFeatures( array_slice($all_track_ids, $start, $max_audio_features) );
	$i++;
}

foreach($tracks_info_arr as $arr){
	foreach($arr->audio_features as $feat){
		$tracks_info[] = $feat;
	}
}

$i= 1;
while($i <= ceil(count($artist_ids)/$max_audio_features)){
	$start = ($max_audio_features * ($i-1));
	$artists_info[] = $api->getArtists( array_slice($artist_ids, $start, $max_audio_features) );
	$i++;
}

foreach($artists_info as $arr){
	foreach($arr->artists as $artist){
		foreach($artist->genres as $genre){
			array_push($all_genres_unfiltered, $genre);
		}
	}
}

function groupGenres($genre){
	switch ($genre) {
		case stripos($genre, 'soul') !== false:
		case stripos($genre, 'motown') !== false:
		case stripos($genre, 'quiet storm') !== false:
		$genre = 'soul';
		break;
		case stripos($genre, 'jazz') !== false:
		case stripos($genre, 'free improvisation') !== false:
		$genre = 'jazz';
		break;
		case stripos($genre, 'uk drill') !== false:
		case stripos($genre, 'grime') !== false:
		$genre = 'grime / uk drill';
		break;
		case stripos($genre, 'afropop') !== false:
		case stripos($genre, 'afrobeat') !== false:
		case stripos($genre, 'Afrobeats / Azonto') !== false:
		case stripos($genre, 'afrobeats') !== false:
		case stripos($genre, 'azonto') !== false:
		case stripos($genre, 'highlife') !== false:
		$genre = 'afrobeats';
		break;
		case stripos($genre, 'rap') !== false:
		case stripos($genre, 'hip hop') !== false:
		$genre = 'hiphop / rap';
		break;
		case stripos($genre, 'Alternative Metal') !== false:
		case stripos($genre, 'Nu Metal') !== false:
		case stripos($genre, 'metal') !== false:
		$genre = 'metal';
		case stripos($genre, 'Big Beat') !== false:
		case stripos($genre, 'abstract beats') !== false:
		case stripos($genre, 'freakbeat') !== false:
		$genre = 'breakbeat';
		break;
		case stripos($genre, 'r&b') !== false:
		case stripos($genre, 'r&b') !== false:
		$genre = 'r&b';
		break;
		case stripos($genre, 'trap') !== false:
		$genre = 'trap';
		break;
		case stripos($genre, 'songwriter') !== false:
		$genre = 'singer-songwrite';
		break;
		case stripos($genre, 'rock') !== false:
		case stripos($genre, 'grunge') !== false:
		$genre = 'rock';
		break;
		case stripos($genre, 'folk') !== false:
		$genre = 'folk';
		break;
		case stripos($genre, 'indie') !== false:
		$genre = 'indie';
		break;
		case stripos($genre, 'psychedelic') !== false:
		case stripos($genre, ' psych') !== false:
		$genre = 'psychedelic';
		break;
		case stripos($genre, 'dance') !== false:
		$genre = 'dance';
		break;
		case stripos($genre, 'punk') !== false:
		$genre = 'punk';
		break;
		case stripos($genre, 'pop') !== false:
		$genre = 'pop';
		break;
		case stripos($genre, 'house') !== false:
		$genre = 'house';
		break;
		case stripos($genre, 'funk') !== false:
		$genre = 'funk';
		break;
		case stripos($genre, 'techno') !== false:
		$genre = 'techno';
		break;
		case stripos($genre, 'reggae') !== false:
		case stripos($genre, 'ska') !== false:
		$genre = 'reggae';
		break;
		case stripos($genre, 'uk') !== false:
		//case stripos($genre, 'garage') !== false:
		case stripos($genre, 'british') !== false:
		case stripos($genre, 'canterbury') !== false:
		case stripos($genre, 'mersey') !== false:
		case stripos($genre, 'madchester') !== false:
		$genre = 'UK';
		break;
		case stripos($genre, 'rave') !== false:
		$genre = 'rave';
		break;
		case stripos($genre, 'disco') !== false:
		$genre = 'disco';
		break;
		case stripos($genre, 'EDM') !== false:
		$genre = 'electronic';
		break;
		case stripos($genre, 'ambient') !== false:
		$genre = 'ambient';
		break;
		case stripos($genre, 'fourth world') !== false:
		case stripos($genre, 'world fusion') !== false:
		case stripos($genre, 'escape room') !== false:
		case stripos($genre, 'urban contemporary') !== false:
		case stripos($genre, 'carioca') !== false:
		case stripos($genre, 'wave') !== false:
		case stripos($genre, 'lo-fi') !== false:
		case stripos($genre, 'stomp and holler') !== false:
		case stripos($genre, 'new weird america') !== false:
		case stripos($genre, 'mellow gold') !== false:
		case stripos($genre, 'bass music') !== false:
		case stripos($genre, 'chillhop') !== false:
		case stripos($genre, 'downtempo') !== false:
		case stripos($genre, 'french soundtrack') !== false:
		case stripos($genre, 'glitch') !== false:
		case stripos($genre, 'laboratorio') !== false:
		case stripos($genre, 'mashup') !== false:
		case stripos($genre, 'french electronic') !== false:
		case stripos($genre, 'wonky') !== false:
		case stripos($genre, 'adult standard') !== false:
		case stripos($genre, 'new romantic') !== false:
		case stripos($genre, 'nu gaze') !== false:
		case stripos($genre, 'preverb') !== false:
		$genre = 'world';
		break;
	}

	return ucwords($genre);
}

sort( $all_genres_unfiltered );
foreach($all_genres_unfiltered as $k => $genre){
	$genres_filtered['total']++;

	$genre = groupGenres($genre);

	if(array_key_exists($genre, $genres_filtered)){
		$genres_filtered[$genre]++;
	} else {
		$genres_filtered[$genre] = 1;
	}
}

$numItems = count($genres_filtered);
$labels = '[';
$data = '[';
foreach($genres_filtered as $genre => $amount){
	if($genre != 'total'){
		$data .= $amount .', ';
		$labels .= "'". $genre ."', ";
	}
}
$data .= ']';
$data = str_replace(', ]', ']', $data);
$labels .= ']';
$labels = str_replace(', ]', ']', $labels);

?>

<?php include('./parts/header.php'); ?>

<div class="container-fluid">
	<div class="row align-items-center">
		<div class="col-sm-6 col-lg-3">
			<?php if(!empty($playlist->images)) { ?>
				<section id="album-image">
					<figure class="thumbnail <?php if(count($playlist->images) == 1){ echo 'uploaded-image'; } ?>">
						<img src="<?php echo $playlist->images[0]->url; ?>" width="<?php echo $playlist->images[0]->width; ?>" height="<?php echo $playlist->images[0]->height; ?>" />
					</figure>
				</section>
			<?php } ?>
		</div>
		<div class="col-sm-6 col-lg">
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
			</section>
		</div>
		<div class="col-12 col-md-6 col-lg-5">
			<canvas id="myChart" width="400" height="400"></canvas>
		</div>
	</div>
</div>

<section class="tracks-overview-filter">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<table class="overview-table">
					<thead>
						<tr class="py-3 row--header">
							<th>&nbsp;</th>
							<th>Track name</th>
							<th>Artist name</th>
							<th class="d-none d-md-table-cell">Album</th>
							<th class="d-none d-lg-table-cell">Year</th>
							<th class="d-none d-md-table-cell">Length</th>
							<th>BMP</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1; 
						$total_tempo = 0;
						$total_tempo_tracks = 0;
						$total_time = 0;
						foreach($playlist_tracks_collection as $track){
							$total_time = $total_time + $track->duration_ms;
							if(isset($tracks_info[$i]->tempo)){
								$tempo = round($tracks_info[$i]->tempo);
								$total_tempo = $total_tempo + $tracks_info[$i]->tempo;
								$total_tempo_tracks++;
							} else {
								$tempo = 0;
							}
							?>
							<tr class="row--tracks-overview row--<?= $i ;?>" id="<?= $track->id; ?>">
								<td><input type="checkbox" /><span class="d-none track-id"><?= $track->id; ?></span></td>
								<td class="track-name"><?= $track->name; ?></td>
								<td><?= $track->artists[0]->name; ?></td>
								<td class="d-none d-md-table-cell"><?= $track->album->name; ?></td>
								<td class="d-none d-lg-table-cell"><?= substr($track->album->release_date, 0, 4); ?></td>
								<td class="d-none d-md-table-cell"><?= millisecondsToMinutes($track->duration_ms); ?></td>
								<td><?= $tempo; ?></td>
							</tr>
							<?php
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="py-3">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class="text-right"><strong class="d-md-none">Average:</strong></td>
							<td class="d-none d-md-table-cell text-right"><strong class="d-lg-none">Average:</strong></td>
							<td class="d-none d-lg-table-cell text-right"><strong>Average:</strong></td>
							<td class="d-none d-md-table-cell"><?= millisecondsToMinutes($total_time/$i); ?></td>
							<td><?= round($total_tempo/$total_tempo_tracks); ?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</section>

<?php include('./parts/footer.php'); ?>

<script src="//www.chartjs.org/docs/latest/gitbook/gitbook-plugin-chartjs/Chart.bundle.js"></script>
<script>
	function shuffle(a) {
		for (let i = a.length - 1; i > 0; i--) {
			const j = Math.floor(Math.random() * (i + 1));
			[a[i], a[j]] = [a[j], a[i]];
		}
		return a;
	}

	function getRandomColor() {
		var letters = '0123456789ABCDEF';
		var color = '#';
		for (var i = 0; i < 6; i++ ) {
			color += letters[Math.floor(Math.random() * 16)];
		}
		return color;
	}

	$( document ).ready(function() {
		if($('#myChart').length){
			var ctx = document.getElementById("myChart").getContext('2d');
			data = {
				datasets: [{
					data: <?= $data; ?>,
					backgroundColor: [getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(),getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(),getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor()],
					borderWidth: 0
				}],
				labels: <?= $labels; ?>
			};

			options = {
				responsive: true,
				legend: {
					position: 'top',
					labels: {
						fontColor: '#f8f8f8'
					}
				}
			};

			var myDoughnutChart = new Chart(ctx, {
				type: 'doughnut',
				data: data,
				options: options
			});
		}
	});
</script>
