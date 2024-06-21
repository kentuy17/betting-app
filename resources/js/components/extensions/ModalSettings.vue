<script setup lang='ts'>
import { VueFinalModal } from "vue-final-modal";
import { ref, watch } from "vue";
import store from "store2";
import { axios } from "@bundled-es-modules/axios";

const storedSecs = store("delay");
const delay = ref(storedSecs ?? 0);
// const isReversed = ref(false);

function incrDelay() {
  delay.value++;
}

function decrDelay() {
  delay.value--;
}

// function toggle() {
  // isReversed.value = !isReversed
// }

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
    content-class="flex flex-col max-w-xl mx-4 p-2 w-40 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2">
    <span class="text-l">{{ title }}</span>
    <div class="flex col justify-center gap-2">
      <div class="flex col justify-justify gap-3">
        <label for="delay">Padaog</label>
        <div class="flex col gap-1 justify-between">
          <button :disabled="!delay" type="button" @click="decrDelay">-</button>
          <span id='delay' class="delay">{{ delay }}</span>
          <button type="button" @click="incrDelay">+</button>
        </div>
      </div>
    </div>
    <div class="flex col justify-between">
      <!-- Rounded switch -->
      <label class="switch">
       <!-- <input type="checkbox" @toggle="toggle" :checked="isReversed"> -->
       <input type="checkbox">
        <span class="slider round"></span>
      </label>
    </div>
    <button class="mt-4 center px-2 border rounded-lg" @click="emit('confirm')">
      OK </button>
  </VueFinalModal>
</template>
<style>
/* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked+.slider {
  background-color: #2196F3;
}

input:focus+.slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked+.slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
