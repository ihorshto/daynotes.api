#!/bin/bash

# Start ngrok in background
ngrok http 8000 > /dev/null &

# Wait for ngrok to boot
sleep 3

# Get public HTTPS URL from ngrok API
NGROK_URL=$(curl -s http://127.0.0.1:4040/api/tunnels \
  | grep -o '"public_url":"https:[^"]*' \
  | head -n1 \
  | cut -d'"' -f4)

echo "Ngrok URL: $NGROK_URL"

# Update .env
sed -i.bak "s|^NGROK_URL=.*|NGROK_URL=$NGROK_URL|" .env

# Clear config cache
php artisan config:clear

# Set Telegram webhook
php artisan telegram:set-webhook

echo "Script completed."
