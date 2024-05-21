import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import DataTableDropDown from "./DataTableDropDown.vue";
import { ArrowUpDown } from "lucide-vue-next";
import { Button } from "../ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import type { Course } from "@/Pages/Classroom/Show.vue";

export interface Student {
    id: string;
    name: string;
    gender: "male" | "female";
    qr_code_image_path: string;
    attendance: Array<{
        course: Course | null;
        status: "absent" | "present";
        date: string; // Optional: if you want to show the date as well
    }>;
}

export const columns: ColumnDef<Student>[] = [
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
        accessorKey: "gender",
        header: "Gender",
        cell: (info) => info.getValue() as string,
    },
    {
        accessorKey: "qr_code_image_path",
        header: "QR Code",
        cell: (info) =>
            h("img", {
                src: `/storage/${(info.getValue() as string).replace("public/", "")}`,
                alt: "QR Code",
                class: 'w-[80px] h-[80px]'
            }),
    },
    {
        accessorKey: "attendance",
        header: "Attendance",
        cell: ({ row }) => {
            const attendance = row.original.attendance;
            if (Array.isArray(attendance) && attendance.length > 0) {
                return h(
                    "div",
                    attendance.map((record) => {
                        return h(
                            "div",
                            { key: record.course?.id || record.date },
                            [
                                h(
                                    "span",
                                    {
                                        class:
                                            record.status === "present"
                                                ? "text-green-500"
                                                : "text-red-500",
                                    },
                                    record.status,
                                ),
                            ],
                        );
                    }),
                );
            } else {
                return h("div", "No attendance");
            }
        },
    },
    {
        accessorKey: "course",
        header: "Course",
        cell: ({ row }) => {
            const attendance = row.original.attendance;
            if (Array.isArray(attendance) && attendance.length > 0) {
                return h(
                    "div",
                    attendance.map((record) => {
                        return h(
                            "div",
                            { key: record.course?.id || record.date },
                            [record.course ? record.course.name : "No course"],
                        );
                    }),
                );
            } else {
                return h("div", "No course");
            }
        },
    },
    {
        id: "actions",
        enableHiding: false,
        cell: ({ row }) => {
            const student = row.original;
            return h(
                "div",
                { class: "relative" },
                h(DataTableDropDown, { student }),
            );
        },
    },
];
