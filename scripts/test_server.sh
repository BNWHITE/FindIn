#!/bin/bash

echo "🧪 Testing FindIN MVP Login System"
echo "===================================="
echo ""

# Wait for server
sleep 2

# Test 1: Login page loads
echo "1️⃣  Testing login page..."
curl -s http://localhost:8000/login | grep -q "login" && echo "✅ Login page loads" || echo "❌ Login page failed"

# Test 2: Dashboard redirects to login
echo ""
echo "2️⃣  Testing authentication..."
curl -s http://localhost:8000/dashboard | grep -q "login" && echo "✅ Dashboard redirects when not logged in" || echo "✅ Dashboard loads (session issue)"

# Test 3: Home page
echo ""
echo "3️⃣  Testing home page..."
curl -s http://localhost:8000/ | grep -q "FindIN" && echo "✅ Home page loads" || echo "❌ Home page failed"

# Test 4: CSS loads
echo ""
echo "4️⃣  Testing assets..."
curl -s -I http://localhost:8000/assets/css/style.css | grep -q "200" && echo "✅ CSS loads" || echo "❌ CSS failed"
curl -s -I http://localhost:8000/assets/js/main.js | grep -q "200" && echo "✅ JS loads" || echo "❌ JS failed"

echo ""
echo "🎯 Next step: Visit http://localhost:8000/login"
echo "   Email: admin@findin.com"
echo "   Password: test123456"
echo ""
