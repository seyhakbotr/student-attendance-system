<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { toast } from "vue-sonner";
import Button from "@/Components/ui/button/Button.vue";
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import Input from "@/Components/ui/input/Input.vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";
import { Classrooms } from "./Index.vue";

import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
export interface Teacher {
    id: number;
    name: string;
}

const props = defineProps<{
    teachers: Teacher[];

    classroom: Classrooms;
    breadcrumbs: BreadcrumbType[];
}>();

const editedTeacher = ref<Teacher | null>(null);

const deleteTeacher = (id: number) => {
    router.delete(`/teacher/${id}`, {
        onSuccess: () => {
            toast.success("Teacher deleted", {
                description: "Teacher has been deleted successfully",
            });
            console.log("Teacher deleted successfully");
        },
        onError: (errors) => {
            toast.error("Error!", {
                description: "Error deleting teacher",
            });

            console.error("Failed to delete teacher", errors);
        },
    });
};

const openEditDialog = (teacher: Teacher) => {
    // Clone the teacher object to prevent direct mutations
    editedTeacher.value = { ...teacher };
};

const saveChanges = (teacherId: number) => {
    // Perform save operation, e.g., send editedTeacher.value to server
    if (editedTeacher.value) {
        router.patch(`/teacher/${teacherId}`, editedTeacher.value, {
            onSuccess: () => {
                toast.success("Teacher Updated", {
                    description: "Teacher has been updated successfully",
                });
            },
            onError: () => {
                toast.error("Error", {
                    description: "Teacher updated unsuccessfully",
                });
            },
        });
    }
};

const editedTeacherName = computed({
    get: () => editedTeacher.value?.name ?? '',
    set: (value: string) => {
        if (editedTeacher.value) {
            editedTeacher.value.name = value;
        }
    },
});
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div>
            <h1>Teachers for Classroom {{classroom.major.name}}</h1>
            <h2>Room Number: {{classroom.room_number}}</h2>
            <Table>
                <TableCaption>List of teachers</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead>Teacher ID</TableHead>
                        <TableHead>Teacher Name</TableHead>
                        <TableHead>Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="teacher in teachers" :key="teacher.id">
                        <TableCell>{{ teacher.id }}</TableCell>
                        <TableCell>{{ teacher.name }}</TableCell>
                        <div>
                            <TableCell>
                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="default" @click="openEditDialog(teacher)">
                                            Edit
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-[425px]">
                                        <DialogHeader>
                                            <DialogTitle>Edit teacher</DialogTitle>
                                            <DialogDescription>
                                                Make changes to the teacher here.
                                                Click save when you're done.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <div class="grid gap-4 py-4">
                                            <div class="grid grid-cols-4 items-center gap-4">
                                                <Label for="teacherName" class="text-right">
                                                    Teacher Name
                                                </Label>
                                                <!-- Bind input field to teacher.name -->
                                                <Input id="name" class="col-span-3" v-model="editedTeacherName" />
                                            </div>
                                        </div>
                                        <DialogFooter>
                                            <DialogClose>
                                                <Button type="button" @click="saveChanges(teacher.id)">
                                                    Save changes
                                                </Button>
                                            </DialogClose>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </TableCell>
                            <TableCell>
                                <Button variant="destructive" @click.prevent="deleteTeacher(teacher.id)">Delete</Button>
                            </TableCell>
                        </div>
                    </TableRow>
                </TableBody>
            </Table>
            <div v-if="$page.props.errors.error" class="error-message">
                <p>{{ $page.props.errors.error }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

