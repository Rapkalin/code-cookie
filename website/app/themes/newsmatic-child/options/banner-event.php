<?php

add_action('customize_register', 'newsmatic_child_customize_event_banner', 10);
/* add_action('customize_register', function (WP_Customize_Manager $wp_customize) {
    $wp_customize->add_section('newsmatic_child_customize_banner', [
        'title' => 'Personnalisation de la bannière',
    ]);

    $wp_customize->add_setting('newsmatic_child_banner_title');
    $wp_customize->add_control('newsmatic_child_banner_title', [
        'section' => 'newsmatic_child_customize_banner',
        'setting' => 'newsmatic_child_banner_title',
        'label' => 'Titre de la bannière',
        'sanitize_callback' => 'sanitize_title'
    ]);

    $wp_customize->add_setting('newsmatic_child_banner_url');
    $wp_customize->add_control('newsmatic_child_banner_url', [
        'section' => 'newsmatic_child_customize_banner',
        'setting' => 'newsmatic_child_banner_url',
        'label' => 'URL de la bannière',
        'sanitize_callback' => 'sanitize_url'
    ]);

    $wp_customize->add_setting('newsmatic_child_banner_active');
    $wp_customize->add_control('newsmatic_child_banner_active', [
        'section' => 'newsmatic_child_customize_banner',
        'setting' => 'newsmatic_child_banner_active',
        'label' => 'Activation de la bannière',
        'type'     => 'checkbox',

    ]);
});*/

function newsmatic_child_customize_event_banner (WP_Customize_Manager $wp_customize) {

    $wp_customize->add_section('newsmatic_child_customize_banner', [
        'title' => 'Personnalisation de la bannière',
        'priority' => 3
    ]);

    $wp_customize->add_setting('newsmatic_child_banner_title');
    $wp_customize->add_control('newsmatic_child_banner_title', [
        'section' => 'newsmatic_child_customize_banner',
        'setting' => 'newsmatic_child_banner_title',
        'label' => 'Titre de la bannière',
        'sanitize_callback' => 'sanitize_title',
        'type' => 'textarea'
    ]);

    $wp_customize->add_setting('newsmatic_child_banner_url');
    $wp_customize->add_control('newsmatic_child_banner_url', [
        'section' => 'newsmatic_child_customize_banner',
        'setting' => 'newsmatic_child_banner_url',
        'label' => 'URL de la bannière',
        'sanitize_callback' => 'sanitize_url'
    ]);

    $wp_customize->add_setting('newsmatic_child_banner_active');
    $wp_customize->add_control('newsmatic_child_banner_active', [
        'section' => 'newsmatic_child_customize_banner',
        'setting' => 'newsmatic_child_banner_active',
        'label' => 'Activation de la bannière',
        'type'     => 'checkbox',

    ]);

}