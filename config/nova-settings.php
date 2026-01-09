<?php

/*
|--------------------------------------------------------------------------
| Nova Settings Configuration
|--------------------------------------------------------------------------
|
| This file defines the structure of tenant-scoped settings exposed via
| the NovaSettings tool in Laravel Nova. Each setting is stored as a
| key-value pair in the database and can be organized into tabs/groups.
|
| Database Setup:
| Before using this tool, publish and run the migration:
|   php artisan vendor:publish --tag=nova-settings-migration
|   php artisan migrate
|
| Supported Field Types:
| - text      : Single-line text input
| - email     : Email input with validation
| - number    : Numeric input
| - textarea  : Multi-line text area
| - boolean   : Checkbox (0/1)
| - select    : Dropdown with predefined options
|
| Conditional Visibility (vif):
| Fields can be shown/hidden based on other field values using the 'vif'
| option. When all fields in a group are hidden, the tab disappears too.
| Format: 'vif' => ['field_name', 'expected_value']
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Model
    |--------------------------------------------------------------------------
    |
    | Fully-qualified model class used to persist settings. This model should
    | have 'key' and 'value' columns (or configure keycol/valuecol below).
    | For multi-tenancy, ensure the model uses tenant connection.
    |
    */
    'model' => 'App\\Models\\Setting',

    /*
    |--------------------------------------------------------------------------
    | Database Column Names
    |--------------------------------------------------------------------------
    |
    | Customize the column names used to store setting key and value.
    | Default: 'key' for setting name, 'value' for setting data.
    |
    */
    'keycol' => 'key',
    'valuecol' => 'value',

    /*
    |--------------------------------------------------------------------------
    | Settings Definitions
    |--------------------------------------------------------------------------
    |
    | Define your application settings below. Each setting is an array with:
    |
    | - name:        Unique identifier (stored in DB key column)
    | - label:       Human-readable label shown in UI
    | - type:        Input type (text, email, number, textarea, boolean, select)
    | - group:       Tab/group name for organization
    | - validation:  Laravel validation rules (nullable recommended for optional)
    | - placeholder: Placeholder text for inputs (optional)
    | - help:        Helper text shown below the field (optional)
    | - options:     Array of ['label' => 'X', 'value' => 'Y'] for select type
    | - vif:         Conditional visibility [field_name, expected_value] (optional)
    |
    */
    'settings' => [

        /*
        |--------------------------------------------------------------------------
        | Company Info Group - Basic Organization Information
        |--------------------------------------------------------------------------
        */
        [
            'name' => 'company_name',
            'label' => 'Company Name',
            'type' => 'text',
            'group' => 'Company Info',
            'validation' => 'nullable|string|max:255',
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

        /*
        |--------------------------------------------------------------------------
        | Features Group - Module Toggles
        |--------------------------------------------------------------------------
        */
        [
            'name' => 'api_enabled',
            'label' => 'Enable API Integration',
            'type' => 'boolean',
            'group' => 'Features',
            'validation' => 'nullable|in:0,1',
        ],

        /*
        |--------------------------------------------------------------------------
        | Integration Settings - Provider Selection
        |--------------------------------------------------------------------------
        |
        | This select field determines which external system handles integrations.
        | When a provider is selected, provider-specific tabs will appear.
        |
        */
        [
            'name' => 'integration_provider',
            'label' => 'Integration Provider',
            'type' => 'select',
            'group' => 'Integration Settings',
            'validation' => 'nullable|string|in:internal,external_api,third_party',
            'options' => [
                ['label' => 'Internal System', 'value' => 'internal'],
                ['label' => 'External API', 'value' => 'external_api'],
                ['label' => 'Third Party Service', 'value' => 'third_party'],
            ],
            'placeholder' => 'Choose provider',
            'help' => 'Select which system handles integrations for this application.',
        ],

        /*
        |--------------------------------------------------------------------------
        | Integration Settings - External API Configuration
        |--------------------------------------------------------------------------
        |
        | These fields appear only when 'external_api' is selected above.
        | The 'vif' option controls visibility: ['field_name', 'expected_value']
        |
        | When provider changes, this tab disappears/reappears dynamically.
        |
        */
        [
            'name' => 'api_key',
            'label' => 'API Key',
            'type' => 'text',
            'group' => 'Integration Settings - External API',
            'validation' => 'nullable|string|max:255',
            'help' => 'API key for authentication',
            'vif' => ['integration_provider', 'external_api'],
        ],
        [
            'name' => 'api_secret',
            'label' => 'API Secret',
            'type' => 'text',
            'group' => 'Integration Settings - External API',
            'validation' => 'nullable|string|max:255',
            'help' => 'API secret for secure authentication',
            'vif' => ['integration_provider', 'external_api'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Advanced Settings
        |--------------------------------------------------------------------------
        */
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

