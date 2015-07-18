<?php
require_once('globals.php');

if(!current_user_can('wpfansubpagemanager')) {
	die('Acesso Negado');
}

if ( $_POST['addnew'] ) {
	foreach($_POST as $key=>$val) {
		$vars[$key] = $wpdb->escape($val);
	}
	$sql = "INSERT INTO `".$wpdb->prefix."releases` (`pid`,`epi`,`ver`,`tor`,`ddl`,`crc`,`time`) VALUES ";
	$sql .= "('".$vars['pid']."','".$vars['epi']."','".$vars['ver']."','".$vars['tor']."','".$vars['ddl']."','".$vars['crc']."',UNIX_TIMESTAMP());";
	$wpdb->query($sql);

	$update = 1;
	$updatemsg = "Lançamento adicionado com sucesso.";
}
if ( $_GET['do'] == "del" ) {
	$query = "DELETE FROM ".$wpdb->prefix."releases WHERE id='".$wpdb->escape($_GET['delid'])."' LIMIT 1;";
	$wpdb->query($query);

	$update = 1;
	$updatemsg = "Lançamento apagado com sucesso.";
}
if ( $_POST['edit'] == "complete" ) {
	if ( $_POST['pid'] ) $update[] = "`pid`='".$wpdb->escape($_POST['pid'])."'";
	if ( $_POST['epi'] ) $update[] = "`epi`='".$wpdb->escape($_POST['epi'])."'";
	if ( $_POST['ver'] ) $update[] = "`ver`='".$wpdb->escape($_POST['ver'])."'";
	if ( $_POST['tor'] ) $update[] = "`tor`='".$wpdb->escape($_POST['tor'])."'";
	if ( $_POST['ddl'] ) $update[] = "`ddl`='".$wpdb->escape($_POST['ddl'])."'";
	if ( $_POST['crc'] ) $update[] = "`crc`='".$wpdb->escape($_POST['crc'])."'";

	$query = "UPDATE ".$wpdb->prefix."releases SET ".implode(",", $update)." WHERE id='".$wpdb->escape($_POST['editid'])."' LIMIT 1;";
	$wpdb->query($query);

	$update = 1;
	$updatemsg = "Lançamento actualizado com sucesso.";
}
if ( $_GET['do'] == "edit" && !$_POST ) {
?>
<div class="wrap">
	<h2><?php _e('WP-FansubPageManager - Editar', 'wpfansubpagemanager' ); ?></h2>
<?php
$row = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."releases WHERE id='".$wpdb->escape($_GET['editid'])."' LIMIT 1;", ARRAY_A);
$row = $row['0'];
?>
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Editar lançamento</td>
	</tr>
	</thead>
	<tbody>
		<form method='post'><input type='hidden' name='do' value='edit' /><input type='hidden' name='edit' value='complete' /><input type='hidden' name='editid' value='<?php echo(stripslashes($row['id'])); ?>' />
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Projecto:</td><td class="post-title column-title"><select name='pid'>
<?php
$rows = $wpdb->get_results("SELECT id, title FROM ".$wpdb->prefix."projects ORDER by title ASC", ARRAY_A);
foreach($rows as $r) {
	print("<option value='".$r['id']."' ".($r['id'] == $row['pid'] ? "selected" : "").">".$r['title']."</option>");
}
?>
</select></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Episódio:</td><td class="post-title column-title"><input type='text' name='epi' style='width:400px;' value='<?php echo(stripslashes($row['epi'])); ?>' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Versão:</td><td class="post-title column-title"><input type='text' name='ver' style='width:400px;' value='<?php echo(stripslashes($row['ver'])); ?>' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Torrent:</td><td class="post-title column-title"><input type='text' name='tor' style='width:400px;' value='<?php echo(stripslashes($row['tor'])); ?>' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>DDL:</td><td class="post-title column-title"><input type='text' name='ddl' style='width:400px;' value='<?php echo(stripslashes($row['ddl'])); ?>' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>CRC32:</td><td class="post-title column-title"><input type='text' name='crc' style='width:400px;' value='<?php echo(stripslashes($row['crc'])); ?>' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td colspan='2' align='center'><input type='submit' class="button-primary" value='Edit' /></td></tr></form>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Editar Lançamento</td>
	</tr>
	</tfoot>
