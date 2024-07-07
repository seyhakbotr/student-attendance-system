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
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";

import ComboBox from "@/Components/ComboBox.vue";
import { Pencil } from "lucide-vue-next";
import { Faculty } from "@/Components/faculty/columns";
import { computed } from "vue";
interface Form {
    major_id: number | null;
    room_number: string;
    faculty_id: number | null;
    year_id: number;
    shift: string | null;
    semester_id: number;
}

const props = defineProps<{
    classroom: Classrooms;
    form: Form;
    majors: Major[];
    faculties: Faculty[];
    editClassroom: (classroom: Classrooms) => void;
    updateClassroom: (classroomId: number) => void;
}>();

function handleFacultySelect(id: number) {
    props.form.faculty_id = id;
    props.form.major_id = null;
}
function handleMajorSelect(id: number) {
    props.form.major_id = id;
}
const yearOptions = [
    { id: 1, label: "Year 1" },
    { id: 2, label: "Year 2" },
    { id: 3, label: "Year 3" },
    { id: 4, label: "Year 4" },
];

const filteredMajors = computed(() => {
    if (props.form.faculty_id === null) {
        return [];
    }
    return props.majors.filter(
        (major) => major.faculty_id === props.form.faculty_id,
    );
});
const shiftOptions = [
    { id: "Morning", label: "Morning" },
    { id: "Afternoon", label: "Afternoon" },
    { id: "Evening", label: "Evening" },
];

const semesterOptions = [
    { id: 1, label: "Semester 1" },
    { id: 2, label: "Semester 2" },
];
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
                        <Label for="faculty" class="text-left py-2">
                            Faculty
                        </Label>
                        <ComboBox
                            :items="props.faculties"
                            label="faculty"
                            class="col-span-3"
                            :selectedId="form.faculty_id"
                            @update:selectedItemId="handleFacultySelect"
                        />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="major" class="text-left py-2">
                            Major
                        </Label>
                        <ComboBox
                            :items="filteredMajors"
                            label="Major"
                            class="col-span-3"
                            :selectedId="form.major_id"
                            @update:selectedItemId="handleMajorSelect"
                        />
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="shift" class="font-medium">Shift</label>
                        <div class="flex flex-wrap items-center space-x-4">
                            <RadioGroup
                                v-model="form.shift"
                                class="flex space-x-4"
                            >
                                <div
                                    v-for="option in shiftOptions"
                                    :key="option.id"
                                    class="flex items-center space-x-2"
                                >
                                    <RadioGroupItem
                                        :id="'Shift' + option.id"
                                        :value="option.id"
                                    />
                                    <label
                                        :for="'Shift' + option.id"
                                        class=""
                                        >{{ option.label }}</label
                                    >
                                </div>
                            </RadioGroup>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="year" class="font-medium">Year</label>
                        <div class="flex flex-wrap items-center space-x-4">
                            <RadioGroup
                                v-model="form.year_id"
                                class="flex space-x-4"
                            >
                                <div
                                    v-for="option in yearOptions"
                                    :key="option.id"
                                    class="flex items-center space-x-2"
                                >
                                    <RadioGroupItem
                                        :id="'year' + option.id"
                                        :value="option.id"
                                    />
                                    <label :for="'year' + option.id" class="">{{
                                        option.label
                                    }}</label>
                                </div>
                            </RadioGroup>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="semester" class="font-medium"
                            >Semester</label
                        >
                        <div class="flex flex-wrap items-center space-x-4">
                            <RadioGroup
                                v-model="form.semester_id"
                                class="flex space-x-4"
                            >
                                <div
                                    v-for="option in semesterOptions"
                                    :key="option.id"
                                    class="flex items-center space-x-2"
                                >
                                    <RadioGroupItem
                                        :id="'semester' + option.id"
                                        :value="option.id"
                                    />
                                    <label
                                        :for="'semester' + option.id"
                                        class=""
                                        >{{ option.label }}</label
                                    >
                                </div>
                            </RadioGroup>
                        </div>
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
