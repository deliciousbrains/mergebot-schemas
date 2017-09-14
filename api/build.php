<?php

$schema_dir = dirname( __DIR__ ) . '/plugins/';
$plugins = array_diff( scandir( $schema_dir), array( '..', '.' ) );

$plugins_index = array();

foreach( $plugins as $plugin  ) {
	$file = $schema_dir . $plugin;
	$contents = file_get_contents( $file );
	$schema   = json_decode( $contents );

	if ( ! isset( $schema->name ) ) {
		continue;
	}

	$in_git = exec( sprintf( 'git status %s', $file ) );
	if ( 'nothing added to commit but untracked files present (use "git add" to track)' === $in_git ) {
		// Schema not committed, WIP
		continue;
	}

	$plugins_index[ $plugin ] = array(
		'name'       => $schema->name,
		'version'    => $schema->version,
		'testedUpTo' => $schema->testedUpTo,
		'pluginURL'  => $schema->url,
		'schemaURL'  => 'https://github.com/deliciousbrains/mergebot-schemas/blob/master/plugins/' . $plugin,
	);
}

$index_file = dirname( __FILE__ ) . '/plugins.json';
file_put_contents( $index_file, json_encode( $plugins_index, JSON_PRETTY_PRINT ) );