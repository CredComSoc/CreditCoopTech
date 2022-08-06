

const url = "155.4.159.231:8081"


beforeEach(function() {
  cy.viewport(1280, 800)
}) 
  
describe('Login', () => {
  it('Login to SB web app', () => {
    cy.visit(url)
    cy.get('input[id="email-input"]').click().type("User")
    cy.get('input[id="password-input"]').click().type("123")
    cy.get('Button').click()
    cy.contains('Välkommen')
  })

  after(() => {
    cy.get('div[id="navbar-logout"]').click()
  })

})

describe('Saldo widget', () => {
  beforeEach(() => {
    cy.visit(url)
    cy.get('input[id="email-input"]').click().type("User")
    cy.get('input[id="password-input"]').click().type("123")
    cy.get('Button').click()
    cy.wait(1000)
  }) 

  it("Show/Hide saldo card", () => {
    cy.visit(url)
    cy.get('img[id="right-arrow-img"]').click()
    cy.contains('Saldo: ').should('not.exist')

    cy.get('img[id="left-arrow-img"]').click()
    cy.contains('Saldo: ')
  })

})


describe('Visit all pages from home screen using navbar', () => {

  beforeEach(() => {
    cy.visit(url)
    cy.get('input[id="email-input"]').click().type("User")
    cy.get('input[id="password-input"]').click().type("123")
    cy.get('Button').click()
    cy.wait(1000)
  }) 

  it('Shop', () => { 
    cy.visit(url)
    cy.get('div[id="navbar-shop"]').click()
    cy.get('h2').contains('SHOP')
  })

  it('Cart', () => {
    cy.visit(url)
    cy.get('div[id="navbar-cart"]').click()
    cy.get('h1').contains('Varukorg')
  })

  it('Article', () => {
    cy.visit(url)
    cy.get('div[id="navbar-article"]').click()
    cy.contains('Artikelns syfte')
  })

  it('Members', () => {
    cy.visit(url)
    cy.get('div[id="navbar-members"]').click()
    cy.get('h2').contains('Medlemmar')
  })

  it('Home', () => {
    cy.visit(url)
    cy.get('div[id="navbar-home"]').click()
    cy.contains('Välkommen')
  })

  it('Chat', () => {
    cy.visit(url)
    cy.get('div[id="navbar-chat"]').click()
    cy.get('h1').contains('MEDDELANDEN')
  })

  it('Profile', () => {
    cy.visit(url)
    cy.get('div[id="navbar-profile"]').click()
    cy.get('h1').contains('MIN SIDA')
  })

  it('Logout', () => {
    cy.visit(url)
    cy.get('div[id="navbar-logout"]').click()
    cy.contains("Logga in på Svensk Barter")
  }) 

})

describe('Visit shop and member pages from home screen', () => {

  beforeEach(() => {
    cy.visit(url)
    cy.get('input[id="email-input"]').click().type("User3")
    cy.get('input[id="password-input"]').click().type("123")
    cy.get('Button').click() 
    cy.wait(1000)
  })

  it('Shop', () => {
    cy.visit(url)
    cy.get('div[id="home-content-card"]').get("Button").contains("shoppen").click()
    cy.get('h2').contains('SHOP')
  })

  it('Members', () => {
    cy.visit(url)
    cy.get('div[id="home-content-card"]').get("Button").contains("medlemmar").click()
    cy.get('h2').contains('Medlemmar')
  })

})

describe('Visit all links in the footer', () => {
  beforeEach(() => {
    cy.visit(url)
    cy.get('input[id="email-input"]').click().type("User3")
    cy.get('input[id="password-input"]').click().type("123")
    cy.get('Button').click() 
    cy.wait(1000)
  })

  it('Om Sidan', () => {
    cy.visit(url) 
    cy.get('p[id="om-sidan"]').click()
    cy.contains('Viktor Rösler')  
  })
})

  
