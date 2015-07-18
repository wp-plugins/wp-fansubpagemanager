<?php
require_once('globals.php');

if(!current_user_can('wpfansubpagemanager')) {
	die('Access Denied');
}
if ( $_POST ) {
	if ( $_POST['nyaa'] ) update_option('fnsb_nyaa', ($_POST['nyaa'] == "2" ? "0" : "1"));
	if ( $_POST['nyaa_id'] ) update_option('fnsb_nyaa_id', $_POST['nyaa_id']);
	if ( $_POST['xdcc'] ) update_option('fnsb_xdcc', ($_POST['xdcc'] == "2" ? "0" : "1"));
?>
<div class="updated"><p><strong><?php _e('Settings Saved', 'wpfansubpagemanager' ); ?></strong></p></div>
<?
}
if ( !$_GET['do'] ) {
?>

<div class="wrap">
	<h2><?php _e('WP-FansubPageManager - Settings', 'wpfansubpagemanager' ); ?></h2>


<form method='post'>
<div id="poststuff" class="metabox-holder has-right-sidebar">

<div id="side-info-column" class="inner-sidebar">
<div id='side-sortables' class='meta-box-sortables'>
<div id="linksubmitdiv" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span>Save</span></h3>
<div class="inside">
<div class="submitbox" id="submitlink">

<div id="minor-publishing">

<div style="display:none;">
<input type="submit" name="save" value="Save" />

</div>

<div id="minor-publishing-actions">
<div id="preview-action">
</div>
<div class="clear"></div>
</div>

<div id="misc-publishing-actions">
<div class="misc-pub-section misc-pub-section-last">
	Update Settings of WP-FansubPageManager 
</div>
</div>

</div>

<div id="major-publishing-actions">
<div id="delete-action">
</div>

<div id="publishing-action">
	<input name="save" type="submit" class="button-primary" id="publish" tabindex="4" accesskey="p" value="Save" />
</div>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
</div>
</div>
</div></div>

<div id="post-body">
<div id="post-body-content">
<div id="namediv" class="stuffbox">
<h3><label for="nyaa">Activate Nyaa</label></h3>
<div class="inside">
	<select name="nyaa" id="nyaa"><option value='1' <?php if ( get_option('fnsb_nyaa') ) print("selected"); ?>>Yes</option><option value='2' <?php if ( !get_option('fnsb_nyaa') ) print("selected"); ?>>No</option></select>
</div>
</div>
<div id="namediv" class="stuffbox">
<h3><label for="nyaa_id">Nyaa ID Account</label></h3>
<div class="inside">
	<input type="text" name="nyaa_id" id='nyaa_id' size="30" tabindex="1" value="<?php echo get_option('fnsb_nyaa_id'); ?>" />
    <p>Example: 1234,12345,123456... (You can put more than one ID separated by comma)</p>
</div>
</div>
<div id="namediv" class="stuffbox">
<h3><label for="xdcc">Activate XDCC</label></h3>
<div class="inside">
	<select name="xdcc" id="xdcc"><option value='1' <?php if ( get_option('fnsb_xdcc') ) print("selected"); ?>>Yes</option><option value='2' <?php if ( !get_option('fnsb_xdcc') ) print("selected"); ?>>No</option></select>
</div>
</div>

<div id='advanced-sortables' class='meta-box-sortables'>
</div>

</div>
</div>
</div>

</form>
</div>

</div>
<?php
}
?>