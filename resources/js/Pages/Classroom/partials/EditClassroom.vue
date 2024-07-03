<script setup lang="ts">
import { defineProps } from "vue";
import { Major } from "@/Components/major/columns";
import { Classrooms } from "@/Pages/Classroom/Index.vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import Button from "@/Components/ui/button/Button.vue";
import Label from "@/Components/ui/label/Label.vue";
import Input from "@/Components/ui/input/Input.vue";

import { Pencil } from "lucide-vue-next";
interface Form {
    major_id: string;
    room_number: string;
    faculty_id: string;
    year_id: number;
    semester_id: number;
}

const props = defineProps<{
    classroom: Classrooms;
    form: Form;
    majors: Major[];
    editClassroom: (classroom: Classrooms) => void;
    updateClassroom: (classroomId: number) => void;
}>();
</script>

<template>
    <Dialog>
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger as-child>
                    <DialogTrigger @click="editClassroom(classroom)">
                        <Pencil />
                    </DialogTrigger>
                </TooltipTrigger>
                <TooltipContent>
                    <p>Edit Classroom</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Edit Classroom</DialogTitle>
                <DialogDescription>
                    Make changes to your Classroom here. Click save when you're
                    done.
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="updateClassroom(classroom.id)">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="editMajor" class="text-right">Major</Label>
                        <Select
                            class="max-w-10"
                            id="editMajor"
                            v-model="form.major_id"
                        >
                            <SelectTrigger class="text-lg w-max">
                                <SelectValue
                                    placeholder="Select a major"
                                    class=""
                                />
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
                        <Label for="editYear" class="text-right">Year</Label>
                        <Select
                            class="max-w-10"
                            id="editYear"
                            v-model="form.year_id"
                        >
                            <SelectTrigger class="text-lg w-max">
                                <SelectValue
                                    placeholder="Select a year"
                                    class=""
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Years</SelectLabel>
                                    <SelectItem value="1"> Year 1 </SelectItem>
                                    <SelectItem value="2"> Year 2 </SelectItem>
                                    <SelectItem value="3"> Year 3 </SelectItem>
                                    <SelectItem value="4"> Year 4 </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="editSemester" class="text-right"
                            >Semester</Label
                        >
                        <Select
                            class="max-w-10"
                            id="editSemester"
                            v-model="form.semester_id"
                        >
                            <SelectTrigger class="text-lg w-max">
                                <SelectValue
                                    placeholder="Select a semester"
                                    class=""
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Years</SelectLabel>
                                    <SelectItem value="1">
                                        Semester 1
                                    </SelectItem>
                                    <SelectItem value="2">
                                        Semester 2
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="editRoom" class="text-right"
                            >Room Number</Label
                        >
                        <Input
                            id="editRoom"
                            class="col-span-3"
                            v-model="form.room_number"
                        />
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="submit" variant="default"
                        >Save changes</Button
                    >
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
