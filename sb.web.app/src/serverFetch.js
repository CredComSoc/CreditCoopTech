
// upploadFile kommer skapa och returnera ett promise som tar emot filen och skicka den till backend

export async function getAllListings (searchword, destinationsArray, categoryArray, articleArray) {
  const getAllListingsPromise = fetch('http://localhost:3000/getAllListings/', {
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
    .then((data) => {
      return data
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
      return (false)
    })

  return getAllListingsPromise
}
