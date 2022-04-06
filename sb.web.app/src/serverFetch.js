import JsSHA from 'jssha'
import fetchNoCors from 'fetch-no-cors'

const CORS_ANYWHERE = 'https://sheltered-cliffs-58344.herokuapp.com/'
const CC_NODE_URL = 'http://155.4.159.231/cc-node'
// const EXPRESS_URL = 'http://localhost:3000' // USE LOCAL DB
const EXPRESS_URL = 'http://155.4.159.231:3000' // USE HOST DB
// const EXPRESS_URL = 'http://192.168.0.100:3000' // FOR VIRTUALBOX HOST

function hashMyPassword (password) {
  const hashObj = new JsSHA('SHA-512', 'TEXT', { numRounds: 1 })
  hashObj.update(password)
  const hash = hashObj.getHash('HEX')
  return hash
}

export async function authenticate () {
  const authPromise = fetch(EXPRESS_URL + '/authenticate', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    if (!response.ok) {
      return false
    }
    return true
  }).catch(() => {
    return false
  }) 

  return authPromise 
}

export async function login (username, password) {
  const hashedPassword = hashMyPassword(password)

  const loginPromise = fetch(EXPRESS_URL + '/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ username: username, password: password }),
    credentials: 'include'
  }).then((response) => {
    if (!response.ok) {
      return false
    } else {
      return true
    }
  }).catch(() => {
    return false
  })
  return loginPromise
}

export async function register (username, password) {
  const hashedPassword = hashMyPassword(password)

  fetch(EXPRESS_URL + '/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ username: username, password: hashedPassword })
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok')
      } else {
        console.log('Registering account')
        return true
      }
    })
    .catch(() => {
      return false
    })
}

export async function logout (sessionID) {
  fetch(EXPRESS_URL + '/logout', {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ sessionID: sessionID })
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok')
      } else {
        console.log('Signing out')
        return true
      }
    })
    .catch(() => {
      return false
    })
}

export async function getAllListings (searchword, destinationsArray, categoryArray, articleArray) {
  const getAllListingsPromise = fetch(EXPRESS_URL + '/getAllListings/', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ searchword: searchword, destinations: destinationsArray, categories: categoryArray, articles: articleArray })

  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok')
      } else {
        return response.json()
      }
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })

  return getAllListingsPromise
}

export function getTransactions (ccUser, ccAuth) {
  const allTransactionPromise = fetchNoCors(CC_NODE_URL + '/transaction/full', {
    method: 'GET',
    headers: {
      'cc-user': 'TestAdmin',
      'cc-auth': '123'
    }
  }, CORS_ANYWHERE)
    .then((res) => {
      // console.log(res.json())
      return res.json()
    })
    .then((data) => {
      // console.log('data ' + data)
      return (data)
    })
    .catch(() => {
      return false
    })
  return allTransactionPromise
}

// UNUSED
export async function getTransaction (ccUser, ccAuth, id) {
  const transactionPromise = fetchNoCors(CC_NODE_URL + '/transaction/' + id + '/full', {
    method: 'GET',
    headers: {
      'cc-user': ccUser,
      'cc-auth': ccAuth,
      'Content-Type': 'application/json'
    }
  }, CORS_ANYWHERE)
    .then((res) => {
      // console.log(res.json())
      return res.json()
    })
    .then((data) => {
      // console.log('data ' + data)
      return (data)
    })
    .catch(() => {
      return false
    })
  return transactionPromise
}

export async function profile () {
  const profilePromise = fetch(EXPRESS_URL + '/profile', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  })
    .then((res) => {
      return res.json()
    })
    .then((data) => {
      return (data)
    })
    .catch(() => {
      return false
    })
  return profilePromise
}
