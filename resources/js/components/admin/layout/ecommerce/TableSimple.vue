<script setup lang="ts">
  import { defineProps, ref, onMounted } from 'vue';

  interface Order {
    id: number;
    nameClient: string;
    nameProduct: string;
    category: string;
    price: number;
    status: number;
    image: string;
  }

  // Objeto de prueba para los pedidos
  const dataOrder: Order[] = [
    {
      id: 1,
      nameClient: 'Juan Perez',
      nameProduct: 'Macbook pro 13”',
      category: 'Laptop',
      price: 239999,
      status: 1,
      image: 'https://picsum.photos/id/63/200/200'
    },
    {
      id: 2,
      nameClient: 'Juan Perez',
      nameProduct: 'Apple Watch Ultra',
      category: 'Watch',
      price: 879000,
      status: 0,
      image: 'https://picsum.photos/id/63/200/200'
    },
    {
      id: 3,
      nameClient: 'Juan Perez',
      nameProduct: 'iPhone 15 Pro Max',
      category: 'SmartPhone',
      price: 690000,
      status: 1,
      image: 'https://picsum.photos/id/63/200/200'
    },
    {
      id: 4,
      nameClient: 'Juan Perez',
      nameProduct: 'iPad Pro 3rd Gen',
      category: 'Electronics',
      price: 169900,
      status: 2,
      image: 'https://picsum.photos/id/63/200/200'
    },
    {
      id: 5,
      nameClient: 'Juan Perez',
      nameProduct: 'Airpods Pro 2nd Gen',
      category: 'Accessories',
      price: 24000,
      status: 1,
      image: 'https://picsum.photos/id/63/200/200'
    }
  ];

  function formatCurrency(value: number, money?: boolean): string {
    if (money) {
      return '$' + value.toLocaleString();
    }
    return value.toLocaleString();
  }

  function getStatusLabel(value: number): { bgClass: string; label: string } {
    let bgClass = '';
    let label = '';

    switch (value) {
      case 0:
        bgClass = 'bg-[#E9E946]/10 text-warning';
        label = 'Pendiente';
        break;
      case 1:
        bgClass = 'bg-[#46e99e]/10 text-primary';
        label = 'Entregado';
        break;
      case 2:
        bgClass = 'bg-[#E96A46]/10 text-error';
        label = 'Cancelado';
        break;
      default:
        bgClass = 'bg-gray-100 text-gray-800';
        label = 'Desconocido';
    }

    return { bgClass, label }; 
  }
</script>

<style scoped>
</style>

<template>
  <div class="col-span-12 mt-4">
    <div class="rounded-md border border-sidebar-border px-5 pt-5 bg-sidebar-hover sm:px-6 sm:pt-6">
      <div class="flex flex-wrap items-start justify-between gap-5">
        <div>
          <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">
            Ventas Recientes
          </h3>
          <span class="block text-theme-sm text-gray-500 dark:text-gray-400">
            Últimas 5 ventas del día realizadas
          </span>
        </div>

        <div class="flex items-center gap-3">
          <button class="inline-flex items-center gap-2 rounded-lg border border-sidebar-border bg-sidebar-hover px-4 py-2.5 font-medium hover:bg-gray-50 hover:text-gray-200 text-gray-400 hover:bg-white/[0.03]">
            <svg class="stroke-current fill-white dark:fill-gray-800" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2.29004 5.90393H17.7067" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M17.7075 14.0961H2.29085" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z" fill="" stroke="" stroke-width="1.5"></path>
              <path d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z" fill="" stroke="" stroke-width="1.5"></path>
            </svg>
            Filtrar
          </button>

          <button class="text-theme-sm shadow-theme-xs inline-flex items-center gap-2 rounded-lg border border-sidebar-border bg-sidebar-hover px-4 py-2.5 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
            Ver todo
          </button>
        </div>
      </div>
      <div class="max-w-full overflow-x-auto custom-scrollbar mt-5">
        <table class="min-w-full">
          <!-- table header start -->
          <thead class="border-gray-100 border-y dark:border-gray-800">
            <tr>
              <th class="px-6 py-3 whitespace-nowrap first:pl-0">
                <div class="flex items-center">
                  <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                    Producto
                  </p>
                </div>
              </th>
              <th class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center">
                  <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                    Categoria
                  </p>
                </div>
              </th>
              <th class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center">
                  <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                    Precio
                  </p>
                </div>
              </th>
              <th class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center">
                  <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                    Estado
                  </p>
                </div>
              </th>
            </tr>
          </thead>
          <!-- table header end -->

          <!-- table body start -->
          <tbody class="py-3 divide-y divide-gray-100 dark:divide-gray-800">
            <tr v-for="order in dataOrder" :key="order.id">
              <td class="px-6 py-3 whitespace-nowrap first:pl-0">
                <div class="flex items-center">
                  <div class="flex items-center gap-3">
                    <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                      <img :src="order.image" alt="order">
                    </div>
                    <div>
                      <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                        {{ order.nameProduct }}
                      </p>
                      <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                        Cliente: {{ order.nameClient }}
                      </span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-3 whitespace-nowrap first:pl-0">
                <div class="flex items-center">
                  <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                    {{ order.category }}
                  </p>
                </div>
              </td>
              <td class="px-6 py-3 whitespace-nowrap first:pl-0">
                <div class="flex items-center">
                  <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                    {{ formatCurrency(order.price, true) }}
                  </p>
                </div>
              </td>
              <td class="px-6 py-3 whitespace-nowrap first:pl-0">
                <div class="flex items-center">
                  <p :class="getStatusLabel(order.status).bgClass" class="rounded-full px-2 py-0.5 text-sm font-medium">
                    {{ getStatusLabel(order.status).label }}
                  </p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>