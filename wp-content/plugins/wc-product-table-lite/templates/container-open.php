<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$data = wcpt_get_table_data();

$html_class = trim( apply_filters( 'wcpt_container_html_class', 'wcpt wcpt-' . $data['id'] . ' ' . trim( $this->attributes['class'] ) ) );

ob_start();
?>
data-wcpt-table-id="<?php echo $data['id'];?>"
data-wcpt-query-string="<?php echo wcpt_get_table_query_string(); ?>"
data-wcpt-sc-attrs="<?php echo esc_attr( json_encode( $GLOBALS['wcpt_table_data']['query']['sc_attrs'] ) ); ?>"
<?php
$attributes = apply_filters( 'wcpt_container_html_attributes', ob_get_clean() );

?>
<div
	id="wcpt-<?php echo $data['id'];?>"
	class="<?php echo $html_class; ?>"
	<?php echo $attributes; ?>
>
