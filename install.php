<?php

/************************************************************************************
 ************************************************************************************
 **                                                                                **
 **  If you can read this text in your browser then you don't have PHP installed.  **
 **  Please install PHP 5.0 or higher, preferably PHP 5.2.                         **
 **                                                                                **
 ************************************************************************************
 ************************************************************************************/

/**
 * SilverStripe CMS Installer
 * This installer doesn't use any of the fancy Sapphire stuff in case it's unsupported.
 * It's also PHP4 syntax compatable
 */

// speed up mysql_connect timeout if the server can't be found
ini_set('mysql.connect_timeout', 5);

ini_set('max_execution_time', 0);
error_reporting(E_ALL ^ E_NOTICE);
session_start();

$majorVersion = strtok(phpversion(),'.');
if($majorVersion < 5) {
	header("HTTP/1.1 500 Server Error");
	echo str_replace('$PHPVersion', phpversion(), file_get_contents("sapphire/dev/install/php5-required.html"));
	die();
}

// Include environment files
$usingEnv = false;
$envFileExists = false;
$envFiles = array('_ss_environment.php', '../_ss_environment.php', '../../_ss_environment.php');
foreach($envFiles as $envFile) {
	if(@file_exists($envFile)) {
		include_once($envFile);
		$envFileExists = true;
		$usingEnv = true;
		break;
	}
}

if($envFileExists) {
	if(!empty($_REQUEST['useEnv'])) {
		$usingEnv = true;
	} else {
		$usingEnv = false;
	}
}

include_once('sapphire/core/Object.php');
include_once('sapphire/core/i18n.php');
include_once('sapphire/dev/install/DatabaseConfigurationHelper.php');
include_once('sapphire/dev/install/DatabaseAdapterRegistry.php');

// Set default locale, but try and sniff from the user agent
$locales = i18n::$common_locales;
$defaultLocale = i18n::get_locale();
if(isset($_SERVER['HTTP_USER_AGENT'])) {
	foreach($locales as $code => $details) {
		$bits = explode('_', $code);
		if(preg_match("/{$bits[0]}.{$bits[1]}/", $_SERVER['HTTP_USER_AGENT'])) {
			$defaultLocale = $code;
			break;
		}
	}
}

// Discover which databases are available
DatabaseAdapterRegistry::autodiscover();

// Determine which external database modules are USABLE
foreach(DatabaseAdapterRegistry::get_adapters() as $class => $details) {
	$databaseClasses[$class] = $details;
	if(file_exists($details['helperPath'])) {
		$databaseClasses[$class]['hasModule'] = true;
		include_once($details['helperPath']);
	} else {
		$databaseClasses[$class]['hasModule'] = false;
	}
}

// Load database config
if(isset($_REQUEST['db'])) {
	if(isset($_REQUEST['db']['type'])) $type = $_REQUEST['db']['type'];
	else $type = $_REQUEST['db']['type'] = defined('SS_DATABASE_CLASS') ? SS_DATABASE_CLASS : 'MySQLDatabase';

	// Disabled inputs don't submit anything - we need to use the environment (except the database name)
	if($usingEnv) {
		$_REQUEST['db'][$type] = $databaseConfig = array(
			"type" => defined('SS_DATABASE_CLASS') ? SS_DATABASE_CLASS : $type,
			"server" => defined('SS_DATABASE_SERVER') ? SS_DATABASE_SERVER : "localhost",
			"username" => defined('SS_DATABASE_USERNAME') ? SS_DATABASE_USERNAME : "root",
			"password" => defined('SS_DATABASE_PASSWORD') ? SS_DATABASE_PASSWORD : "",
			"database" => $_REQUEST['db'][$type]['database'],
		);
		
	} else {
		// Normal behaviour without the environment
		$databaseConfig = $_REQUEST['db'][$type];
		$databaseConfig['type'] = $type;
	}
} else {
	$_REQUEST['db'][$type] = $databaseConfig = array(
		"type" => defined('SS_DATABASE_CLASS') ? SS_DATABASE_CLASS : "MySQLDatabase",
		"server" => defined('SS_DATABASE_SERVER') ? SS_DATABASE_SERVER : "localhost",
		"username" => defined('SS_DATABASE_USERNAME') ? SS_DATABASE_USERNAME : "root",
		"password" => defined('SS_DATABASE_PASSWORD') ? SS_DATABASE_PASSWORD : "",
		"database" => isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : "SS_mysite",
	);
}

if(isset($_REQUEST['admin'])) {
	// Disabled inputs don't submit anything - we need to use the environment (except the database name)
	if($usingEnv) {
		$_REQUEST['admin'] = $adminConfig = array(
			'username' => defined('SS_DEFAULT_ADMIN_USERNAME') ? SS_DEFAULT_ADMIN_USERNAME : 'admin',
			'password' => defined('SS_DEFAULT_ADMIN_PASSWORD') ? SS_DEFAULT_ADMIN_PASSWORD : '',
		);
	} else {
		$adminConfig = $_REQUEST['admin'];
	}
} else {
	$_REQUEST['admin'] = $adminConfig = array(
		'username' => defined('SS_DEFAULT_ADMIN_USERNAME') ? SS_DEFAULT_ADMIN_USERNAME : 'admin',
		'password' => defined('SS_DEFAULT_ADMIN_PASSWORD') ? SS_DEFAULT_ADMIN_PASSWORD : '',
	);
}

