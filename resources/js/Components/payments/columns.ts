import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";
import { Button } from "../ui/button";
import { Checkbox } from "../ui/checkbox";
import { router } from "@inertiajs/vue3";
import type { Major } from "../major/columns";
import { toast } from "vue-sonner";

interface Attendance {
    id: number;
    attended: "present" | "absent";
    date: string | null;
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
    courseAttendance: {
        [courseId: number]: {
            course_name: string;
            total_attendance: number;
        };
    };
    major_id: number;
    faculty_id: number;
    major: Major;
    classroom_id: number;
    year_id: number;
    semester_id: number;
    classrooms_count: number | null;
    attendance_count: number | null;
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
        cell: ({ row }) => row.index + 1,
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
                console.log(major.name);
                return major.name;
            } else {
                console.log("No major assigned");
                return "No major assigned";
            }
        },
    },
    {
        accessorKey: "faculty_id",
        header: "Faculty",
        cell: ({ row }) => {
            const major = row.original.major;
            if (major && major.faculty) {
                const facultyName = major.faculty.name;
                console.log("Faculty", facultyName);
                return facultyName;
            } else {
                console.log("No Faculty Assigned");
                return "No Faculty Assigned";
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
                return "No year set yet";
            }
        },
    },
    {
        accessorKey: "attendance_count",
        header: "Attendance Count",
        cell: ({ row }) => {
            const courseAttendance = row.original.courseAttendance;
            if (courseAttendance) {
                return h("div",
                    Object.values(courseAttendance).map(({ course_name, total_attendance }) =>
                        h("div", { key: course_name }, `${course_name}: ${total_attendance}`)
                    )
                );
            } else {
                return "No attendance yet";
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
                return "No semester set yet";
            }
        },
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
                                "\u2022 ",
                            ),
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
                            h("span", "\u2022 "),
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
    {
        id: "actions",
        header: "Actions",
        cell: ({ row }) =>
            h("div", { class: "flex space-x-2" }, [
                h(
                    Button,
                    {
                        variant: "destructive",
                        onClick: () =>
                            handleDelete(
                                row.original.pivot.student_id,
                                row.original.pivot.classroom_id,
                            ),
                    },
                    "Delete",
                ),
                h(
                    Button,
                    {
                        variant: "default",
                        onClick: () =>
                            handleEdit(
                                row.original.pivot.student_id,
                                row.original.pivot.classroom_id,
                            ),
                    },
                    "Edit",
                ),
            ]),
    },
];

function handleDelete(studentId: string, classroomId: number) {
    console.log(
        `Delete student with ID: ${studentId} from classroom ID: ${classroomId}`,
    );
    router.delete(`/classrooms/${classroomId}/students/${studentId}`, {
        onSuccess: () => {
            return toast.success("Deleted successfully");
        },
        onError: () => {
            console.log("Error");
        },
    });
}

export function handleBulkDelete(selectedIds: number[], classroom_id: number) {
    console.log(`Delete majors with IDs: ${selectedIds}`);

    const reloadAfterDelay = () => {
        setTimeout(() => {
            location.reload();
        }, 2000);
    };

    router.delete(`/studentsClassroom`, {
        data: { ids: selectedIds, classroom_id: classroom_id },
        onSuccess: () => {
            toast.success("Students Deleted", {
                description: "Selected students have been deleted successfully",
            });
            reloadAfterDelay();
        },
        onError: () => {
            toast.error("Error deleting Students");
        },
    });
}

function handleEdit(student_id: string, classroom_id: number) {
    console.log("Classroom ID:", classroom_id, student_id);
    router.visit(`/student/${student_id}/edit/${classroom_id}`);
}

