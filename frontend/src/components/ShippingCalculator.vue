<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const form = reactive({
  carrier: 'transcompany',
  weight: 0,
});

const result = ref(null);
const error = ref(null);
const loading = ref(false);
const useWebSocket = ref(false);
const wsConnected = ref(false);

let socket = null;

const carriers = [
  { value: 'transcompany', label: 'TransCompany' },
  { value: 'packgroup', label: 'PackGroup' },
];

const connectWebSocket = () => {
  socket = new WebSocket('ws://localhost:8081');

  socket.onopen = () => {
    wsConnected.value = true;
    console.log('WS Connected');
  };

  socket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    loading.value = false;
    if (data.error) {
      error.value = data.error;
      result.value = null;
    } else {
      result.value = data.cost;
      error.value = null;
    }
  };

  socket.onclose = () => {
    wsConnected.value = false;
    console.log('WS Disconnected');
  };

  socket.onerror = (e) => {
    console.error('WS Error', e);
    error.value = 'WebSocket connection error';
  };
};

onMounted(() => {
  connectWebSocket();
});

onUnmounted(() => {
  if (socket) socket.close();
});

const calculate = async () => {
  loading.value = true;
  error.value = null;
  result.value = null;

  try {
    if (useWebSocket.value && wsConnected.value) {
      socket.send(JSON.stringify({
        carrier: form.carrier,
        weightKg: form.weight
      }));
    } else {
      // Fallback to HTTP if WS is not checked or not connected
      if (useWebSocket.value && !wsConnected.value) {
          console.warn('WS not connected, using HTTP fallback');
      }
      
      const response = await axios.post('http://localhost:8080/api/shipping/calculate', {
        carrier: form.carrier,
        weightKg: parseFloat(form.weight)
      });
      result.value = response.data.cost;
    }
  } catch (e) {
    if (e.response && e.response.data && e.response.data.error) {
      error.value = e.response.data.error;
    } else {
      error.value = e.message;
    }
    loading.value = false;
  } finally {
    if (!useWebSocket.value) {
        loading.value = false;
    }
  }
};
</script>

<template>
  <div class="bg-slate-800 p-8 rounded-2xl shadow-xl shadow-slate-900/50 border border-slate-700">
    <div class="mb-6 flex justify-between items-center">
      <h2 class="text-xl font-semibold text-white">Calculation parameters</h2>
      <div class="flex items-center space-x-2">
        <div 
          class="w-3 h-3 rounded-full transition-colors duration-300"
          :class="wsConnected ? 'bg-green-500 shadow-lg shadow-green-500/50' : 'bg-red-500'"
          title="WebSocket Status"
        ></div>
        <span class="text-xs text-slate-400">WS</span>
      </div>
    </div>

    <form @submit.prevent="calculate" class="space-y-6">
      <div class="space-y-2">
        <label class="block text-sm font-medium text-slate-300">Carrier</label>
        <div class="grid grid-cols-2 gap-4">
          <label 
            v-for="c in carriers" 
            :key="c.value"
            class="cursor-pointer relative"
          >
            <input 
              type="radio" 
              v-model="form.carrier" 
              :value="c.value" 
              class="peer sr-only"
            >
            <div class="p-4 rounded-xl border border-slate-600 bg-slate-700/50 hover:bg-slate-700 transition-all peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:ring-1 peer-checked:ring-primary/50 text-center">
              <span class="block text-white font-medium">{{ c.label }}</span>
            </div>
          </label>
        </div>
      </div>

      <div class="space-y-2">
        <label class="block text-sm font-medium text-slate-300">Package Weight (kg)</label>
        <div class="relative">
          <input 
            type="number" 
            v-model="form.weight" 
            step="0.1" 
            min="0"
            class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all pr-12"
            placeholder="0.0"
          >
          <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500">kg</span>
        </div>
      </div>

      <div class="flex items-center space-x-3 p-3 bg-slate-900/50 rounded-lg border border-slate-700/50">
        <input 
          type="checkbox" 
          id="useWs" 
          v-model="useWebSocket"
          class="w-5 h-5 rounded border-slate-600 text-primary focus:ring-primary bg-slate-700"
        >
        <label for="useWs" class="text-sm text-slate-300 cursor-pointer select-none">
          Use WebSocket for calculation
        </label>
      </div>

      <button 
        type="submit" 
        :disabled="loading || form.weight <= 0"
        class="w-full bg-gradient-to-r from-primary to-accent hover:from-primary/90 hover:to-accent/90 text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/25 transform transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center"
      >
        <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span v-if="!loading">Calculate Shipping Cost</span>
        <span v-else>Calculating...</span>
      </button>
    </form>

    <div v-if="result !== null" class="mt-8 p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-center animate-fade-in-up">
      <p class="text-sm text-emerald-400 mb-1">Estimated Cost</p>
      <div class="text-4xl font-bold text-emerald-400">
        {{ result.toFixed(2) }} <span class="text-lg">EUR</span>
      </div>
    </div>

    <div v-if="error" class="mt-8 p-6 bg-red-500/10 border border-red-500/20 rounded-xl text-center animate-fade-in-up">
      <p class="text-sm text-red-400 font-semibold">Calculation Error</p>
      <p class="text-red-300 mt-1">{{ error }}</p>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
