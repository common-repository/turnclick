<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('turnclick_customer_id');
delete_option('turnclick_customer_email');
