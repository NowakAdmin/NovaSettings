<?php

use Illuminate\Support\Facades\Route;
use Nowakadmin\NovaSettings\Http\Controllers\SettingsController;
use Laravel\Nova\Http\Requests\NovaRequest;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/', function (NovaRequest $request) {
    $settingsDefinition = config('nova-settings.settings', []);
    $modelClass = config('nova-settings.model', 'App\\Models\\Setting');

    // Load current values from database
    $settings = [];
    foreach ($settingsDefinition as $definition) {
        $value = $modelClass::where('key', $definition['name'])->value('value');
        
        // Decode list-type JSON values to arrays for the frontend
        if (($definition['type'] ?? null) === 'list') {
            \Log::info("Loading list field: {$definition['name']}", [
                'raw_value' => $value,
                'is_string' => is_string($value),
            ]);
            
            $decoded = [];
            if (is_string($value) && $value !== '' && $value !== 'null') {
                $decoded = json_decode($value, true);
                if ($decoded === null || json_last_error() !== JSON_ERROR_NONE) {
                    \Log::warning("JSON decode failed for {$definition['name']}", [
                        'raw' => $value,
                        'error' => json_last_error_msg()
                    ]);
                    $decoded = [];
                }
            }
            $settings[$definition['name']] = is_array($decoded) ? $decoded : [];
            
            \Log::info("Final value for {$definition['name']}: " . json_encode($settings[$definition['name']]));
        } else {
            $settings[$definition['name']] = $value;
        }
    }

    // Group definitions
    $groups = [];
    foreach ($settingsDefinition as $definition) {
        $groupName = $definition['group'] ?? 'General';
        if (!isset($groups[$groupName])) {
            $groups[$groupName] = [];
        }
        $groups[$groupName][] = $definition;
    }

    return inertia('NovaSettings', [
        'definitions' => $settingsDefinition,
        'settings' => $settings,
        'groups' => $groups,
    ]);
});
