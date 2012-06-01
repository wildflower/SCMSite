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
	<div class="col-1">
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
	<div class="col-2">
		<div class="indent-col">
		
<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
<script type="IN/CompanyProfile" data-id="1183481" data-format="inline"></script>
<script type="IN/MemberProfile" data-id="http://nz.linkedin.com/pub/miles-flower/4/575/5b3" data-format="inline"></script>
<script type="IN/MemberProfile" data-id="http://nz.linkedin.com/pub/brendan-mcfadden/6/1ab/215" data-format="inline"></script> 
<script type="IN/MemberProfile" data-id="http://nz.linkedin.com/pub/grant-loudon/36/129/a52" data-format="inline"></script> 
<script type="IN/MemberProfile" data-id="http://nz.linkedin.com/pub/maggie-christian/5/979/27a" data-format="inline"></script>
<script type="IN/MemberProfile" data-id="http://www.linkedin.com/pub/mark-anderson/9/63b/820" data-format="inline"></script>
<script type="IN/MemberProfile" data-id="http://www.linkedin.com/in/haydnflower" data-format="inline"></script>
<script type="IN/MemberProfile" data-id="http://nz.linkedin.com/in/itspersonal" data-format="inline"></script>
<script type="IN/MemberProfile" data-id="http://nz.linkedin.com/pub/joy-pryor/49/9a6/667" data-format="inline"></script>
<script type="IN/MemberProfile" data-id="http://nz.linkedin.com/pub/karl-may/36/130/734" data-format="inline"></script> 

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
