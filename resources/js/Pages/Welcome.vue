<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { Button } from "@/Components/ui/button";
import { useDark, useToggle } from "@vueuse/core";
import { watchOnce } from "@vueuse/core";
import {
    Carousel,
    type CarouselApi,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from "@/Components/ui/carousel";
import { Card, CardContent } from "@/Components/ui/card";
import AnimatedText from "@/Components/AnimatedText.vue";
import placeholderImage from "../../../public/images/ryoshu.png";
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from "@/Components/ui/sheet";
const isDark = useDark();
const toggleDark = useToggle(isDark);
defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
    isDark: boolean;
}>();

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}
const emblaMainApi = ref<CarouselApi>();
const emblaThumbnailApi = ref<CarouselApi>();
const selectedIndex = ref(0);

function onSelect() {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    selectedIndex.value = emblaMainApi.value.selectedScrollSnap();
    emblaThumbnailApi.value.scrollTo(emblaMainApi.value.selectedScrollSnap());
}

function onThumbClick(index: number) {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    emblaMainApi.value.scrollTo(index);
}

watchOnce(emblaMainApi, (emblaMainApi) => {
    if (!emblaMainApi) return;

    onSelect();
    emblaMainApi.on("select", onSelect);
    emblaMainApi.on("reInit", onSelect);
});
const imageUrl =
    "https://via.assets.so/game.png?id=1&q=95&w=360&h=360&fit=fill";
</script>

<template>
    <div>

        <Head title="Welcome" />
        <div class="w-3/5 mx-auto pt-4">
            <nav class="flex justify-between items-center mt-2 lg:col-start-2">
                <h1 class="text-xl font-extrabold lg:text-4xl xl:text-6xl sm:text-lg text-center text-green-400">
                    BELTEI
                </h1>
                <div>
                    <!-- Check if the user is logged in -->
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                        Dashboard
                        </Link>
                    </template>
                    <!-- If the user is not logged in, display login and register links -->
                    <template v-else>
                        <div class="hidden lg:flex">
                            <Link :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Log in
                            </Link>
                            <Link v-if="canRegister" :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Register
                            </Link>
                            <button @click="toggleDark()">
                                <i :class="isDark ? 'carbon-moon' : 'carbon-sun'
                                    "></i>
                                <span class="ml-2">{{
                                    isDark ? "Dark" : "Light"
                                    }}</span>
                            </button>
                        </div>
                        <div class="lg:hidden">
                            <Sheet>
                                <SheetTrigger as-child>
                                    <Button variant="outline">Open</Button>
                                </SheetTrigger>
                                <SheetContent>
                                    <SheetHeader>
                                        <SheetTitle>Please select an option</SheetTitle>
                                    </SheetHeader>
                                    <div class="grid gap-4 py-4">
                                        <Link :href="route('login')"
                                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                        Log in
                                        </Link>
                                        <Link v-if="canRegister" :href="route('register')"
                                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                        Register
                                        </Link>
                                        <Button @click="toggleDark()">
                                            <i :class="isDark
                                                    ? 'carbon-moon'
                                                    : 'carbon-sun'
                                                "></i>
                                            <span class="ml-2">{{
                                                isDark ? "Dark" : "Light"
                                                }}</span>
                                        </Button>
                                    </div>
                                </SheetContent>
                            </Sheet>
                        </div>
                    </template>
                </div>
            </nav>
            <div class="flex justify-center flex-col py-20">
                <h1 class="text-4xl lg:text-6xl text-center font-extrabold tracking-tight text-green-400">
                    Attendence Checker
                </h1>
                <p class="text-lg lg:text-xl text-center mt-4">
                    Your ultimate project management solution
                </p>
            </div>
        </div>
    </div>
</template>
