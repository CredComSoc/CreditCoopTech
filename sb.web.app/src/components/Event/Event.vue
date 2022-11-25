<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { createEventId, initEvents, getLoadedEvents } from './event-utils'
import { store } from '../../store/index'
import { uploadEvent } from '../../serverFetch'
import { ref, toDisplayString } from 'vue'
//import { Fancybox } from '@fancyapps/ui' TA BORT DETTA PAKET FRÅN PACKET-LOCK JSON
import Modal from '../Modal/Modal.vue'
  
export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
    Modal
  },
  setup () {
    //initEvents()
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
        //initialEvents: getLoadedEvents(), // alternatively, use the `events` setting to fetch from a feed
        events: this.$store.state.allEvents,
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
      savedDate: [],
      counter: 0
    }
  },
  methods: {
    handleWeekendsToggle () {
      this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
    },
    handleDateSelect (selectInfo) {
      this.collectInfoModal = true
      this.savedDate = selectInfo
    },
    handleEventClick (clickInfo) {
      this.clickedEvent = clickInfo
      this.showModal = true
    },

    handleEvents (events) {
      console.log(events)
      console.log(this.counter++)
      this.currentEvents = events
      console.log('This is the store/data: ')
      console.log(this.$store.state.allEvents)
    },

    disableTime () {  
      if (document.getElementById('all-day').checked) {
        document.getElementById('eventTimeStart').disabled = true
        document.getElementById('eventTimeEnd').disabled = true
      } else {
        document.getElementById('eventTimeStart').disabled = false
        document.getElementById('eventTimeEnd').disabled = false  
      }
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
          website: this.eventURL,
          _startTime: document.getElementById('eventTimeStart').value, 
          _endTime: document.getElementById('eventTimeEnd').value
        })
        //, document.getElementById('eventTimeStart').value, document.getElementById('eventTimeEnd').value
        uploadEvent(this.eventTitle, this.savedDate.start, this.savedDate.end, this.savedDate.allDay, 
          this.eventLocation, this.eventDescription, this.eventContacts, this.eventURL, 
          document.getElementById('eventTimeStart').value, 
          document.getElementById('eventTimeEnd').value).then((res) => {
          if (res.status === 200) {
            this.isPublished = true // open popup with success message
          } else {
            this.error = true
          }
        }) 
      }
    }
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
      <p> Eventdetaljer</p>
      <p v-if="this.clickedEvent.event != null"> {{this.clickedEvent.event.title}} 
        <br>{{this.clickedEvent.event.title}} 
        <br>{{this.clickedEvent.event.extendedProps._endTime}} 
        <br>{{this.clickedEvent.event.extendedProps._startTime}} </p>
    </Modal> 
    <Modal :open="collectInfoModal" @close="collectInfoModal = !collectInfoModal">
      <div>
      <p> Titel för eventet: 
      <br><input v-model="eventTitle" placeholder="Titel" /> </p>
      <p> Plats för eventet: 
      <br><input v-model="eventLocation" placeholder="Plats" /> </p>
      <p> Kontaktuppgifter:
      <br><input v-model="eventContacts" placeholder="Kontaktuppgifter" /></p>
      <p> URL för att visa andra medlemmar mer information: {{eventURL}} 
      <br><input v-model="eventURL" placeholder="URL" /></p>
      <p> Beskrivning av eventet:  
      <br><input v-model="eventDescription" placeholder="Beskrivning" /></p>
      <p> Välj starttid: 
      <input type='time' id='eventTimeStart' name="EventTimeStart"/>
       Välj sluttid: 
      <input type='time' id='eventTimeEnd' name="EventTimeEnd"/></p>  
      <input type='checkbox' @click="disableTime()" id='all-day' name='all-day' />
      <label for='all-day' > Hela dagen</label> <br>
      </div>
      <button @click="handleInput();collectInfoModal = !collectInfoModal" class="button-add">Lägg till event</button>
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

.button-add {
  border: 1px solid rgba(0, 0, 0, 0.3);
  border-radius: 0.3rem;
  padding: 0.2rem;
}
</style>
