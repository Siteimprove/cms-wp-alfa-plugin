# WordPress plugin integration for Siteimprove Accessibility

## Introduction

This repository is used to set up a local development environment for Siteimprove Accessibility WordPress Plugin, using DDEV.
The goal of this repository is to focus on plugin development, while the entire WordPress site is set up
automatically and can be reinstalled at any time.

## Directory Structure

- `.ddev/`: Contains the DDEV configuration files.
- `siteimprove-accessibility/`: Contains the Siteimprove Accessibility WordPress Plugin.
- `wordpress/`: Contains the WordPress installation. This directory is created automatically by DDEV.

## Setting up local development environment

### Prerequisites

- Install [Docker](https://ddev.readthedocs.io/en/stable/users/install/docker-installation/)
- Install [DDEV](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)

Note, that EPAM doesn't provide license for Docker Desktop. As an alternative that has been proven to work and that you can 
use is [Rancher Desktop](https://rancherdesktop.io/), which is a free and open-source alternative.

### Setup Steps

1. Clone the repository:
    ```sh
    git clone https://github.com/Siteimprove/cms-wp-alfa-plugin.git
    cd cms-wp-alfa-plugin
    ```

2. Start the DDEV environment:
    ```sh
    ddev start
    ```
   
    Check out the `.ddev/config.yaml` file for the configuration of the environment. Also note, that the `.ddev/setup.sh`
    script is executed after the environment is started. This script is used to download and install the latest 
    version of WordPress for the first time if it was not installed before.


3. Access the local development site:
    ```sh
    ddev launch
    ```

### Frontend Development

To set up the frontend development environment, follow these steps:

1. Navigate to the `siteimprove-accessibility` directory:
    ```sh
    cd siteimprove-accessibility
    ```

2. Install the necessary npm packages:
    ```sh
    npm install
    ```

The Siteimprove Alfa engine, which is written in TypeScript, along with this project's advanced JavaScript code that 
relies on Node.js, both require compilation into simple JavaScript. This ensures compatibility with WordPress environments. To facilitate this process, the following npm scripts are provided:
- To compile for production, use:
    ```sh
    npm run build
    ```

- To start a development server that watches for changes and automatically recompiles the code, use:
    ```sh
    npm run watch
    ```

### Additional Useful Commands

- To stop the DDEV environment:
    ```sh
    ddev stop
    ```

- To enable Xdebug:
    ```sh
    ddev xdebug on
    ```

- To view logs:
    ```sh
    ddev logs
    ```

For more commands, check out the [DDEV documentation](https://ddev.readthedocs.io/en/stable/users/usage/commands/).
