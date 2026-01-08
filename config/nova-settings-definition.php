<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nova Settings Definition
    |--------------------------------------------------------------------------
    |
    | Define all available settings with their types, validation rules, and groups.
    | Settings will be persisted using the model configured in 'nova-settings.php'
    |
    */

    'settings' => [
        // Company Info Group
        [
            'name' => 'company_name',
            'label' => 'Company Name',
            'type' => 'text',
            'group' => 'Company Info',
            'validation' => 'required|string|max:255',
            'placeholder' => 'Enter company name',
        ],
        [
            'name' => 'company_address',
            'label' => 'Company Address',
            'type' => 'textarea',
            'group' => 'Company Info',
            'validation' => 'nullable|string|max:1000',
            'placeholder' => 'Enter company address',
        ],
        [
            'name' => 'company_phone',
            'label' => 'Company Phone',
            'type' => 'text',
            'group' => 'Company Info',
            'validation' => 'nullable|string|max:20',
            'placeholder' => 'Enter phone number',
        ],
        [
            'name' => 'company_email',
            'label' => 'Company Email',
            'type' => 'email',
            'group' => 'Company Info',
            'validation' => 'nullable|email|max:255',
            'placeholder' => 'Enter email address',
        ],

        // Features Group
        [
            'name' => 'woocommerce_enabled',
            'label' => 'Enable WooCommerce Integration',
            'type' => 'boolean',
            'group' => 'Features',
            'validation' => 'nullable|in:0,1',
        ],
        [
            'name' => 'fakturomania_enabled',
            'label' => 'Enable Fakturomania Integration',
            'type' => 'boolean',
            'group' => 'Features',
            'validation' => 'nullable|in:0,1',
        ],

        // Advanced Group
        [
            'name' => 'maintenance_mode',
            'label' => 'Maintenance Mode',
            'type' => 'boolean',
            'group' => 'Advanced',
            'validation' => 'nullable|in:0,1',
        ],
        [
            'name' => 'max_login_attempts',
            'label' => 'Max Login Attempts',
            'type' => 'number',
            'group' => 'Advanced',
            'validation' => 'nullable|integer|min:1|max:100',
            'placeholder' => '5',
        ],
    ],
];
