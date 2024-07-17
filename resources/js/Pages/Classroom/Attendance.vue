<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, computed, onMounted } from "vue";
import { QrcodeStream } from "vue-qrcode-reader";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Classrooms } from "./Index.vue";
import Button from "@/Components/ui/button/Button.vue";
import { toast } from "vue-sonner";
import { router } from "@inertiajs/vue3";
import { Breadcrumb as BreadcrumbType } from "@/types/Breadcrumb";
interface Course {
    classroom_id: number;
    id: number;
    name: string;
}
const result = ref<{ student_id: number; name: string } | null>(null);
const selectedCourse = ref<Course | null>(null);

function onDetect(detectedCodes) {
    console.log(detectedCodes);
    const parsedCodes = detectedCodes
        .map((code) => {
            try {
                const parsedCode = JSON.parse(code.rawValue);
                console.log(parsedCode);
                return {
                    student_id: parsedCode.student_id,
                    name: parsedCode.name,
                };
            } catch (e) {
                console.error("Error parsing code:", e);
                return null;
            }
        })
        .filter((code) => code !== null);

    if (parsedCodes.length > 0) {
        result.value = parsedCodes[0];
        // Trigger a toast notification
        toast(`QR Code Detected`, {
            description: `id: ${result.value?.student_id}, name: ${result.value.name}`,
            action: {
                label: "OK",
                onClick: () => console.log("OK clicked"),
            },
        });
        router.post(`/classroom/${props.classroom.id}/attendance`, {
            student_id: result.value?.student_id,
            classroom_id: props.classroom.id,
            course_id: props.classroomSchedule.course_id,
            // date: new Date().toISOString().split("T")[0], // Current date in YYYY-MM-DD format
            attended: "present",
        });
    } else {
        toast("No Student Found in the specific classroom", {
            description: "No student found",
        });
    }
}

const selectedDevice = ref(null);
const devices = ref([]);

onMounted(async () => {
    devices.value = (await navigator.mediaDevices.enumerateDevices()).filter(
        ({ kind }) => kind === "videoinput",
    );

    if (devices.value.length > 0) {
        selectedDevice.value = devices.value[0];
    }
});

function paintOutline(detectedCodes, ctx) {
    for (const detectedCode of detectedCodes) {
        const [firstPoint, ...otherPoints] = detectedCode.cornerPoints;

        ctx.strokeStyle = "red";

        ctx.beginPath();
        ctx.moveTo(firstPoint.x, firstPoint.y);
        for (const { x, y } of otherPoints) {
            ctx.lineTo(x, y);
        }
        ctx.lineTo(firstPoint.x, firstPoint.y);
        ctx.closePath();
        ctx.stroke();
    }
}

function paintBoundingBox(detectedCodes, ctx) {
    for (const detectedCode of detectedCodes) {
        const {
            boundingBox: { x, y, width, height },
        } = detectedCode;

        ctx.lineWidth = 2;
        ctx.strokeStyle = "#007bff";
        ctx.strokeRect(x, y, width, height);
    }
}

function paintCenterText(detectedCodes, ctx) {
    for (const detectedCode of detectedCodes) {
        const { boundingBox, rawValue } = detectedCode;

        const centerX = boundingBox.x + boundingBox.width / 2;
        const centerY = boundingBox.y + boundingBox.height / 2;

        const fontSize = Math.max(
            12,
            (50 * boundingBox.width) / ctx.canvas.width,
        );

        ctx.font = `bold ${fontSize}px sans-serif`;
        ctx.textAlign = "center";

        ctx.lineWidth = 3;
        ctx.strokeStyle = "#35495e";
        ctx.strokeText(detectedCode.rawValue, centerX, centerY);

        ctx.fillStyle = "#5cb984";
        ctx.fillText(rawValue, centerX, centerY);
    }
}

