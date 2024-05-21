<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from "vue";
import { columns } from "@/Components/payments/columns";
import type { Student } from "@/Components/payments/columns";
import DataTable from "@/Components/payments/data-table.vue";
import type { Classrooms } from "./Index.vue";
import { Link, router } from "@inertiajs/vue3";
import Button from "@/Components/ui/button/Button.vue";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";
import { Input } from "@/Components/ui/input";
import { reactive } from "vue";

import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
const props = defineProps<{
    classroom: Classrooms;
    students: Student[];
    courses: Course[];
    attendances: Object;
    breadcrumbs: BreadcrumbType[];
}>();
export interface Course {
    classroom_id: number;
    id: number;
    name: string;
}
interface StudentData extends Omit<Student, 'id' | 'qr_code_image_path' | 'attendance'> {
    classroom_id: number;
}

const data = ref<Student[]>(props.students);
const form = reactive<StudentData>({
    name: "",
    gender: "male",
    classroom_id: props.classroom.id
});
const createStudent = () => {
    router.post("/student", form);
};
onMounted(() => {
    data.value = props.students;
});

</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col sm:flex-row items-center justify-between p-4 rounded-lg">
            <div class="order-2 sm:order-1">
                <p class="text-2xl sm:text-5xl text-green-500 font-extrabold">
                    {{ classroom.name }}
                </p>
                <Dialog>
                    <DialogTrigger as-child>
                        <Button variant="outline" class="mt-4 md:mt-0">
                            Add Student
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-[425px]">
                        <form @submit.prevent="createStudent">
                            <DialogHeader>
                                <DialogTitle>Add Student</DialogTitle>
                                <DialogDescription>
                                    Create a new student. Click save when you're
                                    done
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="name" class="text-right">
                                        Student Name
                                    </Label>
                                    <Input id="name" placeholder="John doe.." class="col-span-3" v-model="form.name" />
                                </div>
                                <div class="grid grid-cols-4 items-center gap-4">
                                    <Label for="gender" class="text-right">
                                        Student Gender
                                    </Label>
                                    <Select v-model="form.gender">
                                        <SelectTrigger class="w-[180px]">
                                            <SelectValue placeholder="Select student gender" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="male">Male</SelectItem>
                                                <SelectItem value="female">Female</SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <DialogFooter class="flex items-end">
                                <DialogClose class="">
                                    <Button type="submit">
                                        Save changes
                                    </Button>
                                </DialogClose>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                <p v-if="classroom.room_number" class="text-sm sm:text-lg text-gray-400 mt-2 sm:mt-0">
                    Room Number: {{ classroom.room_number }}
                </p>
                <p v-else class="text-sm sm:text-lg text-gray-400 mt-2 sm:mt-0">
                    No designated room number yet
                </p>
            </div>
            <div class="order-1 sm:order-2"></div>
        </div>

        <div class="container py-2 mx-auto">
            <DataTable :columns="columns" :data="data" :classroom-id="classroom.id" />
        </div>
    </AuthenticatedLayout>
</template>
