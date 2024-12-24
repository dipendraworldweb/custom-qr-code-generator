<?php
/**
* Listings of QR.
*
* @package    Cqrc_Generator
* @subpackage Cqrc_Generator/admin
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
* QR Code Listing Page
*
* Handle listing
*
* @package Generate QR Code
* @since 1.0.0
*/
/**
* Cqrc_Custom_List_Table
*/
class Cqrc_Custom_List_Table extends WP_List_Table {

    /**
    * __construct
    *
    * @return void
    */
    public function __construct() {
        parent::__construct(
            array(
                'singular' => 'item',
                'plural'   => 'items',
                'ajax'     => false,
            )
        );
    }

    /**
    * Column_default
    *
    * @param  mixed $item return item.
    * @param  mixed $column_name return column name.
    */
    
    public function column_default( $item, $column_name ) {
        return $item[ $column_name ];
    }

    public function column_total_scans($item) {
        return esc_html($item['total_scans']);
    }
    /**
    * Get_bulk_actions
    *
    * @return $actions
    */
    public function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }

    /**
    * Process_bulk_action
    */
    public function process_bulk_action() {

        if ( 'delete' === $this->current_action() ) {
            if (empty($_REQUEST['_qrcode_bulk_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_qrcode_bulk_nonce'])), 'qrcode_bulk_action')) {
                wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator'));
            }
            
            $delete_ids = ( !empty( $_POST['id'] ) ) ? array_map( 'absint', $_POST['id'] ) : array();
            if ( ! empty( $delete_ids ) ) {
                foreach ( $delete_ids as $id ) {
                    self::cqrc_delete_qr( $id );
                }
            }
        }
    }

    /**
    * Column_qrcode
    *
    * @param  mixed $item item.
    */
    public function column_qrcode( $item ) {
        return '<img src="' . esc_url( $item['qr_code'] ) . '" alt="QR Code" style="max-width: 100px; height: auto;" />'; // phpcs:ignore
    }

    /**
    * Column_cb
    *
    * @param mixed $item item.
    */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            esc_attr($item['id'], 'custom-qr-code-generator' )
        );
    }

    public function column_name( $item ) {
        $name = esc_html( $item['name'] );

        // Generate actions.
        $actions = array(
            'edit' => sprintf(
                '<a href="%s">' . __( 'Edit', 'custom-qr-code-generator' ) . '</a>',
                esc_url( add_query_arg(
                    array(
                        'page' => 'custom-qrcode-generate-form',
                        'id' => $item['id'],
                        '_qr_code_nonce_action' => wp_create_nonce('qr_code_nonce_action'),
                    ),
                    admin_url('admin.php')
                ) )
            ),
            'delete' => sprintf(
                '<a href="%s" class="submitdelete" onclick="return confirm(\'Are you sure you want to delete this record?\');">' . __( 'Delete', 'custom-qr-code-generator' ) . '</a>',
                esc_url( add_query_arg(
                    array(
                        'page' => 'custom-qr-code-generator',
                        'action' => 'delete',
                        'id' => $item['id'],
                        '_qr_code_nonce_action' => wp_create_nonce('qr_code_nonce_action'),
                    ),
                    admin_url('admin.php')
                ) )
            ),
            'download' => sprintf(
                '<a href="#" class="qrcode-download-link-trigger" data-id="%d" data-name="%s">' . __( 'Download', 'custom-qr-code-generator' ) . '</a>',
                $item['id'],
                esc_attr( $item['name'] )
            ),
        );

        // Add actions HTML after the name.
        return sprintf(
            /* translators: %1$s: The name of the item; %2$s: The actions available for the item*/
            __( '%1$s<br><div class="row-actions">%2$s</div>', 'custom-qr-code-generator' ),
            $name,
            implode(' | ', $actions)
        );
    }
    
    public function column_description( $item ) {
        $unserialize_desc = $item['description'];
        if ( is_serialized( $unserialize_desc ) && is_serialized_string( $unserialize_desc ) ) {
            $unserialize_desc = maybe_unserialize( $unserialize_desc );
        }

        // Strip HTML tags to get plain text
        $description = wp_strip_all_tags( $unserialize_desc );

        // Set the word limit
        $word_limit = 20;

        // Explode the description into an array of words
        $words = explode( ' ', $description );

        // Limit the number of words
        if ( count( $words ) > $word_limit ) {
            $words = array_slice( $words, 0, $word_limit );
            $description = implode( ' ', $words ) . '...';
        }

        return $description;
    }
    
    /**
    * Column_edit
    *
    * @param  mixed $item item.
    */
    public function column_edit( $item ) {
        $edit_url = add_query_arg(
            array(
                'page' => 'custom-qrcode-generate-form',
                'id'   => $item['id'],
            ),
            admin_url('admin.php')
        );

        return sprintf(
            '<a href="%s" class="page-title-action">%s</a>',
            esc_url( $edit_url ),
            __( 'Edit', 'custom-qr-code-generator' )
        );
    }

    /**
    * Get_columns
    *
    * @return $columns column.
    */
    public function get_columns() {
        $columns = array(
            'cb'         => '<input type="checkbox" />',
            'qrcode'     => 'QR Code',
            'name'       => 'Name',
            'description'=> 'Description',
            'url'        => 'Destination URL',
            'total_scans' => 'Total Scan',
            'shortcode'  => 'Shortcode',
            'created_at' => 'Created',
        );
        return $columns;
    }

    /**
    * Get_column_shortcode
    *
    * @return $column_shortcode sortable.
    */
    public function column_shortcode( $item ) {
        // Generate the shortcode with the ID, ensuring it's properly escaped.
        $shortcode = sprintf('[cqrc_gen_qrcode_view id="%d"]', esc_attr($item['id']));
        $copy_text = esc_html( 'Code copied!!!', 'custom-qr-code-generator' );
        
        // Prepare the translatable text for the shortcode.
        $translatable_shortcode = sprintf(
            /* translators: %s: The shortcode of the item */
            __('%s', 'custom-qr-code-generator'), // phpcs:ignore.
            esc_html($shortcode)
        );

         // Return the span element with the onclick event to copy the shortcode to the clipboard.
 		return sprintf(
            '<div class="shortcode-list-cqrc"><span class="shortcode" id="copy-code-icon-%d" data-clipboard-text="%s">
	            <pre id="shortcode-code"><code>%s</code><span id="copy-message-%d" style="display: none; color: green; margin-left: 10px;">%s</span></pre><span id="copy-code-icons" class="dashicons dashicons-admin-page" style="cursor: pointer; font-size: 20px; margin-left: 10px;  margin-right: 20px;" title="Copy to clipboard"></span></span></div>',
            esc_attr($item['id']),
            esc_attr($shortcode),
            esc_html($translatable_shortcode),
            esc_attr($item['id']),
            esc_html($copy_text)
        );
    }

    /**
    * Get_sortable_columns
    *
    * @return $sortable_columns sortable.
    */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name'       => array('name', false),
            'description' => array('description', false),
            'url'        => array('url', false),
            'total_scans' => array('total_scans', false),
            'created_at' => array('created_at', false),
            'updated_at' => array('updated_at', false),
        );

        return $sortable_columns;
    }
    
    public function cqrc_get_custom_data_from_database($search_term = '', $orderby = 'id', $order = 'desc') {
        global $wpdb;
        $table_name = esc_sql( QRCODE_GENERATOR_TABLE );
        // phpcs:disable

        // Escape orderby and order to prevent SQL injection.
        $orderby = esc_sql($orderby);
        $order = esc_sql($order);

        // Add search filter if a search term is provided.
        if (!empty($search_term)) {
            $search_term = '%' . $wpdb->esc_like($search_term) . '%';
            $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE name LIKE %s OR description LIKE %s",
                $search_term,
                $search_term 
            );
        }else{
            $query = esc_sql("SELECT * FROM {$table_name}");
        }

        // Append the ORDER BY clause to the query.
        $query .= " ORDER BY {$orderby} {$order}";

        // Fetch and return the data.
        $data = $wpdb->get_results($query, ARRAY_A); // phpcs:ignore

        // phpcs:enable
        if (false === $data) {
            echo 'Error: ' . esc_html($wpdb->last_error);
            return array();
        }

        return $data;
    }
    
    /**
    * Prepare_items
    */
    public function prepare_items() {
        $this->process_bulk_action();

        // Get sorting params from the URL or default to 'id' and 'asc'
        $orderby = !empty($_GET['orderby']) ? sanitize_text_field(wp_unslash($_GET['orderby'])) : 'id'; // phpcs:ignore
        $order = !empty($_GET['order']) ? sanitize_text_field(wp_unslash($_GET['order'])) : 'desc'; // phpcs:ignore

        // Fetch data with sorting.
        $search_term = !empty($_REQUEST['s']) ? sanitize_text_field(wp_unslash(trim($_REQUEST['s']))) : ''; // phpcs:ignore
        $data = $this->cqrc_get_custom_data_from_database($search_term, $orderby, $order);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        // Pagination logic.
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        // Apply pagination.
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        $this->items = $data;

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page' => $per_page,
                'total_pages' => ceil($total_items / $per_page),
            )
        );
    }


    /**
    * Usort_reorder
    *
    * @param mixed $a order.
    * @param mixed $b order.
    */
    public function usort_reorder($a, $b) {
        $orderby = !empty($_GET['orderby']) ? sanitize_text_field(wp_unslash($_GET['orderby'])) : 'id'; // phpcs:ignore
        $order = !empty($_GET['order']) ? sanitize_text_field(wp_unslash($_GET['order'])) : 'asc'; // phpcs:ignore

        // Compare values for sorting
        $result = strcmp($a[$orderby], $b[$orderby]);

        // If descending order, reverse the result
        return ('desc' === $order) ? $result : -$result;
    }

    public function column_download( $item ) {
        // Get the ID and ensure it's an integer
        $id = absint( $item['id'] );

        // Ensure the URLs for downloads are correctly formed
        $download_png_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'png' ), home_url( '/download-qr/' ) ) );
        $download_jpg_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'jpg' ), home_url( '/download-qr/' ) ) );
        $download_pdf_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'pdf' ), home_url( '/download-qr/' ) ) );

        // Return the HTML for the download buttons
        return sprintf(
            '<div class="download-qr-code-column">
            <a class="button button-primary download-buttons-qrcode" href="%s" download="qrcode.png">%s</a>
            <a class="button button-primary download-buttons-qrcode" href="%s" download="qrcode.jpg">%s</a>
            <a class="button button-primary download-buttons-qrcode" href="%s" download="qrcode.pdf">%s</a>
            </div>',
            $download_png_url,
            esc_html__( 'PNG', 'custom-qr-code-generator' ),
            $download_jpg_url,
            esc_html__( 'JPG', 'custom-qr-code-generator' ),
            $download_pdf_url,
            esc_html__( 'PDF', 'custom-qr-code-generator' )
        );
    }

    /**
    * Delete a qrcode record.
    *
    * @param int $id qrcode ID.
    */
    public static function cqrc_delete_qr( $id ) {
        global $wpdb;
        $table_name = esc_sql( QRCODE_GENERATOR_TABLE );
        $insights_table = esc_sql( QRCODE_INSIGHTS_TABLE );

        // Step 1: Retrieve the QR Code Data.
        $qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT qr_code, id AS qrid FROM $table_name WHERE ID = %d", $id )); // phpcs:ignore
        $qr_code_rows = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", $id )); // phpcs:ignore
        if ( ! $qr_code_row ) {
            return;
        }

        if ( ! $qr_code_rows ) {
        // QR code record with the given ID doesn't exist.
            return;
        }

        $qr_code = $qr_code_row->qr_code;
        $qrid = $qr_code_rows->id;

        // Step 2: Find Matching Media Posts.
        $media_posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment'",  $qr_code )); // phpcs:ignore

        // Step 3: Delete All Matching Media Posts.
        if ( $media_posts ) {
            foreach ( $media_posts as $media_post ) {
            // true for force delete, false for trash.
                wp_delete_post( $media_post->ID, true );
            }
        }

        // Step 4: Delete Records from qrcode_insights where qrid matches
        $wpdb->delete( $insights_table, array( 'qrid' => $qrid ), array( '%d' )); // phpcs:ignore

        // Step 5: Delete the QR Code Record.
        $wpdb->delete( $table_name, array( 'ID' => $id ), array( '%d' )); // phpcs:ignore
    }
}

