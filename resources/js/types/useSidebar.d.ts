import { Ref } from 'vue'

declare module '@/composables/useSidebar' {
  export interface SidebarContextType {
    isExpanded: Ref<boolean>
    isMobileOpen: Ref<boolean>
    isHovered: Ref<boolean>
    activeItem: Ref<string | null>
    openSubmenu: Ref<string | null>
    toggleSidebar: () => void
    toggleMobileSidebar: () => void
    setIsHovered: (isHovered: boolean) => void
    setActiveItem: (item: string | null) => void
    toggleSubmenu: (item: string) => void
  }

  export function useSidebarProvider(): SidebarContextType
  export function useSidebar(): SidebarContextType
}
