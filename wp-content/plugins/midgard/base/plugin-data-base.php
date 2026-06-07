<?php

namespace FF\Midgard;

use phpFastCache\CacheManager;
use JsonPath\JsonObject;

/**
 * The class to retrieve and handle remote data
 *
 * This class is intended to be extended by sub-plugins
 *
 * @package    midgard
 * @subpackage midgard_calendar/admin
 * @author     Firefly Interactive
 */
class Midgard_Plugin_Data_Base {

	// last error message
	protected $last_error;

	/**
	 * Constructor - set some convenience values
	 */
	public function __construct() {

	}


	/**
	 * Public API - get feed data by ID
	 * @param {numeric} $feed_id - ID of the feed record
	 * @param {boolean} $nomap - flag true to skip mapping and display original encoded data
	 * @return {string} - JSON encoded output
	 */
	public function get_data($feed_id, $nomap=false) {
		// is caching on?
		$cache_active = Midgard::is_cache_active();

		// if caching is on, try retrieving from cache
		// don't cache things with nomap set to true
		if($cache_active && !$nomap ) {
			// get an instance to cache object
			$cache = Midgard::get_cache_instance();

			// set cache key
			$key = get_permalink( $feed_id );

			// try to get cached item
			$cache_item = $cache->getItem($key);
			$output = $cache_item->get();

			// if object not in cache, or what's in cache isn't valid then retrieve it again
			if (is_null( $output ) || ! $this->is_valid_data($output) ) {

				// get data from live source
				$output = $this->_get_data($feed_id);

				// determine if data is valid
				$valid	 	=  $this->is_valid_data($output);

				// get the requesteed cache timeout
				$timeout 	= get_post_meta( $feed_id, 'midgard_cache_time', true );

				// Only cache valid data
				// This will cause the system to keep trying to retrieve good data on subsequent requests
				// TODO: Maybe limit retries...
				if( $valid ) {
					// write to fallback cache
					Midgard_Common::save_meta_values($feed_id, 'midgard_cache_data', $output );
				}

				// FALLBACK
				// retrieve last good data from DB and return that if available
				else {
					$output = get_post_meta( $feed_id, 'midgard_cache_data', true );
					$timeout = 30; // set a short cache timeout just to ensure the target server doesn't get hammered by bad requests
								   // we want to check back for latest data as soon as possible though

					// recheck for validity
					$valid	 	=  $this->is_valid_data($output);

					if( ! $valid ) {
						$output = $this->create_return_structure( $feed_id, array('error' => 'Unable to retrieve data and no fallback exists') );
					}
				}

				// if still valid, cache the formatted data
				if( $valid ) {
					$tag 		= get_site_url(); // add a tag for grouping and identification

					$cache_item->set( $output )
								->expiresAfter( intval( $timeout ) ) //in seconds, also accepts Datetime
								->addTag( $tag );

					$cache->save($cache_item); // Save the cache item just like you do with doctrine and entities
				}
			}
		}
		// otherwise cache is inactive - grab directly from source
		else {
			$output = $this->_get_data($feed_id, $nomap);
		}

		return $output;
	}


	/**
	 * Retrieve data from source (not cache)
	 * @param {numeric} $feed_id - ID of the feed record
	 * @param {boolean} $nomap - flag true to skip mapping and display original encoded data
	 * @return {string} - JSON encoded output
	 */
	protected function _get_data($feed_id, $nomap=false) {
		// grab data and format it
		$data = $this->get_feed_data($feed_id);

		// if we have an error response then DO NOT MAP
		if( isset($data['error']) && $data['error'] ) {
			$nomap = true;
		}

		// apply mappings if required
		if(!$nomap) {
			$data = $this->apply_mappings($feed_id, $data);
		}

		$output = $this->create_return_structure($feed_id, $data);

		//$output = json_encode($output, JSON_PRETTY_PRINT );
		return $output;
	}


	/**
	 * Remap the supplied data according to defined feed mapping
	 * @param int $feed_id
	 * @param {object} $data - the source data to map
	 * @return String - JSON encoded output
	 */
	protected function apply_mappings( $feed_id, $data ) {
		// get mapping mode
		$mode 	   = get_post_meta( $feed_id, 'midgard_mapping_mode', true );

		// simple mode - JSONPath key/value pairs
		if( $mode == 'simple') {
			return $this->_apply_mappings_simple( $feed_id, $data);
		}
		// advanced mode - twig template
		else if( $mode == 'advanced') {
			return $this->_apply_mappings_advanced( $feed_id, $data);
		}
		// no mapping
		else {
			return $data;
		}

	}


