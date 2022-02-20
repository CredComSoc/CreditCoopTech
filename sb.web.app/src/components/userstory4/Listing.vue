<template>
    <div>
      <p>{{ title }}</p>
      <p>{{ description }}</p>
      <p>{{ image }}</p>
    </div>

</template>
<script>
export default {

  props: {
    title: String,
    description: String,
    image: String
  }
}
</script>
