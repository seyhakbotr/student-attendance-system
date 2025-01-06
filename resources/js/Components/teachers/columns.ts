import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableDropDown from "./DataTableDropDown.vue";
import { ArrowUpDown } from "lucide-vue-next";
import { Button } from "../ui/button";
import { Checkbox } from "../ui/checkbox";
import { router } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { Classrooms } from "@/Pages/Classroom/Index.vue";
export interface Teacher {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
    classroom: Classrooms;
    classroom_count?: number;
    classroom_id: number | null;
}

export const columns: ColumnDef<Teacher>[] = [
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
        cell: (info) => info.row.original.classroom_count?.toString(),
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
    router.delete(`/teacher/${id}`, {
        onSuccess: () => {
            toast.success("Teacher Deleted", {
                description: "Teacher has been deleted successfully",
            });
            location.reload();
        },
        onError: () => {
            toast.error("Error deleting teacher");
        },
    });
}
export function handleBulkDelete(selectedIds: number[]) {
    console.log(`Delete teacher with IDs: ${selectedIds}`);

    const reloadAfterDelay = () => {
        setTimeout(() => {
            location.reload();
        }, 2000);
    };

    router.delete(`/teachers/${selectedIds}`, {
        data: { ids: selectedIds },
        onSuccess: () => {
            toast.success("teachers Deleted", {
                description: "Selected teachers have been deleted successfully",
            });
            reloadAfterDelay();
        },
        onError: () => {
            toast.error("teachers failed to deleted", {
                description: "Selected teachers failed to delete",
            });
        },
    });
}

function handleEdit(id: number) {
    console.log("Hit Edit", id);
    router.visit(`/teacher/${id}`);
}
