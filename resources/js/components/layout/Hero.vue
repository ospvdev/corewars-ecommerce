<script setup lang="ts">
</script>

<style scoped>

</style>

<template>
    <!-- Wrapper: el navbar (fixed top-0) queda por encima -->
    <section class="relative isolate w-full h-[100svh] overflow-hidden">
        <!-- Imagen -->
        <img src="/img/clone.jpg" alt="Hero" class="absolute inset-0 -z-10 w-full h-full object-cover"/>

        <!-- Overlay 1: degradado vertical suave (oscurece la parte superior) -->
        <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-bg-1/100 via-bg-2/70 to-transparent"></div>

        <!-- Overlay 2: viñeta en bordes (oscurece los laterales y base) -->
        <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(120%_80%_at_50%_30%,transparent_55%,rgba(5,5,6,0.75)_100%)]"></div>

        <!-- Tinte sutil con el color primario, da look “gaming” -->
        <div class="pointer-events-none absolute inset-0 -z-10 mix-blend-overlay bg-[linear-gradient(180deg,rgba(70,233,158,0.10)_0%,transparent_60%)]"></div>

                <!-- Contenido del hero (título, botón, etc.) -->
                <div class="flex flex-col items-center justify-center h-[80vh] text-center space-y-6">
                        <div class="hero-content w-full">
                            <slot />
                        </div>
                </div>

        <!-- Bottom lip -->
        <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10">
            <div class="relative h-[12vw] max-h-40">
                <!-- capa oscura -->
                <div class="absolute inset-0 bg-bg-2 clip-bottom-custom"></div>

                <!-- capa verde -->
                <div class="absolute inset-0 bg-primary clip-bottom-green"></div>
            </div>
        </div>
    </section>
</template>