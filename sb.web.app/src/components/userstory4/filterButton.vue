<template>
    <div className="filterButton">
        <input className="button" type="button" value="Filter" @click="$emit('filterTrigger')" >
    </div>
</template>

export default {

  props: {
  },
  methods: {
  }
}

<style scoped>

.button {
    height: 100%;
    width: 3rem;
    border-color: grey;
    border-radius: 0px 10px 10px 0px;
    border-left: transparent;
}

</style>
