<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'crb_load');
add_action('carbon_fields_register_fields', 'crb_attach_theme_options');

function crb_load()
{
    \Carbon_Fields\Carbon_Fields::boot();
}

function crb_attach_theme_options()
{
    Container::make('theme_options', __('Result Options'))
        ->set_icon('dashicons-email-alt')
        ->add_fields(
            array(
                Field::make('text', 'mrz_cre_quiz_id', 'Quiz Id'),
                Field::make('text', 'mrz_cre_admin_email', 'Admin Email'),
                Field::make('text', 'mrz_cre_email_subject', 'Email Subject'),
                Field::make('textarea', 'mrz_cre_email_text', 'Email Text'),
            )
        );
}
