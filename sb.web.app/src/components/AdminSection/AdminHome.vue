<template>
  <div class="admin_home"> 
    <h1>Välkommen Admin</h1>
  </div>
  <div class="Task">
    <div class="search">
      Search
    </div>
    <div class="action">
      Task
    </div>
    <div class="add members" >
      <router-link :to="{name:'New_Member'}">        
        <div>Add Members</div>
        <img src="../../assets/cart_images/add.png" alt="">
      </router-link>
    </div>
  </div>
  
  <div class="container">

  </div>
</template>

<script>

export default {
  name: 'AdminHome',
  components: {

  },
  data () {
    return {
      
    }
  }
}

</script>

<style>
  .admin_home{
    width: fit-content;
    margin: auto;
  }
  .admin_home>h1{
    margin: 0px;
  }

  .Task{
    width: 100%;
    padding-inline: 20%;
    height: fit-content;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  .search{
    width: 30%;
    height: 20px;
    background-color: aquamarine;
    border-radius: 4px; 
  }
  .action{
    width: 30%;
    height: 20px;
    background-color: aquamarine;
    border-radius: 4px; 
  }
  .seting{
    width: 30%;
    height: 20px;
    background-color: aquamarine;
    border-radius: 4px; 
  }

</style>
