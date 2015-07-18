<?php
require_once('globals.php');

if(!current_user_can('wpfansubpagemanager')) {
	die('Access Denied');
}

if ( $_POST['addnew'] ) {
	foreach($_POST as $key=>$val) {
		$vars[$key] = $wpdb->escape($val);
	}
	$sql = "INSERT INTO `".$wpdb->prefix."projects` (`title`,`image`,`genre`,`originalwork`,`episodes`,`ann`,`anidb`,`official`,`season`,`status`) VALUES ";
	$sql .= "('".$vars['title']."','".$vars['image']."','".$vars['genre']."','".$vars['originalwork']."','".$vars['episodes']."','".$vars['ann']."','".$vars['anidb']."','".$vars['official']."','".$vars['season']."','".$vars['status']."');";
	$wpdb->query($sql);

	$update = 1;
	$updatemsg = "Project added with success.";
}
if ( $_GET['do'] == "del" ) {
	$query = "DELETE FROM ".$wpdb->prefix."projects WHERE id='".$wpdb->escape($_GET['delid'])."' LIMIT 1;";
	$wpdb->query($query);
	$query = "DELETE FROM ".$wpdb->prefix."releases WHERE pid='".$wpdb->escape($_GET['delid'])."' LIMIT 1;";
	$wpdb->query($query);

	$update = 1;
	$updatemsg = "Project deleted with success and all its releases.";
}
if ( $_POST['edit'] == "complete" ) {
	if ( $_POST['title'] ) $update[] = "`title`='".$wpdb->escape($_POST['title'])."'";
	if ( $_POST['image'] ) $update[] = "`image`='".$wpdb->escape($_POST['image'])."'";
	if ( $_POST['genre'] ) $update[] = "`genre`='".$wpdb->escape($_POST['genre'])."'";
	if ( $_POST['originalwork'] ) $update[] = "`originalwork`='".$wpdb->escape($_POST['originalwork'])."'";
	if ( $_POST['episodes'] ) $update[] = "`episodes`='".$wpdb->escape($_POST['episodes'])."'";
	if ( $_POST['ann'] ) $update[] = "`ann`='".$wpdb->escape($_POST['ann'])."'";
	if ( $_POST['anidb'] ) $update[] = "`anidb`='".$wpdb->escape($_POST['anidb'])."'";
	if ( $_POST['official'] ) $update[] = "`official`='".$wpdb->escape($_POST['official'])."'";
	if ( $_POST['season'] ) $update[] = "`season`='".$wpdb->escape($_POST['season'])."'";
	if ( $_POST['status'] ) $update[] = "`status`='".$wpdb->escape($_POST['status'])."'";

	$query = "UPDATE ".$wpdb->prefix."projects SET ".implode(",", $update)." WHERE id='".$wpdb->escape($_POST['editid'])."' LIMIT 1;";
	$wpdb->query($query);

	$update = 1;
	$updatemsg = "Project updated with success.";
}
if ( $_GET['do'] == "edit" && !$_POST ) {
?>
<div class="wrap">
	<h2><?php _e('WP-FansubPageManager - Editar', 'wpfansubpagemanager' ); ?></h2>
<?php
$row = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."projects WHERE id='".$wpdb->escape($_GET['editid'])."' LIMIT 1;", ARRAY_A);
$row = $row['0'];
?>
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Editar Projecto</td>
	</tr>
	</thead>
	<tbody>
		<form method='post'><input type='hidden' name='do' value='edit' /><input type='hidden' name='edit' value='complete' /><input type='hidden' name='editid' value='<?php echo(stripslashes($row['id'])); ?>' />
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Title:</td><td class="post-title column-title"><input type='text' name='title' style='width:400px;' value='<?php echo(stripslashes($row['title'])); ?>' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Image:</td><td class="post-title column-title"><input type='text' name='image' style='width:400px;' value='<?php echo(stripslashes($row['image'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Genre:</td><td class="post-title column-title"><input type='text' name='genre' style='width:400px;' value='<?php echo(stripslashes($row['genre'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Original Work:</td><td class="post-title column-title"><input type='text' name='originalwork' style='width:400px;' value='<?php echo(stripslashes($row['originalwork'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Episodes:</td><td class="post-title column-title"><input type='text' name='episodes' style='width:400px;' value='<?php echo(stripslashes($row['episodes'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>ANN ID:</td><td class="post-title column-title"><input type='text' name='ann' style='width:400px;' value='<?php echo(stripslashes($row['ann'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>AniDB ID:</td><td class="post-title column-title"><input type='text' name='anidb' style='width:400px;' value='<?php echo(stripslashes($row['anidb'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Oficial Website:</td><td class="post-title column-title"><input type='text' name='official' style='width:400px;' value='<?php echo(stripslashes($row['official'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Season:</td><td class="post-title column-title"><input type='text' name='season' style='width:400px;' value='<?php echo(stripslashes($row['season'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Status:</td><td class="post-title column-title"><?php echo(fnsb_status_opt($row['status'])); ?></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td colspan='2' align='center'><input type='submit' class="button-primary" value='Edit' /></td></tr></form>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Edit Project</td>
	</tr>
	</tfoot>
</table>
</div>
<?php
} else {

	$max_num_rows = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."projects;");
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
	
	
	$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."projects ORDER by title DESC LIMIT ".$limitvalue.", ".$limit, ARRAY_A);
?>
<div class="wrap">
	<h2><?php _e('WP-FansubPageManager - Projects', 'wpfansubpagemanager' ); ?></h2>
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
	<th scope="col" id="title" class="manage-column column-title" style="">Title</th>
	<th scope="col" id="genre" class="manage-column column-author" style="">Genre</th>
	<th scope="col" id="producers" class="manage-column column-categories" style="">Producers</th>
	<th scope="col" id="season" class="manage-column column-tags" style="">Season</th>
	<th scope="col" id="status" class="manage-column column-date" style="">Status</th>
	</tr>
	</thead>
<?php
foreach($rows as $r) {
?>
	<tr id='proj-<?php echo($r['id']); ?>' class='<?php echo(($i%2) ? "" : "alternate "); ?>author-other status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="proj[]" value="<?php echo($r['id']); ?>" /></th>
		<td class="post-title column-title"><strong><a class="row-title" href="#"><?php echo(stripslashes($r['title'])); ?></a></strong>
			<div class="row-actions"><span class='edit'><a href="admin.php?page=<?php echo(FNSB_DIRNAME); ?>/projects.php&do=edit&editid=<?php echo($r['id']); ?>" title="Edit this project">Edit</a> | </span><span class='delete'><a class='submitdelete' title='Delete this project' href='admin.php?page=<?php echo(FNSB_DIRNAME); ?>/projects.php&do=del&delid=<?php echo($r['id']); ?>' onclick="if ( confirm('You are about to delete this project \'<?php echo($wpdb->escape($r['title'])); ?>\' and all of its releases.\n \'Cancel\' to stop, \'OK\' to delete.') ) { return true;}return false;">Delete</a></div>
		</td>
		<td class="author column-author"><?php echo(stripslashes($r['genre'])); ?></td>
		<td class="author column-author"><?php echo(stripslashes($r['originalwork'])); ?></td>
		<td class="author column-author"><?php echo(stripslashes($r['season'])); ?></td>
		<td class="author column-author"<?php echo($fnsb_scolor[$r['status']] ? " style='color:".$fnsb_scolor[$r['status']]."'" : ""); ?>><?php echo($fnsb_status[$r['status']]); ?></td>

	</tr>
<?php
	++$i;
}
?>


	</tbody>

	<tfoot>
	<tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" id="title" class="manage-column column-title" style="">Title</th>
	<th scope="col" id="genre" class="manage-column column-author" style="">Genre</th>
	<th scope="col" id="producers" class="manage-column column-categories" style="">Producers</th>
	<th scope="col" id="season" class="manage-column column-tags" style="">Season</th>
	<th scope="col" id="status" class="manage-column column-date" style="">Status</th>
	</tr>
	</tfoot>

	<tbody>

</table>

<div class="clear"></div>
<br />
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Add New Project</td>
	</tr>
	</thead>
	<tbody>
		<form method='post'><input type='hidden' name='addnew' value='1' />
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Title:</td><td class="post-title column-title"><input type='text' name='title' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Image:</td><td class="post-title column-title"><input type='text' name='image' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Genre:</td><td class="post-title column-title"><input type='text' name='genre' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Producers:</td><td class="post-title column-title"><input type='text' name='originalwork' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Episodes:</td><td class="post-title column-title"><input type='text' name='episodes' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>ANN ID:</td><td class="post-title column-title"><input type='text' name='ann' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>AniDB ID:</td><td class="post-title column-title"><input type='text' name='anidb' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Oficial Website:</td><td class="post-title column-title"><input type='text' name='official' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Season:</td><td class="post-title column-title"><input type='text' name='season' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Status:</td><td class="post-title column-title"><?php echo(fnsb_status_opt()); ?></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td colspan='2' align='center'><input type='submit' class="button-primary" value='Add' /></td></tr></form>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Add New Project</td>
	</tr>
	</tfoot>
</table>
</div>
<?php
}
?>