<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Nova Settings</h1>
      <button
        @click="saveSettings"
        :disabled="isSaving"
        class="shrink-0 h-9 px-4 inline-flex items-center font-bold shadow rounded focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 text-sm"
      >
        <svg v-if="isSaving" class="animate-spin -ml-1 mr-3 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ isSaving ? 'Saving...' : 'Save Settings' }}
      </button>
    </div>

    <!-- Alert Messages -->
    <div v-if="message" class="rounded-lg border" :class="messageClass">
      {{ message }}
    </div>

    <div v-if="errors" class="rounded-lg border border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-950 p-4">
      <p class="text-sm font-semibold text-red-900 dark:text-red-100 mb-3">Validation Errors:</p>
      <ul class="space-y-1">
        <li v-for="(error, key) in errors" :key="key" class="text-sm text-red-800 dark:text-red-200">
          <strong>{{ formatFieldName(key) }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
        </li>
      </ul>
    </div>

    <!-- Tabs for groups -->
    <div v-if="groupNames.length > 1" class="border-b border-gray-200 dark:border-gray-700">
      <ul class="flex flex-wrap -mb-px text-sm font-medium">
        <li v-for="groupName in groupNames" :key="groupName" class="me-2">
          <button
            @click="activeGroup = groupName"
            :class="[
              'inline-flex items-center justify-center px-4 py-3 border-b-2 rounded-t-lg transition duration-150',
              activeGroup === groupName
                ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 active'
                : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:border-gray-300 dark:hover:border-gray-600'
            ]"
            :aria-current="activeGroup === groupName ? 'page' : undefined"
          >
            {{ groupName }}
          </button>
        </li>
      </ul>
    </div>

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
      <!-- Form fields for active group -->
      <div class="p-6 space-y-6">
        <div v-for="definition in activeGroupDefinitions" :key="definition.name" class="space-y-2" v-show="isFieldVisible(definition)">
          <!-- Label -->
          <label :for="definition.name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ definition.label }}
          </label>

          <!-- Text Input -->
          <input
            v-if="definition.type === 'text'"
            :id="definition.name"
            v-model="formData[definition.name]"
            type="text"
            :placeholder="definition.placeholder || ''"
            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
          />

          <!-- Email Input -->
          <input
            v-else-if="definition.type === 'email'"
            :id="definition.name"
            v-model="formData[definition.name]"
            type="email"
            :placeholder="definition.placeholder || ''"
            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
          />

          <!-- Number Input -->
          <input
            v-else-if="definition.type === 'number'"
            :id="definition.name"
            v-model.number="formData[definition.name]"
            type="number"
            :placeholder="definition.placeholder || ''"
            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
          />

          <!-- Textarea -->
          <textarea
            v-else-if="definition.type === 'textarea'"
            :id="definition.name"
            v-model="formData[definition.name]"
            :placeholder="definition.placeholder || ''"
            rows="4"
            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 resize-none"
          />

          <!-- Boolean/Checkbox -->
          <div v-else-if="definition.type === 'boolean'" class="flex items-center">
            <input
              :id="definition.name"
              v-model="formData[definition.name]"
              type="checkbox"
              :true-value="1"
              :false-value="0"
              class="w-4 h-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 dark:focus:ring-offset-gray-800 cursor-pointer"
            />
            <label :for="definition.name" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
              Enable
            </label>
          </div>

          <!-- Select/Dropdown -->
          <select
            v-else-if="definition.type === 'select'"
            :id="definition.name"
            v-model="formData[definition.name]"
            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
          >
            <option value="">Select an option</option>
            <option v-for="option in definition.options" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>

          <!-- List (array of strings) with add + pills -->
          <div v-else-if="definition.type === 'list'">
            <div class="flex items-center gap-2 mb-3">
              <input
                :id="definition.name + '_input'"
                v-model="newListItem[definition.name]"
                @keyup.enter="addListItem(definition.name)"
                type="text"
                :placeholder="definition.placeholder || 'Add value'"
                class="flex-1 px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
              />
              <button
                @click="addListItem(definition.name)"
                type="button"
                class="h-9 px-3 inline-flex items-center font-bold shadow rounded focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white text-sm"
              >
                Add
              </button>
            </div>
            <div v-if="Array.isArray(formData[definition.name]) && formData[definition.name].length" class="flex flex-wrap gap-2">
              <span
                v-for="(val, idx) in formData[definition.name]"
                :key="definition.name + '-' + idx + '-' + val"
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200"
              >
                {{ val }}
                <button
                  @click="removeListItem(definition.name, idx)"
                  type="button"
                  class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 hover:bg-red-600 text-white"
                  aria-label="Remove"
                  title="Remove"
                >
                  ✕
                </button>
              </span>
            </div>
            <div v-else-if="Array.isArray(formData[definition.name])" class="text-sm text-gray-500 dark:text-gray-400 italic">
              No items added yet.
            </div>
            <p v-if="definition.help" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
              {{ definition.help }}
            </p>
          </div>

          <!-- Helper text for non-list fields -->
          <p v-else-if="definition.help && definition.type !== 'list'" class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
            {{ definition.help }}
          </p>
        </div>
      </div>

      <!-- Form Footer (buttons) -->
      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 rounded-b-lg flex items-center">
        <button
          @click="saveSettings"
          :disabled="isSaving"
          class="shrink-0 h-9 px-4 inline-flex items-center font-bold shadow rounded focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 text-sm"
        >
          <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ isSaving ? 'Saving...' : 'Save Settings' }}
        </button>
        <button
          @click="resetForm"
          class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 transition duration-150 font-medium text-sm"
        >
          Reset
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue'

