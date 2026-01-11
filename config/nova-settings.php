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
    | Tool Title
    |--------------------------------------------------------------------------
    |
    | Customize the title displayed in the Nova menu and page header.
    | Default: 'NovaSettings'
    |
    */
    'title' => 'NovaSettings',

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
    | Tab Ordering
    |--------------------------------------------------------------------------
    |
    | Control the order of tabs/groups in the NovaSettings UI. Provide an array
    | of group names in the exact order you want them to appear. Any groups not
    | listed here will be appended alphabetically after the ordered groups.
    | Leave empty to keep the default alphabetical ordering for all groups.
    |
    */
    'group_order' => [],

    /*
    |--------------------------------------------------------------------------
    | Settings Definitions
    |--------------------------------------------------------------------------
    |
    | Define your application settings below. Each setting is an array with:
    |
    | - name:          Unique identifier (stored in DB key column)
    | - label:         Default label (English fallback)
    | - type:          Input type (text, email, number, textarea, boolean, select)
    | - group:         Tab/group name for organization
    | - validation:    Laravel validation rules (nullable recommended for optional)
    | - placeholder:   Default placeholder text (English fallback, optional)
    | - help:          Default helper text (English fallback, optional)
    | - options:       Array of ['label' => 'X', 'value' => 'Y'] for select type
    | - vif:           Conditional visibility [field_name, expected_value] (optional)
    |
    | Locale-Specific Translations:
    | Add locale-specific overrides using keys like 'pl_label', 'pl_placeholder', 'pl_help':
    |
    | Example:
    | [
    |     'name' => 'company_name',
    |     'label' => 'Company Name',          // Default (English) fallback
    |     'pl_label' => 'Nazwa Firmy',        // Polish label
    |     'placeholder' => 'Enter company',   // Default placeholder
    |     'pl_placeholder' => 'Wpisz nazwę',  // Polish placeholder
    |     'help' => 'Your business name',     // Default help text
    |     'pl_help' => 'Nazwa Twojej firmy',  // Polish help text
    | ]
    |
    | The system automatically loads the current APP_LOCALE and looks for
    | locale-specific keys. If found, uses them; otherwise falls back to
    | the default label/placeholder/help values.
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
            'pl_label' => 'Nazwa Firmy',
            'type' => 'text',
            'group' => 'Company Info',
            'validation' => 'nullable|string|max:255',
            'placeholder' => 'Enter company name',
            'pl_placeholder' => 'Wpisz nazwę firmy',
        ],
        [
            'name' => 'company_address',
            'label' => 'Company Address',
            'pl_label' => 'Adres Firmy',
            'type' => 'textarea',
            'group' => 'Company Info',
            'validation' => 'nullable|string|max:1000',
            'placeholder' => 'Enter company address',
            'pl_placeholder' => 'Wpisz adres firmy',
        ],
        [
            'name' => 'company_phone',
            'label' => 'Company Phone',
            'pl_label' => 'Telefon Firmy',
            'type' => 'text',
            'group' => 'Company Info',
            'validation' => 'nullable|string|max:20',
            'placeholder' => 'Enter phone number',
            'pl_placeholder' => 'Wpisz numer telefonu',
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

