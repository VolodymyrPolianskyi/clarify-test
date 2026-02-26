<template>
  <div class="app">
    <div class="card">
      <div class="card-header">
        <div class="logo">📦</div>
        <h1>ZOO Shipping Calculator</h1>
        <p class="subtitle">Calculate your shipping cost instantly</p>
      </div>

      <div class="card-body">
        <!-- Carrier Select -->
        <div class="field">
          <label for="carrier">Carrier</label>
          <div class="select-wrapper">
            <select
              id="carrier"
              v-model="selectedCarrier"
              :disabled="loadingCarriers"
              class="select"
            >
              <option value="" disabled>
                {{ loadingCarriers ? 'Loading carriers...' : 'Select a carrier' }}
              </option>
              <option
                v-for="carrier in carriers"
                :key="carrier.slug"
                :value="carrier.slug"
              >
                {{ carrier.name }}
              </option>
            </select>
          </div>
          <p v-if="carriersError" class="field-error">{{ carriersError }}</p>
        </div>

        <!-- Weight Input -->
        <div class="field">
          <label for="weight">Parcel Weight (kg)</label>
          <input
            id="weight"
            v-model="weightKg"
            type="number"
            step="0.1"
            min="0.1"
            placeholder="e.g. 12.5"
            class="input"
          />
        </div>

        <!-- Calculate Button -->
        <button
          class="btn"
          :disabled="!canCalculate || calculating"
          @click="calculate"
        >
          <span v-if="calculating" class="spinner">⏳</span>
          <span v-else>Calculate Price</span>
        </button>

        <!-- Result -->
        <transition name="fade">
          <div v-if="result" class="result result--success">
            <div class="result-label">Shipping cost</div>
            <div class="result-price">
              {{ result.price.toFixed(2) }}
              <span class="result-currency">{{ result.currency }}</span>
            </div>
            <div class="result-details">
              {{ result.weightKg }} kg via {{ result.carrier }}
            </div>
          </div>
        </transition>

        <transition name="fade">
          <div v-if="error" class="result result--error">
            <span class="error-icon">⚠️</span>
            {{ error }}
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { fetchCarriers, calculateShipping } from './services/shippingApi.js'

const carriers = ref([])
const loadingCarriers = ref(true)
const carriersError = ref('')

const selectedCarrier = ref('')
const weightKg = ref('')

const calculating = ref(false)
const result = ref(null)
const error = ref('')

const canCalculate = computed(() => {
  return selectedCarrier.value && weightKg.value && parseFloat(weightKg.value) > 0
})

onMounted(async () => {
  try {
    carriers.value = await fetchCarriers()
  } catch (e) {
    carriersError.value = 'Failed to load carriers. Is the API running?'
  } finally {
    loadingCarriers.value = false
  }
})

async function calculate() {
  result.value = null
  error.value = ''
  calculating.value = true

  try {
    result.value = await calculateShipping(selectedCarrier.value, weightKg.value)
  } catch (e) {
    error.value = e.message
  } finally {
    calculating.value = false
  }
}
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.app {
  width: 100%;
  max-width: 440px;
}

.card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 32px 32px 28px;
  text-align: center;
  color: white;
}

.logo {
  font-size: 48px;
  margin-bottom: 12px;
}

.card-header h1 {
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 6px;
}

.subtitle {
  font-size: 14px;
  opacity: 0.85;
}

.card-body {
  padding: 28px 32px 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.select-wrapper {
  position: relative;
}

.select,
.input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 15px;
  color: #111827;
  background: #f9fafb;
  transition: border-color 0.2s, background 0.2s;
  appearance: none;
}

.select:focus,
.input:focus {
  outline: none;
  border-color: #667eea;
  background: #fff;
}

.select:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.field-error {
  color: #ef4444;
  font-size: 13px;
}

.btn {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.1s;
}

.btn:hover:not(:disabled) {
  opacity: 0.92;
  transform: translateY(-1px);
}

.btn:active:not(:disabled) {
  transform: translateY(0);
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.result {
  padding: 20px;
  border-radius: 12px;
  text-align: center;
}

.result--success {
  background: linear-gradient(135deg, #d1fae5, #a7f3d0);
  border: 1px solid #6ee7b7;
}

.result--error {
  background: #fef2f2;
  border: 1px solid #fca5a5;
  color: #991b1b;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.result-label {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #065f46;
  margin-bottom: 6px;
}

.result-price {
  font-size: 42px;
  font-weight: 800;
  color: #064e3b;
  line-height: 1;
  margin-bottom: 8px;
}

.result-currency {
  font-size: 22px;
  font-weight: 600;
  margin-left: 4px;
}

.result-details {
  font-size: 13px;
  color: #047857;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style>
