<script setup lang="ts">
    import Navbar from '@/components/layout/Navbar.vue';
    import Footer from '@/components/layout/Footer.vue';
    import { defineProps } from 'vue';
    const props = defineProps<{ isAdmin: boolean, isLogged: boolean }>();
</script>

<template>
    <div class="min-h-screen bg-bg-2">
        <Navbar :isAdmin="props.isAdmin" :isLogged="props.isLogged" />
        <slot />
        <Footer />
    </div>
</template>