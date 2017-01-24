<?php

$schema_dir = dirname( __DIR__ ) . '/plugins/';
$plugins = array_diff( scandir( $schema_dir), array( '..', '.' ) );

$plugins_index = array();

foreach( $plugins as $plugin  ) {
	$contents = file_get_contents( $schema_dir . $plugin );
	$schema   = json_decode( $contents );

	if ( ! isset( $schema->name ) ) {
		continue;
	}

	$plugins_index[ $plugin ] = array(
		'name'      => $schema->name,
		'version'   => $schema->version,
		'pluginURL' => $schema->url,
		'schemaURL' => 'https://github.com/deliciousbrains/mergebot-schemas/blob/master/plugins/' . $plugin,
	);
}

$index_file = dirname( __FILE__ ) . '/plugins.json';
file_put_contents( $index_file, json_encode( $plugins_index, JSON_PRETTY_PRINT ) );