#!/bin/bash

# This script sets up WordPress and ensures that the environment is configured correctly.

cd /var/www/html/wordpress

# Check if WordPress is installed
if ! wp core is-installed --allow-root; then
    echo "WordPress is not installed. Downloading Core..."
    wp core download
    echo "Installing Wordpress..."
    wp core install --url='$DDEV_PRIMARY_URL' --title="DDEV WordPress" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com"
fi

SOURCE_PLUGIN_DIR="/var/www/html/siteimprove-accessibility"
TARGET_PLUGIN_DIR="/var/www/html/wordpress/wp-content/plugins/siteimprove-accessibility"

# Ensure the plugin directory does not already exist (prevent overwriting an actual plugin directory)
if ! [ -d "$TARGET_PLUGIN_DIR" ]; then
    echo "Creating symlink for the custom plugin..."
    ln -s "$SOURCE_PLUGIN_DIR" "$TARGET_PLUGIN_DIR"
fi

# Activate the plugin
wp plugin activate siteimprove-accessibility

# Install land activate additional plugins for development environment
wp plugin install debug-bar
wp plugin install query-monitor

cd /var/www/html
echo "Setup completed."