<?php

/*
 * @wordpress-plugin
 * Plugin Name: Custom Remark Emails
 * Description: Generate and send a custom email according to the admin requirement for the Quiz and Survey Mastery (QSM) plugin. This plugin is dependent on the QSM plugin and needs QSM to be installed before you can use this plugin.
 * Version: 2.1.0
 * Author: mrz
 * Author URI: https://github.com/mrz004
 * Text Domain: custom-remark-email
 */

if (!defined('ABSPATH')) {
    die("Unauthorized Access!"); // Exit if accessed directly.
}

global $wpdb;

if (!class_exists('CustomRemarkEmail')) {
    class CustomRemarkEmail
    {
        public function __construct()
        {
            define("MRZ_CRE_PLUGIN_PATH", plugin_dir_path(__FILE__));
            require MRZ_CRE_PLUGIN_PATH . "vendor/autoload.php";
        }

        public function initialize()
        {
            require_once MRZ_CRE_PLUGIN_PATH . "includes/options_page.php";
            $this->add_hooks();
        }

        public function add_hooks()
        {
            require_once MRZ_CRE_PLUGIN_PATH . 'includes/functions.php';
            add_filter('mlw_qmn_template_variable_results_page', 'filter_custom_qsm_variable', 10, 2);
            add_action('qsm_after_results_page', array($this, 'send_email_request'), 10, 2);
        }

        /**
         * {
         *     "name": "User Name",
         *     "email": "Plugin Name",
         *     "business": "Action",
         *     "phone": "Action",
         *     "remarks": [
         *            {
         *            "category": "category name",
         *            "marks": obtained marks,
         *            "remark": "remark"
         *            }
         *      ]
         * }
         */

        /**
         * Send custom remark email to the user whenever the user submits the quiz in QSM.
         *
         * This function extract the user details, and the result of the quiz, then generates a PDF file and sends it as attachment to the user.
         *
         * @param string $content The contents of the results page
         * @param array $mlw_quiz_array The array of all the results from user taking the quiz
         * @return void Does not return anything
         */
        public function send_email_request($mlw_quiz_array)
        {
            require_once MRZ_CRE_PLUGIN_PATH . 'includes/functions.php';


            // debugging
            // echo "Debug: " . json_encode($mlw_quiz_array);


            // *** Getting the user details
            // $name = get_property_value($mlw_quiz_array, 'user_name');
            // $email = get_property_value($mlw_quiz_array, 'user_email');
            $quiz_id = get_property_value($mlw_quiz_array, 'quiz_id');
            // $unique_id = get_property_value($mlw_quiz_array, 'result_unique_id');
            // $email = $mlw_quiz_array['user_email'];
            // $phone = get_property_value($mlw_quiz_array, 'user_phone');
            // $business = get_property_value($mlw_quiz_array, 'user_business');

            if ($quiz_id != carbon_get_theme_option('mrz_cre_quiz_id'))
                return;

            // *** Generate the PDF
            // $file_path = '';
            $file_path = generate_pdf($mlw_quiz_array);

            // *** Sending the email
            send_pdf_email(carbon_get_theme_option('mrz_cre_admin_email'), carbon_get_theme_option('mrz_cre_email_subject'), carbon_get_theme_option('mrz_cre_email_text'), $file_path);
            // wp_mail($email, 'Testing', 'Your plugin is working');

            // *** Deleting the generated file
            unlink($file_path);
        }
    }


    $custom_remark_email_obj = new CustomRemarkEmail;
    $custom_remark_email_obj->initialize();
}