const trackFunctionOptions = [
    { text: "nothing (default)", value: undefined },
    { text: "outline", value: paintOutline },
    { text: "centered text", value: paintCenterText },
    { text: "bounding box", value: paintBoundingBox },
];
const trackFunctionSelected = ref(trackFunctionOptions[1]);

/*** barcode formats ***/
const barcodeFormats = ref({
    aztec: false,
    code_128: false,
    code_39: false,
    code_93: false,
    codabar: false,
    databar: false,
    databar_expanded: false,
    data_matrix: false,
    dx_film_edge: false,
    ean_13: false,
    ean_8: false,
    itf: false,
    maxi_code: false,
    micro_qr_code: false,
    pdf417: false,
    qr_code: true,
    rm_qr_code: false,
    upc_a: false,
    upc_e: false,
    linear_codes: false,
    matrix_codes: false,
});
const selectedBarcodeFormats = computed(() => {
    return Object.keys(barcodeFormats.value).filter(
        (format) => barcodeFormats.value[format],
    );
});

/*** error handling ***/
const error = ref("");

function onError(err) {
    error.value = `[${err.name}]: `;

    if (err.name === "NotAllowedError") {
        error.value += "you need to grant camera access permission";
    } else if (err.name === "NotFoundError") {
        error.value += "no camera on this device";
    } else if (err.name === "NotSupportedError") {
        error.value += "secure context required (HTTPS, localhost)";
    } else if (err.name === "NotReadableError") {
        error.value += "is the camera already in use?";
    } else if (err.name === "OverconstrainedError") {
        error.value += "installed cameras are not suitable";
    } else if (err.name === "StreamApiNotSupportedError") {
        error.value += "Stream API is not supported in this browser";
    } else if (err.name === "InsecureContextError") {
        error.value +=
            "Camera access is only permitted in secure context. Use HTTPS or localhost rather than HTTP.";
    } else {
        error.value += err.message;
    }
}

interface Course {
    classroom_id: number;
    id: number;
    name: string;
}
const qrStreamVisible = ref(false);

function toggleQRStream() {
    qrStreamVisible.value = !qrStreamVisible.value;
}
const props = defineProps<{
    classroom: Classrooms[];
    classroomSchedule: Object;
    courses: Course[];
    breadcrumbs: BreadcrumbType[];
}>();
</script>

<template>
    <AuthenticatedLayout :breadcrumbs="props.breadcrumbs">
        <div class="flex">
            <div class="flex-1">
                <div v-if="!classroomSchedule">
                    <p>No classes/course scheduled.</p>
                </div>
                <div v-else>
                    <h1 class="text-4xl text-primary-accent font-extrabold">
                        Today Date: {{ classroomSchedule.day }}
                    </h1>
                    <h2>
                        Current Teacher: {{ classroomSchedule.teacher.name }}
                    </h2>
                </div>
                <Button @click="toggleQRStream" class="ml-4 w-2/6">
                    {{ qrStreamVisible ? "Hide QR Stream" : "Show QR Stream" }}
                </Button>
                <p class="error">{{ error }}</p>

                <p class="decode-result">
                    Last result:
                    <b>{{
                        result
                            ? `id: ${result.student_id}, name: ${result.name}`
                            : "No result"
                    }}</b>
                </p>
            </div>

            <div
                v-if="qrStreamVisible"
                class="flex-1 flex justify-center items-center"
            >
                <qrcode-stream
                    :constraints="{ deviceId: selectedDevice.deviceId }"
                    :track="trackFunctionSelected.value"
                    :formats="selectedBarcodeFormats"
                    @error="onError"
                    @detect="onDetect"
                    v-if="selectedDevice !== null"
                    class="border-2 border-gray-300 rounded-lg p-4"
                />
                <p v-else class="error">No cameras on this device</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
.error {
  font-weight: bold;
  color: red;
}
.barcode-format-checkbox {
  margin-right: 10px;
  white-space: nowrap;
  display: inline-block;
}
</style>
