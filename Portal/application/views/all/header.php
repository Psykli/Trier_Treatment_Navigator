<!DOCTYPE html>

<html lang="de">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>

    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#ffffff">

    <script type="text/javascript" src="<?php echo base_url(); ?>/dist/js/out.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>/dist/css/main.css"> 
    <link rel="stylesheet" href="<?php echo base_url();?>/css/style.css"> 
    
    
   <!-- Piwik -->
	<script type="text/javascript">
	  var _paq = _paq || [];
	  _paq.push(["setDomains", ["*.psykli120.uni-trier.de/ci_pf_0.8.0/index.php","*.psykli120/ci_pf_0.8.0/index.php"]]);
	  _paq.push(['trackPageView']);
	  _paq.push(['enableLinkTracking']);
	  (function() {
		var u="//psykli120.uni-trier.de/piwik/";
		_paq.push(['setTrackerUrl', u+'piwik.php']);
		_paq.push(['setSiteId', 2]);
		var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
	  })();
	</script>
	<noscript><p><img src="//psykli120.uni-trier.de/piwik/piwik.php?idsite=2" style="border:0;" alt="" /></p></noscript>
	<!-- End Piwik Code -->
  
  <?php include 'application/views/all/noscript.php';?>
  

    <!-- inject:js -->
    <!-- endinject -->

    <!-- inject:css -->
    <!-- endinject -->
</head>

<body>