// Create an instance of the Cqrc_Custom_List_Table and prepare items.
$wp_list_table = new Cqrc_Custom_List_Table();
$wp_list_table->prepare_items();

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'QR Codes', 'custom-qr-code-generator' ); ?></h1>
    <a href="<?php echo add_query_arg( 'page', 'custom-qrcode-generate-form', admin_url( 'admin.php' ) ); // phpcs:ignore?>" class="page-title-action"><?php esc_html_e( 'Add New QR Code', 'custom-qr-code-generator' ); ?></a>
    <a href="<?php echo add_query_arg( 'page', 'custom-qrcode-export', admin_url( 'admin.php' ) ); // phpcs:ignore?>" class="page-title-action"><?php esc_html_e( 'Export QR Codes', 'custom-qr-code-generator' ); ?></a>
    <a href="<?php echo add_query_arg( 'page', 'custom-qrcode-import', admin_url( 'admin.php' ) ); // phpcs:ignore?>" class="page-title-action"><?php esc_html_e( 'Import QR Codes', 'custom-qr-code-generator' ); ?></a>
    <form method="post" id="qr-listing-form">
      <input type="hidden" name="page" value="custom-qr-code-generator">
      <?php
      wp_nonce_field( 'qrcode_bulk_action', '_qrcode_bulk_nonce' );
      $wp_list_table->search_box( 'Search', 'search_id' );
      $wp_list_table->display();
      ?>
  </form>
</div>
