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

    protected function configureAggregations(TableAggregations $aggregations): void
    {
        $aggregations
            ->count('rows', 'Rows')
            ->sum('totalAmount', 'amount', 'Total amount')
            ->avg('averageAmount', 'amount', 'Average amount')
            ->groupBy('status');
    }

    protected function configureDefaultSorting(TableSorting $sorting): void
    {
        $sorting->desc('createdAt')->asc('name');
    }

    protected function capabilities(): TableCapabilitiesDTO
    {
        return new TableCapabilitiesDTO(rowSelection: true, bulkActions: true, export: true);
    }

    protected function exportPolicy(): ?TableExportPolicyDTO
    {
        return new TableExportPolicyDTO(
            formats: ['csv'],
            scopes: [CollectionDataScopeDTO::FILTERED, CollectionDataScopeDTO::SELECTED],
            defaultScope: CollectionDataScopeDTO::FILTERED,
            permission: 'EXPORT_USERS',
        );
    }
}
```

`AbstractTable` owns declaration ergonomics only. Collection execution remains in Collectioning, route/mutation execution remains in Cruding or the host application, and provider mappers only translate the resulting metadata to the selected UI ecosystem.

## Server-side grid protocol

Tabling also bridges provider-specific grid request state into Collectioning without teaching Collectioning about Ant Design or PrimeReact. `AntDesignCollectionQueryMapper` accepts ProTable-style `current`/`pageSize`, filters and sorter metadata; `PrimeReactCollectionQueryMapper` accepts lazy DataTable-style `first`/`rows`, filters and single/multi-sort metadata. Both delegate policy validation and query construction to `TableCollectionQueryBuilder`, which emits `CollectionQueryDTO` and fails closed with `InvalidArgumentException` when an explicit filter, sort, or projection violates the table's `CollectionDefinitionDTO` field policy.

Provider-specific request grammar therefore stops at Tabling; canonical search, filter, sort, projection and pagination semantics remain owned by Collectioning. Provider operators are translated only when Tabling has an explicit canonical mapping: unsupported explicit PrimeReact match modes raise `InvalidArgumentException` rather than being silently dropped or reinterpreted as another Collectioning operator.

## Saved and personalized views

`TableViewDTO` captures provider-neutral saved view state: column visibility/order/width/pinning, search text, Collectioning filters/sorts, page size and metadata. `TableViewNormalizer` revalidates saved state against the current table/Collectioning policies so stale or unauthorized fields cannot be replayed after a schema or permission change. `TableViewStoreInterface` is the persistence boundary; hosts may back it with Doctrine, Redis or another store without making Tabling own user storage.

`AntDesignTableViewMapper` and `PrimeReactTableViewMapper` translate one normalized view into each provider's native state shape, preserving a single backend representation across both UI ecosystems.

## Faceted navigation

Tables may declare facet dimensions through `TableFacets`. Tabling owns only the declaration and provider metadata; bucket computation remains a Collectioning concern. `TableFacetService` converts declared `TableFacetDTO` values into Collectioning `CollectionFacetDTO` requests and delegates them to `CollectionFacetProcessorInterface`.

This keeps facet counts consistent with the same search and filter policy used for collection queries. Ant Design Pro and PrimeReact receive the same provider-neutral facet declarations, while Collectioning decides which fields are facetable and executes aggregation.

## Summaries and grouping

`TableAggregations` declares table summaries such as `count`, `sum`, `avg`, `min`, and `max`, plus optional `groupBy()` dimensions. These declarations are provider-neutral metadata only. `TableAggregationService` translates them to Collectioning `CollectionAggregationDTO` requests and delegates execution to `CollectionAggregationProcessorInterface`.

Ant Design Pro and PrimeReact therefore receive identical summary/grouping metadata, while Collectioning remains authoritative for field/function allowlists, search/filter semantics, grouping eligibility, execution limits, and truncation reporting. This supports footer totals and grouped summary rows without introducing a second aggregation engine inside Tabling.

## Server-side export and data scopes

`TableExportPolicyDTO` declares allowed export formats, allowed data scopes, a default scope, and an optional Symfony Security permission. Export remains disabled unless `TableCapabilitiesDTO::export` is enabled. Both provider mappers receive the same normalized policy metadata; that metadata is descriptive and is not a substitute for backend authorization.

`TableExportService` enforces the table capability and permission, validates the requested scope, and delegates row retrieval to Collectioning's `CollectionScopedReaderInterface`. `currentPage` preserves the visible page query, `filtered` streams the complete filtered result in bounded pages, and `selected` translates selected row keys to a canonical identifier `in` filter through `TableDataScopeResolver`. The generic selected resolver currently requires exactly one scalar identifier field with policy-approved `in` filtering; composite identifiers fail explicitly rather than being exported incorrectly.

Tabling deliberately does not serialize CSV/JSON files or own download controllers. File encoding, response streaming, storage, and delivery remain host/provider concerns built over the scoped row iterable, while Collectioning remains authoritative for search, filters, sorting, projection, and page traversal.
