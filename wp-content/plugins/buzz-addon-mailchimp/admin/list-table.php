<?php

/**
 * The class to allow plugin the ability to create custom WP_List_Table objects
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/admin
 * @author     Firefly Interactive
 */
class FF_Newsletter_Mailchimp_List_Table extends WP_List_Table {

    /**
     * Constructor will create the menu item
     */
    public function __construct( $options ) {

		// init items array and merge with $options
		$this->items = array_merge( array(
										'columns' 		=> NULL,
										'sortable_cols' => NULL,
										'data'			=> NULL,
										'current_page'	=> NULL,
										'page_size'		=> NULL,
										'total_items'	=> NULL
									), $options );

		// Parent Constructor
    	parent::__construct();

		// Prepare items
		$this->prepare_items();

		// Display filters and search
		//$this->search_box('search', 'search_id');
    }

	public function prepare_items() {

		// prepare columns
        $columns 	= $this->get_columns( $this->items['columns'] );
        $hidden 	= $this->get_hidden_columns(); // unused but needs to be included for compatibility
        $sortable 	= $this->get_sortable_columns( $this->items['sortable_cols'] );

		// get table data
        $data 			= $this->table_data( $this->items['data'] );
        usort( $data, array( &$this, 'sort_data' ) );
        $per_page 		= $this->items['page_size'];
        $current_page 	= $this->items['current_page'];

		// transform to readable data
		foreach($data as $key => $val) {
			switch($data[$key]['status']) {
				case 'save' 	: $data[$key]['status'] = 'Saved'; break;
				case 'paused' 	: $data[$key]['status'] = 'Paused'; break;
				case 'schedule' : $data[$key]['status'] = 'Scheduled'; break;
				case 'sending' 	: $data[$key]['status'] = 'Sending...'; break;
				case 'sent' 	: $data[$key]['status'] = 'Sent'; break;
				default			:
			}
		}

		// pagination
        $this->set_pagination_args( array(
            'total_items' => $this->items['total_items'],
            'per_page'    => $per_page
        ) );

		// fill columns
        $this->_column_headers = array( $columns, $hidden, $sortable );

		// fill data
        $this->items = $data;

	}

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @param $columns 	{array} 	Array listing the columns
	 * 								Format: array('id' => 'ID', 'title' => 'Title'),
     *
     * @return Array
     */
    public function get_columns( $columns=NULL ) {
		if( empty( $columns )) {
			return array();
		}

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
	 *
	 * @param $sortable_cols 	{array} 	Array listing the sortable columns
	 * 										Format: array('id' => array('id', false), 'title' => array('title', false))
     *
     * @return Array
     */
    public function get_sortable_columns( $sortable_cols=NULL ) {
		if( empty( $sortable_cols )) {
			return array();
		}

        return $sortable_cols;
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data( $data=NULL ) {
		if( empty( $data )) {
			return array();
		}

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
    }

	/**
     * Define row actions on Name column
     *
     * @param  Array $item        Data
     *
     * @return Mixed
     */
	public function column_name($item) {
		// set up action links
		$actions = array(
			'view'		=> sprintf( '<a href="%s" target="_blank">Preview</a>',
									$item['archive_url_long'] ),
			'update'	=> sprintf( '<a href="?post_type=newsletter&page=%s&tab=%s&cid=%s">Edit Options</a>',
									$_REQUEST['page'],
									'update',
									$item['id'] ),
			'stats'		=> sprintf( '<a href="?post_type=newsletter&page=%s&tab=%s&cid=%s">Statistics</a>',
									$_REQUEST['page'],
									'stats',
									$item['id'] ),
			'delete'	=> sprintf( '<a href="?post_type=newsletter&page=%s&tab=%s&cid=%s">Delete</a>',
									$_REQUEST['page'],
									'delete',
									$item['id'] ),
		);

		// disable Send link if campaign already sent
		$sent = array();
		if( $item['status'] !== 'Saved' ) {
			$sent['send'] = 'Already sent';
		} else {
			$sent['send'] = sprintf( '<a href="?post_type=newsletter&page=%s&tab=%s&cid=%s">Send</a>',
									$_REQUEST['page'],
									'send',
									$item['id'] );
		}

		// Add Send link in with rest of links
		array_splice( $actions, 2, 0, $sent );

		// return action links
		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
	}

    /**
     * Allows you to sort the data by the tables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'create_time';
        $order = 'desc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strnatcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc') {
            return $result;
        }

        return -$result;
    }

}