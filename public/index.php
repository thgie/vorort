<?php

	require '../lib/Slim/Slim.php';
	require '../lib/Slim-Extras/Views/TwigView.php';

	require_once '../lib/Paris/idiorm.php';
	require_once '../lib/Paris/paris.php';

	require '../lib/BarcodeQR/BarcodeQR.php';

	/**
	* Configuration
	*/

	$baseurl = "http://vorort.pagodabox.com/ort/";

	TwigView::$twigDirectory = '../lib/Twig';
	TwigView::$twigOptions = array(
		'debug' => false,
		'charset' => 'utf-8',
		'cache' => realpath('../templates/cache'),
		'auto_reload' => true,
		'strict_variables' => false,
		'autoescape' => true
	);

	ORM::configure('sqlite:../db/vorort.sqlite');

	/*ORM::configure('mysql:host=localhost;dbname=vorort');
	ORM::configure('username', 'vorort');
	ORM::configure('password', 'helloworld');*/

	/*ORM::configure('mysql:host=tunnel.pagodabox.cor;dbname=vorort');
	ORM::configure('username', 'nu');
	orm::configure('password', 'Kefh8zdM');*/


	/**
	 * Models
	 */
	class Ort extends Model { }
	class Post extends Model { }

	/**
	* Application
	*/
	$app = new Slim(array(
		'debug' => true,
		'log.enabled' => true,
		'log.level' => 4,
		'templates.path' => '../templates',
		'view' => new TwigView()
	));

	$app->get( '/', function() use ( $app ) {
		$orte = Model::factory( 'Ort' )->find_many();
		$app->render( 'main.html', array( 'orte' => $orte ) );
	});


	$app->get( '/info', function() use ( $app ) {
		phpinfo();
	});

	$app->get( '/ort/:uid', function( $uid ) use ( $app ) {
		$ort = Model::factory( 'Ort' )->where( 'uid', $uid )->find_one();
		$posts = Model::factory( 'Post' )->where( 'ort', $uid )->find_many();
		$app->render( 'ort.html', array( 'ort' => $ort, 'posts' => $posts ) );
	});

	$app->post( '/ort', function() use ( $app, $baseurl ) {
		$ort = Model::factory( 'Ort' )->create();

		$ort->uid = uniqid();
		$ort->url = '/ort/' . $ort->uid;
		$ort->keyword = $app->request()->post( 'keyword' );

		$ort->save();

		$uqr = new BarcodeQR();
		$uqr->url( $baseurl . $ort->uid ); 
		$uqr->draw( 300 , 'tmp/codes/' . $ort->uid . '.png' );

		$app->render( 'code.html', array( 'ort' => $ort ) );
	});

	$app->post( '/post/:uid', function( $uid ) use ( $app ) {
		$post = Model::factory( 'post' )->create();

		$post->ort = $uid;
		$post->message = $app->request()->post( 'message' );

		$post->save();

		$app->redirect( '/ort/' . $uid );
	});


	$app->run();
?>
