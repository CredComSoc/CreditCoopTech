import jsSHA from 'jssha';

function hashMyPassword(password) {
    let hashObj = new jsSHA("SHA-512", "TEXT", {numRounds: 1});
    hashObj.update(password);
    let hash = hashObj.getHash("HEX");
    return(hash);
  }

  
  export async function login(username, password) { 
    let hashedPassword = hashMyPassword(password)

    const loginPromise = fetch('http://localhost:3000/login', {
        method: 'POST', 
        headers : {
            'Content-Type': 'application/json'
        },
        body : JSON.stringify({"username": username, "password" : hashedPassword})
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        else {
            return(response.json())
        }
    })
    .then((data) => {

        return data.sessionID
    })
    .catch(err => {
        console.error('There has been a problem with your fetch operation:', err);
        return(false)
    });

    return loginPromise
}


export async function registerFetch(username, password) { 
    let hashedPassword = hashMyPassword(password)

    fetch('http://localhost:3000/register', {
        method: 'POST', 
        headers : {
            'Content-Type': 'application/json'
        },
        body : JSON.stringify({"username": username, "password" : hashedPassword})
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        else {
            console.log("Registering account")
            return(true)
        }
    })
    .catch(err => {
        console.error('There has been a problem with your fetch operation:', err);
    });
}

export async function logout(sessionID) {

    fetch('http://localhost:3000/logout', {
        method: 'PATCH', 
        headers : {
            'Content-Type': 'application/json'
        },
        body : JSON.stringify({sessionID: sessionID})
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            else {
                console.log("Signing out")
                return(true)
            }
        })
        .catch(err => {
            console.error('There has been a problem with your fetch operation:', err);
        });
}
