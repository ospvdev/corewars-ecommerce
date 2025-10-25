<script setup lang="ts">
    import { useSidebar } from '@/composables/useSidebar';
    import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
    import { defineProps } from 'vue';
    import type { Ref } from 'vue';
    const { toggleSidebar, isMobileOpen, isExpanded, isHovered } = useSidebar();

    const isDesktop = ref(false);
    const handleResize = () => {
      isDesktop.value = window.innerWidth >= 1024;
    }
    onMounted(() => {
      isDesktop.value = window.innerWidth >= 1024;
    window.addEventListener('resize', handleResize);
    });

    onBeforeUnmount(() => {
      window.removeEventListener('resize', handleResize);
    });

    const showExpanded = computed(() => {
      return isExpanded.value || isHovered.value || isMobileOpen.value;
    });

    const leftClass = computed(() => {
      return showExpanded.value ? 'lg:left-[18rem]' : 'lg:left-[5rem]';
    })

    const headerStyle = computed(() => {
    if (!isDesktop.value) return {}
    if (showExpanded.value) return { width: 'calc(100% - 18rem)' };
      return { width: 'calc(100% - 5rem)' };
    });

    const showDotsPanel = ref(false);
    const dotsBtn: Ref<HTMLElement | null> = ref(null);
    const dotsPanel: Ref<HTMLElement | null> = ref(null);

    const toggleDots = (): void => {
      showDotsPanel.value = !showDotsPanel.value
    }

    const onDocumentClick = (e: MouseEvent): void => {
    const btn = dotsBtn.value;
    const panel = dotsPanel.value;
    if (!showDotsPanel.value) return;
    const target = e.target as Node | null;
    if (btn && target && btn.contains(target as Node)) return;
    if (panel && target && panel.contains(target as Node)) return;
      showDotsPanel.value = false;
    }

    const data = defineProps<{ btnActive: string, user: { haveAccess?: boolean; name?: string; role?: string } }>();
    onMounted(() => {
      document.addEventListener('click', onDocumentClick);
    });

    onBeforeUnmount(() => {
      document.removeEventListener('click', onDocumentClick);
    });
</script>

<style scoped>
.topbar { background-color: var(--color-bg-2); color: var(--color-white); }
.text-sidebar-icon { color: rgba(255,255,255,0.66); }
.profile img { box-shadow: 0 0 0 2px rgba(255,255,255,0.02); }

input::placeholder { color: rgba(255,255,255,0.45); }

input { border: 1px solid rgba(255,255,255,0.04); color: var(--color-white); }

.topbar .block.lg\:hidden { pointer-events: none; }
.topbar .block.lg\:hidden * { pointer-events: auto; }

.dots-panel { min-width: 14rem; }

.topbar { box-sizing: border-box; overflow: visible; }

@media (max-width: 1024px) {
  .topbar .block.lg\:hidden img { width: 2.5rem; height: 2.5rem; }
  .topbar .block.lg\:hidden .text-lg { font-size: 1rem; }
}

</style>
<template>
  <header :class="['topbar fixed inset-x-0 top-0 h-16 flex items-center px-6 md:px-10 bg-sidebar-bg text-sidebar-text border-b border-sidebar-border z-50', leftClass]" :style="headerStyle">
    <button v-if="isMobileOpen" @click="toggleSidebar" class="lg:hidden absolute left-3 top-3 p-2 w-10 h-10 rounded-lg text-sidebar-icon flex items-center justify-center border border-sidebar-border hover:bg-sidebar-hover" aria-label="close-sidebar">
      <i class="fa-solid fa-xmark w-4 h-4"></i>
    </button>
    <template v-if="!isMobileOpen">
      <button @click="toggleSidebar" class="mr-2 p-2 w-10 h-10 rounded-lg text-sidebar-icon flex items-center justify-center border border-sidebar-border hover:bg-sidebar-hover" aria-label="toggle-sidebar">
        <i class="fa-solid fa-bars w-4 h-4"></i>
      </button>
    </template>
      <div class="flex-1 flex items-center justify-center lg:justify-start">
        <div class="block lg:hidden absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 flex items-center gap-3">
          <div class="w-10 h-10 rounded-md bg-primary-ghost flex items-center justify-center overflow-hidden">
            <img src="/img/logo.png" alt="logo" class="w-8 h-8 object-contain" />
          </div>
          <div class="text-lg font-semibold">TailAdmin</div>
        </div>

      <div class="hidden lg:block relative w-full max-w-3xl">
        <input placeholder="Search or type command..." class="w-full text-sm bg-sidebar-hover text-sidebar-text rounded-lg pl-10 pr-4 py-1.5 border border-sidebar-border focus:outline-none shadow-sm" />
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sidebar-icon"></i>
      </div>
    </div>

  <div class="flex items-center gap-3 lg:mt-0">
      <div class="relative lg:hidden">
        <button ref="dotsBtn" @click.stop="toggleDots" class="p-2 w-10 h-10 rounded-lg hover:bg-sidebar-hover text-sidebar-icon flex items-center justify-center border border-sidebar-border" aria-label="more">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
        <div v-if="showDotsPanel" ref="dotsPanel" class="dots-panel absolute right-0 mt-2 w-56 rounded-md bg-sidebar-bg border border-sidebar-border shadow-lg z-[60] text-sidebar-text">
          <div class="p-3">
            <div class="text-sm font-medium mb-2">Quick actions</div>
            <ul class="space-y-2">
              <li><button class="w-full text-left px-2 py-2 rounded hover:bg-sidebar-hover">Profile</button></li>
              <li><button class="w-full text-left px-2 py-2 rounded hover:bg-sidebar-hover">Settings</button></li>
              <li><button class="w-full text-left px-2 py-2 rounded hover:bg-sidebar-hover">Help</button></li>
              <li><button class="w-full text-left px-2 py-2 rounded hover:bg-sidebar-hover">Sign out</button></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="hidden lg:flex items-center gap-3">
        <button class="p-2 w-10 h-10 rounded-lg hover:bg-sidebar-hover text-sidebar-icon flex items-center justify-center border border-sidebar-border" title="Theme">
          <i class="fa-solid fa-sun"></i>
        </button>

        <button class="p-2 w-10 h-10 rounded-lg hover:bg-sidebar-hover text-sidebar-icon flex items-center justify-center border border-sidebar-border" title="Notifications">
          <i class="fa-solid fa-bell"></i>
        </button>

        <div class="profile flex items-center gap-2">
          <img src="/img/859561.jpg" class="w-8 h-8 rounded-full" alt="user" />
          <div class="hidden sm:block text-sm">{{ data.user.name }}</div>
          <i class="fa-solid fa-chevron-down text-sidebar-icon"></i>
        </div>
      </div>
    </div>
  </header>
</template>