$alreadyInstalled = false;
if(file_exists('mysite/_config.php')) {
	// Find the $database variable in the relevant config file without having to execute the config file
	if(preg_match("/\\\$database\s*=\s*[^\n\r]+[\n\r]/", file_get_contents("mysite/_config.php"), $parts)) {
		eval($parts[0]);
		if($database) $alreadyInstalled = true;
	// Assume that if $databaseConfig is defined in mysite/_config.php, then a non-environment-based installation has
	// already gone ahead
	} else if(preg_match("/\\\$databaseConfig\s*=\s*[^\n\r]+[\n\r]/", file_get_contents("mysite/_config.php"), $parts)) {
		$alreadyInstalled = true;
	}
}

if(file_exists('sapphire/silverstripe_version')) {
	$sapphireVersionFile = file_get_contents('sapphire/silverstripe_version');
		if(strstr($sapphireVersionFile, "/sapphire/trunk")) {
			$silverstripe_version = "trunk";
		} else {
			preg_match("/sapphire\/(?:(?:branches)|(?:tags))(?:\/rc)?\/([A-Za-z0-9._-]+)\/silverstripe_version/", $sapphireVersionFile, $matches);
			$silverstripe_version = $matches[1];
		}
} else {
	$silverstripe_version = "unknown";
}

// Check requirements
$req = new InstallRequirements();
$req->check();

if($req->hasErrors()) {
	$hasErrorOtherThanDatabase = true;
}

if($databaseConfig) {
	$dbReq = new InstallRequirements();
	$dbReq->checkdatabase($databaseConfig);
}

if($adminConfig) {
	$adminReq = new InstallRequirements();
	$adminReq->checkAdminConfig($adminConfig);
}

// Actual processor
$installFromCli = (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'install');

// CLI-install error message.  exit(1) will halt any makefile.
if($installFromCli && ($req->hasErrors() || $dbReq->hasErrors())) {
	echo "Cannot install due to errors:\n";
	$req->listErrors();
	$dbReq->listErrors();
	exit(1);
}

if((isset($_REQUEST['go']) || $installFromCli) && !$req->hasErrors() && !$dbReq->hasErrors() && $adminConfig['username'] && $adminConfig['password']) {
	// Confirm before reinstalling
	if(!isset($_REQUEST['force_reinstall']) && !$installFromCli && $alreadyInstalled) {
		include('sapphire/dev/install/config-form.html');
		
	} else {
		$inst = new Installer();
		if($_REQUEST) $inst->install($_REQUEST);
		else $inst->install(array(
			'db' => $databaseConfig,
			'admin' => $adminConfig,
		));
	}

// Show the config form
} else {
	include('sapphire/dev/install/config-form.html');	
}

/**
 * This class checks requirements
 * Each of the requireXXX functions takes an argument which gives a user description of the test.
 * It's an array of 3 parts:
 *  $description[0] - The test catetgory
 *  $description[1] - The test title
 *  $description[2] - The test error to show, if it goes wrong
 */
class InstallRequirements {
	var $errors, $warnings, $tests;
	
	/**
	 * Check the database configuration. These are done one after another
	 * starting with checking the database function exists in PHP, and
	 * continuing onto more difficult checks like database permissions.
	 */
	function checkdatabase($databaseConfig) {
		if($this->requireDatabaseFunctions(
			$databaseConfig,
			array(
				"Database Configuration",
				"Database support",
				"Database support not included in PHP.")
			)
		) {
			if($this->requireDatabaseServer(
				$databaseConfig,
				array(
					"Database Configuration",
					"Database server exists",
					"I couldn't find a database server on '$databaseConfig[server]'",
					$databaseConfig['server']
				)
			)) {
				if($this->requireDatabaseConnection(
					$databaseConfig,
					array(
						"Database Configuration",
						"Database access credentials correct",
						"That username/password doesn't work"
					)
				)) {
					$this->requireDatabaseOrCreatePermissions(
						$databaseConfig,
						array(
							"Database Configuration",
							"Can I access/create the database",
							"I can't create new databases and the database '$databaseConfig[database]' doesn't exist"
						)
					);
				}
			}
		}
	}
	
	function checkAdminConfig($adminConfig) {
		if(!$adminConfig['username']) {
			$this->error(array('', 'Please enter a username!'));
		}
		if(!$adminConfig['password']) {
			$this->error(array('', 'Please enter a password!'));
		}
	}

	/**
	 * Check if the web server is IIS.
	 * @return boolean
	 */
	function isIIS() {
		if(isset($_SERVER['SERVER_SOFTWARE'])) {
			if(strpos($_SERVER['SERVER_SOFTWARE'], 'IIS/7') !== false) {
				return true;
			}
		}
		return false;
	}
	
	function isApache() {
		$signature = !empty($_SERVER['SERVER_SIGNATURE'])
			? $_SERVER['SERVER_SIGNATURE']
			: @$_SERVER['SERVER_SOFTWARE'];
		
		$signature = strip_tags(trim($signature));
		if($signature && strpos($signature, 'Apache') !== false) {
			return true;
		} else {
			return false;
		}
	}

	function findWebserver() {
		$webserver = strip_tags(trim(@$_SERVER['SERVER_SIGNATURE']));
		if(!$webserver) {
			if(isset($_SERVER['SERVER_SOFTWARE'])) {
				if(strpos($_SERVER['SERVER_SOFTWARE'], 'IIS/7') !== false ||
					strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
					$webserver = $_SERVER['SERVER_SOFTWARE'];
				}
			} else {
				$webserver = "I can't tell what webserver you are running";
			}
		}
		return $webserver;
	}
	
