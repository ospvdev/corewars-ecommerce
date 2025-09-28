<script setup lang="ts">
    import {ref} from 'vue';

    // const count = ref(0);
    // const isDisabled = ref(false);
    // const bg_color = ref('default');
    const itemClicked = ref(0);
    const activated = ref(false);
    const btns = [
        { id: 1, name: 'Item 1', active: false, url: '/user/profile' },
        { id: 2, name: 'Item 2', active: false, url: '/user/profile' },
        { id: 3, name: 'Item 3', active: false, url: '/user/profile' },
        { id: 4, name: 'Item 4', active: false, url: '/user/profile' },
    ]

    function activate(item: number) {
      itemClicked.value = item;
      activated.value = true;
    }

    // function increment() {
    //     count.value++;

    //     if(count.value > 0){
    //         bg_color.value = 'danger';
    //         isDisabled.value = false;
    //     }
    // }

    // function decrement() {
    //     count.value--;

    //     if(count.value <= 0){
    //         count.value = 0;
    //         bg_color.value = 'default';
    //         isDisabled.value = true;
    //     }
    // }
</script>

<style scoped>
#bg-red{
    background-color: red;
}

.btn {
  border: 2px solid black;
  background-color: white;
  color: black;
  padding: 4px 8px;
  font-size: 16px;
  cursor: pointer;
}

/* Green */
.success {
  border-color: #04AA6D;
  color: green;
}

.success:hover {
  background-color: #04AA6D;
  color: white;
}

/* Blue */
.info {
  border-color: #2196F3;
  color: dodgerblue
}

.info:hover {
  background: #2196F3;
  color: white;
}

/* Orange */
.warning {
  border-color: #ff9800;
  color: orange;
}

.warning:hover {
  background: #ff9800;
  color: white;
}

/* Red */
.danger {
  border-color: #f44336;
  color: red
}

.danger:hover {
  background: #f44336;
  color: white;
}

/* Gray */
.default {
  border-color: #e7e7e7;
  color: black;
}

.default:hover {
  background: #e7e7e7;
}

.thick-underline {
  position: relative;
  text-decoration: none;
  color: inherit;
}

.thick-underline::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: -3px;      /* distancia desde el texto (ajusta) */
  height: 2px;       /* grosor de la línea (ajusta) */
  background: green;
  transform-origin: left center;
  transform: scaleX(0);
  transition: transform .18s ease;
}

.thick-underline:hover::after,
.thick-underline.activated::after {
  transform: scaleX(1);
}
</style>

<template>
    <!-- <h1>Contador {{ count }}</h1>
    <button class="btn" :class="bg_color" :disabled="isDisabled" @click="decrement">-</button>
    <button class="btn success" @click="increment">+</button> -->

    <!-- Navlink activo -->
    <ul>
      <li v-for="btn in btns" :key="btn.id"><a :href="btn.url" @click="activate(btn.id)" :class="[{activated: activated && itemClicked === btn.id}, 'thick-underline']">{{ btn.name }}</a></li>
    </ul>
    <h2>Item clickeado: {{ itemClicked }}</h2>
</template>