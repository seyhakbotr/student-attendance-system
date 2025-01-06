<template>
  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        role="combobox"
        :aria-expanded="open"
        class="w-[200px] justify-between"
      >
        {{
          selectedValue
            ? selectedValue.name
            : `Select a ${label}`
        }}
        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-[200px] p-0">
      <Command>
        <CommandInput class="h-9" :placeholder="`Search ${label}`" />
        <CommandEmpty>No {{ label }} Found</CommandEmpty>
        <CommandList>
          <CommandGroup>
            <CommandItem
              v-for="item in items"
              :key="item.id"
              :value="item.name"
              @select="() => handleSelect(item)"
            >
              {{ item.name }}
              <Check
                :class="cn('ml-auto h-4 w-4', selectedValue && selectedValue.id === item.id ? 'opacity-100' : 'opacity-0')"
              />
            </CommandItem>
          </CommandGroup>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue';
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { Button } from "@/Components/ui/button";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/Components/ui/command";
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { cn } from "@/lib/utils";

const props = defineProps<{
  items: { id: number; name: string }[];
  label: string;
  selectedId: number | null;
}>();

const emit = defineEmits(['update:selectedItemId']);

const open = ref(false);
const selectedValue = ref<{ id: number; name: string } | null>(null);

watch(() => props.selectedId, (newId) => {
  if (newId !== null) {
    const selectedItem = props.items.find(item => item.id === newId);
    if (selectedItem) {
      selectedValue.value = selectedItem;
    }
  }
}, { immediate: true });

function handleSelect(item: { id: number; name: string }) {
  selectedValue.value = item;
  emit('update:selectedItemId', item.id);
  open.value = false;
}
</script>

<style scoped>
/* Add any required styles here */
</style>

