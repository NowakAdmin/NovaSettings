<?php

namespace Nowakadmin\NovaSettings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Load all settings and configuration for the frontend.
     */
    public function index(Request $request)
    {
        $settingsDefinition = config('nova-settings-definition.settings', []);
        $modelClass = config('nova-settings.model', 'App\\Models\\Setting');

        // Load current values from database
        $settings = [];
        foreach ($settingsDefinition as $definition) {
            $value = $modelClass::where('key', $definition['name'])->value('value');
            $settings[$definition['name']] = $value;
        }

        return response()->json([
            'settings' => $settings,
            'definitions' => $settingsDefinition,
            'groups' => $this->groupDefinitions($settingsDefinition),
        ]);
    }

    /**
     * Save settings with validation.
     */
    public function store(Request $request)
    {
        $settingsDefinition = config('nova-settings-definition.settings', []);
        $modelClass = config('nova-settings.model', 'App\\Models\\Setting');

        // Build validation rules from definition
        $rules = [];
        $definitions = collect($settingsDefinition)->keyBy('name');

        foreach ($settingsDefinition as $definition) {
            $rules[$definition['name']] = $definition['validation'] ?? 'nullable';
        }

        // Validate
        $validated = Validator::make($request->all(), $rules)->validate();

        // Save each setting
        foreach ($validated as $key => $value) {
            $modelClass::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully',
            'settings' => $validated,
        ]);
    }

    /**
     * Group setting definitions by group name.
     */
    private function groupDefinitions(array $definitions): array
    {
        $groups = [];
        foreach ($definitions as $definition) {
            $groupName = $definition['group'] ?? 'General';
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = [];
            }
            $groups[$groupName][] = $definition;
        }
        return $groups;
    }
}
