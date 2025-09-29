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
        class="flex-1 transition-all duration-300 ease-in-out bg-sidebar-bg text-sidebar-text min-h-screen pt-16 lg:pt-0"
        :class="[(isExpanded || isHovered) ? 'lg:ml-72' : 'lg:ml-20', isMobileOpen ? 'ml-0' : '']"
      >
        <div class="flex-1 flex flex-col">
          <main role="main" class="p-4 mx-auto max-w-[var(--breakpoint-2xl)] md:p-6">
            <div class="page-header mb-4">
              <slot name="header"></slot>
            </div>
            <div class="page-content">
              <slot></slot>
            </div>
          </main>
        </div>
      </div>
  </div>
</template>