	/**
	 * Check everything except the database
	 */
	function check() {
		$this->errors = null;
		$isApache = $this->isApache();
		$isIIS = $this->isIIS();
		$webserver = $this->findWebserver();
		
		$this->requirePHPVersion('5.2.0', '5.1.0', array("PHP Configuration", "PHP5 installed", null, "PHP version " . phpversion()));

		// Check that we can identify the root folder successfully
		$this->requireFile('sapphire/dev/install/config-form.html', array("File permissions", 
			"Does the webserver know where files are stored?", 
			"The webserver isn't letting me identify where files are stored.",
			$this->getBaseDir()
		));
		
		$this->requireFile('mysite', array("File permissions", "mysite/ folder exists", "There's no mysite folder."));
		$this->requireFile('sapphire', array("File permissions", "sapphire/ folder exists", "There's no sapphire folder."));
		$this->requireFile('cms', array("File permissions", "cms/ folder exists", "There's no cms folder."));
		
		if($isApache) {
			$this->requireWriteable('.htaccess', array("File permissions", "Is the .htaccess file writeable?", null));
		} elseif($isIIS) {
			$this->requireWriteable('web.config', array("File permissions", "Is the web.config file writeable?", null));
		}
		
		$this->requireWriteable('mysite/_config.php', array("File permissions", "Is the mysite/_config.php file writeable?", null));
		$this->requireWriteable('assets', array("File permissions", "Is the assets/ folder writeable?", null));
		
		$this->requireTempFolder(array('File permissions', 'Is the temporary folder writeable?', null));
		
		// Check for web server, unless we're calling the installer from the command-line
		if(!isset($_SERVER['argv']) || !$_SERVER['argv']) {
			$this->isRunningWebServer(array("Webserver Configuration", "Server software", "Unknown web server", $webserver));
			
			if($isApache) {
				$this->requireApacheRewriteModule('mod_rewrite', array("Webserver Configuration", "URL rewriting support", "You need mod_rewrite to use friendly URLs with SilverStripe, but it is not enabled."));
			} elseif($isIIS) {
				$this->requireIISRewriteModule('IIS_UrlRewriteModule', array("Webserver Configuration", "URL rewriting support", "You need to enable the IIS URL Rewrite Module to use friendly URLs with SilverStripe, but it is not installed or enabled. Download it for IIS 7 from http://www.iis.net/expand/URLRewrite"));
			} else {
				$this->warning(array("Webserver Configuration", "URL rewriting support", "I can't tell whether any rewriting module is running.  You may need to configure a rewriting rule yourself."));
			}
			
			$this->requireServerVariables(array('SCRIPT_NAME','HTTP_HOST','SCRIPT_FILENAME'), array("Webserver config", "Recognised webserver", "You seem to be using an unsupported webserver.  The server variables SCRIPT_NAME, HTTP_HOST, SCRIPT_FILENAME need to be set."));
		}
		
		// Check for GD support
		if(!$this->requireFunction("imagecreatetruecolor", array("PHP Configuration", "GD2 support", "PHP must have GD version 2."))) {
			$this->requireFunction("imagecreate", array("PHP Configuration", "GD2 support", "GD support for PHP not included."));
		}
		
		// Check for XML support
		$this->requireFunction('xml_set_object', array("PHP Configuration", "XML support", "XML support not included in PHP."));
		$this->requireClass('DOMDocument', array("PHP Configuration", "DOM/XML support", "DOM/XML support not included in PHP."));
		
		// Check for token_get_all
		$this->requireFunction('token_get_all', array("PHP Configuration", "PHP Tokenizer", "PHP tokenizer support not included in PHP."));
		
		
		// Check memory allocation
		$this->requireMemory(32*1024*1024, 64*1024*1024, array("PHP Configuration", "Memory allocated (PHP config option 'memory_limit')", "SilverStripe needs a minimum of 32M allocated to PHP, but recommends 64M.", ini_get("memory_limit")));
			
		return $this->errors;
	}
	
	function suggestPHPSetting($settingName, $settingValues, $testDetails) {
		$this->testing($testDetails);
		
		$val = ini_get($settingName);
		if(!in_array($val, $settingValues) && $val != $settingValues) {
			$testDetails[2] = "$settingName is set to '$val' in php.ini.  $testDetails[2]";
			$this->warning($testDetails);
		}
	}
	
	function requireMemory($min, $recommended, $testDetails) {
		$_SESSION['forcemem'] = false;
		
		$mem = $this->getPHPMemory();
		if($mem < (64 * 1024 * 1024)) {
			ini_set('memory_limit', '64M');
			$mem = $this->getPHPMemory();
			$testDetails[3] = ini_get("memory_limit");
		}
		
		$this->testing($testDetails);

		if($mem < $min && $mem > 0) {
			$testDetails[2] .= " You only have " . ini_get("memory_limit") . " allocated";
			$this->error($testDetails);
		} else if($mem < $recommended && $mem > 0) {
			$testDetails[2] .= " You only have " . ini_get("memory_limit") . " allocated";
			$this->warning($testDetails);
		} elseif($mem == 0) {
			$testDetails[2] .= " We can't determine how much memory you have allocated. Install only if you're sure you've allocated at least 20 MB.";
			$this->warning($testDetails);
		}
	}
	
