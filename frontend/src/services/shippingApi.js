const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8080'

export async function fetchCarriers() {
  const response = await fetch(`${API_BASE}/api/carriers`)
  if (!response.ok) {
    throw new Error('Failed to load carriers')
  }
  const data = await response.json()
  return data.carriers
}

export async function calculateShipping(carrier, weightKg) {
  const response = await fetch(`${API_BASE}/api/shipping/calculate`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ carrier, weightKg: parseFloat(weightKg) }),
  })

  const data = await response.json()

  if (!response.ok) {
    throw new Error(data.error || 'Calculation failed')
  }

  return data
}
