<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { Head, Link } from '@inertiajs/vue3'
import { login, dashboard } from '@/routes'
import {
  Shield,
  KeyRound,
  FolderLock,
  ClipboardCheck,
  ScrollText,
  UserMinus,
  ArrowRight,
  Lock,
  Eye,
  RotateCcw,
  ChevronRight,
} from 'lucide-vue-next'

const features = [
  {
    icon: Shield,
    title: 'Управление доступами',
    description: 'Выдача и отзыв доступов с истечением срока. Полный контроль кто, куда и когда получил доступ.',
  },
  {
    icon: KeyRound,
    title: 'Хранилище секретов',
    description: 'Metadata-first подход к секретам. Безопасное хранение с политиками раскрытия и ротацией.',
  },
  {
    icon: FolderLock,
    title: 'Проекты и ресурсы',
    description: 'Организуйте ресурсы по проектам. Привязывайте участников, среды и внешние системы.',
  },
  {
    icon: ClipboardCheck,
    title: 'Ревью доступов',
    description: 'Периодические ревью для проверки актуальности доступов. Задачи с дедлайнами и ответственными.',
  },
  {
    icon: ScrollText,
    title: 'Аудит-лог',
    description: 'Каждое действие фиксируется. Полная история изменений для compliance и расследований.',
  },
  {
    icon: UserMinus,
    title: 'Offboarding',
    description: 'Чеклист-процесс отзыва всех доступов при уходе сотрудника. Ничего не будет забыто.',
  },
]

const steps = [
  {
    number: '01',
    title: 'Настройте проекты',
    description: 'Создайте проекты, добавьте ресурсы и участников команды.',
  },
  {
    number: '02',
    title: 'Управляйте доступами',
    description: 'Выдавайте и отзывайте доступы, храните секреты с политиками ротации.',
  },
  {
    number: '03',
    title: 'Контролируйте безопасность',
    description: 'Ревью, аудит и offboarding — всё под контролем в одном месте.',
  },
]

const trustItems = [
  { icon: Lock, label: 'Шифрование данных' },
  { icon: Eye, label: 'Политики раскрытия' },
  { icon: ScrollText, label: 'Полный аудит' },
  { icon: RotateCcw, label: 'Ротация секретов' },
]
</script>

<template>
  <Head title="Access Atlas — Управление доступами" />

  <div class="min-h-svh bg-default text-default">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 border-b border-default/60 bg-default/80 backdrop-blur-lg">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
          <AppLogoIcon class="size-7 fill-current" />
          <span class="text-lg font-semibold tracking-tight">Access Atlas</span>
        </div>

        <div class="flex items-center gap-3">
          <UButton
            :to="login()"
            variant="ghost"
            size="md"
          >
            Войти
          </UButton>
          <UButton
            :to="login()"
            size="md"
          >
            Начать работу
            <template #trailing>
              <ArrowRight class="size-4" />
            </template>
          </UButton>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-b from-primary/5 to-transparent" />
      <div class="relative mx-auto max-w-6xl px-6 py-24 text-center lg:py-32">
        <div class="mx-auto max-w-3xl">
          <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-default/60 bg-default px-4 py-1.5 text-sm text-muted">
            <Shield class="size-4" />
            <span>Единый центр управления доступами</span>
          </div>

          <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            Контроль доступов
            <span class="block text-muted">без хаоса</span>
          </h1>

          <p class="mt-6 text-lg leading-relaxed text-muted sm:text-xl">
            Access Atlas — инструмент для команд, которые хотят знать кто имеет доступ куда,
            управлять секретами и не терять контроль при росте.
          </p>

          <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <UButton
              :to="login()"
              size="xl"
            >
              Начать бесплатно
              <template #trailing>
                <ChevronRight class="size-5" />
              </template>
            </UButton>
            <UButton
              href="#features"
              variant="outline"
              size="xl"
            >
              Узнать больше
            </UButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="border-t border-default/60 py-20 lg:py-28">
      <div class="mx-auto max-w-6xl px-6">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
            Всё для управления доступами
          </h2>
          <p class="mt-4 text-lg text-muted">
            Шесть ключевых модулей, которые закрывают полный цикл — от выдачи доступа до offboarding.
          </p>
        </div>

        <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="group rounded-xl border border-default/60 bg-default p-6 transition-colors duration-200 hover:border-primary/40"
          >
            <div class="mb-4 inline-flex rounded-lg border border-default/60 p-2.5">
              <component :is="feature.icon" class="size-5 text-muted transition-colors duration-200 group-hover:text-primary" />
            </div>
            <h3 class="text-lg font-semibold">
              {{ feature.title }}
            </h3>
            <p class="mt-2 text-sm leading-relaxed text-muted">
              {{ feature.description }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section class="border-t border-default/60 bg-elevated/50 py-20 lg:py-28">
      <div class="mx-auto max-w-6xl px-6">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
            Как это работает
          </h2>
          <p class="mt-4 text-lg text-muted">
            Три шага до полного контроля над доступами в вашей команде.
          </p>
        </div>

        <div class="mt-16 grid gap-8 lg:grid-cols-3">
          <div
            v-for="step in steps"
            :key="step.number"
            class="relative rounded-xl border border-default/60 bg-default p-8"
          >
            <span class="text-5xl font-bold text-primary/10">
              {{ step.number }}
            </span>
            <h3 class="mt-4 text-xl font-semibold">
              {{ step.title }}
            </h3>
            <p class="mt-2 text-sm leading-relaxed text-muted">
              {{ step.description }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust badges -->
    <section class="border-t border-default/60 py-20 lg:py-28">
      <div class="mx-auto max-w-6xl px-6">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
            Безопасность в основе
          </h2>
          <p class="mt-4 text-lg text-muted">
            Каждый аспект Access Atlas спроектирован с приоритетом безопасности.
          </p>
        </div>

        <div class="mt-12 grid grid-cols-2 gap-6 sm:grid-cols-4">
          <div
            v-for="item in trustItems"
            :key="item.label"
            class="flex flex-col items-center gap-3 rounded-xl border border-default/60 bg-default p-6 text-center"
          >
            <div class="rounded-full border border-default/60 p-3">
              <component :is="item.icon" class="size-6 text-muted" />
            </div>
            <span class="text-sm font-medium">{{ item.label }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="border-t border-default/60 bg-elevated/50 py-20 lg:py-28">
      <div class="mx-auto max-w-6xl px-6 text-center">
        <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
          Готовы навести порядок в доступах?
        </h2>
        <p class="mt-4 text-lg text-muted">
          Начните использовать Access Atlas уже сегодня.
        </p>
        <div class="mt-8">
          <UButton
            :to="login()"
            size="xl"
          >
            Начать бесплатно
            <template #trailing>
              <ArrowRight class="size-5" />
            </template>
          </UButton>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-default/60 py-8">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-6">
        <div class="flex items-center gap-2 text-sm text-muted">
          <AppLogoIcon class="size-5 fill-current" />
          <span>Access Atlas</span>
        </div>
        <p class="text-sm text-muted">
          &copy; {{ new Date().getFullYear() }}
        </p>
      </div>
    </footer>
  </div>
</template>
