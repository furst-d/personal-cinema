#!/bin/sh

# Ensure the directory for Redis configuration exists
mkdir -p /usr/local/etc/redis

# Create a Redis configuration file with max memory and memory policy settings
cat <<EOF > /usr/local/etc/redis/redis.conf
requirepass $REDIS_PASSWORD
maxmemory 6gb
maxmemory-policy noeviction
EOF

# Start Redis with the custom configuration
redis-server /usr/local/etc/redis/redis.conf
