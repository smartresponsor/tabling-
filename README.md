# Tabling

Tabling is the Symfony-side, provider-neutral table definition primitive.

It owns PHP declarations for columns, filter metadata, row/bulk actions, default ordering, table capabilities and Symfony Security visibility. It does not own CRUD mutation, collection query execution, React, JavaScript data-grid implementations or final visual design.

Collection semantics are delegated to `collectioning/collection`. Actions point to application/Cruding routes and are authorized through Symfony Security. UI providers such as Ant Design Pro Components and PrimeReact consume normalized table metadata through provider mappers.

Provider mapper output is a serialization-ready metadata boundary. Table action DTOs are normalized to scalar/array metadata before they leave Tabling; CRUD execution and route ownership remain in Cruding/host applications.

## PHP-first table declarations

Applications may declare a table as a small Symfony-side class and let Tabling compile it into the provider-neutral definition consumed by Ant Design Pro Components or PrimeReact:

```php
final class UserTable extends AbstractTable
{
    protected function name(): string { return 'users'; }

    protected function collection(): CollectionDefinitionDTO
    {
        return new CollectionDefinitionDTO(User::class, []);
    }

    protected function configureColumns(TableColumns $columns): void
    {
        $columns->text('name', sortable: true, searchable: true)->status()->dateTime('createdAt');
    }

    protected function configureActions(TableActions $actions): void
    {
        $actions->edit('crud_edit')->delete('crud_delete')->bulk('archive', 'Archive', 'crud_bulk_archive');
    }
}
```

`AbstractTable` owns declaration ergonomics only. Collection execution remains in Collectioning, route/mutation execution remains in Cruding or the host application, and provider mappers only translate the resulting metadata to the selected UI ecosystem.
