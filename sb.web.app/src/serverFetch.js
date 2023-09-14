import JsSHA from 'jssha'
import store from './store'

export const EXPRESS_URL = process.env.VUE_APP_EXPRESS_URL
export const CHAT_URL = process.env.VUE_APP_CHAT_URL
const standardCreditLine = -5000
const standardMaxAmount = 20000

console.log(EXPRESS_URL)
/*****************************************************************************
 * 
 *                           Helper Functions
 *                 
 *****************************************************************************/
//maybe doing this in backend so that one cant inspect the source code to encrypt a password
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

export async function register (isadmin, username, password, description, adress, city, billingName, billingBox, billingAdress, orgNumber, email, phone, logo) {
  const hashedPassword = hashMyPassword(password) // maybe doing this in backend for security
  const data = new FormData()
  data.append('accountInfo', JSON.stringify({ 
    is_admin: isadmin,
    accountName: username,
    password: password,
    description: description,
    adress: adress,
    city: city,
    billingName: billingName,
    billingBox: billingBox,
    billingAdress: billingAdress,
    orgNumber: orgNumber, 
    email: email.toLowerCase(),
    phone: phone,
    min_limit: standardCreditLine,
    max_limit: standardMaxAmount
  }))
  data.append('file', logo)
  return await fetch(EXPRESS_URL + '/register', {
    method: 'POST',
    body: data
  })
    .then((response) => {
      return response
    })
}
/*
export async function fetchEconomy (maxDate2, minDate2, company2, product2, entries2) {

  const data = new FormData()
  data.append('filterInfo', JSON.stringify({ 
    maxDate: maxDate2,
    minDate: minDate2,
    companyName: company2,
    productName: product2,
    entries: entries2
  }))
  const data2 = 'test igen'
  
  const data = {
    maxDate: maxDate2,
    minDate: minDate2,
    companyName: company2,
    productName: product2,
    entries: entries2
  }*/

