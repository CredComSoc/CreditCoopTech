
// upploadFile kommer skapa och returnera ett promise som tar emot filen och skicka den till backend

async function getAllListings (searchword) {
  const getAllListingsPromise = fetch('http://localhost:3000/getAllListings', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ searchword: searchword })
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok')
      } else {
        console.log('AAAA det funka')
        return (true)
      }
    })
    .then((data) => {
      return data
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
      return (false)
    })

  return getAllListingsPromise
}
