<script setup lang="ts">
  import { ref, computed, onMounted, defineProps } from 'vue'
  import { useSidebar } from '@/composables/useSidebar'
  import { Link, usePage } from '@inertiajs/vue3'

  import {
    GridIcon,
    CalenderIcon,
    UserCircleIcon,
    BarChartIcon,
    PieChartIcon,
    ChevronDownIcon,
    SettingsIcon,
    TableIcon,
    ListIcon,
    ArchiveIcon,
    PlugInIcon,
    BoxIcon,
    InfoIcon,
    SupportIcon
  } from '@/icons';

  const data = defineProps<{ btnActive: string, user: { haveAccess?: boolean; name?: string; role?: string; permissions?: string[] } }>();
  const permissions = JSON.parse(JSON.stringify(data.user.permissions))



  const activeBtn = ref<string>(String(data.btnActive || ''));

  // Ensure we sync initial server-provided active button after mount (hydration safe)
  onMounted(() => {
    if (data.btnActive) activeBtn.value = String(data.btnActive)
  })

  interface SubItem {
    name: string
    path: string
    new?: boolean
    active?: boolean,
    permissions?: boolean
  }

  interface MenuItem {
    icon: any
    name: string
    path?: string | null
    subItems?: SubItem[]
    active?: boolean
    permissions?: boolean
  }

  interface MenuGroup {
    title: string
    items: MenuItem[]
  }

  const menuGroups: MenuGroup[] = [
    {
      title: 'ECOMMERCE',
      items: [
        { 
          icon: GridIcon, name: 'Dashboard',
          path: '/admin',
          permissions: havePermission('orders', 'canView') 
        },
        { 
          icon: BoxIcon, 
          name: 'Productos', 
          subItems: [
            { name: 'Ordenes', path: 'dashboard/categorias' , permissions: havePermission('orders', 'canView')},
            { name: 'Productos', path: 'dashboard/productos' , permissions: havePermission('products', 'canView')},
          ], 
          path: '' 
        },
        { 
          icon: CalenderIcon, 
          name: 'Descuentos', 
          subItems: [
            { name: 'Ordenes', path: 'dashboard/categorias' , permissions: havePermission('orders', 'canView')},
            { name: 'Productos', path: 'dashboard/productos' , permissions: havePermission('products', 'canView')},
          ], 
          path: '' 
        },
        { 
          icon: ArchiveIcon, 
          name: 'Pedidos', 
          subItems: [
            { name: 'Ordenes', path: 'dashboard/categorias' , permissions: havePermission('orders', 'canView')},
            { name: 'Productos', path: 'dashboard/productos' , permissions: havePermission('products', 'canView')},
          ], 
          path: '' 
        },
        { 
          icon: UserCircleIcon, 
          name: 'Clientes', 
          subItems: [
            { name: 'Ordenes', path: 'dashboard/categorias' , permissions: havePermission('orders', 'canView')},
            { name: 'Productos', path: 'dashboard/productos' , permissions: havePermission('products', 'canView')},
          ], 
          path: '' 
        },
        { 
          icon: SettingsIcon, 
          name: 'Tienda', 
          subItems: [
            { name: 'Ordenes', path: 'dashboard/categorias' , permissions: havePermission('orders', 'canView')},
            { name: 'Productos', path: 'dashboard/productos' , permissions: havePermission('products', 'canView')},
          ], 
          path: '' 
        }
      ],
    },
    {
      title: 'OTRO',
      items: [
        { icon: InfoIcon, name: 'Ayuda', subItems: [{ name: 'Line Chart', path: '' }], path: '' },
        { icon: SupportIcon, name: 'Documentación', subItems: [{ name: 'Signin', path: '' }, { name: 'Signup', path: '' }], path: '' },
      ],
    },
  ]

  const open = ref<Record<string, boolean>>({})
  const { isExpanded, isMobileOpen, isHovered, setIsHovered } = useSidebar()

  const showExpanded = computed(() => {
    return isExpanded.value || isHovered.value || isMobileOpen.value
  })

  const asideClass = computed(() => {
  const widthClass = showExpanded.value ? 'w-72 border-r border-sidebar-border' : 'w-20 collapsed-sidebar border-r border-sidebar-border'
    const base = 'fixed left-0 top-0 h-screen pt-18 lg:pt-4 overflow-auto'
    const px = showExpanded.value ? 'px-4' : 'px-2'
    const z = isMobileOpen.value ? 'z-40' : 'z-10'
    const translate = isMobileOpen.value ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    return [widthClass, `${base} ${px} bg-sidebar-bg text-sidebar-text ${z} transform transition-all duration-200`, translate]
  })
  const page = usePage()

  const currentPath = computed(() => {
    if (page && page.props && page.props.url) return page.props.url
    return typeof window !== 'undefined' ? window.location.pathname : ''
  })

  // Simple helper that prefixes /admin if not present
  const buildAdminHref = (path: string | null | undefined) => {
    if (!path) return ''
    if (path.startsWith('/admin')) return path
    if (path.startsWith('/')) return `/admin${path}`
    return `/admin/${path}`
  }

  const isActive = (path: string | null | undefined) => {
    if (!path) return false
    const href = buildAdminHref(path)
    const normalize = (s: any) => String(s || '').replace(/\/?$/, '')
    const current = normalize(currentPath.value)
    const target = normalize(href)
    return current === target
  }

  const hasActiveSubItems = (item: MenuItem) => {
    return !!(item.subItems && item.subItems.some((s: SubItem) => s.name === activeBtn.value))
  }

  const isItemActive = (item?: MenuItem | null) => {
    if (!item) return false
    if (item.name === activeBtn.value) return true
    if (item.path && isActive(item.path)) return true
    return false
  }

  const toggleOpen = (key: string) => {
    const currently = !!open.value[key]
    open.value = {}
    if (!currently) open.value[key] = true
  }

  const onMouseEnter = () => {
    if (!isMobileOpen.value && !isExpanded.value) setIsHovered(true)
  }

  const onMouseLeave = () => {
    if (!isMobileOpen.value && !isExpanded.value) setIsHovered(false)
  }

  onMounted(() => {
    for (let g = 0; g < menuGroups.length; g++) {
      const group = menuGroups[g]
      for (let i = 0; i < group.items.length; i++) {
        const item = group.items[i]
        if (item.subItems) {
          const found = item.subItems.some((s) => s.name === activeBtn.value)
          if (found) {
            open.value = {}
            open.value[`${g}-${i}`] = true
            return
          }
        } else if (item.name === activeBtn.value) {
          return
        }
      }
    }
  });

  function setActiveBtn(newActive: string){
    activeBtn.value = newActive;
    // console.log('Boton activo: ' + activeBtn.value)
  }

  function havePermission(module: string, action: string): boolean {
    return permissions[module] && permissions[module][action];
  }

  console.log('Puede ver productos:', havePermission('users', 'canView'));