	function getPHPMemory() {
		$memString = ini_get("memory_limit");

		switch(strtolower(substr($memString,-1))) {
			case "k":
				return round(substr($memString,0,-1)*1024);

			case "m":
				return round(substr($memString,0,-1)*1024*1024);
			
			case "g":
				return round(substr($memString,0,-1)*1024*1024*1024);
			
			default:
				return round($memString);
		}
	}
	
	function listErrors() {
		if($this->errors) {
			echo "<p>The following problems are preventing me from installing SilverStripe CMS:</p>\n\n";
			foreach($this->errors as $error) {
				echo "<li>" . htmlentities(implode(", ", $error)) . "</li>\n";
			}
		}
	}
	
	function showTable($section = null) {
		if($section) {
			$tests = $this->tests[$section];
			$id = strtolower(str_replace(' ', '_', $section));
			echo "<table id=\"{$id}_results\" class=\"testResults\" width=\"100%\">";
			foreach($tests as $test => $result) {
				echo "<tr class=\"$result[0]\"><td>$test</td><td>" . nl2br(htmlentities($result[1])) . "</td></tr>";
			}
			echo "</table>";
			
		} else {
			foreach($this->tests as $section => $tests) {
				$failedRequirements = 0;
				$warningRequirements = 0;
				
				$output = "";
				
				foreach($tests as $test => $result) {
					if(isset($result['0'])) {
						switch($result['0']) {
							case 'error':
								$failedRequirements++;
								break;
							case 'warning':
								$warningRequirements++;
								break;
						}
					}
					$output .= "<tr class=\"$result[0]\"><td>$test</td><td>" . nl2br(htmlentities($result[1])) . "</td></tr>";
				}
				$className = "good";
				$text = "All Requirements Pass";
				$pluralWarnings = ($warningRequirements == 1) ? 'Warning' : 'Warnings';
				
				if($failedRequirements > 0) {
					$className = "error";
					$pluralWarnings = ($warningRequirements == 1) ? 'Warning' : 'Warnings';
					
					$text = $failedRequirements . ' Failed and '. $warningRequirements . ' '. $pluralWarnings;
				}
				else if($warningRequirements > 0) {
					$className = "warning";
					$text = "All Requirements Pass but ". $warningRequirements . ' '. $pluralWarnings;
				}
				
				echo "<h5 class='requirement $className'><em class='inlineBarText'>$section</em> <a href='#'>See All Requirements</a> <span>$text</span></h5>";
				echo "<table class=\"testResults\">";
				echo $output;
				echo "</table>";
			}		
		}
	}
	
	function requireFunction($funcName, $testDetails) {
		$this->testing($testDetails);
		if(!function_exists($funcName)) $this->error($testDetails);
		else return true;
	}

	function requireClass($className, $testDetails) {
		$this->testing($testDetails);
		if(!class_exists($className)) $this->error($testDetails);
		else return false;
	}

	/**
	 * Require that the given class doesn't exist
	 */
	function requireNoClasses($classNames, $testDetails) {
		$this->testing($testDetails);
		$badClasses = array();
		foreach($classNames as $className) {
			if(class_exists($className)) $badClasses[] = $className;
		}
		if($badClasses) {
			$testDetails[2] .= ".  The following classes are at fault: " . implode(', ', $badClasses);
			$this->error($testDetails);
		}
		else return true;
	}
		
	function requirePHPVersion($recommendedVersion, $requiredVersion, $testDetails) {
		$this->testing($testDetails);
		
		$installedVersion = phpversion();
		
		if(version_compare($installedVersion, $requiredVersion, '<')) {
			$testDetails[2] = "SilverStripe requires PHP version $requiredVersion or later.\n
				PHP version $installedVersion is currently installed.\n
				While SilverStripe requires at least PHP version $requiredVersion, upgrading to $recommendedVersion or later is recommended.\n
				If you are installing SilverStripe on a shared web server, please ask your web hosting provider to upgrade PHP for you.";
			$this->error($testDetails);
			return;
		}
		
		if(version_compare($installedVersion, $recommendedVersion, '<')) {
			$testDetails[2] = "PHP version $installedVersion is currently installed.\n
				Upgrading to at least PHP version $recommendedVersion is recommended.\n
				SilverStripe should run, but you may run into issues. Future releases may require a later version of PHP.\n";
			$this->warning($testDetails);
			return;
		}
		
		return true;
	}
	
	function requireFile($filename, $testDetails) {
		$this->testing($testDetails);
		$filename = $this->getBaseDir() . $filename;
		if(!file_exists($filename)) {
			$testDetails[2] .= " (file '$filename' not found)";
			$this->error($testDetails);
		}
	}
	
