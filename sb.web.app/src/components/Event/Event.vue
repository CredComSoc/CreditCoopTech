<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import rrulePlugin from '@fullcalendar/rrule'
import { createEventId, initUserId, myUserId, initEvents, getLoadedEvents } from './event-utils'
import { uploadEvent, deleteEvent, setEventData, updateEvent } from '../../serverFetch'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'
import { ref } from 'vue'
import Modal from '../Modal/Modal.vue'
import LoadingComponent from '../SharedComponents/LoadingComponent.vue'

/*
The FullCalendar plugin is used to create calendar.
Modal is used for popup windows. check Modal/Modal.vue
event-utils.js contains some helperfunctions to fullcalendar
sb.web.app/src/serverFetch.js has function to talk to database. 
*/
export default {
  components: {
    FullCalendar, 
    Modal,
    PopupCard,
    LoadingComponent
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
          interactionPlugin,
          rrulePlugin
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
        handleInput: this.handleInput,
        eventTimeFormat: { // like '14:30:00'
          hour: '2-digit',
          minute: '2-digit',
          meridiem: 'short'
        }
      },
      currentEvents: [],
      clickedEvent: '',
      savedDate: [],
      counter: 0,
      owner: false,
      openCreateMeetingModal: false, 
      eventType: '',
      recurringType: '',
      dayOfWeek: '',
      dateOfMonth: '',
      eventDate: {
        startDate: '',
        endDate: '',
        startTime: '',
        endTime: ''
      },
      eventId: '',
      timeLapsError: ''
    }
  },
  async mounted () {
    await setEventData()
    this.calendarOptions.events = this.$store.state.allEvents
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
      this.resetData()
    },

    //Helper function to add correct time to event.
    timeManipulate (savedDate, dateType) { 
      let realtime = ''     
      if (dateType === 'End') {
        realtime = document.getElementById('eventTimeEnd').value + ':00'
      } else if (dateType === 'Start') {
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
    async removeEvent () {
      this.$refs.loadingComponent.showLoading()
      this.clickedEvent.event.remove()
      await deleteEvent(this.clickedEvent.event.extendedProps._id) 
      await setEventData()
      this.calendarOptions.events = this.$store.state.allEvents
      this.$refs.loadingComponent.hideLoading()
    },
     
    //Creates an event and add it to both database and calendar Api. Called on by createevent modal.       
    handleInput () {
      this.$refs.loadingComponent.showLoading()
      // const calendarApi = this.savedDate.view.calendar
      // calendarApi.unselect()
      this.showModal = false
      if (!this.eventId) {
        this.savedDate.endStr = this.timeManipulate(this.savedDate.startStr, 'End')
        this.savedDate.startStr = this.timeManipulate(this.savedDate.startStr, 'Start')
      }
      
      if (document.getElementById('eventTimeEnd').value <= document.getElementById('eventTimeStart').value) {
        this.$refs.loadingComponent.hideLoading()
        this.timeLapsError = true
        return
      }
      const eventId = createEventId()
      if (this.eventTitle) {
        const uploadEventData = {
          title: this.eventTitle, 
          start: this.savedDate.startStr, 
          end: this.savedDate.endStr, 
          allDay: this.savedDate.allDay, 
          location: this.eventLocation, 
          description: this.eventDescription, 
          contacts: this.eventContacts, 
          webpage: this.eventURL, 
          _startTime: document.getElementById('createEventTimeStart').value, 
          _endTime: document.getElementById('createEventTimeEnd').value,
          eventType: 'onetime',
          recurringType: null,
          daysOfWeek: null,
          recurrenceRule: null
        } 

        
        // UploadEvent saves event on database, is located in sb.web.app/src/serverFetch.js   
        if (this.eventId) {
          updateEvent(this.eventId, { ...uploadEventData }).then(async (res) => {
            if (res.status === 200) {
              this.isPublished = true
              this.resetData()
              await setEventData()
              this.calendarOptions.events = this.$store.state.allEvents
              this.$refs.loadingComponent.hideLoading()
            } else {
              this.error = true
              this.$refs.loadingComponent.hideLoading()
            }
          })
        } else {
          uploadEvent({ ...uploadEventData }).then(async (res) => {
            if (res.status === 200) {
              this.isPublished = true
              this.resetData()
              await setEventData()
              this.calendarOptions.events = this.$store.state.allEvents
              this.$refs.loadingComponent.hideLoading()
            } else {
              this.error = true
              this.$refs.loadingComponent.hideLoading()
            }
          })
        }  
      }      
    },

    createEvent () {
      this.$refs.loadingComponent.showLoading()
      const eventId = createEventId()
      this.showModal = false
      if (this.eventDate.startDate === this.eventDate.endDate && (document.getElementById('eventTimeEnd').value <= document.getElementById('eventTimeStart').value)) {
        this.$refs.loadingComponent.hideLoading()
        this.timeLapsError = true
        return
      }
      if (this.recurringType === 'monthly') {
        const date = new Date()
        const year = date.getFullYear()
        const month = date.getMonth()
        const day = date.getDate()
        console.log(this.dateOfMonth)
        this.eventDate.startDate = new Date(year, month, this.dateOfMonth)
        this.eventDate.endDate = new Date(year, month, this.dateOfMonth)
      }
      if (this.eventTitle) {
        const dow = [this.dayOfWeek]
        const uploadEventData = {
          title: this.eventTitle, 
          start: new Date(Date.parse(this.eventDate.startDate)), 
          end: new Date(Date.parse(this.eventDate.endDate)), 
          allDay: this.savedDate.allDay, 
          location: this.eventLocation, 
          description: this.eventDescription, 
          contacts: this.eventContacts, 
          webpage: this.eventURL, 
          _startTime: document.getElementById('createEventTimeStart').value, 
          _endTime: document.getElementById('createEventTimeEnd').value,
          eventType: this.eventType,
          recurringType: this.recurringType,
          daysOfWeek: this.recurringType === 'weekly' ? dow : null,
          recurrenceRule: this.recurringType === 'monthly' ? 'RRULE:FREQ=MONTHLY;BYMONTHDAY=' + this.dateOfMonth : null
        } 
        // UploadEvent saves event on database, is located in sb.web.app/src/serverFetch.js   
        if (this.eventId) {
          updateEvent(this.eventId, { ...uploadEventData }).then(async (res) => {
            if (res.status === 200) {
              this.isPublished = true
              this.resetData()
              await setEventData()
              this.calendarOptions.events = this.$store.state.allEvents
              this.$refs.loadingComponent.hideLoading()
            } else {
              this.error = true
              this.$refs.loadingComponent.hideLoading()
            }
          })
        } else {
          uploadEvent({ ...uploadEventData }).then(async (res) => {
            if (res.status === 200) {
              this.isPublished = true
              this.resetData()
              await setEventData()
              this.calendarOptions.events = this.$store.state.allEvents
              this.$refs.loadingComponent.hideLoading()
            } else {
              this.error = true
              this.$refs.loadingComponent.hideLoading()
            }
          })
        }
      }      
    },
    resetData () {
      this.eventTitle = ''
      this.eventLocation = ''
      this.eventContacts = ''
      this.eventURL = ''
      this.eventDescription = ''
      this.eventType = ''
      this.recurringType = ''
      this.dayOfWeek = ''
      this.dateOfMonth = ''
      this.eventDate = {
        startDate: '',
        endDate: '',
        startTime: '',
        endTime: ''
      }
      this.eventId = ''
    },
    editEvent (clickedEvent) {
      const event = {
        title: clickedEvent.event.title,
        location: clickedEvent.event.extendedProps.location,
        description: clickedEvent.event.extendedProps.description,
        contacts: clickedEvent.event.extendedProps.contacts,
        webpage: clickedEvent.event.extendedProps.webpage,
        startTime: clickedEvent.event.extendedProps._startTime,
        endTime: clickedEvent.event.extendedProps._endTime,
        startDate: new Date(clickedEvent.event.start).toLocaleDateString('en-US', { day: 'numeric', month: 'numeric', year: 'numeric' }),
        endDate: new Date(clickedEvent.event.end).toLocaleDateString('en-US', { day: 'numeric', month: 'numeric', year: 'numeric' }),
        eventType: clickedEvent.event.extendedProps.eventType,
        recurringType: clickedEvent.event.extendedProps.recurringType,
        daysOfWeek: clickedEvent.event.extendedProps.daysOfWeek,
        recurrenceRule: clickedEvent.event.extendedProps.recurrenceRule,
        _id: clickedEvent.event.extendedProps._id
      }
      if (event.recurringType || event.startDate !== event.endDate) {
        this.openCreateMeetingModal = true
      } else {
        this.collectInfoModal = true
      }
      this.eventTitle = clickedEvent.event.title
      this.eventLocation = clickedEvent.event.extendedProps.location
      this.eventContacts = clickedEvent.event.extendedProps.contacts
      this.eventURL = clickedEvent.event.extendedProps.webpage
      this.eventDescription = clickedEvent.event.extendedProps.description
      this.eventType = clickedEvent.event.extendedProps.eventType
      this.recurringType = clickedEvent.event.extendedProps.recurringType
      this.dayOfWeek = clickedEvent.event.extendedProps.dow
      this.dateOfMonth = clickedEvent.event.extendedProps.recurrenceRule
      this.eventDate.startDate = new Date(clickedEvent.event.start).toISOString().split('T')[0]
      this.eventDate.endDate = new Date (clickedEvent.event.end).toISOString().split('T')[0]
      this.eventDate.startTime = clickedEvent.event.extendedProps._startTime
      this.eventDate.endTime = clickedEvent.event.extendedProps._endTime
      this.eventId = clickedEvent.event.extendedProps._id
      // set the modal data and change the event submit button to edit event and create endpoint to update the event.
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
    <div>
      <button class="create_event" @click="resetData(); openCreateMeetingModal = true">{{$t('event.create_event')}}</button>
    </div>
    <div class='demo-app-main'>
        <FullCalendar
        class='demo-app-calendar'
        :options='calendarOptions'
        >
        <template v-slot:eventContent='arg'>
            <b>{{ arg.timeText }}</b>
            <i v-if="arg.event.title.length > 6">{{ arg.event.title.slice(0, 6)}}<b>...</b></i>
            <i v-if="arg.event.title.length <= 6">{{ arg.event.title }}</i>
        </template>
        </FullCalendar>
    <!-- Modal to show events   -->
    <Modal :open="showModal" :displayCloseButton="false" @close="showModal = !showModal">        
      <h4 v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.title}} </h4>
      <b> {{ $t('location') }}: </b>  <template v-if="this.clickedEvent.event != null"> {{this.clickedEvent.event.extendedProps.location}} </template>            
      <br><b>{{ $t('event.start') }}:</b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps._startTime}}  </template>
      <br><b>{{ $t('event.end_time') }}: </b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps._endTime}} </template>    
      <br><b>{{ $t('event.event_info') }}: </b> <template v-if="this.clickedEvent.event != null">{{this.clickedEvent.event.extendedProps.description}} </template>
      <br><b>{{ $t('event.url') }}: </b> <template v-if="this.clickedEvent.event != null"><a :href=" 'http://'+this.clickedEvent.event.extendedProps.webpage">{{this.clickedEvent.event.extendedProps.webpage}}</a>  </template>
      <br>
      <br>          
      <button v-if="owner" class="button-modal-delete" @click="removeEvent(); showModal = false">{{$t('event.delete_event')}}</button> 
      <button v-if="owner" class="button-modal-edit" @click="editEvent (this.clickedEvent)">{{$t('event.edit_event')}}</button>
    </Modal> 

    <!-- Modal to create events   -->
    <Modal :open="collectInfoModal" @close="collectInfoModal = !collectInfoModal">
      <div>
        <p> {{ $t('event.event_name') }}: 
          <br><input v-model="eventTitle" placeholder="Title"/> 
        </p>
        <p> {{ $t('event.event_loc') }}: 
          <br><input v-model="eventLocation" :placeholder="$t('place')" /> 
        </p>
        <p> {{ $t('event.contact') }}:
          <br><input v-model="eventContacts" :placeholder=" $t('event.event_contact') " />
        </p>
        <p> {{ $t('event.url') }}: {{eventURL}} 
          <br><input v-model="eventURL" placeholder="URL" />
        </p>
        <p> {{ $t('event.event_description') }}:  
          <br><textarea v-model="eventDescription" :placeholder=" $t('description')"> </textarea>
        </p>
        <p> 
          {{ $t('event.choose_start') }}: 
          <input type='time' id='eventTimeStart' v-model="eventDate.startTime" name="EventTimeStart"/>
          {{ $t('event.choose_end') }}: 
          <input type='time' id='eventTimeEnd' :min="eventDate.startTime" v-model="eventDate.endTime" name="EventTimeEnd"/>
        </p>          
      </div>
      <button v-if="!this.eventId" @click="handleInput(); collectInfoModal = !collectInfoModal" class="button-modal-create"> {{ $t('event.create_event') }}</button>
      <button v-if="this.eventId" @click="handleInput(); collectInfoModal = !collectInfoModal" class="button-modal-edit-submit"> {{ $t('event.edit_event') }}</button>
    </Modal> 

    <Modal :open="openCreateMeetingModal" @close="openCreateMeetingModal = false">
      <div>
        <p> {{ $t('event.event_name') }}: 
          <br><input v-model="eventTitle" placeholder="Title"/> 
        </p>
        <p> {{ $t('event.event_loc') }}: 
          <br><input v-model="eventLocation" :placeholder="$t('place')" /> 
        </p>
        <p> {{ $t('event.contact') }}:
          <br><input v-model="eventContacts" :placeholder=" $t('event.event_contact') " />
        </p>
        <p> {{ $t('event.url') }}: {{eventURL}} 
          <br><input v-model="eventURL" placeholder="URL" />
        </p>
        <p> {{ $t('event.event_description') }}:  
          <br><textarea v-model="eventDescription" :placeholder=" $t('description')"> </textarea>
        </p>
        <span> {{ $t('event.event_type') }}: 
          <br>
          <select v-model="eventType" id="payment-type" name="payment-type" :placeholder="$t('event.event_type')">
            <option value="recurring">{{$t('event.recurring')}}</option>
            <option value="onetime">{{$t('event.one_time')}}</option>
          </select>
        </span>
        <span v-if="eventType == 'recurring'"> {{ $t('event.recursion_type') }}: 
          <select v-model="recurringType" id="payment-type" name="payment-type" :placeholder="$t('event.event_type')">
            <option value="weekly">{{$t('event.weekly')}}</option>
            <option value="monthly">{{$t('event.monthly')}}</option>
          </select>
        </span>
        <p v-if="recurringType == 'weekly'">
          {{ $t('event.select_day') }}
          <br/>
          <select v-model="dayOfWeek" id="day-selector">
            <option value="1">{{$t('Monday')}}</option>
            <option value="2">{{$t('Tuesday')}}</option>
            <option value="3">{{$t('Wednesday')}}</option>
            <option value="4">{{$t('Thursday')}}</option>
            <option value="5">{{$t('Friday')}}</option>
            <option value="6">{{$t('Saturday')}}</option>
            <option value="0">{{$t('Sunday')}}</option>
          </select>
        </p>
        <p v-if="recurringType == 'monthly'">
          {{$t('event.recurring_Date')}}
          <input id='dateOfMonth' name="dateOfMonth" v-model="dateOfMonth" :placeholder="$t('event.recurring_Date')"/>
        </p>
        <p>
          <span v-if="eventType != 'recurring'"> 
          {{ $t('event.choose_start') }}: 
          <input type='date' id='createEventDateStart' name="createEventDateStart" v-model="eventDate.startDate"/>
          </span>
          <span>
          {{ $t('event.choose_start') }}: 
          <input type='time' id='createEventTimeStart' name="createEventTimeStart" v-model="eventDate.startTime"/>
          </span>
          
        </p>    
        <p> 
          <span v-if="eventType != 'recurring'"> 
          {{ $t('event.choose_end') }}: 
          <input type='date' id='createEventDateEnd' name="createEventDateEnd" v-model="eventDate.endDate"/>
          </span>
          <span>
          {{ $t('event.choose_end') }}: 
          <input type='time' id='createEventTimeEnd' name="createEventTimeEnd" v-model="eventDate.endTime"/>
          </span>
          
        </p>         
      </div>
      <button v-if="!this.eventId" @click="createEvent(); openCreateMeetingModal = false" class="button-modal-create"> {{ $t('event.create_event') }}</button>
      <button v-if="this.eventId" @click="createEvent(); openCreateMeetingModal = false" class="button-modal-edit-submit"> {{ $t('event.edit_event') }}</button>
    </Modal>

    </div>
        <PopupCard v-if="this.timeLapsError" :title="$t('event.time_error')" btnLink="/event" btnText="Ok" :cardText="$t('event.time_error_body')"/>
        <LoadingComponent ref="loadingComponent" />

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

.button-modal-delete {
  background-color: #f44336; /* Red */
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 10px;
}
.button-modal-edit {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 10px;
}
.button-modal-create {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 10px;
}
.button-modal-edit-submit {
  background-color: #babd37; /* Green */
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 10px;
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
.create_event {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 10px;
}
</style>

<style scoped>
/** for mobile browsers only */
@media only screen and (max-width: 600px) {
  .demo-app {
    flex-direction: column;
  }
  .demo-app-sidebar {
    width: auto;
  }
}
</style>
