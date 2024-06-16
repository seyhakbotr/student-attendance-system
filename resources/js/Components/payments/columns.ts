import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import { Button } from "../ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { router } from "@inertiajs/vue3";
import type { Major } from "../major/columns";

interface Attendance {
    id: number;
    attended: "present" | "absent";
    date: string | null; // Optional date
    course: {
        id: number;
        name: string;
    };
}

export interface Student {
    id: string;
    name: string;
    gender: "male" | "female";
    qr_code_image_path: string;
    attendances: Attendance[];
    course: {
        id: number;
        name: string;
    };
    major_id: number;
    major: Major;
    classroom_id: number;
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
                () => ["Name"],
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
                class: "w-[80px] h-[80px]",
            }),
    },
    {
        accessorKey: "attendances",
        header: "Attendance",
        cell: ({ row }) => {
            const attendances = row.original.attendances;
            if (Array.isArray(attendances) && attendances.length > 0) {
                return h(
                    "div",
                    attendances.map((attendance) => {
                        return h("div", { key: attendance.id }, [
                            h(
                                "span",
                                {
                                    class:
                                        attendance.attended === "present"
                                            ? "text-green-500"
                                            : "text-red-500",
                                },
                                attendance.attended,
                            ),
                            // Optionally display date if available
                        ]);
                    }),
                );
            } else {
                return h("div", "No attendance");
            }
        },
    },
    {
        accessorKey: "course",
        header: "Courses",
        cell: ({ row }) => {
            const attendances = row.original.attendances;
            if (Array.isArray(attendances) && attendances.length > 0) {
                return h(
                    "div",
                    attendances.map((attendance) => {
                        return h("div", { key: attendance.id }, [
                            h(
                                "span",
                                attendance.course
                                    ? attendance.course.name
                                    : "No Course",
                            ),
                        ]);
                    }),
                );
            } else {
                return h("div", "No course");
            }
        },
    },
    //},
    //{
    //    accessorKey: "courses",
    //    header: "Courses",
    //    cell: ({ row }) => {
    //        const courses = row.original.attendances.map(att => att.course.name);
    //        if (Array.isArray(courses) && courses.length > 0) {
    //            return h(
    //                "div",
    //                courses.map((course, index) => {
    //                    return h(
    //                        "div",
    //                        { key: index },
    //                        [course],
    //                    );
    //                }),
    //            );
    //        } else {
    //            return h("div", "No courses");
    //        }
    //    },
    //},
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
                        onClick: () =>
                            handleEdit(
                                row.original.id,
                                row.original.pivot.classroom_id,
                            ),
                    },
                    "Edit",
                ),
            ]),
    },
];

function handleDelete(id: string) {
    console.log(`Delete student with ID: ${id}`);
    router.delete(`/student/${id}`, {
        onSuccess: () => {
            return Promise.all([location.reload()]);
        },
        onError: () => {
            console.log("Error");
        },
    });
}

function handleEdit(studentId: string, classroomId: number) {
    console.log("Classroom ID:", classroomId); // Check the type and value of classroomId
    router.visit(`/student/${studentId}/edit/${classroomId}`); // Use classroomId instead of classroom
}
