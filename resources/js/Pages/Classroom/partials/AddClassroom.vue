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

import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import Button from "@/Components/ui/button/Button.vue";
import Label from "@/Components/ui/label/Label.vue";
import ComboBox from "@/Components/ComboBox.vue";
import Input from "@/Components/ui/input/Input.vue";
import { ref, computed } from "vue";
interface Faculty {
    id: number;
    name: string;
}

interface Major {
    id: number;
    name: string;
    faculty_id: number;
}

interface ClassroomForm {
    faculty_id: number | null;
    major_id: number | null;
    room_number: string;
    shift: string | null;
    year_id: number | null;
    semester_id: number | null;
}

const props = defineProps<{
    faculties: Faculty[];
    majors: Major[];
    storeClassroom: () => void;
    classroomForm: ClassroomForm;
}>();

const classroomForm = ref<ClassroomForm>(props.classroomForm);

function handleFacultySelect(id: number) {
    classroomForm.value.faculty_id = id;
    classroomForm.value.major_id = null; // Reset major selection when faculty changes
}

function handleMajorSelect(id: number) {
    classroomForm.value.major_id = id;
}

const filteredMajors = computed(() => {
    if (classroomForm.value.faculty_id === null) {
        return [];
    }
    return props.majors.filter(
        (major) => major.faculty_id === classroomForm.value.faculty_id,
    );
});
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
                        <Label for="faculty" class="text-right">
                            Faculty
                        </Label>
                        <ComboBox
                            :items="props.faculties"
                            label="Faculty"
                            class="col-span-3"
                            v-model="classroomForm.faculty_id"
                            @update:selectedItemId="handleFacultySelect"
                        />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="major" class="text-right"> Major </Label>
                        <ComboBox
                            :items="filteredMajors"
                            label="Major"
                            class="col-span-3"
                            v-model="classroomForm.major_id"
                            @update:selectedItemId="handleMajorSelect"
                        />
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="Shift" class="text-right"> Shift </Label>
                        <div class="flex items-center space-x-4">
                            <RadioGroup
                                class="flex flex-row"
                                v-model="classroomForm.shift"
                                default-value="Morning"
                            >
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="morning" value="Morning" />
                                    <Label for="morning">Morning</Label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="afternoon" value="Afternoon" />
                                    <Label for="afternoon">Afternoon</Label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <RadioGroupItem id="evening" value="Evening" />
                                    <Label for="evening">Evening</Label>
                                </div>
                            </RadioGroup>
                        </div>
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
                            Room Number
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