</table>
</div>
<?php
} else {
	$max_num_rows = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."releases;");
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
	$rows = $wpdb->get_results("SELECT p.*, r.id as rid, r.pid as rpid, r.epi, r.ver, r.tor, r.ddl, r.crc, r.time FROM ".$wpdb->prefix."releases r LEFT JOIN ".$wpdb->prefix."projects p ON(r.pid=p.id) ORDER by `time` DESC LIMIT ".$limitvalue.", ".$limit, ARRAY_A);
?>
<div class="wrap">
	<h2><?php _e('WP-FansubPageManager - Lançamentos', 'wpfansubpagemanager' ); ?></h2>
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
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'A mostrar %s&#8211;%s de %s' ) . '</span>%s',
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
	<th scope="col" id="title" class="manage-column column-title" style="">Título</th>
	<th scope="col" id="episode" class="manage-column column-author" style="">Episódio</th>
	<th scope="col" id="crc32" class="manage-column column-categories" style="">CRC32</th>
	<th scope="col" id="date" class="manage-column column-categories" style="">Adicionado</th>
	<th scope="col" id="misc" class="manage-column column-tags" style="">Misc</th>
	</tr>
	</thead>
<?php
foreach($rows as $r) {
?>
	<tr id='proj-<?php echo($r['id']); ?>' class='<?php echo(($i%2) ? "" : "alternate "); ?>author-other status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="proj[]" value="<?php echo($r['id']); ?>" /></th>
		<td class="post-title column-title"><strong><a class="row-title" href="#"><?php echo(stripslashes($r['title'])); ?></a></strong>
			<div class="row-actions"><span class='edit'><a href="admin.php?page=<?php echo(FNSB_DIRNAME); ?>/releases.php&do=edit&editid=<?php echo($r['rid']); ?>" title="Editar este lançamento">Editar</a> | </span><span class='delete'><a class='submitdelete' title='Apagar este lançamento' href='admin.php?page=<?php echo(FNSB_DIRNAME); ?>/releases.php&do=del&delid=<?php echo($r['rid']); ?>' onclick="if ( confirm('Está prestes a apagar este \'<?php echo($wpdb->escape($r['title'])); ?>\' episódio \'<?php echo($r['epi'].($r['ver'] !== "1" ? "v".$r['ver'] : "")); ?>\'.\n \'Cancelar\' para retroceder, \'OK\' para apagar.') ) { return true;}return false;">Apagar</a></div>
		</td>
		<td class="author column-author"><?php echo(str_pad(stripslashes($r['epi']), strlen($r['episodes']), "0", STR_PAD_LEFT)); ?></td>
		<td class="author column-author"><?php echo(stripslashes($r['crc'])); ?></td>
		<td class="author column-author"><?php echo(date("m-d H:i", $r['time'])); ?></td>
		<td class="author column-author"><a href='<?php echo($r['tor']); ?>'>Torrent</a> - <a href='<?php echo($r['ddl']); ?>'>DDL</a></td>

	</tr>
<?php
	++$i;
}
?>


	</tbody>

	<tfoot>
	<tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" id="title" class="manage-column column-title" style="">Título</th>
	<th scope="col" id="episode" class="manage-column column-author" style="">Episódio</th>
	<th scope="col" id="crc32" class="manage-column column-categories" style="">CRC32</th>
	<th scope="col" id="date" class="manage-column column-categories" style="">Adicionado</th>
	<th scope="col" id="misc" class="manage-column column-tags" style="">Misc</th>
	</tr>
	</tfoot>

	<tbody>

</table>

<div class="clear"></div>
<br />
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Adicionar Novo Lançamento</td>
	</tr>
	</thead>
	<tbody>
		<form method='post'><input type='hidden' name='addnew' value='1' />
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Projecto:</td><td class="post-title column-title"><select name='pid'>
<?php
$rows = $wpdb->get_results("SELECT id, title FROM ".$wpdb->prefix."projects ORDER by title ASC", ARRAY_A);
foreach($rows as $r) {
	print("<option value='".$r['id']."'>".$r['title']."</option>");
}
?>
</select></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Episódio:</td><td class="post-title column-title"><input type='text' name='epi' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Versão:</td><td class="post-title column-title"><input type='text' name='ver' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Torrent:</td><td class="post-title column-title"><input type='text' name='tor' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>DDL:</td><td class="post-title column-title"><input type='text' name='ddl' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>CRC32:</td><td class="post-title column-title"><input type='text' name='crc' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td colspan='2' align='center'><input type='submit' class="button-primary" value='Add' /></td></tr></form>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Adicionar Novo Lançamento</td>
	</tr>
	</tfoot>
</table>
</div>
<?php
}
?>