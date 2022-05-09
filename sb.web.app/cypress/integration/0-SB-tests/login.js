

//const url = "155.4.159.231:8080"
const url = "localhost:8080"



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
  })


  describe('Visit all pages from home screen', () => {

    before(() => {

        cy.visit(url)

        cy.get('input[id="email-input"]').click().type("User")

        cy.get('input[id="password-input"]').click().type("123")

        cy.get('Button').click()
      
    })

    it('Shop', () => {
        cy.visit(url)
        cy.pause()
        //cy.get('router-link[]').click()
    })

    // it('Medlemmar', () => {
    //     cy.get('router-link[id="route-members"]').click()
    // })
    // it('Varukorg', () => {
    //     cy.get('router-link[id="route-cart"]').click()
    // })
    // it('Min Sida', () => {
    //     cy.get('router-link[id="route-profile"]').click()
    // })
  })

  
>>>>>>> origin/robin-linus-4
