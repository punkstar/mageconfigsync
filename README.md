# Magento Configuration Sync

This is a tool designed to allow teams to keep Magento (1.x and 2.x supported) configuration under version control, eliminating the unknown when tracking down potentially configuration related bugs.

![image](http://up.nicksays.co.uk/image/3J3n461U1E35/Screen%20Shot%202013-09-11%20at%2018.47.10.png)

## File Syntax

The configuration values are stored in a YAML file.  The format of the file is as follows:

    environment:
        scope_key:
           path: value

For example:

    production:
        default:
            dev/debug/template_hints: 0
    development:
        default:
            dev/debug/template_hints: 1
        stores-1:
            currency/options/base: GBP
            dev/restrict/allow_ips: null

The above will:

* disable template hints on product;
* enable template hints on the development environment;
* set the currency to Pounds Sterling in the store scope for the store with ID #1;
* ensure the allowed development ip's are inherited from the website for store #1.

Valid scope keys are:

* default
* stores-`$id`
* websites-`$id`

## Usage

    php bin/mageconfigsync --help
    
 Most commands take an optional argument of `--magento-root` if not running from within a Magento directory and an optional argyment of `--env` used to describe the current environment of the configuration.

### Dump

    php bin/mageconfigsync dump --help

The dump command outputs the current contents of the configuration as YAML to stdout.  Specify `--env` and the environment will be added as the first key, for convenience.

### Load

    php bin/mageconfigsync load --help

The load command synchronises the database with the contents of the file.  Any changed lines will be output to stderr.  The `--env` option will determine which section of the YAML file is loaded.

### Diff

    php bin/mageconfigsync diff --help configuration_file.yaml

## Example Scenario

Thanks to the symfony/yaml component we can use all the tricks YAML provides us, including merge keys.  Consider the following file, `config.yml`:

    prod:
      default: &prod_global
        currency/options/base: GBP
        dev/debug/template_hints: 0

    dev:
      default:
        <<: *prod_global
        dev/debug/template_hints: 1

Our aim is to ensure that the currency is always set to `GBP`, regardless of the environment, but template hints should only be enabled for the local environment.

On our development machine we can pull our latest changes and run the following command to get the configuration just as we want it:

    php bin/mageconfigsync load --magento-root ~/Sites/magento --env dev config.yml
    
We can also use this `config.yml` as part of our deployment process.  Consider a workflow like the following:

    // Take a backup of the configuration, incase we need to restore as part of a rollback
    php bin/mageconfigsync dump --env prod > config.yml.pre-deploy
    
    // Give us a diff for the deployment log so we can see what's about to be changed
    php bin/mageconfigsync diff --file-env prod config.yml
    
    // Sync the latest configuration changes to prod
    php bin/mageconfigsync load --env prod config.yml
    
Congratulations, your Magento configuration is now under control, is auditable and consistent.
