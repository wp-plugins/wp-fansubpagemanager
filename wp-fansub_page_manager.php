<?php
/*
Plugin Name: WP-FansubPageManager
Plugin URI: To be added
Description: Integration which allows you to manage fansub projects in wordpress.
Version: 1.0.4
Author: DrX & Leinad4Mind
Copyright 2009  DrX
Copyright 2014-2015  Leinad4Mind
*/

require_once('globals.php');

function fnsbmenu()
{

	if ( function_exists("add_object_page") ) {
		add_object_page(FNSB_NAME, FNSB_NAME, 2, FNSB_BASE);
	} else {
		add_menu_page(FNSB_NAME, FNSB_NAME, 2, FNSB_BASE);
	}
	add_submenu_page(FNSB_BASE, FNSB_SETTINGS, FNSB_SETTINGS, 7, FNSB_BASE);
	add_submenu_page(FNSB_BASE, FNSB_PROJECTS, FNSB_PROJECTS, 7, FNSB_DIRNAME.'/projects.php');
	add_submenu_page(FNSB_BASE, FNSB_RELEASES, FNSB_RELEASES, 7, FNSB_DIRNAME.'/releases.php');
	add_submenu_page(FNSB_BASE, FNSB_XDCC, FNSB_XDCC, 7, FNSB_DIRNAME.'/xdcc.php');
	add_submenu_page(FNSB_BASE, FNSB_SCRAPE, FNSB_SCRAPE, 7, FNSB_DIRNAME.'/scrape.php');
}
function fnsb_activation() {
	global $wpdb;
	wp_schedule_event(time(), 'hourly', 'my_hourly_event');

	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."projects` (";
	$sql .= "  `id` int(10) NOT NULL auto_increment,";
	$sql .= "  `title` varchar(255) NOT NULL,";
	$sql .= "  `image` varchar(255) NOT NULL,";
	$sql .= "  `genre` varchar(255) NOT NULL,";
	$sql .= "  `originalwork` varchar(255) NOT NULL,";
	$sql .= "  `episodes` int(10) NOT NULL,";
	$sql .= "  `ann` int(10) NOT NULL,";
	$sql .= "  `anidb` int(10) NOT NULL,";
	$sql .= "  `official` text NOT NULL,";
	$sql .= "  `season` text NOT NULL,";
	$sql .= "  `status` int(1) NOT NULL,";
	$sql .= "  PRIMARY KEY  (`id`)";
	$sql .= ") ENGINE=MyISAM ;";
		$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."releases` (";
	$sql .= "  `id` int(10) NOT NULL auto_increment,";
	$sql .= "  `pid` int(10) NOT NULL,";
	$sql .= "  `epi` int(10) NOT NULL,";
	$sql .= "  `ver` int(10) NOT NULL default '1',";
	$sql .= "  `tor` varchar(255) NOT NULL,";
	$sql .= "  `ddl` varchar(255) NOT NULL,";
	$sql .= "  `crc` varchar(8) NOT NULL,";
	$sql .= "  `time` int(10) NOT NULL,";
	$sql .= "  PRIMARY KEY  (`id`)";
	$sql .= ") ENGINE=MyISAM ;";
		$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."nyaa` (";
	$sql .= "  `crc` varchar(255) NOT NULL,";
	$sql .= "  `seeders` int(10) NOT NULL,";
	$sql .= "  `leechers` int(10) NOT NULL,";
	$sql .= "  `completed` int(10) NOT NULL,";
	$sql .= "  PRIMARY KEY  (`crc`),";
	$sql .= "  UNIQUE KEY `crc` (`crc`)";
	$sql .= ") ENGINE=MyISAM ;";
		$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."xdcc_bot` (";
	$sql .= "  `id` int(10) NOT NULL auto_increment,";
	$sql .= "  `nick` varchar(255) NOT NULL,";
	$sql .= "  `url` text NOT NULL,";
	$sql .= "  UNIQUE KEY `id` (`id`,`nick`)";
	$sql .= ") ENGINE=MyISAM ;";
		$wpdb->query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."xdcc_list` (";
	$sql .= "  `id` int(10) NOT NULL,";
	$sql .= "  `nick` varchar(255) NOT NULL,";
	$sql .= "  `crc` varchar(8) NOT NULL,";
	$sql .= "  UNIQUE KEY `id` (`id`,`nick`)";
	$sql .= ") ENGINE=MyISAM ;";
		$wpdb->query($sql);

	add_option('fnsb_nyaa', "0", 'fnsb_nyaa', 'yes');
	add_option('fnsb_nyaa_id', "1234", 'fnsb_nyaa_id', 'yes');
	add_option('fnsb_xdcc', "0", 'fnsb_xdcc', 'yes');
}
function fnsb_deactivation() {
	global $wpdb;
	wp_clear_scheduled_hook('my_hourly_event');

	$sql = "DROP TABLE IF EXISTS `".$wpdb->prefix."projects`;";
		$wpdb->query($sql);

	$sql = "DROP TABLE IF EXISTS `".$wpdb->prefix."releases`;";
		$wpdb->query($sql);

	$sql = "DROP TABLE IF EXISTS `".$wpdb->prefix."nyaa`;";
		$wpdb->query($sql);

	$sql = "DROP TABLE IF EXISTS `".$wpdb->prefix."xdcc_bot`;";
		$wpdb->query($sql);

	$sql = "DROP TABLE IF EXISTS `".$wpdb->prefix."xdcc_list`;";
		$wpdb->query($sql);

	delete_option('fnsb_nyaa');
	delete_option('fnsb_nyaa_id');
	delete_option('fnsb_xdcc');
}
function fnsbpage( $content ) {
	global $wpdb, $fnsb_scolor, $fnsb_status;
	if(false === strpos($content, '<!--fansubs-->')) {
		return $content;
	}
	$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."projects ORDER by id DESC, title ASC", ARRAY_A);
	foreach($rows as $r) {
		$projects[$r['id']] = $r;
	}
	$rows = $wpdb->get_results("SELECT p.id, r.id as rid, r.pid, r.epi, r.ver, r.tor, r.ddl, r.crc, r.time FROM ".$wpdb->prefix."releases r LEFT JOIN ".$wpdb->prefix."projects p ON(r.pid=p.id) ORDER by p.title ASC, r.epi ASC", ARRAY_A);
	foreach($rows as $r) {
		$releases[$r['id']][$r['rid']] = $r;
	}

	if ( get_option('fnsb_xdcc') ) {
		$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xdcc_list ORDER by crc ASC, nick DESC", ARRAY_A);
		foreach($rows as $r) {
			$xdcc[$r['crc']][$r['nick']] = "<tr><td><b>".$r['nick'].":</b></td><td><input type='text' value='/msg ".$r['nick']." xdcc send #".$r['id']."' style='width:400px;' onClick=\"select();\" readonly /></td></tr>";
		}
		
	}
	if ( get_option('fnsb_nyaa') ) {
		$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."nyaa ORDER by crc ASC", ARRAY_A);
		foreach($rows as $r) {
			$torr[$r['crc']]['s'] = $r['seeders'];
			$torr[$r['crc']]['l'] = $r['leechers'];
			$torr[$r['crc']]['c'] = $r['completed'];
		}
		
	}

	if ( get_option('fnsb_nyaa') && get_option('fnsb_xdcc') ) $mcolspan = 9;
	else if ( get_option('fnsb_nyaa') ) $mcolspan = 8;
	else if ( get_option('fnsb_xdcc') ) $mcolspan = 6;
	else $mcolspan = 5;

	foreach($projects as $key => $data) {
		$comp_episodes = $data['status'] == "5" ? $data['episodes'] : count($releases[$key]);
		$newcont .= "<div>";
		$newcont .= "<p><img src='".$data['image']."' align='left' class='proj_img' />";
		$newcont .= "<b>".stripslashes($data['title'])."</b><br />";
		$newcont .= "<b>Género: </b>".stripslashes($data['genre'])."<br />";
		$newcont .= "<b>Estúdio: </b>".stripslashes($data['originalwork'])."<br />";
		$newcont .= "<b>Temporada: </b>".stripslashes($data['season'])."<br />";
		$newcont .= "<b>Episódios: </b> ".$comp_episodes."/".$data['episodes']."<br />";
		$newcont .= "<b>Estado: </b><span ".($fnsb_scolor[$data['status']] ? " style='color:".$fnsb_scolor[$data['status']]."'" : "").">".$fnsb_status[$data['status']]."</span><br />";
		$newcont .= "<a href='".$data['official']."'>Site Oficial</a> - <a href='http://anidb.net/a".$data['anidb']."'>AniDB</a> - <a href='http://www.animenewsnetwork.com/encyclopedia/anime.php?id=".$data['ann']."'>ANN</a><br />";
		$newcont .= "</p>";
		$newcont .= "</div>";
		$newcont .= "<div class=\"wpfnsbclear\"></div>";
		if ( is_array($releases[$key]) && $data['status'] !== "3" ) {
			$newcont .= "<div>";
			$newcont .= "<table class=\"releases\">";
			$newcont .= "<thead>";
			$newcont .= "<tr><th colspan='".$mcolspan."' class='title'>Lançamentos de ".stripslashes($data['title'])."</td></tr>";
			$newcont .= "<tr><th class='cols'>Episódio</th class='cols'><th class='cols'>CRC32</th><th class='cols'>Data</th><th class='cols'>Torrent</th>";
			if ( get_option('fnsb_nyaa') ) $newcont .= "<th class='cols'>S</th><th class='cols'>L</th><th class='cols'>C</th>";
			if ( get_option('fnsb_xdcc') ) $newcont .= "<th class='cols'>XDCC</th>";
			$newcont .= "<th class='cols'>DDL</th></tr></thead>";
			$newcont .= "<tbody>";
			foreach($releases[$key] as $epi) {
				$epinum = $epi['ver'] == "1" ? "" : "v".$epi['ver'];
				$newcont .= "<tr class='".($epi['epi'] % 2 ? "normal" : "alternate")."'>";
				$newcont .= "<td>".str_pad(stripslashes($epi['epi']), strlen($data['episodes']), "0", STR_PAD_LEFT).$epinum."</td>";
				$newcont .= "<td>".$epi['crc']."</td>";
				$newcont .= "<td>".date("m-d H:i", $epi['time'])."</td>";
				$epi['torh'] = $epi['tor'] ? "<td><a href='".$epi['tor']."'>TORRENT</a></td>" : "<td colspan='4' align='center'><b>N/A</b></td>";
				$newcont .= $epi['torh'];
				if ( get_option('fnsb_nyaa') && $epi['tor'] && is_array($torr[$epi['crc']]) ) {
					$newcont .= "<td>".$torr[$epi['crc']]['s']."</td>";
					$newcont .= "<td>".$torr[$epi['crc']]['l']."</td>";
					$newcont .= "<td>".$torr[$epi['crc']]['c']."</td>";
				} else $newcont .= "<td colspan='3' align='center'><b>N/A</b></td>";
				if ( get_option('fnsb_xdcc') ) $newcont .= "<td><a onClick=\"javascript:showHide('xdcc_p".$key."_r".$epi['rid']."');\">XDCC</a></td>";
				$newcont .= "<td><a href='".$epi['ddl']."'>DDL</a></td>";
				$newcont .= "</tr>";
				if ( get_option('fnsb_xdcc') ) {
					$newcont .= "<tr class='xdcc' style='display:none;' id='xdcc_p".$key."_r".$epi['rid']."'><td colspan='".$mcolspan."' align='center'>";
					$newcont .= "<table width='100%;' class='xdcclist'>".(is_array($xdcc[$epi['crc']]) ? implode("", $xdcc[$epi['crc']]) : "Nenhum Bot XDCC tem este pack.")."</table>";
					$newcont .= "</td></tr>";
				}
			}
			$newcont .= "</tbody>";
			$newcont .= "</table>";
			$newcont .= "</div>";
			$newcont .= "<div class=\"wpfnsbclear\"></div>";
		}
	}
	return $newcont;
}
function fnsb_widget($args) {
	global $wpdb;
	extract($args);
	print($before_widget);
	print($before_title.FNSB_RELEASES.$after_title);
	$rows = $wpdb->get_results("SELECT p.id, p.title, p.status, r.id as rid, r.pid, r.epi, r.ver, r.tor, r.ddl, r.crc, r.time FROM ".$wpdb->prefix."releases r LEFT JOIN ".$wpdb->prefix."projects p ON(r.pid=p.id) WHERE p.status != '3' ORDER by r.time DESC LIMIT 10", ARRAY_A);
	print("<ul>");
	foreach($rows as $r) {
		if ( $r['tor'] && $r['ddl'] ) {
			print("<li><a href='".$r['tor']."'>".$r['title']." - ".$r['epi']."</a> (<a href='".$r['ddl']."'>DDL</a>)</li>");
		} else if ( $r['tor'] && !$r['ddl'] ) {
			print("<li><a href='".$r['tor']."'>".$r['title']." - ".$r['epi']."</a></li>");
		} else if ( !$r['tor'] && $r['ddl'] ) {
			print("<li>".$r['title']." - ".$r['epi']." (<a href='".$r['ddl']."'>DDL</a>)</li>");
		} else if ( !$r['tor'] && !$r['ddl'] ) {
			print("<li>".$r['title']." - ".$r['epi']."</li>");
		}
	}
	print("</ul>");
	print($after_widget);
}
 
