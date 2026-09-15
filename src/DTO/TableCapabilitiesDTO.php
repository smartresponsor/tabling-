<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

final readonly class TableCapabilitiesDTO
{
    public function __construct(
        public bool $pagination = true,
        public bool $globalSearch = true,
        public bool $rowSelection = false,
        public bool $bulkActions = false,
        public bool $columnVisibility = true,
        public bool $export = false,
        public bool $virtualScroll = false,
        public bool $inlineEdit = false,
    ) {
    }

    /** @return array<string, bool> */
    public function toArray(): array
    {
        return [
            'pagination' => $this->pagination,
            'globalSearch' => $this->globalSearch,
            'rowSelection' => $this->rowSelection,
            'bulkActions' => $this->bulkActions,
            'columnVisibility' => $this->columnVisibility,
            'export' => $this->export,
            'virtualScroll' => $this->virtualScroll,
            'inlineEdit' => $this->inlineEdit,
        ];
    }
}
