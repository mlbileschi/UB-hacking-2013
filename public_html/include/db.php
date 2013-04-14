<?php

$DB_HOST = 'hack-db.script3r.com';
$DB_USER = 'postgres';
$DB_PASSWORD = 'password1';
$DB_NAME = 'UV';

$CONN_STRING = sprintf( 'host=%s dbname=%s user=%s password=%s',
		$DB_HOST,
		$DB_NAME,
		$DB_USER,
		$DB_PASSWORD);

$conn = pg_connect( $CONN_STRING );
if( !$conn )
	die( 'bad database connection' );