function fnsb_widgit_init()
{
  register_sidebar_widget(__('WP-FansubPageManager'), 'fnsb_widget');
}

function fnsbcss() {
	$fnsbUrl = WP_PLUGIN_URL . '/wp-fansub_page_manager/wp-fansub_page_manager.css';
	$fnsbFile = WP_PLUGIN_DIR . '/wp-fansub_page_manager/wp-fansub_page_manager.css';
	if ( file_exists($fnsbFile) ) {
		wp_register_style('wp-fansub_page_manager', $fnsbUrl);
		wp_enqueue_style( 'wp-fansub_page_manager');
	}
}
function fnsbjs() {
	$fnsbUrl = WP_PLUGIN_URL . '/wp-fansub_page_manager/wp-fansub_page_manager.js';
	$fnsbFile = WP_PLUGIN_DIR . '/wp-fansub_page_manager/wp-fansub_page_manager.js';
	if ( file_exists($fnsbFile) ) {
		wp_register_script('wp-fansub_page_manager', $fnsbUrl);
		wp_enqueue_script( 'wp-fansub_page_manager');
	}
}
function fnsb_hourly_scrape() {
	global $wpdb;
	$rows = $wpdb->get_results("SELECT crc FROM ".$wpdb->prefix."releases ORDER by id ASC;", ARRAY_A);
	foreach($rows as $r) {
		$crc[] = $r['crc'];
	}

	if ( get_option('fnsb_nyaa') ) {
		$nids = explode(",", get_option('fnsb_nyaa_id'));
		foreach($nids as $nid) {
			$data .= file_get_contents("http://www.nyaa.se/?page=separate&user=".$nid);
		}
		preg_match_all("'<td class=\"center\">([0-9a-fA-F]{8})</td>\s*<td class=\"number_?[a-zA-Z]*\">([0-9]*)</td>\s*<td class=\"number_?[a-zA-Z]*\">([0-9]*)</td>\s*<td class=\"number_?[a-zA-Z]*\">([0-9]*)</td>'", $data, $matches);
		unset($matches['0']);
		for($i=0;$i<count($matches['1']);$i++) {
			if ( in_array($matches['1'][$i], $crc) ) {
				$inserts[] = "('".$matches['1'][$i]."','".$matches['2'][$i]."','".$matches['3'][$i]."','".$matches['4'][$i]."')";
			}
		}
		$wpdb->query("DELETE FROM ".$wpdb->prefix."nyaa;");
		$wpdb->query("INSERT INTO ".$wpdb->prefix."nyaa (`crc`,`seeders`,`leechers`,`completed`) VALUES ".implode(",", $inserts).";");
	}
	if ( get_option('fnsb_xdcc') ) {
		$crc = implode("|", $crc);
		$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."xdcc_bot ORDER by id ASC;", ARRAY_A);
		foreach($rows as $r) {
			$ch = curl_init();
			curl_setopt_array($ch, array( CURLOPT_URL => $r['url'], CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => FALSE ));
			if(!($xdcc[$r['nick']] = curl_exec($ch))) {
				continue;
			}
		}
		foreach($xdcc as $nick=>$packlist) {
			preg_match_all("/#(\d+)\s+\d+x\s+\[.*?\d+\.?\d+?\D\]\s+.*\[(".$crc.")\].*\W/mi",$packlist,$packs[$nick]);
		}
		foreach($packs as $nick=>$pack) {
			for($i=0;$i<count($pack['1']);$i++) {
				$insert[] = "('".$pack['1'][$i]."','".$nick."','".$pack['2'][$i]."')";
			}
		}
		$wpdb->query("DELETE FROM ".$wpdb->prefix."xdcc_list;");
		$wpdb->query("INSERT INTO ".$wpdb->prefix."xdcc_list (`id`,`nick`,`crc`) VALUES ".implode(",", $insert).";");
	}
}

