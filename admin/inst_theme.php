<?php
/**********************************************************************
    Copyright (C) NGICON (Next Generation icon) ERP.
               All Rights Reserved By www.ngicon.com. 






***********************************************************************/
$page_security = 'SA_CREATEMODULES';
$path_to_root="..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root."/includes/packages.inc");
include_once($path_to_root . "/admin/db/maintenance_db.inc");
include_once($path_to_root . "/includes/ui.inc");

if ($SysPrefs->use_popup_windows) {
	$js = get_js_open_window(900, 500);
}
page(_($help_context = "Install Themes"), false, false, '', $js);

//---------------------------------------------------------------------------------------------

if (($id = find_submit('Delete', false)) && isset($installed_extensions[$id])
	&& clean_user_themes($installed_extensions[$id]['package']))
{
	$extensions = get_company_extensions();
	$theme = $extensions[$id]['package'];
	$path = $extensions[$id]['path'];

	if (uninstall_package($theme)) {
		$dirname = $path_to_root.'/'.$path;
		flush_dir($dirname, true);
		rmdir($dirname);
		unset($extensions[$id]);
		if (update_extensions($extensions)) {
			display_notification(_("Selected theme has been successfully deleted"));
			meta_forward($_SERVER['PHP_SELF']);
		}
	}
}

if ($id = find_submit('Update', false))
	install_extension($id);

//---------------------------------------------------------------------------------------------
start_form(true);

	div_start('ext_tbl');
	start_table(TABLESTYLE);

	$th = array(_("Theme"),  _("Installed"), _("Available"), "", "");
	$k = 0;

	$mods = get_themes_list();

	if (!$mods)
		display_note(_("No optional theme is currently available."));
	else
	{
		table_header($th);

		foreach($mods as $pkg_name => $ext)
		{
			$available = @$ext['available'];
			$installed = @$ext['version'];
			$id = @$ext['local_id'];

			alt_table_row_color($k);

			label_cell($available ? get_package_view_str($pkg_name, $ext['name']) : $ext['name']);

			label_cell($id === null ? _("None") :
				($available && $installed ? $installed : _("Unknown")));
			label_cell($available ? $available : _("None"));

			if ($available && check_pkg_upgrade($installed, $available)) // outdated or not installed theme in repo
				button_cell('Update'.$pkg_name, $installed ? _("Update") : _("Install"),
					_('Upload and install latest extension package'), ICON_DOWN, 'process');
			else
				label_cell('');

			if ($id !== null) {
				delete_button_cell('Delete'.$id, _('Delete'));
				submit_js_confirm('Delete'.$id, 
					sprintf(_("You are about to remove package \'%s\'.\nDo you want to continue ?"), 
						$ext['name']));
			} else
				label_cell('');

			end_row();
		}

		end_table(1);
	}
	div_end();

//---------------------------------------------------------------------------------------------
end_form();

end_page();
