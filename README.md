Sometimes You have to give the visual interface of i18n message CRUD for a customer. To do this, You need to have storage, which is not under version control and allowed from a form.

# I18n messages stored in database

With this bundle i18n messages stored in a database instead of files, then, you can implement web-interface to manage it.

## Installation

```bash
composer require creative/symfony-db-i18n-bundle
```

Bundle **has not** (yet) a flex auto-configurator. Add 

```php
Creative\DbI18nBundle\DbI18nBundle::class => ['all' => true],
```

to you `config/bundles.php` file, and (optional) place the `db_i18n.yaml` with configuration (see below) file to your config directory.

## Some rules:

- you application service container must have aa array `locales` parameter with possible application locales. For example:
    ```yaml
    # config/services.yaml
    parameters:
      locales: [ 'ru', 'en', 'de' ]
    ```
- implementation of `Symfony\Contracts\Translation\TranslatorInterface` must have a `getCatalogue` method (usually, it have) for import messages from translation files to database.
- You must define the default messages domain as `db_messages` in you views to use messages from database. For example:
    ```yaml
    # templates/main.html.twig
    {% trans_default_domain 'db_messages' %}
    ```
- update you database schema after install this bundle — use `bin/console doctrine:schema:update` command or make migration for this.

So, now you can load messages from old translation files to the database. Command

```bash
bin/console creative:db-i18n:migrate translations/messages.en.yaml
```

will import all messages from `[project root]/translations/messages.en.yaml`. You can set absolute path instead, nevermind, but file name must be compatible with Symfony localization files agreement — `<domain>.<locale>.<format>`.

After (or instead of) that, make your forms/interfaces and add, change and so on with your messages.

## Defaults

Default config is

```yaml
# src/Resources/config/db_i18n.yaml
db_i18n:
  entity: Creative\DbI18nBundle\Entity\Translation
  domain: db_messages
```

Copy this wherever you want and modify.

As you can see, the default messages domain is `db_messages`. If you want to override this and store default Symfony domain `messages` in a database, don't forget to remove (or rename) you `translations/messages.<locale>.[yaml|csv|xlf]` file.
