<?php

	error_reporting(-1);
	ini_set('display_errors', 1);

	include('../includes/Request.php');
	include('../includes/Session.php');
	include('../includes/SpotifyWebAPI.php');
	include('../includes/SpotifyWebAPIException.php');

	$date = date ( 'd-m-Y' );

	$api = new SpotifyWebAPI\SpotifyWebAPI();
	$api->setAccessToken($_GET['access-token']);

	$user['profile'] = $api->me();

	$playlisttitle = 'Term';
	if( isset($_GET['playlisttitle']) ){
		$playlisttitle = $_GET['playlisttitle'] .' ('. $date .')';
	}

	if( isset($_GET['trackstoadd']) ){
		$trackstoadd = $_GET['trackstoadd'];
		$trackstoadd_arr = array_values(array_filter(explode('|', $trackstoadd)));
	}

	$username = $user['profile']->id;

	$new_playlist = $api->createUserPlaylist($username, [
	    'name' => $playlisttitle,
	]);

	$api->addUserPlaylistTracks(
		$username, $new_playlist->id, $trackstoadd_arr
	);

	$return = '<p>';
	$return .= 'Your playlist has been added';
	if(isset($username))
		$return .= ', '. $username;
	$return .= '.';
	$return .= 'You can find in your Spotify App or <a href="'. $new_playlist->external_urls->spotify .'" target="_blank" class="fancy-link">via this link</a>. Enjoy buddy!';
	$return .= '</p>';

	echo $return;