<?php
$val .= <<<SSVIEWER
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	
SSVIEWER;
 array_push($itemStack, $item); if($loop = $item->obj("Items")) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER

	<url>
		<loc>
SSVIEWER;
$val .=  $item->XML_val("AbsoluteLink",null,true) ;
 $val .= <<<SSVIEWER
</loc>
		<lastmod>
SSVIEWER;
$val .=  $item->obj("LastEdited")->XML_val("Format",array("c"),true) ;
 $val .= <<<SSVIEWER
</lastmod>
		
SSVIEWER;
 if($item->hasValue("ChangeFreq")) {  ;
 $val .= <<<SSVIEWER
<changefreq>
SSVIEWER;
$val .=  $item->XML_val("ChangeFreq",null,true) ;
 $val .= <<<SSVIEWER
</changefreq>
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 if($item->hasValue("Priority")) {  ;
 $val .= <<<SSVIEWER
<priority>
SSVIEWER;
$val .=  $item->XML_val("Priority",null,true) ;
 $val .= <<<SSVIEWER
</priority>
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

	</url>
	
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

</urlset>
SSVIEWER;
