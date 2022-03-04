
// upploadFile kommer skapa och returnera ett promise som tar emot filen och skicka den till backend
export async function uploadFile (file) {
  const uploadFilePromise = fetch('http://localhost:3000/upload', {
    method: 'POST',
    body: file
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
      return data.sessionID
    })
    .catch(err => {
      console.error('There has been a problem with your fetch operation:', err)
      return (false)
    })

  return uploadFilePromise
}