	/**
	 * Apply mapping using simple mode - JSONPath key/value pairs
	 * @param int $feed_id
	 * @param {object} $data - the source data to map
	 * @return String - JSON encoded output
	 */
	protected function _apply_mappings_simple( $feed_id, $data ) {
		$map = get_post_meta( $feed_id, 'midgard_feed_mappings', true );
		$map = json_decode( str_replace( "\'", "'" ,  $map), true );

		if( !$map || !is_array($map) || count($map) == 0 ) {
			return $data;
		}

		// find corresponding keys in source data and remap
		$mapped = array();

		// array type
		if( is_array( $data ) ) {
			// test to see if data is sequential array or associative
			if( $this->is_sequential_array($data) ) {
				foreach( $data as $item ) {
					$mapped[] = $this->_map($item, $map);
				}
			}
			// single level assoc array
			else {
				$mapped = $this->_map($data, $map);
			}
		}
		// otherwise just pass it back unmapped
		else {
			$mapped = $data;
		}

		return $mapped;
	}

	/**
	 * Apply mapping using simple mode - JSONPath key/value pairs
	 * @param int $feed_id
	 * @param {object} $data - the source data to map
	 * @return String - remapped output
	 */
	protected function _apply_mappings_advanced( $feed_id, $data ) {
		$template = get_post_meta( $feed_id, 'midgard_mapping_twig', true );

		// go no further if twig template is empty
		if( empty( trim($template) ) ) {
			return $data;
		}

		$template_key = 'template_' . $feed_id;

		// setup twig
		$loader = new \Twig_Loader_Array(array(
			$template_key  => $template
		));

		// TODO: work out how to clear cache (maybe not necessar for our application)
		$twig = new \Twig_Environment($loader, array(
			'autoescape'	=> false		// don't change my data please (default auto-escapes quotes etc as entities)
			//'cache' => MIDGARD_CACHE_DIR . '/twig'
		));

		// TWIG FUNCTION
		// add a twig wrapper for the "find_node" method (jsonpath)
		$function = new \Twig_SimpleFunction('jsonpath', function ($item, $path, $single=true, $skip_encode=false) {
			$result = $this->find_node( $item, $path, $single);

			if( is_string($result) || $skip_encode) {
				return $result;
			} else {
				return json_encode( $result );
			}
		});
		$twig->addFunction($function);


		// TWIG FUNCTION
		// add a twig wrapper for the "strip_tags" method
		$function = new \Twig_SimpleFunction('strip_tags', function ($item, $decode_entities = true, $remove_breaks = true) {
			$str = (string) $item;
			$result = wp_strip_all_tags( $str, $remove_breaks);

			if( $decode_entities ) {
				$result = html_entity_decode( $result );
			}

			// make JSON safe
			$json = json_encode( $result );

			// remove surrounding quotes
			return trim($json, '"');
		});
		$twig->addFunction($function);


		// TWIG FUNCTION
		// add a twig wrapper for the "html" method
		$function = new \Twig_SimpleFunction('html', function ($item, $decode_entities = true ) {
			$str = (string) $item;

			// strip newlines and replace with <br>
			$result = preg_replace('/\v+|\\\r\\\n/','<br/>', $str);

			if( $decode_entities ) {
				$result = html_entity_decode( $result );
			}

			// sanitize
			$result = wp_kses_post( $result );

			// make JSON safe
			$json = json_encode( $result );

			// remove surrounding quotes
			return trim($json, '"');
		});
		$twig->addFunction($function);


		// render output
		$output = $twig->render($template_key, array('feed_id' => $feed_id, 'data' => $data));

		// convert back to a usable structure if it's in JSON format
		$json = json_decode( $output );
		if( $json ) $output = $json;

		return $output;
	}

	/**
	 * Apply the field mapping to the input data
	 * @param {object} $data - the source data
	 * @param {array} $map - an array of field mappings used to extract data (JSONPath strings)
	 * @return {array}
	 */
	protected function _map( $data, $map ) {
		$mapped = array();

		foreach( $map as $m ) {
			if( array_key_exists('key', $m) && array_key_exists('path', $m) ) {
				$key  = $m['key'];
				$path = $m['path'];
				$multi = isset($m['multi']) && $m['multi'] == 1;

				$mapped[$m['key']] = $this->find_node( $data, $path, !$multi );
			}
		}

		// preserve error message if defined
		if( is_array( $data ) && isset($data['error']) ) {
			$mapped['error'] = $data['error'];
		}

		return $mapped;
	}

	/**
	 * Get feed metadata
	 *
	 * @param $id
	 *
	 * @return array|bool
	 */
	protected function get_feed_metadata($feed_id) {
		$feed = get_post($feed_id);

		$output = array(
			'title'		    => '',
			'type'		    => '',
			'slug'		    => '',
			'permalink'	    => '',
			'timestamp'	    => date("Y-m-d H:i:s"),
			'data'		    => array(),
			'error'		    => FALSE,
			'cache_active'  => Midgard::is_cache_active()
		);

		// post doesn't exist? Abort here.
		if(!$feed) {
			return FALSE;
		}

		$output['title'] 	= $feed->post_title;
		$output['type'] 	= get_post_meta( $feed_id, 'midgard_feed_type', true );
		$output['slug'] 	= $feed->post_name;
		$output['permalink']= get_post_permalink($feed_id);

		return $output;
	}

