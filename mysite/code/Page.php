<?php

class Page extends SiteTree {
	
	public static $db = array(
	);
	
	public static $has_one = array(
	"Sidebar" => "WidgetArea",
	);
	
	function getCMSFields() {
	$fields = parent::getCMSFields();
	$fields->addFieldToTab("Root.Content.Widgets", new WidgetAreaEditor("Sidebar"));
	return $fields;
    }
	
}

class Page_Controller extends ContentController {
	
	public function init() {
			
		Requirements::javascript('sapphire/thirdparty/jquery/jquery.js');	
	
   //Requirements::javascript("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js");
	Requirements::javascript("mysite/javascript/jquery.clear.search.js");
			Requirements::javascript("themes/".SSViewer::current_theme()."/js/cufon-yui.js");
		//Requirements::javascript("themes/".SSViewer::current_theme()."/js/cufon-replace.js");
		Requirements::javascript("themes/".SSViewer::current_theme()."/js/Kozuka_Gothic_Pro_OpenType_300.font.js");
		Requirements::javascript("themes/".SSViewer::current_theme()."/js/Kozuka_Gothic_Pro_OpenType_400.font.js");
		Requirements::javascript("themes/".SSViewer::current_theme()."/js/Kozuka_Gothic_Pro_OpenType_500.font.js");
		Requirements::javascript("themes/".SSViewer::current_theme()."/js/Gabriola_400.font.js");
  	
		parent::init();

		// Note: you should use SS template require tags inside your templates
		// instead of putting Requirements calls here.  However these are
		// included so that our older themes still work
		//Requirements::themedCSS("layout"); 
		//Requirements::themedCSS("typography"); 
		//Requirements::themedCSS("form"); 
		//Requirements::css('mysite/css/mycss.css');
	
	
		
		
		$tags = '<!--[if IE 6]> 
<style type="text/css">
	@import url("http://fhlsbs01/silverstripe/mysite/css/ie7.css");
</style>

<![endif]-->
<!--[if IE 9]>
<link rel="stylesheet" type="text/css" 

href="http://fhlsbs01/silverstripe/mysite/css/ie7.css" >
<![endif]-->'; 
//Requirements::insertHeadTags($tags);
		
	}
	
	
	 
	/**
	 * Site search form 
	 */ 
	function SearchForm() {
		$searchText = isset($_REQUEST['Search']) ? $_REQUEST['Search'] : 'Search';
		$fields = new FieldSet(
	      	new TextField("Search", "", $searchText)
	  	);
		$actions = new FieldSet(
	      	new FormAction('results', 'Search')
	  	);

	  	return new SearchForm($this, "SearchForm", $fields, $actions);
	}
	
	/**
	 * Process and render search results
	 */
	function results($data, $form){
	  	$data = array(
	     	'Results' => $form->getResults(),
	     	'Query' => $form->getSearchQuery(),
	      	'Title' => 'Search Results'
	  	);

	  	return $this->customise($data)->renderWith(array('Page_results', 'Page'));
	}	
	
	function showImage () {
  if($this->isAjax) {
   return $this->renderWith("ajaxSnippet");
  }
  else {
   return Array();
  }
 }
 
  function articleMenu() {
  $whereStatement = "ParentID = 10";
  return DataObject::get("Page", $whereStatement); 
 }
 
 function newsMenu() {
  $whereStatement = "ParentID = 9";
  return DataObject::get("Page", $whereStatement); 
 }
}

?>