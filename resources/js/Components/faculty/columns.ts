import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableDropDown from "./DataTableDropDown.vue";
import { ArrowUpDown } from "lucide-vue-next";
import { Button } from "../ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { router } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
export interface Faculty {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
    classrooms_count?: number;
}

export const columns: ColumnDef<Faculty>[] = [
    {
        id: "select",
        header: ({ table }) =>
            h(Checkbox, {
                checked: table.getIsAllPageRowsSelected(),
                "onUpdate:checked": (value: boolean) =>
                    table.toggleAllPageRowsSelected(!!value),
                ariaLabel: "Select all",
            }),
        cell: ({ row }) =>
            h(Checkbox, {
                checked: row.getIsSelected(),
                "onUpdate:checked": (value: boolean) =>
                    row.toggleSelected(!!value),
                ariaLabel: "Select row",
            }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        id: "seq",
        header: "Seq",
        cell: ({ row }) => row.index + 1, // Display sequential number
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: "id",
        header: "ID",
        cell: (info) => info.getValue() as string,
    },
    {
        accessorKey: "name",
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: "ghost",
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === "asc"),
                },
                () => ["Name", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })],
            );
        },
        cell: (info) => info.getValue() as string,
    },
    {
        accessorKey: "classrooms_count",
        header: "Classrooms Count",
        cell: (info) => info.row.original.classrooms_count?.toString(),
    },
    {
        id: "actions",
        header: "Actions",
        cell: ({ row }) =>
            h("div", { class: "flex space-x-2" }, [
                h(
                    Button,
                    {
                        variant: "destructive",
                        onClick: () => handleDelete(row.original.id),
                    },
                    "Delete",
                ),
                h(
                    Button,
                    {
                        variant: "default",
                        onClick: () => handleEdit(row.original.id),
                    },
                    "Edit",
                ),
            ]),
    },
];

function handleDelete(id: number) {
    console.log(`Delete faculty with ID: ${id}`);
    router.delete(`/faculty/${id}`, {
        onSuccess: () => {
            toast.success("Faculty Deleted", {
                description: "Faculty has been deleted successfully",
            });
            location.reload();
        },
        onError: () => {
            toast.error("Error deleting faculty");
        },
    });
}
export function handleBulkDelete(selectedIds: number[]) {
    console.log(`Delete majors with IDs: ${selectedIds}`);

    const reloadAfterDelay = () => {
        setTimeout(() => {
            location.reload();
        }, 2000);
    };

    router.delete(`/facultys`, {
        data: { ids: selectedIds },
        onSuccess: () => {
            toast.success("Facultys Deleted", {
                description: "Selected facultys have been deleted successfully",
            });
            // Call the reload function after success
        },
        onError: () => {
            toast.error("Error deleting facultys");
        },
    });
}

function handleEdit(id: number) {
    console.log("Hit Edit", id);
    router.visit(`/faculty/${id}`);
}
