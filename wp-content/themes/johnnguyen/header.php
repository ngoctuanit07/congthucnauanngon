<!DOCTYPE html>
<html <?php language_attributes() ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>
        <?php if (is_home()): ?>
        <?php bloginfo('name') ?>
        <?php else: ?>
        <?php wp_title('', true,''); ?>
        <?php endif ?>
    </title>

    <?php if (is_home()): ?>
    <!-- Khi ở trang chủ -->
    <meta name="description" content="<?php bloginfo('description') ?>" />
    <?php endif ?>

    <link href="<?php echo get_template_directory_uri() ?>/css/fontawesome.min.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri() ?>/css/all.min.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri() ?>/css/brands.min.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri() ?>/css/regular.min.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri() ?>/css/solid.min.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri() ?>/css/svg-with-js.min.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri() ?>/css/v4-shims.min.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="<?php echo get_template_directory_uri() ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="<?php echo get_template_directory_uri() ?>/css/blog-home.css" rel="stylesheet">

    <link href="<?php echo get_template_directory_uri() ?>/style.css" rel="stylesheet">

    <?php wp_head() ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-165859186-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-165859186-1');
    </script>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NQ38DM9');</script>
<!-- End Google Tag Manager -->

</head>

<body style="padding-top: 0" <?php body_class() ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NQ38DM9"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark <?php echo is_home() ? 'mb-3' : '' ?>">
        <div class="container">
            <a class="navbar-brand" href="<?php echo home_url() ?>">John Nguyen Blog</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">

                <?php 
            wp_nav_menu( array(
                'theme_location'  => 'header-menu', // Gọi menu đã đăng ký trong function
                'depth'           => 2,     // Cấu hình dropdown 2 cấp
                'container'       => false, // Thẻ div bọc menu
                'menu_class'      => 'navbar-nav ml-auto', // Class của nav bootstrap
                'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                'walker'          => new WP_Bootstrap_Navwalker()
            ) );
          ?>

            </div>
        </div>
    </nav>

    <?php mini_blog_breadcrumbs() ?>