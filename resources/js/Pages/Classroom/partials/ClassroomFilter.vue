<script setup lang="ts">
import { ref, computed } from 'vue';
import type Faculty from "@/Components/faculty/columns.ts";
import type Major from "@/Components/major/columns.ts";
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
// Define the props
const props = defineProps<{
  faculties: Faculty[];
  majors: Major[];
}>();

const searchInput = ref('');
const selectedFaculty = ref<Faculty | null>(null);
const selectedMajor = ref<Major | null>(null);

// Computed property to filter majors based on selected faculty
const filteredMajors = computed(() => {
  if (!selectedFaculty.value) {
    return props.majors;
  }
  return props.majors.filter(major => major.faculty_id === selectedFaculty.value?.id);
});

const updateSelectedFaculty = (faculty: Faculty | null) => {
  selectedFaculty.value = faculty;
  selectedMajor.value = null; // Reset selected major when a new faculty is selected
};
</script>

<template>
  <Input
    v-model="searchInput"
    placeholder="Search a classroom by major name or room number.."
    class="w-full mb-4 md:mb-0 p-2 md:w-5/12 sm:p-4"
  />
  <Select v-model="selectedFaculty" @change="updateSelectedFaculty">
    <SelectTrigger class="w-[180px]">
      <SelectValue>
        <span v-if="!selectedFaculty">Select a faculty</span>
        <span v-else>{{ selectedFaculty.name }}</span>
      </SelectValue>
    </SelectTrigger>
    <SelectContent>
      <SelectGroup>
        <SelectLabel>Faculty</SelectLabel>
        <SelectItem
          v-for="faculty in props.faculties"
          :key="faculty.id"
          :value="faculty"
        >
          {{ faculty.name }}
        </SelectItem>
      </SelectGroup>
    </SelectContent>
  </Select>

  <Select v-model="selectedMajor">
    <SelectTrigger class="w-[180px]">
      <SelectValue>
        <span v-if="!selectedMajor">Select a major</span>
        <span v-else>{{ selectedMajor.name }}</span>
      </SelectValue>
    </SelectTrigger>
    <SelectContent>
      <SelectGroup>
        <SelectLabel>Major</SelectLabel>
        <SelectItem
          v-for="major in filteredMajors"
          :key="major.id"
          :value="major"
        >
          {{ major.name }}
        </SelectItem>
      </SelectGroup>
    </SelectContent>
  </Select>
</template>

