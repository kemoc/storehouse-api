# Simple storehouse API by Radek Z

## Install

Add routing to  `<symfony-root>/config/routes.yaml`:

```
kemoc_storehouse_api:
    resource: ../vendor/kemoc/storehouse-api/Controller/REST
    prefix: /storehouse/api/rest
    type: rest
    name_prefix: storehouse_api_rest_
...
```

Set `<symfony-root>/config/packages/fos_rest.yaml`
by: `<this-bundle>/Resources/doc/fos_rest.yaml`

If you do not have database then create it.
Configure connection to DB.

Exec (after composer install):

```bash
php bin/console doctrine:schema:update --force
```