<script setup lang="ts">
    import { defineProps } from 'vue';
    import AppSidebar from '@/components/admin/layout/Sidebar.vue';
    import Topbar from '@/components/admin/layout/Topbar.vue';
    import { useSidebarProvider } from '@/composables/useSidebar';

    const { isExpanded, isHovered, isMobileOpen, toggleSidebar } = useSidebarProvider();
    const data = defineProps<{ btnActive: string, user: { haveAccess?: boolean; name?: string; role?: string; permissions?: string[] } }>();
</script>

<style scoped>
    .z-20 { z-index: 20; }
</style>

<template>
  <div class="min-h-screen xl:flex">
    <Topbar :btnActive="data.btnActive" :user="data.user" />
    <AppSidebar :btnActive="data.btnActive" :user="data.user" />

    <div v-if="isMobileOpen" @click="toggleSidebar" class="lg:hidden fixed inset-0 bg-black/40 z-30"></div>

      <div
        class="flex-1 transition-all duration-300 ease-in-out bg-sidebar-bg text-sidebar-text min-h-screen"
        :class="[(isExpanded || isHovered) ? 'lg:ml-72' : 'lg:ml-20', isMobileOpen ? 'ml-0' : '']"
        style="padding-top: var(--topbar-height, 4rem);"
      >
        <div class="flex-1 flex flex-col">
          <main role="main" class="flex-1">
            <div class="page-header mb-4 px-4 md:px-6">
              <slot name="header"></slot>
            </div>

            <div class="page-content">
              <div class="mx-auto max-w-8xl px-4 md:px-6 lg:px-8 py-6">
                <slot></slot>
              </div>
            </div>
          </main>
        </div>
      </div>
  </div>
</template>
