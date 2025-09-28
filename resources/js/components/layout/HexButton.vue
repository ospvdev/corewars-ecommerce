<script setup lang="ts">
    const props = withDefaults(defineProps<{
    label        : string
    href?        : string
    textColor?   : string   // color del texto / icono (currentColor)
    borderColor? : string   // color del trazo del hexágono
    bgColor?     : string   // color del overlay de hover
    showIcon?    : boolean
    }>(), {
    href        : '#',
    textColor   : 'var(--color-primary)',
    borderColor : 'var(--color-primary)',
    bgColor     : 'transparent',   // no relleno base; overlay solo en hover
    showIcon    : true,
    });

    const d = 'M20 2 H280 L298 50 L280 98 H20 L2 50 Z';
    </script>

<template>
    <a :href="href" class="hex-btn relative group inline-flex items-center gap-2 h-10 px-5 select-none" :style="{ color: textColor }">
        <!-- BORDE BASE: visible siempre -->
        <svg class="absolute inset-0 h-full w-full pointer-events-none" viewBox="0 0 300 100" preserveAspectRatio="none" aria-hidden="true">
            <path :d="d" :style="{ fill: bgColor }" />
            <path class="hex-stroke-base transition-opacity duration-150 group-hover:opacity-0" :d="d" fill="none" :style="{ stroke: borderColor }" stroke-width="4" stroke-linejoin="round" vector-effect="non-scaling-stroke" pathLength="1000"/>
            <!-- TRAZO ANIMADO: solo en hover -->
            <path class="hex-stroke-anim opacity-0 group-hover:opacity-100" :d="d" fill="none" :style="{ stroke: borderColor }" stroke-width="4" stroke-linejoin="round" vector-effect="non-scaling-stroke" pathLength="1000"/>
        </svg>

        <!-- Contenido -->
            <span class="relative inline-flex items-center gap-2">
            <!-- Ícono por defecto o slot personalizado -->
            <slot name="icon" v-if="showIcon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
            </svg>
            </slot>

            <span class="font-semibold tracking-wider">{{ label }}</span>
        </span>
    </a>
</template>