	function requireWriteable($filename, $testDetails) {
		$this->testing($testDetails);
		$filename = $this->getBaseDir() . str_replace("/", DIRECTORY_SEPARATOR,$filename);
		
		if(!is_writeable($filename)) {
			if(function_exists('posix_getgroups')) {
				$userID = posix_geteuid();
				$user = posix_getpwuid($userID);

				$currentOwnerID = fileowner($filename);
				$currentOwner = posix_getpwuid($currentOwnerID);

				$testDetails[2] .= "User '$user[name]' needs to be able to write to this file:\n$filename\n\nThe file is currently owned by '$currentOwner[name]'.  ";

				if($user['name'] == $currentOwner['name']) {
					$testDetails[2] .= "We recommend that you make the file writeable.";
				} else {
					
					$groups = posix_getgroups();
					foreach($groups as $group) {
						$groupInfo = posix_getgrgid($group);
						if(in_array($currentOwner['name'], $groupInfo['members'])) $groupList[] = $groupInfo['name'];
					}
					if($groupList) {
						$testDetails[2] .= "	We recommend that you make the file group-writeable and change the group to one of these groups:\n - ". implode("\n - ", $groupList)
							. "\n\nFor example:\nchmod g+w $filename\nchgrp " . $groupList[0] . " $filename";  		
					} else {
						$testDetails[2] .= "  There is no user-group that contains both the web-server user and the owner of this file.  Change the ownership of the file, create a new group, or temporarily make the file writeable by everyone during the install process.";
					}
				}

			} else {
				$testDetails[2] .= "The webserver user needs to be able to write to this file:\n$filename";
			}
			
			$this->error($testDetails);
		}
	}
	
	function requireTempFolder($testDetails) {
		$this->testing($testDetails);
		
		if(function_exists('sys_get_temp_dir')) {
	        $sysTmp = sys_get_temp_dir();
	    } elseif(isset($_ENV['TMP'])) {
			$sysTmp = $_ENV['TMP'];    	
	    } else {
	        @$tmpFile = tempnam('adfadsfdas','');
	        @unlink($tmpFile);
	        $sysTmp = dirname($tmpFile);
	    }
	    
	    $worked = true;
	    $ssTmp = "$sysTmp/silverstripe-cache";
	    
	    if(!@file_exists($ssTmp)) {
	    	@$worked = mkdir($ssTmp);
	    	
	    	if(!$worked) {
		    	$ssTmp = dirname($_SERVER['SCRIPT_FILENAME']) . "/silverstripe-cache";
		    	$worked = true;
		    	if(!@file_exists($ssTmp)) {
		    		@$worked = mkdir($ssTmp);
		    	}
		    	if(!$worked) {
		    		$testDetails[2] = "Permission problem gaining access to a temp folder. " .
		    			"Please create a folder named silverstripe-cache in the base folder "  .
		    			"of the installation and ensure it has the adequate permissions";
		    		$this->error($testDetails);
		    	}
		    }
		}
	}
	
	function requireApacheModule($moduleName, $testDetails) {
		$this->testing($testDetails);
		if(!in_array($moduleName, apache_get_modules())) {
			$this->error($testDetails);
			return false;
		} else {
			return true;
		}
	}

	function testApacheRewriteExists($moduleName = 'mod_rewrite') {
		if(function_exists('apache_get_modules') && in_array($moduleName, apache_get_modules())) {
			return true;
		} else {
			return false;
		}
	}
	
	function testIISRewriteModuleExists($moduleName = 'IIS_UrlRewriteModule') {
		if(isset($_SERVER[$moduleName]) && $_SERVER[$moduleName]) {
			return true;
		} else {
			return false;
		}
	}
	
	function requireApacheRewriteModule($moduleName, $testDetails) {
		$this->testing($testDetails);
		if($this->testApacheRewriteExists()) {
			return true;
		} else {
			$this->warning($testDetails);
			return false;
		}
	}
	
	/**
	 * Determines if the web server has any rewriting capability.
	 * @return boolean
	 */
	function hasRewritingCapability() {
		return ($this->testApacheRewriteExists() || $this->testIISRewriteModuleExists());
	}
	
	function requireIISRewriteModule($moduleName, $testDetails) {
		$this->testing($testDetails);
		if($this->testIISRewriteModuleExists()) {
			return true;
		} else {
			$this->warning($testDetails);
			return false;
		}
	}
	
	/**
	 * Get an instance of a helper class for the specific database.
	 * @param string $databaseClass e.g. MySQLDatabase or MSSQLDatabase
	 */
	function getDatabaseConfigurationHelper($databaseClass) {
		$adapters = DatabaseAdapterRegistry::get_adapters();
		if(isset($adapters[$databaseClass])) {
			$helperPath = $adapters[$databaseClass]['helperPath'];
			$class = str_replace('.php', '', basename($helperPath));
		}
		return (class_exists($class)) ? new $class() : new MySQLDatabaseConfigurationHelper();
		return new $class();
	}
	
	function requireDatabaseFunctions($databaseConfig, $testDetails) {
		$this->testing($testDetails);
		$helper = $this->getDatabaseConfigurationHelper($databaseConfig['type']);
		$result = $helper->requireDatabaseFunctions($databaseConfig);
		if($result) {
			return true;
		} else {
			$this->error($testDetails);
			return false;
		}
	}
	
	function requireDatabaseConnection($databaseConfig, $testDetails) {
		$this->testing($testDetails);
		$helper = $this->getDatabaseConfigurationHelper($databaseConfig['type']);		
		$result = $helper->requireDatabaseConnection($databaseConfig);
		if($result['success']) {
			return true;
		} else {
			$testDetails[2] .= ": " . $result['error'];
			$this->error($testDetails);
			return false;
		}
	}
	
