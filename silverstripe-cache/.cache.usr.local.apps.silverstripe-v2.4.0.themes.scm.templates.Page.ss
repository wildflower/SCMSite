<?php
$val .= <<<SSVIEWER
<!DOCTYPE html >
<html lang="en" >
  <head>
	<meta charset="utf-8" />
		
SSVIEWER;
$val .=  SSViewer::get_base_tag($val); ;
 $val .= <<<SSVIEWER

		<title>
SSVIEWER;
 if($item->hasValue("MetaTitle")) {  ;
 $val .= <<<SSVIEWER

SSVIEWER;
$val .=  $item->XML_val("MetaTitle",null,true) ;
 $val .= <<<SSVIEWER

SSVIEWER;
 } else { ;
 $val .= <<<SSVIEWER

SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER

SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER
 &raquo; SCM Group</title>
		
SSVIEWER;
$val .=  $item->XML_val("MetaTags",array("false"),true) ;
 $val .= <<<SSVIEWER

		<link rel="shortcut icon" href="/favicon.ico" />
		
		
SSVIEWER;
 Requirements::themedCSS("layout"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::themedCSS("style"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::themedCSS("mysuperfish"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::CSS("mysite/css/scmfade.css"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::javascript("sapphire/thirdparty/jquery/jquery-1.6.1.js"); ;
 $val .= <<<SSVIEWER
				
		
SSVIEWER;
 Requirements::javascript("mysite/javascript/hoverIntent.js"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::javascript("mysite/javascript/superfish.js"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::javascript("mysite/javascript/supersubs.js"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::javascript("mysite/javascript/mymenu.js"); ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 Requirements::javascript("mysite/javascript/scmfade.js"); ;
 $val .= <<<SSVIEWER

		<!--[if IE ]> 
			<style type="text/css">
			 @import url(mysite/css/ie.css);
			</style> 
		<![endif]-->
		<meta name="google-site-verification" content="AbXuXHH4n_byNWZKBAABXSUBLo6Kxf4bMkMpuslsjvU" />
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-706457-8']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

	</head>
<body id="page1">
	<div class="tail-top">
	<div class="main">
		<div id="header">
		        <div class="indent1">
        			<div class="fright"><!--<a href="http://www.linkedin.com/company/scm-group?trk=fc_badge"><img src="http://static01.linkedin.com/scds/common/u/img/webpromo/btn_cofollow_badge.png"  alt="SCM Group on LinkedIn" /></a><a href="http://www.linkedin.com/groups?about=&gid=1831492&trk=anet_ug_grppro"><img alt="" src="themes/scm/images/icons/LinkedIn_Logo30px.png" /></a>--> &nbsp;| &nbsp;<a href="/admin">User Login</a> &nbsp;|<!-- &nbsp;<a href="sitemap.xml">Sitemap</a> &nbsp;| &nbsp;<a href="
SSVIEWER;
$val .=  SSViewer::$options['rewriteHashlinks'] ? Convert::raw2att( $_SERVER['REQUEST_URI'] ) : "" ;
 $val .= <<<SSVIEWER
#">Career</a>-->
				</div> 
		        </div>
		        <div class="indent2">Risk for Reward Specialists: "Accepting the risks, delivering the rewards"</div>
			        <a href="/"><img alt="" src="themes/scm/images/logo.png" class="logo" /></a><br />      
			 <!-- [if IE6]><div id="IE6"><![endif]-->
<div id="menu">
<ul class="sf-menu">
	
SSVIEWER;
 array_push($itemStack, $item); if($loop = $item->obj("Menu", array("1"))) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER

		
SSVIEWER;
 if($item->hasValue("Children")) {  ;
 $val .= <<<SSVIEWER

			<li class="
SSVIEWER;
$val .=  $item->XML_val("LinkingMode",null,true) ;
 $val .= <<<SSVIEWER
 
SSVIEWER;
 if($item->hasValue("First")) {  ;
 $val .= <<<SSVIEWER
first
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER
"><a href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" class="
SSVIEWER;
$val .=  $item->XML_val("LinkingMode",null,true) ;
 $val .= <<<SSVIEWER
" title="View more info about 
SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER
" ><span>
SSVIEWER;
$val .=  $item->XML_val("MenuTitle",null,true) ;
 $val .= <<<SSVIEWER
</span><!--[if gte IE 7]><!--></a><!--<![endif]-->
				<!--[if lte IE 6]><table><tr><td><![endif]-->
				<ul>
					
SSVIEWER;
 array_push($itemStack, $item); if($loop = $item->obj("Children")) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER

						<li><a href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" title="View more about 
SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER
" class="fly 
SSVIEWER;
$val .=  $item->XML_val("LinkingMode",null,true) ;
 $val .= <<<SSVIEWER
">
SSVIEWER;
$val .=  $item->XML_val("MenuTitle",null,true) ;
 $val .= <<<SSVIEWER
</a></li>
					
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

				</ul>
			<!--[if lte IE 6]></td></tr></table></a><![endif]-->
			</li>
		
SSVIEWER;
 } else { ;
 $val .= <<<SSVIEWER
	
			<li class="
SSVIEWER;
$val .=  $item->XML_val("LinkingMode",null,true) ;
 $val .= <<<SSVIEWER
 
SSVIEWER;
 if($item->hasValue("First")) {  ;
 $val .= <<<SSVIEWER
first
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER
"><a href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" class="
SSVIEWER;
$val .=  $item->XML_val("LinkingMode",null,true) ;
 $val .= <<<SSVIEWER
" title="View more info about 
SSVIEWER;
$val .=  $item->XML_val("Title",null,true) ;
 $val .= <<<SSVIEWER
"><span>
SSVIEWER;
$val .=  $item->XML_val("MenuTitle",null,true) ;
 $val .= <<<SSVIEWER
</span></a></li>
		
SSVIEWER;
 }  ;
 $val .= <<<SSVIEWER

	
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

</ul>
</div>
<!-- [if IE6]></div><![endif]-->

		</div> <!-- header -->

	
SSVIEWER;
$val .=  $item->XML_val("Layout",null,true) ;
 $val .= <<<SSVIEWER
						

	<div class="row2">
            <div class="box1">
                <div class="border-right">
                    <div class="corner-top-right">
                        <div class="corner-top-left">
                            <div class="indent-box">
                            	<div class="container">
                                	<div class="col-1">
                                    		<h2>Pages</h2>
                                    		<ul class="ul1">
 	
SSVIEWER;
 array_push($itemStack, $item); if($loop = $item->obj("Menu", array("1"))) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER
	  
  		<li><a href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" title="Go to the 
SSVIEWER;
$val .=  $item->obj("Title",null,true)->XML_val("XML",null,true) ;
 $val .= <<<SSVIEWER
 page"><span>
SSVIEWER;
$val .=  $item->obj("MenuTitle",null,true)->XML_val("XML",null,true) ;
 $val .= <<<SSVIEWER
</span></a></li>
   	
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

 </ul>
                                    	</div>
					<div class="col-1">
<!---                                    		<h2>Articles</h2>
                                    		<ul class="ul1">
                                    		
SSVIEWER;
 array_push($itemStack, $item); if($loop = $item->obj("articleMenu")) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER

							<li class="
SSVIEWER;
$val .=  $item->XML_val("FirstLast",null,true) ;
 $val .= <<<SSVIEWER
 
SSVIEWER;
$val .=  $item->XML_val("LinkingMode",null,true) ;
 $val .= <<<SSVIEWER
"><a href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" title="Go to the 
SSVIEWER;
$val .=  $item->obj("Title",null,true)->XML_val("XML",null,true) ;
 $val .= <<<SSVIEWER
 page"><span>
SSVIEWER;
$val .=  $item->obj("MenuTitle",null,true)->XML_val("XML",null,true) ;
 $val .= <<<SSVIEWER
</span></a></li>
						
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

						</ul>--->
					</div>
					<div class="col-1">
<!---                                    		<h2>News</h2>
                                    		<ul class="ul1">
                                    		
SSVIEWER;
 array_push($itemStack, $item); if($loop = $item->obj("newsMenu")) foreach($loop as $key => $item) { ;
 $val .= <<<SSVIEWER

							<li class="
SSVIEWER;
$val .=  $item->XML_val("FirstLast",null,true) ;
 $val .= <<<SSVIEWER
 
SSVIEWER;
$val .=  $item->XML_val("LinkingMode",null,true) ;
 $val .= <<<SSVIEWER
"><a href="
SSVIEWER;
$val .=  $item->XML_val("Link",null,true) ;
 $val .= <<<SSVIEWER
" title="Go to the 
SSVIEWER;
$val .=  $item->obj("Title",null,true)->XML_val("XML",null,true) ;
 $val .= <<<SSVIEWER
 page"><span>
SSVIEWER;
$val .=  $item->obj("MenuTitle",null,true)->XML_val("XML",null,true) ;
 $val .= <<<SSVIEWER
</span></a></li>
						
SSVIEWER;
 } $item = array_pop($itemStack); ;
 $val .= <<<SSVIEWER

						</ul> --->
					</div>
                                	<div class="col-1"></div>
					<div class="col-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- row2 -->

    </div> <!-- content in layout -->   



   <div id="footer">
        <div class="bot-left">
            <div class="bot-right">
                <div class="indent-footer">SCM Group (c) 2010 &nbsp;| &nbsp;<a href="
SSVIEWER;
$val .=  SSViewer::$options['rewriteHashlinks'] ? Convert::raw2att( $_SERVER['REQUEST_URI'] ) : "" ;
 $val .= <<<SSVIEWER
#">Privacy policy</a></div>
            </div>
        </div>
    </div>  <!-- footer -->
   
</div> <!-- main -->
</div> <!-- tail-top -->

</body>
</html>

SSVIEWER;
