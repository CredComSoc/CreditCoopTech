
// upploadFile kommer skapa och returnera ett promise som tar emot filen och skicka den till backend

export async function getAllListings (searchword) {
  console.log(typeof searchword)

  const getAllListingsPromise = fetch('http://localhost:3000/getAllListings/' + searchword, {
    method: 'GET'
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok')
      } else {
        console.log('AAAA det funka')
        return response.json()
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
