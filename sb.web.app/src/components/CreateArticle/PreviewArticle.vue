<template>
    <div>
    <div id="center-header">
      <h2> {{ $t('shop_items.preview') }} </h2>
      <p> {{ $t('shop_items.ensure_data_correct') }} </p>
    </div>
    <div id="items-container">
      <PreviewItem :title="$t('shop_items.item_title')" :text='this.savedProgress.title' :images="null" />
      <PreviewItem :title="$t('shop_items.item_description')" :text='this.savedProgress.longDesc' :images="null" />
      <PreviewItem :title="$t('shop_items.item_type')" :text='this.savedProgress.article' :images="null" />
      <PreviewItem :title="$t('category')" :text='this.savedProgress.category' :images="null" />
      <PreviewItem :title="$t('time.time')" :text='this.endDate' :images="null" />
      <PreviewItem :title="$t('shop_items.location')" :text='this.savedProgress.destination' :images="null" />
      <PreviewItem :title="$t('price')" :text="this.savedProgress.price +  '  ' + $t('org.token')" :images="null" />
      <PreviewItem :title="$t('shop_items.images')" :text="null" :images='this.images' />
    </div>
    <PopupCard v-if="this.isPublished" :title="$t('confirmation')" :cardText="$t('shop_items.item_available_in_shop')" btnLink='/shop' btnText="Ok" />
  </div>
</template>

<script>
/* eslint-disable */
import PreviewItem from './PreviewItem.vue'
import PopupCard from '../SharedComponents/PopupCard.vue'
import { getImg } from '../../serverFetch'

export default {
  name: 'PreviewArticle',
  components: {
    PreviewItem,
    PopupCard
  },
  mounted () {
    if (this.savedProgress.img) {
      for (const img of this.savedProgress.img) {
        const URLImg = URL.createObjectURL(img)
        if (this.images.length % 2 === 0) {
          this.images.push([URLImg, true, this.images.length, img.isCoverImg])
        } else {
          this.images.push([URLImg, false, this.images.length, img.isCoverImg])
        }
      }
    } else {
      let categorySelected = this.savedProgress.categories.filter(el => el.name == this.savedProgress.category)[0]
      getImg(categorySelected.defaultMainImage).then((res) => {
        if (res.ok) {
          return res.blob()
        }
      }).then(data => {
        const URLImg = URL.createObjectURL(data)
        this.images.push([URLImg, true, this.images.length, true])
      })
      // multiple images uploaded
      if ('defaultImage' in categorySelected) {
        for (const img of categorySelected.defaultImage) {
          getImg(img).then((res) => {
            if (res.ok) {
              return res.blob()
            }
          }).then(data => {
            const URLImg = URL.createObjectURL(data)
            this.images.push([URLImg, true, this.images.length, false])
          })
        }
      }
    }
    if (this.savedProgress['end-date'] !== null) {
      const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      }

      this.endDate = this.savedProgress['end-date']
    }
  },
  props: ['savedProgress', 'isPublished'],
  data () {
    return {
      images: [],
      endDate: this.$i18n.t('shop_items.indefinitely')
    }
  }
}
</script>

<style scoped>


h2 {
    font-weight: 400;
    font-size: 24px;
    letter-spacing: 0.06em;
    font-style: normal;
}

p {
    font-style: normal;
    font-weight: 300;
    font-size: 16px;
    line-height: 18px;
    letter-spacing: 0.03em;
    margin-top: 10px;
}

#center-header {
    text-align: center;
    margin-top: 20px;
}

#items-container {
    width: 500px;
}

@media (max-width: 600px) {
  #items-container {
    width: 80%;
  }

  h2 {
    font-size: 20px;
  }

  p {
    font-size: 13px;
  }
}

</style>
