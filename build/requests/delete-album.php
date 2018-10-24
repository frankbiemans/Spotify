<?php

	error_reporting(-1);
	ini_set('display_errors', 1);

	include('../../includes/Request.php');
	include('../../includes/Session.php');
	include('../../includes/SpotifyWebAPI.php');
	include('../../includes/SpotifyWebAPIException.php');

	$api = new SpotifyWebAPI\SpotifyWebAPI();
	$api->setAccessToken($_GET['access-token']);

	$api->deleteMyAlbums([
	    $_GET['album-id']
	]);
