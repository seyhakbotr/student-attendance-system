import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import { ArrowUpDown } from "lucide-vue-next";
import { Button } from "../ui/button";
import { Checkbox } from "../ui/checkbox";
import { router } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import type { Faculty } from "../faculty/columns";
export interface Major {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
    faculty: Faculty;
}

export const columns: ColumnDef<Major>[] = [
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
        accessorKey: "faculty",
        header: "Faculty Name",
        cell: (info) => info.row.original.faculty.name,
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
    console.log(`Delete major with ID: ${id}`);
    router.delete(`/major/${id}`, {
        onSuccess: () => {
            toast.success("Major Deleted", {
                description: "Major has been deleted successfully",
            });
            location.reload();
        },
        onError: () => {
            toast.error("Error deleting Major");
        },
    });
}
export function handleBulkDelete(selectedIds: number[]) {
    console.log(`Delete majors with IDs: ${selectedIds}`);

    // Function to reload the page after a delay
    const reloadAfterDelay = () => {
        setTimeout(() => {
            location.reload();
        }, 2000); // 2 seconds delay
    };

    router.delete(`/majors`, {
        data: { ids: selectedIds },
        onSuccess: () => {
            toast.success("Majors Deleted", {
                description: "Selected majors have been deleted successfully",
            });
            // Call the reload function after success
            reloadAfterDelay();
        },
        onError: () => {
            toast.error("Error deleting majors");
        },
    });
}

function handleEdit(id: number) {
    console.log("Hit Edit", id);
    router.visit(`/major/${id}`);
}
