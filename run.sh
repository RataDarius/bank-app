#!/bin/bash
docker compose down -v > /dev/null 2>&1
rm -f templates/logs/stolen_cookies.txt src/logs/stolen_cookies.txt
docker compose up -d --build
echo "SecureBank is running at http://localhost:8080"
echo ""
echo "To stop:"
echo "  docker compose down       — stops containers (data persists)"
echo "  docker compose down -v    — stops + wipes the database (full reset)"
