<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nova Settings Configuration
    |--------------------------------------------------------------------------
    |
    | Define the model used to persist settings and all available setting definitions.
    | Customize this file in your host app to define project-specific settings.
    |
    */

    // Fully-qualified model class used to persist settings
    'model' => 'App\\Models\\Setting',

    // Define all available settings with their types, validation rules, and groups
    'settings' => [
        /*
        |--------------------------------------------------------------------------
        | Example Settings (Uncomment and customize for your project)
        |--------------------------------------------------------------------------
        |
        | Each setting definition should have:
        | - name: database key (stored in settings table)
        | - label: display label in form
        | - type: form field type (text, email, number, textarea, boolean, select)
        | - group: tab/section grouping
        | - validation: Laravel validation rules
        | - placeholder: optional input placeholder
        | - help: optional helper text
        | - options: for select fields, array of {label, value}
        |
        */

        // Company Info Group
        // [
        //     'name' => 'company_name',
        //     'label' => 'Company Name',
        //     'type' => 'text',
        //     'group' => 'Company Info',
        //     'validation' => 'required|string|max:255',
        //     'placeholder' => 'Enter company name',
        // ],
        // [
        //     'name' => 'company_address',
        //     'label' => 'Company Address',
        //     'type' => 'textarea',
        //     'group' => 'Company Info',
        //     'validation' => 'nullable|string|max:1000',
        //     'placeholder' => 'Enter company address',
        // ],
        // [
        //     'name' => 'company_phone',
        //     'label' => 'Company Phone',
        //     'type' => 'text',
        //     'group' => 'Company Info',
        //     'validation' => 'nullable|string|max:20',
        //     'placeholder' => 'Enter phone number',
        // ],
        // [
        //     'name' => 'company_email',
        //     'label' => 'Company Email',
        //     'type' => 'email',
        //     'group' => 'Company Info',
        //     'validation' => 'nullable|email|max:255',
        //     'placeholder' => 'Enter email address',
        // ],

        // Features Group
        // [
        //     'name' => 'woocommerce_enabled',
        //     'label' => 'Enable WooCommerce Integration',
        //     'type' => 'boolean',
        //     'group' => 'Features',
        //     'validation' => 'nullable|in:0,1',
        // ],
        // [
        //     'name' => 'fakturomania_enabled',
        //     'label' => 'Enable Fakturomania Integration',
        //     'type' => 'boolean',
        //     'group' => 'Features',
        //     'validation' => 'nullable|in:0,1',
        // ],

        // Advanced Group
        // [
        //     'name' => 'maintenance_mode',
        //     'label' => 'Maintenance Mode',
        //     'type' => 'boolean',
        //     'group' => 'Advanced',
        //     'validation' => 'nullable|in:0,1',
        // ],
        // [
        //     'name' => 'max_login_attempts',
        //     'label' => 'Max Login Attempts',
        //     'type' => 'number',
        //     'group' => 'Advanced',
        //     'validation' => 'nullable|integer|min:1|max:100',
        //     'placeholder' => '5',
        // ],
    ],
];

