<?php
$val .= <<<SSVIEWER
<div class="typography">
	
SSVIEWER;
 if($item->hasValue("Results")) {  ;
 $val .= <<<SSVIEWER

	    <ul id="SearchResults">
	      
SSVIEWER;
 array_push($itemStack, $item); if($loop = $item->obj("Results")) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER

	        <li>
	            
SSVIEWER;
 if($item->hasValue("MenuTitle")) {  ;
 $val .= <<<SSVIEWER

	              <h3><a class="searchResultHeader" href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
">
SSVIEWER;
$val .=  $item->XML_val("MenuTitle",null,true) ;
 $val .= <<<SSVIEWER
</a></h3>
	            
SSVIEWER;
 } else { ;
 $val .= <<<SSVIEWER

	              <h3><a class="searchResultHeader" href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
">
SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER
</a></h3>
	            
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

			  
SSVIEWER;
 if($item->hasValue("Content")) {  ;
 $val .= <<<SSVIEWER

	          	
SSVIEWER;
$val .=  $item->obj("Content")->XML_val("FirstParagraph",array("html"),true) ;
 $val .= <<<SSVIEWER

			  
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

	          <a class="readMoreLink" href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" title="Read more about &quot;
SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER
&quot;">Read more about &quot;
SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER
&quot;...</a>
	        </li>
	      
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

	    </ul>
	  
SSVIEWER;
 } else { ;
 $val .= <<<SSVIEWER

	    <p>Sorry, your search query did not return any results.</p>
	  
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER


	  
SSVIEWER;
 if($item->obj("Results",null,true)->hasValue("MoreThanOnePage")) {  ;
 $val .= <<<SSVIEWER

	    <div id="PageNumbers">
	      
SSVIEWER;
 if($item->obj("Results",null,true)->hasValue("NotLastPage")) {  ;
 $val .= <<<SSVIEWER

	        <a class="next" href="
SSVIEWER;
$val .=  $item->obj("Results",null,true)->XML_val("NextLink",null,true) ;
 $val .= <<<SSVIEWER
" title="View the next page">Next</a>
	      
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

	      
SSVIEWER;
 if($item->obj("Results",null,true)->hasValue("NotFirstPage")) {  ;
 $val .= <<<SSVIEWER

	        <a class="prev" href="
SSVIEWER;
$val .=  $item->obj("Results",null,true)->XML_val("PrevLink",null,true) ;
 $val .= <<<SSVIEWER
" title="View the previous page">Prev</a>
	      
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

	      <span>
	        
SSVIEWER;
 array_push($itemStack, $item); if(($loop = $item->obj("Results")) && ($loop = $loop->obj("SummaryPagination", array("5")))) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER

	          
SSVIEWER;
 if($item->hasValue("CurrentBool")) {  ;
 $val .= <<<SSVIEWER

	            
SSVIEWER;
$val .=  $item->XML_val("PageNum",null,true) ;
 $val .= <<<SSVIEWER

	          
SSVIEWER;
 } else { ;
 $val .= <<<SSVIEWER

	            <a href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" title="View page number 
SSVIEWER;
$val .=  $item->XML_val("PageNum",null,true) ;
 $val .= <<<SSVIEWER
">
SSVIEWER;
$val .=  $item->XML_val("PageNum",null,true) ;
 $val .= <<<SSVIEWER
</a>
	          
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

	        
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

	      </span>
      
	    </div>
	 
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

</div>
SSVIEWER;
