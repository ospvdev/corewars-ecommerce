<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import Hero from '@/components/layout/Hero.vue';
    import HexButton from '@/components/layout/HexButton.vue';
    import Section_Explorer from '@/components/layout/Section_Explorer.vue';
    import { useMouse } from '@/composables/useMouse';
    import { defineProps } from 'vue';

    const { updateMousePosition, pointStyle, state } = useMouse();
    const props = defineProps<{ isAdmin: boolean, isLogged: boolean }>();
    const isAdmin = props.isAdmin;
    const isLogged = props.isLogged;
</script>

<style scoped>
</style>

<template>
    <div @mousemove="updateMousePosition">
        <div v-if="state.showPoint" :style="pointStyle" class="mouse-point"></div>
        <AppLayout :isAdmin="isAdmin" :isLogged="isLogged">
            <Hero>
                <h1 class="streaming-text text-5xl sm:text-7xl md:text-9xl font-sans font-black text-white text-center md:text-center m-8">STEAMING</h1>
                <h3 class="text-2xl sm:text-2xl md:text-3xl font-sans font-black text-white text-center md:text-center m-8">WELCOME TO JUNGLE</h3>
                <HexButton
                    label="CONTACTANOS"
                    textColor="var(--color-bg-1)"      
                    borderColor="var(--color-white)"
                    bgColor="var(--color-primary)"
                    :showIcon="false"
                />
            </Hero>

            <Section_Explorer />
        </AppLayout>
    </div>
</template>