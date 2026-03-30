<script setup lang="ts">
import type { SearchResult, SearchResultType } from '@/types'
import { router } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { route } from 'ziggy-js'

interface SearchResponse {
  results: SearchResult[]
}

interface CommandItem {
  id: string
  label: string
  description: string
  icon: string
  onSelect: () => void
}

const open = ref(false)
const loading = ref(false)
const searchTerm = ref('')
const results = ref<SearchResult[]>([])

let debounceTimeout: ReturnType<typeof setTimeout> | null = null
let activeController: AbortController | null = null

const defaultItems: CommandItem[] = [
  {
    id: 'projects',
    label: 'Проекты',
    description: 'Перейти к списку проектов',
    icon: 'i-lucide-folder-open-dot',
    onSelect: () => router.visit(route('projects.index')),
  },
  {
    id: 'users',
    label: 'Пользователи',
    description: 'Управление людьми и app roles',
    icon: 'i-lucide-users-round',
    onSelect: () => router.visit(route('users.index')),
  },
  {
    id: 'resources',
    label: 'Ресурсы',
    description: 'Сервисы, системы и точки доступа',
    icon: 'i-lucide-server-cog',
    onSelect: () => router.visit(route('resources.index')),
  },
  {
    id: 'access-grants',
    label: 'Доступы',
    description: 'Кому и какой доступ выдан',
    icon: 'i-lucide-key-round',
    onSelect: () => router.visit(route('access-grants.index')),
  },
  {
    id: 'secrets',
    label: 'Секреты',
    description: 'Ключи, токены и пароли',
    icon: 'i-lucide-shield-ellipsis',
    onSelect: () => router.visit(route('secrets.index')),
  },
]

const itemTypeLabels: Record<SearchResultType, string> = {
  project: 'Проект',
  user: 'Пользователь',
  resource: 'Ресурс',
  access: 'Доступ',
  secret: 'Секрет',
}

const itemTypeIcons: Record<SearchResultType, string> = {
  project: 'i-lucide-folder-open-dot',
  user: 'i-lucide-users-round',
  resource: 'i-lucide-server-cog',
  access: 'i-lucide-key-round',
  secret: 'i-lucide-shield-ellipsis',
}

const resultItems = computed<CommandItem[]>(() =>
  results.value.map(result => ({
    id: `${result.type}:${result.id}`,
    label: result.title,
    description: `${itemTypeLabels[result.type]} · ${result.subtitle}`,
    icon: itemTypeIcons[result.type],
    onSelect: () => {
      open.value = false
      router.visit(result.href)
    },
  })),
)

const groups = computed(() => {
  if (!searchTerm.value.trim()) {
    return [
      {
        id: 'sections',
        label: 'Разделы',
        items: defaultItems,
      },
    ]
  }

  return [
    {
      id: 'results',
      label: 'Результаты',
      ignoreFilter: true,
      items: resultItems.value,
    },
  ]
})

defineShortcuts({
  meta_л: {
    usingInput: true,
    handler: () => {
      open.value = true
    },
  },
})

watch(open, (isOpen) => {
  if (isOpen) {
    return
  }

  loading.value = false
  searchTerm.value = ''
  results.value = []

  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
    debounceTimeout = null
  }

  activeController?.abort()
  activeController = null
})

watch(searchTerm, (value) => {
  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
    debounceTimeout = null
  }

  activeController?.abort()
  activeController = null

  const query = value.trim()

  if (!query) {
    loading.value = false
    results.value = []
    return
  }

  debounceTimeout = setTimeout(async () => {
    const controller = new AbortController()

    activeController = controller
    loading.value = true

    try {
      const response = await fetch(route('search.index', { q: query }), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        signal: controller.signal,
      })

      if (!response.ok) {
        throw new Error(`Search request failed with status ${response.status}`)
      }

      const payload = await response.json() as SearchResponse

      if (searchTerm.value.trim() === query) {
        results.value = payload.results ?? []
      }
    } catch (error) {
      if (error instanceof DOMException && error.name === 'AbortError') {
        return
      }

      results.value = []
    } finally {
      if (activeController === controller) {
        loading.value = false
        activeController = null
      }
    }
  }, 180)
})

onBeforeUnmount(() => {
  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
  }

  activeController?.abort()
})
</script>

<template>
  <div class="flex items-center gap-2">
    <UButton
      color="neutral"
      variant="outline"
      class="hidden min-w-72 justify-between sm:flex"
      @click="open = true"
    >
      <template #leading>
        <UIcon name="i-lucide-search" class="size-4" />
      </template>

      <span class="truncate">Искать проект, доступ, ресурс или секрет</span>

      <template #trailing>
        <div class="flex items-center gap-1">
          <UKbd value="meta" />
          <UKbd value="K" />
          <UKbd value="meta" />
          <UKbd value="Л" />
        </div>
      </template>
    </UButton>

    <UButton
      color="neutral"
      variant="ghost"
      square
      class="sm:hidden"
      icon="i-lucide-search"
      @click="open = true"
    />

    <UDashboardSearch
      v-model:open="open"
      v-model:search-term="searchTerm"
      :groups="groups"
      :loading="loading"
      :color-mode="false"
      close
      placeholder="Начните вводить название проекта, человека, ресурса или секрета"
      title="Глобальный поиск"
      description="Быстрый переход по ключевым сущностям Access Atlas."
    >
      <template #footer>
        <div class="flex items-center justify-between gap-3 px-2 py-1 text-xs text-muted">
          <span>Enter открывает результат, Esc закрывает поиск.</span>
          <div class="hidden items-center gap-1 sm:flex">
            <UKbd value="meta" />
            <UKbd value="K" />
            <UKbd value="meta" />
            <UKbd value="Л" />
          </div>
        </div>
      </template>
    </UDashboardSearch>
  </div>
</template>
