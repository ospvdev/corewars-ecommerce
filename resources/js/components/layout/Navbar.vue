<script setup lang="ts">
  import { onMounted, onBeforeUnmount, ref, watch } from 'vue';
  import { route } from 'ziggy-js';
  const { isLogged } = defineProps<{ isAdmin?: boolean, isLogged: boolean }>();

  const isAtTop = ref(true);
  const showBar = ref(true);
  const hasSolidBg = ref(false);
  const showMenu = ref(false);

  let lastY = 0;
  let ticking = false;

  const TOP_THRESHOLD = 12;
  const SOLID_THRESHOLD = 80;

  const itemClicked = ref(0);
  const activated = ref(false);
  const btns = [
    { id: 1, name: 'INICIO', active: false, url: '/' },
    { id: 2, name: 'PRODUCTOS', active: false, url: '/productos' },
    { id: 3, name: 'SOBRE NOSOTROS', active: false, url: '/about-us' },
    { id: 4, name: 'NOVEDADES', active: false, url: '/novedades' },
    { id: 5, name: 'CONTACTO', active: false, url: '/contacto' },
  ];

  function activate(item: number) {
      itemClicked.value = item;
      activated.value = true;
  }

  function onScroll() {
    const y = window.scrollY || 0;
    const goingDown = y > lastY;

    showBar.value = !(goingDown && y > 0);   // ocultar al bajar, mostrar al subir
    isAtTop.value = y <= TOP_THRESHOLD;
    hasSolidBg.value = y > SOLID_THRESHOLD && !isAtTop.value;

    lastY = y;
    ticking = false;
  }
  function onScrollTick() {
    if (!ticking) {
      requestAnimationFrame(onScroll);
      ticking = true;
    }
  }

  function showDiv(){
    showMenu.value = !showMenu.value;
  }

  function csrfToken(){
    try {
      const el = document.querySelector('meta[name="csrf-token"]');
      return el ? String(el.getAttribute('content')) : '';
    } catch {
      return '';
    }
  }

  async function logout(){
    try{
      const url = route('steam.logout');
      const token = csrfToken();
      await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-XSRF-TOKEN': token,
          'X-CSRF-TOKEN': token,
        },
        credentials: 'same-origin',
      });
      // Redirect to home after logout
      window.location.href = route('home');
    }catch{
      // fallback: navigate to home
      window.location.href = route('home');
    }
  }

  onMounted(() => {
    lastY = window.scrollY || 0;
    window.addEventListener('scroll', onScrollTick, { passive: true });
  });
  onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScrollTick);
  });

  // Lock horizontal scroll when mobile menu is open to avoid page shifting
  watch(showMenu, (val) => {
    try {
      document.documentElement.style.overflowX = val ? 'hidden' : '';
    } catch {
      // ignore in non-browser environments
    }
  });

  onBeforeUnmount(() => {
    try { document.documentElement.style.overflowX = ''; } catch {}
  });
</script>

<style scoped>
.thick-underline {
  position: relative;
  z-index: 1;
}

.thick-underline::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: -3px;      /* distancia desde el texto (ajusta) */
  height: 2px;       /* grosor de la línea (ajusta) */
  background: var(--color-primary);
  transform-origin: left center;
  transform: scaleX(0);
  transition: transform .18s ease;
}

.thick-underline:hover::after,
.thick-underline.activated::after {
  transform: scaleX(1);
}

/* Mobile panel styles inside component scope for small adjustments */
.mobile-backdrop {
  background: rgba(0,0,0,0.45);
}
</style>

