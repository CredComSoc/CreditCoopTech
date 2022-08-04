

const url = "155.4.159.231:8080"


// desktop tests
beforeEach(function() {
  cy.viewport(1280, 800)
}) 

describe('My First Test', () => {
    it('Does not do much!', () => {
      expect(true).to.equal(true)
    })
})
  

describe('Login', () => {
  it('Login to SB web app', () => {
    cy.visit(url)

    cy.get('input[id="email-input"]').click().type("User")

    cy.get('input[id="password-input"]').click().type("123")

    cy.get('Button').click()
  })

  after(() => {
    cy.get('div[id="navbar-logout"]').click()
  })
})


describe('Visit all pages from home screen', () => {

  beforeEach(() => {

      cy.visit(url)

      cy.get('input[id="email-input"]').click().type("User")

      cy.get('input[id="password-input"]').click().type("123")

      cy.get('Button').click()
    
  })

  it('Shop', () => {
      cy.get('div[id="navbar-shop"]').click()  
  })

  it('Cart', () => {
    cy.get('div[id="navbar-cart"]').click()
  })

  it('Article', () => {
    cy.get('div[id="navbar-article"]').click()
  })

  it('Members', () => {
    cy.get('div[id="navbar-members"]').click()
  })

  it('Home', () => {
    cy.get('div[id="navbar-home"]').click()
  })

  it('Chat', () => {
    cy.get('div[id="navbar-chat"]').click()
  })

  it('Profile', () => {
    cy.get('div[id="navbar-profile"]').click()
  })

  it('Logout', () => {
    cy.get('div[id="navbar-logout"]').click()
  })

})

  
