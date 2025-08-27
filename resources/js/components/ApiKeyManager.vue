<template>
  <Card>
    <CardHeader>
      <CardTitle>API Keys</CardTitle>
      <CardDescription>
        Manage your API keys for external integrations.
      </CardDescription>
    </CardHeader>
    <CardContent>
      <div class="space-y-4">
        <!-- Create New API Key -->
        <div class="flex space-x-2">
          <Input
            v-model="newKeyName"
            placeholder="API Key Name"
            class="flex-1"
          />
          <Button @click="createApiKey" :disabled="!newKeyName">
            Create Key
          </Button>
        </div>

        <!-- Existing API Keys -->
        <div v-if="apiKeys.length === 0" class="text-center py-4">
          <p class="text-muted-foreground">No API keys created yet.</p>
        </div>
        
        <div v-else class="space-y-3">
          <div
            v-for="key in apiKeys"
            :key="key.id"
            class="border rounded-lg p-4"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <h4 class="font-medium">{{ key.name }}</h4>
                <div class="text-sm text-muted-foreground space-y-1 mt-2">
                  <p><strong>Public Key:</strong> {{ key.key }}</p>
                  <p><strong>Secret Key:</strong> {{ key.secret }}</p>
                  <p v-if="key.last_used_at">
                    Last used: {{ new Date(key.last_used_at).toLocaleDateString() }}
                  </p>
                </div>
              </div>
              <div class="flex space-x-2">
                <Button
                  variant="outline"
                  size="sm"
                  @click="copyToClipboard(key.key + ':' + key.secret)"
                >
                  Copy Keys
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  @click="toggleKeyStatus(key)"
                >
                  {{ key.is_active ? 'Disable' : 'Enable' }}
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  @click="deleteApiKey(key.id)"
                >
                  Delete
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

interface ApiKey {
  id: number
  name: string
  key: string
  secret: string
  is_active: boolean
  last_used_at: string | null
}

const apiKeys = ref<ApiKey[]>([])
const newKeyName = ref('')

onMounted(() => {
  // Load API keys from the server
  loadApiKeys()
})

const loadApiKeys = () => {
  // This would typically fetch from an API endpoint
  // For now, we'll use a placeholder
  apiKeys.value = []
}

const createApiKey = () => {
  if (!newKeyName.value) return

  router.post('/api-keys', {
    name: newKeyName.value
  }, {
    onSuccess: () => {
      newKeyName.value = ''
      loadApiKeys()
    }
  })
}

const toggleKeyStatus = (key: ApiKey) => {
  router.patch(`/api-keys/${key.id}/toggle`, {}, {
    onSuccess: () => {
      loadApiKeys()
    }
  })
}

const deleteApiKey = (id: number) => {
  if (confirm('Are you sure you want to delete this API key?')) {
    router.delete(`/api-keys/${id}`, {
      onSuccess: () => {
        loadApiKeys()
      }
    })
  }
}

const copyToClipboard = async (text: string) => {
  try {
    await navigator.clipboard.writeText(text)
    // You could add a toast notification here
  } catch (err) {
    console.error('Failed to copy to clipboard:', err)
  }
}
</script>
