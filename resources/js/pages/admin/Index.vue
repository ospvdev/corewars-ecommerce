<script setup lang="ts">
    import { defineProps } from 'vue';
    import AppAdminLayout from '@/layouts/AppAdminLayout.vue';

    interface User {
        haveAccess?: boolean;
        name?: string;
        role?: string;
    }

    const data = defineProps<{ btnActive: string, user: User }>();
</script>

<template>
    <AppAdminLayout :btnActive="data.btnActive" :user="data.user">
    </AppAdminLayout>
</template>