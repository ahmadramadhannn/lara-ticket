// tests/Frontend/schedule-search.test.js

import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { getByLabelText, getByRole, getByText } from '@testing-library/dom';
import '@testing-library/jest-dom';

// Simplified HTML from resources/views/schedules/index.blade.php
const renderComponent = () => {
  const container = document.createElement('div');
  container.innerHTML = `
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
      <h1 class="text-4xl font-bold text-white mb-4">
          Book Online Bus Tickets
      </h1>
      <form action="/schedules" method="GET" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Origin -->
              <div>
                  <label for="origin" class="block text-sm font-medium text-gray-700 mb-1">
                      From
                  </label>
                  <select id="origin" name="origin" required
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                      <option value="">Select Origin Terminal</option>
                      <optgroup label="West Java">
                        <option value="1">Terminal A (City A)</option>
                      </optgroup>
                  </select>
              </div>

              <!-- Destination -->
              <div>
                  <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">
                      To
                  </label>
                  <select id="destination" name="destination" required
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                      <option value="">Select Destination Terminal</option>
                      <optgroup label="West Java">
                        <option value="2">Terminal B (City B)</option>
                      </optgroup>
                  </select>
              </div>

              <!-- Date -->
              <div>
                  <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                      Departure Date
                  </label>
                  <input type="date" id="date" name="date" required
                      min="2024-01-01"
                      value="2024-01-01"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              </div>

              <!-- Operator (optional) -->
              <div>
                  <label for="operator" class="block text-sm font-medium text-gray-700 mb-1">
                      Bus Operator (optional)
                  </label>
                  <select id="operator" name="operator"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                      <option value="">All Operators</option>
                      <option value="1">Operator X</option>
                  </select>
              </div>
          </div>

          <div class="flex justify-center">
              <button type="submit"
                  class="px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                  🔍 Search Schedule
              </button>
          </div>
      </form>
    </div>
  `;
  return container;
};

describe('Schedule Search Form', () => {
  let container;

  beforeEach(() => {
    container = renderComponent();
    document.body.appendChild(container);
  });

  afterEach(() => {
    document.body.innerHTML = '';
  });

  it('renders the main heading', () => {
    const heading = getByRole(document.body, 'heading', {
      name: /Book Online Bus Tickets/i,
    });
    expect(heading).toBeInTheDocument();
  });

  it('renders the "From" origin field', () => {
    const originLabel = getByText(document.body, 'From');
    const originSelect = getByLabelText(document.body, 'From');
    expect(originLabel).toBeInTheDocument();
    expect(originSelect).toBeInTheDocument();
    expect(originSelect.tagName).toBe('SELECT');
  });

  it('renders the "To" destination field', () => {
    const destinationLabel = getByText(document.body, 'To');
    const destinationSelect = getByLabelText(document.body, 'To');
    expect(destinationLabel).toBeInTheDocument();
    expect(destinationSelect).toBeInTheDocument();
    expect(destinationSelect.tagName).toBe('SELECT');
  });

  it('renders the "Departure Date" field', () => {
    const dateLabel = getByText(document.body, 'Departure Date');
    const dateInput = getByLabelText(document.body, 'Departure Date');
    expect(dateLabel).toBeInTheDocument();
    expect(dateInput).toBeInTheDocument();
    expect(dateInput.type).toBe('date');
  });

  it('renders the optional "Bus Operator" field', () => {
    const operatorLabel = getByText(document.body, /Bus Operator/i);
    const operatorSelect = getByLabelText(document.body, /Bus Operator/i);
    expect(operatorLabel).toBeInTheDocument();
    expect(operatorSelect).toBeInTheDocument();
    expect(operatorSelect.tagName).toBe('SELECT');
  });

  it('renders the "Search Schedule" button', () => {
    const searchButton = getByRole(document.body, 'button', {
      name: /Search Schedule/i,
    });
    expect(searchButton).toBeInTheDocument();
    expect(searchButton.type).toBe('submit');
  });
});