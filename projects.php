<?php
require_once('globals.php');

if(!current_user_can('wpfansubpagemanager')) {
	die('Acesso Negado');
}

if ( $_POST['addnew'] ) {
	foreach($_POST as $key=>$val) {
		$vars[$key] = $wpdb->escape($val);
	}
	$sql = "INSERT INTO `".$wpdb->prefix."projects` (`title`,`image`,`genre`,`originalwork`,`episodes`,`ann`,`anidb`,`official`,`season`,`status`) VALUES ";
	$sql .= "('".$vars['title']."','".$vars['image']."','".$vars['genre']."','".$vars['originalwork']."','".$vars['episodes']."','".$vars['ann']."','".$vars['anidb']."','".$vars['official']."','".$vars['season']."','".$vars['status']."');";
	$wpdb->query($sql);

	$update = 1;
	$updatemsg = "Projecto adicionado com sucesso.";
}
if ( $_GET['do'] == "del" ) {
	$query = "DELETE FROM ".$wpdb->prefix."projects WHERE id='".$wpdb->escape($_GET['delid'])."' LIMIT 1;";
	$wpdb->query($query);
	$query = "DELETE FROM ".$wpdb->prefix."releases WHERE pid='".$wpdb->escape($_GET['delid'])."' LIMIT 1;";
	$wpdb->query($query);

	$update = 1;
	$updatemsg = "Projecto apagado com sucesso e todos os seus lançamentos.";
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
	$updatemsg = "Projecto actualizado com sucesso.";
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
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Título:</td><td class="post-title column-title"><input type='text' name='title' style='width:400px;' value='<?php echo(stripslashes($row['title'])); ?>' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Imagem:</td><td class="post-title column-title"><input type='text' name='image' style='width:400px;' value='<?php echo(stripslashes($row['image'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Género:</td><td class="post-title column-title"><input type='text' name='genre' style='width:400px;' value='<?php echo(stripslashes($row['genre'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Estúdio:</td><td class="post-title column-title"><input type='text' name='originalwork' style='width:400px;' value='<?php echo(stripslashes($row['originalwork'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Episódios:</td><td class="post-title column-title"><input type='text' name='episodes' style='width:400px;' value='<?php echo(stripslashes($row['episodes'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>ANN ID:</td><td class="post-title column-title"><input type='text' name='ann' style='width:400px;' value='<?php echo(stripslashes($row['ann'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>AniDB ID:</td><td class="post-title column-title"><input type='text' name='anidb' style='width:400px;' value='<?php echo(stripslashes($row['anidb'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Site Oficial:</td><td class="post-title column-title"><input type='text' name='official' style='width:400px;' value='<?php echo(stripslashes($row['official'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Temporada:</td><td class="post-title column-title"><input type='text' name='season' style='width:400px;' value='<?php echo(stripslashes($row['season'])); ?>'  /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Estado:</td><td class="post-title column-title"><?php echo(fnsb_status_opt($row['status'])); ?></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td colspan='2' align='center'><input type='submit' class="button-primary" value='Editar' /></td></tr></form>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Editar Projecto</td>
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
	<h2><?php _e('WP-FansubPageManager - Projectos', 'wpfansubpagemanager' ); ?></h2>
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
	<th scope="col" id="genre" class="manage-column column-author" style="">Género</th>
	<th scope="col" id="producers" class="manage-column column-categories" style="">Estúdio</th>
	<th scope="col" id="season" class="manage-column column-tags" style="">Temporada</th>
	<th scope="col" id="status" class="manage-column column-date" style="">Estado</th>
	</tr>
	</thead>
<?php
foreach($rows as $r) {
?>
	<tr id='proj-<?php echo($r['id']); ?>' class='<?php echo(($i%2) ? "" : "alternate "); ?>author-other status-publish iedit' valign="top">
		<th scope="row" class="check-column"><input type="checkbox" name="proj[]" value="<?php echo($r['id']); ?>" /></th>
		<td class="post-title column-title"><strong><a class="row-title" href="#"><?php echo(stripslashes($r['title'])); ?></a></strong>
			<div class="row-actions"><span class='edit'><a href="admin.php?page=<?php echo(FNSB_DIRNAME); ?>/projects.php&do=edit&editid=<?php echo($r['id']); ?>" title="Editar este projecto">Editar</a> | </span><span class='delete'><a class='submitdelete' title='Apagar este projecto' href='admin.php?page=<?php echo(FNSB_DIRNAME); ?>/projects.php&do=del&delid=<?php echo($r['id']); ?>' onclick="if ( confirm('Está prestes a apagar este projecto \'<?php echo($wpdb->escape($r['title'])); ?>\' e todos os seus lançamentos.\n \'Cancelar\' para retroceder, \'OK\' para apagar.') ) { return true;}return false;">Apagar</a></div>
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
	<th scope="col" id="title" class="manage-column column-title" style="">Título</th>
	<th scope="col" id="genre" class="manage-column column-author" style="">Género</th>
	<th scope="col" id="producers" class="manage-column column-categories" style="">Estúdio</th>
	<th scope="col" id="season" class="manage-column column-tags" style="">Temporada</th>
	<th scope="col" id="status" class="manage-column column-date" style="">Estado</th>
	</tr>
	</tfoot>

	<tbody>

</table>

<div class="clear"></div>
<br />
<table class="widefat post fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Adicionar Novo Projecto</td>
	</tr>
	</thead>
	<tbody>
		<form method='post'><input type='hidden' name='addnew' value='1' />
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Título:</td><td class="post-title column-title"><input type='text' name='title' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Imagem:</td><td class="post-title column-title"><input type='text' name='image' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Género:</td><td class="post-title column-title"><input type='text' name='genre' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Estúdio:</td><td class="post-title column-title"><input type='text' name='originalwork' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Episódios:</td><td class="post-title column-title"><input type='text' name='episodes' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>ANN ID:</td><td class="post-title column-title"><input type='text' name='ann' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>AniDB ID:</td><td class="post-title column-title"><input type='text' name='anidb' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Site Oficial:</td><td class="post-title column-title"><input type='text' name='official' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Temporada:</td><td class="post-title column-title"><input type='text' name='season' style='width:400px;' /></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td style='width:100px;'>Estado:</td><td class="post-title column-title"><?php echo(fnsb_status_opt()); ?></td></tr>
		<tr id='addnew' class='author-other status-publish iedit' valign="top"><td colspan='2' align='center'><input type='submit' class="button-primary" value='Adicionar' /></td></tr></form>
	<tfoot>
	<tr>
	<th scope="col" id="title" class="manage-column column-title" colspan="2">Adicionar Novo Projecto</td>
	</tr>
	</tfoot>
</table>
</div>
<?php
}
?>