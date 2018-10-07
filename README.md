# Simple storehouse API by Radek Z

## Install

Add routing to /config/routes.yaml

```
kemoc_storehouse_api:
    resource: ../vendor/kemoc/storehouse-api/Controller/REST
    prefix: /storehouse/api/rest
    type: rest
...
```

Set `<symfony-root>/config/packages/fos_rest.yaml`
by: `<this-bundle>/Resources/doc/fos_rest.yaml`

Exec (after composer install):

```bash
php bin/console doctrine:schema:update --force
```