export async function fetchEconomy () {
  return await fetch(EXPRESS_URL + '/economy', { 
    method: 'GET',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json'
    }
  }).then((response) => {
    return response.json()
  }).catch(() => {
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
    email: email.toLowerCase(),
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

export async function updateuserProfile (previousname, accountName, description, adress, city, billingName, billingBox, billingAdress, orgNumber, email, phone, logo) {
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
    email: email.toLowerCase(),
    phone: phone
  }))
  data.append('file', logo)
  return await fetch(EXPRESS_URL + '/updateuserProfile/' + previousname, {
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

export async function editArticle (id, data) {
  return await fetch(EXPRESS_URL + '/edit/article/' + id, {
    method: 'PATCH',
    credentials: 'include',
    body: data
  })
    .then((res) => {
      return res
    })
    .catch((error) => {
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
  // eslint-disable-next-line
  let transactionResults = {
    successResults: [],
    failedResults: []
  }
  try {
    for (const element of cart) {
      await fetch(EXPRESS_URL + '/createrequest', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(element),
        credentials: 'include'
      }).then(res => {
        if (res.status === 200) {
          transactionResults.successResults.push(element)
          postNotification('saleRequest', element.userUploader)
        } else {
          transactionResults.failedResults.push(element)
        }
      })
    }
    return transactionResults
  } catch (ex) {
    return transactionResults
  }
  
  /*cart.forEach(element => {
    await fetch(EXPRESS_URL + '/createrequest', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(element),
      credentials: 'include'
    })
    postNotification('saleRequest', element.userUploader) 
  })*/
}

/*****************************************************************************
 * 
 *                                Saldo
 *                 
 *****************************************************************************/
//calls the backend router for the router.get(/saldo located in file ccRequests.js 
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

export async function uploadFile (File) {
  const data = new FormData()
  data.append('file', File)
  return await fetch(EXPRESS_URL + '/uploadFile', {
    method: 'POST',
    credentials: 'include',
    body: data 
  }).then((response) => {
    if (!response.ok) {
      throw new Error('Network response was not ok')
    } else {
      return response.json() //contains file original name
    }
  }).catch(err => {
    console.error('There has been a problem with your fetch operation:', err)
  })
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

export async function createNewCategories (data) {
  return await fetch(EXPRESS_URL + '/categories', { 
    method: 'POST',
    credentials: 'include',
    body: data 
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  }) 
}

export async function getCategories () {
  return fetch(EXPRESS_URL + '/categories', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    return response.json()
  }).catch(() => {
    return null
  })
}
export async function updateCategoryStatus (data) {
  console.log(data)
  return await fetch(EXPRESS_URL + '/updateCategoryStatus', { 
    method: 'POST',
    
    credentials: 'include',
    body: data 
  }).then((res) => {
    return res
  }).then((success) => {
    return success
  }).catch(error => {
    return error
  }) 
}

export async function getCartByUser () {
  return await fetch(EXPRESS_URL + '/cart/byUser', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    return response.json()
  }).catch(() => {
    return null
  })
}
export async function setCartData () {
  const cart = await getCartByUser().then((res) => {
    if (res) {
      return res
    }
  })
  if (cart !== null) {
    store.commit('replaceMyCart', cart)

    let cartSize = 0
    for (const item of cart) {
      cartSize += item.quantity
    }
    store.commit('replaceMyCartSize', cartSize)
  }
}


export async function getNotificationsByUser () {
  return fetch(EXPRESS_URL + '/notifications/byUser', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    let notifications = response.json();
    if (Array.isArray(notifications)) {
      return notifications;
    }
    else {
      return [];
    }
  }).catch(() => {
    return null
  })
}
export async function setNotificationsData () {
  const notifications = await getNotificationsByUser().then((res) => {
    if (res) {
      return res
    }
  })
  if (notifications !== null) {
    const oldNotifications = []
    const newNotifications = []
    if (notifications) {
      for (const notification of notifications) {
        if (notification.seen) {
          oldNotifications.push(notification)
        } else {
          newNotifications.push(notification)
        }
      }
    }

    oldNotifications.sort(function (a, b) {
      return new Date(b.date) - new Date(a.date)
    })

    newNotifications.sort(function (a, b) {
      return new Date(b.date) - new Date(a.date)
    })

    store.commit('replaceOldNotifications', oldNotifications)
    store.commit('replaceNewNotifications', newNotifications)
  }
}


export async function getTransactionsByUser () {
  return await fetch(EXPRESS_URL + '/transactions', { 
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    credentials: 'include'
  }).then((response) => {
    return response.json()
  }).catch(() => {
    return null
  })
}
export async function setTransactionsData () {
  const notifications = await getTransactionsByUser().then((res) => {
    if (res) {
      return res
    }
  })
  if (notifications !== null) {
    if (notifications.requests) {
      store.commit('replaceRequests', notifications.requests)            
    }

    if (notifications.pendingPurchases) {
      store.commit('replacePendingPurchases', notifications.pendingPurchases)            
    }    

    if (notifications.completedTransactions) {
      store.commit('replaceCompletedTransactions', notifications.completedTransactions)            
    } 
  }
}


/*****************************************************************************
* 
*                                setStoreData
*                 
*****************************************************************************/

export async function setStoreData () {
  const data = await fetchData().then((res) => {
    if (res) {
      return res
    }
  })
  if (data) { 
    window.localStorage.removeItem('vuex')
    if (data.user) {
      store.commit('replaceUser', data.user)

      const oldNotifications = []
      const newNotifications = []
      if (data.user.notifications) {
        for (const notification of data.user.notifications) {
          if (notification.seen) {
            oldNotifications.push(notification)
          } else {
            newNotifications.push(notification)
          }
        }
      }

      oldNotifications.sort(function (a, b) {
        return new Date(b.date) - new Date(a.date)
      })

      newNotifications.sort(function (a, b) {
        return new Date(b.date) - new Date(a.date)
      })

      store.commit('replaceOldNotifications', oldNotifications)
      store.commit('replaceNewNotifications', newNotifications)
    }

    if (data.allArticles) {
      store.commit('replaceAllArticles', data.allArticles)
    }

    if (data.allMembers) {       
      store.commit('replaceAllMembers', data.allMembers)
    }

    // removed this because all cart store data's are handled by the setCartData function

    // if (data.myCart) {
    //   store.commit('replaceMyCart', data.myCart)

    //   let cartSize = 0
    //   for (const item of data.myCart) {
    //     cartSize += item.quantity
    //   }
    //   store.commit('replaceMyCartSize', cartSize)
    // }
    
    store.commit('replaceSaldo', data.saldo)
    store.commit('replaceCreditLine', data.creditLine)
    store.commit('replaceCreditLimit', data.creditLimit)
    // if (data.requests) {
    //   store.commit('replaceRequests', data.requests)            
    // }

    // if (data.pendingPurchases) {
    //   store.commit('replacePendingPurchases', data.pendingPurchases)            
    // }    

    // if (data.completedTransactions) {
    //   store.commit('replaceCompletedTransactions', data.completedTransactions)            
    // }           
    
    if (data.allEvents) {       
      store.commit('replaceAllEvents', data.allEvents)
    }
    // console.log(store.state.user.email)
  }
}
