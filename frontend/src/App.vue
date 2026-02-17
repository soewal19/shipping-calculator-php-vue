<script setup>
import ShippingCalculator from './components/ShippingCalculator.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center p-4">
    <header class="mb-8 text-center">
      <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-accent mb-2">
        Shipping Calculator
      </h1>
      <p class="text-slate-400">Calculate delivery costs with precision</p>
    </header>

    <main class="w-full max-w-md">
      <ShippingCalculator />
    </main>

    <footer class="mt-8 text-slate-500 text-sm">
      &copy; 2023 LOGISTIC CORP
    </footer>
  </div>
</template>
