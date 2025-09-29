<script setup lang="ts">
import { defineProps } from 'vue';
import AppAdminLayout from '@/layouts/AppAdminLayout.vue';

interface User {
  haveAccess?: boolean;
  name?: string;
  role?: string;
  permissions?: string[];
}

const data = defineProps<{ btnActive: string, user: User }>();
</script>

<style scoped>
</style>

<template>
  <AppAdminLayout :btnActive="data.btnActive" :user="data.user">
    <div class="p-6">
      <h1 class="text-3xl font-extrabold">Vista Ecommerce</h1>
    </div>
  </AppAdminLayout>
</template>
