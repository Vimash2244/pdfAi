<template>
  <AppLayout title="PDF Parse">
    <template #header>
      <Heading>PDF Parse with AI</Heading>
    </template>

    <div class="space-y-6">
      <!-- Upload Section -->
      <Card v-if="canUseAi">
        <CardHeader>
          <CardTitle>Upload PDF for AI Parsing</CardTitle>
          <CardDescription>
            Upload a PDF file and select an AI model to extract structured information.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <Label for="pdf_file">PDF File</Label>
              <Input
                id="pdf_file"
                type="file"
                accept=".pdf"
                @change="handleFileChange"
                required
              />
              <p class="text-sm text-muted-foreground mt-1">
                Maximum file size: 10MB
              </p>
            </div>

            <div>
              <Label for="ai_model">AI Model</Label>
              <select
                id="ai_model"
                v-model="form.ai_model_id"
                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                required
              >
                <option value="">Select AI Model</option>
                <option
                  v-for="model in aiModels"
                  :key="model.id"
                  :value="model.id"
                >
                  {{ model.name }} ({{ model.model_identifier }})
                </option>
              </select>
            </div>

            <Button type="submit" :disabled="!form.pdf_file || !form.ai_model_id">
              Parse PDF
            </Button>
          </form>
        </CardContent>
      </Card>

      <!-- Subscription Required Message -->
      <Card v-else>
        <CardContent class="pt-6">
          <div class="text-center">
            <HeadingSmall>Subscription Required</HeadingSmall>
            <p class="text-muted-foreground mt-2">
              You need an active subscription to use AI models for PDF parsing.
            </p>
          </div>
        </CardContent>
      </Card>

      <!-- Results Section -->
      <Card>
        <CardHeader>
          <CardTitle>Parse History</CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="pdfParses.data.length === 0" class="text-center py-8">
            <p class="text-muted-foreground">No PDFs parsed yet.</p>
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="parse in pdfParses.data"
              :key="parse.id"
              class="border rounded-lg p-4"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="font-medium">{{ parse.original_filename }}</h4>
                  <p class="text-sm text-muted-foreground">
                    AI Model: {{ parse.ai_model?.name }} ({{ parse.ai_model?.model_identifier }})
                  </p>
                  <p class="text-sm text-muted-foreground">
                    Status: 
                    <span
                      :class="{
                        'text-green-600': parse.status === 'completed',
                        'text-yellow-600': parse.status === 'processing',
                        'text-red-600': parse.status === 'failed'
                      }"
                    >
                      {{ parse.status }}
                    </span>
                  </p>
                  <p class="text-sm text-muted-foreground">
                    {{ new Date(parse.created_at).toLocaleDateString() }}
                  </p>
                </div>
                <div class="flex space-x-2">
                  <Button
                    v-if="parse.status === 'completed'"
                    variant="outline"
                    size="sm"
                    @click="viewResult(parse)"
                  >
                    View Result
                  </Button>
                  <Button
                    variant="outline"
                    size="sm"
                    @click="deleteParse(parse.id)"
                  >
                    Delete
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pdfParses.last_page > 1" class="mt-6 flex justify-center">
            <nav class="flex space-x-2">
              <Button
                v-for="page in pdfParses.last_page"
                :key="page"
                variant="outline"
                size="sm"
                :class="{ 'bg-primary text-primary-foreground': page === pdfParses.current_page }"
                @click="goToPage(page)"
              >
                {{ page }}
              </Button>
            </nav>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import Heading from '@/components/Heading.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'

interface AiModel {
  id: number
  name: string
  model_identifier: string
}

interface PdfParse {
  id: number
  original_filename: string
  status: string
  created_at: string
  ai_model?: AiModel
}

interface PaginatedData {
  data: PdfParse[]
  current_page: number
  last_page: number
}

interface Props {
  aiModels: AiModel[]
  pdfParses: PaginatedData
  canUseAi: boolean
}

const props = defineProps<Props>()

const form = reactive({
  pdf_file: null as File | null,
  ai_model_id: ''
})

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.pdf_file = target.files[0]
  }
}

const submitForm = () => {
  if (!form.pdf_file || !form.ai_model_id) return

  const formData = new FormData()
  formData.append('pdf_file', form.pdf_file)
  formData.append('ai_model_id', form.ai_model_id)

  router.post('/pdf-parse', formData, {
    onSuccess: () => {
      form.pdf_file = null
      form.ai_model_id = ''
      // Reset file input
      const fileInput = document.getElementById('pdf_file') as HTMLInputElement
      if (fileInput) fileInput.value = ''
    }
  })
}

const viewResult = (parse: PdfParse) => {
  router.get(`/pdf-parse/${parse.id}`)
}

const deleteParse = (id: number) => {
  if (confirm('Are you sure you want to delete this parse record?')) {
    router.delete(`/pdf-parse/${id}`)
  }
}

const goToPage = (page: number) => {
  router.get('/pdf-parse', { page }, { preserveState: true })
}
</script>
