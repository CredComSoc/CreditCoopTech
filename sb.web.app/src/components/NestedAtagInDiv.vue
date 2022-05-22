<template>
    <a class="cover-link" href="/#"></a>
</template>

<script>
// Component that can be used to make a-tag cover all of a div-tag

export default {
  name: 'NestedAtagInDiv'
}
</script>

<style scoped>

a.cover-link {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    text-decoration: none;
}

</style>