	/**
	 * Transfrorm data into a different format
	 * If root node is defined then parse structure and return that
	 * THIS METHOD IS OVERRIDDEN BY PLUGINS
	 */
	protected function transform_data($data, $feed_id) {
		// get mapping mode
		$mode 	   = get_post_meta( $feed_id, 'midgard_mapping_mode', true );

		// simple mode allows us to reset the root node
		if( $mode == 'simple') {

			// optionally set a deeper root path
			$root_path = get_post_meta( $feed_id, 'midgard_feed_root_path', true );
			$multi 	   = get_post_meta( $feed_id, 'midgard_feed_root_multi', true );

			if($root_path) {
				$data = $this->find_node( $data, $root_path, $multi != 1 );
			}

		}

		return $data;
	}

	/**
	 * Retrieve data
	 * THIS METHOD SHOULD BE OVERRIDDEN BY PLUGINS WHICH EXTEND THIS CLASS
	 */
	protected function get_feed_data($feed_id) {
		// override me
		return $feed_id;
	}

	/**
	 * Push data into our standard return structure
	 * @param {int} feed_id - the feed ID
	 * @param {*} 	data - the feed data
	 */
	protected function create_return_structure($feed_id, $data) {
		$structure = $this->get_feed_metadata($feed_id);


		// hash items so we can uniquely identify later (e.g. tracking read status in app)
		if( $this->is_sequential_array($data) ) {
			foreach( $data as &$item ) {
				if( is_array( $item ) ) {
					$item['_midgard_hash'] = $this->create_hash($item);
				} else if( is_object( $item ) ) {
					$item->_midgard_hash = $this->create_hash($item);
				}
			}
		}
		// or if data is a single JSON object then add the hash to that
		else if( is_array($data ) ) {
			$data['_midgard_hash'] = $this->create_hash($data);
		}

		$structure['data'] = $data;

		// hash the whole thing
		$structure['_midgard_hash'] = $this->create_hash($data);

		// if we have an error response null the data after extracting the error info
		if(isset($data['error']) && $data['error']) {
			$structure['error'] = $data['error'];
			$structure['data'] = null;
		}

		return $structure;
	}


	/**
	 * Format an error message for output in the feed
	 */
	protected function error_message($message) {
		return array('error' => $message);
	}

	/**
	 * Find a node value in a JSON structure given a path
	 * @param {array} $item - a json-encoded RSS item
	 * @param {string} $path - path to the node value we want to return
	 *
	 * usage: find_node( $item, 'path/to/somevalue');
	 *
	 * this will find and return (if it exists) item.path.to.somevalue - flattening arrays if necessary
	 *
	 * @return String node value
	 */
	protected function find_node( $item, $path, $single=false ) {
		$value = '';

		// empty path = empty value
		$trimmed = trim($path);
		if( empty( $trimmed ) ) {
			return $value;
		}

		try {
			$obj = new JsonObject($item);
			$value = $obj->get($path);
			if( $value === false ) $value = NULL;
		}
		catch( JsonPath\InvalidJsonPathException $ex) {
			trigger_error('Invalid JSON Path : ' . $ex->getMessage(), E_USER_WARNING);
			$value = '';
		}
		catch( Exception $ex ) {
			trigger_error('Unable to find node with path ' . $path, E_USER_WARNING );
			$value = '';
		}

		if($single && is_array($value)) {
			if( empty( $value )) $value = '';
			else 				 $value = $value[0];
		}
		return $value;

	}

	/**
	 * Utility method - test for sequential array (as opposed to associative)
	 */
	function is_sequential_array($data) {
		$is_sequential = is_array($data) && (empty($data) || (array_merge($data) === $data && is_numeric( implode( array_keys( $data ) ) ) ) );
		return $is_sequential;
	}

	/**
	 * Create a hash of the given input. This is just to check uniqueness, so needs to be fast but not necessarily secure.
	 */
	function create_hash( $data ) {
		return md5( json_encode( $data ) );
	}

	/**
	 * Is the data packet valid?
	 * - Not null
	 * - Data key exists and is not null
	 * - No error key
	 * @param {Array} $data
	 */
	function is_valid_data( $data ) {
		return 	is_array($data)										// data is array
				&& isset($data['data']) 							// data exists
				&& ! is_null( $data['data'] ) 						// data is not null
				&& (! isset( $data['error']) || ! $data['error']);	// no error
	}
}
