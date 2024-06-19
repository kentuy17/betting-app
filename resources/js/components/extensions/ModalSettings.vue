<script setup lang='ts'>
import { VueFinalModal } from "vue-final-modal";
import { ref, watch } from "vue";
import store from "store2";

const storedSecs = store("delay");
const delay = ref(storedSecs ?? 0);

function incrDelay() {
  delay.value++;
}

function decrDelay() {
  delay.value--;
}

watch(delay, (newDelay) => {
  store("delay", newDelay);
});

defineProps({
  title: String,
});

// const emit = defineEmits(["confirm"]);
const emit = defineEmits({
  e: 'confirm'
})

</script>
<template>
  <VueFinalModal class="flex justify-center items-center"
    content-class="flex flex-col max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2">
    <span class="text-l">{{ title }}</span>
    <div class="flex col justify-center gap-2">
      <label for="delay">Delay</label>
      <button :disabled="!delay" type="button" @click="decrDelay">-</button>
      <span id='delay' class="delay">{{ delay }}</span>
      <button type="button" @click="incrDelay">+</button>
    </div>
    <button class="mt-4 center px-2 border rounded-lg" @click="emit('confirm')">
      OK </button>
  </VueFinalModal>
</template>
