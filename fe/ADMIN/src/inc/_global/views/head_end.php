<?php
/**
 * head_end.php
 *
 * Author: pixelcave
 *
 * (continue) The first block of code used in every page of the template
 *
 * The reason we separated head_start.php and head_end.php is for enabling
 * us to include between them extra plugin CSS files needed only in specific pages
 *
 */
?>

    <!-- Fonts and Codebase framework -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700&display=swap">



    <script type="application/javascript" src="<?php echo $cb->assets_folder; ?>/js/codebase.core.min.js"></script>
    <script type="application/javascript" src="<?php echo $cb->assets_folder; ?>/js/codebase.app.min.js"></script>    

    <link rel="stylesheet" href="<?php echo $cb->assets_folder; ?>/css/codebase.min.css" id="css-main">
    <link rel="stylesheet" href="/ADMIN/node_modules/bootstrap-fileinput/css/fileinput.min.css" media="all" type="text/css" />   

    <link href="https://kendo.cdn.telerik.com/2020.2.513/styles/kendo.common.min.css" rel="stylesheet" />
    <link href="https://kendo.cdn.telerik.com/2020.2.513/styles/kendo.default.min.css" rel="stylesheet" />


    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/flat.min.css"> -->
    <?php if ($cb->theme) { ?>
        <link rel="stylesheet" id="css-theme" href="<?php echo $cb->assets_folder; ?>/css/themes/<?php echo $cb->theme; ?>.min.css">
    <?php } ?>
    <!-- END Stylesheets -->
  
  
    <script type="application/javascript" src="<?php echo $cb->assets_folder; ?>/js/core/fa.js"></script>
    <script type="application/javascript" src="<?php echo $cb->assets_folder; ?>/js/plugins/flatpickr/flatpickr.min.js"></script>
 
    
    
 

</head>
<body>
