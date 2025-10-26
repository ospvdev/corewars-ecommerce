<script setup lang="ts">
  import { ref, computed, watch } from 'vue'

  const props = defineProps<{
    indeterminate: boolean
    className?: string
    checked?: boolean
  }>()

  const emit = defineEmits<{
    (e: 'change', value: Event): void
  }>()

  const inputRef = ref<HTMLInputElement | null>(null)

  // set indeterminate prop on mount / update
  // (porque :indeterminate en template no siempre actualiza visualmente en todos los navegadores)
  watch(
    () => props.indeterminate,
    (val) => {
      if ( inputRef.value ) {
        inputRef.value.indeterminate = val
      }
    },
    { immediate: true }
  )

  const baseClasses = computed(() => {
    return [
      // reset estilo nativo
      'appearance-none',
      'cursor-pointer',
      'inline-flex items-center justify-center',

      // tamaño caja
      'w-4 h-4',

      // bordes y bg base
      'rounded border border-white/10 bg-transparent',

      // estado checked
      'checked:bg-[var(--color-primary)] checked:border-[var(--color-primary)]',

      // "check" (usando background-image SVG)
      'checked:[background-image:theme(backgroundImage.checkmark)]',
      'checked:bg-center checked:bg-no-repeat checked:bg-[length:0.75rem_0.75rem]',

      // focus ring opcional
      'focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40',

      props.className // tailwind extra que le pases
    ]
  })
</script>

<template>
  <input
    ref="inputRef"
    type="checkbox"
    :class="baseClasses"
    :checked="checked"
    @change="(e) => emit('change', e)"
    v-bind="$attrs"
  />
</template>
