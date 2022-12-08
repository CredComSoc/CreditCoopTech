<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { createEventId, initUserId, myUserId, initEvents, getLoadedEvents } from './event-utils'
import { store } from '../../store/index'
import { uploadEvent, deleteEvent } from '../../serverFetch'
import { ref, toDisplayString } from 'vue'
//import { Fancybox } from '@fancyapps/ui' TA BORT DETTA PAKET FRÅN PACKET-LOCK JSON
import Modal from '../Modal/Modal.vue'
  
export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
    Modal
  },
  setup () {
    initUserId()
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
      counter: 0,
      owner: false //To control if user is allowed to change events      
    }
  },
  methods: {
    handleWeekendsToggle () {
      this.calendarOptions.weekends = !this.calendarOptions.weekends // update a property
    },
    handleDateSelect (selectInfo) {
      selectInfo.allDay = false //Disables allday     
      
      this.savedDate = selectInfo
      this.collectInfoModal = true   
    },
    stringmanipulat (savedDate, variabel) { //
      let realtime = ''     
      if (variabel === 'End') {
        realtime = document.getElementById('eventTimeEnd').value + ':00'
      } else if (variabel === 'Start') {
        realtime = document.getElementById('eventTimeStart').value + ':00' 
      } else { realtime = '' }
      const datestring = savedDate + ' ' + realtime
      return datestring
    },

    handleEventClick (clickInfo) {
      this.clickedEvent = clickInfo
      this.owner = false
      //console.log('user: ')
      //console.log(myUserId)
      //console.log('eventuser:')
      //console.log(this.clickedEvent.event.extendedProps.userId)      
      if (this.clickedEvent.event.extendedProps.userId === myUserId) { //Kollar man är ägare av event
        this.owner = true
      }
      this.showModal = true
    },

    handleEvents (events) {
      //console.log(events)
      //console.log(this.counter++)
      this.currentEvents = events
      //console.log('This is the store/data: ')
      //console.log(this.$store.state.allEvents)
    },
    testfunc () {
      deleteEvent(this.clickedEvent.event.extendedProps._id)
      this.clickedEvent.event.remove()
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

      this.savedDate.startStr = this.stringmanipulat(this.savedDate.startStr, 'Start')
      this.savedDate.endStr = this.stringmanipulat(this.savedDate.startStr, 'End')

      const eventId = createEventId()
      if (this.eventTitle) {
        calendarApi.addEvent({
          id: eventId,
          title: this.eventTitle,
          start: this.savedDate.startStr,
          end: this.savedDate.startStr,
          allDay: this.savedDate.allDay,
          location: this.eventLocation,
          description: this.eventDescription,
          contact: this.eventContacts,          
          webpage: this.eventURL, 
          _startTime: document.getElementById('eventTimeStart').value, 
          _endTime: document.getElementById('eventTimeEnd').value          
        })
        //, document.getElementById('eventTimeStart').value, document.getElementById('eventTimeEnd').value
        uploadEvent(this.eventTitle, this.savedDate.startStr, this.savedDate.endStr, this.savedDate.allDay, 
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
        <h2>Instruktioner</h2>
        <ul>
            <li>Klicka på ett datum för att skapa ett evenemang</li>
            <li>Klicka på ett evenemang för att se information</li>
            <!--<li>Click an event to delete it</li>-->
        </ul>
        </div>
        <div class='demo-app-sidebar-section'>
        <label>
            <input
            type='checkbox'
            :checked='calendarOptions.weekends'
            @change='handleWeekendsToggle'
            />
            Visa helgerna
        </label>
        </div>
        <div class='demo-app-sidebar-section'>
        <h3>Alla evenemang ({{ currentEvents.length }})</h3>
        <ul>
            <li v-for='event in currentEvents' :key='event.id'>
            <b>{{ event.startStr.slice(0, 10) }}</b>
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
    <!-- Modal to show events   -->
    <Modal :open="showModal" @close="showModal = !showModal">
        
          <h4 v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.title}} </h4>
          <b> Plats: </b>  <template v-if="this.clickedEvent.event != null"> {{this.clickedEvent.event.extendedProps.location}} </template>
                  
          <br><b>Starttid:</b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps._startTime}}  </template>
          <br><b>Sluttid: </b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps._endTime}} </template>
          
          <br><b>Info om eventet: </b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps.description}} </template>
          <br><b>URL: </b> <template v-if="this.clickedEvent.event != null"><a :href=" 'http://'+this.clickedEvent.event.extendedProps.webpage">{{this.clickedEvent.event.extendedProps.webpage}}</a>  </template>
          <br><br>
          <button v-if="owner" class="button-modal">Redigera</button>
          <button v-if="owner" class="button-modal" @click="testfunc ();showModal = !showModal">Radera</button> 
    </Modal> 

    <!-- Modal to create events   -->
    <Modal :open="collectInfoModal" @close="collectInfoModal = !collectInfoModal">
      <div>
        <p> Titel för eventet: 
          <br><input v-model="eventTitle" placeholder="Titel" /> 
        </p>
        <p> Plats för eventet: 
          <br><input v-model="eventLocation" placeholder="Plats" /> 
        </p>
        <p> Kontaktuppgifter:
          <br><input v-model="eventContacts" placeholder="Kontaktuppgifter" />
        </p>
        <p> URL för att visa andra medlemmar mer information: {{eventURL}} 
          <br><input v-model="eventURL" placeholder="URL" />
        </p>
        <p> Beskrivning av eventet:  
          <br><input v-model="eventDescription" placeholder="Beskrivning" />
        </p>
        <p> Välj starttid: 
          <input type='time' id='eventTimeStart' name="EventTimeStart"/>
          Välj sluttid: 
          <input type='time' id='eventTimeEnd' name="EventTimeEnd"/>
        </p>  
        <!--
        <input type='checkbox' @click="disableTime()" id='all-day' name='all-day' />
        <label for='all-day' > Hela dagen</label> <br>
        -->
      </div>
      <button @click="handleInput();collectInfoModal = !collectInfoModal" class="button-modal">Lägg till event</button>
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

.button-modal {
  border: 1px solid rgba(0, 0, 0, 0.3);
  border-radius: 0.3rem;
  padding: 0.2rem;
}
/*
#editbuttons {
  display: show;
}
*/
.modal-split {
  height: 100%;
  width: 50%;
  position: fixed;
  z-index: 1;
  top: 0;
  overflow-x: hidden;
  padding-top: 20px;
}

.modal-left{
right: 0;
background-color: red;
}

.modal-left{
left: 0;
background-color: green;
}
</style>
