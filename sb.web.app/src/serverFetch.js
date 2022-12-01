import JsSHA from 'jssha'

//const urlBase = 'http://155.4.159.231' // USE HOST EXPRESS
const urlBase = 'http://localhost'
export const EXPRESS_URL = urlBase + ':3000' 
export const CHAT_URL = urlBase + ':3001'

/*****************************************************************************
 * 
 *                           Helper Functions
 *                 
 *****************************************************************************/

function hashMyPassword (password) {
  const hashObj = new JsSHA('SHA-512', 'TEXT', { numRounds: 1 })
  hashObj.update(password)
  const hash = hashObj.getHash('HEX')
  return hash
}

/*****************************************************************************
 * 
 *                               Images
 *                 
 *****************************************************************************/

export async function getImg (filename) {
  const promise = await fetch(EXPRESS_URL + '/image/' + filename, {
    method: 'GET',
    credentials: 'include'
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  }) 

  return promise
}

/*****************************************************************************
 * 
 *                           Login & Authentication
 *                 
 *****************************************************************************/

export async function login (email, password) {
  //const hashedPassword = hashMyPassword(password)

  return await fetch(EXPRESS_URL + '/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email: email, password: password }),
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
}

export async function logout () {
  await fetch(EXPRESS_URL + '/logout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  })
  return true
}

export async function authenticate () {
  return fetch(EXPRESS_URL + '/authenticate', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    return response.json()
  }).catch(() => {
    return false
  }) 
}

export async function checkAdminStatus () {
  return fetch(EXPRESS_URL + '/admin', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    return response.json()
  }).catch(() => {
    return false
  })
}

/*****************************************************************************
 * 
 *                                User Data
 *                 
 *****************************************************************************/

export async function fetchData () {
  return fetch(EXPRESS_URL + '/data', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    return response.json()
  }).catch(() => {
    return false
  })
}

/*****************************************************************************
 * 
 *                                Admin Page
 *                 
 *****************************************************************************/

export async function register (username, password) {
  const hashedPassword = hashMyPassword(password)

  return await fetch(EXPRESS_URL + '/register', {
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

/*****************************************************************************
 * 
 *                                Profile 
 *                 
 *****************************************************************************/

export async function profile () {
  return await fetch(EXPRESS_URL + '/profile', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    if (!response.ok) {
      throw new Error('Network response was not ok')
    } else {
      return response.json()
    }
  }).catch(err => {
    console.error('There has been a problem with your fetch operation:', err)
  })
}

export async function updateProfile (accountName, description, adress, city, billingName, billingBox, billingAdress, orgNumber, email, phone, logo) {
  const data = new FormData()
  data.append('accountInfo', JSON.stringify({ 
    accountName: accountName,
    description: description,
    adress: adress,
    city: city,
    billingName: billingName,
    billingBox: billingBox,
    billingAdress: billingAdress,
    orgNumber: orgNumber, 
    email: email,
    phone: phone
  }))
  data.append('file', logo)
  return await fetch(EXPRESS_URL + '/updateProfile', {
    method: 'POST',
    credentials: 'include',
    body: data 
  }).then((response) => {
    if (!response.ok) {
      throw new Error('Network response was not ok')
    } else {
      return response
    }
  }).catch(err => {
    console.error('There has been a problem with your fetch operation:', err)
  })
}

/*****************************************************************************
 * 
 *                                Articles 
 *                 
 *****************************************************************************/

export function getArticles () {
  const promise = fetch(EXPRESS_URL + '/articles', {
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

export async function getArticleWithId (id) {
  const promise = await fetch(EXPRESS_URL + '/article/' + id, {
    method: 'GET',
    credentials: 'include'
  })
    .then((res) => {
      return res.json()
    })
    .then((data) => {
      return (data.listing)
    })
    .catch(() => {
      return false
    })
  return promise
}

export async function uploadArticle (data) {
  return await fetch(EXPRESS_URL + '/upload/article', { 
    method: 'POST',
    credentials: 'include',
    body: data // This is your file object
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  }) 
}

export async function deactivateArticle (id) {
  return await fetch(EXPRESS_URL + '/article/remove/' + id, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  }) 
}

/*****************************************************************************
 * 
 *                                Members
 *                 
 *****************************************************************************/

export async function sendMoney (amount, comment, payee) {
  const data = {
    price: amount,
    quantity: 1,
    article: comment,
    id: '0',
    userUploader: payee
  }
  await fetch(EXPRESS_URL + '/createrequest', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data),
    credentials: 'include'
  })
}

/*****************************************************************************
 * 
 *                                Notifications
 *                 
 *****************************************************************************/

export async function postNotification (type, user, amount = 0) {
  const data = { 
    date: '',
    type: type,
    toUser: user,
    fromUser: '',
    seen: false,
    amount: amount
  }
  const promise = await fetch(EXPRESS_URL + '/notification', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data),
    credentials: 'include'
  }).then((response) => {
    if (!response.ok) {
      throw new Error('Network response was not ok')
    } else {
      return response
    }
  }).catch(err => {
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
        return response
      }
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    })
  return promise
}

