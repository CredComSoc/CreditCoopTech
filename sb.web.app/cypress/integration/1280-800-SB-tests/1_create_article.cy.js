

const url = "155.4.159.231:8081"


beforeEach(function() {
  cy.viewport(1280, 800)
}) 

describe('Create article', () => {

    beforeEach(() => {
        cy.visit(url)
        cy.get('input[id="email-input"]').click().type("User")
        cy.get('input[id="password-input"]').click().type("123")
        cy.get('Button').click()
        cy.wait(1000)
        cy.visit(url)
        cy.get('div[id="navbar-article"]').click()
        cy.wait(1000)
      }) 

    it('Create request-article', { scrollBehavior: false }, () => {
        // stage 1

        cy.get('div[id="buy-or-sell"]').within(() => {
            cy.get('div[id="combo-box-field"]').click()
        })
        cy.get('p').contains('Köpes').click()
        
        cy.get('div[id="title-field"]').within(() => {
            cy.get('input').click().type("TEST KÖP " + Math.floor(Math.random() * 100) + 1)
        })

        cy.get('div[id="short-desc"]').within(() => {
            cy.get('textarea').click().type("SHORT DESCRIPTION")
        })

        cy.get('div[id="desc"]').within(() => {
            cy.get('textarea').click().type("DESCRIPTION")
        })

        cy.scrollTo('bottom')

        cy.get('div[id="category"]').within(() => {
            cy.get('div[id="combo-box-field"]').click()
        })
        cy.get('p').contains('Kiosk').scrollIntoView().click()

        cy.get('div[id="type"]').within(() => {
            cy.get('div[id="combo-box-field"]').click()
        })
        cy.get('p').contains('Produkt').click()

        cy.get('a').contains('Nästa').click()

        // stage 2
        cy.get('input[type="checkbox"]').click()

        cy.wait(5000)

        // stage 3

        // confirm
    })
    
    it('Create offer-article', () => {
    
    })
    
    it('Create article with 5 images', () => {
    
    })
    
    it('Create time-restricted article (1 day)', () => {
    
    })
})