export default {
  props: {
    definitions: {
      type: Array,
      required: true,
    },
    settings: {
      type: Object,
      required: true,
    },
    groups: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    // Deep clone settings to properly handle arrays
    const deepClone = (obj) => {
      if (obj === null || typeof obj !== 'object') return obj
      if (Array.isArray(obj)) return obj.map(deepClone)
      return Object.fromEntries(
        Object.entries(obj).map(([k, v]) => [k, deepClone(v)])
      )
    }

    const formData = ref(deepClone(props.settings))
    const originalData = ref(deepClone(props.settings)) // Track original values
    const activeGroup = ref(Object.keys(props.groups)[0] || 'General')
    const isSaving = ref(false)
    const message = ref('')
    const messageClass = ref('')
    const errors = ref(null)
    const newListItem = ref({})

    // Ensure all list-type fields are initialized as arrays
    onMounted(() => {
      props.definitions.forEach(def => {
        if (def.type === 'list') {
          console.log(`Initializing list field: ${def.name}`, {
            currentValue: formData.value[def.name],
            isArray: Array.isArray(formData.value[def.name]),
            originalValue: originalData.value[def.name]
          })
          if (!Array.isArray(formData.value[def.name])) {
            formData.value[def.name] = []
          }
          if (!Array.isArray(originalData.value[def.name])) {
            originalData.value[def.name] = []
          }
        }
      })
      console.log('Final formData after mount:', JSON.stringify(formData.value, null, 2))
    })

    const groupNames = computed(() => {
      // Filter groups to only show those with at least one visible field
      return Object.keys(props.groups)
        .filter(groupName => {
          const definitions = props.groups[groupName] || []
          return definitions.some(def => isFieldVisible(def))
        })
        .sort()
    })

    const activeGroupDefinitions = computed(() => {
      return props.groups[activeGroup.value] || []
    })

    const formatFieldName = (key) => {
      return key
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
    }

    // Check if field should be visible based on vif condition
    const isFieldVisible = (definition) => {
      if (!definition.vif || !Array.isArray(definition.vif) || definition.vif.length !== 2) {
        return true // No condition or invalid format, always show
      }
      const [fieldName, expectedValue] = definition.vif
      return formData.value[fieldName] === expectedValue
    }

    // Watch for changes in visible groups and switch to first available if current becomes hidden
    watch(groupNames, (newGroups) => {
      if (!newGroups.includes(activeGroup.value) && newGroups.length > 0) {
        activeGroup.value = newGroups[0]
      }
    })

    // Get only changed values
    const getChangedValues = () => {
      const changed = {}
      for (const key in formData.value) {
        const currentValue = formData.value[key]
        const originalValue = originalData.value[key]

        // Compare values (handle arrays vs scalars)
        const isArrayCurrent = Array.isArray(currentValue)
        const isArrayOriginal = Array.isArray(originalValue)

        if (isArrayCurrent || isArrayOriginal) {
          const curr = isArrayCurrent ? JSON.stringify(currentValue) : JSON.stringify([])
          const orig = isArrayOriginal ? JSON.stringify(originalValue) : JSON.stringify([])
          if (curr !== orig) {
            changed[key] = currentValue
          }
        } else {
          // Compare values (handle null/undefined/empty string equivalence)
          const normalizedCurrent = currentValue === null || currentValue === undefined || currentValue === '' ? '' : String(currentValue)
          const normalizedOriginal = originalValue === null || originalValue === undefined || originalValue === '' ? '' : String(originalValue)
          if (normalizedCurrent !== normalizedOriginal) {
            changed[key] = currentValue
          }
        }
      }
      return changed
    }

    const saveSettings = async () => {
      isSaving.value = true
      message.value = ''
      errors.value = null

      // Get only changed values
      const changedValues = getChangedValues()
      
      // If nothing changed, show message and return
      if (Object.keys(changedValues).length === 0) {
        messageClass.value = 'border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 p-4 text-sm text-blue-800 dark:text-blue-200'
        message.value = 'No changes to save'
        isSaving.value = false
        setTimeout(() => {
          message.value = ''
        }, 3000)
        return
      }

      try {
        const response = await fetch('/nova-vendor/novasettings/settings', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          },
          body: JSON.stringify(changedValues), // Only send changed values
        })

        const data = await response.json()

        if (!response.ok) {
          errors.value = data.errors || {}
          messageClass.value = 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 p-4 text-sm text-red-800 dark:text-red-200'
          message.value = data.message || 'Failed to save settings'
        } else {
          // Update original data to current values after successful save
          originalData.value = deepClone(formData.value)
          
          messageClass.value = 'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200'
          message.value = data.message || 'Settings saved successfully'
          // Auto-hide success message after 5 seconds
          setTimeout(() => {
            message.value = ''
          }, 5000)
        }
      } catch (error) {
        messageClass.value = 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 p-4 text-sm text-red-800 dark:text-red-200'
        message.value = `Error: ${error.message}`
      } finally {
        isSaving.value = false
      }
    }

    const resetForm = () => {
      formData.value = deepClone(props.settings)
      originalData.value = deepClone(props.settings) // Reset original data too
      message.value = ''
      errors.value = null
    }

    const addListItem = (fieldName) => {
      const val = (newListItem.value[fieldName] || '').trim()
      if (!val) return
      if (!Array.isArray(formData.value[fieldName])) {
        formData.value[fieldName] = []
      }
      if (!formData.value[fieldName].includes(val)) {
        formData.value[fieldName].push(val)
      }
      newListItem.value[fieldName] = ''
    }

    const removeListItem = (fieldName, idx) => {
      if (!Array.isArray(formData.value[fieldName])) return
      formData.value[fieldName].splice(idx, 1)
    }

    return {
      formData,
      activeGroup,
      activeGroupDefinitions,
      groupNames,
      isSaving,
      message,
      messageClass,
      errors,
      saveSettings,
      resetForm,
      formatFieldName,
      isFieldVisible,
      newListItem,
      addListItem,
      removeListItem,
    }
  },
}
</script>

<style scoped>
/* Nova-aligned form styling */
input[type="text"],
input[type="email"],
input[type="number"],
textarea,
select {
  @apply transition-all duration-150;
}

input[type="checkbox"] {
  @apply cursor-pointer;
}
</style>
