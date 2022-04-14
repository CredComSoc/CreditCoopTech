import JsSHA from 'jssha'

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

export async function checkAdminStatus () {
  const authPromise = fetch(EXPRESS_URL + '/admin', { 
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

export async function logout () {
  const logoutPromise = fetch(EXPRESS_URL + '/logout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
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
  return logoutPromise
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

export async function profile () {
  const profilePromise = fetch(EXPRESS_URL + '/profile', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
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
  return profilePromise
}

export async function getNotifications () {
  const promise = await fetch(EXPRESS_URL + '/notification', {
    method: 'GET',
    credentials: 'include'
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
  return promise
}

export async function postNotification (type, user) {
  const data = { 
    date: '',
    type: type,
    toUser: user,
    fromUser: '',
    seen: false
  }
  const promise = await fetch(EXPRESS_URL + '/notification', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data),
    credentials: 'include'
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
  return promise
}

export async function setNotificationsToSeen () {
  const promise = await fetch(EXPRESS_URL + '/notification', {
    method: 'PATCH',
    credentials: 'include'
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
  return promise
}

/* Routes using cc-node */

export function getTransactions () {
  const promise = fetch(EXPRESS_URL + '/transactions', {
    method: 'GET',
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
  return promise
}

export async function getSaldo () {
  const promise = await fetch(EXPRESS_URL + '/saldo', {
    method: 'GET',
    credentials: 'include'
  })
    .then((res) => {
      return res.json()
    })
    .then((data) => {
      return (data)
    })
    .catch(() => {
      return null
    })
  if (promise) {
    return promise.completed.balance
  } else {
    return null
  }
}
