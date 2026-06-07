<?php

namespace FF\LogicalDoc;

/* Logical operations */

class Logical {

	// Logical Session ID (from login)
	protected $sid = 0;

	// SOAP CLIENTS
	protected $authClient;
	protected $documentClient;
	protected $folderClient;
	protected $searchClient;
	protected $systemClient;
	protected $securityClient;
	protected $tagClient;

	public function __construct() {
		$this->authClient 		= new \SoapClient ( LOGICAL_HOST . '/services/Auth?wsdl' 	);
		$this->documentClient 	= new \SoapClient ( LOGICAL_HOST . '/services/Document?wsdl' );
		$this->folderClient 	= new \SoapClient ( LOGICAL_HOST . '/services/Folder?wsdl' 	);
		$this->searchClient 	= new \SoapClient ( LOGICAL_HOST . '/services/Search?wsdl' 	);
		$this->systemClient 	= new \SoapClient ( LOGICAL_HOST . '/services/System?wsdl' 	);
		$this->securityClient 	= new \SoapClient ( LOGICAL_HOST . '/services/Security?wsdl' );
		$this->tagClient 		= new \SoapClient ( LOGICAL_HOST . '/services/Tag?wsdl' );
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// LOGIN / AUTH
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function login() {
		try{
			$loginParams = array ('username' => LOGICAL_USER, 'password' => LOGICAL_PASS );
			$result = $this->authClient->login( $loginParams );
			$this->sid = $result->return;
		} catch( \SoapFault $e ) {
			$this->logError($e);
		}
		return $this->sid;
	}

	public function logout($sid = false) {
		if( !$sid ) {
			$sid = $this->sid;
			$this->sid = null;
		}
		$logoutParams = array ('sid' => $sid );
		$result = $this->authClient->logout ( $logoutParams );
		return;
	}

	public function loginValidate($sid) {
		$loginParams = array ('sid' => $sid );
		$result = $this->authClient->valid( $loginParams );
		if($result->return) {
			$this->sid = $sid;
		}
		return $result;
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// DOCUMENTS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function getDocument( $docid ) {
		// invalid docid
		if( intval( $docid ) <= 0 ) return false;

		// SOAP params
		$params = array ('sid' => $this->sid, 'docId' => intval($docid) );

		// execute request
		try {
			$result = $this->documentClient->getDocument( $params );

		} catch( \SoapFault $e ) {
			$this->logError($e);
			$result = false;
		}

		return $result -> document ?? false;
	}

	public function checkDocumentExists( $docid ) {
		$doc = $this->getDocument($docid);
		return $doc && isset( $doc->id );
	}

	public function deleteDocument( $docid ) {
		// invalid docid
		if( intval( $docid ) <= 0 ) return false;

		// SOAP params
		$params = array ('sid' => $this->sid, 'docId' => intval($docid) );

		// execute request
		try {
			$this->documentClient->delete( $params );
			$result = true;
		} catch( \SoapFault $e ) {
			$this->logError($e);
			$result = false;
		}

		return $result;
	}

	public function createOrUpdateDocument( $page, $docid=0, $meta=array() ) {

		if(!$page) {
			return false;
		}

		// Since we need excerpt we need to setup post data
		// @see https://codex.wordpress.org/Function_Reference/setup_postdata
		global $post;
		$post = $page;
		setup_postdata($post);


		// doc filename
		$filename = $post->post_name . '.html';

		// get current WP username
		$user = wp_get_current_user();
		$login = $user->user_login;

		// init required parameters
		$doc = array();

		$doc ['creatorId'] 			= 0;
		$doc ['dateCategory'] 		= 0;
		$doc ['docType'] 			= 0;
		$doc ['exportStatus'] 		= 0;
		$doc ['fileSize'] 			= 0;
		$doc ['id'] 				= intval($docid);
		$doc ['immutable'] 			= 0;
		$doc ['indexed'] 			= 0;
		$doc ['lengthCategory'] 	= 0;
		$doc ['publisherId'] 		= 0;
		$doc ['signed'] 			= 0;
		$doc ['size'] 				= 0;
		$doc ['status'] 			= 0; // Status = 0: document unlocked

		// Setting some useful Tags for the document created (tags must be separated by commas)
		// sanitize tags
		// $tags = array_filter(explode(',', $docParams['Keywords']), 'strlen');
		// $doc ['tags'] = explode(',', $tags);

		// This is new in 6.4
		$doc ['published'] 			= 1;

		// This are new from 7.3
		$doc ['nature'] 			= 0; // 0 = document
		$doc ['pages'] 				= -1; // -1 = default
		$doc ['stamped'] 			= 0; // 0 = default (not stamped)

		// New from 8.5.x
		$doc ['barcoded'] 			= 0; // 0 = default (not barcoded)
		$doc ['ocrd'] 				= 0; // 0 = default (not ocrd)

		$doc ['language']			= 'en'; // This is important for indexing/search
		$doc ['fileName'] 			= $filename; // It's possible to assign a different file-name to the content
		$doc ['folderId'] 			= LOGICAL_ROOT_FOLDER; // create the document in specific folder

		// description - use excerpt
		$doc['summary']				= get_the_excerpt($post);

		// author
		$author 					= get_userdata($post->post_author);
		$author_name = $author ? $author->display_name : '';


		// permalink
		$permalink = get_permalink( $post );

		// get logical tags
		$tags = [];
		$terms = get_the_terms($post, 'logical-tag');

		if( $terms && !is_wp_error($terms) ) {
			foreach( $terms as $term ) {
				$tags[] = htmlspecialchars_decode( $term->name ); // turn &amp; back to &
			}
			$doc['tags'] = $tags;//implode(',', $tags);
		}

		// get all taxonomies as keywords
		/*
		$taxes = get_object_taxonomies( $post, 'names' );
		$all_terms = [];
		foreach( $taxes as $tax ) {
			$terms = get_the_terms( $post, $tax );

			// if terms available, get just their names
			if( $terms && !empty( $terms ) ) {
				$term_names = array_map( function( $t ) {
					if( strtolower( $t->name ) != 'uncategorized' ) { // do not include "Uncategorized" category
						return str_replace( ',', ' ', $t->name );
					}
				}, $terms);

				// merge
				$all_terms = array_merge( $all_terms, $term_names );
			}
		}

		// keywords comma delimited string
		$keywords = implode( array_filter( $all_terms ), ', '); // array_filter to remove any empty cells
		*/

		// set the template associated with the custom fields
		if( LOGICAL_TEMPLATE_ID ) {
			$doc['templateId'] = LOGICAL_TEMPLATE_ID;
		}

		// Extended custom attributes
		$doc['attributes'] = array(
			array(
				'editor'		=> 0,
				'mandatory'		=> 0,
				'name' 			=> 'title',
				'position'		=> 0,
				'stringValue' 	=> $page->post_title,
				'type'			=> 0
			),
			array(
				'editor'		=> 0,
				'mandatory'		=> 0,
				'name' 			=> 'external-link',
				'position'		=> 0,
				'stringValue' 	=> $permalink,
				'type'			=> 0
			),
			// ,
			// array(
			// 	'editor'		=> 0,
			// 	'mandatory'		=> 0,
			// 	'name' 			=> 'authors',
			// 	'position'		=> 0,
			// 	'stringValue' 	=> $author_name,
			// 	'type'			=> 0
			// )
			// array(
			// 	'editor'		=> 0,
			// 	'mandatory'		=> 0,
			// 	'name' 			=> 'keywords',
			// 	'position'		=> 0,
			// 	'stringValue' 	=> $keywords,
			// 	'type'			=> 0
			// )
		);

		if( is_array( $meta ) ) {
			foreach( $meta as $key=>$item ) {
				foreach( $item as $type=>$value) {
					$typeId = 0;
					switch( $type ) {
						case 'string' 	: $typeId = 0; break;
						case 'int' 		: $typeId = 1; break;
						case 'double' 	: $typeId = 2; break;
						case 'date' 	: $typeId = 3; break;
						case 'user' 	: $typeId = 4;
										  $type = 'int'; break;
						case 'boolean'	: $typeId = 5;
										  $type = 'int';
										  $value = !!$value ? 1 : 0;
										  break;
					}

					// renamed date field
					if( $key == 'date') {
						$key = 'created-date';
					}

					$doc['attributes'][] = array(
						'editor'		=> 0,
						'mandatory'		=> 0,
						'name' 			=> str_replace('_', '-', $key),
						'position'		=> 0,
						$type . 'Value'	=> $value,  // stringValue, dateValue etc
						'type'			=> $typeId
					);
				}
			}
		}

		// add a comment
		$doc['comment'] = sprintf( '%s via WordPress by %s',
									$docid > 0 ? 'Updated' : 'Created',
									$login);

		// LogicalDOC 8.3 - new required "hidden" parameter for attributes
		// LogicalDOC 8.3 - new required "multiple" parameter for attributes
		foreach($doc['attributes'] as &$att) {
			$att['hidden'] = 0;
			$att['multiple'] = 0;
		}

		// SOAP params
		$createParams = array ('sid' => $this->sid, 'document' => $doc);

		// reset WP post data
		wp_reset_postdata();

		// execute request

		// UPDATE
		if( intval($docid) > 0 ) {
			// checkout for update
			try {
				$co = $this->documentClient->checkout( array( 'sid' => $this->sid, 'docId' => $docid) );
			} catch( \SoapFault $e ) {
				$this->logError($e);
				return null;
			}

			// update metadata
			try {
				$result = $this->documentClient->update( $createParams );
			} catch( \SoapFault $e ) {
				$this->logError($e);
				$result = null;
			}

			// update content (checkin)
			try {
				$checkinParams = array(
					'sid'		=> $this->sid,
					'docId'		=> $docid,
					'comment'	=> 'Checked in via WordPress by ' . $login,
					'filename'	=> $filename,
					'release'	=> false,
					'content'	=> apply_filters( 'the_content', get_the_content())
				);

				$ci = $this->documentClient->checkin( $checkinParams );
			} catch( \SoapFault $e ) {
				$this->logError($e);
			}
		}
		// CREATE
		else {

			try {
				// add content key directly for create method
				$createParams['content'] = get_the_content();

				$result = $this->documentClient->create( $createParams );
			} catch( \SoapFault $e ) {
				$this->logError($e);
				$result = null;
			}
		}


		return $result;
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// FOLDERS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**
	 * Find the LogicalDoc folder with the given path.
	 * or FALSE if it doesn't exist
	 */
	public function getFolderIdFromPath( $path ) {
		// add default folder to start of path
		$folderPath = '/Default/' . strtolower($path);

		// remove trailing slashes
		$folderPath = rtrim($folderPath, '/');


		// SOAP params
		$findParams = array ('sid' => $this->sid, 'path' => $folderPath);

		// execute request
		try {
			$result = $this->folderClient->findByPath( $findParams );
		} catch( SoapFault $e ) {
			$this->logError($e);
			throw $e;
		}

		return isset($result->folder) ? $result->folder->id : FALSE;
	}

	public function createFolderPath( $path ) {
		$originalPath = $path;

		// for sanity force lowercase titles
		$path = strtolower($path);

		// store a list of folders to create
		$pending = [];

		// current folder ID
		$folderID = false;

		// check parent folder existence until we get to something that exists
		while( !$folderID )  {
			$folderID = $this->getFolderIdFromPath($path);

			// if the path exists then we just need to create any subfolders in pending
			if($folderID) {
				break;
			}

			$pending[] = basename($path);

			if(empty($path)) {
				break;
			}

			$path = dirname($path);
			$path = $path == '.' ? '' : $path;
		}

		// at this stage, the pending array contains subfolder paths that need to be created
		// under the base folder $folderID
		while( $folderName = array_pop($pending) ) {
			$folderID = $this->createFolder($folderName, $folderID);
			if(!$folderID) {
				throw new Exception('Error creating folder : ' . $originalPath);
			}
		}

		return $folderID;
	}

	public function createFolder( $folderName, $parentFolderId ) {
		// setup required input parameters
		$folder = array(
			'id'		=> 0,
			'name'		=> strtolower($folderName),
			'type'		=> 0,
			'description'	=> null,
			'parentId'	=> $parentFolderId,
			'hidden'	=> 0,
			'position'	=> 0,
			'templateLocked'	=> 0
		);

		// SOAP params
		$folderParams = array ('sid' => $this->sid, 'folder' => $folder);

		// init folder id - this will be the newly created folder's ID if successful
		$folderId = false;

		// execute request
		try {
			$result = $this->folderClient->create( $folderParams );
			$folderId = $result->folder->id;

		} catch( SoapFault $e ) {
			$this->logError($e);
			throw $e;
		}

		return $folderId;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// TAGS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function getAllTags() {
		// SOAP params
		$params = array ('sid' => $this->sid );

		// execute request
		try {
			$result = $this->tagClient->getTagsPreset( $params );

		} catch( \SoapFault $e ) {
			$this->logError($e);
			$result = false;
		}

		return $result;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// UTILS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Log an error message in transients for display on edit screen
	 */
	public function logError($e) {
		//var_dump($e); die();
		$message = $e->getMessage();
		// set a short duration transient which can be picked up later (after page refresh)
		set_transient( 'ff-logical-error-message', $message, 60 );
		set_transient( 'ff-logical-last-status', 'error', 60 );
	}

}