<template>
  <header :class="['fixed inset-x-0 top-0 z-50 transition-transform duration-300 will-change-transform overflow-x-hidden', showBar ? 'translate-y-0' : '-translate-y-full']">
    <nav :class="[ 'w-full backdrop-blur-sm transition-[margin] duration-300', isAtTop ? 'mt-6 md:mt-8' : 'mt-0', hasSolidBg ? 'bg-bg-2/95 border-b border-white/5' : 'bg-transparent']">
  <div class="relative mx-auto max-w-[1700px] px-6 sm:px-8 md:px-12 lg:px-16 h-16 md:h-20 py-2 flex items-center justify-between pr-0 md:pr-14">
        <!-- Marca -->
        <a href="/" class="flex items-center gap-4 md:gap-5 md:mr-10">

          <!-- Logo -->
          <img src="/img/logo.png" alt="MYKD" class="h-10 md:h-14 w-auto" />

          <!-- Texto MYKD -->
          <span class="font-gamer font-black tracking-wider text-3xl md:text-5xl leading-none">
            <span class="text-white">MY</span><span class="text-primary">K</span><span class="text-white">D</span>
          </span>
        </a>
        <!-- Menú -->
        <ul class="hidden md:flex items-center gap-10 lg:gap-12 mr-10">
          <li v-for="btn in btns" :key="btn.id">
            <a :href="btn.url" class="text-lg tracking-wider font-semibold transition text-[#cdd6f4] hover:text-primary" @click.prevent="activate(btn.id)" :class="[{activated: activated && itemClicked === btn.id, 'text-primary': itemClicked === btn.id}, 'thick-underline']">{{ btn.name }}</a>
          </li>
        </ul>

        <!-- Hamburguesa (MÓVIL) -->
        <button
          aria-label="Menu"
          :aria-expanded="showMenu"
          class="md:hidden fixed right-4 sm:right-8 top-4 z-[9999]
                h-10 w-10 rounded border-2 border-primary
                flex items-center justify-center hover:bg-primary/10 transition"
          @click="showDiv"
          style="backdrop-filter: blur(6px);"
        >
          <svg :class="['h-6 w-6', showMenu ? 'text-white' : 'text-primary']" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" d="M4 7.5h16M4 12h16M4 16.5h16"/>
          </svg>
        </button>

        <!-- Acciones -->
        <div class="hidden md:flex items-center pl-6">
          <!-- Lupa -->
          <button class="p-2 text-white/80 hover:text-primary transition" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
            </svg>
          </button>

          <!-- Separador -->
          <span class="mx-6 h-6 w-[3px] rounded bg-white/20"></span>

          <template v-if="isLogged">
            <a href="#" class="hex-btn relative group inline-flex items-center gap-2 h-10 px-5 text-white hover:text-primary"><i class="fa-solid fa-user"></i></a>
            <button @click.prevent="logout" class="hex-btn relative group inline-flex items-center gap-2 h-10 px-5 text-white hover:text-primary"><i class="fa-solid fa-right-from-bracket"></i></button>
          </template>
          <a v-else :href="route('steam.auth')" class="hex-btn relative group inline-flex items-center gap-2 h-10 px-5 text-primary">
            <!-- BORDE BASE: SIEMPRE visible hasta que haya hover -->
            <svg class="absolute inset-0 h-full w-full pointer-events-none" viewBox="0 0 300 100" preserveAspectRatio="none" aria-hidden="true">
              <path
                class="hex-stroke-base transition-opacity duration-150 group-hover:opacity-0"
                d="M20 2 H280 L298 50 L280 98 H20 L2 50 Z"
                fill="none"
                stroke="currentColor"
                stroke-width="4"
                stroke-linejoin="round"
              />
              <!-- TRAZO ANIMADO: aparece SOLO en hover -->
              <path
                class="hex-stroke-anim opacity-0 group-hover:opacity-100"
                d="M20 2 H280 L298 50 L280 98 H20 L2 50 Z"
                fill="none"
                stroke="currentColor"
                stroke-width="4"
                stroke-linejoin="round"
              />
            </svg>

            <!-- Fondo sutil en hover (recortado en hexágono) -->
            <span class="absolute inset-0 bg-primary/5 opacity-0 transition-opacity duration-300 group-hover:opacity-100 [clip-path:polygon(12%_0,88%_0,100%_50%,88%_100%,12%_100%,0_50%)]"></span>

            <!-- Contenido: usa currentColor => será primary -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
              <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
            </svg>

            <span class="font-semibold tracking-wider">~ ENTRAR</span>
          </a>

          <!-- Hamburguesa (DESKTOP) -->
          <!-- <button
            aria-label="Menu"
            class="ml-3 hidden md:inline-flex h-10 w-10 items-center justify-center
                  rounded-md hover:bg-white/5 transition group cursor-pointer"
            @click="showDiv"
          >
            <svg class="h-6 w-6 text-white group-hover:text-primary"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" d="M4 7.5h16M4 12h16M4 16.5h16"/>
            </svg>
          </button> -->
        </div>
      </div>
    </nav>
  </header>
  <!-- Mobile menu + backdrop -->
  <transition name="fade">
    <div v-show="showMenu" class="fixed inset-0 z-40 flex">
      <!-- backdrop -->
      <div class="mobile-backdrop absolute inset-0" @click="showDiv"></div>

      <!-- panel -->
  <aside class="relative ml-auto z-50 w-full sm:w-[60vw] md:w-[30vw] h-full bg-bg-2 shadow-xl rounded-l-xl p-6 overflow-auto">
        <button class="absolute top-4 right-4 z-60 text-white text-[1.5rem] leading-none p-1 cursor-pointer" @click="showDiv">×</button>

        <nav class="mt-8">
          <ul class="flex flex-col gap-6">
            <li v-for="btn in btns" :key="btn.id">
              <a :href="btn.url"
                 class="text-lg tracking-wider font-semibold transition text-[#cdd6f4] hover:text-primary thick-underline"
                 @click.prevent="activate(btn.id); showDiv()"
                 :class="[{ activated: activated && itemClicked === btn.id, 'text-primary': itemClicked === btn.id }]">
                {{ btn.name }}
              </a>
            </li>
          </ul>
        </nav>
      </aside>
    </div>
  </transition>
</template>