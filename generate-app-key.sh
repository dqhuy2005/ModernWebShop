#!/bin/bash
# Script to generate a valid Laravel APP_KEY

echo "Generating Laravel APP_KEY..."
echo ""

# Generate a random 32-byte key and encode it in base64
APP_KEY="base64:$(openssl rand -base64 32)"

echo "Your APP_KEY:"
echo "$APP_KEY"
echo ""
echo "Add this to your Render Environment Variables:"
echo "APP_KEY=$APP_KEY"
