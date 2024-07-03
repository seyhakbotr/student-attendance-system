<script setup lang="ts">
        import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/components/ui/dialog";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

import Button from "@/Components/ui/button/Button.vue";
import Label from "@/Components/ui/label/Label.vue";
import Input from "@/Components/ui/input/Input.vue";
interface CourseData {
    teacher_id: string;
    name: string;
}

interface Teacher {
    id: string;
    name: string;
}

// Define props for the component
defineProps<{
    courseData: CourseData;
    teachers: Teacher[];
    selectedTeacherName: string;
    createCourse: () => void;
}>();
</script>
<template>
    <Dialog>
        <div>
            <DialogTrigger as-child>
                <Button variant="secondary" class="sm:p-2 sm:text-sm"
                    >Add Course</Button
                >
            </DialogTrigger>
        </div>
        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="createCourse">
                <DialogHeader>
                    <DialogTitle>Add Course</DialogTitle>
                    <DialogDescription>
                        Create a new course. Click save when you're done.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="teacher" class="text-right">Teacher</Label>

                        <Select v-model="courseData.teacher_id">
                            <SelectTrigger class="w-[180px]">
                                <SelectValue>
                                    <span v-if="!courseData.teacher_id"
                                        >Select a teacher</span
                                    >
                                    <span v-else>{{
                                        selectedTeacherName
                                    }}</span>
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <template
                                        v-for="teacher in teachers"
                                        :key="teacher.id"
                                    >
                                        <SelectItem :value="teacher.id">
                                            {{ teacher.name }}
                                        </SelectItem>
                                    </template>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="name" class="text-right">Course Name</Label>
                        <Input
                            id="name"
                            placeholder="Course Name..."
                            class="col-span-3"
                            v-model="courseData.name"
                        />
                    </div>
                </div>

                <DialogFooter class="flex items-end">
                    <DialogClose>
                        <Button type="submit">Save changes</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
