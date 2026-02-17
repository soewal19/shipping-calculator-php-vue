import { test, expect } from '@playwright/test';

test.describe('Shipping Cost Calculator', () => {
    test.beforeEach(async ({ page }) => {
        // Go to the home page
        await page.goto('/');
    });

    test('should display total cost for TransCompany', async ({ page }) => {
        // Check title
        await expect(page).toHaveTitle(/Shipping Cost Calculator/i);

        // Fill in the form
        // Assumptions on selectors: input[type="number"], select, button
        await page.fill('input[type="number"]', '10');
        await page.selectOption('select', { value: 'transcompany' });

        // Intercept API call to mock response if backend is not ready (optional, but good for isolation)
        // For E2E we usually want real backend, but since it's missing, this test will fail naturally.

        // Click calculate
        await page.click('button[type="submit"]');

        // Expect result
        // Assumption: Result is verified by checking text content
        await expect(page.locator('body')).toContainText('100.00 EUR'); // Adjust expectation based on actual logic
    });

    test('should display total cost for PackGroup', async ({ page }) => {
        await page.fill('input[type="number"]', '10');
        await page.selectOption('select', { value: 'packgroup' });
        await page.click('button[type="submit"]');

        // Expect result - assumption on price
        await expect(page.locator('body')).toContainText('10.00 EUR'); // Adjust expectation based on actual logic
    });

    test('should show error for invalid input', async ({ page }) => {
        await page.fill('input[type="number"]', '-5');
        await page.click('button[type="submit"]');

        // Expect error message
        await expect(page.locator('body')).toContainText(/error/i);
    });
});
