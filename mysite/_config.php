<?php

global $project;
$project = 'mysite';

global $databaseConfig;
$databaseConfig = array(
	"type" => 'MySQLDatabase',
	"server" => 'localhost', 
	"username" => 'scm', 
	"password" => 'scmgroup', 
	"database" => 'SS_scm',
	"path" => '',
);

// Sites running on the following servers will be
// run in development mode. See
// http://doc.silverstripe.org/doku.php?id=configuration
// for a description of what dev mode does.
Director::set_dev_servers(array(
	'localhost',
	'127.0.0.1'
));

MySQLDatabase::set_connection_charset('utf8');
Security::setDefaultAdmin('admin', 'scmgroup123');

// This line set's the current theme. More themes can be
// downloaded from http://www.silverstripe.org/themes/
SSViewer::set_theme('scm');

// Set the site locale
i18n::set_locale('en_NZ');

// enable nested URLs for this site (e.g. page/sub-page/)
SiteTree::enable_nested_urls();
