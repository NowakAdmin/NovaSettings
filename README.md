# Nova Settings Tool

A modern, tab-based settings management tool for Laravel Nova with conditional field visibility, real-time validation, and multi-tenancy support.

## Features

✨ **Tab-Based Organization** — Group settings into logical tabs that adapt dynamically  
🎨 **Light & Dark Theme** — Fully supports Nova's light and dark modes  
🔄 **Conditional Visibility** — Show/hide fields based on other field values  
📝 **Real-Time Validation** — Instant feedback with Laravel validation rules  
🏢 **Multi-Tenancy Ready** — Tenant-scoped settings via Spatie Multitenancy  
💾 **Smart Saving** — Only saves changed values, not entire form  
🚀 **Modern UI** — Built with Vue 3, Inertia.js, and Tailwind CSS

## Installation

### 1. Install via Composer

```bash
composer require nowakadmin/nova-settings
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="NowakAdmin\NovaSettings\ToolServiceProvider"
```

This creates `config/nova-settings.php` where you define your settings schema.

### 3. Database Setup

Ensure you have a settings table with at minimum:
- `key` column (string) — Setting name/identifier
- `value` column (string/text) — Setting value
- Tenant-scoped if using multi-tenancy

Example migration:
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->timestamps();
});
```

### 4. Register Tool in Nova

In `app/Providers/NovaServiceProvider.php`:

```php
use NowakAdmin\NovaSettings\NovaSettings;

public function tools()
{
    return [
        new NovaSettings,
    ];
}
```

## Configuration

### Basic Setup

Edit `config/nova-settings.php`:

```php
return [
    'model' => 'App\\Models\\Setting', // Your settings model
    'keycol' => 'key',                 // Column storing setting name
    'valuecol' => 'value',             // Column storing setting value
    
    'settings' => [
        // Define your settings here
    ],
];
```

### Defining Settings

Each setting is an array with these properties:

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `name` | string | ✅ | Unique identifier (stored in database) |
| `label` | string | ✅ | Human-readable label shown in UI |
| `type` | string | ✅ | Field type: `text`, `email`, `number`, `textarea`, `boolean`, `select` |
| `group` | string | ✅ | Tab/group name for organization |
| `validation` | string | ✅ | Laravel validation rules |
| `placeholder` | string | ❌ | Placeholder text for inputs |
| `help` | string | ❌ | Helper text displayed below field |
| `options` | array | ❌ | Array of `['label' => '', 'value' => '']` for `select` type |
| `vif` | array | ❌ | Conditional visibility: `['field_name', 'expected_value']` |

### Field Types

#### Text Input
```php
[
    'name' => 'company_name',
    'label' => 'Company Name',
    'type' => 'text',
    'group' => 'Company Info',
    'validation' => 'required|string|max:255',
    'placeholder' => 'Enter company name',
]
```

#### Email Input
```php
[
    'name' => 'contact_email',
    'label' => 'Contact Email',
    'type' => 'email',
    'group' => 'Contact',
    'validation' => 'nullable|email|max:255',
    'placeholder' => 'email@example.com',
]
```

#### Number Input
```php
[
    'name' => 'max_users',
    'label' => 'Maximum Users',
    'type' => 'number',
    'group' => 'Limits',
    'validation' => 'nullable|integer|min:1|max:1000',
]
```

#### Textarea
```php
[
    'name' => 'company_description',
    'label' => 'Company Description',
    'type' => 'textarea',
    'group' => 'Company Info',
    'validation' => 'nullable|string|max:2000',
    'placeholder' => 'Describe your company...',
]
```

#### Boolean / Checkbox
```php
[
    'name' => 'maintenance_mode',
    'label' => 'Enable Maintenance Mode',
    'type' => 'boolean',
    'group' => 'System',
    'validation' => 'nullable|in:0,1',
]
```

#### Select / Dropdown
```php
[
    'name' => 'invoice_provider',
    'label' => 'Invoice Handling Provider',
    'type' => 'select',
    'group' => 'Integrations',
    'validation' => 'required|in:internal,other,another',
    'options' => [
        ['label' => 'Internal System', 'value' => 'internal'],
        ['label' => 'Other', 'value' => 'other'],
        ['label' => 'Another', 'value' => 'another'],
    ],
    'placeholder' => 'Choose provider',
    'help' => 'Select which system handles invoices.',
]
```

## Conditional Visibility

Show or hide fields based on other field values using the `vif` option.

### Example: Provider-Specific Settings

```php
'settings' => [
    // Provider selector
    [
        'name' => 'payment_gateway',
        'label' => 'Payment Gateway',
        'type' => 'select',
        'group' => 'Payments',
        'validation' => 'required|in:stripe,paypal,manual',
        'options' => [
            ['label' => 'Stripe', 'value' => 'stripe'],
            ['label' => 'PayPal', 'value' => 'paypal'],
            ['label' => 'Manual', 'value' => 'manual'],
        ],
    ],
    
    // Stripe-specific fields (only visible when Stripe is selected)
    [
        'name' => 'stripe_api_key',
        'label' => 'Stripe API Key',
        'type' => 'text',
        'group' => 'Payments - Stripe',
        'validation' => 'nullable|string|max:255',
        'vif' => ['payment_gateway', 'stripe'], // Show only when payment_gateway = stripe
    ],
    [
        'name' => 'stripe_secret',
        'label' => 'Stripe Secret',
        'type' => 'text',
        'group' => 'Payments - Stripe',
        'validation' => 'nullable|string|max:255',
        'vif' => ['payment_gateway', 'stripe'],
    ],
    
    // PayPal-specific fields
    [
        'name' => 'paypal_client_id',
        'label' => 'PayPal Client ID',
        'type' => 'text',
        'group' => 'Payments - PayPal',
        'validation' => 'nullable|string|max:255',
        'vif' => ['payment_gateway', 'paypal'], // Show only when payment_gateway = paypal
    ],
];
```

### How `vif` Works

- **Format**: `'vif' => ['field_name', 'expected_value']`
- **Behavior**:
  - Field is shown when `field_name` equals `expected_value`
  - Field is hidden otherwise
  - Changes are reactive — fields appear/disappear as you change the controlling field
  - If all fields in a group are hidden, the tab disappears too
  - If you're on a hidden tab, you're automatically switched to the first visible tab

### Validation Best Practices with `vif`

When using conditional fields, always use `nullable` validation for optional fields:

```php
// ✅ Correct — nullable allows field to be skipped when hidden
'validation' => 'nullable|email|max:255',