function fnsb_admin_post() {
	global $postdata, $id, $post, $wpdb;
	$rows = $wpdb->get_results("SELECT p.id, p.title, p.episodes, r.id as rid, r.epi, r.ver, r.crc FROM ".$wpdb->prefix."releases r LEFT JOIN ".$wpdb->prefix."projects p ON(r.pid=p.id) ORDER by `time` DESC", ARRAY_A);

	$fnsbrel = get_post_meta($post->ID, '_fnsbrel', TRUE);
	$fnsbrela = explode(",", $fnsbrel);
	
?>
<div id="advanced-sortables" class="meta-box-sortables"><div id="fnsb" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span>WP-FansubPageManager</span></h3>

<div class="inside">
		<table style="margin-bottom:40px">
		<tr>
		<th scope="row" style="text-align:right;">Releases:</th><td>
<?php
	if ( $fnsbrel ) {
		foreach($fnsbrela as $rel) {
			$rels[] = "<select name='fnsbrel[]'>".fnsbrel2opts($rows, $rel)."</select>";
		}
	}
	for($i=0;$i<5;$i++) {
		$rels[] = "<select name='fnsbrel[]'>".fnsbrel2opts($rows)."</select>";
	}
	$rels = implode("</td></tr><tr><td></td><td>", $rels);
	print($rels);
?>
		</td>
		</tr>
		</table>
	</div>
</div>
</div>
<?php
}
function fnsb_admin_post_update($id) {
	global $postdata;

	if ( $_POST['fnsbrel'] ) {
		foreach($_POST['fnsbrel'] as $rel) {
			if ( $rel ) $rels[] = $rel;
		}
		delete_post_meta($id, '_fnsbrel');
	
		$fnsbrel = is_array($rels) ? implode(",", $rels) : '';
	
		if ($fnsbrel) {
			add_post_meta($id, '_fnsbrel', $fnsbrel);
		} else {
			delete_post_meta($id, '_fnsbrel');
		}
	}
}
function fnsb_post($content) {
	global $postdata, $id, $post, $wpdb;
	$output = $content;
	$fnsbrel = get_post_meta($post->ID, '_fnsbrel', TRUE);
	if ( $fnsbrel ) {
		$rows = $wpdb->get_results("SELECT p.id, p.title, p.episodes, r.id as rid, r.epi, r.ver, r.crc, r.ddl, r.tor FROM ".$wpdb->prefix."releases r LEFT JOIN ".$wpdb->prefix."projects p ON(r.pid=p.id) WHERE r.id IN (".$wpdb->escape($fnsbrel).");", ARRAY_A);

		$output .= "<div class='wpfnsbclear'></div><div><ul>";
		foreach($rows as $r) {
			$output .= "<li><a href='".$r['tor']."'>".stripslashes($r['title'])." - ".str_pad($r['epi'], strlen($r['episodes']), "0", STR_PAD_LEFT).($r['ver'] == "1" ? "" : "v".$r['ver'])."</a> (<a href='".$r['ddl']."'>DDL</a>)</li>";
		}
		$output .= "</ul></div>";
	}
	return $output;
}

register_activation_hook(__FILE__, 'fnsb_activation');
register_deactivation_hook(__FILE__, 'fnsb_deactivation');

add_action('admin_menu', 'fnsbmenu');
add_action('init', 'fnsbjs');
add_action('wp_print_styles', 'fnsbcss');
add_filter('the_content', 'fnsbpage', 7);
add_action("plugins_loaded", "fnsb_widgit_init");
add_action('my_hourly_event', 'fnsb_hourly_scrape');
add_action('edit_form_advanced', 'fnsb_admin_post');
add_action('save_post', 'fnsb_admin_post_update');
add_action('edit_post', 'fnsb_admin_post_update');
add_action('publish_post', 'fnsb_admin_post_update');
add_filter('the_content', 'fnsb_post');

$role = get_role('administrator');
if(!$role->has_cap('wpfansubpagemanager')) {
	$role->add_cap('wpfansubpagemanager');
}
?>