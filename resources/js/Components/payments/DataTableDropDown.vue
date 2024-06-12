<script setup lang="ts">
import { MoreHorizontal } from "lucide-vue-next";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/Components/ui/button";

const props = defineProps<{
    student: {
        id: string;
        name: string;
        gender: string;
    };
}>();


const emit = defineEmits(["edit"]);

function copy(id: string) {
    navigator.clipboard.writeText(id);
}

function onEdit() {
    emit("edit", props.student);
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="w-8 h-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="w-4 h-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Actions</DropdownMenuLabel>
            <DropdownMenuItem @click="copy(student.id)">Copy student ID</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="onEdit">Edit student</DropdownMenuItem>
            <DropdownMenuSeparator />
        </DropdownMenuContent>
    </DropdownMenu>
</template>

