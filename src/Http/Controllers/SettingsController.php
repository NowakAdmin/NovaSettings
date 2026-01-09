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
        $settingsDefinition = config('nova-settings.settings', []);
        $modelClass = config('nova-settings.model', 'App\\Models\\Setting');
        $keyCol = config('nova-settings.keycol', 'key');
        $valueCol = config('nova-settings.valuecol', 'value');

        // Load current values from database
        $settings = [];
        foreach ($settingsDefinition as $definition) {
            $raw = $modelClass::where($keyCol, $definition['name'])->value($valueCol);
            // Decode list-type JSON values to arrays for the frontend
            if (($definition['type'] ?? null) === 'list') {
                $decoded = null;
                if (is_string($raw) && $raw !== '') {
                    try {
                        $decoded = json_decode($raw, true);
                    } catch (\Throwable $e) {
                        $decoded = null;
                    }
                }
                $settings[$definition['name']] = is_array($decoded) ? $decoded : [];
            } else {
                $settings[$definition['name']] = $raw;
            }
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
        $settingsDefinition = config('nova-settings.settings', []);
        $modelClass = config('nova-settings.model', 'App\\Models\\Setting');
        $keyCol = config('nova-settings.keycol', 'key');
        $valueCol = config('nova-settings.valuecol', 'value');

        // Build validation rules from definition
        $rules = [];
        $definitions = collect($settingsDefinition)->keyBy('name');

        foreach ($settingsDefinition as $definition) {
            // If type is 'list', ensure we validate as array
            if (($definition['type'] ?? null) === 'list') {
                $rules[$definition['name']] = $definition['validation'] ?? 'nullable|array';
            } else {
                $rules[$definition['name']] = $definition['validation'] ?? 'nullable';
            }
        }

        // Validate
        $validated = Validator::make($request->all(), $rules)->validate();

        // Save each setting
        foreach ($validated as $key => $value) {
            $definition = $definitions->get($key);

            // Handle empty boolean values - store as "0" instead of null
            if ($definition && ($definition['type'] ?? null) === 'boolean' && ($value === null || $value === '')) {
                $value = '0';
            }

            // Encode list arrays as JSON strings before saving
            if ($definition && ($definition['type'] ?? null) === 'list') {
                if (is_array($value)) {
                    $value = json_encode(array_values($value));
                } elseif ($value === null || $value === '') {
                    $value = json_encode([]);
                }
            }

            $modelClass::updateOrCreate(
                [$keyCol => $key],
                [$valueCol => $value]
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
