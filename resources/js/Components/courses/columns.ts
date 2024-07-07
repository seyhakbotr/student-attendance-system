import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableDropDown from "./DataTableDropDown.vue";
import { ArrowUpDown } from "lucide-vue-next";
import { Button } from "../ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { router } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
export interface Course {
    id: number;
    name: string;
    major_id: number;
    created_at: string;
    updated_at: string;
    classroom_count?: number;
}

export const columns: ColumnDef<Course>[] = [
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
        accessorKey: "major_id",
        header: "Major",
        cell: ({ row }) => {
            console.log("Major", row.original);
            const major = row.original.major;
            if (major) {
                return major.name; // Display the major name
            } else {
                console.log("No major assigned");
                return "No major assigned"; // Display a default message if major is null
            }
        },
    },
    {
        accessorKey: "classroom_count",
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
    console.log(`Delete course with ID: ${id}`);
    router.delete(`/course/${id}`, {
        onSuccess: () => {
            toast.success("course Deleted", {
                description: "course has been deleted successfully",
            });
            location.reload();
        },
        onError: () => {
            toast.error("Error deleting course");
        },
    });
}
export function handleBulkDelete(selectedIds: number[]) {
    const reloadAfterDelay = () => {
        setTimeout(() => {
            location.reload();
        }, 2000);
    };

    router.delete(`/courses/${selectedIds}`, {
        data: { ids: selectedIds },
        onSuccess: () => {
            toast.success("courses Deleted", {
                description: "Selected courses have been deleted successfully",
            });
            // Call the reload function after success
        },
        onError: () => {
            toast.error("Error deleting courses");
        },
    });
}

function handleEdit(id: number) {
    console.log("Hit Edit", id);
    router.visit(`/course/${id}`);
}
