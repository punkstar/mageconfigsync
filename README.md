# Mage Config Sync

**Note: This tool is a work in progress and should not be used in production systems**

This is a tool designed to allow teams to keep Magento configuration under version control, eliminating the unknown when tracking down potentially configuration related bugs.

## File Syntax

The configuration values are stored in a YAML file.

## Usage

    php bin/mageconfigsync --help
    
 Most commands take an optional argument of `--magento-root` if not running from within a Magento directory.

### Dump

    php bin/mageconfigsync dump --help
    
* Takes an optional argument of `--env` used to describe the current environment of the configuration.

### Load

    php bin/mageconfigsync load --help

* Takes an optional argument of `--env` used to describe the current environment of the configuration.

### Diff

    php bin/mageconfigsync diff --help configuration_file.yaml
    
* Takes optional arguments of `--file-env` and `--db-end` used to describe the environments in the yaml file and database.
