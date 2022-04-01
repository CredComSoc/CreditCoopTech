import JsSHA from 'jssha'
import fetchNoCors from 'fetch-no-cors'

const CORS_ANYWHERE = 'https://sheltered-cliffs-58344.herokuapp.com/'
const CC_NODE_URL = '155.4.159.231/cc-node'
// const EXPRESS_URL = 'http://localhost:3000'  // USE LOCAL DB
// const EXPRESS_URL = '155.4.159.231:3000'     // USE HOST DB
const EXPRESS_URL = 'http://192.168.0.100:3000' // FOR VIRTUALBOX HOST

async function getUserData () {
  const userPromise = fetch(EXPRESS_URL + '/filter/full', {
    method: 'GET'
  })
    .then((res) => {
      return res.json()
    })
    .then((data) => {
      return data
    })
    .catch(() => {
      return false
    })
  return userPromise
}

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
  })
    .then((response) => {
      if (!response.ok) {
        return false
      }
      return true
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
    body: JSON.stringify({ username: username, password: password })
  })
    .then((response) => {
      console.log(response)
      if (!response.ok) {
        return false
      } else {
        return true
      }
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

export function getTransactions (ccUser, ccAuth) {
  const allTransactionPromise = getUserData().then(userData => {
    // console.log(userData)
    const allTransactionPromise = fetchNoCors(CC_NODE_URL + '/transaction/full', {
      method: 'GET',
      headers: {
        'cc-user': userData[0].id,
        'cc-auth': userData[0].all.sessionID
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

export async function profile (ccUser, ccAuth) {
  const profilePromise = fetch(EXPRESS_URL + '/profile', {
    method: 'GET',
    headers: {
      'cc-user': ccUser,
      'cc-auth': ccAuth,
      'Content-Type': 'application/json'
    }
  }, CORS_ANYWHERE)
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
