<script setup lang="ts">
import { defineProps, ref, onMounted } from 'vue';
import AppAdminLayout from '@/layouts/AppAdminLayout.vue';

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

    <div class="col-span-12 mt-4">
      <div class="rounded-md border border-sidebar-border bg-white px-5 pt-5 bg-sidebar-hover sm:px-6 sm:pt-6">
        <div class="flex flex-wrap items-start justify-between gap-5">
          <div>
            <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">
              Analíticas
            </h3>
            <span class="block text-theme-sm text-gray-500 dark:text-gray-400">
              Analíticas de visitantes de los últimos 30 días
            </span>
          </div>

          <div x-data="{selected: 'optionOne'}" class="flex items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
            <button @click="selected = 'optionOne'" :class="selected === 'optionOne' ? 'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'" class=":hover:text-white rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800">
              12 meses
            </button>

            <button @click="selected = 'optionTwo'" :class="selected === 'optionTwo' ? 'shadow-theme-xs text-gray-900 bg-white' : 'text-gray-500'" class="hover:text-gray-900dark:hover:text-white rounded-md px-3 py-2 text-theme-sm font-medium text-gray-500 dark:text-gray-400">
              30 días
            </button>

            <button @click="selected = 'optionThree'" :class="selected === 'optionThree' ? 'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'" class="rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 text-gray-500 dark:text-gray-400">
              7 días
            </button>

            <button @click="selected = 'optionFour'" :class="selected === 'optionFour' ? 'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400'" class="rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 text-gray-500 dark:text-gray-400">
              24 horas
            </button>
          </div>
        </div>
        <div class="custom-scrollbar max-w-full overflow-hidden">
          <div id="chartFour" class="w-full" style="min-height: 365px;">
            <div id="apexchartst65a8jbu" class="apexcharts-canvas apexchartst65a8jbu apexcharts-theme-" style="height: 350px; width: 100%; max-width: 100%;">
              <svg id="SvgjsSvg1006" width="100%" height="350" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)">
                <foreignObject x="0" y="0" width="100%" height="350">
                  <div style="position: relative; height: 100%; width: 100%;" xmlns="http://www.w3.org/1999/xhtml">
                    <div class="apexcharts-legend" style="max-height: 175px;"></div>
                  </div>
                </foreignObject>
                <g id="SvgjsG1019" class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)"></g>
                <g id="SvgjsG1020" class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)"></g>
                <g id="SvgjsG1191" class="apexcharts-yaxis" rel="0" transform="translate(20.01666259765625, 0)">
                  <g id="SvgjsG1192" class="apexcharts-yaxis-texts-g">
                    <text id="SvgjsText1194" font-family="Outfit, sans-serif" x="0" y="33.666666666666664" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="400" fill="#f5f5f5" class="apexcharts-text apexcharts-yaxis-label" style="font-family: Outfit, sans-serif;">
                      <tspan id="SvgjsTspan1195">400</tspan>
                      <title>400</title>
                    </text>
                    <text id="SvgjsText1197" font-family="Outfit, sans-serif" x="0" y="103.84916666666666" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="400" fill="#f5f5f5" class="apexcharts-text apexcharts-yaxis-label " style="font-family: Outfit, sans-serif;">
                      <tspan id="SvgjsTspan1198">300</tspan>
                      <title>300</title>
                    </text>
                    <text id="SvgjsText1200" font-family="Outfit, sans-serif" x="0" y="174.03166666666667" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="400" fill="#f5f5f5" class="apexcharts-text apexcharts-yaxis-label " style="font-family: Outfit, sans-serif;">
                      <tspan id="SvgjsTspan1201">200</tspan>
                      <title>200</title>
                    </text>
                    <text id="SvgjsText1203" font-family="Outfit, sans-serif" x="0" y="244.21416666666667" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="400" fill="#f5f5f5" class="apexcharts-text apexcharts-yaxis-label " style="font-family: Outfit, sans-serif;">
                      <tspan id="SvgjsTspan1204">100</tspan>
                      <title>100</title>
                    </text>
                    <text id="SvgjsText1206" font-family="Outfit, sans-serif" x="0" y="314.39666666666665" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="400" fill="#f5f5f5" class="apexcharts-text apexcharts-yaxis-label " style="font-family: Outfit, sans-serif;">
                      <tspan id="SvgjsTspan1207">0</tspan>
                      <title>0</title>
                    </text>
                  </g>
                </g>
                <g id="SvgjsG1008" class="apexcharts-inner apexcharts-graphical" transform="translate(8, 30)">
                  <defs id="SvgjsDefs1007">
                    <linearGradient id="SvgjsLinearGradient1011" x1="0" y1="0" x2="0" y2="1">
                      <stop id="SvgjsStop1012" stop-opacity="0.4" stop-color="rgba(26,227,240,0.4)" offset="0"></stop>
                      <stop id="SvgjsStop1013" stop-opacity="0.5" stop-color="rgba(190,209,230,0.5)" offset="1"></stop>
                      <stop id="SvgjsStop1014" stop-opacity="0.5" stop-color="rgba(190,209,230,0.5)" offset="1"></stop>
                    </linearGradient>
                    <clipPath id="gridRectMaskt65a8jbu">
                      <rect id="SvgjsRect1016" width="1231.9833374023438" height="280.73" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                    </clipPath>
                    <clipPath id="gridRectBarMaskt65a8jbu">
                      <rect id="SvgjsRect1017" width="1239.9833374023438" height="288.73" x="-4" y="-4" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                    </clipPath>
                    <clipPath id="gridRectMarkerMaskt65a8jbu">
                      <rect id="SvgjsRect1018" width="1231.9833374023438" height="280.73" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                    </clipPath>
                    <clipPath id="forecastMaskt65a8jbu"></clipPath>
                    <clipPath id="nonForecastMaskt65a8jbu"></clipPath>
                  </defs>
                  <!-- <rect id="SvgjsRect1015" width="18.479750061035155" height="280.73" x="507.94401372273757" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke-dasharray="3" fill="url(#SvgjsLinearGradient1011)" class="apexcharts-xcrosshairs" y2="280.73" filter="none" fill-opacity="0.9" x1="507.94401372273757" x2="507.94401372273757"></rect> -->
                  <g id="SvgjsG1086" class="apexcharts-grid">
                    <g id="SvgjsG1087" class="apexcharts-gridlines-horizontal">
                      <line id="SvgjsLine1091" x1="0" y1="70.1825" x2="1231.9833374023438" y2="70.1825" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
                      <line id="SvgjsLine1092" x1="0" y1="140.365" x2="1231.9833374023438" y2="140.365" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
                      <line id="SvgjsLine1093" x1="0" y1="210.5475" x2="1231.9833374023438" y2="210.5475" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
                    </g>
                    <g id="SvgjsG1088" class="apexcharts-gridlines-vertical"></g>
                    <line id="SvgjsLine1096" x1="0" y1="280.73" x2="1231.9833374023438" y2="280.73" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line>
                    <line id="SvgjsLine1095" x1="0" y1="1" x2="0" y2="280.73" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line>
                  </g>
                  <g id="SvgjsG1089" class="apexcharts-grid-borders">
                    <line id="SvgjsLine1090" x1="0" y1="0" x2="1231.9833374023438" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
                    <line id="SvgjsLine1094" x1="0" y1="280.73" x2="1231.9833374023438" y2="280.73" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
                  </g>
                  <g id="SvgjsG1021" class="apexcharts-bar-series apexcharts-plot-series">
                    <g id="SvgjsG1022" class="apexcharts-series" rel="1" seriesName="Sales" data:realIndex="0">
                      <path id="SvgjsPath1027" d="M13.293180592854817 278.731L13.293180592854817 169.8244C13.293180592854817 167.3244 15.793180592854817 164.8244 18.293180592854817 164.8244L22.772930653889972 164.8244C25.272930653889972 164.8244 27.772930653889972 167.3244 27.772930653889972 169.8244L27.772930653889972 278.731L13.293180592854817 278.731C13.293180592854817 278.731 13.293180592854817 278.731 13.293180592854817 278.731C13.293180592854817 278.731 13.293180592854817 278.731 13.293180592854817 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 13.293180592854817 278.731 L 13.293180592854817 169.8244 C 13.293180592854817 167.3244 15.793180592854817 164.8244 18.293180592854817 164.8244 L 22.772930653889972 164.8244 C 25.272930653889972 164.8244 27.772930653889972 167.3244 27.772930653889972 169.8244 L 27.772930653889972 278.731 z " pathFrom="M 13.293180592854817 278.731 L 13.293180592854817 278.731 L 27.772930653889972 278.731 L 27.772930653889972 278.731 L 27.772930653889972 278.731 L 27.772930653889972 278.731 L 27.772930653889972 278.731 L 13.293180592854817 278.731 z" cy="162.8234" cx="50.35929183959961" j="0" val="168" barHeight="117.90660000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1029" d="M54.35929183959961 278.731L54.35929183959961 17.528374999999983C54.35929183959961 15.028374999999983 56.85929183959961 12.528374999999983 59.35929183959961 12.528374999999983L63.839041900634754 12.528374999999983C66.33904190063475 12.528374999999983 68.83904190063475 15.028374999999983 68.83904190063475 17.528374999999983L68.83904190063475 278.731L54.35929183959961 278.731C54.35929183959961 278.731 54.35929183959961 278.731 54.35929183959961 278.731C54.35929183959961 278.731 54.35929183959961 278.731 54.35929183959961 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 54.35929183959961 278.731 L 54.35929183959961 17.528375000000004 C 54.35929183959961 15.028375000000004 56.85929183959961 12.528375000000006 59.35929183959961 12.528375000000006 L 63.839041900634754 12.528375000000006 C 66.33904190063475 12.528375000000006 68.83904190063475 15.028375000000004 68.83904190063475 17.528375000000004 L 68.83904190063475 278.731 z " pathFrom="M 54.35929183959961 278.731 L 54.35929183959961 278.731 L 68.83904190063475 278.731 L 68.83904190063475 278.731 L 68.83904190063475 278.731 L 68.83904190063475 278.731 L 68.83904190063475 278.731 L 54.35929183959961 278.731 z" cy="10.527375000000006" cx="91.4254030863444" j="1" val="385" barHeight="270.202625" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1031" d="M95.4254030863444 278.731L95.4254030863444 146.664175C95.4254030863444 144.164175 97.9254030863444 141.664175 100.4254030863444 141.664175L104.90515314737956 141.664175C107.40515314737956 141.664175 109.90515314737956 144.164175 109.90515314737956 146.664175L109.90515314737956 278.731L95.4254030863444 278.731C95.4254030863444 278.731 95.4254030863444 278.731 95.4254030863444 278.731C95.4254030863444 278.731 95.4254030863444 278.731 95.4254030863444 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 95.4254030863444 278.731 L 95.4254030863444 146.664175 C 95.4254030863444 144.164175 97.9254030863444 141.664175 100.4254030863444 141.664175 L 104.90515314737956 141.664175 C 107.40515314737956 141.664175 109.90515314737956 144.164175 109.90515314737956 146.664175 L 109.90515314737956 278.731 z " pathFrom="M 95.4254030863444 278.731 L 95.4254030863444 278.731 L 109.90515314737956 278.731 L 109.90515314737956 278.731 L 109.90515314737956 278.731 L 109.90515314737956 278.731 L 109.90515314737956 278.731 L 95.4254030863444 278.731 z" cy="139.663175" cx="132.49151433308919" j="2" val="201" barHeight="141.06682500000002" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1033" d="M136.49151433308919 278.731L136.49151433308919 78.58715000000001C136.49151433308919 76.08715000000001 138.99151433308919 73.58715000000001 141.49151433308919 73.58715000000001L145.97126439412435 73.58715000000001C148.47126439412435 73.58715000000001 150.97126439412435 76.08715000000001 150.97126439412435 78.58715000000001L150.97126439412435 278.731L136.49151433308919 278.731C136.49151433308919 278.731 136.49151433308919 278.731 136.49151433308919 278.731C136.49151433308919 278.731 136.49151433308919 278.731 136.49151433308919 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 136.49151433308919 278.731 L 136.49151433308919 78.58715000000001 C 136.49151433308919 76.08715000000001 138.99151433308919 73.58715000000001 141.49151433308919 73.58715000000001 L 145.97126439412435 73.58715000000001 C 148.47126439412435 73.58715000000001 150.97126439412435 76.08715000000001 150.97126439412435 78.58715000000001 L 150.97126439412435 278.731 z " pathFrom="M 136.49151433308919 278.731 L 136.49151433308919 278.731 L 150.97126439412435 278.731 L 150.97126439412435 278.731 L 150.97126439412435 278.731 L 150.97126439412435 278.731 L 150.97126439412435 278.731 L 136.49151433308919 278.731 z" cy="71.58615" cx="173.55762557983397" j="3" val="298" barHeight="209.14385000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1035" d="M177.55762557983397 278.731L177.55762557983397 156.48972500000002C177.55762557983397 153.98972500000002 180.05762557983397 151.48972500000002 182.55762557983397 151.48972500000002L187.03737564086913 151.48972500000002C189.53737564086913 151.48972500000002 192.03737564086913 153.98972500000002 192.03737564086913 156.48972500000002L192.03737564086913 278.731L177.55762557983397 278.731C177.55762557983397 278.731 177.55762557983397 278.731 177.55762557983397 278.731C177.55762557983397 278.731 177.55762557983397 278.731 177.55762557983397 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 177.55762557983397 278.731 L 177.55762557983397 156.48972500000002 C 177.55762557983397 153.98972500000002 180.05762557983397 151.48972500000002 182.55762557983397 151.48972500000002 L 187.03737564086913 151.48972500000002 C 189.53737564086913 151.48972500000002 192.03737564086913 153.98972500000002 192.03737564086913 156.48972500000002 L 192.03737564086913 278.731 z " pathFrom="M 177.55762557983397 278.731 L 177.55762557983397 278.731 L 192.03737564086913 278.731 L 192.03737564086913 278.731 L 192.03737564086913 278.731 L 192.03737564086913 278.731 L 192.03737564086913 278.731 L 177.55762557983397 278.731 z" cy="149.48872500000002" cx="214.62373682657875" j="4" val="187" barHeight="131.241275" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1037" d="M218.62373682657875 278.731L218.62373682657875 150.875125C218.62373682657875 148.375125 221.12373682657875 145.875125 223.62373682657875 145.875125L228.1034868876139 145.875125C230.6034868876139 145.875125 233.1034868876139 148.375125 233.1034868876139 150.875125L233.1034868876139 278.731L218.62373682657875 278.731C218.62373682657875 278.731 218.62373682657875 278.731 218.62373682657875 278.731C218.62373682657875 278.731 218.62373682657875 278.731 218.62373682657875 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 218.62373682657875 278.731 L 218.62373682657875 150.875125 C 218.62373682657875 148.375125 221.12373682657875 145.875125 223.62373682657875 145.875125 L 228.1034868876139 145.875125 C 230.6034868876139 145.875125 233.1034868876139 148.375125 233.1034868876139 150.875125 L 233.1034868876139 278.731 z " pathFrom="M 218.62373682657875 278.731 L 218.62373682657875 278.731 L 233.1034868876139 278.731 L 233.1034868876139 278.731 L 233.1034868876139 278.731 L 233.1034868876139 278.731 L 233.1034868876139 278.731 L 218.62373682657875 278.731 z" cy="143.874125" cx="255.68984807332356" j="5" val="195" barHeight="136.85587500000003" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1039" d="M259.68984807332356 278.731L259.68984807332356 83.49992499999999C259.68984807332356 80.99992499999999 262.18984807332356 78.49992499999999 264.68984807332356 78.49992499999999L269.1695981343587 78.49992499999999C271.6695981343587 78.49992499999999 274.1695981343587 80.99992499999999 274.1695981343587 83.49992499999999L274.1695981343587 278.731L259.68984807332356 278.731C259.68984807332356 278.731 259.68984807332356 278.731 259.68984807332356 278.731C259.68984807332356 278.731 259.68984807332356 278.731 259.68984807332356 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 259.68984807332356 278.731 L 259.68984807332356 83.49992499999999 C 259.68984807332356 80.99992499999999 262.18984807332356 78.49992499999999 264.68984807332356 78.49992499999999 L 269.1695981343587 78.49992499999999 C 271.6695981343587 78.49992499999999 274.1695981343587 80.99992499999999 274.1695981343587 83.49992499999999 L 274.1695981343587 278.731 z " pathFrom="M 259.68984807332356 278.731 L 259.68984807332356 278.731 L 274.1695981343587 278.731 L 274.1695981343587 278.731 L 274.1695981343587 278.731 L 274.1695981343587 278.731 L 274.1695981343587 278.731 L 259.68984807332356 278.731 z" cy="76.49892499999999" cx="296.75595932006837" j="6" val="291" barHeight="204.23107500000003" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1041" d="M300.75595932006837 278.731L300.75595932006837 210.53025C300.75595932006837 208.03025 303.25595932006837 205.53025 305.75595932006837 205.53025L310.2357093811035 205.53025C312.7357093811035 205.53025 315.2357093811035 208.03025 315.2357093811035 210.53025L315.2357093811035 278.731L300.75595932006837 278.731C300.75595932006837 278.731 300.75595932006837 278.731 300.75595932006837 278.731C300.75595932006837 278.731 300.75595932006837 278.731 300.75595932006837 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 300.75595932006837 278.731 L 300.75595932006837 210.53025 C 300.75595932006837 208.03025 303.25595932006837 205.53025 305.75595932006837 205.53025 L 310.2357093811035 205.53025 C 312.7357093811035 205.53025 315.2357093811035 208.03025 315.2357093811035 210.53025 L 315.2357093811035 278.731 z " pathFrom="M 300.75595932006837 278.731 L 300.75595932006837 278.731 L 315.2357093811035 278.731 L 315.2357093811035 278.731 L 315.2357093811035 278.731 L 315.2357093811035 278.731 L 315.2357093811035 278.731 L 300.75595932006837 278.731 z" cy="203.52925" cx="337.8220705668132" j="7" val="110" barHeight="77.20075000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1043" d="M341.8220705668132 278.731L341.8220705668132 136.838625C341.8220705668132 134.338625 344.3220705668132 131.838625 346.8220705668132 131.838625L351.3018206278483 131.838625C353.8018206278483 131.838625 356.3018206278483 134.338625 356.3018206278483 136.838625L356.3018206278483 278.731L341.8220705668132 278.731C341.8220705668132 278.731 341.8220705668132 278.731 341.8220705668132 278.731C341.8220705668132 278.731 341.8220705668132 278.731 341.8220705668132 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 341.8220705668132 278.731 L 341.8220705668132 136.838625 C 341.8220705668132 134.338625 344.3220705668132 131.838625 346.8220705668132 131.838625 L 351.3018206278483 131.838625 C 353.8018206278483 131.838625 356.3018206278483 134.338625 356.3018206278483 136.838625 L 356.3018206278483 278.731 z " pathFrom="M 341.8220705668132 278.731 L 341.8220705668132 278.731 L 356.3018206278483 278.731 L 356.3018206278483 278.731 L 356.3018206278483 278.731 L 356.3018206278483 278.731 L 356.3018206278483 278.731 L 341.8220705668132 278.731 z" cy="129.837625" cx="378.888181813558" j="8" val="215" barHeight="150.89237500000002" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1045" d="M382.888181813558 278.731L382.888181813558 14.019249999999943C382.888181813558 11.519249999999943 385.388181813558 9.019249999999943 387.888181813558 9.019249999999943L392.3679318745931 9.019249999999943C394.8679318745931 9.019249999999943 397.3679318745931 11.519249999999943 397.3679318745931 14.019249999999943L397.3679318745931 278.731L382.888181813558 278.731C382.888181813558 278.731 382.888181813558 278.731 382.888181813558 278.731C382.888181813558 278.731 382.888181813558 278.731 382.888181813558 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 382.888181813558 278.731 L 382.888181813558 14.019249999999966 C 382.888181813558 11.519249999999966 385.388181813558 9.019249999999966 387.888181813558 9.019249999999966 L 392.3679318745931 9.019249999999966 C 394.8679318745931 9.019249999999966 397.3679318745931 11.519249999999966 397.3679318745931 14.019249999999966 L 397.3679318745931 278.731 z " pathFrom="M 382.888181813558 278.731 L 382.888181813558 278.731 L 397.3679318745931 278.731 L 397.3679318745931 278.731 L 397.3679318745931 278.731 L 397.3679318745931 278.731 L 397.3679318745931 278.731 L 382.888181813558 278.731 z" cy="7.018249999999966" cx="419.9542930603028" j="9" val="390" barHeight="273.71175000000005" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1047" d="M423.9542930603028 278.731L423.9542930603028 91.22C423.9542930603028 88.72 426.4542930603028 86.22 428.9542930603028 86.22L433.43404312133794 86.22C435.93404312133794 86.22 438.43404312133794 88.72 438.43404312133794 91.22L438.43404312133794 278.731L423.9542930603028 278.731C423.9542930603028 278.731 423.9542930603028 278.731 423.9542930603028 278.731C423.9542930603028 278.731 423.9542930603028 278.731 423.9542930603028 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 423.9542930603028 278.731 L 423.9542930603028 91.22 C 423.9542930603028 88.72 426.4542930603028 86.22 428.9542930603028 86.22 L 433.43404312133794 86.22 C 435.93404312133794 86.22 438.43404312133794 88.72 438.43404312133794 91.22 L 438.43404312133794 278.731 z " pathFrom="M 423.9542930603028 278.731 L 423.9542930603028 278.731 L 438.43404312133794 278.731 L 438.43404312133794 278.731 L 438.43404312133794 278.731 L 438.43404312133794 278.731 L 438.43404312133794 278.731 L 423.9542930603028 278.731 z" cy="84.219" cx="461.0204043070476" j="10" val="280" barHeight="196.51100000000002" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1049" d="M465.0204043070476 278.731L465.0204043070476 209.12660000000002C465.0204043070476 206.62660000000002 467.5204043070476 204.12660000000002 470.0204043070476 204.12660000000002L474.50015436808275 204.12660000000002C477.00015436808275 204.12660000000002 479.50015436808275 206.62660000000002 479.50015436808275 209.12660000000002L479.50015436808275 278.731L465.0204043070476 278.731C465.0204043070476 278.731 465.0204043070476 278.731 465.0204043070476 278.731C465.0204043070476 278.731 465.0204043070476 278.731 465.0204043070476 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 465.0204043070476 278.731 L 465.0204043070476 209.12660000000002 C 465.0204043070476 206.62660000000002 467.5204043070476 204.12660000000002 470.0204043070476 204.12660000000002 L 474.50015436808275 204.12660000000002 C 477.00015436808275 204.12660000000002 479.50015436808275 206.62660000000002 479.50015436808275 209.12660000000002 L 479.50015436808275 278.731 z " pathFrom="M 465.0204043070476 278.731 L 465.0204043070476 278.731 L 479.50015436808275 278.731 L 479.50015436808275 278.731 L 479.50015436808275 278.731 L 479.50015436808275 278.731 L 479.50015436808275 278.731 L 465.0204043070476 278.731 z" cy="202.12560000000002" cx="502.0865155537924" j="11" val="112" barHeight="78.60440000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1051" d="M506.0865155537924 278.731L506.0865155537924 201.40652500000002C506.0865155537924 198.90652500000002 508.5865155537924 196.40652500000002 511.0865155537924 196.40652500000002L515.5662656148276 196.40652500000002C518.0662656148276 196.40652500000002 520.5662656148276 198.90652500000002 520.5662656148276 201.40652500000002L520.5662656148276 278.731L506.0865155537924 278.731C506.0865155537924 278.731 506.0865155537924 278.731 506.0865155537924 278.731C506.0865155537924 278.731 506.0865155537924 278.731 506.0865155537924 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 506.0865155537924 278.731 L 506.0865155537924 201.40652500000002 C 506.0865155537924 198.90652500000002 508.5865155537924 196.40652500000002 511.0865155537924 196.40652500000002 L 515.5662656148276 196.40652500000002 C 518.0662656148276 196.40652500000002 520.5662656148276 198.90652500000002 520.5662656148276 201.40652500000002 L 520.5662656148276 278.731 z " pathFrom="M 506.0865155537924 278.731 L 506.0865155537924 278.731 L 520.5662656148276 278.731 L 520.5662656148276 278.731 L 520.5662656148276 278.731 L 520.5662656148276 278.731 L 520.5662656148276 278.731 L 506.0865155537924 278.731 z" cy="194.405525" cx="543.1526268005372" j="12" val="123" barHeight="86.324475" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1053" d="M547.1526268005372 278.731L547.1526268005372 138.94410000000002C547.1526268005372 136.44410000000002 549.6526268005372 133.94410000000002 552.1526268005372 133.94410000000002L556.6323768615723 133.94410000000002C559.1323768615723 133.94410000000002 561.6323768615723 136.44410000000002 561.6323768615723 138.94410000000002L561.6323768615723 278.731L547.1526268005372 278.731C547.1526268005372 278.731 547.1526268005372 278.731 547.1526268005372 278.731C547.1526268005372 278.731 547.1526268005372 278.731 547.1526268005372 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 547.1526268005372 278.731 L 547.1526268005372 138.94410000000002 C 547.1526268005372 136.44410000000002 549.6526268005372 133.94410000000002 552.1526268005372 133.94410000000002 L 556.6323768615723 133.94410000000002 C 559.1323768615723 133.94410000000002 561.6323768615723 136.44410000000002 561.6323768615723 138.94410000000002 L 561.6323768615723 278.731 z " pathFrom="M 547.1526268005372 278.731 L 547.1526268005372 278.731 L 561.6323768615723 278.731 L 561.6323768615723 278.731 L 561.6323768615723 278.731 L 561.6323768615723 278.731 L 561.6323768615723 278.731 L 547.1526268005372 278.731 z" cy="131.94310000000002" cx="584.2187380472819" j="13" val="212" barHeight="148.7869" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1055" d="M588.2187380472819 278.731L588.2187380472819 98.23825C588.2187380472819 95.73825 590.7187380472819 93.23825 593.2187380472819 93.23825L597.6984881083171 93.23825C600.1984881083171 93.23825 602.6984881083171 95.73825 602.6984881083171 98.23825L602.6984881083171 278.731L588.2187380472819 278.731C588.2187380472819 278.731 588.2187380472819 278.731 588.2187380472819 278.731C588.2187380472819 278.731 588.2187380472819 278.731 588.2187380472819 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 588.2187380472819 278.731 L 588.2187380472819 98.23825 C 588.2187380472819 95.73825 590.7187380472819 93.23825 593.2187380472819 93.23825 L 597.6984881083171 93.23825 C 600.1984881083171 93.23825 602.6984881083171 95.73825 602.6984881083171 98.23825 L 602.6984881083171 278.731 z " pathFrom="M 588.2187380472819 278.731 L 588.2187380472819 278.731 L 602.6984881083171 278.731 L 602.6984881083171 278.731 L 602.6984881083171 278.731 L 602.6984881083171 278.731 L 602.6984881083171 278.731 L 588.2187380472819 278.731 z" cy="91.23724999999999" cx="625.2848492940267" j="14" val="270" barHeight="189.49275000000003" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1057" d="M629.2848492940267 278.731L629.2848492940267 154.38425C629.2848492940267 151.88425 631.7848492940267 149.38425 634.2848492940267 149.38425L638.7645993550618 149.38425C641.2645993550618 149.38425 643.7645993550618 151.88425 643.7645993550618 154.38425L643.7645993550618 278.731L629.2848492940267 278.731C629.2848492940267 278.731 629.2848492940267 278.731 629.2848492940267 278.731C629.2848492940267 278.731 629.2848492940267 278.731 629.2848492940267 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 629.2848492940267 278.731 L 629.2848492940267 154.38425 C 629.2848492940267 151.88425 631.7848492940267 149.38425 634.2848492940267 149.38425 L 638.7645993550618 149.38425 C 641.2645993550618 149.38425 643.7645993550618 151.88425 643.7645993550618 154.38425 L 643.7645993550618 278.731 z " pathFrom="M 629.2848492940267 278.731 L 629.2848492940267 278.731 L 643.7645993550618 278.731 L 643.7645993550618 278.731 L 643.7645993550618 278.731 L 643.7645993550618 278.731 L 643.7645993550618 278.731 L 629.2848492940267 278.731 z" cy="147.38325" cx="666.3509605407714" j="15" val="190" barHeight="133.34675000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1059" d="M670.3509605407714 278.731L670.3509605407714 70.16525000000001C670.3509605407714 67.66525000000001 672.8509605407714 65.16525000000001 675.3509605407714 65.16525000000001L679.8307106018066 65.16525000000001C682.3307106018066 65.16525000000001 684.8307106018066 67.66525000000001 684.8307106018066 70.16525000000001L684.8307106018066 278.731L670.3509605407714 278.731C670.3509605407714 278.731 670.3509605407714 278.731 670.3509605407714 278.731C670.3509605407714 278.731 670.3509605407714 278.731 670.3509605407714 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 670.3509605407714 278.731 L 670.3509605407714 70.16525000000001 C 670.3509605407714 67.66525000000001 672.8509605407714 65.16525000000001 675.3509605407714 65.16525000000001 L 679.8307106018066 65.16525000000001 C 682.3307106018066 65.16525000000001 684.8307106018066 67.66525000000001 684.8307106018066 70.16525000000001 L 684.8307106018066 278.731 z " pathFrom="M 670.3509605407714 278.731 L 670.3509605407714 278.731 L 684.8307106018066 278.731 L 684.8307106018066 278.731 L 684.8307106018066 278.731 L 684.8307106018066 278.731 L 684.8307106018066 278.731 L 670.3509605407714 278.731 z" cy="63.16425000000001" cx="707.4170717875162" j="16" val="310" barHeight="217.56575" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1061" d="M711.4170717875162 278.731L711.4170717875162 207.021125C711.4170717875162 204.521125 713.9170717875162 202.021125 716.4170717875162 202.021125L720.8968218485513 202.021125C723.3968218485513 202.021125 725.8968218485513 204.521125 725.8968218485513 207.021125L725.8968218485513 278.731L711.4170717875162 278.731C711.4170717875162 278.731 711.4170717875162 278.731 711.4170717875162 278.731C711.4170717875162 278.731 711.4170717875162 278.731 711.4170717875162 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 711.4170717875162 278.731 L 711.4170717875162 207.021125 C 711.4170717875162 204.521125 713.9170717875162 202.021125 716.4170717875162 202.021125 L 720.8968218485513 202.021125 C 723.3968218485513 202.021125 725.8968218485513 204.521125 725.8968218485513 207.021125 L 725.8968218485513 278.731 z " pathFrom="M 711.4170717875162 278.731 L 711.4170717875162 278.731 L 725.8968218485513 278.731 L 725.8968218485513 278.731 L 725.8968218485513 278.731 L 725.8968218485513 278.731 L 725.8968218485513 278.731 L 711.4170717875162 278.731 z" cy="200.020125" cx="748.483183034261" j="17" val="115" barHeight="80.70987500000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1063" d="M752.483183034261 278.731L752.483183034261 224.56675C752.483183034261 222.06675 754.983183034261 219.56675 757.483183034261 219.56675L761.9629330952961 219.56675C764.4629330952961 219.56675 766.9629330952961 222.06675 766.9629330952961 224.56675L766.9629330952961 278.731L752.483183034261 278.731C752.483183034261 278.731 752.483183034261 278.731 752.483183034261 278.731C752.483183034261 278.731 752.483183034261 278.731 752.483183034261 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 752.483183034261 278.731 L 752.483183034261 224.56675 C 752.483183034261 222.06675 754.983183034261 219.56675 757.483183034261 219.56675 L 761.9629330952961 219.56675 C 764.4629330952961 219.56675 766.9629330952961 222.06675 766.9629330952961 224.56675 L 766.9629330952961 278.731 z " pathFrom="M 752.483183034261 278.731 L 752.483183034261 278.731 L 766.9629330952961 278.731 L 766.9629330952961 278.731 L 766.9629330952961 278.731 L 766.9629330952961 278.731 L 766.9629330952961 278.731 L 752.483183034261 278.731 z" cy="217.56575" cx="789.5492942810057" j="18" val="90" barHeight="63.16425" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1065" d="M793.5492942810057 278.731L793.5492942810057 21.037499999999966C793.5492942810057 18.537499999999966 796.0492942810057 16.037499999999966 798.5492942810057 16.037499999999966L803.0290443420408 16.037499999999966C805.5290443420408 16.037499999999966 808.0290443420408 18.537499999999966 808.0290443420408 21.037499999999966L808.0290443420408 278.731L793.5492942810057 278.731C793.5492942810057 278.731 793.5492942810057 278.731 793.5492942810057 278.731C793.5492942810057 278.731 793.5492942810057 278.731 793.5492942810057 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 793.5492942810057 278.731 L 793.5492942810057 21.03749999999999 C 793.5492942810057 18.53749999999999 796.0492942810057 16.03749999999999 798.5492942810057 16.03749999999999 L 803.0290443420408 16.03749999999999 C 805.5290443420408 16.03749999999999 808.0290443420408 18.53749999999999 808.0290443420408 21.03749999999999 L 808.0290443420408 278.731 z " pathFrom="M 793.5492942810057 278.731 L 793.5492942810057 278.731 L 808.0290443420408 278.731 L 808.0290443420408 278.731 L 808.0290443420408 278.731 L 808.0290443420408 278.731 L 808.0290443420408 278.731 L 793.5492942810057 278.731 z" cy="14.03649999999999" cx="830.6154055277505" j="19" val="380" barHeight="266.69350000000003" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1067" d="M834.6154055277505 278.731L834.6154055277505 209.12660000000002C834.6154055277505 206.62660000000002 837.1154055277505 204.12660000000002 839.6154055277505 204.12660000000002L844.0951555887856 204.12660000000002C846.5951555887856 204.12660000000002 849.0951555887856 206.62660000000002 849.0951555887856 209.12660000000002L849.0951555887856 278.731L834.6154055277505 278.731C834.6154055277505 278.731 834.6154055277505 278.731 834.6154055277505 278.731C834.6154055277505 278.731 834.6154055277505 278.731 834.6154055277505 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 834.6154055277505 278.731 L 834.6154055277505 209.12660000000002 C 834.6154055277505 206.62660000000002 837.1154055277505 204.12660000000002 839.6154055277505 204.12660000000002 L 844.0951555887856 204.12660000000002 C 846.5951555887856 204.12660000000002 849.0951555887856 206.62660000000002 849.0951555887856 209.12660000000002 L 849.0951555887856 278.731 z " pathFrom="M 834.6154055277505 278.731 L 834.6154055277505 278.731 L 849.0951555887856 278.731 L 849.0951555887856 278.731 L 849.0951555887856 278.731 L 849.0951555887856 278.731 L 849.0951555887856 278.731 L 834.6154055277505 278.731 z" cy="202.12560000000002" cx="871.6815167744952" j="20" val="112" barHeight="78.60440000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1069" d="M875.6815167744952 278.731L875.6815167744952 131.224025C875.6815167744952 128.724025 878.1815167744952 126.22402500000001 880.6815167744952 126.22402500000001L885.1612668355303 126.22402500000001C887.6612668355303 126.22402500000001 890.1612668355303 128.724025 890.1612668355303 131.224025L890.1612668355303 278.731L875.6815167744952 278.731C875.6815167744952 278.731 875.6815167744952 278.731 875.6815167744952 278.731C875.6815167744952 278.731 875.6815167744952 278.731 875.6815167744952 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 875.6815167744952 278.731 L 875.6815167744952 131.224025 C 875.6815167744952 128.724025 878.1815167744952 126.22402500000001 880.6815167744952 126.22402500000001 L 885.1612668355303 126.22402500000001 C 887.6612668355303 126.22402500000001 890.1612668355303 128.724025 890.1612668355303 131.224025 L 890.1612668355303 278.731 z " pathFrom="M 875.6815167744952 278.731 L 875.6815167744952 278.731 L 890.1612668355303 278.731 L 890.1612668355303 278.731 L 890.1612668355303 278.731 L 890.1612668355303 278.731 L 890.1612668355303 278.731 L 875.6815167744952 278.731 z" cy="124.223025" cx="912.74762802124" j="21" val="223" barHeight="156.506975" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1071" d="M916.74762802124 278.731L916.74762802124 82.7981C916.74762802124 80.2981 919.24762802124 77.7981 921.74762802124 77.7981L926.2273780822751 77.7981C928.7273780822751 77.7981 931.2273780822751 80.2981 931.2273780822751 82.7981L931.2273780822751 278.731L916.74762802124 278.731C916.74762802124 278.731 916.74762802124 278.731 916.74762802124 278.731C916.74762802124 278.731 916.74762802124 278.731 916.74762802124 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 916.74762802124 278.731 L 916.74762802124 82.7981 C 916.74762802124 80.2981 919.24762802124 77.7981 921.74762802124 77.7981 L 926.2273780822751 77.7981 C 928.7273780822751 77.7981 931.2273780822751 80.2981 931.2273780822751 82.7981 L 931.2273780822751 278.731 z " pathFrom="M 916.74762802124 278.731 L 916.74762802124 278.731 L 931.2273780822751 278.731 L 931.2273780822751 278.731 L 931.2273780822751 278.731 L 931.2273780822751 278.731 L 931.2273780822751 278.731 L 916.74762802124 278.731 z" cy="75.7971" cx="953.8137392679847" j="22" val="292" barHeight="204.93290000000002" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1073" d="M957.8137392679847 278.731L957.8137392679847 168.42075000000003C957.8137392679847 165.92075000000003 960.3137392679847 163.42075000000003 962.8137392679847 163.42075000000003L967.2934893290198 163.42075000000003C969.7934893290198 163.42075000000003 972.2934893290198 165.92075000000003 972.2934893290198 168.42075000000003L972.2934893290198 278.731L957.8137392679847 278.731C957.8137392679847 278.731 957.8137392679847 278.731 957.8137392679847 278.731C957.8137392679847 278.731 957.8137392679847 278.731 957.8137392679847 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 957.8137392679847 278.731 L 957.8137392679847 168.42075000000003 C 957.8137392679847 165.92075000000003 960.3137392679847 163.42075000000003 962.8137392679847 163.42075000000003 L 967.2934893290198 163.42075000000003 C 969.7934893290198 163.42075000000003 972.2934893290198 165.92075000000003 972.2934893290198 168.42075000000003 L 972.2934893290198 278.731 z " pathFrom="M 957.8137392679847 278.731 L 957.8137392679847 278.731 L 972.2934893290198 278.731 L 972.2934893290198 278.731 L 972.2934893290198 278.731 L 972.2934893290198 278.731 L 972.2934893290198 278.731 L 957.8137392679847 278.731 z" cy="161.41975000000002" cx="994.8798505147295" j="23" val="170" barHeight="119.31025000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1075" d="M998.8798505147295 278.731L998.8798505147295 84.20175C998.8798505147295 81.70175 1001.3798505147295 79.20175 1003.8798505147295 79.20175L1008.3596005757646 79.20175C1010.8596005757646 79.20175 1013.3596005757646 81.70175 1013.3596005757646 84.20175L1013.3596005757646 278.731L998.8798505147295 278.731C998.8798505147295 278.731 998.8798505147295 278.731 998.8798505147295 278.731C998.8798505147295 278.731 998.8798505147295 278.731 998.8798505147295 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 998.8798505147295 278.731 L 998.8798505147295 84.20175 C 998.8798505147295 81.70175 1001.3798505147295 79.20175 1003.8798505147295 79.20175 L 1008.3596005757646 79.20175 C 1010.8596005757646 79.20175 1013.3596005757646 81.70175 1013.3596005757646 84.20175 L 1013.3596005757646 278.731 z " pathFrom="M 998.8798505147295 278.731 L 998.8798505147295 278.731 L 1013.3596005757646 278.731 L 1013.3596005757646 278.731 L 1013.3596005757646 278.731 L 1013.3596005757646 278.731 L 1013.3596005757646 278.731 L 998.8798505147295 278.731 z" cy="77.20075" cx="1035.9459617614743" j="24" val="290" barHeight="203.52925000000002" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1077" d="M1039.9459617614743 278.731L1039.9459617614743 210.53025C1039.9459617614743 208.03025 1042.4459617614743 205.53025 1044.9459617614743 205.53025L1049.4257118225096 205.53025C1051.9257118225096 205.53025 1054.4257118225096 208.03025 1054.4257118225096 210.53025L1054.4257118225096 278.731L1039.9459617614743 278.731C1039.9459617614743 278.731 1039.9459617614743 278.731 1039.9459617614743 278.731C1039.9459617614743 278.731 1039.9459617614743 278.731 1039.9459617614743 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 1039.9459617614743 278.731 L 1039.9459617614743 210.53025 C 1039.9459617614743 208.03025 1042.4459617614743 205.53025 1044.9459617614743 205.53025 L 1049.4257118225096 205.53025 C 1051.9257118225096 205.53025 1054.4257118225096 208.03025 1054.4257118225096 210.53025 L 1054.4257118225096 278.731 z " pathFrom="M 1039.9459617614743 278.731 L 1039.9459617614743 278.731 L 1054.4257118225096 278.731 L 1054.4257118225096 278.731 L 1054.4257118225096 278.731 L 1054.4257118225096 278.731 L 1054.4257118225096 278.731 L 1039.9459617614743 278.731 z" cy="203.52925" cx="1077.0120730082192" j="25" val="110" barHeight="77.20075000000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1079" d="M1081.0120730082192 278.731L1081.0120730082192 207.021125C1081.0120730082192 204.521125 1083.5120730082192 202.021125 1086.0120730082192 202.021125L1090.4918230692545 202.021125C1092.9918230692545 202.021125 1095.4918230692545 204.521125 1095.4918230692545 207.021125L1095.4918230692545 278.731L1081.0120730082192 278.731C1081.0120730082192 278.731 1081.0120730082192 278.731 1081.0120730082192 278.731C1081.0120730082192 278.731 1081.0120730082192 278.731 1081.0120730082192 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 1081.0120730082192 278.731 L 1081.0120730082192 207.021125 C 1081.0120730082192 204.521125 1083.5120730082192 202.021125 1086.0120730082192 202.021125 L 1090.4918230692545 202.021125 C 1092.9918230692545 202.021125 1095.4918230692545 204.521125 1095.4918230692545 207.021125 L 1095.4918230692545 278.731 z " pathFrom="M 1081.0120730082192 278.731 L 1081.0120730082192 278.731 L 1095.4918230692545 278.731 L 1095.4918230692545 278.731 L 1095.4918230692545 278.731 L 1095.4918230692545 278.731 L 1095.4918230692545 278.731 L 1081.0120730082192 278.731 z" cy="200.020125" cx="1118.078184254964" j="26" val="115" barHeight="80.70987500000001" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1081" d="M1122.078184254964 278.731L1122.078184254964 84.20175C1122.078184254964 81.70175 1124.578184254964 79.20175 1127.078184254964 79.20175L1131.5579343159993 79.20175C1134.0579343159993 79.20175 1136.5579343159993 81.70175 1136.5579343159993 84.20175L1136.5579343159993 278.731L1122.078184254964 278.731C1122.078184254964 278.731 1122.078184254964 278.731 1122.078184254964 278.731C1122.078184254964 278.731 1122.078184254964 278.731 1122.078184254964 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 1122.078184254964 278.731 L 1122.078184254964 84.20175 C 1122.078184254964 81.70175 1124.578184254964 79.20175 1127.078184254964 79.20175 L 1131.5579343159993 79.20175 C 1134.0579343159993 79.20175 1136.5579343159993 81.70175 1136.5579343159993 84.20175 L 1136.5579343159993 278.731 z " pathFrom="M 1122.078184254964 278.731 L 1122.078184254964 278.731 L 1136.5579343159993 278.731 L 1136.5579343159993 278.731 L 1136.5579343159993 278.731 L 1136.5579343159993 278.731 L 1136.5579343159993 278.731 L 1122.078184254964 278.731 z" cy="77.20075" cx="1159.144295501709" j="27" val="290" barHeight="203.52925000000002" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1083" d="M1163.144295501709 278.731L1163.144295501709 21.037499999999966C1163.144295501709 18.537499999999966 1165.644295501709 16.037499999999966 1168.144295501709 16.037499999999966L1172.6240455627442 16.037499999999966C1175.1240455627442 16.037499999999966 1177.6240455627442 18.537499999999966 1177.6240455627442 21.037499999999966L1177.6240455627442 278.731L1163.144295501709 278.731C1163.144295501709 278.731 1163.144295501709 278.731 1163.144295501709 278.731C1163.144295501709 278.731 1163.144295501709 278.731 1163.144295501709 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 1163.144295501709 278.731 L 1163.144295501709 21.03749999999999 C 1163.144295501709 18.53749999999999 1165.644295501709 16.03749999999999 1168.144295501709 16.03749999999999 L 1172.6240455627442 16.03749999999999 C 1175.1240455627442 16.03749999999999 1177.6240455627442 18.53749999999999 1177.6240455627442 21.03749999999999 L 1177.6240455627442 278.731 z " pathFrom="M 1163.144295501709 278.731 L 1163.144295501709 278.731 L 1177.6240455627442 278.731 L 1177.6240455627442 278.731 L 1177.6240455627442 278.731 L 1177.6240455627442 278.731 L 1177.6240455627442 278.731 L 1163.144295501709 278.731 z" cy="14.03649999999999" cx="1200.2104067484538" j="28" val="380" barHeight="266.69350000000003" barWidth="18.479750061035155"></path>
                      <path id="SvgjsPath1085" d="M1204.2104067484538 278.731L1204.2104067484538 68.76160000000002C1204.2104067484538 66.26160000000002 1206.7104067484538 63.761600000000016 1209.2104067484538 63.761600000000016L1213.690156809489 63.761600000000016C1216.190156809489 63.761600000000016 1218.690156809489 66.26160000000002 1218.690156809489 68.76160000000002L1218.690156809489 278.731L1204.2104067484538 278.731C1204.2104067484538 278.731 1204.2104067484538 278.731 1204.2104067484538 278.731C1204.2104067484538 278.731 1204.2104067484538 278.731 1204.2104067484538 278.731 " fill="#46e99e" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="round" stroke-width="4" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectBarMaskt65a8jbu)" pathTo="M 1204.2104067484538 278.731 L 1204.2104067484538 68.76160000000002 C 1204.2104067484538 66.26160000000002 1206.7104067484538 63.76160000000001 1209.2104067484538 63.76160000000001 L 1213.690156809489 63.76160000000001 C 1216.190156809489 63.76160000000001 1218.690156809489 66.26160000000002 1218.690156809489 68.76160000000002 L 1218.690156809489 278.731 z " pathFrom="M 1204.2104067484538 278.731 L 1204.2104067484538 278.731 L 1218.690156809489 278.731 L 1218.690156809489 278.731 L 1218.690156809489 278.731 L 1218.690156809489 278.731 L 1218.690156809489 278.731 L 1204.2104067484538 278.731 z" cy="61.76060000000001" cx="1241.2765179951987" j="29" val="312" barHeight="218.9694" barWidth="18.479750061035155"></path>
                      <g id="SvgjsG1024" class="apexcharts-bar-goals-markers">
                        <g id="SvgjsG1026" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1028" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1030" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1032" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1034" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1036" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1038" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1040" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1042" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1044" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1046" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1048" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1050" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1052" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1054" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1056" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1058" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1060" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1062" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1064" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1066" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1068" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1070" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1072" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1074" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1076" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1078" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1080" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1082" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                        <g id="SvgjsG1084" className="apexcharts-bar-goals-groups" class="apexcharts-hidden-element-shown" clip-path="url(#gridRectMarkerMaskt65a8jbu)"></g>
                      </g>
                      <g id="SvgjsG1025" class="apexcharts-bar-shadows apexcharts-hidden-element-shown"></g>
                    </g>
                    <g id="SvgjsG1023" class="apexcharts-datalabels apexcharts-hidden-element-shown" data:realIndex="0"></g>
                  </g>
                  <line id="SvgjsLine1097" x1="0" y1="0" x2="1231.9833374023438" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line>
                  <line id="SvgjsLine1098" x1="0" y1="0" x2="1231.9833374023438" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line>
                  <g id="SvgjsG1099" class="apexcharts-xaxis" transform="translate(0, 0)">
                    <g id="SvgjsG1100" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)">
                      <text id="SvgjsText1102" font-family="Outfit, sans-serif" x="20.533055623372395" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1103">1</tspan>
                        <title>1</title>
                      </text>
                      <text id="SvgjsText1105" font-family="Outfit, sans-serif" x="61.59916687011719" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1106">2</tspan>
                        <title>2</title>
                      </text>
                      <text id="SvgjsText1108" font-family="Outfit, sans-serif" x="102.66527811686198" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1109">3</tspan>
                        <title>3</title>
                      </text>
                      <text id="SvgjsText1111" font-family="Outfit, sans-serif" x="143.73138936360675" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1112">4</tspan>
                        <title>4</title>
                      </text>
                      <text id="SvgjsText1114" font-family="Outfit, sans-serif" x="184.79750061035153" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1115">5</tspan>
                        <title>5</title>
                      </text>
                      <text id="SvgjsText1117" font-family="Outfit, sans-serif" x="225.86361185709632" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1118">6</tspan>
                        <title>6</title>
                      </text>
                      <text id="SvgjsText1120" font-family="Outfit, sans-serif" x="266.9297231038411" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1121">7</tspan>
                        <title>7</title>
                      </text>
                      <text id="SvgjsText1123" font-family="Outfit, sans-serif" x="307.99583435058594" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1124">8</tspan>
                        <title>8</title>
                      </text>
                      <text id="SvgjsText1126" font-family="Outfit, sans-serif" x="349.06194559733075" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1127">9</tspan>
                        <title>9</title>
                      </text>
                      <text id="SvgjsText1129" font-family="Outfit, sans-serif" x="390.12805684407556" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1130">10</tspan>
                        <title>10</title>
                      </text>
                      <text id="SvgjsText1132" font-family="Outfit, sans-serif" x="431.19416809082037" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1133">11</tspan>
                        <title>11</title>
                      </text>
                      <text id="SvgjsText1135" font-family="Outfit, sans-serif" x="472.2602793375652" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1136">12</tspan>
                        <title>12</title>
                      </text>
                      <text id="SvgjsText1138" font-family="Outfit, sans-serif" x="513.3263905843099" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1139">13</tspan>
                        <title>13</title>
                      </text>
                      <text id="SvgjsText1141" font-family="Outfit, sans-serif" x="554.3925018310547" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1142">14</tspan>
                        <title>14</title>
                      </text>
                      <text id="SvgjsText1144" font-family="Outfit, sans-serif" x="595.4586130777994" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1145">15</tspan>
                        <title>15</title>
                      </text>
                      <text id="SvgjsText1147" font-family="Outfit, sans-serif" x="636.5247243245442" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1148">16</tspan>
                        <title>16</title>
                      </text>
                      <text id="SvgjsText1150" font-family="Outfit, sans-serif" x="677.590835571289" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1151">17</tspan>
                        <title>17</title>
                      </text>
                      <text id="SvgjsText1153" font-family="Outfit, sans-serif" x="718.6569468180337" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1154">18</tspan>
                        <title>18</title>
                      </text>
                      <text id="SvgjsText1156" font-family="Outfit, sans-serif" x="759.7230580647785" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1157">19</tspan>
                        <title>19</title>
                      </text>
                      <text id="SvgjsText1159" font-family="Outfit, sans-serif" x="800.7891693115232" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1160">20</tspan>
                        <title>20</title>
                      </text>
                      <text id="SvgjsText1162" font-family="Outfit, sans-serif" x="841.855280558268" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1163">21</tspan>
                        <title>21</title>
                      </text>
                      <text id="SvgjsText1165" font-family="Outfit, sans-serif" x="882.9213918050127" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1166">22</tspan>
                        <title>22</title>
                      </text>
                      <text id="SvgjsText1168" font-family="Outfit, sans-serif" x="923.9875030517575" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1169">23</tspan>
                        <title>23</title>
                      </text>
                      <text id="SvgjsText1171" font-family="Outfit, sans-serif" x="965.0536142985022" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1172">24</tspan>
                        <title>24</title>
                      </text>
                      <text id="SvgjsText1174" font-family="Outfit, sans-serif" x="1006.119725545247" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1175">25</tspan>
                        <title>25</title>
                      </text>
                      <text id="SvgjsText1177" font-family="Outfit, sans-serif" x="1047.185836791992" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1178">26</tspan>
                        <title>26</title>
                      </text>
                      <text id="SvgjsText1180" font-family="Outfit, sans-serif" x="1088.2519480387368" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1181">27</tspan>
                        <title>27</title>
                      </text>
                      <text id="SvgjsText1183" font-family="Outfit, sans-serif" x="1129.3180592854817" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1184">28</tspan>
                        <title>28</title>
                      </text>
                      <text id="SvgjsText1186" font-family="Outfit, sans-serif" x="1170.3841705322266" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1187">29</tspan>
                        <title>29</title>
                      </text>
                      <text id="SvgjsText1189" font-family="Outfit, sans-serif" x="1211.4502817789714" y="308.73" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-text apexcharts-xaxis-label " style="font-family: Outfit, sans-serif;">
                        <tspan id="SvgjsTspan1190">30</tspan>
                        <title>30</title>
                      </text>
                    </g>
                  </g>
                  <g id="SvgjsG1208" class="apexcharts-yaxis-annotations apexcharts-hidden-element-shown"></g>
                  <g id="SvgjsG1209" class="apexcharts-xaxis-annotations apexcharts-hidden-element-shown"></g>
                  <g id="SvgjsG1210" class="apexcharts-point-annotations apexcharts-hidden-element-shown"></g>
                </g>
              </svg>
              <div class="apexcharts-tooltip apexcharts-theme-light" style="left: 567.201px; top: 202px;">
                <div class="apexcharts-tooltip-series-group apexcharts-tooltip-series-group-0 apexcharts-active" style="order: 1; display: flex;">
                  <span class="apexcharts-tooltip-marker" style="background-color: rgb(70, 95, 255);"></span>
                  <div class="apexcharts-tooltip-text" style="font-family: Outfit, sans-serif; font-size: 12px;">
                    <div class="apexcharts-tooltip-y-group">
                      <span class="apexcharts-tooltip-text-y-label">Sales: </span>
                      <span class="apexcharts-tooltip-text-y-value">123</span>
                    </div>
                    <div class="apexcharts-tooltip-goals-group">
                      <span class="apexcharts-tooltip-text-goals-label"></span>
                      <span class="apexcharts-tooltip-text-goals-value"></span>
                    </div>
                    <div class="apexcharts-tooltip-z-group">
                      <span class="apexcharts-tooltip-text-z-label"></span>
                      <span class="apexcharts-tooltip-text-z-value"></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-light">
                <div class="apexcharts-yaxistooltip-text"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppAdminLayout>
</template>
