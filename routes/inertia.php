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
    $settingsDefinition = config('nova-settings-definition.settings', []);
    $modelClass = config('nova-settings.model', 'App\\Models\\Setting');

    // Load current values from database
    $settings = [];
    foreach ($settingsDefinition as $definition) {
        $value = $modelClass::where('key', $definition['name'])->value('value');
        $settings[$definition['name']] = $value;
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
