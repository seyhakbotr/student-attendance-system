<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from "@/Components/ui/dialog";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";

import Button from "@/Components/ui/button/Button.vue";
import Label from "@/Components/ui/label/Label.vue";
import Input from "@/Components/ui/input/Input.vue";
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";
interface Teacher {
    id: string;
    name: string;
}

interface Course {
    id: string;
    name: string;
}
interface Schedule {
    id: string;
    teacher: Teacher | null;
    course: Course | null;
    day: string;
    time_in: string | null;
    time_out: string | null;
}

interface ScheduleData {
    teacher_id: string;
    course_id: string;
    day: string;
    time_in: string | null;
    time_out: string | null;
}

const props = defineProps<{
    schedules: Schedule[];
    teachers: Teacher[];
    courses: Course[];
    scheduleData: ScheduleData;
    selectedTeacherNameEdit: string;
    selectedCourseName: string;
    weekdays: string[];
    isVisible: boolean;
    toggleVisibility: () => void;
    deleteSchedule: (scheduleId: number) => void;
    populateScheduleData: (scheduleId: number) => void;
    editSchedule: (scheduleId: number) => void;
}>();
</script>
<template>
    <Button @click="toggleVisibility">{{
        isVisible ? "Hide Schedule" : "Show Schedule"
    }}</Button>
    <div v-if="isVisible" class="flex justify-center w-full">
        <Table>
            <TableCaption>Schedule</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Teacher</TableHead>
                    <TableHead>Course</TableHead>
                    <TableHead>Day</TableHead>
                    <TableHead>Time in</TableHead>
                    <TableHead>Time Out</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="schedule in schedules" :key="schedule.id">
                    <TableCell>{{ schedule.id }}</TableCell>

                    <TableCell v-if="schedule.teacher">{{
                        schedule.teacher.name
                    }}</TableCell>
                    <TableCell v-else>N/A</TableCell>

                    <TableCell v-if="schedule.course">{{
                        schedule.course.name
                    }}</TableCell>
                    <TableCell v-else>N/A</TableCell>

                    <TableCell>{{ schedule.day }}</TableCell>
                    <TableCell>{{ schedule.time_in }}</TableCell>

                    <TableCell>{{ schedule.time_out }}</TableCell>

                    <TableCell class="flex gap-2">
                        <Button
                            variant="destructive"
                            @click="deleteSchedule(schedule.id)"
                            >Delete</Button
                        >
                        <Dialog>
                            <div>
                                <DialogTrigger as-child>
                                    <Button
                                        variant="secondary"
                                        @click="
                                            populateScheduleData(schedule.id)
                                        "
                                    >
                                        Edit Schedule
                                    </Button>
                                </DialogTrigger>
                            </div>
                            <DialogContent class="sm:max-w-[425px]">
                                <form
                                    @submit.prevent="editSchedule(schedule.id)"
                                >
                                    <DialogHeader>
                                        <DialogTitle>Edit Schedule</DialogTitle>
                                        <DialogDescription>
                                            Edit an existing schedule for a
                                            teacher on a specific day.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <div class="grid gap-4 py-4">
                                        <div
                                            class="grid grid-cols-4 items-center gap-4"
                                        >
                                            <Label
                                                for="teacher"
                                                class="text-right"
                                                >Teacher</Label
                                            >
                                            <Select
                                                v-model="
                                                    scheduleData.teacher_id
                                                "
                                            >
                                                <SelectTrigger
                                                    class="w-[180px]"
                                                >
                                                    <SelectValue>
                                                        <span
                                                            v-if="
                                                                !scheduleData.teacher_id
                                                            "
                                                        >
                                                            Select a teacher
                                                        </span>
                                                        <span v-else>{{
                                                            selectedTeacherNameEdit
                                                        }}</span>
                                                    </SelectValue>
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <template
                                                            v-for="teacher in teachers"
                                                            :key="teacher.id"
                                                        >
                                                            <SelectItem
                                                                :value="
                                                                    teacher.id
                                                                "
                                                            >
                                                                {{
                                                                    teacher.name
                                                                }}
                                                            </SelectItem>
                                                        </template>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div
                                            class="grid grid-cols-4 items-center gap-4"
                                        >
                                            <Label
                                                for="course"
                                                class="text-right"
                                                >Course</Label
                                            >
                                            <Select
                                                v-model="scheduleData.course_id"
                                            >
                                                <SelectTrigger
                                                    class="w-[180px]"
                                                >
                                                    <SelectValue>
                                                        <span
                                                            v-if="
                                                                !scheduleData.course_id
                                                            "
                                                        >
                                                            Select a course
                                                        </span>
                                                        <span v-else>{{
                                                            selectedCourseName
                                                        }}</span>
                                                    </SelectValue>
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <template
                                                            v-for="course in courses"
                                                            :key="course.id"
                                                        >
                                                            <SelectItem
                                                                :value="
                                                                    course.id
                                                                "
                                                            >
                                                                {{
                                                                    course.name
                                                                }}
                                                            </SelectItem>
                                                        </template>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div
                                            class="grid grid-cols-4 items-center gap-4"
                                        >
                                            <Label for="day" class="text-right"
                                                >Day</Label
                                            >
                                            <Select v-model="scheduleData.day">
                                                <SelectTrigger
                                                    class="w-[180px]"
                                                >
                                                    <SelectValue>
                                                        <span
                                                            v-if="
                                                                !scheduleData.day
                                                            "
                                                        >
                                                            Select a day
                                                        </span>
                                                        <span v-else>{{
                                                            scheduleData.day
                                                        }}</span>
                                                    </SelectValue>
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectItem
                                                            v-for="day in weekdays"
                                                            :key="day"
                                                            :value="day"
                                                        >
                                                            {{ day }}
                                                        </SelectItem>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <div
                                            class="grid grid-cols-4 items-center gap-4"
                                        >
                                            <Label
                                                for="time"
                                                class="block mb-2 text-lg font-medium text-gray-900 dark:text-white"
                                            >
                                                Select time in:
                                            </Label>
                                            <div class="relative col-span-3">
                                                <div
                                                    class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none"
                                                >
                                                    <svg
                                                        class="w-6 h-6 text-gray-500 dark:text-gray-400"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </div>
                                                <Input
                                                    type="time"
                                                    id="time"
                                                    v-model="
                                                        scheduleData.time_in
                                                    "
                                                    required
                                                />
                                            </div>
                                        </div>
                                        <div
                                            class="grid grid-cols-4 items-center gap-4"
                                        >
                                            <Label
                                                for="time"
                                                class="block mb-2 text-lg font-medium text-gray-900 dark:text-white"
                                            >
                                                Select time out:
                                            </Label>
                                            <div class="relative col-span-3">
                                                <div
                                                    class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none"
                                                >
                                                    <svg
                                                        class="w-6 h-6 text-gray-500 dark:text-gray-400"
                                                        aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </div>
                                                <Input
                                                    type="time"
                                                    id="time"
                                                    v-model="
                                                        scheduleData.time_out
                                                    "
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <DialogFooter class="flex items-end">
                                            <Button type="submit"
                                                >Save Schedule</Button
                                            >
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
