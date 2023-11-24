const FormData = require('form-data')
const fetch = require('node-fetch')
const config = require('../config')
const nodemailer = require('nodemailer')


// POST to API end point (which has to be started first) to register users

const express_url = config.BACK_END_API_URL

min_limit_default = -1000;
max_limit_default = 1000;
const support_email = config.SUPPORT_EMAIL
const support_email_password = config.SUPPORT_EMAIL_PASSWORD
const FRONTEND_URL = config.FRONT_END_URL; 

let email_transporter = null
  email_transporter = nodemailer.createTransport({
    host: 'smtp.migadu.com',
    port: 587,
    secure: false, 
    auth: {
      user: support_email,
      pass: support_email_password
    }
  })


async function registerUser (isadmin, username, password, email) {
    console.log("Registering user " + username + " with email " + email)
    const data = new FormData()
    data.append('accountInfo', JSON.stringify({ 
        is_admin: isadmin,
        accountName: username,
        password: password,
        min_limit: min_limit_default,
        max_limit: max_limit_default,
        description: 'description',
        address: 'address',
        city: 'city',
        billingName: 'billingName',
        billingBox: 'billingBox',
        billingAddress: 'billingAddress',
        orgNumber: 'orgNumber', 
        email: email.toLowerCase(),
        sendWelcomeEmail: false,
        phone: 'phone'
    }))
    return await fetch(express_url + '/register', {
        method: 'POST',
        body: data
    })
    .then(async (response) => {
        if (response.status == 200) {
            try {
                // TODO: May be change the language to english if that is the users are english speaking
                const reponse = await email_transporter.sendMail({ //send mail to the new user(admin should be able to change this text later)
                  from: support_email, // sender address
  
                  to: email, 
                  subject: 'Welcome to Land Care Trade', // Subject line
                  text: `
                  You are receiving this email because you have requested to join Land Care Trade.
                  Please click the following link or paste it into a browser to complete the sign up process:
                  ${FRONTEND_URL}/login
  
                  Your login details are:
                  Email address: ${email}
                  Password: ${password}
                  
                  Best wishes,
                  Land Care Trade
                  `
                })
                console.log('email sent ', reponse)
              } catch (error) {
                console.log('error while sending email to user', error)  
              }
            response.text().then(text => 
                console.log(response.status + ": " + text)
            )
        }
        else {
            console.log("Error registering user " + username)
            response.text().then(text => 
                console.log(response.status + " (" + response.statusText + ") - " + text)
            )
        }
        return response
    })
}


// const member = 'testuser'
// const admin = 'testadmin'
// const password = '123'

//registerUser(false, "testuser4", "testpassword4", "test4@nowhere.com")
registerUser(false, "testuser5", "testpassword5", "test5@nowhere.com")
// registerUser(true, "admintest3", "testpassword3", "admin3@nowhere.com")
// registerUser(false, "testuser3", "testpassword", "test3@nowhere.com")

// registerUser(false, "test", "testpassword", "test@gmail.com")
