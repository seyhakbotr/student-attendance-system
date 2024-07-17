import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import { Button } from "../ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { router } from "@inertiajs/vue3";
import type { Major } from "../major/columns";
import { toast } from "vue-sonner";
import type { Student } from "../payments/columns";
import axios from "axios";

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
        accessorKey: "major",
        header: "Major",
        cell: ({ row }) => {
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
        accessorKey: "faculty_id",
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: "ghost",
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === "asc"),
                },
                () => ["Faculty"],
            );
        },
        cell: ({ row }) => {
            const major = row.original.major;
            if (major && major.faculty) {
                const facultyName = major.faculty.name;
                return facultyName; // Return the faculty name directly
            } else {
                console.log("No Faculty Assigned");
                return "No Faculty Assigned"; // Display a default message if faculty or major is null
            }
        },
    },
    {
        accessorKey: "year_id",
        header: "Year",
        cell: ({ row }) => {
            const year = row.original.year_id;
            if (year !== null && year !== undefined) {
                return "Year " + year;
            } else {
                return "No year setted yet";
            }
        },
    },
    {
        accessorKey: "semester_id",
        header: "Semester",
        cell: ({ row }) => {
            const semester = row.original.semester_id;
            if (semester !== null && semester !== undefined) {
                return "Semester " + semester;
            } else {
                return "No semester setted yet";
            }
        },
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

function handleDelete(studentId: number) {
    router.delete(`/student/${studentId}`, {
        onSuccess: () => {
            toast.success("Student Delete");
        },
        onError: () => {
            console.log("Error");
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

    router.delete(`/students`, {
        data: { ids: selectedIds },
        onSuccess: () => {
            toast.success("Students Deleted", {
                description: "Selected students have been deleted successfully",
            });
            // Call the reload function after success
            reloadAfterDelay();
        },
        onError: () => {
            toast.error("Error deleting Students");
        },
    });
}

export async function handleBulkInsert(
    studentIds: number[],
    classroomId: number,
) {
    console.log(
        `Bulk inserting students with IDs: ${studentIds} into classroom ${classroomId}`,
    );

    try {
        const response = await axios.post<{ message?: string }>(
            `/students/bulkInsert`,
            {
                classroom_id: classroomId,
                student_ids: studentIds,
            },
        );

        if (response.data.message) {
            toast.success("Students added", {
                description: response.data.message,
            });
        }
    } catch (error: any) {
        if (error.response) {
            if (error.response.data && error.response.data.error) {
                toast.error("Error adding students:", {
                    description: error.response.data.error,
                });
            } else {
                toast.error("Error adding students: ", {
                    description: error.response.statusText,
                });
            }
        } else if (error.request) {
            toast.error("Error adding students:", {
                description: "No response received from server",
            });
        } else {
            toast.error("Error adding students: " + error.message);
        }
    }
}

function handleEdit(student_id: number) {
    console.log("Student ID:", student_id); // Check the type and value of classroomId
    router.visit(`/student/${student_id}/edit/`); // Use classroomId instead of classroom
}
