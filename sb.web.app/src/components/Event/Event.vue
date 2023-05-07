<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import { createEventId, initUserId, myUserId, initEvents, getLoadedEvents } from './event-utils'
import { uploadEvent, deleteEvent } from '../../serverFetch'
import { ref } from 'vue'
import Modal from '../Modal/Modal.vue'
/*
The FullCalendar plugin is used to create calendar.
Modal is used for popup windows. check Modal/Modal.vue
event-utils.js contains some helperfunctions to fullcalendar
sb.web.app/src/serverFetch.js has function to talk to database. 
*/
export default {
  components: {
    FullCalendar, 
    Modal
  },
  setup () {
    initUserId()
    const showModal = ref(false)
    const collectInfoModal = ref(false)
    const editModal = ref(false)
    return { showModal, collectInfoModal, editModal }
  }, 
  data: function () {
    return {
      calendarOptions: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin 
        ],
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',        
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

      },
      currentEvents: [],
      clickedEvent: '',
      savedDate: [],
      counter: 0,
      owner: false  
    }
  },
  methods: {
    handleWeekendsToggle () {
      this.calendarOptions.weekends = !this.calendarOptions.weekends 
    },

    //runs when user clicks on a date, opens the collect info modal.
    handleDateSelect (selectInfo) {
      selectInfo.allDay = false       
      this.savedDate = selectInfo
      this.collectInfoModal = true   
    },

    //Helper function to add correct time to event.
    timeManipulate (savedDate, variabel) { 
      let realtime = ''     
      if (variabel === 'End') {
        realtime = document.getElementById('eventTimeEnd').value + ':00'
      } else if (variabel === 'Start') {
        realtime = document.getElementById('eventTimeStart').value + ':00' 
      } else { realtime = '' }
      const datestring = savedDate + ' ' + realtime
      return datestring
    },

    //Runs when user clickes event. Saves info eventinfo in clickedEvent, checks ownership and opens the show info modal.
    handleEventClick (clickInfo) {
      this.clickedEvent = clickInfo
      this.owner = false      
      if (this.clickedEvent.event.extendedProps.userId === myUserId) { 
        this.owner = true
      }
      this.showModal = true
    },

    handleEvents (events) {
      this.currentEvents = events
    },

    // Calls deleteEvent that removes event from database and then removes the evenet from calendar Api.  
    removeEvent () {
      deleteEvent(this.clickedEvent.event.extendedProps._id)
      this.clickedEvent.event.remove()
    },
     
    //Creates an event and add it to both database and calendar Api. Called on by createevent modal.       
    handleInput () {
      const calendarApi = this.savedDate.view.calendar
      calendarApi.unselect()

      this.savedDate.endStr = this.timeManipulate(this.savedDate.startStr, 'End')
      this.savedDate.startStr = this.timeManipulate(this.savedDate.startStr, 'Start')

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
          webpage: this.eventURL, 
          _startTime: document.getElementById('eventTimeStart').value, 
          _endTime: document.getElementById('eventTimeEnd').value          
        })  

        // UploadEvent saves event on database, is located in sb.web.app/src/serverFetch.js       
        uploadEvent(this.eventTitle, this.savedDate.startStr, this.savedDate.endStr, this.savedDate.allDay, 
          this.eventLocation, this.eventDescription, this.eventContacts, this.eventURL, 
          document.getElementById('eventTimeStart').value, 
          document.getElementById('eventTimeEnd').value).then((res) => {
          if (res.status === 200) {
            this.isPublished = true 
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
  <!-- Sidebar -->
  <div class='demo-app'>
    <div class='demo-app-sidebar'>
        <div class='demo-app-sidebar-section'>
        <h2>{{ $t('event.instructions') }}</h2>
        <ul>
            <li>{{ $t('event.click_date') }}</li>
            <li>{{ $t('event.click_event') }}</li>            
        </ul>
        </div>
        <div class='demo-app-sidebar-section'>
        <label>
            <input
            type='checkbox'
            :checked='calendarOptions.weekends'
            @change='handleWeekendsToggle'
            />
            {{ $t('event.show_weekends') }}
        </label>
        </div>
        <div class='demo-app-sidebar-section'>
        <h3>{{ $t('event.show_all') }} ({{ currentEvents.length }})</h3>
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
      <b> {{ $t('location') }}: </b>  <template v-if="this.clickedEvent.event != null"> {{this.clickedEvent.event.extendedProps.location}} </template>            
      <br><b>{{ $t('event.start') }}:</b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps._startTime}}  </template>
      <br><b>{{ $t('event.end_time') }}: </b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps._endTime}} </template>    
      <br><b>{{ $t('event.event_info') }}: </b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps.description}} </template>
      <br><b>{{ $t('event.url') }}: </b> <template v-if="this.clickedEvent.event != null"><a :href=" 'http://'+this.clickedEvent.event.extendedProps.webpage">{{this.clickedEvent.event.extendedProps.webpage}}</a>  </template>
      <br>
      <br>          
      <button v-if="owner" class="button-modal" @click="removeEvent ()">Radera</button> 
    </Modal> 

    <!-- Modal to create events   -->
    <Modal :open="collectInfoModal" @close="collectInfoModal = !collectInfoModal">
      <div>
        <p> {{ $t('event.event_name') }}: 
          <br><input v-model="eventTitle" placeholder="Title"/> 
        </p>
        <p> {{ $t('event.event_loc') }}: 
          <br><input v-model="eventLocation" placeholder="Plats" /> 
        </p>
        <p> {{ $t('event.contact') }}:
          <br><input v-model="eventContacts" placeholder="{{ $t('event.event_contact') }}" />
        </p>
        <p> {{ $t('event.url') }}: {{eventURL}} 
          <br><input v-model="eventURL" placeholder="URL" />
        </p>
        <p> {{ $t('event.event_description') }}:  
          <br><textarea v-model="eventDescription" placeholder="{{ $t('description') }}"> </textarea>
        </p>
        <p> 
          {{ $t('event.choose_start') }}: 
          <input type='time' id='eventTimeStart' name="EventTimeStart"/>
          {{ $t('event.choose_end') }}: 
          <input type='time' id='eventTimeEnd' name="EventTimeEnd"/>
        </p>          
      </div>
      <button @click="handleInput(); collectInfoModal = !collectInfoModal" class="button-modal"> {{ $t('event.create_event') }}</button>
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
