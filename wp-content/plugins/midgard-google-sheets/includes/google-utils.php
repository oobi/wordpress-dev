<?php

namespace FF\Midgard\Sheets;

class Midgard_Google_Utils
{
	/**
	 * constructor
	 */
	public function __construct() {
		// nothing to do - static methods
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// STATIC (public) METHODS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Delete all cached JSON credentials files
	 */
	public static function delete_cache() {
		$files = glob(MIDGARD_PLUGIN_GOOGLE_SHEETS_CACHE_DIR . '/*.json');
		self::delete_files($files);
	}

	/**
	 * save client secret JSON to cache path
	 * @param {string} json - JSON content to cache
	 * @param {string} filename - optional filename - otherwise one will be generated
	 */
	public static function set_client_secret($json, $filename=null) {
		return self::write_json($json, 'client_secret_', $filename);
	}

	/**
	 * save credentials JSON to cache path
	 * @param {string} json - JSON content to cache
	 * @param {string} filename - optional filename - otherwise one will be generated
	 */
	public static function set_credentials($json, $filename=null) {
		return self::write_json($json, 'credentials_', $filename);
	}

	/**
	 * Return full file system path to JSON
	 * @param {string} $filename = filename to return path for
	 */
	public static function get_json_path($filename) {
		return MIDGARD_PLUGIN_GOOGLE_SHEETS_CACHE_DIR . '/' . $filename;
	}

	public static function json_path_exists($filename) {
		return file_exists( self::get_json_path( $filename ));
	}

	/**
	 * Return contents of JSON file or null if it doesn't exist
	 * @param {string} $filename = filename to return content for
	 */
	public static function get_json($filename) {
		$path = self::get_json_path($filename);
		if(file_exists($path)) {
			return file_get_contents($path) ;
		} else {
			return null;
		}
	}

	public static function get_decoded_json( $filename ) {
		$json = self::get_json( $filename );
		return $json ? json_decode( $json, true) : array();
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////
	// UTILITY METHODS
	///////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * save credentials JSON to cache path
	 * @param {string} $json - contents of file
	 * @param {string} $prefix - filename prefix to which we will append a random string
	 * @param {string} filename - optional filename - otherwise one will be generated
	 */
	protected static function write_json($json, $prefix, $filename=null) {
		// make sure cache folder path exists
		self::make_dirs(MIDGARD_PLUGIN_GOOGLE_SHEETS_CACHE_DIR);

		// sanitize JSON
		$json = json_encode(json_decode($json));
		// set filename and path if not already set
		$filename = $filename ? $filename : self::create_json_filename($prefix);
		$path = MIDGARD_PLUGIN_GOOGLE_SHEETS_CACHE_DIR . '/' . $filename;
		// write the contents to disk
		file_put_contents ( $path , $json );

		// return generated filename
		return $filename;
	}

	protected static function create_json_filename( $prefix ) {
		return uniqid($prefix) . '.json';
	}

	/**
	 * Delete files in a path
	 * @param {glob} $files - output of glob(...) to delete
	 */
	protected static function delete_files($files) {
		//Loop through the file list.
		foreach($files as $file){
			//Make sure that this is a file and not a directory.
			if(is_file($file)){
				//Use the unlink function to delete the file.
				unlink($file);
			}
		}
	}

	/**
	 * Ensure folder path exists by recursively creating dirs
	 * @param {string} $path - folder path
	 */
	protected static function make_dirs($path) {
		if(! file_exists($path)) {
			mkdir($path, 0755, true);
		}
		return;		
	}

}

