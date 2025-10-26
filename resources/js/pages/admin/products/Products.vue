<script setup lang="ts">
  import { defineProps, ref, onMounted } from 'vue';
  import AppAdminLayout from '@/layouts/AppAdminLayout.vue';
  import TableProducts from '@/components/admin/layout/ecommerce/TableProducts.vue';

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
    <TableProducts />
  </AppAdminLayout>
</template>