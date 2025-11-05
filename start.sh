#!/bin/bash

echo "🛒 Shopping Cart Application - Startup Script"
echo "=============================================="
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running!"
    echo "Please start Docker and try again."
    exit 1
fi

echo "✅ Docker is running"
echo ""

# Stop any existing containers
echo "🛑 Stopping existing containers..."
docker-compose down

echo ""
echo "🔨 Building and starting containers..."
docker-compose up -d --build

echo ""
echo "⏳ Waiting for services to be ready..."
sleep 5

# Check if containers are running
if docker-compose ps | grep -q "Up"; then
    echo "✅ Containers are running!"
    echo ""
    echo "📊 Container Status:"
    docker-compose ps
    echo ""
    echo "🧪 Running tests..."
    docker-compose exec -T backend php artisan test
    echo ""
    echo "✅ Application is ready!"
    echo ""
    echo "🌐 Access URLs:"
    echo "   Frontend: http://localhost:3000"
    echo "   Backend API: http://localhost:8000/api/products"
    echo ""
    echo "📚 Documentation:"
    echo "   Quick Start: QUICK_START.md"
    echo "   Full Guide: INSTRUCTIONS.md"
    echo "   Test Results: TEST_RESULTS.md"
    echo ""
    echo "🎉 Happy shopping!"
else
    echo "❌ Error: Containers failed to start"
    echo "Check logs with: docker-compose logs"
    exit 1
fi

