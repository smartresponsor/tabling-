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

    protected function configureFacets(TableFacets $facets): void
    {
        $facets->terms('status')->terms('region', limit: 10, includeMissing: true);
    }

    protected function configureDefaultSorting(TableSorting $sorting): void
    {
        $sorting->desc('createdAt')->asc('name');
    }

    protected function capabilities(): TableCapabilitiesDTO
    {
        return new TableCapabilitiesDTO(rowSelection: true, bulkActions: true, export: true);
    }
}
```

`AbstractTable` owns declaration ergonomics only. Collection execution remains in Collectioning, route/mutation execution remains in Cruding or the host application, and provider mappers only translate the resulting metadata to the selected UI ecosystem.

## Server-side grid protocol

Tabling also bridges provider-specific grid request state into Collectioning without teaching Collectioning about Ant Design or PrimeReact. `AntDesignCollectionQueryMapper` accepts ProTable-style `current`/`pageSize`, filters and sorter metadata; `PrimeReactCollectionQueryMapper` accepts lazy DataTable-style `first`/`rows`, filters and single/multi-sort metadata. Both delegate policy validation and query construction to `TableCollectionQueryBuilder`, which emits `CollectionQueryDTO` and rejects fields/operators not allowed by the table's `CollectionDefinitionDTO`.

Provider-specific request grammar therefore stops at Tabling; canonical search, filter, sort, projection and pagination semantics remain owned by Collectioning. Provider operators are translated only when Tabling has an explicit canonical mapping: unsupported explicit PrimeReact match modes raise `InvalidArgumentException` rather than being silently dropped or reinterpreted as another Collectioning operator.

## Saved and personalized views

`TableViewDTO` captures provider-neutral saved view state: column visibility/order/width/pinning, search text, Collectioning filters/sorts, page size and metadata. `TableViewNormalizer` revalidates saved state against the current table/Collectioning policies so stale or unauthorized fields cannot be replayed after a schema or permission change. `TableViewStoreInterface` is the persistence boundary; hosts may back it with Doctrine, Redis or another store without making Tabling own user storage.

`AntDesignTableViewMapper` and `PrimeReactTableViewMapper` translate one normalized view into each provider's native state shape, preserving a single backend representation across both UI ecosystems.

## Faceted navigation

Tables may declare facet dimensions through `TableFacets`. Tabling owns only the declaration and provider metadata; bucket computation remains a Collectioning concern. `TableFacetService` converts declared `TableFacetDTO` values into Collectioning `CollectionFacetDTO` requests and delegates them to `CollectionFacetProcessorInterface`.

This keeps facet counts consistent with the same search and filter policy used for collection queries. Ant Design Pro and PrimeReact receive the same provider-neutral facet declarations, while Collectioning decides which fields are facetable and executes aggregation.