/*****************************************************************************
 * 
 *                                Cart
 *                 
 *****************************************************************************/

export async function deleteCart (id) {
  const promise = await fetch(EXPRESS_URL + '/cart/remove/item/edit/' + id, {
    method: 'POST',
    credentials: 'include'
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  }) 

  return promise
}

export async function createTransactions (cart) {
  cart.forEach(element => {
    fetch(EXPRESS_URL + '/createrequest', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(element),
      credentials: 'include'
    })
    postNotification('saleRequest', element.userUploader) 
  })
}

/*****************************************************************************
 * 
 *                                Saldo
 *                 
 *****************************************************************************/

export async function getSaldo () {
  const promise = await fetch(EXPRESS_URL + '/saldo', {
    method: 'GET',
    credentials: 'include'
  }).then((res) => {
    return res.json()
  }).then((data) => {
    return (data)
  }).catch(() => {
    return null
  })
  if (promise) {
    return promise.completed.balance
  } else {
    return null
  }
}

export async function getAvailableBalance () {
  const saldo = await getSaldo()
  const promise = await fetch(EXPRESS_URL + '/limits/min', {
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
  return saldo - promise
}

export async function getUserSaldo (user) {
  const promise = await fetch(EXPRESS_URL + '/saldo', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ user: user }),
    credentials: 'include'
  }).then((res) => {
    return res.json()
  }).then((data) => {
    return (data)
  }).catch(() => {
    return null
  })
  if (promise) {
    return promise.completed.balance
  } else {
    return null
  }
}

export async function getUserAvailableBalance (user) {
  const saldo = await getUserSaldo(user)
  
  const promise = await fetch(EXPRESS_URL + '/limits/min', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ 'profile.accountName': user }),
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
  return saldo - promise
}

export async function getLimits () {
  return await fetch(EXPRESS_URL + '/limits', {
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
}

export async function getUserLimits (user) {
  return await fetch(EXPRESS_URL + '/limits', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ 'profile.accountName': user }),
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
}

/*****************************************************************************
 * 
 *                                Transactions
 *                 
 *****************************************************************************/

export function getPurchases () {
  const promise = fetch(EXPRESS_URL + '/purchases', {
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

export function cancelRequest (id) {
  const data = { uuid: id }
  const promise = fetch(EXPRESS_URL + '/cancelrequest', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data),
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

export function acceptRequest (id) {
  const data = { uuid: id }
  const promise = fetch(EXPRESS_URL + '/acceptrequest', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data),
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

/*****************************************************************************
* 
*                                Reset Password
*                 
*****************************************************************************/

export async function mail (email) {
  const data = { email: email }
  const promise = fetch(EXPRESS_URL + '/forgot', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data),
    credentials: 'include'
  })
    .then((res) => {
      return res.ok
    })
    .catch(() => {
      return false
    })
  return promise
}

export async function resetToken (token, newpass) {
  const promise = fetch(EXPRESS_URL + '/reset/' + token, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ newpass: newpass }),
    credentials: 'include'
  })
    .then((res) => {
      return res.ok
    })
    .catch(() => {
      return false
    })
  return promise
}

/*****************************************************************************
* 
*                                  Chat
*                 
*****************************************************************************/

export async function getChatHistory (chatID) {
  const promise = fetch(EXPRESS_URL + '/chat/history/' + chatID, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  })
    .then((res) => {
      return res
    })
    .catch(() => {
      return false
    })
  return promise
}

export async function getChatHistories () {
  const promise = fetch(EXPRESS_URL + '/chat/histories', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  })
    .then((res) => {
      return res
    })
    .catch(() => {
      return false
    })
  return promise
}

/*****************************************************************************
* 
*                                Event
*                 
*****************************************************************************/
export async function loadEvents () {
  return await fetch(EXPRESS_URL + '/load/event', {
    method: 'GET',
    credentials: 'include'
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok')
      } else {
        return response.json()
      }
    }).catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
    }) 
}
/*
export async function loadEvents () {
  return fetch(EXPRESS_URL + '/load/event', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    console.log('response i server fecth' + response.json().stringify())
    return response.json()
  }).catch(() => {
    return false
  })
}
*/
export async function getUserId () {
  return fetch(EXPRESS_URL + '/userId', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    return response.json()
  }).catch(() => {
    return false
  })
}
export async function uploadEvent (title, start, end, allDay, location, description, contacts, webpage, startTime, endTime) {
  return await fetch(EXPRESS_URL + '/upload/event', { 
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      title: title,
      eventstart: start,
      eventend: end,
      eventallDay: allDay,
      location: location,
      description: description,
      contacts: contacts,
      webpage: webpage, 
      _startTime: startTime,
      _endTime: endTime
    }),
    credentials: 'include'
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  })
}
export async function deleteEvent (id) {
  const promise = await fetch(EXPRESS_URL + '/event/remove/' + id, {
    method: 'POST',
    credentials: 'include'
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  }) 

  return promise
}
