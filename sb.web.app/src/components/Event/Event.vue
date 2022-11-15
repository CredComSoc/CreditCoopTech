<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { INITIAL_EVENTS, createEventId, initEvents, loadedevents } from './event-utils'
import { uploadEvent } from '../../serverFetch'
import { ref } from 'vue'
//import { Fancybox } from '@fancyapps/ui' TA BORT DETTA PAKET FRÅN PACKET-LOCK JSON
import Modal from '../Modal/Modal.vue'
  
export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
    Modal
  }, 
  setup () {
    initEvents()
    const showModal = ref(false)
    const collectInfoModal = ref(false)
    return { showModal, collectInfoModal }
  }, 
  data: function () {
    return {
      calendarOptions: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin // needed for dateClick
        ],
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        initialEvents: loadedevents, // alternatively, use the `events` setting to fetch from a feed
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        select: this.handleDateSelect,
        eventClick: this.handleEventClick,
        eventsSet: this.handleEvents,
        handleInput: this.handleInput
        //eventAdd: this.postEvent
        /* you can update a remote database when these fire:
        eventChange:
        eventRemove:
        */
      },
      currentEvents: [],
      clickedEvent: '',
      savedDate: []
    }
  },
  methods: {
    handleWeekendsToggle () {
      this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
    },
    handleDateSelect (selectInfo) {
      //alert(initEvents())
      this.eventTitle = null
      this.eventDescription = null
      this.eventContacts = null
      this.eventLocation = null
      //const title = prompt('Please enter a new title for your event')
      this.collectInfoModal = true
      this.savedDate = selectInfo
      /*
      const title = this.eventTitle
      const calendarApi = selectInfo.view.calendar
      calendarApi.unselect() // clear date selection
      const eventId = createEventId()
      */ 
      //Lägg till ovanstående i databasen
      /*
      this.newEvent.id = eventId
      this.newEvent.title = 
      this.newEvent.start = selectInfo.startStr
      this.newEvent.end = selectInfo.endStr
      this.newEvent.allDay = selectInfo.allDay*/
      //console.log(selectInfo.id)
    },
    handleEventClick (clickInfo) {
      this.clickedEvent = clickInfo
      this.showModal = true
    },

    handleEvents (events) {
      this.currentEvents = events
    },

    handleInput () {
      const calendarApi = this.savedDate.view.calendar
      calendarApi.unselect() // clear date selection
      const eventId = createEventId()
      if (this.eventTitle) {
        calendarApi.addEvent({
          id: eventId,
          title: this.eventTitle,
          start: this.savedDate.startStr,
          end: this.savedDate.endStr,
          allDay: this.savedDate.allDay,
          location: this.eventLocation,
          description: this.eventDescription,
          contact: this.eventContacts,
          website: this.eventURL
        })

        uploadEvent(this.eventTitle, this.savedDate.start, this.savedDate.end, this.savedDate.allDay, this.eventLocation, this.eventDescription, this.eventContacts, this.eventURL).then((res) => {
          if (res.status === 200) {
            this.isPublished = true // open popup with success message
            this.popupCardText = 'Tjiho!! Det lyckades :).\nVar god försök inte igen senare.'
          } else {
            this.error = true
            this.popupCardText = 'Något gick fel när artikeln skulle laddas upp.\nVar god försök igen senare.'
          }
        }) 
      }
    }
    /*   
    postEvent(){
      const newEvent = {
      id: eventId,
      title,
      start: selectInfo.startStr,
      end: selectInfo.endStr,
      allDay: selectInfo.allDay
    }
        
      const data = new FormData()
      data.append('event', JSON.stringify(this.newEvent))
      uploadEvent(data).then((res) => {
        if (res.status === 200) {
          this.isPublished = true // open popup with success message
          this.popupCardText = 'Tjiho!! Det lyckades :).\nVar god försök inte igen senare.'
        } else {
          this.error = true
          this.popupCardText = 'Något gick fel när artikeln skulle laddas upp.\nVar god försök igen senare.'
        }
        })
    }
    */
  }
}

</script>
<template>
    <div class='demo-app'>
    <div class='demo-app-sidebar'>
        <div class='demo-app-sidebar-section'>
        <h2>Instructions</h2>
        <ul>
            <li>Select dates and you will be prompted to create a new event</li>
            <li>Drag, drop, and resize events</li>
            <li>Click an event to delete it</li>
        </ul>
        </div>
        <div class='demo-app-sidebar-section'>
        <label>
            <input
            type='checkbox'
            :checked='calendarOptions.weekends'
            @change='handleWeekendsToggle'
            />
            toggle weekends
        </label>
        </div>
        <div class='demo-app-sidebar-section'>
        <h2>All Events ({{ currentEvents.length }})</h2>
        <ul>
            <li v-for='event in currentEvents' :key='event.id'>
            <b>{{ event.startStr }}</b>
            <i>{{ event.title }}</i>
            </li>
        </ul>
        </div>
    </div>
    <div class='demo-app-main'>
        <FullCalendar
        class='demo-app-calendar'
        :options='calendarOptions'
        >
        <template v-slot:eventContent='arg'>
            <b>{{ arg.timeText }}</b>
            <i>{{ arg.event.title }}</i>
        </template>
        </FullCalendar>

    <Modal :open="showModal" @close="showModal = !showModal">
      <p> Event Details</p>
      <p> {{this.clickedEvent.event}} </p> 
      <p> {{this.clickedEvent.event}} </p> 
    </Modal> 
    <Modal :open="collectInfoModal" @close="collectInfoModal = !collectInfoModal">
      <div>
      <p> Titel för eventet: {{eventTitle}}</p>
      <input v-model="eventTitle" placeholder="Titel" />
      <p> Plats för eventet: {{eventLocation}}</p>
      <input v-model="eventLocation" placeholder="Plats" />
      <p> Kontaktuppgifter: {{eventContacts}}</p>
      <input v-model="eventContacts" placeholder="Kontaktuppgifter" />
      <p> URL för att visa andra medlemmar mer information: {{eventURL}} </p>
      <input v-model="eventURL" placeholder="URL" />
      <p> Beskrivning av eventet: {{eventDescription}} </p>
      <input v-model="eventDescription" placeholder="Beskrivning" />
      </div>
      <button @click="handleInput();collectInfoModal = !collectInfoModal">Lägg till event</button>
      <!-- <form id="form" onsubmit="return false;">
      <input type="text" id="userInput" />
      <input type="submit" @click='handleInput' />
      </form>-->
    </Modal> 
    </div>
    </div>
</template>

<style lang='css'>
h2 {
    margin: 0;
    font-size: 16px;
}
ul {
    margin: 0;
    padding: 0 0 0 1.5em;
}
li {
    margin: 1.5em 0;
    padding: 0;
}
b { /* used for event dates/times */
    margin-right: 3px;
}
.demo-app {
    display: flex;
    min-height: 100%;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
}
.demo-app-sidebar {
    width: 300px;
    line-height: 1.5;
    background: #eaf9ff;
    border-right: 1px solid #d3e2e8;
}
.demo-app-sidebar-section {
    padding: 2em;
}
.demo-app-main {
    flex-grow: 1;
    padding: 3em;
}
.fc { /* the calendar root */
    max-width: 1100px;
    margin: 0 auto;
}
</style>
