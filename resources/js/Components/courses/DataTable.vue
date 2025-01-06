<script setup lang="ts" generic="TData, TValue">
import type {
    ColumnDef,
    ColumnFiltersState,
    SortingState,
    TableOptions,
    VisibilityState,
} from "@tanstack/vue-table";
import { handleBulkDelete } from "./columns";
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";

import { valueUpdater } from "@/lib/utils";

import {
    ArrowUpDown,
    ChevronDown,
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronsLeft,
    ChevronsRight,
} from "lucide-vue-next";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import { h, ref } from "vue";
import { reactive } from "vue";
import {
    FlexRender,
    getCoreRowModel,
    getPaginationRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useVueTable,
} from "@tanstack/vue-table";

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";

const props = defineProps<{
    columns: ColumnDef<TData, TValue>[];
    data: TData[];
}>();

const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});
const tableOptions = reactive<TableOptions<Document>>({
    get data() {
        return props.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: (updaterOrValue) =>
        valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: (updaterOrValue) =>
        valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: (updaterOrValue) =>
        valueUpdater(updaterOrValue, rowSelection),
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
        get columnVisibility() {
            return columnVisibility.value;
        },
        get rowSelection() {
            return rowSelection.value;
        },
    },
});

const table = useVueTable(tableOptions);
const handleDeleteButton = () => {
    const selectedRows = table.getSelectedRowModel();
    const selectedIds = selectedRows.flatRows.map((row) => row.original.id);
    console.log("selectedIds", selectedIds);
    handleBulkDelete(selectedIds);
};
</script>

<template>
    <div>
        <div class="flex items-center py-4 gap-4">
            <Input
                class="max-w-sm"
                placeholder="Filter name..."
                :model-value="
                    table.getColumn('name')?.getFilterValue() as string
                "
                @update:model-value="
                    table.getColumn('name')?.setFilterValue($event)
                "
            />
            <Button variant="destructive" @click="handleDeleteButton()">Bulk Delete</Button>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="ml-auto">
                        Columns
                        <ChevronDown class="w-4 h-4 ml-2" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuCheckboxItem
                        v-for="column in table
                            .getAllColumns()
                            .filter((column) => column.getCanHide())"
                        :key="column.id"
                        class="capitalize"
                        :checked="column.getIsVisible()"
                        @update:checked="
                            (value) => {
                                column.toggleVisibility(!!value);
                            }
                        "
                    >
                        {{ column.id }}
                    </DropdownMenuCheckboxItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
        <div class="border rounded-md">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                        >
                            <FlexRender
                                v-if="!header.isPlaceholder"
                                :render="header.column.columnDef.header"
                                :props="header.getContext()"
                            />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :data-state="
                                row.getIsSelected() ? 'selected' : undefined
                            "
                        >
                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :colSpan="columns.length"
                                class="h-24 text-center"
                            >
                                No results.
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
        <div class="flex items-center justify-between px-2 py-6">
            <div class="flex-1 text-sm text-muted-foreground">
                {{ table.getFilteredSelectedRowModel().rows.length }} of
                {{ table.getFilteredRowModel().rows.length }} row(s) selected.
            </div>
            <div class="flex items-center space-x-6 lg:space-x-8">
                <div class="flex items-center space-x-2">
                    <p class="text-sm font-medium">Rows per page</p>
                    <Select
                        :model-value="`${table.getState().pagination.pageSize}`"
                        @update:model-value="table.setPageSize"
                    >
                        <SelectTrigger class="h-8 w-[70px]">
                            <SelectValue
                                :placeholder="`${table.getState().pagination.pageSize}`"
                            />
                        </SelectTrigger>
                        <SelectContent side="top">
                            <SelectItem
                                v-for="pageSize in [5, 10, 20, 30, 40, 50]"
                                :key="pageSize"
                                :value="`${pageSize}`"
                            >
                                {{ pageSize }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div
                    class="flex w-[100px] items-center justify-center text-sm font-medium"
                >
                    Page {{ table.getState().pagination.pageIndex + 1 }} of
                    {{ table.getPageCount() }}
                </div>
                <div class="flex items-center space-x-2">
                    <Button
                        variant="outline"
                        class="hidden w-8 h-8 p-0 lg:flex"
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.setPageIndex(0)"
                    >
                        <span class="sr-only">Go to first page</span>
                        <ChevronsLeft class="w-4 h-4" />
                    </Button>
                    <Button
                        variant="outline"
                        class="w-8 h-8 p-0"
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.previousPage()"
                    >
                        <span class="sr-only">Go to previous page</span>
                        <ChevronLeftIcon class="w-4 h-4" />
                    </Button>
                    <Button
                        variant="outline"
                        class="w-8 h-8 p-0"
                        :disabled="!table.getCanNextPage()"
                        @click="table.nextPage()"
                    >
                        <span class="sr-only">Go to next page</span>
                        <ChevronRightIcon class="w-4 h-4" />
                    </Button>
                    <Button
                        variant="outline"
                        class="hidden w-8 h-8 p-0 lg:flex"
                        :disabled="!table.getCanNextPage()"
                        @click="table.setPageIndex(table.getPageCount() - 1)"
                    >
                        <span class="sr-only">Go to last page</span>
                        <ChevronsRight class="w-4 h-4" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