	function requireDatabaseServer($databaseConfig, $testDetails) {
		$this->testing($testDetails);
		$helper = $this->getDatabaseConfigurationHelper($databaseConfig['type']);
		$result = $helper->requireDatabaseServer($databaseConfig);
		if($result['success']) {
			return true;
		} else {
			$testDetails[2] .= ": " . $result['error'];
			$this->error($testDetails);
			return false;
		}
	}
	
	function requireDatabaseOrCreatePermissions($databaseConfig, $testDetails) {
		$this->testing($testDetails);
		$helper = $this->getDatabaseConfigurationHelper($databaseConfig['type']);
		$result = $helper->requireDatabaseOrCreatePermissions($databaseConfig);
		if($result['success']) {
			if($result['alreadyExists']) $testDetails[3] = "Database $databaseConfig[database] exists";
			else $testDetails[3] = "Able to create a new database";
			$this->testing($testDetails);
			return true;
		} else {
			if(!@$result['cannotCreate']) {
				$testDetails[2] .= ". Please create the database manually.";
			} else {
				$testDetails[2] .= " (user '$databaseConfig[username]' doesn't have CREATE DATABASE permissions.)";
			}
			
			$this->error($testDetails);
			return false;
		}
	}
	
	function requireServerVariables($varNames, $errorMessage) {
		//$this->testing($testDetails);
		foreach($varNames as $varName) {
			if(!$_SERVER[$varName]) $missing[] = '$_SERVER[' . $varName . ']';
		}
		if(!isset($missing)) {
			return true;
		} else {
			$testDetails[2] .= " (the following PHP variables are missing: " . implode(", ", $missing) . ")";
			$this->error($testDetails);
		}
	}
	
	function isRunningWebServer($testDetails) {
		$this->testing($testDetails);
		if(function_exists('apache_get_modules') || stristr(@$_SERVER['SERVER_SIGNATURE'], 'Apache')) {
			return true;
		} elseif(strpos($_SERVER['SERVER_SOFTWARE'], 'IIS/7') !== false) {
			return true;
		} else {
			$this->warning($testDetails);
			return false;
		}
	}


	// Must be PHP4 compatible
	var $baseDir;
	function getBaseDir() {
		// Cache the value so that when the installer mucks with SCRIPT_FILENAME half way through, this method
		// still returns the correct value.
		if(!$this->baseDir) $this->baseDir = realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DIRECTORY_SEPARATOR;
		return $this->baseDir;
	}
	
	function testing($testDetails) {
		if(!$testDetails) return;
		
		$section = $testDetails[0];
		$test = $testDetails[1];
		
		$message = "OK";
		if(isset($testDetails[3])) $message .= " ($testDetails[3])";

		$this->tests[$section][$test] = array("good", $message);
	}
	
	function error($testDetails) {
		$section = $testDetails[0];
		$test = $testDetails[1];

		$this->tests[$section][$test] = array("error", $testDetails[2]);
		$this->errors[] = $testDetails;
	}
	
	function warning($testDetails) {
		$section = $testDetails[0];
		$test = $testDetails[1];

		$this->tests[$section][$test] = array("warning", $testDetails[2]);
		$this->warnings[] = $testDetails;
	}
	
	function hasErrors() {
		return sizeof($this->errors);
	}
	
	function hasWarnings() {
		return sizeof($this->warnings);
	}
	
}

class Installer extends InstallRequirements {
	function __construct() {
		// Cache the baseDir value
		$this->getBaseDir();
	}
	
