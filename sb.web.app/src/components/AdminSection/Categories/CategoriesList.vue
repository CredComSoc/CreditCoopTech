<template>
    <div>
        <button class="add_button">
            <router-link :to="{name:'AddCategories'}" >
                {{$t('user.add_category')}}
            </router-link>
        </button>
      <table>
        <thead>
          <tr>
            <th>

            </th>
            <th >
              {{ $t('user.billingnamelabel') }}
            </th>
            <th>
              {{ $t('is_active') }}
            </th>
            <th>{{ $t('action') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in categories" :key="index">
            <td>
                {{ index }}
            </td>
            <td >
              <p>{{ item.name }}</p>
            </td>
            <td>
                {{ item.isActive ? 'True': 'False' }}
            </td>
            <td>
                <button v-if="item.isActive" @click="updateCategories(item['_id'], false)" title="$t('disable_category')" class="fa fa-ban disable-button"></button>
                <button v-if="!item.isActive" @click="updateCategories(item['_id'], true)" title="$t('enable_category')" class="fa fa-check enable-button"></button>
            </td>
          </tr>
        </tbody>
      </table>
      <PopupCard v-if="this.error" @closePopup="this.closePopup" btnText="Ok" :title="'Categories'" :btnLink="null" :cardText="popupCardText" />

    </div>
  </template>
  
<script>
/* eslint-disable */
import { getCategories, updateCategoryStatus } from '../../../serverFetch'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'

  export default {
    name: "CategoriesList",
    components: {
      PopupCard
    },
    data() {
      return {
        categories: [],
        popupCardText: 'Unable to update Categories'
      };
    },
    methods: {

        updateCategories(id, isActive) {
            const data = new FormData();
            data.append("id", id);
            data.append("isActive", isActive)
            updateCategoryStatus(data).then(res => 
            {
                if(res)
                {
                     window.location.reload();
                }
            })
      },
    },
    mounted() {
        getCategories().then((res) => {
      this.categories = res;
    })
    },
  };
  </script>
  
  <style scoped>
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th, td {
    border: 1px solid #ddd;
    padding: 8px;
  }
  
  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
  .disable-button
  {
    background-color: lightcoral;
    border: none;
    font-size: 20px;
  }
  .enable-button
  {
    background-color: lightgreen;
    border: none;
    font-size: 20px;
  }
  .add_button{
    font-size: 20px;
    float: right;
    background-color: lightyellow;
    border-radius: 7px;
    margin: 10px 5px 5px 10px;
  }
  </style>