</script>

<template>
      <aside
          @mouseenter="onMouseEnter"
          @mouseleave="onMouseLeave"
          :class="asideClass"
        >
		<div class="mb-6 hidden lg:flex items-center gap-3">
			<div class="w-12 h-12 rounded-lg flex items-center justify-center bg-primary-ghost">
				<img src="/img/logo.png" alt="logo" class="w-10 h-10 object-contain" />
			</div>
			<div v-if="showExpanded" class="text-2xl font-semibold">TailAdmin</div>
		</div>

		<nav>
			<div v-for="(group, gIndex) in menuGroups" :key="group.title" class="mb-6">
				<div v-if="showExpanded" class="mb-3 text-xs font-medium text-sidebar-muted px-2">{{ group.title }}</div>
				<ul class="space-y-2">
					<li v-for="(item, iIndex) in group.items" :key="item.name">
            <div :class="[ 'menu-item-row rounded-md overflow-hidden', showExpanded ? 'flex items-center justify-between' : 'flex items-center justify-center', isItemActive(item) ? 'active' : '' ]">
							<template v-if="item.subItems">
								<button
									type="button"
									class="sidebar-link flex items-center"
									:class="showExpanded ? 'gap-3 px-2 py-2 rounded-md block flex-1 min-w-0 text-left' : 'justify-center py-2 w-full'"
									@click="toggleOpen(gIndex + '-' + iIndex)"
								>
									<span class="icon-wrap flex items-center justify-center rounded-md" :class="showExpanded ? 'w-8 h-8' : 'w-full'">
                    <component :is="item.icon" :class="['w-5 h-5', (isItemActive(item) || hasActiveSubItems(item)) ? 'text-primary' : 'text-sidebar-icon']" />
									</span>
                  <div v-if="showExpanded" class="text-sm font-medium whitespace-nowrap truncate" :class="isItemActive(item) ? 'text-primary' : (hasActiveSubItems(item) ? 'text-sidebar-text text-opacity-90' : 'text-sidebar-text')">{{ item.name }}</div>
								</button>
							</template>
							<template v-else-if="item.path">
                <Link
                  :href="buildAdminHref(item.path)"
									class="sidebar-link"
									:class="showExpanded ? 'flex items-center gap-3 px-2 py-2 rounded-md block flex-1 min-w-0' : 'flex justify-center py-2 w-full'"
									:aria-current="isActive(item.path) ? 'page' : undefined"
                  @click="setActiveBtn(item.name)"
								>
									<span class="icon-wrap flex items-center justify-center rounded-md" :class="showExpanded ? 'w-8 h-8' : 'w-full'">
										<component :is="item.icon" :class="['w-5 h-5', isActive(item.path) ? 'text-primary' : 'text-sidebar-icon']" />
									</span>
									<div v-if="showExpanded" class="text-sm font-medium whitespace-nowrap truncate" :class="isActive(item.path) ? 'text-primary' : 'text-sidebar-text'">{{ item.name }}</div>
								</Link>
							</template>
							<template v-else>
                                <div :class="[ showExpanded ? 'sidebar-link flex items-center gap-3 px-2 py-2 rounded-md' : 'flex justify-center py-3 w-full', hasActiveSubItems(item) ? 'bg-primary-active' : '' ]">
									<span :class="[ showExpanded ? 'w-8 h-8 flex items-center justify-center rounded-md' : 'w-full flex items-center justify-center' ]">
                                        <component :is="item.icon" :class="['w-5 h-5', hasActiveSubItems(item) ? 'text-primary' : 'text-sidebar-icon']" />
									</span>
									<div v-if="showExpanded" class="text-sm font-medium text-sidebar-text">{{ item.name }}</div>
								</div>
							</template>
							<div v-if="item.subItems && showExpanded" class="pr-2 flex items-center flex-none">
								<button @click="toggleOpen(gIndex + '-' + iIndex)" class="p-2 rounded-full hover:bg-sidebar-hover">
									<ChevronDownIcon :class="['w-5 h-5 transition-transform', open[gIndex + '-' + iIndex] ? 'rotate-180 text-primary' : 'text-sidebar-icon']" />
								</button>
							</div>
						</div>

            <ul v-if="item.subItems && open[gIndex + '-' + iIndex] && showExpanded" class="mt-2 ml-10 space-y-1">
              <li v-for="sub in item.subItems" :key="sub.name">
                <template v-if="sub.path">
                  <Link v-show="sub.permissions" :href="buildAdminHref(sub.path)" @click="setActiveBtn(sub.name)" class="sub-link text-sm" :class="(isActive(sub.path) || sub.name === activeBtn) ? 'bg-active text-primary' : 'text-sidebar-sub'">
                    {{ sub.name }}
                    <span v-if="sub.new" class="ml-2 inline-block bg-new-ghost text-new text-xs px-2 py-0.5 rounded-full">NEW</span>
                  </Link>
                </template>
                <template v-else>
                  <button type="button" @click="setActiveBtn(sub.name)" class="sub-link text-sm" :class="sub.name === activeBtn ? 'bg-active text-primary' : 'text-sidebar-sub'">
                    {{ sub.name }}
                    <span v-if="sub.new" class="ml-2 inline-block bg-new-ghost text-new text-xs px-2 py-0.5 rounded-full">NEW</span>
                  </button>
                </template>
              </li>
            </ul>
					</li>
				</ul>
			</div>
		</nav>
	</aside>
