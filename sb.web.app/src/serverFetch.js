import * as JsSHA from 'jssha'
import fetchNoCors from 'fetch-no-cors'

const CORS_ANYWHERE = 'https://sheltered-cliffs-58344.herokuapp.com/'
const CC_NODE_URL = '155.4.159.231/cc-node'

async function getUserData () {
  const userPromise = fetch('127.0.0.1:3000/filter/full', {
    method: 'GET'
  })
    .then((res) => {
      //console.log(res.text())
      return res
    })
    .then((data) => {
      return (data)
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
  return userPromise
}

function hashMyPassword (password) {
  const hashObj = new JsSHA('SHA-512', 'TEXT', { numRounds: 1 })
  hashObj.update(password)
  const hash = hashObj.getHash('HEX')
  return (hash)
}

export async function login (username, password) {
  const hashedPassword = hashMyPassword(password)

  const loginPromise = fetch('http://localhost:3000/login', {
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
        return (response.json())
      }
    })
    .then((data) => {
      return data.sessionID
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
      return (false)
    })

  return loginPromise
}

export async function registerFetch (username, password) {
  const hashedPassword = hashMyPassword(password)

  fetch('http://localhost:3000/register', {
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
        return (true)
      }
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
}

export async function logout (sessionID) {
  fetch('http://localhost:3000/logout', {
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
        return (true)
      }
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
}

export function getTransactions (ccUser, ccAuth) {
  const userData = getUserData()
  console.log(userData)
  const allTransactionPromise = fetchNoCors(CC_NODE_URL + '/transaction/full', {
    method: 'GET',
    headers: {
      'cc-user': 'TestAdmin',
      'cc-auth': '413080eb-13b5-45fd-b931-ad54634e2100'
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
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
  return allTransactionPromise
}

export async function transaction (ccUser, ccAuth, id) {
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
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
  return transactionPromise
}

export async function profile (ccUser, ccAuth) {
  const profilePromise = fetch('http://localhost:3000/profile', {
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
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
  return profilePromise
}

/*
export async function credit (ccUser, ccAuth) {
  const creditPromise = fetchNoCors(CC_NODE_URL + 'http://localhost:3000/credit', {
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
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
  return creditPromise
} */
