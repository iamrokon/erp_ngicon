<?php
/**********************************************************************
    Copyright (C) NGICON (Next Generation icon) ERP.
	




   
 All Rights Reserved By www.ngicon.com
***********************************************************************/
if (!isset($path_to_root) || isset($_GET['path_to_root']) || isset($_POST['path_to_root']))
	die("Restricted access");
	include_once($path_to_root . '/applications/application.php');
    include_once($path_to_root . '/applications/customers.php');	
	include_once($path_to_root . '/applications/inventory.php');
	include_once($path_to_root . '/applications/generalledger.php');
	include_once($path_to_root . '/applications/setup.php');
	include_once($path_to_root . '/installed_extensions.php');

	class front_accounting
	{
		var $user;
		var $settings;
		var $applications;
		var $selected_application;

		var $menu;

		function add_application($app)
		{	
			if ($app->enabled) // skip inactive modules
				$this->applications[$app->id] = $app;
		}
		function get_application($id)
		{
			 if (isset($this->applications[$id]))
				return $this->applications[$id];
			 return null;
		}
		function get_selected_application()
		{
			if (isset($this->selected_application))
				 return $this->applications[$this->selected_application];
			foreach ($this->applications as $application)
				return $application;
			return null;
		}
		function display()
		{
			global $path_to_root;
			
			include_once($path_to_root . "/themes/".user_theme()."/renderer.php");

			$this->init();
			$rend = new renderer();
			$rend->wa_header();

			$rend->display_applications($this);

			$rend->wa_footer();
			$this->renderer =& $rend;
		}
		function init()
		{
			global $SysPrefs;

			$this->menu = new menu(_("Main  Menu"));
			$this->menu->add_item(_("Main  Menu"), "index.php");
			$this->menu->add_item(_("Logout"), "/account/access/logout.php");
			$this->applications = array();
			if (get_company_pref('use_customers'))			
			$this->add_application(new customers_app());

			if (get_company_pref('use_inventory'))			
			$this->add_application(new inventory_app());
			
			if (get_company_pref('use_generalledger'))	
			$this->add_application(new general_ledger_app());

			hook_invoke_all('install_tabs', $this);

			$this->add_application(new setup_app());
		}
	}
