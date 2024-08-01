# Manage Site Settings Plugin

## Description

This plugin creates a "Site Settings" menu item. It manages ACF fields and customizes the page editor experience by hiding unnecessary features and displaying custom text.

## Features

- Creates a "Site Settings" page with slug "site-settings" on plugin activation.
- Removes the page on plugin deactivation.
- Disables Gutenberg editor and other features on the "Site Settings" page.
- Adds custom text below the page title to guide users on using ACF fields.
- Customizes the admin menu to highlight the "Site Settings" page.

## Installation

1. Upload the `manage-site-settings` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. The "Site Settings" page will be created, and you can manage its fields using ACF.

## Usage

- To add fields to the "Site Settings" page, use the ACF plugin to create and manage fields with the page ID provided in the custom text.
- Customize the page experience through the provided hooks and filters.

## Changelog

**Version 1.0**
- Initial release.

## Author

Taras Yurchysnyn
