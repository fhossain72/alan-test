<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo !empty($page_title) ? $page_title . ' - ' : ''; ?> <?php echo $this->config->item('site_title'); ?></title>

    <link rel="icon" href="<?php echo base_url('assets/images/favicon.png'); ?>" type="image/png">

    <link rel='stylesheet' href='<?php echo site_url('assets/css/font-awesome.min.4.7.0.css'); ?>' type='text/css'/>
    <link rel="stylesheet" href="<?php echo site_url('assets/css/bootstrap.min-v4.1.3.css'); ?>" crossorigin="anonymous"/>

    <link rel='stylesheet' href='<?php echo site_url('assets/css/styles.css'); ?>' type='text/css'/>
    <link rel='stylesheet' href='<?php echo site_url('assets/css/custom.css'); ?>' type='text/css'/>
    <link rel="stylesheet" href="<?php echo site_url('assets/css/responsive.css'); ?>" type="text/css"/>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="<?php echo site_url('assets/js/bootstrap.min.v4.1.3.js'); ?>" ></script>
</head>

<header class="montserrat site-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="logo-wrapper">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="site-logo">
                                <a href="<?php echo site_url(); ?>" title="PrestigeLabs">
                                    <img src="<?php echo site_url('assets/images/cropped-logo-1.png'); ?>" class="attachment-full size-full" alt="PrestigeLabs" title="PrestigeLabs"/>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="header-top">
                                <div class="top-widgets-right">
                                    <?php
                                    if(adminLoginCheck())
                                    {
                                        ?><div class="textwidget roboto">
                                            Welcome <strong><?php echo $this->session->userdata('first_name'); ?></strong> | <a href="<?php echo site_url('auth/logout'); ?>">Logout</a>
                                        </div><?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section>
        <div class="site_menu">
            <div class="header-logo-fix">
                <a href="<?php echo site_url(); ?>"><img src="<?php echo site_url('assets/images/logo_fix.png'); ?>" alt="PrestigeLabs"/></a>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <nav class="primary-nav">
                            <div class="menu-main-menu-container">
                                <ul id="menu-main-menu" class="menu" style="float:left;">
                                    <?php $page_active = isset($page_active) ? $page_active : 'rma_manage'; ?>

                                    <?php
                                    if(adminLoginCheck())
                                    {
                                        ?><li class="<?php echo ($page_active=='rma_manage' ? 'active' : null); ?>"><a class="menu_item" href="<?php echo site_url('rma'); ?>">Return List</a></li>
                                        <li class="<?php echo ($page_active=='rma_request' ? 'active' : null); ?>"><a class="menu_item" href="<?php echo site_url('rma/request'); ?>">Submit Return</a></li><?php
                                    }
                                    else
                                    {
                                        ?><li class="active"><a class="menu_item" href="<?php echo site_url('auth/login'); ?>">Login</a></li><?php
                                    }
                                    ?>
                                </ul>
                                <ul class="menu pull-right">
                                    <?php
                                    if(adminLoginCheck())
                                    {
                                        ?><li><a class="menu_item" href="<?php echo site_url('auth/change_password'); ?>">Change Password</a></li><?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
</header>

<div class="container">
    <?php
    if(validation_errors())
    {
        ?><div class="cus_alert alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo validation_errors(); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div><?php
    }
    ?>

    <?php
    if(!empty($error_msg))
    {
        ?><div class="alert alert-block alert-danger in">
            <button data-dismiss="alert" class="close close-sm" type="button">
                <i class="icon-remove"></i>
            </button>
            <strong>Error!</strong> <?php echo $error_msg; ?>
        </div><?php
    }
    elseif(!empty($success_msg))
    {
        ?><div class="alert alert-success alert-dismissable in">
            <button data-dismiss="alert" class="close close-sm" type="button">
                <i class="icon-remove"></i>
            </button>
            <strong>Success!</strong> <?php echo $success_msg; ?>
        </div><?php
    }
    elseif($this->session->flashdata('success_msg'))
    {
        ?><div class="alert alert-block alert-success in">
        <button data-dismiss="alert" class="close close-sm" type="button">
            <i class="icon-remove"></i>
        </button>
        <strong>Success!</strong> <?php echo $this->session->flashdata('success_msg'); ?>
        </div><?php
    }
    ?>

    <?php echo !empty($layout) ? $layout : ''; ?>
</div>

<footer class="site-footer">
    <aside class="footer-widgets col-xs-12">
        <div class="container">
            <div class="footer-top">
                <section class="widget widget_text">
                    <h3 class="montserrat">CONTACT US</h3>
                    <div class="textwidget">
                        <p class="footer_contact"><b>Phone:</b> 1-800-470-7560 &nbsp;|&nbsp; <b>Email:</b> support@prestigelabs.com &nbsp;|&nbsp; <b>Address:</b> GLS Labs LLC, 30 N Gould St Ste 6466, Sheridan, WY 82801</p>
                    </div>
                </section>
            </div>
            <div class="row footer-top">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="footer-bottom">
                        <div class="copyright">Copyright &copy; 2019 Prestige Labs,  All Rights Reserved.</div>
                        <div id="payment-methods">
                            <span class="payment-method">
                                <a href="#" title="Visa">
                                    <img src="<?php echo site_url('assets/images/icon-cc-visa.png'); ?>" alt="visa">
                                </a>
                            </span>
                            <span class="payment-method">
                                <a href="#" title="American express">
                                    <img src="<?php echo site_url('assets/images/icon-cc-american-express.png'); ?>" alt="american-express">
                                </a>
                            </span>
                            <span class="payment-method">
                                <a href="#" title="Discover">
                                    <img src="<?php echo site_url('assets/images/icon-cc-discover.png'); ?>" alt="discover">
                                </a>
                            </span>
                            <span class="payment-method">
                                <a href="#" title="Mastercard">
                                    <img src="<?php echo site_url('assets/images/icon-cc-mastercard.png"'); ?> alt="mastercard">
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</footer>
</body>
</html>
