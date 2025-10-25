<script setup lang="ts">
import { defineProps, ref, onMounted } from 'vue';
import AppAdminLayout from '@/layouts/AppAdminLayout.vue';
import TableOrders from '@/components/admin/layout/ecommerce/TableSimple.vue';

interface User {
  haveAccess?: boolean;
  name?: string;
  role?: string;
  permissions?: string[];
}

interface CardInfo{
  title: string;
  value: number;
  icon: string;
  porcentaje: number;
  money?: boolean;
}

const dataCards: CardInfo[] = [
  { title: 'Total Productos', value: 3782, icon: 'fa-box-open', porcentaje: 11.01 },
  { title: 'Total Ventas', value: 1250, icon: 'fa-shopping-cart', porcentaje: 8.5 },
  { title: 'Nuevos Clientes', value: 342, icon: 'fa-user-plus', porcentaje: 5.2 },
  { title: 'Ingresos Totales', value: 45230, icon: 'fa-dollar-sign', porcentaje: 12.3, money: true},
];

function formatCurrency(value: number, money?: boolean): string {
  if (money) {
    return '$' + value.toLocaleString();
  }
  return value.toLocaleString();
}

const data = defineProps<{ btnActive: string, user: User }>();
const selected = ref('optionOne');

onMounted(() => {
  // Minimal, low-risk SVG scaling: add viewBox/preserveAspectRatio when the SVG is static
  try {
    const container = document.getElementById('chartFour')
    if (!container) return
    const canvas = container.querySelector<HTMLElement>('.apexcharts-canvas')
    if (!canvas) return
    const svg = canvas.querySelector<SVGElement>('svg')
    if (!svg) return

    // ensure root svg uses percent width so CSS can size it
    svg.setAttribute('width', '100%')

    // Detect meaningful internal width from ApexCharts internals (clip rects)
    let internalW: number | null = null
    const clipRect = svg.querySelector<SVGRectElement>('#SvgjsRect1016') || svg.querySelector<SVGRectElement>('defs rect')
    if (clipRect) {
      const val = clipRect.getAttribute('width')
      if (val && !val.includes('%')) internalW = Math.round(parseFloat(val))
    }

    // Fallback to bounding box
    if (!internalW) {
      try {
        const bb = (svg as any).getBBox ? (svg as any).getBBox() : null
        if (bb && bb.width) internalW = Math.round(bb.width)
      } catch (_) {
        internalW = null
      }
    }

    // Height fallback
    let svgH = 0
    const hAttr = svg.getAttribute('height')
    if (hAttr && !hAttr.includes('%')) svgH = Math.round(parseFloat(hAttr))
    if (!svgH) svgH = Math.round(svg.getBoundingClientRect().height || 0)

    if (internalW && svgH) {
      svg.setAttribute('viewBox', `0 0 ${internalW} ${svgH}`)
      svg.setAttribute('preserveAspectRatio', 'none')
      svg.style.width = '100%'
    } else {
      // Last-resort: normalize numeric widths/lines to container width
      const w = Math.max(0, container.clientWidth)
      svg.querySelectorAll<SVGRectElement>('[width]').forEach(el => {
        const v = el.getAttribute('width') || ''
        if (v && !v.includes('%')) el.setAttribute('width', String(w))
      })
      svg.querySelectorAll<SVGLineElement>('line').forEach(l => {
        if (l.hasAttribute('x2')) l.setAttribute('x2', String(w))
      })
    }
  } catch (err) {
    // eslint-disable-next-line no-console
    console.warn('Error while applying SVG scaling', err)
  }
})
</script>


<style scoped>
/* Make apexcharts responsive inside our container */
.custom-scrollbar {
  /* keep custom scrollbar styles if any, but prevent horizontal overflow */
  overflow-x: hidden;
}

/* Force apexcharts svg/canvas to occupy full width of parent */
.apexcharts-canvas, .apexcharts-svg {
  width: 100% !important;
  max-width: 100% !important;
}

/* Make chart height responsive on small screens */
@media (max-width: 640px) {
  .apexcharts-canvas, .apexcharts-svg {
    height: 240px !important;
  }
}

/* ApexCharts grid line / border color to match sidebar-border subtle tone */
:deep(.apexcharts-gridline),
:deep(.apexcharts-grid line),
:deep(.apexcharts-grid-borders line) {
  stroke: rgba(255,255,255,0.04) !important;
  stroke-opacity: 1 !important;
}

:deep(.apexcharts-yaxis .apexcharts-text) {
  /* keep y-axis group text base very subtle for any non-value text */
  fill: rgba(255,255,255,0.04) !important;
}

:deep(.apexcharts-yaxis .apexcharts-text tspan) {
  /* numeric y-axis values: use primary color (text-primary) */
  fill: rgba(255,255,255,0.6) !important;
}

/* Extra overrides for remaining grid/border lines and x-axis labels */
:deep(.apexcharts-ycrosshairs),
:deep(.apexcharts-ycrosshairs-hidden),
:deep(.apexcharts-grid-borders .apexcharts-gridline),
:deep(.apexcharts-grid-borders line) {
  stroke: rgba(255,255,255,0.04) !important;
}

:deep(.apexcharts-xaxis .apexcharts-text),
:deep(.apexcharts-xaxis .apexcharts-text tspan),
:deep(.apexcharts-xaxis text) {
  fill: rgba(255,255,255,0.6) !important;
}

:deep(.apexcharts-xaxis .apexcharts-xaxis-texts-g) {
  color: rgba(255,255,255,0.18) !important;
}
</style>

<template>
  <AppAdminLayout :btnActive="data.btnActive" :user="data.user">
    <!-- Grids tarjetas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="card in dataCards" class="w-full rounded-md border border-sidebar-border p-5 bg-sidebar-hover md:p-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-sidebar-border bg-sidebar-hover">
          <i class="text-sidebar-icon fa-solid" :class="card.icon"></i>
        </div>

        <div class="mt-5 flex items-end justify-between">
          <div>
            <span class="text-lg text-gray-500 dark:text-gray-400">{{ card.title }}</span>
            <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
              {{ formatCurrency(card.value, card.money) }}
            </h4>
          </div>

          <span class="flex items-center gap-1 rounded-full bg-primary/60 py-0.5 pl-2 pr-2.5 text-sm font-medium">
            <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.1236 1.37432 6.12391 1.37432 6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" fill=""></path>
            </svg>
            {{ card.porcentaje }}%
          </span>
        </div>
      </div>
    </div>
    <TableOrders />
  </AppAdminLayout>
</template>
