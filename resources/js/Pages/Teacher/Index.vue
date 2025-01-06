<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { Teacher } from "@/Components/teachers/columns";
import { onMounted } from "vue";
import { columns } from "@/Components/teachers/columns";
import { ref } from "vue";
import DataTable from "@/Components/teachers/DataTable.vue";
import { Button } from "@/Components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/Components/ui/dialog";
import { Input } from "@/Components/ui/input";
import Label from "@/Components/ui/label/Label.vue";
import { reactive } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { toast } from "vue-sonner";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
import { Classrooms } from "../Classroom/Index.vue";

import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
const props = defineProps<{
    teachers: Teacher[];
    classrooms: Classrooms[];
    breadcrumbs: BreadcrumbType[];
}>();

const teacherForm = reactive({
    name: "",
    classroom_id: null,
});
const data = ref<Teacher[]>([]);
const createTeacher = () => {
    router.post("/teachers", teacherForm, {
        onSuccess: () => {
            toast.success("Teacher added", {
                description: "The teacher has been added succesfully",
            });
            fetchTeachers();
        },
        onError: () => {
            const error = usePage().props.errors.name;
            toast.error("Error adding teacher", {
                description:
                    error || "The teacher has not been added succesfully",
            });
        },
    });
};

const fetchTeachers = async () => {
    try {
        const response = await router.get("/teacher");
        data.value = response.data;
    } catch (error) {
        console.error("Error fetching teacher:", error);
    }
};
onMounted(() => {
    data.value = props.teachers;
});
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="breadcrumbs">
        <h1 class="text-6xl font-extrabold text-primary-accent">
            Teacher List
        </h1>
        <Dialog>
            <div>
                <DialogTrigger as-child>
                    <Button variant="secondary" class="sm:p-2 sm:text-sm">
                        Add Teacher
                    </Button>
                </DialogTrigger>
            </div>
            <!-- Rest of the dialog content remains unchanged -->
            <DialogContent class="sm:max-w-[425px]">
                <form @submit.prevent="createTeacher">
                    <DialogHeader>
                        <DialogTitle>Add Teacher</DialogTitle>
                        <DialogDescription>
                            Create a new Teacher. Click save when you're done
                        </DialogDescription>
                    </DialogHeader>
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">
                                Teacher Name
                            </Label>
                            <Input
                                id="name"
                                placeholder="John Doe..."
                                class="col-span-3"
                                v-model="teacherForm.name"
                            />
                        </div>
                    </div>
                    <div class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="name" class="text-right">
                                Classroom
                            </Label>
                            <Select v-model="teacherForm.classroom_id">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue
                                        :placeholder="
                                            'Select a room' +
                                            (props.classrooms.length
                                                ? ''
                                                : ' (No classrooms available)')
                                        "
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Classrooms </SelectLabel>
                                        <SelectItem
                                            v-for="classroom in props.classrooms"
                                            :key="classroom.id"
                                            :value="classroom.id"
                                        >
                                            {{ classroom.room_number }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <DialogFooter class="flex items-end">
                        <DialogClose class="">
                            <Button type="submit"> Save changes </Button>
                        </DialogClose>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <div class="">
            <DataTable :columns="columns" :data="data" />
        </div>
    </AuthenticatedLayout>
</template>
