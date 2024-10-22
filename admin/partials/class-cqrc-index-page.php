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
function get_custom_data_from_database($search_term = '') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qrcode_generator';

    if (!empty($search_term)) {
        $search_term = esc_sql($wpdb->esc_like($search_term));
        $query = "SELECT * FROM {$table_name} WHERE name LIKE '%{$search_term}%' OR description LIKE '%{$search_term}%' ORDER BY id DESC"; // Adjust 'id' to the appropriate column for your needs
        $data = $wpdb->get_results($query, ARRAY_A); // phpcs:ignore
    } else {
        $data = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC", ARRAY_A); // phpcs:ignore
    }

    if (false === $data) {
        echo 'Error: ' . esc_html($wpdb->last_error);
        return array();
    }

    return $data;
}


if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
* Custom_List_Table
*/
class Custom_List_Table extends WP_List_Table {

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

            if (!isset($_REQUEST['_qrcode_bulk_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_qrcode_bulk_nonce'])), 'qrcode_bulk_action')) {
                wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
            }
            
            $delete_ids = ( isset( $_POST['id'] ) ) ? array_map( 'absint', $_POST['id'] ) : array();
            if ( ! empty( $delete_ids ) ) {
                foreach ( $delete_ids as $id ) {
                    self::delete_qr( $id );
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
        return '<img src="' . esc_url( $item['qr_code'] ) . '" alt="QR Code" style="max-width: 100px; height: auto;" />';
    }

    /**
    * Column_cb
    *
    * @param mixed $item item.
    */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    public function column_name( $item ) {
        $name = esc_html( $item['name'] );

        // Generate actions.
        $actions = array(
            'edit'   => sprintf(
                '<a href="%s">'. __( 'Edit', 'custom-qrcode-generator' ) .'</a>',
                esc_url( admin_url( 'admin.php?page=custom-qrcode-generate-form&id=' . $item['id'] . '&_qr_code_nonce_action=' . wp_create_nonce('qr_code_nonce_action') ) )
            ),
            'delete' => sprintf(
                '<a href="%s" class="submitdelete" onclick="return confirm(\'Are you sure?\');">'. __( 'Delete', 'custom-qrcode-generator' ) .'</a>',
                esc_url( admin_url( 'admin.php?page=custom-qrcode-generator&action=delete&id=' . $item['id'] . '&_qr_code_nonce_action=' . wp_create_nonce('qr_code_nonce_action')  ), 'delete_qr_' . $item['id'] )
            ),
            'download' => sprintf(
                '<a href="#" class="qrcode-download-link-trigger" data-id="%d" data-name="%s">'. __( 'Download', 'custom-qrcode-generator' ) .'</a>',
                $item['id'],
                esc_attr( $item['name'] )
            ),
        );

        // Add actions HTML after the name.
        return sprintf(
            '%s<br><div class="row-actions">%s</div>',
            $name,
            implode(' | ', $actions)
        );
    }
    
    public function column_description( $item ) {
        $description = esc_html( $item['description'] );
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
        $edit_url = admin_url( 'admin.php?page=custom-qrcode-generate-form&id=' . $item['id'] );
        return sprintf(
            '<a href="%s" class="page-title-action">'. __( 'Edit', 'custom-qrcode-generator' ) .'</a>',
            $edit_url
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
        $shortcode = sprintf('[cqrc_gen_qrcode_view id="%d"]', esc_attr($item['id']));
        return '<span class="shortcode" onclick="copyToClipboard(\'' . esc_js($shortcode) . '\')">' . esc_html($shortcode) . '</span>';
    }

    /**
    * Get_sortable_columns
    *
    * @return $sortable_columns sortable.
    */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name'       => array( 'name', false ),
            'description'     => array( 'description', false ),
            'url'        => array( 'url', false ),
            'total_scans'     => array( 'total_scans', false ),
            'created_at' => array( 'created_at', false ),
            'updated_at' => array( 'updated_at', false ),
        );
        return $sortable_columns;
    }

    /**
    * Prepare_items
    */
    public function prepare_items() {
        $this->process_bulk_action();
        $data     = get_custom_data_from_database();
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        // Sorting logic.
        usort( $data, array( &$this, 'usort_reorder' ) );

        // Pagination logic.
        $per_page = 10;

        // Add the search box
        $search_term = isset( $_REQUEST['s'] ) ? sanitize_text_field(wp_unslash( trim( $_REQUEST['s'] ) ) ) : ''; // phpcs:ignore
        $data        = get_custom_data_from_database( $search_term );

        $current_page = $this->get_pagenum();
        $total_items  = count( $data );

        $data        = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
        $this->items = $data;
        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page ),
            )
        );
    }

    /**
    * Usort_reorder
    *
    * @param mixed $a order.
    * @param mixed $b order.
    */
    public function usort_reorder( $a, $b ) {
        // If no sort, default to title.
        $orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'id'; // phpcs:ignore
        // If no order, default to asc.
        $order = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc'; // phpcs:ignore
        // Determine sort order.
        $result = strcmp( $a[ $orderby ], $b[ $orderby ] );
        // Send final sort direction to usort.
        return ( 'desc' === $order ) ? $result : -$result;
    }

    public function column_download( $item ) {
        $id = absint( $item['id'] );
        // Ensure the URLs for downloads are correctly formed
        $download_png_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'png' ), home_url( '/download-qr/' ) ) );
        $download_jpg_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'jpg' ), home_url( '/download-qr/' ) ) );
        $download_pdf_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'pdf' ), home_url( '/download-qr/' ) ) );

        return sprintf(
            '<div class="download-qr-code-column">
            <a class="button button-primary download-buttons-qrcode" href="%s" download="qrcode.png">'. __( 'PNG', 'custom-qrcode-generator' ) .'</a>
            <a class="button button-primary download-buttons-qrcode" href="%s" download="qrcode.jpg">'. __( 'JPG', 'custom-qrcode-generator' ) .'</a>
            <a class="button button-primary download-buttons-qrcode" href="%s" download="qrcode.pdf">'. __( 'PDF', 'custom-qrcode-generator' ) .'</a>
            </div>',
            $download_png_url,
            $download_jpg_url,
            $download_pdf_url
        );
    }

    /**
    * Delete a qrcode record.
    *
    * @param int $id qrcode ID.
    */
    public static function delete_qr( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'qrcode_generator';
        $insights_table = $wpdb->prefix . 'qrcode_insights';

        // Step 1: Retrieve the QR Code Data.
        $qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT qr_code, id AS qrid FROM {$wpdb->prefix}qrcode_generator WHERE ID = %d", $id )); // phpcs:ignore
        $qr_code_rows = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE ID = %d", $id )); // phpcs:ignore
        if ( ! $qr_code_row ) {
        // QR code record with the given ID doesn't exist.
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

// Create an instance of the Custom_List_Table and prepare items.
$wp_list_table = new Custom_List_Table();
$wp_list_table->prepare_items();

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'QR Codes', 'custom-qrcode-generator' ); ?></h1>
    <a href="<?php echo ( esc_url( site_url() ) . '/wp-admin/admin.php?page=custom-qrcode-generate-form' ); ?>" class="page-title-action"><?php esc_html_e( 'Add New QR Code', 'custom-qrcode-generator' ); ?></a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=custom-qrcode-export' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Export QR Codes', 'custom-qrcode-generator' ); ?></a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=custom-qrcode-import' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Import QR Codes', 'custom-qrcode-generator' ); ?></a>
    <form method="post" id="qr-listing-form">
      <input type="hidden" name="page" value="custom-qrcode-generator">
      <?php
      wp_nonce_field( 'qrcode_bulk_action', '_qrcode_bulk_nonce' );
      $wp_list_table->search_box( 'Search', 'search_id' );
      $wp_list_table->display();

      ?>
  </form>
</div>

<script type="text/javascript">
    document.getElementById('qr-listing-form').addEventListener('submit', function(e) {
        var bulkAction = document.querySelector('select[name="action"]').value;
        if (bulkAction === 'delete') {
            var confirmed = confirm("Are you sure you want to delete the selected QR codes?");
            if (!confirmed) {
                e.preventDefault();
            }
        }
    });
</script>
<script type="text/javascript">
    function copyToClipboard(e){if(navigator.clipboard&&window.isSecureContext)navigator.clipboard.writeText(e).then((function(){alert("Shortcode copied to clipboard!")})).catch((function(e){alert("Error copying text: "+e)}));else{const t=document.createElement("textarea");t.value=e,document.body.appendChild(t),t.select();try{document.execCommand("copy"),alert("Shortcode copied to clipboard!")}catch(e){alert("Error copying text: "+e)}document.body.removeChild(t)}};
</script>