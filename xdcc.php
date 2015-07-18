<?php
require_once('globals.php');

if(!current_user_can('wpfansubpagemanager')) {
	die('Access Denied');
}

if ( $_POST['addnew'] ) {
	foreach($_POST as $key=>$val) {
		$vars[$key] = $wpdb->escape($val);
	}
	$sql = "INSERT INTO `".$wpdb->prefix."xdcc_bot` (`nick`,`url`) VALUES ";
	$sql .= "('".$vars['nick']."','".$vars['url']."');";
	$wpdb->query($sql);

	$update = 1;
	$updatemsg = "XDCC Bot added with success.";
}
if ( $_GET['do'] == "del" ) {
	$query = "DELETE FROM ".$wpdb->prefix."xdcc_bot WHERE id='".$wpdb->escape($_GET['delid'])."' LIMIT 1;";
	$wpdb->query($query);

	$update = 1;
	$updatemsg = "XDCC Bot deleted with success.";
}

$max_num_rows = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."xdcc_bot;");
$limit = 15;
$max_num_pages = ceil(($max_num_rows/$limit));
if ( !isset( $_GET['paged'] ) )
	$_GET['paged'] = 1;

$page = $_GET['paged'];
$limitvalue = $page * $limit - ($limit);

$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $max_num_pages,
	'current' => $_GET['paged']
));
$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xdcc_bot ORDER by id ASC LIMIT ".$limitvalue.", ".$limit, ARRAY_A);
?>
<div class="wrap">
	<h2><?php _e('WP-FansubPageManager - XDCC Bots', 'wpfansubpagemanager' ); ?></h2>
<?php
if ( $error ) {
?>
<div class='error'><p><b><?php echo($errormsg); ?></b></p></div>
<?php
}
if ( $update ) {
?>
<div class='updated'><p><b><?php echo($updatemsg); ?></b></p></div>
<?php
}
?>
<div class="tablenav">

<?php if ( $page_links ) { ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
	number_format_i18n( ( $_GET['paged'] - 1 ) * $limit + 1 ),
	number_format_i18n( min( $_GET['paged'] * $limit, $max_num_rows ) ),
	number_format_i18n( $max_num_rows ),
	$page_links
); echo $page_links_text; ?></div>
<?php
}
?>
<div class="clear"></div>
</div>

<div class="clear"></div>
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" id="bot" class="manage-column column-title" style="">Bot</th>
	<th scope="col" id="packlist" class="manage-column column-author" style="">Packlist</th>
	</tr>
	</thead>
<?php
foreach($rows as $r) {
?>
	<tr id='proj-<?php echo($r['id']); ?>' class='<?php echo(($i%2) ? "" : "alternate "); ?>author-other status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="proj[]" value="<?php echo($r['id']); ?>" /></th>
		<td class="post-title column-title"><strong><a class="row-title" href="#"><?php echo(stripslashes($r['nick'])); ?></a></strong>
			<div class="row-actions"><span class='edit'><span class='delete'><a class='submitdelete' title='Delete this bot' href='admin.php?page=<?php echo(FNSB_DIRNAME); ?>/xdcc.php&do=del&delid=<?php echo($r['id']); ?>' onclick="if ( confirm('You are about to delete this bot \'<?php echo($r['nick']); ?>\'\n \'Cancel\' to stop, \'OK\' to delete.') ) { return true;}return false;">Delete</a></div>
		</td>
		<td class="author column-author"><a href='<?php echo($r['url']); ?>'>Packlist</a></td>

	</tr>
<?php
	++$i;
}
?>


	</tbody>

	<tfoot>
	<tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" id="bot" class="manage-column column-title" style="">Bot</th>
	<th scope="col" id="packlist" class="manage-column column-author" style="">Packlist</th>
	</tr>
	</tfoot>

	<tbody>

</table>

<div class="clear"></div>
<br />
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Add New Bot</td>
	</tr>
	</thead>
	<tbody>
		<form method='post'><input type='hidden' name='addnew' value='1' />

		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Bot Nick:</td><td class="post-title column-title"><input type='text' name='nick' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Packlist URL:</td><td class="post-title column-title"><input type='text' name='url' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td colspan='2' align='center'><input type='submit' class="button-primary" value='Add' /></td></tr></form>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Add New Bot</td>
	</tr>
	</tfoot>
</table>
</div>