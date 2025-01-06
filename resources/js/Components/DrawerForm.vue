<template>
  <UseTemplate>
     <form @submit.prevent="submitForm" class="grid items-start gap-4 px-4">
      <div class="grid gap-2">
        <Label html-for="name">Name</Label>
        <Input v-model="formData.name" id="name" />
      </div>
      <div class="grid gap-2">
        <Label html-for="roomNumber">Room Number</Label>
        <Input v-model="formData.room_number" id="roomNumber" type="number" />
      </div>
      <Button type="submit">Save changes</Button>
    </form>
  </UseTemplate>

  <Dialog v-if="isDesktop" v-model:open="isOpen">
    <DialogTrigger as-child>
      <Button variant="outline">
        Edit Profile
      </Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Edit profile</DialogTitle>
        <DialogDescription>
          Make changes to your profile here. Click save when you're done.
        </DialogDescription>
      </DialogHeader>
      <GridForm />
    </DialogContent>
  </Dialog>

  <Drawer v-else v-model:open="isOpen">
    <DrawerTrigger as-child>
      <Button variant="outline">
        Edit Profile
      </Button>
    </DrawerTrigger>
    <DrawerContent>
      <form @submit.prevent="submitForm" class="grid items-start gap-4 px-4">
        <div class="grid gap-2">
          <Label html-for="name">Name</Label>
          <Input v-model="formData.name" id="name" />
        </div>
        <div class="grid gap-2">
          <Label html-for="roomNumber">Room Number</Label>
          <Input v-model="formData.room_number" id="roomNumber" type="number" />
        </div>
        <Button type="submit">Save changes</Button>
      </form>
      <DrawerFooter class="pt-2">
        <DrawerClose as-child>
          <Button variant="outline">
            Cancel
          </Button>
        </DrawerClose>
      </DrawerFooter>
    </DrawerContent>
  </Drawer>
</template>

<script setup lang="ts">
import { ref,defineEmits } from 'vue'
import { createReusableTemplate, useMediaQuery } from '@vueuse/core'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/Components/ui/dialog'
import {
  Drawer,
  DrawerClose,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
  DrawerTrigger,
} from '@/Components/ui/drawer'

// Reuse `form` section
const [UseTemplate, GridForm] = createReusableTemplate()
const isDesktop = useMediaQuery('(min-width: 768px)')

const isOpen = ref(false)
const emit = defineEmits(['submit'])
const formData = {
  name: '',
  room_number: 0,
}

const submitForm = () => {
  // Handle form submission
  console.log('Form data:', formData);

  // Emit the submit event to notify the parent component
  emit('submit', formData)
}
</script>


