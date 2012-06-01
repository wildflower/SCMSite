<?php
$val .= <<<SSVIEWER
 <div id="content">
    	
     	<div class="row1">
            <div class="box">
                <div class="border-top">
                    <div class="border-bottom">
                        <div class="border-right">
                            <div class="border-left">
                                <div class="corner-top-right">
                                    <div class="corner-top-left">
                                        <div class="corner-bottom-left">
                                            <div class="corner-bottom-right"> 
                                                <div class="indent-box">

<div class="container bg">
	<div class="">
		<div class="indent-col">
			<div class="typography">
	
SSVIEWER;
 if($item->hasValue("Menu",array("2"))) {  ;
 $val .= <<<SSVIEWER

		<div id="Content">
	
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

			
	
SSVIEWER;
 if($item->hasValue("Level",array("2"))) {  ;
 $val .= <<<SSVIEWER

	  	
	
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

	
		<h2>
SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER
</h2>	
		
SSVIEWER;
$val .=  $item->XML_val("Content",null,true) ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
$val .=  $item->XML_val("PageComments",null,true) ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
$val .=  $item->XML_val("Form",null,true) ;
 $val .= <<<SSVIEWER

	
SSVIEWER;
 if($item->hasValue("Menu",array("2"))) {  ;
 $val .= <<<SSVIEWER

		</div>
	
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

			</div>
		</div>
	</div>	
</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

SSVIEWER;
