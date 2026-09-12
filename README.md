# Tabling

Tabling is the Symfony-side, provider-neutral table definition primitive.

It owns PHP declarations for columns, filter metadata, row/bulk actions, default ordering, table capabilities and Symfony Security visibility. It does not own CRUD mutation, collection query execution, React, JavaScript data-grid implementations or final visual design.

Collection semantics are delegated to `collectioning/collection`. Actions point to application/Cruding routes and are authorized through Symfony Security. UI providers such as Ant Design Pro Components and PrimeReact consume normalized table metadata through provider mappers.
