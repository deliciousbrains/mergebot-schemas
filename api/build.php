<?php

function build_api_for_schemas( $type = 'plugins' ) {
	$schema_dir = dirname( __DIR__ ) . '/' . $type . '/';
	$schemas    = array_diff( scandir( $schema_dir ), array( '..', '.' ) );

	$plugins_index = array();

	foreach ( $schemas as $schema_file ) {
		$file     = $schema_dir . $schema_file;
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

		$plugins_index[ $schema_file ] = array(
			'name'       => $schema->name,
			'version'    => $schema->version,
			'testedUpTo' => $schema->testedUpTo,
			'pluginURL'  => $schema->url,
			'schemaURL'  => 'https://github.com/deliciousbrains/mergebot-schemas/blob/master/' . $type . '/' . $schema_file,
		);
	}

	$index_file = dirname( __FILE__ ) . '/' . $type . '.json';
	file_put_contents( $index_file, json_encode( $plugins_index, JSON_PRETTY_PRINT ) );
}

build_api_for_schemas( 'plugins' );
build_api_for_schemas( 'themes' );