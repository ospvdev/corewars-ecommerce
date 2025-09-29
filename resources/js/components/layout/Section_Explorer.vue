<script setup lang="ts">
    const itemsFeatured = [
        { id: 1, name: 'Item 1', url: '', brandName: 'Brand A', brandLogo: '', price: 1000, category: 'Shoes' },
        { id: 2, name: 'Item 2', url: '', brandName: 'Brand B', brandLogo: '', price: 2000, category: 'Shoes' },
        { id: 3, name: 'Item 3', url: '', brandName: 'Brand C', brandLogo: '', price: 3000, category: 'Shoes' },
        { id: 4, name: 'Item 4', url: '', brandName: 'Brand D', brandLogo: '', price: 4000, category: 'Shoes' },
    ];
</script>

<style scoped>
.hide-on-mobile { display: none; }
@media (min-width: 1024px) {
    .hide-on-mobile { display: inline-flex; }
}

</style>

<template>
    <section class="py-20 bg-[var(--color-bg-2)] text-white">
        <div class="max-w-375 mx-auto px-4">
            <!-- Encabezado -->
            <div class="flex items-start justify-between gap-6">
                <!-- Left: DESTACADO + heading stacked on lg+, centered on mobile -->
                <div class="w-full lg:w-auto flex flex-col lg:flex-row lg:items-start lg:gap-8 items-center justify-center">
                    <div class="flex flex-col items-center lg:items-start">
                        <div class="flex items-center gap-3">
                            <span class="uppercase text-xl tracking-[0.25rem] text-[var(--color-primary)] font-semibold lg:font-normal">DESTACADO</span>
                            <span class="h-3 md:h-1 w-14 md:w-10 rounded-full bg-[var(--color-primary)]"></span>
                            <span class="h-3 md:h-1 w-14 md:w-10 rounded-full bg-[var(--color-primary)]"></span>
                        </div>
                        <!-- Heading stacked below DESTACADO on lg+ -->
                        <h2 class="mt-4 lg:mt-3 text-4xl md:text-6xl font-normal hidden lg:block">LO MÁS VENDIDO</h2>
                    </div>
                </div>

                <!-- Right: explorar link visible on lg+ -->
                <a href="#" class="shrink-0 hide-on-mobile items-center gap-2 text-xl font-semibold uppercase tracking-wider hover:text-[var(--color-primary)] transition">
                    Explorar Más
                    <svg class="h-4 w-4 text-[var(--color-primary)]" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 5.5 20 12l-6.5 6.5-1.4-1.4L16.7 13H4v-2h12.7l-4.6-4.1 1.4-1.4Z"/></svg>
                </a>
            </div>

            <!-- Grid de cards (items más vendidos) -->
             <div class="mt-10 grid grid-cols-1 md:grid-cols-6 lg:grid-cols-4 gap-4">
                <div v-for="item in itemsFeatured" :key="item.id" class="relative bg-[var(--color-card)] rounded-lg h-100" style="clip-path: polygon(6% 0, 100% 1%, 100% 94%, 94% 100%, 0 100%, 0 6%);">
                    <div class="flex items-center justify-between p-4">
                        <img src="https://static.vecteezy.com/system/resources/previews/010/994/239/non_2x/adidas-logo-black-symbol-clothes-design-icon-abstract-football-illustration-with-white-background-free-vector.jpg" class="absolute top-5 left-5 w-10 h-10 rounded-full object-cover" alt="Alo">
                        <h2 class="absolute text-sm font-bold left-20 top-8 uppercase">{{ item.brandName }}</h2>
                        <button type="button" class="absolute text-lg font-extrabold top-5 right-5 text-[var(--color-card)] cursor-pointer hover:bg-[var(--color-white)] bg-[var(--color-primary)] min-w-20 min-h-10 rounded-xs">VER</button>
                    </div>
                    <div class="relative flex items-center justify-center">
                        <img src="https://i.pinimg.com/236x/8c/bd/01/8cbd01a0cd66f194fb9e454cf26cd63b.jpg" alt="ALo2" class="absolute top-12 w-[90%] h-60 object-cover rounded-lg">
                    </div>
                    <div class="absolute left-0 right-0 bottom-4 px-5 flex items-center justify-between">
                        <div class="flex flex-col">
                            <h2 class="text-xl font-bold text-white uppercase">{{ item.name }}</h2>
                            <h3 class="text-sm text-[#cdd6f4]">{{ item.category }}</h3>
                        </div>
                        <div class="flex items-center">
                            <h3 class="text-xl font-bold text-primary">${{ item.price }}</h3>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </section>
</template>