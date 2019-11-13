# KaliopEzRemoteIdBundle


Bundle adds Reference tab in location view in admin panel where the remoteID can be changed by user with right
permissions.

![Preview image](Resources/docs/images/preview.jpg)

## Instalation

```bash
composer require kaliop/kaliop-ez-remoteid-bundle
```

1. Enable bundle in kernel

    ```php
        public function registerBundles()
        {
            // ...

            $bundles = [
                // ...

                new Kaliop\EzRemoteIdBundle\KaliopEzRemoteIdBundle()
            ];

            // ...
        }
    ```

2. Add KaliopEzRemoteIdBundle to assetic configuration.

    ```yaml
    assetic:
        bundles:
          # ...
          - KaliopEzRemoteIdBundle
    ```

3. Import routings in app/config/routing.yml

    ```yaml
    kaliop_remote_id:
        resource: "@KaliopEzRemoteIdBundle/Resources/config/routing.xml"
        prefix:   /
    ```
   
4. Configuration

```
kaliop_ez_remote_id:
    content_types:
        test:
            pattern: '/^[a-z][a-z0-9]*$/'
            max_length: 32
        test2:
            max_length: 8
            pattern: '/^[a-z][a-z0-9]*$/'
        test3:
            pattern: '/^.*$/'
            max_length: 32
        test4:
            pattern: '/^a.*b$/'
            max_length: 10
    default:
        pattern: '/^[a-z][a-z0-9]*$/'
        max_length: 32
```

defaults in a given example are set when this values are not provide. For example this config can be also achieved by:
```
kaliop_ez_remote_id:
    content_types:
        test: ~
        test2:
            max_length: 8
        test3:
            pattern: /^.*$/
        test4:
            pattern: /^a.*b$/
            max_length: 10
```
Note: Please keep in mind that `max_length` should not exceed constraints in the database tables what is 100 characters (`VARCHAR(100)`). This can differ from different versions of ezPlatform so it's good practice to check it with your current implementation.

Note: Invalid pattern message is in two variants. If the pattern desctiption is defined in the translation domain
`kaliop_ez_remote_id` like the one below:
```
# kaliop_ez_remote_id.en.yml

pattern_description:
  '/^[a-z0-9]+$/': Value must contain only small letters and numbers.
```
Then the validation message is build from key `kaliop_ez_remote_id.validator.remote_id_pattern.invalid`
and the description is available in parameter `%patternDescription%`. If there is not translation for the pattern
then `kaliop_ez_remote_id.validator.remote_id_pattern.invalid_default` is used and the pattern is available
in the `%pattern%` parameter.

```
kaliop_ez_remote_id:
    remote_id_pattern:
      invalid: Remote ID has invalid format. %patternDescription%
      invalid_default: Remote ID has invalid format. Value must match %pattern%.
```

5. Clear cache

    ```
    php bin/console cache:clear
    ```

6. Install assets

    ```bash
    php bin/console assets:install --symlink --relative
    ```
   
7. Configure permissions by adding the right policies. The view policy is for showing the Reference tab in the location
view. Edit enable user to change the remote ID.

![Select new policy](Resources/docs/images/newpolicy.jpg)

![Set limitations](Resources/docs/images/limitations.jpg)
