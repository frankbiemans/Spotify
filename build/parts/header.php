<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Spotify Web API</title>
	<link rel="icon" href="images/favicon.png">
	<link rel="stylesheet" href="styles/libraries.min.css?v=2">
	<link rel="stylesheet" href="styles/site.min.css?v=2">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>

<body>

	<div class="jumbotron">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-8">
					<h1><a href="<?php echo $redirect_url; ?>">Spotify PHP Web Api</a></h1>
					<p class="lead">Our Web API lets your applications fetch data from the Spotify music catalog and manage user’s playlists and saved music.</p>
					<?php if(!isset($_GET['page'])){ ?>
					<div class="row">
						<div class="col-md-10">
							<p>Based on simple REST principles, our Web API endpoints return metadata in JSON format about artists, albums, and tracks directly from the Spotify catalogue. The API also provides access to user-related data such as playlists and music saved in a “Your Music” library, subject to user’s authorization.</p>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="col-sm-4">
					<nav>
						<ul class="nav nav-pills float-sm-right">
							<li class="nav-item">
								<a class="nav-link <?php if(!isset($_GET['page'])){ echo 'active'; } ?>" href="index.php?access-token=<?php echo $_GET['access-token']; ?>">
									Your Profile
								</a>
							</li>
						</ul>
					</nav>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="marquee-wrapper">
		<marquee behavior="alternate" scrolldelay="200">
			<?php
			$user['artists']['recently_played'] = $api->getMyRecentTracks();
			foreach($user['artists']['recently_played']->items as $track){
				echo '<span>';
				echo $track->track->artists[0]->name;
				echo ' - ';
				echo $track->track->name; 
				echo '</span>';
			}
			?>
		</marquee>
		<div class="marquee-header">Onlangs afgespeeld:</div>
	</div>