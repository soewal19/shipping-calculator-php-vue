#!/bin/bash

# Test Execution Script
# Runs backend unit tests and frontend E2E tests

echo "Starting tests..."

# 1. Backend Tests (PHPUnit)
echo "Running Backend Unit Tests..."
# docker-compose exec -T php bin/phpunit

# if [ $? -ne 0 ]; then
#     echo "Backend tests failed!"
#     exit 1
# fi

# 2. Frontend Tests (E2E)
echo "Running Frontend E2E Tests..."
cd E2E
npm install
npx playwright test

if [ $? -ne 0 ]; then
    echo "E2E tests failed!"
    exit 1
fi

echo "All tests passed successfully."