// ❌ Avoid — required will fail validation when field is hidden
'validation' => 'required|email|max:255',
```

## Multi-Tenancy Support

NovaSettings works seamlessly with Spatie Multitenancy. Ensure your settings model uses the tenant connection:

```php
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Setting extends Model
{
    use UsesTenantConnection;
    
    protected $fillable = ['key', 'value'];
    
}
```

## Accessing Settings in Code

### Reading Settings

```php
use App\Models\Setting;

// Get a single setting
$companyName = Setting::where('key', 'company_name')->value('value');

// Get with default value
$maxUsers = Setting::where('key', 'max_users')->value('value') ?? 100;

// Get boolean setting
$maintenanceMode = (bool) Setting::where('key', 'maintenance_mode')->value('value');
```

### Setting Values Programmatically

```php
use App\Models\Setting;

// Create or update
Setting::updateOrCreate(
    ['key' => 'company_name'],
    ['value' => 'Acme Corporation']
);

// Bulk update
$settings = [
    'company_name' => 'Acme Corp',
    'company_email' => 'info@acme.com',
];

foreach ($settings as $key => $value) {
    Setting::updateOrCreate(['key' => $key], ['value' => $value]);
}
```

### Helper Function (Optional)

Create a helper for easy access:

```php
// app/Helpers/settings.php
if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return \App\Models\Setting::where('key', $key)->value('value') ?? $default;
    }
}

// Usage
$companyName = setting('company_name', 'Default Company');
```

## UI Features

### Smart Saving

- Only changed values are sent to the server
- Prevents unnecessary validation errors on untouched fields
- Shows "No changes to save" message if nothing changed
- Auto-hides success messages after 5 seconds

### Real-Time Validation

- Server-side validation rules applied on save
- Clear error messages with field names
- Errors displayed at the top of the form

### Tab Behavior

- Tabs are sorted alphabetically
- Empty tabs (all fields hidden via `vif`) automatically disappear
- Active tab switches to first available if current tab becomes hidden
- Mobile-responsive with scrollable tabs

### Button States

- Save button disabled while saving
- Spinner icon during save operation
- Reset button clears form to original values

## Advanced Usage

### Grouping Related Settings

Use descriptive group names to organize settings logically:

```php
'settings' => [
    // Company Info
    [...], // company_name, company_address, etc.
    
    // Integrations
    [...], // payment_gateway, shipping_provider, etc.
    
    // Security
    [...], // 2fa_enabled, session_timeout, etc.
    
    // Notifications
    [...], // email_notifications, sms_enabled, etc.
],
```

### Cascading Conditional Fields

You can chain `vif` conditions across multiple fields:

```php
[
    'name' => 'level_1',
    'type' => 'select',
    'group' => 'Settings',
    // ... options
],
[
    'name' => 'level_2',
    'type' => 'select',
    'group' => 'Settings',
    'vif' => ['level_1', 'option_a'],
    // ... options
],
[
    'name' => 'level_3',
    'type' => 'text',
    'group' => 'Settings',
    'vif' => ['level_2', 'option_b'], // Only visible when level_1=option_a AND level_2=option_b
],
```

### Custom Model Column Names

If your settings table uses different column names:

```php
'model' => 'App\\Models\\Config',
'keycol' => 'setting_name',  // Instead of 'key'
'valuecol' => 'setting_data', // Instead of 'value'
```

## Troubleshooting

### Settings Not Showing

1. Verify the tool is registered in `NovaServiceProvider`
2. Check that your settings model exists and is accessible
3. Run `php artisan config:clear` after changing config

### Validation Errors on Hidden Fields

- Ensure hidden/conditional fields use `nullable` validation
- Check that `vif` references the correct field name (not label)

### Tab Not Appearing

- Verify `group` name matches across related fields
- Check if all fields in the group have `vif` conditions that hide them

### Dark Mode Issues

- Ensure you've compiled assets: `npm run production`
- Clear browser cache
- Verify Nova's dark mode is enabled in Nova settings

## Development

### Building Assets

```bash
cd vendor/nowakadmin/nova-settings
npm install
npm run production  # For production
npm run dev        # For development with watch
```

### File Structure

```
nova-settings/
├── config/
│   └── nova-settings.php    # Default config (published)
├── resources/
│   ├── js/
│   │   └── pages/
│   │       └── Tool.vue      # Main Vue component
│   └── views/
│       └── navigation.blade.php
├── src/
│   ├── Http/
│   │   └── Controllers/
│   │       └── SettingsController.php
│   ├── NovaSettings.php     # Tool class
│   └── ToolServiceProvider.php
└── routes/
    └── inertia.php           # Tool routes
```

## Requirements

- PHP 8.1+
- Laravel 10+
- Laravel Nova 4+
- Vue 3
- Inertia.js

## License

MIT License — free to use and modify.

## Support

For issues, feature requests, or contributions open an issue in the repository.

---

**Built with ❤️ for Laravel Nova**
