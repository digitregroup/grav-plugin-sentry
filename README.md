# Sentry Plugin

The **Sentry** Plugin is an extension for [Grav CMS](http://github.com/getgrav/grav).

This plugin provide a sentry.io Grav integration.
It is able to automaticaly setup the Sentry SDK in the backend (server side) using de PHP SDK and in the frontend (browser side) using the Javascript SDK.

## Installation

Installing the Sentry plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `sentry`. You can find these files on [GitHub](https://github.com/digitregroup/grav-plugin-sentry) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/sentry
	
> NOTE: This plugin is a modular component for Grav which may require other plugins to operate, please see its [blueprints.yaml-file on GitHub](https://github.com/digitregroup/grav-plugin-sentry/blob/master/blueprints.yaml).

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/sentry/sentry.yaml` to `user/config/plugins/sentry.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
# Main DSN for the backend (Server side) and the frontend (Browser)
dsn: https://13614dc184a941b49e2513cbbfc67b11@sentry.io/1783145

# Backend configuration
backend:
  enabled: true

  # Override the DSN for the backend (Server side)
  # dsn: https://xxxxxxxxxxxxx@sentry.io/xxxxxxx

  ### Sentry Init variables
  max_breadcrumbs: 100
  attach_stacktrace: false
  # release:
  # environment:
  # server_name:

  ### Context settings
  tags:
    side: backend

# Frontend configuration
frontend:
  enabled: true

  # Override the DSN for the frontend (browser side)
  # dsn: https://xxxxxxxxxxxxx@sentry.io/xxxxxxx

  ### Sentry Init variables
  maxBreadcrumbs: 100
  debug: false
  attachStacktrace: false
  # release:
  # environment:
  # serverName:

  ### Context settings
  tags:
    side: frontend

```

Note that if you use the Admin Plugin, a file with your configuration named sentry.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## To Do

- [ ] Automaticaly catch potentialy logged users and send them into the Sentry context

