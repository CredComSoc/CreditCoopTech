import { loadEvents } from '../../serverFetch'
let eventGuid = 0
const todayStr = new Date().toISOString().replace(/T.*$/, '') // YYYY-MM-DD of today

export let loadedevents = []
export function initEvents () {
  console.log('inne i initevenet!!!')
  loadedevents = loadEvents()
  console.log('loaded eventes =' + loadedevents)
  loadedevents = loadEvents()
}

export const INITIAL_EVENTS = [
  {
    id: createEventId(),
    title: 'All-day event',
    start: todayStr
  },
  {
    id: createEventId(),
    title: 'Timed event',
    start: todayStr + 'T12:00:00'
  }
]

export function createEventId () {
  return String(eventGuid++)
}
