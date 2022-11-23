import { loadEvents } from '../../serverFetch'
let eventGuid = 0
const todayStr = new Date().toISOString().replace(/T.*$/, '') // YYYY-MM-DD of today
//let counter = 0
export let loadedevents

/*
 Get function that returns all the events in the database. 
*/
export function getLoadedEvents () {
  console.log('Inne i getLoaded: ' + loadedevents)
  return loadedevents
}
/*
 Function that loads all the events from the database for the calander at start. 
 loadEvents is located in serverFetch.js
 */
export function initEvents () {
  loadEvents()
    .then(res => {
      console.log('Här är res: ')
      console.log(res)
      loadedevents = res
      return res
    })
}

/* 
 Creates a unique id for every event to be able to fetch it from the database
*/
export function createEventId () {
  return String(eventGuid++)
}
