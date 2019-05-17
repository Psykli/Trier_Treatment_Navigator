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
    
    <!-- jQuery: 1.11.1/3: jQuery.validate on submit not working in firefox (34.0.5) -->
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.10.2.js"></script> -->
    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.10.4.custom.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <!-- <script src="http://code.jquery.com/ui/1.10.4/themes/black-tie/jquery-ui.css"></script> -->

    <!-- Das neueste kompilierte und minimierte CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optionales Theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!-- Das neueste kompilierte und minimierte JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <!-- jquery ui css : -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery-ui-1.10.4.custom.min.css" type="text/css" media="screen" charset="utf-8">

    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js"></script>
    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.js"></script>

    <!-- jPLayer -->

    <link rel="stylesheet" href="<?php echo base_url(); ?>css/jplayer.blue.monday.css" type="text/css" media="screen" charset="utf-8">
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.jplayer.min.js"></script>

    <link href="http://vjs.zencdn.net/4.12/video-js.css" rel="stylesheet">
    <script src="http://vjs.zencdn.net/4.12/video.js"></script>

    <!-- Eigene Javascript Methoden -->
    <script type="text/javascript" src="<?php echo base_url(); ?>js/ownMethods.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/mithril/0.2.0/mithril.min.js"></script>

    <!-- Eigene Style-Eigenschaften -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/css/style.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/css/style_dashboard.css" type="text/css" media="screen" charset="utf-8">
    		<!-- Flaggen -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/flag-icon.min.css" type="text/css" media="screen" charset="utf-8">

    <!-- Datatables.net -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/u/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.12,b-1.2.1,b-colvis-1.2.1,b-flash-1.2.1,b-html5-1.2.1,b-print-1.2.1/datatables.min.css"/>
	  <script type="text/javascript" src="https://cdn.datatables.net/u/bs/jszip-2.5.0,pdfmake-0.1.18,dt-1.10.12,b-1.2.1,b-colvis-1.2.1,b-flash-1.2.1,b-html5-1.2.1,b-print-1.2.1/datatables.min.js"></script>

  <?php include 'application/views/all/noscript.php';?>

</head>

<body>