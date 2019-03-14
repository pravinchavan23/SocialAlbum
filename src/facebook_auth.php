<?php
/**
 * Created by pravin chavan
 */
	require_once __DIR__ . '/lib/facebook/autoload.php';

    if(!session_id()) {
        session_start();
    }

	// facebook configuration
	$fb_obj = new Facebook\Facebook([
	    'app_id' => 'your_app_id',
	    'app_secret' => 'your_app_secret',
	    'default_graph_version' => 'v2.10',
	]);