</template>

<style scoped>
.bg-sidebar-bg { background-color: var(--color-bg-2); }
.text-sidebar-text { color: var(--color-white); }
.border-sidebar-border { border-color: rgba(255,255,255,0.04); }
.text-sidebar-sub { color: rgba(255,255,255,0.9); }
.text-sidebar-muted { color: rgba(255,255,255,0.6); }
.bg-sidebar-hover { background-color: rgba(17,21,30,0.6); }
.text-sidebar-icon { color: rgba(255,255,255,0.6); }
.text-primary { color: var(--color-primary); }
.bg-primary-active { background-color: rgba(70,233,158,0.12); }
.bg-new-ghost { background-color: rgba(70,233,158,0.12); }
.text-new { color: #10b981; }

.btn-hover-bg { background-color: rgba(17,21,30,0.85); }

.sidebar-link { transition: background-color .12s ease; background: transparent; }
.menu-item-row { transition: background-color .12s ease; }
.menu-item-row:hover { background-color: rgba(17,21,30,0.6); }
.menu-item-row.active { background-color: rgba(70,233,158,0.12); }
.menu-item-row .icon-wrap { background-color: transparent; }
.menu-item-row:hover .icon-wrap,
.menu-item-row.active .icon-wrap { background-color: transparent; }

.w-20 { width: 5rem; }
.w-72 { width: 18rem; }
.menu-item-row { padding-left: 0.25rem; padding-right: 0.25rem; }
.menu-item-row .sidebar-link { width: 100%; }

.collapsed-sidebar { border-right: 1px solid rgba(255,255,255,0.04); }
.collapsed-sidebar .menu-item-row { padding-left: 0; padding-right: 0; }
.collapsed-sidebar .menu-item-row .sidebar-link { justify-content: center; padding-left: 0; padding-right: 0; }
.collapsed-sidebar .menu-item-row .icon-wrap { width: 2rem; height: 2rem; display: flex; justify-content: center; }
.collapsed-sidebar .menu-item-row .icon-wrap > * { width: 1.25rem; height: 1.25rem; }
.collapsed-sidebar .text-sm { display: none; }

.collapsed-sidebar .menu-item-row:hover { background: transparent; }
.collapsed-sidebar .menu-item-row .sidebar-link:hover { background: transparent; }

.border-sidebar-divider { border-right: 1px solid rgba(255,255,255,0.04); }

.sub-link { display: block; padding-left: 0.75rem; padding-right: 0.75rem; padding-top: 0.5rem; padding-bottom: 0.5rem; border-radius: 0.5rem; transition: background-color .12s ease; }
.sub-link:hover { background-color: rgba(17,21,30,0.6); }
.sub-link.bg-active { background-color: rgba(70,233,158,0.12); }

@media (max-width: 1024px) {
	aside { width: 260px; }
}

</style>