	function install($config) {
		if(isset($_SERVER['HTTP_HOST'])) {
			?>
<html>
	<head>
		<title>PHP 5 is required</title>
		<link rel="stylesheet" type="text/css" href="themes/blackcandy/css/layout.css" />
		<link rel="stylesheet" type="text/css" href="themes/blackcandy/css/typography.css" />
		<link rel="stylesheet" type="text/css" href="themes/blackcandy/css/form.css" />
		<link rel="stylesheet" type="text/css" href="sapphire/dev/install/install.css" />
		<script src="sapphire/thirdparty/jquery/jquery.js"></script>
	</head>
	<body>
		<div id="BgContainer">
			<div id="Container">
				<div id="Header">
					<h1>SilverStripe CMS Installation</h1>
				</div>

				<div id="Navigation">&nbsp;</div>
				<div class="clear"><!-- --></div>

				<div id="Layout">
					<div class="typography">
						<h1>Installing SilverStripe...</h1>
						<p>I am now running through the installation steps (this should take about 30 seconds)</p>
						<p>If you receive a fatal error, refresh this page to continue the installation</p>
						<ul>
<?php
		} else {
			echo "SILVERSTRIPE COMMAND-LINE INSTALLATION\n\n";
		}
		
		$webserver = $this->findWebserver();
		$isIIS = $this->isIIS();
		$isApache = $this->isApache();
		
		flush();
		
		if(isset($_POST['stats'])) {
			if(file_exists('sapphire/silverstripe_version')) {
				$sapphireVersionFile = file_get_contents('sapphire/silverstripe_version');
				if(strstr($sapphireVersionFile, "/sapphire/trunk")) {
					$silverstripe_version = "trunk";
				} else {
					preg_match("/sapphire\/(?:(?:branches)|(?:tags))(?:\/rc)?\/([A-Za-z0-9._-]+)\/silverstripe_version/", $sapphireVersionFile, $matches);
					$silverstripe_version = $matches[1];
				}
			} else {
				$silverstripe_version = "unknown";
			}
			
			$phpVersion = urlencode(phpversion());
			$encWebserver = urlencode($webserver);

			if($type == 'MySQLDatabase') {
				$conn = @mysql_connect($dbConfig['server'], null, null);
				$databaseVersion = urlencode('MySQLDatabase: ' . mysql_get_server_info());
			} else {
				$databaseVersion = $type;
			}
			
			$url = "http://ss2stat.silverstripe.com/Installation/add?SilverStripe=$silverstripe_version&PHP=$phpVersion&Database=$databaseVersion&WebServer=$encWebserver";
			
			if(isset($_SESSION['StatsID']) && $_SESSION['StatsID']) {
				$url .= '&ID=' . $_SESSION['StatsID'];
			}
			
			@$_SESSION['StatsID'] = file_get_contents($url);
		}
		
		if(file_exists('mysite/_config.php')) {
			// Truncate the contents of _config instead of deleting it - we can't re-create it because Windows handles permissions slightly
			// differently to UNIX based filesystems - it takes the permissions from the parent directory instead of retaining them
			$fh = fopen('mysite/_config.php', 'wb');
			fclose($fh);
		}
		$theme = isset($_POST['template']) ? $_POST['template'] : 'blackcandy';
		$locale = isset($_POST['locale']) ? $_POST['locale'] : 'en_US';
		$type = $config['db']['type'];
		$dbConfig = $config['db'][$type];
		if(!$dbConfig) {
			echo "<p style=\"color: red\">Bad config submitted</p><pre>";
			print_r($config);
			echo "</pre>";
			die();
		}
		
		// Write the config file
		global $usingEnv;
		if($usingEnv) {
			
			$this->statusMessage("Setting up 'mysite/_config.php' for use with _ss_environment.php...");
			$this->writeToFile("mysite/_config.php", <<<PHP
<?php

global \$project;
\$project = 'mysite';

global \$database;
\$database = '{$dbConfig['database']}';

require_once('conf/ConfigureFromEnv.php');

MySQLDatabase::set_connection_charset('utf8');

// This line set's the current theme. More themes can be
// downloaded from http://www.silverstripe.org/themes/
SSViewer::set_theme('$theme');

// Set the site locale
i18n::set_locale('$locale');

// enable nested URLs for this site (e.g. page/sub-page/)
SiteTree::enable_nested_urls();
PHP
			);
			
		} else {
			$this->statusMessage("Setting up 'mysite/_config.php'...");
			$escapedPassword = addslashes($dbConfig['password']);
			$this->writeToFile("mysite/_config.php", <<<PHP
<?php

global \$project;
\$project = 'mysite';

global \$databaseConfig;
\$databaseConfig = array(
	"type" => '{$type}',
	"server" => '{$dbConfig['server']}', 
	"username" => '{$dbConfig['username']}', 
	"password" => '{$escapedPassword}', 
	"database" => '{$dbConfig['database']}',
	"path" => '{$dbConfig['path']}',
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

// This line set's the current theme. More themes can be
// downloaded from http://www.silverstripe.org/themes/
SSViewer::set_theme('$theme');

// Set the site locale
i18n::set_locale('$locale');

// enable nested URLs for this site (e.g. page/sub-page/)
SiteTree::enable_nested_urls();
PHP
			);
		}

		// Write the appropriate web server configuration file for rewriting support
		if($this->hasRewritingCapability()) {
			if($isApache) {
				$this->statusMessage("Setting up '.htaccess' file...");
				$this->createHtaccess();
			} elseif($isIIS) {
				$this->statusMessage("Setting up 'web.config' file...");
				$this->createWebConfig();	
			}
		}

		// Load the sapphire runtime
		$_SERVER['SCRIPT_FILENAME'] = dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/sapphire/main.php';
		chdir('sapphire');

		// Rebuild the manifest
		$_GET['flush'] = true;
		// Show errors as if you're in development mode 
		$_SESSION['isDev'] = 1;
		
		require_once('core/Core.php');
	
		$this->statusMessage("Building database schema...");

		// Build database
		$con = new Controller();
		$con->pushCurrent();

		global $databaseConfig;
		DB::connect($databaseConfig);
		
		$dbAdmin = new DatabaseAdmin();
		$dbAdmin->init();
		
		$dbAdmin->doBuild(true);
		
		// Create default administrator user and group in database 
		// (not using Security::setDefaultAdmin())
		$adminMember = Security::findAnAdministrator();
		$adminMember->Email = $config['admin']['username'];
		$adminMember->Password = $config['admin']['password'];
		$adminMember->PasswordEncryption = Security::get_password_encryption_algorithm();
		
		// @todo Exception thrown if database with admin already exists with same Email
		try {
			$adminMember->write();
		} catch(Exception $e) {
		}
		
		// Syncing filesystem (so /assets/Uploads is available instantly, see ticket #2266)
		Filesystem::sync();
		
		$_SESSION['username'] = $config['admin']['username'];
		$_SESSION['password'] = $config['admin']['password'];

		if(!$this->errors) {
			if(isset($_SERVER['HTTP_HOST']) && $this->hasRewritingCapability()) {
				$this->statusMessage("Checking that friendly URLs work...");
				$this->checkRewrite();
			} else {
				echo <<<HTML
				<li>SilverStripe successfully installed; I am now redirecting you to your SilverStripe site...</li>
				<script>
					setTimeout(function() {
						window.location = "index.php/home/successfullyinstalled?flush=1";
					}, 2000);
				</script>
				<noscript>
				<li><a href="index.php/home/successfullyinstalled?flush=1">Click here to access your site.</li>
				</noscript>
HTML;
			}
		}
		
		return $this->errors;
	}
	
	function writeToFile($filename, $content) {
		$base = $this->getBaseDir();
		$this->statusMessage("Setting up $base$filename");

		if((@$fh = fopen($base . $filename, 'wb')) && fwrite($fh, $content) && fclose($fh)) {
			return true;
		} else {
			$this->error("Couldn't write to file $base$filename");
		}
	}
	
	function createHtaccess() {
		$start = "### SILVERSTRIPE START ###\n";
		$end = "\n### SILVERSTRIPE END ###";
		
		$base = dirname($_SERVER['SCRIPT_NAME']);
		if(defined('DIRECTORY_SEPARATOR')) $base = str_replace(DIRECTORY_SEPARATOR, '/', $base);
		else $base = str_replace("\\", '/', $base);
		
		if($base != '.') $baseClause = "RewriteBase $base\n";
		else $baseClause = "";
		
		$rewrite = <<<TEXT
<Files *.ss>
	Order deny,allow
	Deny from all
	Allow from 127.0.0.1
</Files>

<Files web.config>
	Order deny,allow
	Deny from all
</Files>

<IfModule mod_rewrite.c>
	RewriteEngine On
	$baseClause
	RewriteCond %{REQUEST_URI} ^(.*)$
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule .* sapphire/main.php?url=%1&%{QUERY_STRING} [L]
</IfModule>
TEXT;
		
		if(file_exists('.htaccess')) {
			$htaccess = file_get_contents('.htaccess');
			
			if(strpos($htaccess, '### SILVERSTRIPE START ###') === false && strpos($htaccess, '### SILVERSTRIPE END ###') === false) {
				$htaccess .= "\n### SILVERSTRIPE START ###\n### SILVERSTRIPE END ###\n";
			}
		
			if(strpos($htaccess, '### SILVERSTRIPE START ###') !== false && strpos($htaccess, '### SILVERSTRIPE END ###') !== false) {
				$start = substr($htaccess, 0, strpos($htaccess, '### SILVERSTRIPE START ###')) . "### SILVERSTRIPE START ###\n";
				$end = "\n" . substr($htaccess, strpos($htaccess, '### SILVERSTRIPE END ###'));
			}
		}
		
		$this->writeToFile('.htaccess', $start . $rewrite . $end);
	}
	
	/**
	 * Writes basic configuration to the web.config for IIS
	 * so that rewriting capability can be use.
	 */
	function createWebConfig() {
		$content = <<<TEXT
<?xml version="1.0" encoding="utf-8"?>
<configuration>
	<system.webServer>
		<rewrite>
			<rules>
				<rule name="SilverStripe Clean URLs" stopProcessing="true">
					<match url="^(.*)$" />
					<conditions>
						<add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
					</conditions>
					<action type="Rewrite" url="sapphire/main.php?url={R:1}" appendQueryString="true" />
				</rule>
			</rules>
		</rewrite>
	</system.webServer>
</configuration>
TEXT;
		
		$this->writeToFile('web.config', $content);
	}
	
	function checkRewrite() {
		if(!isset($_SERVER['HTTP_HOST']) || !$_SERVER['HTTP_HOST']) {
			$this->statusMessage("Installer seems to be called from command-line, we're going to assume that rewriting is working.");
			return true;
		}

		echo <<<HTML
<li id="ModRewriteResult">Testing...</li>
<script>
	if(typeof $ == 'undefined') {
		document.getElemenyById('ModeRewriteResult').innerHTML = "I can't run jQuery ajax to set rewriting; I will redirect you to the homepage to see if everything is working.";
		setTimeout(function() {
			window.location = "home/successfullyinstalled?flush=1";
		}, 10000);
	} else {
		$.ajax({
			method: 'get',
			url: 'InstallerTest/testrewrite',
			complete: function(response) {
				if(response.responseText == 'OK') {
					$('#ModRewriteResult').html("Friendly URLs set up successfully; I am now redirecting you to your SilverStripe site...")
					setTimeout(function() {
						window.location = "home/successfullyinstalled?flush=1";
					}, 2000);
				} else {
					$('#ModRewriteResult').html("Friendly URLs are not working.  This is most likely because a rewrite module isn't configured"
						+ "correctly on your site.  Please check the following things in your Apache configuration; "
						+ " you may need to get your web host or server administrator to do this for you:"
						+ "<ul><li>mod_rewrite is enabled</li><li>AllowOverride All is set for your directory</li></ul>");
				}
			}
		});
	}
</script>
<noscript>
<li><a href="home/successfullyinstalled?flush=1">Click here to check friendly URLs are working.  If you get a 404 then something is wrong.</li>
</noscript>
HTML;
	}
	
	function var_export_array_nokeys($array) {
		$retval = "array(\n";
		foreach($array as $item) {
			$retval .= "\t'";
			$retval .= trim($item);
			$retval .= "',\n";
		}
		$retval .= ")";
		return $retval;
	}
	
	/**
	 * Show an installation status message.
	 * The output differs depending on whether this is CLI or web based
	 */
	function statusMessage($msg) {
		if(isset($_SERVER['HTTP_HOST'])) echo "<li>$msg</li>\n";
		else echo "$msg\n";
		flush();
	}
}