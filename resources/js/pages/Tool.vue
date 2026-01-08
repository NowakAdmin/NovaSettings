<template>
  <div class="space-y-6 p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-900">Nova Settings</h1>
      <button
        @click="saveSettings"
        :disabled="isSaving"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
      >
        {{ isSaving ? 'Saving...' : 'Save Settings' }}
      </button>
    </div>

    <div v-if="message" class="p-4 rounded-lg" :class="messageClass">
      {{ message }}
    </div>

    <div v-if="errors" class="p-4 rounded-lg bg-red-50 border border-red-200">
      <p class="text-red-800 font-semibold mb-2">Validation Errors:</p>
      <ul class="list-disc list-inside space-y-1">
        <li v-for="(error, key) in errors" :key="key" class="text-red-700">
          <strong>{{ key }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
        </li>
      </ul>
    </div>

    <!-- Tabs for groups -->
    <div class="border-b border-gray-200">
      <div class="flex space-x-8">
        <button
          v-for="groupName in groupNames"
          :key="groupName"
          @click="activeGroup = groupName"
          :class="[
            'px-1 py-4 text-sm font-medium border-b-2 transition',
            activeGroup === groupName
              ? 'border-blue-600 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          {{ groupName }}
        </button>
      </div>
    </div>

    <!-- Form fields for active group -->
    <div class="space-y-6">
      <div v-for="definition in activeGroupDefinitions" :key="definition.name" class="space-y-2">
        <label :for="definition.name" class="block text-sm font-medium text-gray-700">
          {{ definition.label }}
        </label>

        <!-- Text Input -->
        <input
          v-if="definition.type === 'text'"
          :id="definition.name"
          v-model="formData[definition.name]"
          type="text"
          :placeholder="definition.placeholder || ''"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />

        <!-- Email Input -->
        <input
          v-else-if="definition.type === 'email'"
          :id="definition.name"
          v-model="formData[definition.name]"
          type="email"
          :placeholder="definition.placeholder || ''"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />

        <!-- Number Input -->
        <input
          v-else-if="definition.type === 'number'"
          :id="definition.name"
          v-model.number="formData[definition.name]"
          type="number"
          :placeholder="definition.placeholder || ''"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />

        <!-- Textarea -->
        <textarea
          v-else-if="definition.type === 'textarea'"
          :id="definition.name"
          v-model="formData[definition.name]"
          :placeholder="definition.placeholder || ''"
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />

        <!-- Boolean/Checkbox -->
        <label
          v-else-if="definition.type === 'boolean'"
          class="flex items-center space-x-2 cursor-pointer"
        >
          <input
            :id="definition.name"
            v-model="formData[definition.name]"
            type="checkbox"
            :value="1"
            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
          />
          <span class="text-sm text-gray-600">Enable</span>
        </label>

        <!-- Select/Dropdown -->
        <select
          v-else-if="definition.type === 'select'"
          :id="definition.name"
          v-model="formData[definition.name]"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Select an option</option>
          <option v-for="option in definition.options" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>

        <!-- Helper text -->
        <p v-if="definition.help" class="text-xs text-gray-500">
          {{ definition.help }}
        </p>
      </div>
    </div>

    <!-- Save button at bottom -->
    <div class="flex gap-2 pt-4 border-t border-gray-200">
      <button
        @click="saveSettings"
        :disabled="isSaving"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
      >
        {{ isSaving ? 'Saving...' : 'Save Settings' }}
      </button>
      <button
        @click="resetForm"
        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
      >
        Reset
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

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
    const formData = ref({ ...props.settings })
    const activeGroup = ref(Object.keys(props.groups)[0] || 'General')
    const isSaving = ref(false)
    const message = ref('')
    const messageClass = ref('')
    const errors = ref(null)

    const groupNames = computed(() => Object.keys(props.groups).sort())

    const activeGroupDefinitions = computed(() => {
      return props.groups[activeGroup.value] || []
    })

    const saveSettings = async () => {
      isSaving.value = true
      message.value = ''
      errors.value = null

      try {
        const response = await fetch('/nova-vendor/novasettings/settings', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          },
          body: JSON.stringify(formData.value),
        })

        const data = await response.json()

        if (!response.ok) {
          errors.value = data.errors || {}
          messageClass.value = 'bg-red-50 border border-red-200 text-red-800'
          message.value = data.message || 'Failed to save settings'
        } else {
          messageClass.value = 'bg-green-50 border border-green-200 text-green-800'
          message.value = data.message || 'Settings saved successfully'
        }
      } catch (error) {
        messageClass.value = 'bg-red-50 border border-red-200 text-red-800'
        message.value = `Error: ${error.message}`
      } finally {
        isSaving.value = false
      }
    }

    const resetForm = () => {
      formData.value = { ...props.settings }
      message.value = ''
      errors.value = null
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
    }
  },
}
</script>

<style scoped>
/* Nova-style form */
input[type="text"],
input[type="email"],
input[type="number"],
textarea,
select {
  @apply transition duration-150 ease-in-out;
}
</style>
