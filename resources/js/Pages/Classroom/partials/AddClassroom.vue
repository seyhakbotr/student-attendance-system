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

import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import Button from "@/Components/ui/button/Button.vue";
import Label from "@/Components/ui/label/Label.vue";
import Input from "@/Components/ui/input/Input.vue";
interface Faculty {
    id: number;
    name: string;
}

interface Major {
    id: number;
    name: string;
}
interface ClassroomForm {
    faculty_id: number | null;
    major_id: number | null;
    room_number: string;
    year_id: number | null;
    semester_id: number | null;
}
const props = defineProps<{
    faculties: Faculty[];
    majors: Major[];
    storeClassroom: () => void;
    classroomForm: ClassroomForm;
}>();
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button variant="outline" class="mt-4 md:mt-0">
                Add Classroom
            </Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[425px]">
            <form @submit.prevent="storeClassroom">
                <DialogHeader>
                    <DialogTitle>Add Classroom</DialogTitle>
                    <DialogDescription>
                        Create a new classroom. Click save when you're done.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="faculty_id" class="text-right">
                            Faculty Name
                        </Label>
                        <Select
                            v-model="classroomForm.faculty_id"
                            class="max-w-10"
                        >
                            <SelectTrigger class="w-max">
                                <SelectValue placeholder="Select a faculty" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup
                                    v-for="faculty in props.faculties"
                                    :key="faculty.id"
                                >
                                    <SelectItem :value="faculty.id">{{
                                        faculty.name
                                    }}</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="major_id" class="text-right">
                            Major Name
                        </Label>
                        <Select
                            v-model="classroomForm.major_id"
                            class="max-w-10"
                        >
                            <SelectTrigger class="w-max">
                                <SelectValue placeholder="Select a major" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup
                                    v-for="major in props.majors"
                                    :key="major.id"
                                >
                                    <SelectItem :value="major.id">{{
                                        major.name
                                    }}</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="year" class="text-right"> Year </Label>
                        <div class="flex items-center space-x-4">
                            <RadioGroup
                                class="flex flex-row"
                                v-model="classroomForm.year_id"
                                default-value="Year 1"
                            >
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="year1" value="1" />
                                    <Label for="year1">Year 1</Label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="year2" value="2" />
                                    <Label for="year2">Year 2</Label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="year3" value="3" />
                                    <Label for="year3">Year 3</Label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="year4" value="4" />
                                    <Label for="year4">Year 4</Label>
                                </div>
                            </RadioGroup>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="semester" class="text-right">
                            Semester
                        </Label>
                        <div class="flex items-center space-x-4">
                            <RadioGroup
                                class="flex flex-row"
                                v-model="classroomForm.semester_id"
                                default-value="1"
                            >
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="semester1" value="1" />
                                    <Label for="year1">Semester 1</Label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="semester2" value="2" />
                                    <Label for="year2">Semester 2</Label>
                                </div>
                            </RadioGroup>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="room_number" class="text-right">
                            Room Number (Optional)
                        </Label>
                        <Input
                            id="room_number"
                            placeholder="123.."
                            class="col-span-3"
                            v-model="classroomForm.room_number"
                        />
                    </div>
                </div>

                <DialogFooter>
                    <DialogClose>
                        <Button type="submit">Save changes</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
