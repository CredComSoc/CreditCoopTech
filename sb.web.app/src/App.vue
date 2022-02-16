<template>
  <div id="app">
    <Navbar />
    <Banner />
    <!-- <ContentCard title="Events" description="Bläddra bland senast upplagda produkter och tjänster." /> -->
    <ContentCard title="SHOP" description="Bläddra bland senast upplagda produkter och tjänster." theme="yellow-card" theme_btn="yellow-btn" btn_txt="Till shopen" :data="shop" />
    <ContentCard title="EVENTS" description="Bläddra bland senast upplagda event." theme="blue-card" theme_btn="blue-btn" btn_txt="Till events" :data="events" />
    <ContentCard title="MEDLEMMAR" description="Bläddra bland nya medlemmar i nätverket." theme="yellow-card" theme_btn="yellow-btn" btn_txt="Till medlemmar" :data="members" />
    <SaldoCard />
    <Footer />
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from './components/Navbar.vue'
import Banner from '@/components/ContentBanner.vue'
import Footer from '@/components/Footer.vue'
import ContentCard from '@/components/ContentCard.vue'
import SaldoCard from '@/components/SaldoCard.vue'

export default {
  name: 'Home',
  components: {
    Banner,
    Footer,
    Navbar,
    ContentCard,
    SaldoCard
  },
  data () {
    return {
      shop: [{ id: 0, img_path: 'Event_11.png', title: 'Hyr lokal', desc: 'Kontorsplatser', theme: 'regular' },
        { id: 1, img_path: 'Event_21.png', title: 'Frukostpaketet', desc: 'Färdiga mackor', theme: 'regular' },
        { id: 2, img_path: 'Event_6.png', title: 'Fönsterputs', desc: 'Vi putsar era fönster 8h', theme: 'regular' },
        { id: 3, img_path: 'Event_13.png', title: 'Konsulttjänst', desc: 'Affärsutveckling', theme: 'regular' },
        { id: 4, img_path: 'Event_7.png', title: 'Kontosstäd', desc: 'Städ 4h', theme: 'regular' }],

      events: [{ id: 0, img_path: 'Event_2.png', title: 'Barterbowling', desc: '18/12-21', theme: 'regular' },
        { id: 1, img_path: 'Event_15.png', title: 'Yoga med SB', desc: '12/11-21', theme: 'regular' },
        { id: 2, img_path: 'Event_3.png', title: 'Barter-fika', desc: '12/12-21', theme: 'regular' },
        { id: 3, img_path: 'Event_9.png', title: 'Svensk Barter 5-kamp', desc: '5/11-21', theme: 'regular' },
        { id: 4, img_path: 'Event_10.png', title: 'Systuga', desc: '1/12-21', theme: 'regular' }],

      members: [{ id: 0, img_path: 'Ellipse_1.png', title: 'Fotografgänget', theme: 'ellipse' },
        { id: 1, img_path: 'Ellipse_2.png', title: 'Cafegruppen', theme: 'ellipse' },
        { id: 2, img_path: 'Ellipse_3.png', title: 'Hemstäd Linköping', theme: 'ellipse' },
        { id: 3, img_path: 'Ellipse_8.png', title: 'IT-support', theme: 'ellipse' },
        { id: 4, img_path: 'Ellipse_6.png', title: 'Hemfixare', theme: 'ellipse' }]
    }
  }
}
</script>
