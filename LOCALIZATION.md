# NovaSettings Localization Guide

## Overview
NovaSettings tool now supports full localization with Polish (pl) translations included.

## Translation Files

### Main Translation File
- **Location**: `resources/lang/pl.json`
- **Purpose**: Contains all UI strings used in the NovaSettings tool

### Available Translations

```json
{
  "Nova Settings": "Ustawienia Nova",
  "Save Settings": "Zapisz ustawienia",
  "Saving...": "Zapisywanie...",
  "Save": "Zapisz",
  "Reset": "Resetuj",
  "Validation Errors:": "Błędy walidacji:",
  "Select an option": "Wybierz opcję",
  "Add": "Dodaj",
  "Add value": "Dodaj wartość",
  "No items added yet.": "Nie dodano jeszcze żadnych elementów.",
  "Enable": "Włącz",
  "Remove": "Usuń",
  "No changes to save": "Brak zmian do zapisania",
  "Failed to save settings": "Nie udało się zapisać ustawień",
  "Settings saved successfully": "Ustawienia zapisane pomyślnie",
  "Error:": "Błąd:",
  "NovaSettings": "Ustawienia",
  "General": "Ogólne"
}
```

## Implementation

### Vue Component (Tool.vue)
The tool uses Laravel's translation helper `__()` for all UI strings:

```vue
<!-- Example: Translated button -->
<button>
  {{ __('Save Settings') }}
</button>

<!-- Example: Translated checkbox label -->
<label>{{ __('Enable') }}</label>

<!-- Example: Translated message -->
<div>{{ __('Settings saved successfully') }}</div>
```

### Menu Translation
The tool menu in Nova sidebar uses translation:

```php
// src/NovaSettings.php
public function menu(Request $request): MenuSection
{
    return MenuSection::make(__('novasettings::NovaSettings'))
        ->path('/novasettings')
        ->icon('server');
}
```

## Publishing Translations

To publish translations to your Laravel application:

```bash
php artisan vendor:publish --tag=nova-settings-lang
```

This copies translations to: `lang/vendor/novasettings/pl.json`

## Adding New Languages

### 1. Create Translation File
Create a new JSON file in `resources/lang/`:
- `en.json` - English
- `de.json` - German
- `fr.json` - French
- etc.

### 2. Copy Structure from pl.json
Use the same keys as `pl.json`, translate values to your language:

```json
{
  "Nova Settings": "Nova Einstellungen",
  "Save Settings": "Einstellungen speichern",
  "Saving...": "Speichert...",
  ...
}
```

### 3. Rebuild Assets
After adding translations, rebuild the tool:

```bash
cd vendor/nowakadmin/novasettings
npm install
npm run production
```

## Translation Keys Reference

| Key | Polish (pl) | Usage |
|-----|-------------|-------|
| `Nova Settings` | Ustawienia Nova | Main page title |
| `Save Settings` | Zapisz ustawienia | Save button text |
| `Saving...` | Zapisywanie... | Loading state text |
| `Reset` | Resetuj | Reset button |
| `Validation Errors:` | Błędy walidacji: | Error section header |
| `Select an option` | Wybierz opcję | Dropdown placeholder |
| `Add` | Dodaj | List add button |
| `Add value` | Dodaj wartość | List input placeholder |
| `No items added yet.` | Nie dodano jeszcze żadnych elementów. | Empty list message |
| `Enable` | Włącz | Checkbox enable label |
| `Remove` | Usuń | Remove button tooltip |
| `No changes to save` | Brak zmian do zapisania | Info message |
| `Failed to save settings` | Nie udało się zapisać ustawień | Error message |
| `Settings saved successfully` | Ustawienia zapisane pomyślnie | Success message |
| `Error:` | Błąd: | Error prefix |
| `NovaSettings` | Ustawienia | Menu item |
| `General` | Ogólne | Default group name |

## Laravel Configuration

### Setting Application Locale
In your `.env` file:
```env
APP_LOCALE=pl
APP_FALLBACK_LOCALE=en
```

Or in `config/app.php`:
```php
'locale' => 'pl',
'fallback_locale' => 'en',
```

### Dynamic Locale Switching
If you want users to switch languages:

```php
// In a controller or middleware
app()->setLocale('pl'); // or 'en', 'de', etc.
```

## Testing Translations

### 1. Check Translation Loading
In your browser console (when viewing NovaSettings):
```javascript
// Should return translated text
window.__('Save Settings')
// Expected: "Zapisz ustawienia" (if locale is 'pl')
```

### 2. Verify Menu Translation
Check Nova sidebar - menu item should display:
- English (en): "NovaSettings"
- Polish (pl): "Ustawienia"

### 3. Test All UI Elements
- Page title
- Buttons (Save, Reset, Add, Remove)
- Messages (success, error, info)
- Placeholders
- Labels

## Troubleshooting

### Translations Not Working

1. **Clear Laravel cache**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

2. **Rebuild assets**:
```bash
cd vendor/nowakadmin/novasettings
npm run production
```

3. **Verify locale setting**:
```bash
php artisan tinker
>>> app()->getLocale()
=> "pl"
```

4. **Check translation file exists**:
```bash
ls -la lang/vendor/novasettings/pl.json
# or
ls -la vendor/nowakadmin/novasettings/resources/lang/pl.json
```

### Menu Still in English

Ensure you're using the translation helper in `NovaSettings.php`:
```php
MenuSection::make(__('novasettings::NovaSettings'))
```

Not:
```php
MenuSection::make('NovaSettings') // ❌ Wrong - hardcoded
```

### Missing Translations

If you see a translation key instead of text (e.g., "Save Settings" shows as is):
1. Check the key exists in your locale's JSON file
2. Ensure JSON syntax is valid (no trailing commas)
3. Verify file encoding is UTF-8

## Best Practices

1. **Always use translation keys**: Never hardcode UI strings in Vue templates
2. **Consistent naming**: Use clear, descriptive translation keys
3. **Provide fallbacks**: Always include English (en) translations as fallback
4. **Test all locales**: Verify UI layout works with longer/shorter translations
5. **Document custom keys**: If adding new translations, document them here

## Contributing Translations

To contribute translations for a new language:

1. Fork the repository
2. Create `resources/lang/{locale}.json` (e.g., `de.json` for German)
3. Translate all keys from `pl.json`
4. Test in your application
5. Submit a pull request with:
   - Translation file
   - Updated LOCALIZATION.md (this file)
   - Screenshots showing translated UI

## Future Enhancements

- [ ] Add more language translations (de, fr, es, it)
- [ ] Add date/time format localization for timestamps
- [ ] Add number format localization (decimal separator, thousands)
- [ ] Add RTL (right-to-left) support for Arabic/Hebrew
- [ ] Add locale switcher in tool UI
