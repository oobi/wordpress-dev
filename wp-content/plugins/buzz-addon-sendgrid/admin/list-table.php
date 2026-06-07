<?php

/**
 * The class to allow plugin the ability to create custom WP_List_Table objects
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/admin
 * @author     Firefly Interactive
 */
class FF_Newsletter_Sendgrid_List_Table extends WP_List_Table {

    /**
     * Constructor will create the menu item
     */
    public function __construct( $options ) {

		// init items array and merge with $options
		$this->items = array_merge( array(
										'columns' 		=> NULL,
										'data'			=> NULL
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

		// get table data
        $data 			= $this->table_data( $this->items['data'] );
        $perPage 		= 20;
        $currentPage 	= $this->get_pagenum();
        $totalItems 	= count($data);
        $data 			= array_slice($data,(($currentPage-1)*$perPage),$perPage);


		// pagination
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

		// fill columns
        $this->_column_headers = array($columns, $hidden);

		// fill data
        $this->items = $data;

	}

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @param $columns 	{array} 	Array listing the columns
	 * 								Format: array('id' => 'ID', 'title' => 'Title'),
     *
     * @return array
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
     * @return array
     */
    public function get_hidden_columns() {
        return array();
    }


    /**
     * Get the table data
     *
     * @return array
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
     * @param  array $item        Data
     * @param  string $column_name - Current column name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
    }

    /**
     * Define row actions on Name column
     *
     * @param  array $item        Data
     *
     * @return mixed
     */
	public function column_name($item) {
		// set up action links
		$actions = array(
            'view'		=> sprintf( '<a href="%s" target="_blank">Preview</a>',
									$item['url'] ),
            'delete'	=> sprintf( '<a href="?post_type=newsletter&page=%s&tab=%s&cid=%s">Delete</a>',
									$_REQUEST['page'],
									'delete',
									$item['id'] ),
		);

        // disable Send link if campaign already sent
		$sent = array();
		if( $item['status'] !== 'draft' ) {
			$sent['send'] = 'Already scheduled or sent';
		} else {
			$sent['send'] = sprintf( '<a href="?post_type=newsletter&page=%s&tab=%s&cid=%s">Test &amp; Send</a>',
									$_REQUEST['page'],
									'send',
									$item['id'] );
		}

		// Add Send link in with rest of links
		array_splice( $actions, 1, 0, $sent );

        //disbale stats links for unsent campaigns
        $stats = array();
        if( $item['status'] !== 'sent' ) {
			$stats['stats'] = 'Not sent';
		} else {
			$stats['stats'] = sprintf( '<a href="?post_type=newsletter&page=%s&tab=%s&cid=%s">Statistics</a>',
									$_REQUEST['page'],
									'stats',
									$item['id'] );
		}
        // Add Send link in with rest of links
		array_splice( $actions, 2, 0, $stats );

		// return action links
		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );

	}

}
