<?php

if ( ! function_exists( 'build_api_for_schemas' ) ) {
	function build_api_for_schemas( $type = 'plugins' ) {
		$schema_dir = dirname( __DIR__ ) . '/' . $type . '/';
		$schemas    = array_diff( scandir( $schema_dir ), array( '..', '.' ) );

		$plugins_index = array();

		foreach ( $schemas as $schema_file ) {
			$file     = $schema_dir . $schema_file;
			$contents = file_get_contents( $file );
			$schema   = json_decode( $contents );

			if ( is_null( $schema ) ) {
				continue;
			}

			$is_core = 'core' === $type;

			if ( ! isset( $schema->name ) && ! $is_core ) {
				continue;
			}

			$in_git = exec( sprintf( 'git status %s', $file ) );
			if ( 'nothing added to commit but untracked files present (use "git add" to track)' === $in_git ) {
				// Schema not committed, WIP
				continue;
			}

			$coreVersion = '';
			if ( $is_core ) {
				$filename    = str_replace( '.json', '', $schema_file );
				$filename    = explode( '-', $filename );
				$coreVersion = array_pop( $filename );
			}

			$name       = $is_core ? 'WordPress' : $schema->name;
			$version    = $is_core ? $coreVersion : $schema->version;
			$testedUpTo = $is_core ? $coreVersion : $schema->testedUpTo;
			$url        = $is_core ? 'https://wordpress.org/download/release-archive/' : $schema->url;
			$urlKey     = rtrim( $type, 's' ) . 'URL';

			$plugins_index[ $schema_file ] = array(
				'name'       => $name,
				'version'    => $version,
				'testedUpTo' => $testedUpTo,
				$urlKey      => $url,
				'schemaURL'  => 'https://github.com/deliciousbrains/mergebot-schemas/blob/master/' . $type . '/' . $schema_file,
			);
		}

		$index_file = dirname( __FILE__ ) . '/' . $type . '.json';
		file_put_contents( $index_file, json_encode( $plugins_index, JSON_PRETTY_PRINT ) );
	}
}

build_api_for_schemas( 'plugins' );
build_api_for_schemas( 'themes' );
build_api_for_schemas( 'core' );